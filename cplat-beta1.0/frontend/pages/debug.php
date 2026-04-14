<?php
session_start();
include("conexao.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $msg = "Preencha todos os campos!";
    } else {
        // DEBUG
        error_log("TENTATIVA LOGIN: email=$email");
        
        // Método alternativo: usar get_result() se disponível
        $stmt = $conexao->prepare("SELECT id, senha_hash, nome, email FROM Tabela_usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $login_sucesso = false;
        
        // Tentar método moderno primeiro
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
            
            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                $_SESSION['usuario'] = $usuario['nome'];
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['email'] = $usuario['email'];
                $login_sucesso = true;
                error_log("LOGIN SUCESSO (get_result)");
            } else {
                $msg = "Usuário ou Senha incorretos.";
                error_log("LOGIN FALHOU (get_result)");
            }
        } else {
            // Método tradicional
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($usuario_id, $senha_hash, $nome_bd, $email_bd);
                $stmt->fetch();
                
                if (password_verify($senha, $senha_hash)) {
                    $_SESSION['usuario'] = $nome_bd;
                    $_SESSION['usuario_id'] = $usuario_id;
                    $_SESSION['email'] = $email_bd;
                    $login_sucesso = true;
                    error_log("LOGIN SUCESSO (bind_result)");
                } else {
                    $msg = "Usuário ou Senha incorretos.";
                    error_log("LOGIN FALHOU (bind_result - senha)");
                }
            } else {
                $msg = "Usuário ou Senha incorretos.";
                error_log("LOGIN FALHOU (bind_result - email)");
            }
        }
        
        $stmt->close();
        
        // Se login foi bem sucedido, redirecionar
        if ($login_sucesso) {
            $stmt_update = $conexao->prepare("UPDATE Tabela_usuarios SET ultima_vez_acessado = NOW() WHERE email = ?");
            $stmt_update->bind_param("s", $email);
            $stmt_update->execute();
            $stmt_update->close();

            error_log("REDIRECIONANDO para bemvindos.php");
            header("Location: ../frontend/pages/bemvindos.php");
            exit();
        }
    }
}

$conexao->close();

if ($msg !== "") {
    $_SESSION['msg_login'] = $msg;
    error_log("REDIRECIONANDO com erro: $msg");
    header("Location: ../frontend/pages/bemvindos.php");
    exit();
}
?>