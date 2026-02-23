<?php
// --- CONFIGURAÇÕES E DEPENDÊNCIAS ---
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensagem = '';
$tipo_alerta = 'info';

// --- LÓGICA DE RECUPERAÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        $mensagem = "Por favor, insira um endereço de e-mail válido.";
        $tipo_alerta = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            // Mensagem genérica por segurança
            $mensagem = "Se o e-mail existir em nosso sistema, um link para redefinir sua senha foi enviado.";
            $tipo_alerta = 'success';

            if ($stmt->rowCount() > 0) {
                // Gera token e expiração
                $token = bin2hex(random_bytes(32));
                $expiracao = date("Y-m-d H:i:s", strtotime("+1 hour"));
                
                $update_stmt = $pdo->prepare("UPDATE usuarios SET token_recuperacao = :token, token_expiracao = :expiracao WHERE email = :email");
                $update_stmt->execute([
                    ':token' => $token,
                    ':expiracao' => $expiracao,
                    ':email' => $email
                ]);
                
                // Envia e-mail com PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Configurações do Servidor SMTP
                    $mail->isSMTP();
                    $mail->Host       = 'smtp-relay.brevo.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '96124a001@smtp-brevo.com';
                    $mail->Password   = 'pQySsmVg3bER7Ndk';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    // Remetente e Destinatário
                    $mail->setFrom('fantin.leo.1988@gmail.com', 'Sistema Médico QR');
                    $mail->addAddress($email);

                    // Conteúdo
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperação de Senha - Sistema Médico';
                    
                    $link = "https://anamnese-qr.freeddns.org/redefinir_senha.php?token=" . $token;
                    
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #0d6efd;'>Recuperação de Senha</h2>
                            <p>Olá,</p>
                            <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='" . $link . "' style='background-color: #0d6efd; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Redefinir Minha Senha</a>
                            </p>
                            <p>Se você não solicitou isso, por favor, ignore este e-mail.</p>
                            <p style='color: #6c757d; font-size: 12px;'>Este link é válido por 1 hora.</p>
                            <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                            <p style='color: #6c757d; font-size: 12px;'>Equipe Sistema Médico QR</p>
                        </div>
                    ";
                    $mail->AltBody = "Para redefinir sua senha, copie e cole este link: " . $link;

                    $mail->send();

                } catch (Exception $e) {
                    error_log("Erro ao enviar email: " . $mail->ErrorInfo);
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
            $tipo_alerta = 'danger';
            error_log("Erro no banco: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Sistema Médico</title>
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
            align-items: center; /* Centraliza verticalmente o card */
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
            <a href="login-paciente.php" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-box-arrow-in-right"></i> Voltar ao Login
            </a>
        </div>
    </nav>

    <div class="container main-content py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white text-center py-4 border-bottom-0">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-envelope-check display-4"></i>
                        </div>
                        <h4 class="fw-bold">Recuperar Senha</h4>
                        <p class="text-secondary mb-0">Não se preocupe, vamos ajudá-lo a recuperar o acesso.</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <?php if ($mensagem): ?>
                            <div class="alert alert-<?= $tipo_alerta ?> d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div><?= htmlspecialchars($mensagem) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">E-mail Cadastrado</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                                </div>
                                <div class="form-text">Enviaremos um link seguro para o seu e-mail.</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Enviar Link de Recuperação
                                </button>
                                <a href="login-paciente.php" class="btn btn-light text-secondary">
                                    Cancelar e Voltar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted small">Ainda não tem conta? <a href="cadastro_paciente.php" class="text-decoration-none">Cadastre-se</a></p>
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