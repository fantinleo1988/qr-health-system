<?php
// Adicione estas 3 linhas para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Linha crucial para limpar o carrinho APÓS o sucesso do pagamento
if (isset($_SESSION['carrinho'])) {
    unset($_SESSION['carrinho']);
}

// Inclui os arquivos necessários
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

// Importa as classes da v3 do SDK
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

$payment_info = null;
$error_message = null;

// Pega o ID do pagamento da URL
$payment_id = $_GET['payment_id'] ?? null;

if ($payment_id) {
    try {
        // Configura o Access Token
        MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);

        // Cria um cliente de pagamento
        $client = new PaymentClient();
        // Busca os dados do pagamento na API do Mercado Pago
        $payment = $client->get($payment_id);

        // Verifica se o pagamento foi encontrado e está aprovado
        if ($payment && $payment->status == 'approved') {
            $payment_info = [
                'id' => $payment->id,
                'status' => 'Aprovado',
                'total' => number_format($payment->transaction_amount, 2, ',', '.'),
                'payment_method' => $payment->payment_method_id,
                'card_last_four' => $payment->card->last_four_digits ?? 'N/A',
            ];
            
            // AQUI você pode salvar o $payment->id no seu banco de dados, associado ao pedido.

        } else {
            $error_message = "O pagamento não foi aprovado ou não foi encontrado.";
        }
    } catch (MPApiException $e) {
        $error_message = "Erro ao comunicar com a API do Mercado Pago: " . $e->getApiResponse()->getContent();
    } catch (Exception $e) {
        $error_message = "Ocorreu um erro geral: " . $e->getMessage();
    }
} else {
    $error_message = "ID de pagamento não encontrado na URL.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Status do Pagamento - Anamnese QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card text-center shadow-sm">
                
                <?php if ($payment_info): ?>
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-check-circle-fill me-2"></i>Pagamento Aprovado!</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="lead">Obrigado! Seu pedido foi confirmado com sucesso.</p>
                        <hr>
                        <div class="text-start">
                            <h5 class="mb-3">Detalhes da Transação</h5>
                            <p><strong>ID do Pagamento:</strong> <?= htmlspecialchars($payment_info['id']); ?></p>
                            <p><strong>Valor Total:</strong> R$ <?= htmlspecialchars($payment_info['total']); ?></p>
                            <p><strong>Método:</strong> <?= htmlspecialchars(ucfirst($payment_info['payment_method'])); ?> terminado em <?= htmlspecialchars($payment_info['card_last_four']); ?></p>
                        </div>
                        <hr>
                        <div class="alert alert-info mt-4">
                             <i class="bi bi-truck me-2"></i>
                             Seu cartão será produzido e enviado no prazo de <strong>10 dias úteis</strong>.
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="area-paciente.php" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>Voltar para Minha Área
                        </a>
                    </div>
                    
                <?php else: ?>
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Falha na Verificação</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="lead">Houve um problema ao verificar seu pagamento.</p>
                        <div class="alert alert-danger mt-3">
                            <strong>Motivo:</strong> <?= htmlspecialchars($error_message); ?>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="area-paciente.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Voltar para Minha Área
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

</body>
</html>