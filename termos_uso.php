<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso - Anamnese QR</title>
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
        
        /* Cabeçalho da Página */
        .page-header {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
            padding: 4rem 0;
            margin-bottom: 3rem;
        }

        /* Estilo para facilitar leitura de texto longo */
        .legal-content p, .legal-content li {
            text-align: justify;
            margin-bottom: 1rem;
            line-height: 1.7;
            color: #4a5568;
        }
        .legal-content h3 {
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
            color: #0d6efd;
            font-weight: 700;
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
        }
        
        .alert-warning-custom {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            color: #856404;
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

    <div class="page-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Termos de Uso</h1>
            <p class="lead text-secondary">Regras e responsabilidades para utilização da plataforma.</p>
            <span class="badge bg-secondary">Versão 1.0 - <?= date('Y') ?></span>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-9 legal-content bg-white p-5 shadow-sm rounded border">
                
                <p class="lead">
                    Bem-vindo ao <strong>Sistema Anamnese QR</strong>. Ao acessar ou utilizar nossa plataforma, você concorda com estes Termos de Uso. Se você não concordar com qualquer parte destes termos, não deverá utilizar nossos serviços.
                </p>

                <h3>1. Descrição do Serviço</h3>
                <p>O Anamnese QR é uma plataforma tecnológica que permite aos usuários armazenar informações de saúde e gerar um QR Code ("Código de Resposta Rápida") para facilitar o acesso a esses dados em situações de emergência ou consultas médicas.</p>

                <h3>2. Responsabilidade pelas Informações</h3>
                <div class="alert alert-warning-custom p-4 rounded mb-4">
                    <div class="d-flex">
                        <div class="me-3"><i class="bi bi-exclamation-triangle-fill fs-3"></i></div>
                        <div>
                            <strong>Importante:</strong> Você é o único responsável pela veracidade, precisão e atualização dos dados inseridos.
                        </div>
                    </div>
                </div>
                <p>O sistema não verifica clinicamente as informações inseridas. Inserir dados incorretos (como tipo sanguíneo ou alergias) pode resultar em tratamento médico inadequado. O Anamnese QR não se responsabiliza por quaisquer danos causados por informações imprecisas fornecidas pelo usuário.</p>

                <h3>3. Isenção de Responsabilidade Médica</h3>
                <p>A nossa plataforma é uma ferramenta de armazenamento de informações e <strong>não substitui aconselhamento, diagnóstico ou tratamento médico profissional</strong>.</p>
                <ul>
                    <li>Não garantimos que o QR Code será lido com sucesso por todos os dispositivos ou em todas as condições de iluminação.</li>
                    <li>Não garantimos que socorristas ou médicos consultarão o QR Code em todas as situações de emergência.</li>
                </ul>

                <h3>4. Segurança da Conta e do QR Code</h3>
                <p>Você é responsável por manter a confidencialidade de sua senha. Além disso, ao gerar e imprimir seu QR Code (em adesivos, carteiras, etc.), você entende que qualquer pessoa com um leitor de QR Code poderá visualizar os dados que você configurou como "Públicos" ou "Emergência".</p>

                <h3>5. Disponibilidade do Serviço</h3>
                <p>Nós nos esforçamos para manter o serviço disponível 24 horas por dia, mas não garantimos que a plataforma estará livre de interrupções, falhas técnicas ou indisponibilidade temporária para manutenção.</p>

                <h3>6. Alterações nos Termos</h3>
                <p>Podemos atualizar estes Termos de Uso periodicamente. O uso contínuo da plataforma após as alterações constitui aceitação dos novos termos.</p>

                <h3>7. Legislação Aplicável</h3>
                <p>Estes termos são regidos pelas leis da República Federativa do Brasil. Fica eleito o foro da comarca de São Paulo/SP para dirimir quaisquer dúvidas oriundas deste contrato.</p>

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