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

if (!isset($_POST['modulo_id'], $_POST['acertos']) || !is_numeric($_POST['modulo_id']) || !is_numeric($_POST['acertos'])) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','msg'=>'Dados inválidos']);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$modulo_id  = (int) $_POST['modulo_id'];
$acertos    = (int) $_POST['acertos'];
$TOTAL_QUESTOES = 10;

// Log para debug (com tratamento de erro)
$log_message = date('Y-m-d H:i:s') . " - Finalizando quiz: usuario_id=$usuario_id, modulo_id=$modulo_id, acertos=$acertos\n";
error_log($log_message, 3, __DIR__ . '/debug_quiz.log');

if ($acertos < 0 || $acertos > $TOTAL_QUESTOES) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','msg'=>'Número de acertos inválido']);
    exit;
}

// calcula nota
$nota = (int)(($acertos / $TOTAL_QUESTOES) * 100);

// define status baseado na nota
$status = $nota >= 70 ? 'concluido' : 'reprovado';

// banco de dados
require 'conexao.php';

// CORRIGINDO A SQL: TEMOS 5 PARÂMETROS, NÃO 6
$sql = "
INSERT INTO progresso_modulo
    (usuario_id, modulo_id, nota_quiz, quiz_finalizado, status, data_conclusao)
VALUES (?, ?, ?, 1, ?, IF(?='concluido', NOW(), NULL))
ON DUPLICATE KEY UPDATE
    nota_quiz = VALUES(nota_quiz),
    quiz_finalizado = 1,
    status = VALUES(status),
    data_conclusao = IF(VALUES(status)='concluido', NOW(), data_conclusao)
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $error_message = date('Y-m-d H:i:s') . " - Erro prepare: " . $conn->error . "\n";
    error_log($error_message, 3, __DIR__ . '/debug_quiz.log');
    
    http_response_code(500);
    echo json_encode(['status'=>'erro','msg'=>'Erro no prepare','erro_sql'=>$conn->error]);
    exit;
}

// CORRIGIDO: temos 5 placeholders, então 5 parâmetros
// Placeholders: ?, ?, ?, ?, ? (o 1 é valor fixo)
// Parâmetros: $usuario_id, $modulo_id, $nota, $status, $status
$stmt->bind_param("iiiss", 
    $usuario_id,    // int
    $modulo_id,     // int  
    $nota,          // int
    $status,        // string (primeiro)
    $status         // string (segundo - para o IF)
);

if (!$stmt->execute()) {
    $error_message = date('Y-m-d H:i:s') . " - Erro execute: " . $stmt->error . "\n";
    error_log($error_message, 3, __DIR__ . '/debug_quiz.log');
    
    http_response_code(500);
    echo json_encode(['status'=>'erro','msg'=>'Erro ao executar query','erro_sql'=>$stmt->error]);
    exit;
}

$log_success = date('Y-m-d H:i:s') . " - Quiz salvo com sucesso: usuario_id=$usuario_id, modulo_id=$modulo_id, nota=$nota, status=$status\n";
error_log($log_success, 3, __DIR__ . '/debug_quiz.log');

echo json_encode([
    'status'     => $status,
    'usuario_id' => $usuario_id,
    'modulo_id'  => $modulo_id,
    'acertos'    => $acertos,
    'nota'       => $nota,
    'resultado'  => $nota >= 70 ? 'Aprovado' : 'Reprovado'
]);

$stmt->close();
$conn->close();
?>