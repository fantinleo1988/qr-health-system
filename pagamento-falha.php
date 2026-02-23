<?php
session_start();
// Redireciona para o login se não houver um paciente logado
if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    header('Location: login-paciente.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

$payment_status = 'recusado';
$status_detail = 'Não foi possível obter o motivo da recusa.';
$payment_id = $_GET['payment_id'] ?? null;

if ($payment_id) {
    try {
        MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);
        $client = new PaymentClient();
        $payment = $client->get($payment_id);
        
        if ($payment) {
            $payment_status = $payment->status; // ex: "rejected"
            $status_detail = $payment->status_detail; // ex: "cc_rejected_bad_filled_card_number"
        }
    } catch (Exception $e) {
        // Se a API falhar, mantém a mensagem de erro padrão
        error_log("Erro ao buscar detalhes do pagamento com falha: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Falha no Pagamento - Anamnese QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card text-center shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-x-circle-fill me-2"></i>Pagamento Recusado</h4>
                </div>
                <div class="card-body p-4">
                    <p class="lead">Houve um problema ao processar seu pagamento.</p>
                    <div class="alert alert-warning">
                        <p class="mb-1"><strong>Status:</strong> <?= htmlspecialchars(ucfirst($payment_status)); ?></p>
                        <p class="mb-0"><strong>Motivo:</strong> <?= htmlspecialchars($status_detail); ?></p>
                    </div>
                    <hr>
                    <h5 class="mt-4">O que você pode fazer?</h5>
                    <ul class="list-unstyled text-start mx-auto" style="max-width: 300px;">
                        <li><i class="bi bi-check text-success"></i> Verifique os dados do cartão.</li>
                        <li><i class="bi bi-check text-success"></i> Tente usar outro cartão.</li>
                        <li><i class="bi bi-check text-success"></i> Entre em contato com seu banco.</li>
                    </ul>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="area-paciente.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Voltar para Minha Área
                    </a>
                    <a href="carrinho.php" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat me-2"></i>Tentar Pagar Novamente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>