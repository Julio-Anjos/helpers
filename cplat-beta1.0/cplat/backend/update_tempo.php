<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['status'=>'erro','msg'=>'Usuário não autenticado']);
    exit;
}

if (!isset($_POST['modulo_id'], $_POST['tempo']) || !is_numeric($_POST['modulo_id']) || !is_numeric($_POST['tempo'])) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','msg'=>'Dados inválidos']);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$modulo_id  = (int) $_POST['modulo_id'];
$tempo      = (int) $_POST['tempo'];

require 'conexao.php';

// Log para debug
$log_message = date('Y-m-d H:i:s') . " - Atualizando tempo: usuario_id=$usuario_id, modulo_id=$modulo_id, tempo=$tempo\n";
error_log($log_message, 3, __DIR__ . '/debug_tempo.log');

// Atualiza APENAS o tempo total (soma, não substitui)
$sql = "
INSERT INTO progresso_modulo
    (usuario_id, modulo_id, tempo_total_segundos, status)
VALUES (?, ?, ?, 'em_andamento')
ON DUPLICATE KEY UPDATE
    tempo_total_segundos = tempo_total_segundos + ?,
    status = IF(status = 'concluido', 'concluido', 'em_andamento')
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $error_message = date('Y-m-d H:i:s') . " - Erro prepare tempo: " . $conn->error . "\n";
    error_log($error_message, 3, __DIR__ . '/debug_tempo.log');
    
    http_response_code(500);
    echo json_encode(['status'=>'erro','msg'=>'Erro no prepare','erro_sql'=>$conn->error]);
    exit;
}

$stmt->bind_param("iiii", $usuario_id, $modulo_id, $tempo, $tempo);

if (!$stmt->execute()) {
    $error_message = date('Y-m-d H:i:s') . " - Erro execute tempo: " . $stmt->error . "\n";
    error_log($error_message, 3, __DIR__ . '/debug_tempo.log');
    
    http_response_code(500);
    echo json_encode(['status'=>'erro','msg'=>'Erro ao executar query','erro_sql'=>$stmt->error]);
    exit;
}

$log_success = date('Y-m-d H:i:s') . " - Tempo atualizado: usuario_id=$usuario_id, modulo_id=$modulo_id, tempo_adicionado=$tempo\n";
error_log($log_success, 3, __DIR__ . '/debug_tempo.log');

echo json_encode([
    'status' => 'ok',
    'msg' => 'Tempo atualizado',
    'tempo_adicionado' => $tempo
]);

$stmt->close();
$conn->close();
?>