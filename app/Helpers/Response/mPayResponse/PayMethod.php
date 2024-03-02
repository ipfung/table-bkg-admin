<?php

namespace App\Helpers\Response\mPayResponse;

/**
 * From File: mPay_MerchantSetupGuide_Browser_v6.16
 */
class PayMethod
{

    public static $PC_ALIPAY_CN = 5;
    public static $PC_ALIPAY_HK = 35;
    public static $PC_AE = 39;
    public static $PC_ATOME = 57;
    public static $PC_BOC_PAY = 62;
    public static $PC_CUP_UPOP = 13;
    public static $PC_CUP_QUICK_PAY = 66;
    public static $PC_CYBS_VM_CARD = 46;   // VM = VISA / MASTER
    public static $PC_FPS_HSBC = 56;
    public static $PC_JETCO_VM_CARD = 7;
    public static $PC_MIGS_VM_CARD = 2;
    public static $PC_MPGS_VM_CARD = 37;
    public static $PC_NTT_VM_CARD = 64;
    public static $PC_OCTOPUS = 19;
    public static $PC_PAYME = 50;
    public static $PC_PPS = 4;
    public static $PC_TAP_AND_GO = 60;
    public static $PC_STANDARD_VM_CARD = 70;
    public static $PC_WECHAT_PAY = 41;

    public static $MOBILE_ALIPAY_CN = 23;
    public static $MOBILE_ALIPAY_HK = 36;
    public static $MOBILE_AE = 40;
    public static $MOBILE_APPLE_PAY = 49;
    public static $MOBILE_ATOME = 58;
    public static $MOBILE_BOC_PAY = 63;
    public static $MOBILE_CUP_UPOP = 29;
    public static $MOBILE_GOOGLE_PAY = 48;
    public static $MOBILE_CYBS_VM_CARD = 47;   // VM = VISA / MASTER
    public static $MOBILE_JETCO_VM_CARD = 32;
    public static $MOBILE_MIGS_VM_CARD = 28;
    public static $MOBILE_MPGS_VM_CARD = 38;
    public static $MOBILE_NTT_VM_CARD = 65;
    public static $MOBILE_OCTOPUS = 27;
    public static $MOBILE_PAYME = 51;
    public static $MOBILE_TAP_AND_GO = 61;
    public static $MOBILE_STANDARD_VM_CARD = 71;
    public static $MOBILE_WECHAT_PAY = 42;

    /**
    const paymentMethods = [
    'octopus' => PayMethod::$PC_OCTOPUS,
    'payme' => PayMethod::$PC_PAYME,
    'fps' => PayMethod::$PC_FPS_HSBC,
    'alipayHK' => PayMethod::$PC_ALIPAY_HK,
    'wechatpayHK' => PayMethod::$PC_WECHAT_PAY,
    'pps' => PayMethod::$PC_PPS,
    'vm'  => PayMethod::$PC_STANDARD_VM_CARD,
    // mobile
    'octopus_mob' => PayMethod::$MOBILE_OCTOPUS,
    'payme_mob' => PayMethod::$MOBILE_PAYME,
    'alipayHK_mob' => PayMethod::$MOBILE_ALIPAY_HK,
    'wechatpayHK_mob' => PayMethod::$MOBILE_WECHAT_PAY,
    ];
     */
    public static function getName($value) {
        switch ($value) {
            case PayMethod::$PC_AE:
                return 'ae';
            case PayMethod::$MOBILE_AE:
                return 'ae_mob';
            case PayMethod::$PC_ALIPAY_CN:
                return 'alipayCN';
            case PayMethod::$MOBILE_ALIPAY_CN:
                return 'alipayCN_mob';
            case PayMethod::$PC_ALIPAY_HK:
                return 'alipayHK';
            case PayMethod::$MOBILE_ALIPAY_HK:
                return 'alipayHK_mob';
            case PayMethod::$MOBILE_APPLE_PAY:
                return 'applePay';
            case PayMethod::$PC_ATOME:
                return 'atome';
            case PayMethod::$MOBILE_ATOME:
                return 'atome_mob';
            case PayMethod::$PC_BOC_PAY:
                return 'bocPay';
            case PayMethod::$MOBILE_BOC_PAY:
                return 'bocPay_mob';
            case PayMethod::$PC_CUP_UPOP:
                return 'cupUpop';
            case PayMethod::$MOBILE_CUP_UPOP:
                return 'cupUpop_mob';
            case PayMethod::$PC_CUP_QUICK_PAY:
                return 'cupQuickPay';
            case PayMethod::$MOBILE_GOOGLE_PAY:
                return 'googlePay';
            case PayMethod::$PC_CYBS_VM_CARD:
                return 'cybs';
            case PayMethod::$MOBILE_CYBS_VM_CARD:
                return 'cybs_mob';
            case PayMethod::$PC_FPS_HSBC:
                return 'fps';
            case PayMethod::$PC_JETCO_VM_CARD:
                return 'jetco';
            case PayMethod::$MOBILE_JETCO_VM_CARD:
                return 'jetco_mob';
            case PayMethod::$PC_MIGS_VM_CARD:
                return 'migs';
            case PayMethod::$MOBILE_MIGS_VM_CARD:
                return 'migs_mob';
            case PayMethod::$PC_MPGS_VM_CARD:
                return 'mpgs';
            case PayMethod::$MOBILE_MPGS_VM_CARD:
                return 'mpgs_mob';
            case PayMethod::$PC_NTT_VM_CARD:
                return 'ntt';
            case PayMethod::$MOBILE_NTT_VM_CARD:
                return 'ntt_mob';
            case PayMethod::$PC_OCTOPUS:
                return 'octopus';
            case PayMethod::$MOBILE_OCTOPUS:
                return 'octopus_mob';
            case PayMethod::$PC_PAYME:
                return 'payme';
            case PayMethod::$MOBILE_PAYME:
                return 'payme_mob';
            case PayMethod::$PC_PPS:
                return 'pps';
            case PayMethod::$PC_TAP_AND_GO:
                return 'tapAndGo';
            case PayMethod::$MOBILE_TAP_AND_GO:
                return 'tapAndGo_mob';
            case PayMethod::$PC_STANDARD_VM_CARD:
                return 'visaOrMaster';
            case PayMethod::$MOBILE_STANDARD_VM_CARD:
                return 'visaOrMaster_mob';
            case PayMethod::$PC_WECHAT_PAY:
                return 'wechat';
            case PayMethod::$MOBILE_WECHAT_PAY:
                return 'wechat_mob';
            default:
                return 'n/a';
        }
    }
}
