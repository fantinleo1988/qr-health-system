<?php
session_start();

// Pega os itens do carrinho da sessão
$carrinho_itens = $_SESSION['carrinho'] ?? [];
$total_carrinho = 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho - Anamnese QR</title>
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
                <i class="bi bi-cart3"></i> Anamnese QR Store
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Continuar Comprando
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5 main-content">
        <h2 class="mb-4"><i class="bi bi-bag-check"></i> Meu Carrinho de Compras</h2>

        <?php if (empty($carrinho_itens)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-cart-x display-4 d-block mb-3"></i>
                <h4>Seu carrinho está vazio.</h4>
                <p class="mb-4">Adicione produtos para garantir sua segurança.</p>
                <a href="index.php" class="btn btn-primary">Ir para a Loja</a>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unit.</th>
                                    <th class="text-center">Qtd.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrinho_itens as $item): ?>
                                    <?php
                                        $subtotal = $item['preco'] * $item['quantidade'];
                                        $total_carrinho += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?= htmlspecialchars($item['nome']) ?></span>
                                        </td>
                                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                        <td class="text-center"><?= $item['quantidade'] ?></td>
                                        <td class="text-end fw-bold">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Taxas e frete serão calculados na etapa de pagamento.</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4 class="mb-3">Total: <span class="text-success fw-bold">R$ <?= number_format($total_carrinho, 2, ',', '.') ?></span></h4>
                            
                            <form id="form-checkout">
                                <button type="submit" id="checkout-btn" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-shield-check"></i> Finalizar Compra e Pagar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
    document.getElementById('form-checkout').addEventListener('submit', async function(event) {
        event.preventDefault(); // Impede o envio direto do formulário

        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gerando link de pagamento...';

        try {
            // Chama o script que cria a preferência no back-end
            const response = await fetch('criar-preferencia.php', {
                method: 'POST' 
            });

            if (!response.ok) {
                throw new Error('Falha ao criar a preferência de pagamento.');
            }

            const data = await response.json();

            // Se receber a URL de pagamento (init_point), redireciona o usuário
            if (data.init_point) {
                window.location.href = data.init_point;
            } else {
                throw new Error('URL de checkout não foi recebida do servidor.');
            }

        } catch (error) {
            console.error(error);
            alert('Não foi possível iniciar o pagamento. Por favor, tente novamente.');
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i class="bi bi-shield-check"></i> Finalizar Compra e Pagar';
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>