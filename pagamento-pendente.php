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

$payment_info = null;
$error_message = null;
$payment_id = $_GET['payment_id'] ?? null;

if ($payment_id) {
    try {
        MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);
        $client = new PaymentClient();
        $payment = $client->get($payment_id);

        if ($payment) {
            $payment_info = [
                'id' => $payment->id,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'boleto_url' => $payment->transaction_details->external_resource_url ?? null,
                'boleto_barcode' => $payment->barcode->content ?? null,
            ];
        } else {
            $error_message = "Pagamento não encontrado.";
        }
    } catch (Exception $e) {
        $error_message = "Ocorreu um erro ao verificar o status do pagamento.";
        error_log("Erro ao buscar detalhes do pagamento pendente: " . $e->getMessage());
    }
} else {
    $error_message = "ID de pagamento não fornecido.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Pendente - Anamnese QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card text-center shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Pagamento Pendente</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($payment_info): ?>
                        <p class="lead">Seu pagamento está aguardando confirmação.</p>
                        <hr>
                        <div class="alert alert-info">
                            <p class="mb-0">Assim que o pagamento for aprovado, você receberá uma notificação por e-mail.</p>
                        </div>
                        
                        <?php if ($payment_info['boleto_url']): ?>
                            <div class="d-grid gap-2 mt-4">
                                <a href="<?= htmlspecialchars($payment_info['boleto_url']); ?>" target="_blank" class="btn btn-lg btn-primary">
                                    <i class="bi bi-printer-fill"></i> Visualizar e Imprimir Boleto
                                </a>
                            </div>
                            <p class="mt-3">Ou copie o código de barras:</p>
                            <input type="text" class="form-control text-center" value="<?= htmlspecialchars($payment_info['boleto_barcode']); ?>" readonly>
                        <?php endif; ?>

                        <p class="text-muted small mt-4">
                            ID da transação para referência: <?= htmlspecialchars($payment_info['id']); ?>
                        </p>
                    <?php else: ?>
                        <p class="lead">Ocorreu um erro ao buscar os detalhes do seu pagamento.</p>
                        <div class="alert alert-danger">
                           <?= htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light">
                    <a href="area-paciente.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i> Voltar para Minha Área
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>