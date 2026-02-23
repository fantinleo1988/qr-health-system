<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - Anamnese QR</title>
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
            color: #0d6efd; /* Azul primário */
            font-weight: 700;
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
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
                <a class="btn btn-outline-primary" href="index.html">
                    <i class="bi bi-arrow-left"></i> Voltar ao Início
                </a>
            </div>
        </div>
    </nav>

    <div class="page-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Política de Privacidade</h1>
            <p class="lead text-secondary">Transparência sobre como protegemos seus dados de saúde.</p>
            <span class="badge bg-primary">Atualizado em: <?= date('d/m/Y') ?></span>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-9 legal-content bg-white p-5 shadow-sm rounded border">
                
                <p class="lead">
                    A sua privacidade é fundamental para o <strong>Sistema Anamnese QR</strong>. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e compartilhamos suas informações pessoais e de saúde, em conformidade com a <strong>Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD)</strong>.
                </p>

                <h3>1. Informações que Coletamos</h3>
                <p>Para fornecer o serviço de identificação médica via QR Code, coletamos os seguintes tipos de dados:</p>
                <ul>
                    <li><strong>Dados Pessoais:</strong> Nome completo, data de nascimento, CPF (para identificação única) e contatos de emergência.</li>
                    <li><strong>Dados Sensíveis (Saúde):</strong> Tipo sanguíneo, alergias, medicamentos em uso contínuo, condições médicas pré-existentes e plano de saúde.</li>
                </ul>

                <h3>2. Finalidade do Tratamento de Dados</h3>
                <p>Os seus dados são utilizados estritamente para:</p>
                <ul>
                    <li>Gerar um QR Code único que permita acesso rápido às suas informações em situações de emergência médica.</li>
                    <li>Permitir que socorristas e médicos visualizem dados vitais para salvar sua vida ou agilizar o atendimento.</li>
                    <li>Manter o cadastro atualizado e seguro em nossa plataforma.</li>
                </ul>

                <h3>3. Como Funciona o QR Code</h3>
                <div class="alert alert-info border-0 shadow-sm p-4">
                    <div class="d-flex">
                        <div class="me-3"><i class="bi bi-info-circle-fill fs-3 text-info"></i></div>
                        <div>
                            <h5 class="alert-heading fw-bold">Atenção ao Compartilhamento</h5>
                            <p class="mb-0">Ao utilizar nosso sistema, você entende que o QR Code gerado serve para facilitar o acesso. Qualquer pessoa que escanear o código terá acesso às informações que você escolheu deixar visíveis na "Ficha de Emergência".</p>
                        </div>
                    </div>
                </div>

                <h3>4. Compartilhamento de Dados</h3>
                <p>O <strong>Anamnese QR</strong> não vende, aluga ou comercializa seus dados pessoais. Seus dados podem ser acessados por:</p>
                <ul>
                    <li>Profissionais de saúde e socorristas (através da leitura do QR Code).</li>
                    <li>Autoridades judiciais ou governamentais, quando exigido por lei.</li>
                </ul>

                <h3>5. Segurança das Informações</h3>
                <p>Adotamos medidas técnicas e administrativas aptas a proteger os dados pessoais de acessos não autorizados e de situações acidentais ou ilícitas de destruição, perda, alteração, comunicação ou difusão. Utilizamos criptografia no banco de dados e conexões seguras (HTTPS).</p>

                <h3>6. Seus Direitos (Titular dos Dados)</h3>
                <p>Conforme o Art. 18 da LGPD, você tem direito a:</p>
                <ul>
                    <li>Acessar seus dados a qualquer momento.</li>
                    <li>Corrigir dados incompletos, inexatos ou desatualizados.</li>
                    <li>Solicitar a exclusão da sua conta e de todos os dados armazenados (salvo obrigações legais de retenção).</li>
                    <li>Revogar o consentimento de uso da plataforma.</li>
                </ul>

                <h3>7. Contato do Encarregado (DPO)</h3>
                <p>Para exercer seus direitos ou tirar dúvidas sobre esta política, entre em contato com nosso Encarregado de Proteção de Dados:</p>
                <div class="p-3 bg-light rounded border d-inline-block">
                    <p class="fw-bold mb-0 text-primary">
                        <i class="bi bi-envelope-fill me-2"></i> lgpd@anamneseqr.com.br
                    </p>
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