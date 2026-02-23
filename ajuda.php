<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Ajuda - Anamnese QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .help-hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        .search-box {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        .search-box input {
            padding-left: 3rem;
            border-radius: 50px;
            height: 55px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .search-box i {
            position: absolute;
            left: 20px;
            top: 18px;
            color: #6c757d;
            font-size: 1.2rem;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
        /* Estilos do rodapé */
        footer a { text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #ffffff !important; text-decoration: underline; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.html">
                <i class="bi bi-hospital"></i> Anamnese QR
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-primary" href="index.html">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </nav>

    <div class="help-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Como podemos ajudar?</h1>
            <p class="lead mb-4 text-white-50">Encontre respostas rápidas para suas dúvidas sobre o sistema.</p>
            
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Digite sua dúvida (ex: como imprimir QR code)...">
            </div>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <h4 class="mb-3 text-primary"><i class="bi bi-rocket-takeoff me-2"></i>Primeiros Passos</h4>
                <div class="accordion shadow-sm mb-5" id="accordionBasico">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Como crio minha conta?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionBasico">
                            <div class="accordion-body">
                                Para criar sua conta, clique no botão <strong>"Quero me Cadastrar"</strong> na página inicial. Você precisará fornecer seu nome, CPF, e-mail e criar uma senha segura. Após isso, você poderá preencher sua ficha de saúde.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                É gratuito utilizar o sistema?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionBasico">
                            <div class="accordion-body">
                                Sim! O plano básico para pacientes, que inclui a ficha de emergência e a geração do QR Code, é totalmente gratuito.
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3 text-primary"><i class="bi bi-qr-code me-2"></i>QR Code e Emergências</h4>
                <div class="accordion shadow-sm mb-5" id="accordionQR">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Onde devo colocar meu QR Code?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                            <div class="accordion-body">
                                Recomendamos imprimir seu QR Code e colocá-lo em locais visíveis e acessíveis, como:
                                <ul>
                                    <li>Na carteira de identidade ou motorista.</li>
                                    <li>Como adesivo no capacete (para motociclistas).</li>
                                    <li>Na capinha do celular.</li>
                                    <li>Em um crachá ou pulseira de identificação.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Preciso de internet para ler o código?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionQR">
                            <div class="accordion-body">
                                Quem for ler o código (socorrista) precisa de internet no celular para acessar seus dados atualizados em nosso servidor. O código em si é apenas um link para sua ficha online.
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i>Privacidade e Segurança</h4>
                <div class="accordion shadow-sm mb-5" id="accordionSeguranca">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Qualquer pessoa pode ver meus dados?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#accordionSeguranca">
                            <div class="accordion-body">
                                Sim e Não. Qualquer pessoa que escanear seu QR Code verá a <strong>Ficha de Emergência</strong> (dados que você marcou como públicos para salvar sua vida). Dados sensíveis privados (como histórico detalhado ou endereço completo) podem ser ocultados nas configurações de privacidade.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-white p-4 text-center shadow-sm">
                    <h5 class="fw-bold">Ainda precisa de ajuda?</h5>
                    <p class="text-secondary">Nossa equipe está pronta para responder.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="mailto:suporte@anamneseqr.com.br" class="btn btn-outline-primary">
                            <i class="bi bi-envelope me-2"></i> Enviar E-mail
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=554891359339&text=&source=&data=&app_absent=" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i> WhatsApp
                        </a>
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
                        <li class="mb-2"><a href="politica_privacidade.php" class="text-secondary">Política de Privacidade</a>
                        <li class="mb-2"><a href="termos_uso.php" class="text-secondary">Termos de Uso</a></li>
                        <li class="mb-2"><a href="portal_privacidade.php" class="text-secondary">Portal da Privacidade</a></li>
                    </ul>
                    <div class="mt-3">
                        <span class="badge bg-secondary"><i class="bi bi-shield-lock"></i> Dados Protegidos</span>
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
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.accordion-item');

            items.forEach(function(item) {
                let text = item.innerText.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>