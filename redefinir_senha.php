<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$token = $_GET['token'] ?? '';
$erro = '';
$mensagem_sucesso = '';
$mostrar_formulario = false;

// 1. VERIFICAR SE O TOKEN É VÁLIDO (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($token)) {
        $erro = "Token não fornecido. O link pode estar quebrado.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, token_expiracao FROM usuarios WHERE token_recuperacao = :token");
            $stmt->execute([':token' => $token]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                if (new DateTime() > new DateTime($usuario['token_expiracao'])) {
                    $erro = "Este link de recuperação expirou. Por favor, solicite um novo.";
                } else {
                    $mostrar_formulario = true;
                }
            } else {
                $erro = "Token inválido. Solicite um novo link de recuperação.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no sistema. Tente novamente.";
            error_log("Erro ao validar token: " . $e->getMessage());
        }
    }
}

// 2. PROCESSAR NOVA SENHA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token_post = $_POST['token'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $mostrar_formulario = true; 

    if (empty($nova_senha) || empty($confirmar_senha)) {
        $erro = "Por favor, preencha os dois campos de senha.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($nova_senha) < 8) {
        $erro = "A senha deve ter no mínimo 8 caracteres.";
    } else {
        try {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "UPDATE usuarios SET senha = :senha, token_recuperacao = NULL, token_expiracao = NULL 
                 WHERE token_recuperacao = :token"
            );
            $stmt->execute([
                ':senha' => $senha_hash,
                ':token' => $token_post
            ]);

            $_SESSION['login_success'] = "Senha redefinida com sucesso! Faça login.";
            header('Location: login-paciente.php'); // Redireciona para login de paciente ou admin conforme sua lógica
            exit;

        } catch (PDOException $e) {
            $erro = "Erro ao atualizar a senha. Tente novamente.";
            error_log("Erro ao redefinir senha: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Sistema Médico</title>
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
            display: flex;
            align-items: center;
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
    
    <nav class="navbar navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-hospital"></i> Anamnese QR
            </a>
        </div>
    </nav>

    <div class="container main-content py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white text-center py-4 border-bottom-0">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-key display-4"></i>
                        </div>
                        <h4 class="fw-bold">Criar Nova Senha</h4>
                    </div>
                    <div class="card-body px-4 pb-4">

                        <?php if ($erro): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?= htmlspecialchars($erro) ?></div>
                            </div>
                            <?php if (!$mostrar_formulario): ?>
                                <div class="d-grid mt-3">
                                    <a href="esqueci_senha.php" class="btn btn-primary">Solicitar Novo Link</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($mostrar_formulario): ?>
                            <form method="POST" action="redefinir_senha.php?token=<?= htmlspecialchars($token) ?>">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                
                                <div class="mb-3">
                                    <label for="nova_senha" class="form-label">Nova Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="8" placeholder="Mínimo 8 caracteres">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="confirmar_senha" class="form-label">Confirme a Nova Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required placeholder="Repita a senha">
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Salvar Nova Senha</button>
                                </div>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-light pt-5 pb-3 mt-auto">
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
</body>
</html>