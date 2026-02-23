<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php'; // Para usar a constante MP_ACCESS_TOKEN
require_once __DIR__ . '/includes/db.php';

// Importa as classes da v3 do SDK
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

// É uma boa prática guardar um log de todas as notificações recebidas
$log_file = __DIR__ . '/mercado_pago_log.txt';

// Função para facilitar o log
function log_notification($message) {
    global $log_file;
    file_put_contents($log_file, date('[Y-m-d H:i:s]') . " " . $message . "\n", FILE_APPEND);
}

// Responde imediatamente ao Mercado Pago com 200 OK para evitar retentativas
http_response_code(200);
flush(); // Envia a resposta e continua processando

// Pega os dados da notificação
$json_data = json_decode(file_get_contents('php://input'), true);

if (!$json_data) {
    log_notification("Notificação recebida sem corpo JSON.");
    exit;
}

log_notification("Notificação Recebida: " . json_encode($json_data));

// Verifica se é uma notificação de pagamento
if (isset($json_data['type']) && $json_data['type'] === 'payment' && isset($json_data['data']['id'])) {
    $payment_id = $json_data['data']['id'];

    try {
        // --- INÍCIO DA ATUALIZAÇÃO PARA SDK V3 ---
        
        // Configura o Access Token a partir do seu arquivo de configuração
        MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);

        // Cria um cliente de pagamento e busca os detalhes
        $client = new PaymentClient();
        $payment = $client->get($payment_id);
        
        // --- FIM DA ATUALIZAÇÃO PARA SDK V3 ---

        if ($payment) {
            // Pega o ID do nosso pedido, que guardámos na 'external_reference'
            $pedido_id_completo = $payment->external_reference;
            
            // Extrai apenas o ID numérico, se seu external_reference for como "PEDIDO_ANAMNESE_xxx"
            // Se for apenas um número, esta lógica pode ser simplificada.
            $pedido_id_parts = explode('_', $pedido_id_completo);
            $pedido_id = end($pedido_id_parts);
            
            $status_pagamento = $payment->status; // ex: "approved", "pending", "rejected"
            $mercado_pago_id_transacao = $payment->id;

            // Prepara o status de produção com base no status do pagamento
            $status_producao = 'Aguardando Pagamento';
            if ($status_pagamento === 'approved') {
                $status_producao = 'Em produção';
            } elseif (in_array($status_pagamento, ['rejected', 'cancelled', 'failed'])) {
                $status_producao = 'Cancelado';
            }

            // Atualiza o nosso banco de dados
            // ATENÇÃO: Verifique se sua tabela se chama 'pedidos_cartao' e se as colunas estão corretas.
            $stmt = $pdo->prepare(
                "UPDATE pedidos_cartao 
                   SET status_pagamento = :status_pagamento, 
                       mercado_pago_id = :mercado_pago_id,
                       status_producao = :status_producao
                 WHERE id = :pedido_id"
            );
            $stmt->execute([
                ':status_pagamento' => $status_pagamento,
                ':mercado_pago_id' => $mercado_pago_id_transacao,
                ':status_producao' => $status_producao,
                ':pedido_id' => $pedido_id
            ]);

            log_notification("Pedido {$pedido_id} atualizado para status: {$status_pagamento}");
        } else {
            log_notification("Pagamento com ID {$payment_id} não encontrado na API.");
        }
    } catch (MPApiException $e) {
        log_notification("Erro da API do MP ao processar notificação: " . $e->getApiResponse()->getContent());
    } catch (Exception $e) {
        log_notification("Erro geral ao processar notificação: " . $e->getMessage());
    }
} else {
    log_notification("Notificação não é do tipo 'payment' ou não possui ID.");
}
?>