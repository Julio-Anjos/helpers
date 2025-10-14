<?php

$servidor = "localhost";
$usuario = "userweb";
$senha = "CPlat2024@2025";
$data_base = "usuarioscplat";

$conexao = new mysqli($servidor, $usuario, $senha, $data_base);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>
