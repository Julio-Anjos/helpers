<?php
session_start();
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Encerra a sessão
header("Location: ../frontend/pages/bemvindos.php"); // Redireciona para a página de login ou home
exit();
?>