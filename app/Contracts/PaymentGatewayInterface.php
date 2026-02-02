<?php
namespace App\Contracts;

interface PaymentGatewayInterface
{
    public function createPaymentSession(array $data);
    public function verifyPayment(string $reference);
    public function handleWebhook(array $payload);
}