<?php
session_start();
include("conexao.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $nome = trim($_POST['nome'] ?? '');

    if (empty($email) || empty($matricula)|| empty($senha) || empty($nome)) {
        $msg = "Preencha todos os campos!";

    } elseif (!preg_match('/^[0-9]{6}$/', $matricula)) {
        $msg = "Matrícula inválida. Deve conter 6 números.";
    
    } else {
        $stmt = $conn->prepare("SELECT id FROM Tabela_usuarios WHERE email = ? OR matricula = ?");
        $stmt->bind_param("ss", $email, $matricula);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "Cadastro Falho: E-mail ou matrícula já cadastrados.";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Tabela_usuarios (nome, matricula, email, senha_hash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $matricula, $email, $senha_hash);

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

$conn->close();

$_SESSION['msg_cadastro'] = $msg;
header("Location: ../frontend/pages/bemvindos.php");
exit();
