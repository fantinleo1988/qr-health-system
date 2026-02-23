<?php
session_start();
// Garante que o usuário está logado
if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

// Inclui seus arquivos de configuração
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php'; // Inclui sua conexão com o banco de dados

// Importa as classes necessárias do SDK do Mercado Pago
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

// Configura sua chave secreta
MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);

// Lê os dados enviados pelo JavaScript
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// 1. Valida apenas os dados essenciais que vêm do Brick
if (!isset($data['token'], $data['payment_method_id'], $data['transaction_amount'], $data['installments'])) {
    http_response_code(400); 
    echo json_encode(['error' => 'Dados de pagamento essenciais do Brick estão faltando.']);
    exit;
}

// 2. Busca os dados completos do paciente no seu banco de dados usando o ID da sessão
try {
    $stmt = $pdo->prepare("SELECT nome, sobrenome, email, cpf FROM pacientes WHERE id = ?");
    $stmt->execute([$_SESSION['paciente']['id']]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        throw new Exception("Paciente não encontrado no banco de dados.");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar dados do paciente.', 'details' => $e->getMessage()]);
    exit;
}

// 3. Cria a requisição final de pagamento, combinando os dados do seu servidor com os dados do Brick
$client = new PaymentClient();
try {
    $request = [
        "transaction_amount" => (float)$data['transaction_amount'],
        "token" => $data['token'],
        "description" => "Cartão Físico Anamnese QR",
        "installments" => (int)$data['installments'],
        "payment_method_id" => $data['payment_method_id'],
        "payer" => [
            "email" => $paciente['email'], // Usa o e-mail do seu banco (mais seguro)
            "first_name" => $paciente['nome'],
            "last_name" => $paciente['sobrenome'],
            "identification" => [
                "type" => "CPF",
                "number" => $paciente['cpf'] // Usa o CPF do seu banco
            ]
        ]
    ];
    
    // O issuer_id (ID do banco emissor) só é enviado para cartões de crédito
    if (isset($data['issuer_id'])) {
        $request['issuer_id'] = (int)$data['issuer_id'];
    }

    $payment = $client->create($request);

    // Retorna o resultado para o JavaScript
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $payment->status,
        'id' => $payment->id
    ]);

} catch (MPApiException $e) {
    http_response_code(400);
    $response_content = $e->getApiResponse()->getContent();
    $details = json_decode($response_content, true) ?? ['raw' => $response_content];
    echo json_encode([
        'error' => 'Erro da API do Mercado Pago.',
        'details' => $details
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno no servidor.',
        'details' => $e->getMessage()
    ]);
}