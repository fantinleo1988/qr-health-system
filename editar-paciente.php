<?php
session_start();

// 1. VERIFICAÇÃO DE SESSÃO E AUTORIZAÇÃO
if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    header('Location: login-paciente.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

// Pega o ID da sessão e da URL
$paciente_logado_id = $_SESSION['paciente']['id'];
$paciente_id_da_url = $_GET['id'] ?? 0;

// Garante que o paciente só pode editar o seu próprio perfil
if ($paciente_logado_id != $paciente_id_da_url) {
    header('Location: area-paciente.php'); 
    exit;
}

$errors = [];

// 2. LÓGICA DE ATUALIZAÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validação básica para evitar o erro de "Column cannot be null"
    if (empty($_POST['estado_civil'])) {
        $errors[] = "Por favor, selecione seu Estado Civil.";
    }
    if (empty($_POST['genero'])) {
        $errors[] = "Por favor, selecione seu Gênero.";
    }
    if (empty($_POST['pronomes'])) {
        $errors[] = "Por favor, selecione seus Pronomes.";
    }

    // Só prossegue se não houver erros de validação
    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            // Atualiza os dados principais
            $sql_paciente = "UPDATE pacientes SET 
                                nome_completo = :nome_completo, cpf = :cpf, email = :email, data_nascimento = :data_nascimento,
                                genero = :genero, pronomes = :pronomes, estado_civil = :estado_civil,
                                cep = :cep, logradouro = :logradouro, numero = :numero, complemento = :complemento,
                                bairro = :bairro, localidade = :localidade, uf = :uf, telefone = :telefone,
                                contato_emergencia_nome = :contato_emergencia_nome, telefone_emergencia = :telefone_emergencia,
                                condicao_saude = :condicao_saude, tipo_sanguineo = :tipo_sanguineo,
                                historico_familiar = :historico_familiar, alergias = :alergias, medico_responsavel = :medico_responsavel, observacoes = :observacoes
                            WHERE id = :id";
            
            $stmt_paciente = $pdo->prepare($sql_paciente);
            
            // O uso de ?? '' garante que se o campo não existir, envia string vazia (evita erro de undefined index), 
            // mas como validamos acima, estado_civil terá valor.
            $stmt_paciente->execute([
                ':nome_completo' => $_POST['nome_completo'] ?? '', 
                ':cpf' => $_POST['cpf'] ?? '', 
                ':email' => $_POST['email'] ?? '', 
                ':data_nascimento' => $_POST['data_nascimento'] ?? null, 
                ':genero' => $_POST['genero'] ?? 'Não informado', 
                ':pronomes' => $_POST['pronomes'] ?? 'Não informado', 
                ':estado_civil' => $_POST['estado_civil'] ?? 'Solteiro', // Valor padrão de segurança
                ':cep' => $_POST['cep'] ?? '', 
                ':logradouro' => $_POST['logradouro'] ?? '', 
                ':numero' => $_POST['numero'] ?? '', 
                ':complemento' => $_POST['complemento'] ?? '', 
                ':bairro' => $_POST['bairro'] ?? '', 
                ':localidade' => $_POST['localidade'] ?? '', 
                ':uf' => $_POST['uf'] ?? '', 
                ':telefone' => $_POST['telefone'] ?? '', 
                ':contato_emergencia_nome' => $_POST['contato_emergencia_nome'] ?? '', 
                ':telefone_emergencia' => $_POST['telefone_emergencia'] ?? '', 
                ':condicao_saude' => $_POST['condicao_saude'] ?? '', 
                ':tipo_sanguineo' => $_POST['tipoSanguineo'] ?? '', // Atenção ao name no HTML (tipoSanguineo)
                ':historico_familiar' => $_POST['historico_familiar'] ?? '', 
                ':alergias' => $_POST['alergias'] ?? '', 
                ':medico_responsavel' => $_POST['medico_responsavel'] ?? '', 
                ':observacoes' => $_POST['observacoes'] ?? '',
                ':id' => $paciente_logado_id
            ]);

            // Sincroniza medicamentos
            $stmt_delete = $pdo->prepare("DELETE FROM medicamentos WHERE paciente_id = ?");
            $stmt_delete->execute([$paciente_logado_id]);

            if (isset($_POST['medicamentos']) && is_array($_POST['medicamentos'])) {
                $sql_medicamento = "INSERT INTO medicamentos (paciente_id, nome, dosagem, frequencia) VALUES (?, ?, ?, ?)";
                $stmt_medicamento = $pdo->prepare($sql_medicamento);
                
                // Verifica se os índices existem antes de acessar
                if (isset($_POST['medicamentos']['nome'])) {
                    foreach ($_POST['medicamentos']['nome'] as $index => $nome) {
                        if (!empty($nome)) {
                            $dosagem = $_POST['medicamentos']['dosagem'][$index] ?? '';
                            $frequencia = $_POST['medicamentos']['frequencia'][$index] ?? '';
                            $stmt_medicamento->execute([$paciente_logado_id, $nome, $dosagem, $frequencia]);
                        }
                    }
                }
            }
            
            $pdo->commit();
            $_SESSION['flash_message'] = "Seus dados foram atualizados com sucesso!";
            header('Location: area-paciente.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Erro ao atualizar dados: " . $e->getMessage();
        }
    }
}

// 3. BUSCAR DADOS ATUAIS
try {
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
    $stmt->execute([$paciente_logado_id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        session_destroy();
        header('Location: login-paciente.php');
        exit;
    }
    
    $stmt_medicamentos = $pdo->prepare("SELECT * FROM medicamentos WHERE paciente_id = ?");
    $stmt_medicamentos->execute([$paciente_logado_id]);
    $medicamentos_paciente = $stmt_medicamentos->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erro ao carregar seus dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Meus Dados - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sticky Footer */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .main-content {
            flex: 1;
        }
        /* Estilos do Rodapé Padrão */
        footer a { text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #ffffff !important; text-decoration: underline; }
        
        /* Botão Admin no Footer */
        .admin-btn-hover:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand"><i class="bi bi-person-lines-fill"></i> Área do Paciente</span>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-light btn-sm" href="logout-paciente.php">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content">
        <div class="row">
            
            <div class="col-md-3">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white fw-bold text-primary">Menu</div>
                    <div class="list-group list-group-flush">
                        <a href="area-paciente.php" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-person-lines-fill me-2"></i> Meus Dados
                        </a>
                        <a href="editar-paciente.php?id=<?= $paciente['id'] ?>" class="list-group-item list-group-item-action active border-0">
                            <i class="bi bi-pencil-square me-2"></i> Editar Dados
                        </a>
                        <a href="alterar-senha-paciente.php" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-shield-lock me-2"></i> Alterar Senha
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i> Editar Meus Dados</h5>
                        <a href="area-paciente.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="editar-paciente.php?id=<?= $paciente['id'] ?>" method="POST" class="row g-3">
                            
                            <div class="col-12"><h6 class="text-secondary text-uppercase border-bottom pb-2">Dados Pessoais</h6></div>
                            
                            <div class="col-md-6">
                                <label for="nome_completo" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($paciente['nome_completo'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="<?= htmlspecialchars($paciente['cpf'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($paciente['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($paciente['data_nascimento'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="genero" class="form-label">Gênero *</label>
                                <select id="genero" name="genero" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="Mulher" <?= ($paciente['genero'] ?? '') == 'Mulher' ? 'selected' : '' ?>>Mulher</option>
                                    <option value="Homem" <?= ($paciente['genero'] ?? '') == 'Homem' ? 'selected' : '' ?>>Homem</option>
                                    <option value="Mulher Trans" <?= ($paciente['genero'] ?? '') == 'Mulher Trans' ? 'selected' : '' ?>>Mulher Trans</option>
                                    <option value="Homem Trans" <?= ($paciente['genero'] ?? '') == 'Homem Trans' ? 'selected' : '' ?>>Homem Trans</option>
                                    <option value="Não binário" <?= ($paciente['genero'] ?? '') == 'Não binário' ? 'selected' : '' ?>>Não binário</option>
                                    <option value="Outro" <?= ($paciente['genero'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="pronomes" class="form-label">Pronomes *</label>
                                <select id="pronomes" name="pronomes" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="Ela/dela" <?= ($paciente['pronomes'] ?? '') == 'Ela/dela' ? 'selected' : '' ?>>Ela/dela</option>
                                    <option value="Ele/dele" <?= ($paciente['pronomes'] ?? '') == 'Ele/dele' ? 'selected' : '' ?>>Ele/dele</option>
                                    <option value="Elu/delu" <?= ($paciente['pronomes'] ?? '') == 'Elu/delu' ? 'selected' : '' ?>>Elu/delu</option>
                                    <option value="Prefiro não informar" <?= ($paciente['pronomes'] ?? '') == 'Prefiro não informar' ? 'selected' : '' ?>>Prefiro não informar</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="estado_civil" class="form-label">Estado Civil *</label>
                                <select id="estado_civil" name="estado_civil" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php
                                    $estados_civis = ['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'União Estável', 'Separado', 'Prefiro não informar'];
                                    foreach($estados_civis as $ec) {
                                        $selected = ($paciente['estado_civil'] ?? '') == $ec ? 'selected' : '';
                                        echo "<option value='$ec' $selected>$ec</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12 mt-4"><h6 class="text-secondary text-uppercase border-bottom pb-2">Endereço</h6></div>
                            
                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="<?= htmlspecialchars($paciente['cep'] ?? '') ?>">
                            </div>
                            <div class="col-md-7">
                                <label for="logradouro" class="form-label">Rua/Logradouro</label>
                                <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= htmlspecialchars($paciente['logradouro'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="<?= htmlspecialchars($paciente['numero'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="<?= htmlspecialchars($paciente['complemento'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" value="<?= htmlspecialchars($paciente['bairro'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="localidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="localidade" name="localidade" value="<?= htmlspecialchars($paciente['localidade'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="uf" class="form-label">UF</label>
                                <input type="text" class="form-control" id="uf" name="uf" maxlength="2" value="<?= htmlspecialchars($paciente['uf'] ?? '') ?>">
                            </div>

                            <div class="col-12 mt-4"><h6 class="text-secondary text-uppercase border-bottom pb-2">Contatos de Emergência</h6></div>
                            
                            <div class="col-md-4">
                                <label for="telefone" class="form-label">Seu Telefone</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($paciente['telefone'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="contato_emergencia_nome" class="form-label">Nome do Contato</label>
                                <input type="text" class="form-control" id="contato_emergencia_nome" name="contato_emergencia_nome" value="<?= htmlspecialchars($paciente['contato_emergencia_nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="telefone_emergencia" class="form-label">Telefone do Contato</label>
                                <input type="text" class="form-control" id="telefone_emergencia" name="telefone_emergencia" value="<?= htmlspecialchars($paciente['telefone_emergencia'] ?? '') ?>">
                            </div>

                            <div class="col-12 mt-4"><h6 class="text-secondary text-uppercase border-bottom pb-2">Informações de Saúde</h6></div>
                            
                            <div class="col-md-6">
                                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo *</label>
                                <select id="tipo_sanguineo" name="tipoSanguineo" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php 
                                    $tipos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Não sei informar'];
                                    foreach ($tipos as $tipo) {
                                        $selected = ($paciente['tipo_sanguineo'] ?? '') == $tipo ? 'selected' : '';
                                        echo "<option value='$tipo' $selected>$tipo</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="condicao_saude" class="form-label">Condição Principal</label>
                                <select id="condicao_saude" name="condicao_saude" class="form-select">
                                    <option value="">Selecione...</option>
                                    <?php
                                    $condicoes = ['Pessoa com 60 anos ou mais', 'Pessoa com necessidades especiais (PnE)', 'Condição crônica de saúde', 'Condição rara de saúde', 'Nenhuma condição preexistente', 'Outras condições'];
                                    foreach($condicoes as $cond) {
                                        $sel = ($paciente['condicao_saude'] ?? '') == $cond ? 'selected' : '';
                                        echo "<option value='$cond' $sel>$cond</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="medico_responsavel" class="form-label">Médico Responsável</label>
                                <input type="text" class="form-control" id="medico_responsavel" name="medico_responsavel" value="<?= htmlspecialchars($paciente['medico_responsavel'] ?? '') ?>">
                            </div>
                            
                            <div class="col-12">
                                <label for="alergias" class="form-label">Alergias</label>
                                <textarea class="form-control" id="alergias" name="alergias" rows="2"><?= htmlspecialchars($paciente['alergias'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="historico_familiar" class="form-label">Histórico Familiar</label>
                                <textarea class="form-control" id="historico_familiar" name="historico_familiar" rows="2"><?= htmlspecialchars($paciente['historico_familiar'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Outras Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="2"><?= htmlspecialchars($paciente['observacoes'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-0 w-100">Medicamentos</h6>
                                </div>
                                <div id="medicamentos-container">
                                    <?php if (empty($medicamentos_paciente)): ?>
                                        <p class="text-muted small fst-italic no-meds-msg">Nenhum medicamento cadastrado.</p>
                                    <?php else: ?>
                                        <?php foreach ($medicamentos_paciente as $medicamento): ?>
                                            <div class="row g-2 mb-2 align-items-center medicamento-row">
                                                <div class="col-md-4"><input type="text" name="medicamentos[nome][]" class="form-control form-control-sm" placeholder="Nome" value="<?= htmlspecialchars($medicamento['nome']) ?>"></div>
                                                <div class="col-md-3"><input type="text" name="medicamentos[dosagem][]" class="form-control form-control-sm" placeholder="Dosagem" value="<?= htmlspecialchars($medicamento['dosagem']) ?>"></div>
                                                <div class="col-md-4"><input type="text" name="medicamentos[frequencia][]" class="form-control form-control-sm" placeholder="Frequência" value="<?= htmlspecialchars($medicamento['frequencia']) ?>"></div>
                                                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm w-100" onclick="removerMedicamento(this)"><i class="bi bi-trash"></i></button></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="adicionarMedicamento()"><i class="bi bi-plus-circle"></i> Adicionar Medicamento</button>
                            </div>
                            
                            <hr class="mt-4">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3 text-primary">Anamnese QR</h5>
                    <p class="small text-secondary">
                        Facilitando o acesso a informações vitais de saúde através da tecnologia. 
                        Sua segurança e bem-estar são nossa prioridade número um.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Contato</h5>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt-fill me-2 text-primary"></i> 
                            R. José Cosme Pamplona - Bela Vista, Palhoça - SC
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill me-2 text-primary"></i> 
                            suporte@anamneseqr.com.br
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill me-2 text-primary"></i> 
                            (48) 99135-9339
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Suporte</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="ajuda.php" class="text-secondary">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="faq.php" class="text-secondary">Perguntas Frequentes (FAQ)</a></li>
                        <li class="mb-2"><a href="reportar_erro.php" class="text-secondary">Reportar um Erro</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Privacidade (LGPD)</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="politica_privacidade.php" class="text-secondary">Política de Privacidade</a></li>
                        <li class="mb-2"><a href="termos_uso.php" class="text-secondary">Termos de Uso</a></li>
                        <li class="mb-2"><a href="portal_privacidade.php" class="text-secondary">Portal da Privacidade</a></li>
                    </ul>

                    <div class="mt-4">
                        <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-3 py-2 w-100">
                            <i class="bi bi-shield-lock-fill me-1"></i> Dados Protegidos
                        </span>
                    </div>

                    <div class="mt-2">
                        <a href="login.php" class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-3 py-2 w-100 text-decoration-none admin-btn-hover">
                            <i class="bi bi-person-badge-fill me-1"></i> Acesso Administrativo
                        </a>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-secondary mb-0">© <?= date('Y') ?> Sistema Anamnese QR. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="redes_sociais.php" class="text-secondary me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="redes_sociais.php" class="text-secondary me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="redes_sociais.php" class="text-secondary"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function adicionarMedicamento() {
        const container = document.getElementById('medicamentos-container');
        // Remove a mensagem "Nenhum medicamento cadastrado" se ela existir
        const noMedicationMessage = container.querySelector('.no-meds-msg');
        if (noMedicationMessage) {
            noMedicationMessage.remove();
        }
        
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 mb-2 align-items-center medicamento-row';
        newRow.innerHTML = `
            <div class="col-md-4"><input type="text" name="medicamentos[nome][]" class="form-control form-control-sm" placeholder="Nome do medicamento"></div>
            <div class="col-md-3"><input type="text" name="medicamentos[dosagem][]" class="form-control form-control-sm" placeholder="Dosagem"></div>
            <div class="col-md-4"><input type="text" name="medicamentos[frequencia][]" class="form-control form-control-sm" placeholder="Frequência"></div>
            <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm w-100" onclick="removerMedicamento(this)"><i class="bi bi-trash"></i></button></div>
        `;
        container.appendChild(newRow);
    }

    function removerMedicamento(button) {
        button.closest('.medicamento-row').remove();
    }
    </script>
</body>
</html>