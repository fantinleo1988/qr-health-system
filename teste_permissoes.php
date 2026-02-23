<?php
// Força a exibição de todos os erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Teste de Permissões e Execução do PHP</h1>";

$caminho_do_arquivo = __DIR__ . '/log_teste.txt';
$conteudo = "O PHP está funcionando e consegue escrever aqui. Data: " . date('Y-m-d H:i:s');

echo "<p>Tentando escrever no arquivo: " . htmlspecialchars($caminho_do_arquivo) . "</p>";

// Tenta escrever no arquivo
if (file_put_contents($caminho_do_arquivo, $conteudo)) {
    echo "<p style='color:green; font-weight:bold;'>✅ SUCESSO! O arquivo 'log_teste.txt' foi criado ou atualizado.</p>";
    echo "<p><b>Diagnóstico:</b> O problema é um erro de sintaxe no arquivo 'login.php'.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ FALHA! Não foi possível escrever o arquivo.</p>";
    echo "<p><b>Diagnóstico:</b> O problema é de permissões na pasta. O servidor web não tem permissão para escrever em '/var/www/anamneseqrcode/'.</p>";
}
?>