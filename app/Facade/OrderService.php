<?php

namespace App\Facade;

use App\Services\NotificationsService;

class OrderService
{
    public function sendPaymentNotifications($tamplte_name, $order, $userId) {
        $placeholderService = new PlaceholderService();
        $notificationService = new NotificationsService();
        // send mail if notify option enabled.
        $payload = [
            'template' => $tamplte_name,
            'placeholders' => $placeholderService->getOrderData($order),
            // extra params.
            'data' => [
                'page' => 'order',
                'customer_name' => $order->customer->name,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_date' => $order->order_date
            ]
        ];
        $resp = $notificationService->sendToCustomer($order->customer, $payload, $userId);
        return $resp;
    }

}
