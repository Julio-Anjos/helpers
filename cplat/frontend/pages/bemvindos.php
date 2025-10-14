<?php
session_start();

// Verifica se a sessão de usuário está ativa
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, define a variável para exibir o alerta
    $session_not_active = true;
} else {
    $session_not_active = false;
}
// Recupera o email do usuário da sessão
$email_usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guia C-Plat</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Orbitron', sans-serif;
      background: black;
      color: white;
      overflow: hidden;
    }

    header {
      background-color: #0052cc;
      width: 400px;
      margin: 20px auto 0 auto;
      padding: 10px;
      text-align: center;
      font-size: 24px;
      border: 4px solid white;
      clip-path: polygon(10% 0, 90% 0, 100% 100%, 0 100%);
    }

   .boasvindas {
      text-align: center;
    }

    .modulos {
      position: absolute;
      top: 100px;
      left: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .modulo {
      background-color: #8224e3;
      padding: 25px 20px;
      width: 200px;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
      position: relative;
      clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
      text-align: center;
      border: 2px solid white;
    }

    .modulo::after {
      content: '';
      position: absolute;
      top: -4px;
      left: -4px;
      width: calc(100% + 8px);
      height: calc(100% + 8px);
      border: 2px solid white;
      clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
      z-index: -1;
    }

    .gamepad {
      position: absolute;
      bottom: 30px;
      right: 40px;
      width: 120px;
      cursor: pointer;
    }

    .desenho {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
    }

    .video-centralizado {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 300px; /* ajuste conforme o tamanho do vídeo/imagem */
      height: auto;
      z-index: 1;
      pointer-events: none;
      transition: opacity 1s ease;
    }

    #imagemFinal {
      width: 300px;
      opacity: 0;
      top: 50%;
      left: 50%;
      height: 295px;
      display: none;
      transition: opacity 1s ease;
    }

  </style>
</head>

<body>


  <header>Guia C-Plat</header>
  <h1 class="boasvindas">Bem-vindo, <?php echo htmlspecialchars($email_usuario); ?>!</h1>
  <div class="modulos">
    <button class="modulo" onclick="window.location.href='modulo1.html'">MÓDULO 1</button>
    <button class="modulo" onclick="window.location.href='modulo2.html'">MÓDULO 2</button>
    <button class="modulo" onclick="window.location.href='modulo3.html'">MÓDULO 3</button>
    <button class="modulo" onclick="window.location.href='modulo4.html'">MÓDULO 4</button>
    <button class="modulo" onclick="window.location.href='modulo5.html'">MÓDULO 5</button>
    <button class="modulo" onclick="window.location.href='modulo6.html'">MÓDULO 6</button>
    <button class="modulo" onclick="window.location.href='modulo7.html'">MÓDULO 7</button>
    <button class="modulo" onclick="window.location.href='modulo8.html'">MÓDULO 8</button>
    <button class="modulo" onclick="window.location.href='modulo9.html'">MÓDULO 9</button>
    <button class="modulo" onclick="window.location.href='modulo10.html'">MÓDULO 10</button>
  </div>

  <a href="desafios.html">
    <img src="../assets/imagens/controle.png" alt="controle" class="gamepad">
  </a>


<div class="container-video">
  <video id="introVideo" class="video-centralizado">
    <source src="../assets/C-borg/animated/Cena-Kiddos.mp4" type="video/mp4">
  </video>

<img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado">
</div>

<script>
  const video = document.getElementById('introVideo');
  const imagem = document.getElementById('imagemFinal');

  video.addEventListener('ended', () => {
    imagem.style.display = 'block';
    imagem.style.opacity = 1;
    video.style.opacity = 0;
    setTimeout(() => {
      video.style.display = 'none';
    }, 1000);
  });

  video.play();
</script>

</body>
</html>