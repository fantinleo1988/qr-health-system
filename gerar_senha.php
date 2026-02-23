<?php
// Inicializa as variáveis que usaremos
$hash_gerado = null;
$senha_original = null;

// Verifica se o formulário foi enviado (se o método da requisição é POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega a senha enviada pelo formulário, removendo espaços extras
    $senha_original = trim($_POST['senha_texto'] ?? '');

    // Garante que a senha não está vazia antes de gerar o hash
    if (!empty($senha_original)) {
        $hash_gerado = password_hash($senha_original, PASSWORD_DEFAULT);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Hash de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-box {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: 0.25rem;
            word-wrap: break-word;
            font-family: monospace;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-header">
                <h4 class="mb-0">Gerador de Hash de Senha (password_hash)</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Use esta ferramenta para criar um hash seguro para armazenar senhas no banco de dados.</p>

                <form method="POST" action="gerar_senha.php">
                    <div class="mb-3">
                        <label for="senha_texto" class="form-label">Senha em Texto Puro:</label>
                        <input type="text" class="form-control" id="senha_texto" name="senha_texto" 
                               value="<?= htmlspecialchars($senha_original ?? '') ?>" 
                               placeholder="Digite ou cole a senha aqui" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">Gerar Hash</button>
                </form>

                <?php if ($hash_gerado): ?>
                    <hr>
                    <h5 class="mt-4">Resultado:</h5>
                    <p>Para a senha: <strong><?= htmlspecialchars($senha_original) ?></strong></p>
                    <p>Copie o hash abaixo e cole na coluna `senha` do seu banco de dados:</p>
                    <div class="result-box">
                        <?= htmlspecialchars($hash_gerado) ?>
                    </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <hr>
                    <div class="alert alert-danger mt-4">
                        Por favor, digite uma senha para gerar o hash.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>