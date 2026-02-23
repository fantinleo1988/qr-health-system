<?php
session_start();

// Verifica se o array 'paciente' existe e se a chave 'logado' é verdadeira.
if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    header('Location: login-paciente.php');
    exit;
}

// Se a verificação passar, podemos usar as informações do paciente com segurança.
// Ajuste para garantir que pegamos do array correto da sessão
$paciente_id_sessao = $_SESSION['paciente']['id'];

require_once __DIR__ . '/includes/db.php';

// Obter dados do paciente
try {
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $paciente_id_sessao]);
    $paciente = $stmt->fetch();
    
    if (!$paciente) {
        session_destroy();
        header('Location: login-paciente.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do paciente: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Área - <?= htmlspecialchars($paciente['nome_completo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>

    <style>
        /* Layout Flexível para Rodapé Fixo */
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

        /* Configurações de Impressão */
        @media print {
            /* Esconde tudo na página por padrão */
            body * { visibility: hidden; }
            /* Mostra apenas a área de impressão e seus filhos */
            .printable-area, .printable-area * { visibility: visible; }
            /* Garante que a área de impressão ocupe a página toda e centralize */
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                border: none !important;
            }
            /* Remove sombras e cores de fundo para economizar tinta */
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
            /* Esconde explicitamente navbar e footer */
            nav, footer, .btn-secondary { display: none !important; }
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand"><i class="bi bi-person-lines-fill"></i> Área do Paciente</span>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3 text-light opacity-75 d-none d-md-inline">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($paciente['nome_completo']) ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="logout-paciente.php">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content">
        <div class="row">
            
            <div class="col-md-4 col-lg-3">
                
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white fw-bold text-primary">Menu</div>
                    <div class="list-group list-group-flush">
                        <a href="area-paciente.php" class="list-group-item list-group-item-action active border-0">
                            <i class="bi bi-person-lines-fill me-2"></i> Meus Dados
                        </a>
                        <a href="editar-paciente.php?id=<?= $paciente['id'] ?>" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-pencil-square me-2"></i> Editar Dados
                        </a>
                        <a href="alterar-senha-paciente.php" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-shield-lock me-2"></i> Alterar Senha
                        </a>
                    </div>
                </div>

                <div class="card mb-4 printable-area shadow-sm border-0">
                    <div class="card-header bg-white fw-bold text-primary">
                        <i class="bi bi-qr-code me-2"></i> Meu QR Code
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted small">Este é o seu código de emergência. Imprima e mantenha com você.</p>
                        <div id="qrCodeContainer" class="d-flex justify-content-center mb-3">
                            </div>
                        <button class="btn btn-secondary w-100" onclick="imprimirQRCode()">
                            <i class="bi bi-printer me-2"></i> Imprimir QR Code
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-card-heading me-2"></i>Meus Dados Cadastrais</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['flash_message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_message']); ?>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">Dados Pessoais</h6>
                                <p class="mb-1"><strong class="text-dark">Nome:</strong> <span class="text-secondary"><?= htmlspecialchars($paciente['nome_completo']) ?></span></p>
                                <p class="mb-1"><strong class="text-dark">CPF:</strong> <span class="text-secondary"><?= htmlspecialchars($paciente['cpf']) ?></span></p>
                                <p class="mb-1"><strong class="text-dark">Data Nasc.:</strong> <span class="text-secondary"><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></span></p>
                                <p class="mb-1"><strong class="text-dark">Gênero:</strong> <span class="text-secondary"><?= htmlspecialchars($paciente['genero']) ?></span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">Contato</h6>
                                <p class="mb-1"><strong class="text-dark">Telefone:</strong> <span class="text-secondary"><?= htmlspecialchars($paciente['telefone']) ?></span></p>
                                <p class="mb-1"><strong class="text-dark">E-mail:</strong> <span class="text-secondary"><?= htmlspecialchars($paciente['email']) ?></span></p>
                                <div class="alert alert-warning p-2 mt-3 mb-0 small">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> <strong>Em caso de emergência avisar:</strong><br>
                                    <?= htmlspecialchars($paciente['contato_emergencia_nome']) ?> <br>
                                    <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($paciente['telefone_emergencia']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">Endereço</h6>
                                <p class="mb-1"><?= htmlspecialchars($paciente['logradouro']) ?>, <?= htmlspecialchars($paciente['numero']) ?></p>
                                <?php if(!empty($paciente['complemento'])): ?>
                                    <p class="mb-1">Complemento: <?= htmlspecialchars($paciente['complemento']) ?></p>
                                <?php endif; ?>
                                <p class="mb-1">Bairro: <?= htmlspecialchars($paciente['bairro']) ?></p>
                                <p class="mb-1">CEP: <?= htmlspecialchars($paciente['cep']) ?></p>
                                <p class="mb-1"><?= htmlspecialchars($paciente['localidade']) ?> - <?= htmlspecialchars($paciente['uf']) ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3 border-bottom pb-2">Informações Médicas (Públicas)</h6>
                                <p class="mb-1"><strong class="text-danger"><i class="bi bi-droplet-fill"></i> Tipo Sanguíneo:</strong> <?= htmlspecialchars($paciente['tipo_sanguineo']) ?></p>
                                
                                <div class="mt-3">
                                    <strong class="text-dark">Alergias:</strong><br>
                                    <span class="text-secondary"><?= nl2br(htmlspecialchars($paciente['alergias'] ?: 'Nenhuma alergia cadastrada.')) ?></span>
                                </div>
                                
                                <div class="mt-2">
                                    <strong class="text-dark">Histórico Familiar:</strong><br>
                                    <span class="text-secondary"><?= nl2br(htmlspecialchars($paciente['historico_familiar'] ?: 'Nenhum histórico informado.')) ?></span>
                                </div>
                            </div>
                        </div>
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const container = document.getElementById("qrCodeContainer");
            const canvas = document.createElement("canvas");
            
            // Constrói a URL que o QR Code irá conter
            // Aponta para a ficha pública
            const qrUrl = `<?= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" ?>/ficha.php?id=<?= json_encode($paciente['id']) ?>`;

            // Gera o QR Code no elemento canvas
            QRCode.toCanvas(canvas, qrUrl, { width: 180, margin: 2 }, function (error) {
                if (error) {
                    console.error(error);
                    container.innerHTML = "<p class='text-danger'>Erro ao gerar QR Code.</p>";
                } else {
                    container.appendChild(canvas);
                }
            });
        });

        function imprimirQRCode() {
            // A mágica acontece no @media print definido no CSS
            window.print();
        }
    </script>
</body>
</html>