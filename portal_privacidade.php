<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal da Privacidade - Anamnese QR</title>
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

        /* Header do Portal */
        .portal-header {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%); /* Verde para segurança/privacidade */
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        
        /* Cards Interativos */
        .card-icon {
            font-size: 2rem;
            color: #198754;
        }
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
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
            <a class="navbar-brand fw-bold text-success" href="index.php">
                <i class="bi bi-shield-lock"></i> Anamnese QR <span class="text-secondary fw-light">| Privacidade</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-success" href="index.php">
                    <i class="bi bi-house"></i> Voltar ao Site
                </a>
            </div>
        </div>
    </nav>

    <div class="portal-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Portal da Privacidade</h1>
            <p class="lead">Central de controle dos seus dados pessoais.</p>
            <p class="small opacity-75">Aqui você exerce seus direitos garantidos pela LGPD.</p>
        </div>
    </div>

    <div class="container mb-5 flex-grow-1">
        
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-file-text card-icon mb-3"></i>
                        <h4 class="card-title">Política de Privacidade</h4>
                        <p class="card-text text-secondary">Entenda detalhadamente como coletamos e usamos seus dados.</p>
                        <a href="politica_privacidade.php" class="btn btn-outline-success stretched-link">Ler Política</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-journal-check card-icon mb-3"></i>
                        <h4 class="card-title">Termos de Uso</h4>
                        <p class="card-text text-secondary">As regras e responsabilidades ao utilizar nossa plataforma.</p>
                        <a href="termos_uso.php" class="btn btn-outline-success stretched-link">Ler Termos</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold text-secondary"><i class="bi bi-fingerprint me-2"></i>Solicitação de Titular</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary mb-4">Utilize este formulário para solicitar o acesso, correção ou exclusão dos seus dados pessoais armazenados em nosso sistema.</p>
                        
                        <form action="" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" required placeholder="Seu nome">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-mail de Cadastro</label>
                                    <input type="email" class="form-control" required placeholder="exemplo@email.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipo de Solicitação</label>
                                    <select class="form-select" required>
                                        <option value="" selected disabled>Selecione uma opção...</option>
                                        <option value="acesso">Quero baixar uma cópia dos meus dados</option>
                                        <option value="correcao">Quero corrigir dados incorretos</option>
                                        <option value="exclusao">Quero excluir minha conta (Direito ao Esquecimento)</option>
                                        <option value="duvida">Tenho uma dúvida sobre privacidade</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mensagem Adicional (Opcional)</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-success px-4" onclick="alert('Esta é uma demonstração. O formulário será ativado em breve.')">Enviar Solicitação</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted">
                Dúvidas específicas? Fale com nosso Encarregado de Proteção de Dados (DPO):<br>
                <strong><a href="mailto:lgpd@anamneseqr.com.br" class="text-success text-decoration-none">lgpd@anamneseqr.com.br</a></strong>
            </p>
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