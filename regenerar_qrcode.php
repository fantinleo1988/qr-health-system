<?php
session_start();
header('Content-Type: application/json'); // Define que a resposta será em JSON

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// 2. DEPENDÊNCIAS
require_once __DIR__ . "/includes/db.php";

// 3. OBTER DADOS DA REQUISIÇÃO
$dados = json_decode(file_get_contents('php://input'), true);
$paciente_id = $dados['paciente_id'] ?? null;

if (empty($paciente_id)) {
    echo json_encode(['success' => false, 'message' => 'ID do paciente não fornecido.']);
    exit;
}

// 4. GERAR A NOVA URL E ATUALIZAR O BANCO
try {
    // Monta a URL padrão do QR Code
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $nova_url_qrcode = $protocolo . $host . "/visualizar_paciente.php?id=" . $paciente_id;
    
    // Prepara e executa a atualização no banco de dados
    $stmt = $pdo->prepare("UPDATE pacientes SET qr_code = :qr_code WHERE id = :id");
    $stmt->execute([
        ':qr_code' => $nova_url_qrcode,
        ':id' => $paciente_id
    ]);

    // Verifica se alguma linha foi de fato atualizada
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'QR Code regenerado e atualizado com sucesso!',
            'new_qr_url' => $nova_url_qrcode
        ]);
    } else {
        // Isso pode acontecer se o paciente não for encontrado
        echo json_encode(['success' => false, 'message' => 'Paciente não encontrado ou nenhum dado foi alterado.']);
    }

} catch (PDOException $e) {
    // Em caso de erro, retorna uma mensagem de erro
    error_log("Erro ao regenerar QR Code: " . $e->getMessage()); // Grava o erro técnico no log do servidor
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar com o banco de dados.']);
}
?>