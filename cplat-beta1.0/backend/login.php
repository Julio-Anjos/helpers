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
        error_log("=== TENTATIVA LOGIN ===");
        
        // 1. ADICIONE 'nivel' NO SEU SELECT
        $stmt = $conn->prepare("SELECT id, senha_hash, nome, email, nivel FROM Tabela_usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $msg = "Usuário ou Senha incorretos.";
        } else {
            // 2. ADICIONE A VARIÁVEL $nivel_bd NO BIND_RESULT
            $stmt->bind_result($usuario_id, $senha_hash, $nome_bd, $email_bd, $nivel_bd);
            $stmt->fetch();

            if (password_verify($senha, $senha_hash)) {
                $_SESSION['usuario'] = $nome_bd;
                $_SESSION['usuario_id'] = $usuario_id;
                $_SESSION['email'] = $email_bd;
                
                // 3. SALVE O NÍVEL NA SESSÃO PARA USAR NO MENU
                $_SESSION['nivel'] = $nivel_bd; 

                // Atualizar último acesso...
                $stmt_update = $conn->prepare("UPDATE Tabela_usuarios SET ultima_vez_acessado = NOW() WHERE email = ?");
                $stmt_update->bind_param("s", $email);
                $stmt_update->execute();
                $stmt_update->close();

                header("Location: ../frontend/pages/bemvindos.php");
                exit();
            } else {
                $msg = "Usuário ou Senha incorretos.";
            }
        }
        $stmt->close();
    }
} else {
    $msg = "Método inválido.";
}

$conn->close();

if ($msg !== "") {
    $_SESSION['msg_login'] = $msg;
    error_log("Redirecionando com erro: $msg");
    header("Location: ../frontend/pages/bemvindos.php");
    exit();
}
?>