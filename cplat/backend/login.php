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
        $stmt = $conexao->prepare("SELECT senha_hash, nome FROM Tabela_usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $msg = "Usuário ou Senha incorretos.";
        } else {
            $stmt->bind_result($senha_hash, $nome_bd);
            $stmt->fetch();

            if (password_verify($senha, $senha_hash)) {
                $_SESSION['usuario'] = $nome_bd;

                    $stmt_update = $conexao->prepare("UPDATE Tabela_usuarios SET ultima_vez_acessado = NOW() WHERE email = ?");
                    $stmt_update->bind_param("s", $email);
                    $stmt_update->execute();
                    $stmt_update->close();

                header("Location: ../frontend/pages/bemvindos.php");
                exit();
            } else {
                $msg = "Senha incorreta.";
            }
        }
        $stmt->close();
    }
} else {
    $msg = "Método inválido.";
}

$conexao->close();

if ($msg !== "") {
    $_SESSION['msg_login'] = $msg;
    header("Location: ../frontend/index.php");
    exit();
}
