<?php
require '../../backend/conexao.php'; 

$usuario_id = $_SESSION['usuario_id'] ?? null;
$modulo_id = (int) ($MODULO_ID ?? 0);

if ($usuario_id && $modulo_id) {
    // Só cria registro se não existir (não sobrescreve status concluído)
    $sql = "
        INSERT INTO progresso_modulo 
        (usuario_id, modulo_id, status) 
        VALUES (?, ?, 'em_andamento')
        ON DUPLICATE KEY UPDATE 
            status = IF(status = 'concluido', 'concluido', 'em_andamento')
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $modulo_id);
    $stmt->execute();
    $stmt->close();
}
?>