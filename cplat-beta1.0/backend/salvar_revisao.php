<?php
// backend/salvar_revisao.php
session_start();

// Habilitar exibição de erros para diagnóstico (remova após consertar)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao.php'; // Certifique-se que conexao.php está nesta mesma pasta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Captura dos dados
        $progresso_id   = isset($_POST['progresso_id']) ? (int)$_POST['progresso_id'] : 0;
        $comentario     = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
        $nota_professor = isset($_POST['nota_professor']) ? (int)$_POST['nota_professor'] : 0;
        $status         = isset($_POST['status']) ? $_POST['status'] : 'pre_aprovado';
        $revisado       = isset($_POST['revisado']) ? 1 : 0;

        if ($progresso_id === 0) {
            throw new Exception("ID de progresso inválido.");
        }

        // Prepara a Query - Verifique se os nomes das colunas batem com seu PHPMyAdmin
        $sql = "UPDATE progresso_desafios SET 
                comentario_professor = ?, 
                nota_professor = ?, 
                status = ?, 
                revisado = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Erro na preparação da query: " . $conn->error);
        }

        // "sisii" -> s: comentario (string), i: nota (int), s: status (string), i: revisado (int), i: id (int)
        $stmt->bind_param("sisii", $comentario, $nota_professor, $status, $revisado, $progresso_id);

        if ($stmt->execute()) {
            // Redirecionamento usando caminho absoluto para evitar erro 404
            header("Location: /helpers/cplat/frontend/pages/painel_professor.php?status=sucesso");
            exit();
        } else {
            throw new Exception("Erro ao executar update: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Se der erro, ele vai imprimir na tela em vez de dar erro 500 puramente
        die("Erro detectado: " . $e->getMessage());
    }
} else {
    header("Location: /helpers/cplat/frontend/pages/painel_professor.php");
    exit();
}