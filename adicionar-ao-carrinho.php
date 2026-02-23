<?php
session_start();

// Verifica se os dados do produto foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto'])) {
    
    // Inicializa o carrinho se ele não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    $id_produto = $_POST['id_produto'];
    
    // Verifica se o produto já está no carrinho para incrementar a quantidade
    if (isset($_SESSION['carrinho'][$id_produto])) {
        $_SESSION['carrinho'][$id_produto]['quantidade']++;
    } else {
        // Adiciona o novo produto ao carrinho
        $_SESSION['carrinho'][$id_produto] = [
            'id' => $id_produto,
            'nome' => $_POST['nome_produto'],
            'preco' => (float)$_POST['preco_produto'],
            'quantidade' => 1
        ];
    }

    // Redireciona para a página do carrinho
    header('Location: carrinho.php');
    exit;

} else {
    // Se alguém tentar acessar o arquivo diretamente, redireciona para a home
    header('Location: index.php');
    exit;
}
?>