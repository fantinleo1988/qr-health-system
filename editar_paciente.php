<?php
session_start();

// 1. SEGURANÇA E DEPENDÊNCIAS
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . "/includes/db.php";

// 2. OBTER E VALIDAR O ID DO PACIENTE
$paciente_id = $_GET['id'] ?? 0;
if (empty($paciente_id)) {
    header('Location: pacientes.php');
    exit;
}

$errors = [];

// 3. LÓGICA DE ATUALIZAÇÃO (QUANDO O FORMULÁRIO É ENVIADO)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta dos dados do formulário
    $nome_completo = trim($_POST['nome_completo']);
    $cpf = trim($_POST['cpf']);
    // ... outros campos são coletados diretamente no array $params abaixo

    // Validação básica
    if (empty($nome_completo)) {
        $errors[] = "O nome completo é obrigatório.";
    }

    if (empty($errors)) {
        try {
            // Monta a query de UPDATE
            $sql = "UPDATE pacientes SET 
                        nome_completo = :nome_completo, 
                        cpf = :cpf,
                        email = :email,
                        data_nascimento = :nascimento,
                        genero = :genero,
                        pronomes = :pronomes,
                        estado_civil = :estado_civil,
                        cep = :cep,
                        logradouro = :logradouro,
                        numero = :numero,
                        complemento = :complemento,
                        bairro = :bairro,
                        localidade = :localidade,
                        uf = :uf,
                        telefone = :telefone,
                        contato_emergencia_nome = :contato_emergencia_nome,
                        telefone_emergencia = :telefone_emergencia,
                        condicao_saude = :condicao_saude,
                        tipo_sanguineo = :tipoSanguineo,
                        alergias = :alergias,
                        observacoes = :observacoes
                        -- Adicione outros campos conforme necessário
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            $params = [
                ':nome_completo' => $_POST['nome_completo'],
                ':cpf' => $_POST['cpf'],
                ':email' => $_POST['email'],
                ':nascimento' => $_POST['nascimento'],
                ':genero' => $_POST['genero'],
                ':pronomes' => $_POST['pronomes'],
                ':estado_civil' => $_POST['estado_civil'],
                ':cep' => $_POST['cep'],
                ':logradouro' => $_POST['logradouro'],
                ':numero' => $_POST['numero'],
                ':complemento' => $_POST['complemento'],
                ':bairro' => $_POST['bairro'],
                ':localidade' => $_POST['localidade'],
                ':uf' => $_POST['uf'],
                ':telefone' => $_POST['telefone'],
                ':contato_emergencia_nome' => $_POST['contato_emergencia_nome'],
                ':telefone_emergencia' => $_POST['telefone_emergencia'],
                ':condicao_saude' => $_POST['condicao_saude'],
                ':tipoSanguineo' => $_POST['tipoSanguineo'],
                // ':medicamentos' => $_POST['medicamentos'], // Lógica de medicamentos geralmente requer tabela separada ou tratamento JSON
                ':alergias' => $_POST['alergias'],
                ':observacoes' => $_POST['observacoes'],
                ':id' => $paciente_id
            ];

            // Lógica para senha (opcional)
            if (!empty($_POST['senha'])) {
                if ($_POST['senha'] !== $_POST['confirmarSenha']) {
                    $errors[] = "As senhas não coincidem.";
                } else {
                    // Adiciona a senha à query se não houver erros
                    // Nota: Isso requer refazer a query ou usar lógica condicional mais robusta
                    // Para simplificar, assumimos que se a senha for alterada, fazemos um UPDATE separado ou ajustamos a query acima
                    // A abordagem correta seria construir a query dinamicamente.
                    
                    // Exemplo simples de update de senha separado:
                    $stmtSenha = $pdo->prepare("UPDATE pacientes SET senha = :senha WHERE id = :id");
                    $stmtSenha->execute([
                        ':senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
                        ':id' => $paciente_id
                    ]);
                }
            }
            
            if (empty($errors)) {
                $stmt->execute($params);

                $_SESSION['flash_message'] = "Dados do paciente atualizados com sucesso!";
                header('Location: pacientes.php'); // Redireciona para a lista
                exit;
            }

        } catch (Exception $e) {
            $errors[] = "Erro ao atualizar paciente: " . $e->getMessage();
        }
    }
}

// 4. BUSCAR DADOS ATUAIS PARA PREENCHER O FORMULÁRIO
try {
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
    $stmt->execute([$paciente_id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        header('Location: pacientes.php');
        exit;
    }
} catch (Exception $e) {
    die("Erro ao carregar dados do paciente: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente - Sistema Médico</title>
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
        .password-wrapper { position:relative; }
        .toggle-password-icon { position:absolute; top:50%; right:15px; transform:translateY(-50%); cursor:pointer; }
        
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
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistema Médico</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                <a class="btn btn-outline-light" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i> Editar Paciente: <?= htmlspecialchars($paciente['nome_completo']) ?></h5>
                <a href="pacientes.php?id=<?= $paciente_id ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form id="editPatientForm" action="editar_paciente.php?id=<?= $paciente_id ?>" method="POST" class="row g-3">

                    <div class="col-12"><h5 class="text-secondary border-bottom pb-2">Dados Pessoais</h5></div>
                    <div class="col-md-6">
                        <label class="form-label" for="nome_completo">Nome Completo *</label>
                        <input id="nome_completo" name="nome_completo" class="form-control" value="<?= htmlspecialchars($paciente['nome_completo']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cpf">CPF *</label>
                        <input id="cpf" name="cpf" class="form-control" value="<?= htmlspecialchars($paciente['cpf']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">E-mail *</label>
                        <input id="email" name="email" type="email" class="form-control" value="<?= htmlspecialchars($paciente['email']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="nascimento">Nascimento *</label>
                        <input type="date" id="nascimento" name="nascimento" class="form-control" value="<?= htmlspecialchars($paciente['data_nascimento']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="genero">Gênero *</label>
                        <select id="genero" name="genero" class="form-select" required>
                            <option value="Mulher" <?= ($paciente['genero'] == 'Mulher') ? 'selected' : '' ?>>Mulher</option>
                            <option value="Homem" <?= ($paciente['genero'] == 'Homem') ? 'selected' : '' ?>>Homem</option>
                            <option value="Outro" <?= ($paciente['genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="pronomes">Pronomes</label>
                        <select id="pronomes" name="pronomes" class="form-select">
                            <option value="Ela/dela" <?= ($paciente['pronomes'] == 'Ela/dela') ? 'selected' : '' ?>>Ela/dela</option>
                            <option value="Ele/dele" <?= ($paciente['pronomes'] == 'Ele/dele') ? 'selected' : '' ?>>Ele/dele</option>
                            <option value="Elu/delu" <?= ($paciente['pronomes'] == 'Elu/delu') ? 'selected' : '' ?>>Elu/delu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="estado_civil">Estado Civil</label>
                        <select id="estado_civil" name="estado_civil" class="form-select">
                            <?php
                            $estados = ['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'União Estável'];
                            foreach($estados as $est) {
                                $sel = ($paciente['estado_civil'] == $est) ? 'selected' : '';
                                echo "<option value='$est' $sel>$est</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 mt-4"><h5 class="text-secondary border-bottom pb-2">Alterar Senha (Opcional)</h5></div>
                    <div class="col-md-6">
                        <label class="form-label" for="senha">Nova Senha</label>
                        <div class="password-wrapper">
                            <input type="password" id="senha" name="senha" class="form-control" placeholder="Deixe em branco para não alterar">
                             <i class="bi bi-eye-slash toggle-password-icon" onclick="togglePassword('senha', this)"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="confirmarSenha">Confirmar Nova Senha</label>
                         <div class="password-wrapper">
                            <input type="password" id="confirmarSenha" name="confirmarSenha" class="form-control">
                            <i class="bi bi-eye-slash toggle-password-icon" onclick="togglePassword('confirmarSenha', this)"></i>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Salvar Alterações</button>
                    </div>
                </form>
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
        // Função para ver/ocultar senha
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        }
    </script>
</body>
</html>