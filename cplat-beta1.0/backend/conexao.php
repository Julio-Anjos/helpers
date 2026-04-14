<?php
$servidor  = "localhost";
$usuario   = "userweb";
$senha     = "CPlat2024@2025";
$data_base = "usuarioscplat";

$conn = new mysqli($servidor, $usuario, $senha, $data_base);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'status' => 'erro',
        'msg' => 'Falha na conexão com o banco',
        'erro' => $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
