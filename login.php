<?php
// 1. INICIA A SESSÃO
session_start();

require_once __DIR__ . '/includes/db.php';

// 2. VERIFICA SE O USUÁRIO JÁ ESTÁ LOGADO
if (isset($_SESSION['usuario_logado'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* Layout e Fundo */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center; /* Centraliza verticalmente */
            justify-content: center;
            padding: 2rem 0;
        }

        /* Card de Login */
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            border-radius: 15px;
            background-color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Input de Senha */
        .password-wrapper {
            position: relative;
        }
        .toggle-password-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
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

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.html">
                <i class="bi bi-hospital"></i> Anamnese QR
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-primary btn-sm" href="index.html">
                    <i class="bi bi-house"></i> Início
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <div class="login-container">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h3 class="fw-bold">Acesso Administrativo</h3>
                <p class="text-muted small">Área restrita a profissionais autorizados</p>
            </div>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= htmlspecialchars($_SESSION['login_error']) ?></div>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" required placeholder="admin@email.com">
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="senha" class="form-label">Senha</label>
                        <a href="esqueci_senha.php" class="small text-decoration-none">Esqueceu a senha?</a>
                    </div>
                    <div class="password-wrapper">
                        <input type="password" class="form-control form-control-lg" id="senha" name="senha" required placeholder="Sua senha">
                        <i class="bi bi-eye-slash toggle-password-icon" id="toggleSenha" onclick="togglePassword('senha', this)"></i>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">Entrar</button>
                </div>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <p class="text-muted small mb-2">Você é um paciente?</p>
                <a href="login-paciente.php" class="btn btn-outline-secondary btn-sm w-100">Acessar Área do Paciente</a>
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
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>
</body>
</html>