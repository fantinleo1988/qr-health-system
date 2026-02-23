<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perguntas Frequentes (FAQ) - Anamnese QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Tipografia e Fundo */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #495057;
        }

        /* Hero Section Moderna */
        .faq-hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            color: white;
            padding: 4rem 0 6rem; /* Padding extra embaixo para o search bar entrar */
            margin-bottom: 2rem;
            position: relative;
        }

        /* Barra de Pesquisa Flutuante */
        .search-container {
            margin-top: -3rem; /* Sobe para ficar "dentro" do hero */
            margin-bottom: 3rem;
        }
        .search-box {
            background: white;
            padding: 0.5rem;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        .search-box input {
            border: none;
            box-shadow: none;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
        }
        .search-box input:focus {
            outline: none;
        }
        .search-icon {
            font-size: 1.2rem;
            color: #0d6efd;
            padding: 0 1rem;
        }

        /* Estilo Moderno do Accordion */
        .accordion-item {
            border: none;
            border-radius: 12px !important;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .accordion-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        
        .accordion-button {
            font-weight: 600;
            color: #2c3e50;
            background-color: #fff;
            padding: 1.2rem 1.5rem;
            border: none;
            box-shadow: none !important; /* Remove linha azul do foco padrão */
        }
        
        .accordion-button:not(.collapsed) {
            color: #0d6efd;
            background-color: #f0f7ff;
        }
        
        .accordion-button::after {
            background-size: 1rem;
            transition: transform 0.3s ease;
        }

        .accordion-body {
            padding: 1.5rem;
            line-height: 1.6;
            color: #6c757d;
        }

        /* Categoria Títulos */
        .category-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #adb5bd;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
            border-left: 3px solid #0d6efd;
        }

        /* Estilos do Rodapé (Padrão) */
        footer a { text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #ffffff !important; text-decoration: underline; }
        
        /* Botão Admin no Footer */
        .admin-btn-hover:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        
        /* Sticky Footer Fix */
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .main-container { flex: 1; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.html">
                <i class="bi bi-hospital"></i> Anamnese QR
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-secondary btn-sm" href="ajuda.php">
                    <i class="bi bi-life-preserver me-1"></i> Central de Ajuda
                </a>
            </div>
        </div>
    </nav>

    <div class="faq-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Perguntas Frequentes</h1>
            <p class="opacity-75">Tire suas dúvidas sobre o funcionamento do sistema de forma rápida.</p>
        </div>
    </div>

    <div class="container main-container">
        
        <div class="row justify-content-center search-container">
            <div class="col-lg-8">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="O que você procura? (ex: senha, qr code, internet)">
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">

                <div class="category-title">Funcionamento do Sistema</div>
                <div class="accordion" id="accordionSystem">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="bi bi-question-circle me-2"></i> O que é o Anamnese QR?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionSystem">
                            <div class="accordion-body">
                                É uma plataforma inovadora que armazena sua ficha médica de emergência na nuvem e gera um <strong>QR Code exclusivo</strong>. Em caso de acidente ou mal súbito, socorristas podem escanear esse código para ter acesso imediato ao seu tipo sanguíneo, alergias e contatos de emergência.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="bi bi-wifi-off me-2"></i> Funciona sem internet?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionSystem">
                            <div class="accordion-body">
                                <ul>
                                    <li><strong>Para quem lê (socorrista):</strong> É necessária conexão com a internet (3G/4G/Wi-Fi) para acessar seus dados atualizados no servidor.</li>
                                    <li><strong>Para você (paciente):</strong> O QR Code impresso funciona "offline", pois é apenas uma imagem física.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-title">Minha Conta e Dados</div>
                <div class="accordion" id="accordionAccount">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                <i class="bi bi-pencil-square me-2"></i> Como altero meus dados médicos?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionAccount">
                            <div class="accordion-body">
                                É simples! Basta fazer login na sua conta, clicar em <strong>"Meus Dados"</strong> ou <strong>"Editar"</strong>. Qualquer alteração que você salvar será atualizada automaticamente no seu QR Code existente. Você <strong>não</strong> precisa imprimir um novo código a cada alteração.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                <i class="bi bi-key me-2"></i> Esqueci minha senha. E agora?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionAccount">
                            <div class="accordion-body">
                                Na tela de login, clique no link <strong>"Esqueci minha senha"</strong>. Digite seu e-mail cadastrado e enviaremos um link de recuperação seguro para você criar uma nova senha.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-title">Privacidade e Segurança</div>
                <div class="accordion" id="accordionPrivacy">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                <i class="bi bi-shield-lock me-2"></i> Meus dados estão seguros?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionPrivacy">
                            <div class="accordion-body">
                                Sim. Utilizamos criptografia de ponta para armazenar seus dados sensíveis. Além disso, a ficha acessível pelo QR Code exibe apenas informações vitais para emergência (Ficha Pública), mantendo dados como seu endereço completo e histórico detalhado protegidos na área logada.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5 p-4 bg-white rounded-3 shadow-sm border">
                    <h5 class="fw-bold mb-2">Não encontrou o que procurava?</h5>
                    <p class="text-secondary mb-3">Nossa equipe de suporte está pronta para ajudar.</p>
                    <a href="reportar_erro.php" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-envelope-paper me-2"></i> Entrar em Contato
                    </a>
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
    
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.accordion-item');
            
            items.forEach(function(item) {
                // Procura no botão (pergunta) e no corpo (resposta)
                let btnText = item.querySelector('.accordion-button').innerText.toLowerCase();
                let bodyText = item.querySelector('.accordion-body').innerText.toLowerCase();
                
                if (btnText.includes(filter) || bodyText.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>