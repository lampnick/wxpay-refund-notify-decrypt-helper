# wxpay-refund-notify-decrypt-helper
微信退款通知解密，消息回复帮助类

此类实现了通知消息的解密，要处理自己的业务逻辑需要实现\libs\wxpay\WxpayRefundNotifyHelper::handelInternal()方法。

调用方式：

1.引入类：

        require_once 'WxpayRefundNotifyHelper.php';

2.实例化并调用handle方法：

        $refundNotify = new WxpayRefundNotifyHelper();
  
        $refundNotify->handle();
