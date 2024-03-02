<?php

namespace App\Http\Controllers;

use App\Facade\OrderService;
use App\Helpers\Response\mPayResponse\PayMethod;
use App\Models\Order;
use App\Helpers\Response\mPayResponse\AliPayHk;
use App\Helpers\Response\mPayResponse\mPay;
use App\Helpers\Response\mPayResponse\PayMe;
use App\Helpers\Response\mPayResponse\WeChat;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\mPay\inc\MerchantClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PaymentGatewayController extends Controller
{
    private $orderService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        $canAccess = config("app.jws.settings.finance") && config("app.jws.settings.payment_gateway");
        if (!$canAccess) {
            abort(404);
        }
        $this->orderService = $orderService;
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

        $datetime = date("YmdHis", time());
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
            'paymethod' => 0,   // FIXME select paymethod in our client side?
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

        $urlType = $request->urlType;
        if ($urlType=="app") {
            $mpayPaymentURL = config('app.jws.mpay.mobile_payment_url');
        }


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
     * 419 error. ref: https://www.educative.io/answers/what-is-the-419-page-expired-error-and-its-solution-in-laravel
     *
     * @return \Illuminate\Http\Response
     */
    public function returnPage(Request $request)
    {
        $hashvalid = "NotCheck";
        $response = [
            'merchantid' => $request->post('merchantid'),
            'storeid' => $request->post('storeid'),
            'merchant_tid' => $request->post('merchant_tid'),
            'ordernum' => $request->post('ordernum'),
            'cardnum' => $request->post('cardnum'),
            'ref' => $request->post('ref'),
            'amt' => $request->post('amt'),
            'depositamt' => $request->post('depositamt'),
            'currency' => $request->post('currency'),
            'rspcode' => $request->post('rspcode'),
            'customizeddata' => $request->post('customizeddata'),
            'authcode' => $request->post('authcode'),
            'fi_post_dt' => $request->post('fi_post_dt'),
            'sysdatetime' => $request->post('sysdatetime'),
            'settledate' => $request->post('settledate'),
            'paymethod' => $request->post('paymethod'),
            'accounttype' => $request->post('accounttype'),
            'tokenid' => $request->post('tokenid'),
        ];
        $salt = $request->post('salt');
        $hash = $request->post('hash');
        $securekey = config('app.jws.mpay.secure_key');

        $merchantClient = new MerchantClient();

        $responseMessage = $securekey.";".$response['accounttype'].$response['amt'].$response['authcode'].$response['cardnum'].$response['currency']
            .$response['customizeddata'].$response['depositamt'].$response['fi_post_dt'].$response['merchant_tid'].$response['merchantid']
            .$response['ordernum'].$response['paymethod'].$response['ref'].$response['rspcode'].$response['settledate']
            .$response['storeid'].$response['sysdatetime'].$response['tokenid'].";".$salt;
        $hashvalue = $merchantClient->genHashValue($responseMessage);
        if (strcasecmp($hash, $hashvalue) == 0) {
            //Hash valid
            $ary = explode(':', $response['customizeddata']);
            $order = Order::where('order_number', $ary[0])->first();
            //
            $gw_paymentMethod = $this->getGateway($response['paymethod']);
            $respCls = $this->getResponseClass($gw_paymentMethod);

            $respCode = $response['rspcode'];
            // 100 = Transaction successful.
            if (100 == $respCode) {
                $d1 = Carbon::createFromFormat("YmdHis", $response['sysdatetime']);
                DB::beginTransaction();
                $customer_id = $ary[1];
                $order_id = $ary[2];
                // update order & payment status.
                if ($order->payment_status == 'pending') {
                    if ($response['amt'] >= $order->total_amount) {
                        $order->payment_status = 'paid';
                        $order->order_status = 'confirmed';
                    } else if ($order->total_amount > $response['amt']) {
                        $order->payment_status = 'partially';
                        $order->order_status = 'confirmed';
                    } else {
                        $order->payment_status = 'pending';
                    }
                    $order->save();

                    // update appointment if it's not a package.
                    foreach ($order->details as $item) {
                        if ($item->booking && $item->booking->appointment && $item->booking->appointment->user_id == $order->customer_id) {
                            $item->booking->appointment->status = 'approved';
                            $item->booking->appointment->save();
                        }
                    }

                    // update payment as well.
                    $order->payment->status = 'paid';
                    $order->payment->payment_method = 'electronic';
                    $order->payment->amount = $response['amt'];
                    $order->payment->payment_date_time = $d1->format('Y-m-d H:i:s');
                    $order->payment->gateway_msg = $respCls->getResponseMessage($respCode);
                    $order->payment->gateway = $gw_paymentMethod;
                    $order->payment->gateway_response = $response;
                    $order->payment->save();

                    DB::commit();

                    $user = Auth::user();
                    // send successful email to client.
                    $resp = $this->orderService->sendOrderNotifications('payment_successful', $order, $user->id);

                    // redirect to a successful page where to show some "Succeed" message.
                    return Redirect::to(config('app.client_url') . '/#/payment-successful/' . $order->payment->id);
                }
            } else {
                $d1 = Carbon::now();
                if ($response['sysdatetime']) {
                    $d1 = Carbon::createFromFormat("YmdHis", $response['sysdatetime'],);
                }
                $d1->setTimezone(new DateTimeZone(config("app.jws.local_timezone")));   // must set timezone, otherwise the punch-in time use UTC(app.php) and can't checkin.
                $order->payment->payment_date_time = $d1->format('Y-m-d H:i:s');
                $order->payment->gateway = $this->getGateway($response['paymethod']);
                $order->payment->gateway_msg = $respCls->getResponseMessage($respCode);
                $order->payment->gateway_response = $response;
                $order->payment->save();

                // redirect to a failure page where to show some "Failed" message.
                return Redirect::to(config('app.client_url') . '/#/payment-fail/' . $order->payment->id);

            }
            return Redirect::to(config('app.client_url') . '/#/finance');
        } else {
            $hashvalid = "False";
            echo 'Issue with payment.';
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
        return PayMethod::getName($number);
    }

    private function getResponseClass(string $gw_paymentMethod)
    {
        switch ($gw_paymentMethod) {
            case 'payme':
            case 'payme_mob':
                return new PayMe();
            case 'alipayHK':
            case 'alipayHK_mob':
                return new AliPayHk();
            case 'wechatpayHK':
            case 'wechatpayHK_mob':
                return new WeChat();
        }
        return new mPay();
    }

}
