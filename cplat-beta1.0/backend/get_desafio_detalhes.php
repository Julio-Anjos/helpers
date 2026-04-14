<?php
// backend/get_desafio_detalhes.php
session_start();
require 'conexao.php'; // Verifique se o caminho da conexão aqui está correto para a pasta backend

header('Content-Type: application/json');

if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'professor') {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Importante: usei alias para evitar conflitos e garantir que as colunas existam
    $stmt = $conn->prepare("SELECT codigo_submetido, feedback_ia, comentario_professor FROM progresso_desafios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Registro não encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID inválido']);
}