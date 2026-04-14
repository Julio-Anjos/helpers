<?php
session_start();
require '../../backend/conexao.php';

// Segurança
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'professor') {
    header("Location: bemvindos.php");
    exit();
}

$logado = isset($_SESSION['usuario']);
$email_usuario = $_SESSION['email'] ?? '';
$nome_usuario = $_SESSION['usuario'] ?? '';
$nivel = $_SESSION['nivel'] ?? 'aluno';

// Consulta
$sql = "
SELECT 
    pd.id AS progresso_id,
    u.nome AS aluno_nome,
    m.titulo AS modulo_titulo,
    pd.nota_ia,
    pd.nota_professor,
    pd.status,
    pd.revisado,
    pd.atualizado_em
FROM progresso_desafios pd
JOIN Tabela_usuarios u ON pd.usuario_id = u.id
JOIN modulos m ON pd.modulo_id = m.id
ORDER BY pd.revisado ASC, pd.atualizado_em DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor | C-Plat</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Fira+Code&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    
    <style>
        :root { --primary: #00bcd4; --bg: #f4f7f6; }
        body { background: var(--bg); font-family: 'Roboto', sans-serif; margin: 0; }

        /* HEADER PADRÃO DO SEU PROJETO */
        .topbar {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 20px;
          height: 70px;
          background-color: var(--primary);
          border-bottom: 3px solid white;
          box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .logo { width: 100px; height: auto; cursor: pointer; }
        .right-group { display: flex; align-items: center; gap: 15px; color: white; }
        
        /* Dropdown do Usuário */
        .user-menu { position: relative; }
        .avatar-btn { width: 44px; height: 44px; border-radius: 50%; background: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .avatar-icon { font-size: 22px; color: var(--primary); }
        .user-dropdown { display: none; position: absolute; right: 0; top: calc(100% + 10px); background: white; min-width: 200px; border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,.2); z-index: 9999; overflow: hidden; }
        .user-dropdown.show { display: block; }
        .user-dropdown a { display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px; }
        .user-dropdown a:hover { background: #f0f0f0; }
        .user-dropdown .user-email { padding: 12px 16px; font-size: 13px; font-weight: bold; background: #f7f7f7; color: #666; border-bottom: 1px solid #eee; }

        /* CONTEÚDO */
        .container { max-width: 1300px; margin: 30px auto; padding: 0 20px; }
        .btn-voltar { background: white; color: #555; border: 1px solid #ddd; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; margin-bottom: 20px; }
        .btn-voltar:hover { background: #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

        .dashboard-card { background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden; }
        .status-pill { padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-pending { background: #fff9db; color: #f08c00; }
        .status-done { background: #ebfbee; color: #2b8a3e; }
        
        .btn-action { background: var(--primary); color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; }

        /* MODAL */
        #modalAvaliacao .w3-modal-content { max-width: 1300px; width: 95%; border-radius: 12px; overflow: hidden; }
        .code-container { background: #1e1e1e; border-radius: 8px; overflow: hidden; border-left: 5px solid var(--primary); }
        pre[class*="language-"] { margin: 0 !important; font-size: 15px !important; height: 550px !important; }
        .ia-feedback-area { background: #f1f3f5; padding: 20px; border-radius: 8px; height: 200px; overflow-y: auto; margin-bottom: 20px; }
    </style>
</head>
<body>

<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
  <div class="right-group">
    <span class="w3-hide-small">Professor: <strong><?php echo htmlspecialchars($nome_usuario); ?></strong></span>
    <div class="user-menu">
      <button class="avatar-btn" id="avatarToggle">
        <span class="avatar-icon">👤</span>
      </button>
      <div class="user-dropdown" id="userDropdown">
        <div class="user-email"><?php echo htmlspecialchars($email_usuario); ?></div>
        <a href="bemvindos.php">🏠 Voltar ao Início</a>
        <a href="../../backend/logout.php" style="color:red; font-weight:bold;">🚪 Sair</a>
      </div>
    </div>
  </div>
</header>

<div class="container">
    <a href="bemvindos.php" class="btn-voltar">
        ⬅ Voltar para o Início
    </a>

    <div class="dashboard-card">
        <div class="w3-container w3-padding-24">
            <h2 style="margin:0; font-weight:bold; color: #34495e;">Fila de Correção</h2>
            <p style="color: #95a5a6;">Avalie os desafios práticos submetidos pelos alunos.</p>
        </div>
        
        <table class="w3-table-all">
            <thead>
                <tr>
                    <th>Aluno</th>
                    <th>Módulo / Desafio</th>
                    <th class="w3-center">Nota IA</th>
                    <th class="w3-center">Status</th>
                    <th class="w3-center">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['aluno_nome']) ?></strong></td>
                    <td><?= htmlspecialchars($row['modulo_titulo']) ?></td>
                    <td class="w3-center"><span class="w3-tag w3-round w3-light-gray"><b><?= $row['nota_ia'] ?></b>/100</span></td>
                    <td class="w3-center">
                        <span class="status-pill <?= $row['revisado'] ? 'status-done' : 'status-pending' ?>">
                            <?= $row['revisado'] ? '✔ REVISADO' : '⏳ PENDENTE' ?>
                        </span>
                    </td>
                    <td class="w3-center">
                        <button onclick="abrirAvaliacao(<?= $row['progresso_id'] ?>)" class="btn-action">AVALIAR</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalAvaliacao" class="w3-modal">
    <div class="w3-modal-content w3-animate-top w3-card-4">
        <header class="w3-container w3-cyan w3-padding-16"> 
            <span onclick="document.getElementById('modalAvaliacao').style.display='none'" class="w3-button w3-display-topright w3-hover-red w3-large">&times;</span>
            <h3 style="color:white; margin:0; font-weight:bold;">Revisão de Desafio</h3>
        </header>

        <div class="w3-container w3-white w3-padding-32">
            <div class="w3-row-padding">
                <div class="w3-col l8 m12">
                    <label style="font-weight:bold; color: #7f8c8d;">💻 CÓDIGO DO ALUNO:</label>
                    <div class="code-container">
                        <pre><code id="codigoAluno" class="language-c"></code></pre>
                    </div>
                </div>

                <div class="w3-col l4 m12">
                    <label style="font-weight:bold; color: #7f8c8d;">🤖 ANÁLISE IA:</label>
                    <div id="feedbackIA" class="ia-feedback-area"></div>

                    <form action="../../backend/salvar_revisao.php" method="POST">
                        <input type="hidden" name="progresso_id" id="progresso_id_input">
                        
                        <div class="w3-row-padding" style="margin:0 -16px;">
                            <div class="w3-half">
                                <label style="font-weight:bold;">⭐ NOTA DO PROFESSOR (0-100):</label>
                                <input type="number" name="nota_professor" id="nota_prof_input" class="w3-input w3-border w3-round w3-margin-bottom" min="0" max="100" required>
                            </div>
                            
                            <div class="w3-half">
                                <label style="font-weight:bold;">📌 STATUS DO DESAFIO:</label>
                                <select name="status" id="status_input" class="w3-select w3-border w3-round w3-margin-bottom">
                                    <option value="reprovado">❌ Reprovado</option>
                                    <option value="pre_aprovado">⏳ Pré-aprovado (IA)</option>
                                    <option value="finalizado">✅ Finalizado / Aprovado</option>
                                </select>
                            </div>
                        </div>

                        <label style="font-weight:bold;">👨‍🏫 SEU COMENTÁRIO:</label>
                        <textarea name="comentario" id="comentario_prof" class="w3-input w3-border w3-round w3-margin-bottom" rows="5" style="resize:none;"></textarea>
                        
                        <div class="w3-padding w3-light-gray w3-round w3-margin-bottom">
                            <input class="w3-check" type="checkbox" name="revisado" value="1" checked id="c1">
                            <label for="c1"> Marcar como revisado</label>
                        </div>

                        <button type="submit" class="w3-button w3-block w3-cyan w3-text-white w3-round-large w3-large" style="font-weight:bold;">SALVAR AVALIAÇÃO FINAL</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-c.min.js"></script>

<script>
// Toggle do Menu do Avatar
const avatarBtn = document.getElementById('avatarToggle');
const userDropdown = document.getElementById('userDropdown');

avatarBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('show');
});

document.addEventListener('click', () => {
    userDropdown.classList.remove('show');
});

// Lógica do Modal
function abrirAvaliacao(id) {
    const modal = document.getElementById('modalAvaliacao');
    const codeTag = document.getElementById('codigoAluno');
    const iaBox = document.getElementById('feedbackIA');
    const inputId = document.getElementById('progresso_id_input');
    const inputComent = document.getElementById('comentario_prof');

    modal.style.display = 'block';
    codeTag.textContent = "Carregando código...";
    iaBox.innerHTML = "<i>Aguardando...</i>";
    inputId.value = id;

    fetch(`../../backend/get_desafio_detalhes.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            codeTag.textContent = data.codigo_submetido;
            Prism.highlightElement(codeTag);
            iaBox.innerHTML = data.feedback_ia ? data.feedback_ia.replace(/\n/g, '<br>') : "Sem análise.";
            inputComent.value = data.comentario_professor || "";
            document.getElementById('nota_prof_input').value = (data.nota_professor !== null) ? data.nota_professor : data.nota_ia;
            document.getElementById('status_input').value = data.status;
        })
        .catch(err => console.error(err));
}

window.onclick = function(event) {
    if (event.target == document.getElementById('modalAvaliacao')) {
        document.getElementById('modalAvaliacao').style.display = "none";
    }
}
</script>

</body>
</html>