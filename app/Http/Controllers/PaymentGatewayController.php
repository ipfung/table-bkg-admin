<?php

namespace App\Http\Controllers;

use App\Facade\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\mPay\inc\MerchantClient;

class PaymentGatewayController extends Controller
{
    const paymentMethods = [
        'octopus' => 19,
        'payme' => 50,
        'fps' => 56,
        'alipayHK' => 35,
        'wechatpayHK' => 41,
        'pps' => 4,
        'vm'  => 70
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        $canAccess = config("app.jws.settings.finance");
        if (!$canAccess) {
            abort(404);
        }
    }

    /**
     * to payment gateway provider page.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentPage(Request $request, String $orderNum)
    {
        $user = Auth::user();

        $order = Order::where('order_number', $orderNum)
//            ->where('customer_id', $user->id)    // to ensure order belongs to login customer.
            ->first();

        $merchantClient = new MerchantClient();

        $currenturl = config('app.url').$_SERVER['REQUEST_URI'];
        $baseurl = substr($currenturl, 0, strrpos($currenturl, "/")+1);

        $datetime = date("YmdHis", time()) ;
        $data = [
            'version' => '5.0',
            'merchantid' => config('app.jws.mpay.merchant_id'),
            'storeid' => "1",
            'merchant_tid' => config('app.jws.mpay.terminal_id'),
            'datetime' => $datetime,
            'ordernum' => $datetime . '-' . $order->customer_id . '-' . $order->id,//$order->order_number,
            'amt' => $order->total_amount,
            'depositamt' => "",
            'currency' => config('app.jws.mpay.currency'),
            'paymethod' => 41,   // FIXME
            'accounttype' => "",
            'customizeddata' => $order->order_number . ':' . $order->customer_id . ':' . $order->id,
            'locale' => 'zh_TW',    //supports en_US, zh_TW, zh_CN
            'extrafield1' => "",
            'extrafield2' => "",
            'extrafield3' => "",
            'salt' => $merchantClient->genSalt(),
            'returnurl' => $baseurl."feedback",
            'notifyurl' => $baseurl."notify",
        ];

        // field that don't need to pass.
        $mpayPaymentURL = config('app.jws.mpay.payment_url');
        $securekey = config('app.jws.mpay.secure_key');

//        $urlType = $request->urlType;
//        if ($urlType=="UAT") {
//            $mpayPaymentURL = "https://demo.mobiletech.com.hk/MPay/MerchantPay.jsp";
//        } else if ($urlType=="UATM") {
//            $mpayPaymentURL = "https://demo.mobiletech.com.hk/MPayMobi/MerchantPay.jsp";
//        }


        $requestMessage = $data['salt'].";".$data['accounttype'].$data['amt'].$data['currency'].$data['customizeddata'].$datetime
            .$data['depositamt'].$data['extrafield1'].$data['extrafield2'].$data['extrafield3'].$data['locale']
            .$data['merchant_tid'].$data['merchantid'].$data['notifyurl'].$data['ordernum'].$data['paymethod']
            .$data['returnurl'].$data['storeid'].";".$securekey;
        $hash = $merchantClient->genHashValue($requestMessage);

        // ref: https://stackoverflow.com/questions/5576619/php-redirect-with-post-data
        return view("orders.mpay", ['data' => $data, 'url' => $mpayPaymentURL, 'hash' => $hash]);
    }

    /**
     * mPay: The return URL of merchant which payment response pass back by customer’s browser redirection.
     *
     * @return \Illuminate\Http\Response
     */
    public function returnPage(Request $request)
    {
        $hashvalid = "NotCheck";
        $merchantid = $request->merchantid;
        $storeid = $request->storeid;
        $merchant_tid = $request->merchant_tid;
        $ordernum = $request->ordernum;
        $cardnum = $request->cardnum;
        $ref = $request->ref;
        $amt = $request->amt;
        $depositamt = $request->depositamt;
        $currency = $request->currency;
        $rspcode = $request->rspcode;
        $customizeddata = $request->customizeddata;
        $authcode = $request->authcode;
        $fi_post_dt = $request->fi_post_dt;
        $sysdatetime = $request->sysdatetime;
        $settledate = $request->settledate;
        $paymethod = $request->paymethod;
        $accounttype = $request->accounttype;
        $tokenid = $request->tokenid;
        $salt = $request->salt;
        $hash = $request->hash;
        $securekey = config('app.jws.mpay.secure_key');

        $merchantClient = new MerchantClient();

        $responseMessage = $securekey.";".$accounttype.$amt.$authcode.$cardnum.$currency
            .$customizeddata.$depositamt.$fi_post_dt.$merchant_tid.$merchantid
            .$ordernum.$paymethod.$ref.$rspcode.$settledate
            .$storeid.$sysdatetime.$tokenid.";".$salt;
        $hashvalue = $merchantClient->genHashValue($responseMessage);
        if (strcasecmp($hash, $hashvalue) == 0) {
            //Hash valid
            // update order & payment status.
            $order = Order::where('order_number', $ordernum);
            if ($order->payment_status == 'pending') {
                $d1 = Carbon::createFromFormat("YmdHis", $sysdatetime);
                if ($amt >= $order->total_amount)
                    $order->payment_status = 'paid';
                else if ($order->total_amount > $amt)
                    $order->payment_status = 'partially';
                else $order->payment_status = 'pending';
                $order->save();

                // update payment as well.
                $order->payment->status = 'paid';
                $order->payment->payment_method = 'electronic';
                $order->payment->amount = $amt;
                $order->payment->payment_date_time = $d1->format('Y-m-d H:i:s');
                $order->payment->gateway = $this->getGateway($paymethod);
                $order->payment->save();
            }
        } else {
            $hashvalid = "False";
        }
    }

    /**
     * mPay: The notify URL of merchant which receive payment callback response from mPay server to merchant server directly.
     * The URL must be accessible by outside over the internet.
     * It is recommended to use different URL with the retunurl to handle two kinds of responses.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifyPage(Request $request)
    {
        $hashvalid = "NotCheck";
        $merchantid = config('app.jws.mpay.merchant_id');
        $storeid = $request->storeid;
        $merchant_tid = $request->merchant_tid;
        $ordernum = $request->ordernum;
        $cardnum = $request->cardnum;
        $ref = $request->ref;
        $amt = $request->amt;
        $depositamt = $request->depositamt;
        $currency = $request->currency;
        $rspcode = $request->rspcode;
        $customizeddata = $request->customizeddata;
        $authcode = $request->authcode;
        $fi_post_dt = $request->fi_post_dt;
        $sysdatetime = $request->sysdatetime;
        $settledate = $request->settledate;
        $paymethod = $request->paymethod;
        $accounttype = $request->accounttype;
        $tokenid = $request->tokenid;
        $salt = $request->salt;
        $hash = $request->hash;
        $securekey = config('app.jws.mpay.secure_key');

        $merchantClient = new MerchantClient();

        $responseMessage = $securekey.";".$accounttype.$amt.$authcode.$cardnum.$currency
            .$customizeddata.$depositamt.$fi_post_dt.$merchant_tid.$merchantid
            .$ordernum.$paymethod.$ref.$rspcode.$settledate
            .$storeid.$sysdatetime.$tokenid.";".$salt;
        $hashvalue = $merchantClient->genHashValue($responseMessage);
        if (strcasecmp($hash, $hashvalue) == 0) {
            //Hash valid
            $hashvalid = "True";
        } else {
            $hashvalid = "False";
        }
    }

    private function getGateway($number) {
        foreach (self::paymentMethods as $key => $value) {
            if ($number == $value) {
                return $key;
            }
        }
        return $number . ' n/a';
    }
}
