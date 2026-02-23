<?php
// Código de depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Resposta para requisições pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/includes/db.php';

// Decodifica o corpo da requisição JSON
$input = json_decode(file_get_contents('php://input'), true);

// Valida se o JSON é válido
if (!$input || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados de entrada inválidos ou mal formatados.']);
    exit;
}

// Inicia a transação para garantir a integridade dos dados
$pdo->beginTransaction();

try {
    // 1. Validação e sanitização dos dados do PACIENTE
    // Padronizando para usar os nomes que vêm do front-end (snake_case)
    $nome_completo = !empty($input['nome_completo']) ? htmlspecialchars(trim($input['nome_completo'])) : null;
    $email = !empty($input['email']) ? filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL) : null;
    $senha = !empty($input['senha']) ? $input['senha'] : null;

    if (!$nome_completo || !$email || !$senha) {
        throw new Exception('Nome, e-mail e senha são obrigatórios.');
    }
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Array com os dados para inserir na tabela 'pacientes'
    $paciente_data = [
        ':nome_completo' => $nome_completo,
        ':email' => $email,
        ':senha' => $senha_hash,
        ':cpf' => isset($input['cpf']) ? preg_replace('/[^0-9]/', '', $input['cpf']) : null,
        ':data_nascimento' => !empty($input['data_nascimento']) ? $input['data_nascimento'] : null,
        ':genero' => isset($input['genero']) ? htmlspecialchars($input['genero']) : null,
        ':pronomes' => isset($input['pronomes']) ? htmlspecialchars($input['pronomes']) : null,
        ':estado_civil' => isset($input['estado_civil']) ? htmlspecialchars($input['estado_civil']) : null,
        ':cep' => isset($input['cep']) ? htmlspecialchars($input['cep']) : null,
        ':logradouro' => isset($input['logradouro']) ? htmlspecialchars($input['logradouro']) : null,
        ':numero' => isset($input['numero']) ? htmlspecialchars($input['numero']) : null,
        ':bairro' => isset($input['bairro']) ? htmlspecialchars($input['bairro']) : null,
        ':localidade' => isset($input['localidade']) ? htmlspecialchars($input['localidade']) : null,
        ':uf' => isset($input['uf']) ? htmlspecialchars($input['uf']) : null,
        ':telefone' => isset($input['telefone']) ? htmlspecialchars($input['telefone']) : null,
        ':contato_emergencia_nome' => isset($input['contato_emergencia_nome']) ? htmlspecialchars($input['contato_emergencia_nome']) : null,
        ':telefone_emergencia' => isset($input['telefone_emergencia']) ? htmlspecialchars($input['telefone_emergencia']) : null,
        ':condicao_saude' => isset($input['condicao_saude']) ? htmlspecialchars($input['condicao_saude']) : null,
        ':tipo_sanguineo' => isset($input['tipo_sanguineo']) ? htmlspecialchars($input['tipo_sanguineo']) : null,
        ':historico_familiar' => isset($input['historico_familiar']) ? htmlspecialchars($input['historico_familiar']) : null,
        ':alergias' => isset($input['alergias']) ? htmlspecialchars($input['alergias']) : null,
        ':medico_responsavel' => isset($input['medico_responsavel']) ? htmlspecialchars($input['medico_responsavel']) : null,
        ':observacoes' => isset($input['observacoes']) ? htmlspecialchars($input['observacoes']) : null
    ];

    // 2. INSERÇÃO NA TABELA 'pacientes'
    // SQL corrigido com colunas e parâmetros correspondentes
    $sql_paciente = "INSERT INTO pacientes 
        (nome_completo, email, senha, cpf, data_nascimento, genero, pronomes, estado_civil, cep, logradouro, numero, bairro, localidade, uf, telefone, contato_emergencia_nome, telefone_emergencia, condicao_saude, tipo_sanguineo, historico_familiar, alergias, medico_responsavel, observacoes) 
        VALUES 
        (:nome_completo, :email, :senha, :cpf, :data_nascimento, :genero, :pronomes, :estado_civil, :cep, :logradouro, :numero, :bairro, :localidade, :uf, :telefone, :contato_emergencia_nome, :telefone_emergencia, :condicao_saude, :tipo_sanguineo, :historico_familiar, :alergias, :medico_responsavel, :observacoes)";
    
    $stmt_paciente = $pdo->prepare($sql_paciente);
    $stmt_paciente->execute($paciente_data);
    $paciente_id = $pdo->lastInsertId();

    if (!$paciente_id) {
        throw new Exception('Não foi possível obter o ID do novo paciente.');
    }

    // 3. INSERÇÃO NA TABELA 'medicamentos'
    // Verifica se existem medicamentos no input
    if (isset($input['medicamentos']) && is_array($input['medicamentos']) && !empty($input['medicamentos'])) {
        $sql_medicamento = "INSERT INTO medicamentos (paciente_id, nome, dosagem, frequencia) VALUES (:paciente_id, :nome, :dosagem, :frequencia)";
        $stmt_medicamento = $pdo->prepare($sql_medicamento);

        // Loop para cada medicamento enviado
        foreach ($input['medicamentos'] as $medicamento) {
            // Verifica se o medicamento tem os dados necessários
            if (!empty($medicamento['nome'])) {
                $stmt_medicamento->execute([
                    ':paciente_id' => $paciente_id,
                    ':nome' => htmlspecialchars($medicamento['nome']),
                    ':dosagem' => htmlspecialchars($medicamento['dosagem'] ?? ''),
                    ':frequencia' => htmlspecialchars($medicamento['frequencia'] ?? '')
                ]);
            }
        }
    }

    // Se tudo deu certo, confirma a transação
    $pdo->commit();

    // Retorna sucesso com o ID do paciente criado
    echo json_encode([
        'success' => true,
        'pacienteId' => $paciente_id, // Chave 'pacienteId' que o JavaScript espera
        'message' => 'Paciente salvo com sucesso!'
    ]);

} catch (Exception $e) {
    // Se algo deu errado, desfaz a transação
    $pdo->rollBack();
    http_response_code(500);
    // Retorna o erro específico que ocorreu
    echo json_encode(['success' => false, 'error' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?>