<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossas Redes Sociais - Anamnese QR</title>
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
        
        .social-hero {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        
        /* Estilos específicos para cada rede */
        .card-social {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        .card-social:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
        }
        /* Cores das Marcas */
        .bg-facebook { background-color: #1877F2; }
        .bg-instagram { background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d); }
        .bg-linkedin { background-color: #0A66C2; }

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
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-hospital"></i> Anamnese QR
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-primary" href="index.php">
                    <i class="bi bi-arrow-left"></i> Voltar ao Início
                </a>
            </div>
        </div>
    </nav>

    <div class="social-hero text-center">
        <div class="container">
            <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">Em Breve</span>
            <h1 class="display-4 fw-bold mb-3">Estamos preparando algo especial!</h1>
            <p class="lead text-secondary mx-auto" style="max-width: 700px;">
                Nossos canais oficiais de comunicação nas redes sociais estão sendo configurados. 
                Queremos garantir que você receba apenas conteúdo de qualidade e informações relevantes sobre saúde e tecnologia.
            </p>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        <div class="row g-4 justify-content-center">
            
            <div class="col-md-4">
                <div class="card card-social h-100 p-4 text-center shadow-sm">
                    <div class="icon-box bg-facebook">
                        <i class="bi bi-facebook"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Facebook</h3>
                    <p class="text-secondary mb-4">Em breve, nossa página oficial com novidades, dicas de uso e comunidade.</p>
                    <button class="btn btn-light disabled text-muted border">
                        <i class="bi bi-hourglass-split me-2"></i>Aguarde
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-social h-100 p-4 text-center shadow-sm">
                    <div class="icon-box bg-instagram">
                        <i class="bi bi-instagram"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Instagram</h3>
                    <p class="text-secondary mb-4">Fotos dos bastidores, tutoriais rápidos e histórias de quem usa o Anamnese QR.</p>
                    <button class="btn btn-light disabled text-muted border">
                        <i class="bi bi-hourglass-split me-2"></i>Aguarde
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-social h-100 p-4 text-center shadow-sm">
                    <div class="icon-box bg-linkedin">
                        <i class="bi bi-linkedin"></i>
                    </div>
                    <h3 class="fw-bold mb-3">LinkedIn</h3>
                    <p class="text-secondary mb-4">Conexões corporativas, vagas e artigos sobre tecnologia na saúde.</p>
                    <button class="btn btn-light disabled text-muted border">
                        <i class="bi bi-hourglass-split me-2"></i>Aguarde
                    </button>
                </div>
            </div>

        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="text-muted">Enquanto isso, você pode entrar em contato conosco pelos canais tradicionais:</p>
                <a href="mailto:contato@anamneseqr.com.br" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-envelope-fill me-2"></i> Enviar E-mail
                </a>
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