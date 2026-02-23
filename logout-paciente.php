<?php
session_start();

// Destruir todas as vari���veis de sess���o
$_SESSION = array();

// Apagar o cookie de sess���o
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir a sess���o
session_destroy();

// Redirecionar para a página inicial
header("Location: index.html");
exit;