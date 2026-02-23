<?php
session_start();

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php?erro=vazio');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

// Configurações do SDK
MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);
MercadoPagoConfig::setIntegratorId("SEU_INTEGRATOR_ID_AQUI"); // AJUSTE 5

// Monta os itens a partir do carrinho
$itens_para_pagamento = [];
foreach ($_SESSION['carrinho'] as $produto) {
    $itens_para_pagamento[] = [
        "id" => $produto['id'],
        "title" => $produto['nome'],
        "description" => "Cartão de emergência com QR Code para acesso a ficha de anamnese.", // AJUSTE 1
        "picture_url" => "https://anamnese-qr.freeddns.org/exemplo-cracha.png", // AJUSTE 1
        "quantity" => $produto['quantidade'],
        "unit_price" => (float)$produto['preco'],
        "currency_id" => "BRL"
    ];
}

$email_paciente = $_SESSION['paciente']['email'] ?? null;

$client = new PreferenceClient();

// Cria a preferência de pagamento com todos os ajustes
$preference = $client->create([
    "items" => $itens_para_pagamento,
    "payer" => [
        "email" => $email_paciente
    ],
    "payment_methods" => [
        "excluded_payment_methods" => [
            ["id" => "boleto"] // AJUSTE 3
        ],
        "installments" => 12 // AJUSTE 2
    ],
    "back_urls" => [
        "success" => "https://anamnese-qr.freeddns.org/pagamento-sucesso.php",
        "failure" => "https://anamnese-qr.freeddns.org/pagamento-falha.php",
        "pending" => "https://anamnese-qr.freeddns.org/pagamento-pendente.php"
    ],
    "auto_return" => "approved",
    "external_reference" => "PEDIDO_ANAMNESE_" . uniqid(), // AJUSTE 4
    "notification_url" => "https://anamnese-qr.freeddns.org/notificacao-pagamento.php", // AJUSTE 6
]);

// Limpa o carrinho APENAS na página de sucesso
// unset($_SESSION['carrinho']); // << Mantenha esta linha COMENTADA ou REMOVIDA daqui

header('Content-Type: application/json');
echo json_encode([
    'preferenceId' => $preference->id,
    'init_point' => $preference->init_point
]);