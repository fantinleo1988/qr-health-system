<?php
// A SESSÃO DEVE SER INICIADA ANTES DE QUALQUER OUTRA COISA!
session_start();

require_once __DIR__ . "/includes/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (empty($_POST['email']) || empty($_POST['senha'])) {
    $_SESSION['login_error'] = "Por favor, preencha o e-mail e a senha.";
    header('Location: login.php');
    exit;
}

$email = $_POST['email'];
$senha_formulario = $_POST['senha'];

try {
    $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha_formulario, $usuario['senha'])) {
        // Sucesso no login
        session_regenerate_id(true);
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];

        // Limpa qualquer erro de login anterior da sessão
        unset($_SESSION['login_error']);

        header('Location: dashboard.php');
        exit;
    } else {
        // Falha no login
        $_SESSION['login_error'] = "E-mail ou senha inválidos.";
        header('Location: login.php');
        exit;
    }
// CÓDIGO PARA ERRO
} catch (Exception $e) {
    // Em caso de erro com o banco de dados.
    $_SESSION['login_error'] = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
    // Em um ambiente de produção, seria bom registrar o erro $e->getMessage() em um log.
    header('Location: login.php');
    exit;
}
?>