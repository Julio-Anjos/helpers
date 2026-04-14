<?php
session_start();

// Captura mensagens de erro/sucesso para login e cadastro
$msg_login = $_SESSION['msg_login'] ?? '';
$msg_cadastro = $_SESSION['msg_cadastro'] ?? '';
unset($_SESSION['msg_login'], $_SESSION['msg_cadastro']);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GuiaC-Plat - Login/Cadastro</title>
  <style>
    /* seu CSS permanece igual */
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #0b032d, #1d0df4);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      display: flex;
      background: transparent;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.4);
      max-width: 900px;
      width: 100%;
    }
    .left {
      flex: 1;
      background-color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .left img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .right {
      flex: 1;
      padding: 40px;
      background: transparent;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 700px;
    }
    .right h1 {
      font-size: 32px;
      margin-bottom: 10px;
    }
    .right h2 {
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 2px solid white;
      border-radius: 5px;
      background: transparent;
      color: white;
      font-size: 16px;
    }
    input::placeholder {
      color: #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      border: 2px solid white;
      border-radius: 5px;
      background-color: transparent;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: white;
      color: #1d0df4;
    }
    .switch {
      margin-top: 10px;
      text-align: center;
      cursor: pointer;
      color: #ccc;
      text-decoration: underline;
    }
    .msg {
      color: yellow;
      margin-bottom: 10px;
      text-align: center;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="left">
      <img src="../assets/imagens/Guiac-plat.png" alt="Imagem lateral"/>
    </div>
    <div class="right">
      <h1>GuiaC-Plat</h1>
      <h2 id="formTitle">Login</h2>

      <!-- Mensagens -->
      <div class="msg" id="msgLogin"><?php echo htmlspecialchars($msg_login); ?></div>
      <div class="msg" id="msgCadastro"><?php echo htmlspecialchars($msg_cadastro); ?></div>

      <form id="formulario" method="POST" action="../../backend/login.php">

        <div id="nomeContainer" class="hidden">
          <label for="nome">Nome:</label>
          <input type="text" name="nome" id="nome" placeholder="Digite seu nome">
        </div>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required placeholder="Digite seu email">

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required placeholder="Digite sua senha">

        <button type="submit" id="botao">Entrar</button>
      </form>

      <div class="switch" onclick="alternar()">Não tem conta? Cadastre-se</div>
    </div>
  </div>

  <script>
    let cadastro = false;

    function alternar() {
      cadastro = !cadastro;
      document.getElementById('nomeContainer').classList.toggle('hidden', !cadastro);
      document.getElementById('formulario').action = cadastro ? '../backend/cadastro.php' : '../backend/login.php';
      document.getElementById('formTitle').innerText = cadastro ? 'Cadastro' : 'Login';
      document.getElementById('botao').innerText = cadastro ? 'Cadastrar' : 'Entrar';
      document.querySelector('.switch').innerText = cadastro ? 'Já tem conta? Login' : 'Não tem conta? Cadastre-se';

      // Limpar mensagens ao alternar
      document.getElementById('msgLogin').innerText = '';
      document.getElementById('msgCadastro').innerText = '';
    }
  </script>

</body>
</html>
