<?php
session_start();
include("conexao.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $nome = trim($_POST['nome'] ?? '');

    if (empty($email) || empty($senha) || empty($nome)) {
        $msg = "Preencha todos os campos!";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM Tabela_usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "Esse e-mail já está cadastrado.";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("INSERT INTO Tabela_usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);

            if ($stmt->execute()) {
                $msg = "Cadastro realizado com sucesso!";

            } else {
                $msg = "Erro ao cadastrar.";
            }
        }
        $stmt->close();
    }
} else {
    $msg = "Método inválido.";
}

$conexao->close();

$_SESSION['msg_cadastro'] = $msg;
header("Location: ../frontend/index.php");
exit();
