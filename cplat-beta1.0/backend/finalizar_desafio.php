<?php
session_start();
header('Content-Type: application/json');

include_once 'conexao.php'; 

$input = file_get_contents('php://input');
$input = mb_convert_encoding($input, 'UTF-8', 'UTF-8');
$data = json_decode($input, true);

if (!$data || !isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Dados invalidos ou sessao expirada']);
    exit;
}

$usuario_id  = $_SESSION['usuario_id'];
$desafio_id  = $data['desafio_id'];
$modulo_id   = $data['modulo_id'];
$nota_ia     = $data['nota'];
$feedback_ia = $data['feedback_ia']; // Novo campo
$codigo      = $data['codigo'];
$status      = ($nota_ia >= 70) ? 'pre_aprovado' : 'reprovado';

try {
    $sql = "INSERT INTO progresso_desafios (usuario_id, modulo_id, desafio_id, codigo_submetido, nota_ia, feedback_ia, status, revisado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 0)
            ON DUPLICATE KEY UPDATE 
            codigo_submetido = VALUES(codigo_submetido),
            nota_ia = VALUES(nota_ia),
            feedback_ia = VALUES(feedback_ia),
            status = VALUES(status),
            revisado = 0";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario_id, $modulo_id, $desafio_id, $codigo, $nota_ia, $feedback_ia, $status]);

    echo json_encode(['success' => true, 'status' => $status]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro no banco: ' . $e->getMessage()]);
}
?>