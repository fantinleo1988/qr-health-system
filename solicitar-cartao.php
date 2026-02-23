<?php
// DEBUG (Podes remover em produção)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

// A verificação de login continua importante para sabermos quem está comprando
if (!isset($_SESSION['paciente']['logado']) || $_SESSION['paciente']['logado'] !== true) {
    header('Location: login-paciente.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nosso Cartão Físico - Anamnese QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <a class="btn btn-outline-primary btn-sm" href="area-paciente.php">
                    <i class="bi bi-arrow-left"></i> Minha Área
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5 main-content">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-credit-card-2-front me-2"></i> Nosso Produto</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center mb-4 mb-md-0">
                                <img src="exemplo-cracha.png" class="img-fluid rounded shadow-sm" alt="Exemplo do Cartão Anamnese QR" style="max-height: 300px;" onerror="this.src='https://via.placeholder.com/350x250?text=Cart%C3%A3o+F%C3%ADsico'">
                            </div>
                            <div class="col-md-6">
                                <h3 class="fw-bold text-dark mb-3">Cartão Físico Anamnese QR</h3>
                                <p class="text-muted mb-4">
                                    Tenha suas informações médicas essenciais sempre à mão. Nosso cartão físico em PVC com QR Code exclusivo oferece acesso rápido e seguro à sua anamnese em qualquer emergência.
                                </p>
                                
                                <div class="mb-4 p-3 bg-light rounded">
                                    <h4 class="mb-0">Valor: <strong class="text-success">R$ 29,90</strong></h4>
                                    <small class="text-muted">Frete calculado no checkout</small>
                                </div>
                                
                                <p class="small text-secondary mb-4">
                                    <i class="bi bi-clock-history me-1"></i> O cartão será produzido e enviado no prazo de <strong>10 dias úteis</strong> após a confirmação do pagamento.
                                </p>
                                
                                <form action="adicionar-ao-carrinho.php" method="post">
                                    <input type="hidden" name="id_produto" value="CARD-001">
                                    <input type="hidden" name="nome_produto" value="Cartão Físico Anamnese QR">
                                    <input type="hidden" name="preco_produto" value="29.90">
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                            <i class="bi bi-cart-plus me-2"></i> Adicionar ao Carrinho
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                     <a href="area-paciente.php" class="text-decoration-none text-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para minha área
                     </a>
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