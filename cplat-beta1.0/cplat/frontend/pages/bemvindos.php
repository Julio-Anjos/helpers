<?php
session_start();

$logado = isset($_SESSION['usuario']);
$email_usuario = $logado ? $_SESSION['email'] : '';
$nivel = $_SESSION['nivel'] ?? 'aluno'; // Pega o nível da sessão

// Captura mensagens de login/cadastro
$msg_login = $_SESSION['msg_login'] ?? '';
$msg_cadastro = $_SESSION['msg_cadastro'] ?? '';
unset($_SESSION['msg_login'], $_SESSION['msg_cadastro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guia C-Plat</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
/* ===========================
   Seu CSS existente
=========================== */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  height: 70px;
  background-color: #00bcd4;
  border-bottom: 3px solid white;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.logo {
  width: 100px;
  height: auto;
  cursor: pointer;
}
.logo:hover { transform: scale(1.05); }

.right-group {
  display: flex;
  align-items: center;
  gap: 15px;
}
.boasvindas {
  margin: 0;
  font-size: 20px;
  font-weight: bold;
  font-family: 'Arial', sans-serif;
  color: white;
  line-height: 1;
}
.logout-btn {
  background-color: #e33c3c;
  color: white;
  border: none;
  padding: 10px 20px;
  font-weight: bold;
  cursor: pointer;
  border-radius: 6px;
  transition: background-color 0.3s;
}
.logout-btn:hover { background-color: #c32a2a; }




/* ===========================
   Demais estilos da página
=========================== */
body { margin:0; font-family:'Orbitron',sans-serif; background:#ddddee; color:white; overflow:hidden; }

.modulos {
  position: absolute;
  bottom: 100px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  gap: 50px;
  align-items: center;
}
.linha {
  display: flex;
  justify-content: center;
  gap: 50px;
  flex-wrap: nowrap; /* força tudo na mesma linha */
}


.modulo {
  background-color: #00bcd4; padding:25px 20px; width:200px; color:white;
  font-weight:bold; border:none; cursor:pointer; position:relative;
  clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
  text-align:center; border:2px solid white;
  box-shadow:0 4px 8px rgba(0,0,0,0.3); transition: all 0.3s ease;
}
.modulo:hover { transform:translateY(-5px); box-shadow:0 8px 16px rgba(0,0,0,0.5); }
.modulo::after {
  content:''; position:absolute; top:-4px; left:-4px; width:calc(100% + 8px);
  height:calc(100% + 8px); border:2px solid white; clip-path: polygon(10% 0%,100% 0%,90% 100%,0% 100%);
  z-index:-1;
}
.gamepad { position:absolute; bottom:30px; right:40px; width:120px; cursor:pointer; }
.video-centralizado {
  position:absolute; top:32%; left:50%;
  transform:translate(-50%, -50%);
  width:420px; 
  height:500px;
  z-index:1; 
  pointer-events:none; 
  transition:opacity 1s ease;
}
#imagemFinal { width:300px; 
  height:295px; 
  opacity:0; 
  display:none; 
  transition:opacity 1s ease;
  transform: translate(calc(-50% + 15px), -48%); }
.faixa-ondas { position:relative; height:500px; background-color:#000000; overflow:hidden; }
.faixa-ondas::after {
  content:''; position:absolute; bottom:-50px; left:0; width:100%; height:100px;
  background-color:#ddddee; border-radius:50% 50% 0 0 / 50% 50% 0 0;
}
.avatar-btn { 
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  } 
.avatar-icon { 
  font-size: 22px;
  color: #00bcd4;
  }
.user-menu {
  position: relative;
}
.user-dropdown {
 display: none;
 position: absolute;
 right: 0;
 top: calc(100% + 8px);
 background: white;
 min-width: 200px;
 border-radius: 8px;
 box-shadow: 0 8px 16px rgba(0,0,0,.35);
 z-index: 9999;
 }
 .user-dropdown {
  display: none;
  position: absolute;
  right: 0;
  top: calc(100% + 10px);
  background: #ffffff;
  min-width: 220px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0,0,0,.25);
  z-index: 9999;
  overflow: hidden;
  font-family: 'Roboto', sans-serif;
}

/* email do usuário */
.user-dropdown .user-email {
  padding: 12px 16px;
  font-size: 14px;
  font-weight: bold;
  color: #333;
  border-bottom: 1px solid #eee;
  background: #f7f7f7;
}

/* links */
.user-dropdown a {
  display: block;
  padding: 12px 16px;
  color: #333;
  text-decoration: none;
  font-size: 14px;
  transition: background 0.2s;
}

.user-dropdown a:hover {
  background: #f0f0f0;
}

/* logout em destaque */
.user-dropdown a.logout {
  color: #c62828;
  font-weight: bold;
}

.user-dropdown.show {
 display: block;
}

</style>
</head>
<body>
<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
  <div class="right-group">
    <?php if($logado): ?>
      <h1 class="boasvindas">Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
      
      <div class="user-menu"> 
        <button class="avatar-btn" id="avatarToggle" aria-label="Menu do usuário"> 
          <span class="avatar-icon">👤</span> 
        </button> 

        <div class="user-dropdown" id="userDropdown"> 
          <div class="user-email"> <?php echo htmlspecialchars($email_usuario); ?> </div> 
          
          <a href="progresso.php">📊 Meu progresso</a> 

          <?php if ($nivel === 'professor'): ?>
            <a href="painel_professor.php" style="background: #fff3e0; font-weight: bold; color: #e67e22;">
              👨‍🏫 Corrigir Desafios
            </a>
          <?php endif; ?>

          <a href="../../backend/logout.php" class="logout">🚪 Logout</a> 
        </div> 
      </div>
    <?php else: ?>
      <button onclick="document.getElementById('id01').style.display='block'" class="w3-btn w3-light-grey w3-border w3-round-large w3-large w3-text-dark-gray">Login</button>
    <?php endif; ?>
  </div>
</header>

<!-- Modal de Login/Cadastro - Versão W3 -->
<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-animate-zoom w3-card-4" style="max-width:500px">
    
    <header class="w3-container w3-cyan"> 
    <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright w3-hover-teal">&times;</span>
        <h2 id="formTitle" class="w3-text-white">Login</h2>
    </header>
    
    <div class="w3-container">
      <h1 class="w3-center w3-text-black">GuiaC-Plat</h1>
      
      <!-- Mensagens de login/cadastro -->
      <div class="w3-text-red" id="msgLogin"><?php echo htmlspecialchars($msg_login); ?></div>
      <div class="w3-text-green" id="msgCadastro"><?php echo htmlspecialchars($msg_cadastro); ?></div>

      <form id="formulario" method="POST" action="../../backend/login.php" class="w3-container">
        <div id="nomeContainer" class="w3-animate-opacity" style="display:none;">
          <label for="nome" class="w3-text-black">Nome:</label>
          <input class="w3-input w3-border w3-round" type="text" name="nome" id="nome" placeholder="Digite seu nome">
          <label for="matricula" class="w3-text-black">Matrícula:</label>
          <input class="w3-input w3-border w3-round" type="text" name="matricula" id="matricula" placeholder="Digite sua matrícula" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
        </div>

        <label for="email" class="w3-text-black">Email:</label>
        <input class="w3-input w3-border w3-round" type="email" name="email" id="email" required placeholder="Digite seu email">

        <label for="senha" class="w3-text-black">Senha:</label>
        <input class="w3-input w3-border w3-round" type="password" name="senha" id="senha" required placeholder="Digite sua senha">

        <button type="submit" id="botao" class="w3-button w3-margin-top w3-round w3-cyan w3-hover-teal w3-text-white">Entrar</button>
      </form>

      <div class="w3-center w3-margin-top">
        <span class="w3-text-blue w3-hover-opacity switch" style="cursor:pointer;" onclick="alternar()">Não tem conta? Cadastre-se</span>      </div>
      </div>

    <footer class="w3-container w3-center w3-cyan">
      <p class="w3-text-white">GuiaC-Plat © 2025</p>
    </footer>
  </div>
</div>

<div class="faixa-ondas"></div>
<div class="modulos">
  <?php
  $total = 10;
  $por_linha = ceil($total / 2); // divide igualmente entre 2 linhas

  for ($linha = 0; $linha < 2; $linha++):
    echo '<div class="linha">';
    for ($i = 1 + $linha * $por_linha; $i <= min(($linha + 1) * $por_linha, $total); $i++): ?>
      <button class="modulo"
        onclick="<?php if ($logado): ?>
          window.location.href='modulo<?= $i ?>.php';
        <?php else: ?>
          abrirLoginModal();
        <?php endif; ?>">
        MÓDULO <?= $i ?>
      </button>
    <?php endfor;
    echo '</div>';
  endfor;
  ?>
</div>

<div class="container-video">
  <video id="introVideo" class="video-centralizado">
    <source src="../assets/C-borg/animated/intro-home.mp4" type="video/quicktime">
  </video>
  <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado">
</div>

<script>

const avatarBtn = document.getElementById('avatarToggle');
const userDropdown = document.getElementById('userDropdown');

if (avatarBtn && userDropdown) {
  avatarBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    userDropdown.classList.remove('show');
  });
}



// Vídeo
const video = document.getElementById('introVideo');
const imagem = document.getElementById('imagemFinal');

// Checa se já foi reproduzido nesta sessão
if (!sessionStorage.getItem('videoReproduzido')) {
  video.addEventListener('ended', () => {
    imagem.style.display='block'; 
    imagem.style.opacity=1; 
    video.style.opacity=0;
    setTimeout(()=>{video.style.display='none';},1000);

    // Marca como reproduzido
    sessionStorage.setItem('videoReproduzido', 'true');
  });
  video.play();
} else {
  // Já foi reproduzido, mostra apenas imagem final
  video.style.display = 'none';
  imagem.style.display = 'block';
  imagem.style.opacity = 1;
}


const modal = document.getElementById('id01'); // usa o id que existe no HTML

function abrirLoginModal() {
  modal.style.display = 'block';
}

function fecharLoginModal() {
  modal.style.display = 'none';
}

// fecha quando clica fora do conteúdo do modal
window.addEventListener('click', (e) => {
  if (e.target === modal) fecharLoginModal();
});

// Alternar login/cadastro
// Alternar login/cadastro
let cadastro = false;
document.getElementById('matricula').required = false;
document.getElementById('nome').required = false;

function alternar() {
  cadastro = !cadastro;

  const nomeContainer = document.getElementById('nomeContainer');
  const formulario = document.getElementById('formulario');
  const titulo = document.getElementById('formTitle');
  const botao = document.getElementById('botao');
  const msgLogin = document.getElementById('msgLogin');
  const msgCadastro = document.getElementById('msgCadastro');
  const switchElem = document.querySelector('.switch');
  const matriculaInput = document.getElementById('matricula');
  const nomeInput = document.getElementById('nome');

  if (cadastro) {
    nomeContainer.style.display = '';
    formulario.action = '../../backend/cadastro.php';
    titulo.innerText = 'Cadastro';
    botao.innerText = 'Cadastrar';
    switchElem.innerText = 'Já tem conta? Login';

    matriculaInput.required = true;
    nomeInput.required = true;

  } else {
    nomeContainer.style.display = 'none';
    formulario.action = '../../backend/login.php';
    titulo.innerText = 'Login';
    botao.innerText = 'Entrar';
    switchElem.innerText = 'Não tem conta? Cadastre-se';

    matriculaInput.required = false;
    nomeInput.required = false;
  }

  if (msgLogin) msgLogin.innerText = '';
  if (msgCadastro) msgCadastro.innerText = '';
}

    let timeoutId;

</script>

</body>
</html>
