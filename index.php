<?php
session_start();
// Calcula o total de itens no carrinho para exibir no cabeçalho
$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $total_itens_carrinho += $item['quantidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nosso Produto - Anamnese QR</title>
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
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-shop"></i> Anamnese QR Store
            </a>
            <div class="navbar-nav ms-auto">
                <a href="carrinho.php" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart3"></i> Carrinho
                    <?php if ($total_itens_carrinho > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $total_itens_carrinho ?>
                            <span class="visually-hidden">itens no carrinho</span>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5 main-content">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="row g-0">
                        <div class="col-md-12">
                            <div class="bg-light text-center p-5">
                                <img src="exemplo-cracha.png" class="img-fluid" alt="Cartão Físico Anamnese QR" style="max-height: 250px;" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=Imagem+do+Cart%C3%A3o';">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body p-4">
                                <span class="badge bg-success mb-2">Destaque</span>
                                <h3 class="card-title fw-bold text-primary">Cartão Físico Anamnese QR</h3>
                                <p class="card-text text-muted mb-4">
                                    Tenha suas informações médicas essenciais sempre à mão. Nosso cartão físico em PVC com QR Code exclusivo oferece acesso rápido e seguro à sua anamnese em qualquer emergência. Resistente à água e durável.
                                </p>
                                
                                <div class="d-flex align-items-end justify-content-between mb-4">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">R$ 49,90</small>
                                        <h2 class="mb-0 fw-bold text-dark">R$ 29,90</h2>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-success"><i class="bi bi-truck"></i> Frete calculado no checkout</small>
                                    </div>
                                </div>
                                
                                <form action="adicionar-ao-carrinho.php" method="post">
                                    <input type="hidden" name="id_produto" value="CARD-001">
                                    <input type="hidden" name="nome_produto" value="Cartão Físico Anamnese QR">
                                    <input type="hidden" name="preco_produto" value="29.90">
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                            <i class="bi bi-cart-plus me-2"></i> Adicionar ao Carrinho
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="mt-3 text-center">
                                    <small class="text-muted"><i class="bi bi-shield-check"></i> Compra 100% Segura</small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>