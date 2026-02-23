<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar um Erro - Anamnese QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Layout Flexível para Rodapé Fixo */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .flex-grow-1 {
            flex: 1;
        }

        /* Header Específico de Erro */
        .report-header {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%); /* Vermelho para alerta */
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        
        /* Inputs com foco vermelho */
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
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
            <a class="navbar-brand fw-bold text-danger" href="index.html">
                <i class="bi bi-bug-fill"></i> Anamnese QR <span class="text-secondary fw-light">| Bugs</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-danger" href="index.html">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
        </div>
    </nav>

    <div class="report-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold"><i class="bi bi-exclamation-triangle me-3"></i>Encontrou um problema?</h1>
            <p class="lead opacity-75">Ajude-nos a melhorar o sistema reportando falhas técnicas.</p>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        
                        <div class="alert alert-warning mb-4 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Dica:</strong> Se tiver dúvidas de como usar o sistema, visite nossa <a href="ajuda.php" class="alert-link">Central de Ajuda</a> antes de reportar um erro.
                            </div>
                        </div>

                        <form onsubmit="enviarReport(event)" enctype="multipart/form-data">
                            <div class="row g-3">
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Seu Nome</label>
                                    <input type="text" class="form-control" required placeholder="Ex: João Silva">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Seu E-mail</label>
                                    <input type="email" class="form-control" required placeholder="Para darmos retorno">
                                </div>

                                <div class="col-12"><hr class="text-secondary opacity-25"></div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Onde ocorreu o erro?</label>
                                    <select class="form-select" required>
                                        <option value="" selected disabled>Selecione...</option>
                                        <option value="login">Tela de Login / Cadastro</option>
                                        <option value="qrcode">Leitura do QR Code</option>
                                        <option value="ficha">Edição da Ficha Médica</option>
                                        <option value="visual">Erro Visual / Layout</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Dispositivo usado</label>
                                    <input type="text" class="form-control" placeholder="Ex: iPhone, PC Windows, Android...">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Descrição do Problema</label>
                                    <textarea class="form-control" rows="4" required placeholder="Descreva o que aconteceu..."></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Anexar Evidência (Print/Screenshot)</label>
                                    <input class="form-control" type="file" id="arquivoEvidencia" accept="image/png, image/jpeg, image/jpg, .pdf">
                                    <div class="form-text">
                                        <i class="bi bi-paperclip"></i> Formatos aceitos: JPG, PNG ou PDF. Tamanho máximo: 5MB.
                                    </div>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-danger btn-lg px-5">
                                        <i class="bi bi-send-fill me-2"></i> Enviar Report
                                    </button>
                                </div>
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
        function enviarReport(e) {
            e.preventDefault(); 
            
            // Validação de Tamanho do Arquivo
            const fileInput = document.getElementById('arquivoEvidencia');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    alert('O arquivo selecionado é muito grande! O limite máximo é 5MB.');
                    return;
                }
            }

            // Simulação de envio
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            btn.disabled = true;

            setTimeout(() => {
                alert("Obrigado! Seu relatório (com anexo) foi enviado com sucesso. Nossa equipe técnica irá analisar.");
                e.target.reset();
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        }
    </script>
</body>
</html>