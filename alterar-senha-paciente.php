<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    header('Location: login-paciente.php');
    exit;
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $erro = "As novas senhas não coincidem.";
    } elseif (strlen($nova_senha) < 8) {
        $erro = "A senha deve ter pelo menos 8 caracteres.";
    } else {
        try {
            // Verificar senha atual
            // Nota: Ajustei $_SESSION['paciente_id'] para $_SESSION['paciente']['id'] baseado no padrão do seu arquivo area-paciente.php
            $stmt = $pdo->prepare("SELECT senha FROM pacientes WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $_SESSION['paciente']['id']]);
            $paciente = $stmt->fetch();
            
            if ($paciente && password_verify($senha_atual, $paciente['senha'])) {
                // Atualizar senha
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE pacientes SET senha = :senha WHERE id = :id");
                $stmt->execute([
                    ':senha' => $nova_senha_hash,
                    ':id' => $_SESSION['paciente']['id']
                ]);
                $sucesso = true;
            } else {
                $erro = "Senha atual incorreta.";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar senha: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - Anamnese QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sticky Footer e Layout */
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
            <span class="navbar-brand"><i class="bi bi-shield-lock"></i> Área do Paciente</span>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-light" href="logout-paciente.php">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 main-content">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-key-fill me-2"></i>Alterar Senha</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($sucesso): ?>
                            <div class="alert alert-success text-center">
                                <i class="bi bi-check-circle-fill fs-4 d-block mb-2"></i>
                                Senha alterada com sucesso!
                            </div>
                            <div class="d-grid">
                                <a href="area-paciente.php" class="btn btn-primary">Voltar para Minha Área</a>
                            </div>
                        <?php else: ?>
                            <?php if ($erro): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div><?= htmlspecialchars($erro) ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="senha_atual" class="form-label">Senha Atual</label>
                                    <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nova_senha" class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="8">
                                    <div class="form-text">Mínimo de 8 caracteres.</div>
                                </div>
                                <div class="mb-4">
                                    <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required minlength="8">
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="area-paciente.php" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                                    <button type="submit" class="btn btn-primary px-4">Salvar Nova Senha</button>
                                </div>
                            </form>
                        <?php endif; ?>
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
</body>
</html>