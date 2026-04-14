<?php
session_start();
require '../../backend/conexao.php';

if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'professor') {
    header("Location: bemvindos.php");
    exit();
}

$email_usuario = $_SESSION['email'] ?? '';
$nome_usuario = $_SESSION['usuario'] ?? '';

$filtro_revisado = $_GET['revisado'] ?? '';
$filtro_aluno = $_GET['aluno'] ?? '';
$filtro_modulo = $_GET['modulo'] ?? '';

$where = [];
$params = [];
$types = "";

if ($filtro_revisado !== '') {
    $where[] = "pd.revisado = ?";
    $params[] = $filtro_revisado;
    $types .= "i";
}

if ($filtro_aluno !== '') {
    $where[] = "u.nome LIKE ?";
    $params[] = "%$filtro_aluno%";
    $types .= "s";
}

if ($filtro_modulo !== '') {
    $where[] = "m.titulo LIKE ?";
    $params[] = "%$filtro_modulo%";
    $types .= "s";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

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
$where_sql
ORDER BY pd.revisado ASC, pd.atualizado_em DESC
";

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br>

<head>
<meta charset="UTF-8">
<title>Painel do Professor | C-Plat</title>

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Fira+Code&display=swap" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

<style>

:root{
--primary:#00bcd4;
--bg:#f4f7f6;
}

body{
background:var(--bg);
font-family:'Roboto',sans-serif;
margin:0;
}

.topbar{
display:flex;
align-items:center;
justify-content:space-between;
padding:0 20px;
height:70px;
background-color:var(--primary);
border-bottom:3px solid white;
box-shadow:0 4px 8px rgba(0,0,0,0.2);
}

.logo{
width:100px;
cursor:pointer;
}

.right-group{
display:flex;
align-items:center;
gap:15px;
color:white;
}

.container{
max-width:1300px;
margin:30px auto;
padding:0 20px;
}

.dashboard-card{
background:white;
border-radius:12px;
box-shadow:0 8px 24px rgba(0,0,0,0.05);
overflow:hidden;
}

.status-pill{
padding:6px 14px;
border-radius:20px;
font-size:11px;
font-weight:bold;
}

.status-pending{
background:#fff9db;
color:#f08c00;
}

.status-done{
background:#ebfbee;
color:#2b8a3e;
}

.btn-action{
background:var(--primary);
color:white;
border:none;
padding:8px 18px;
border-radius:6px;
font-weight:bold;
cursor:pointer;
}

#modalAvaliacao .w3-modal-content{
    max-width:1200px;
    width:95%;
    border-radius:10px;
    overflow:hidden;
}

/* ÁREA DO CÓDIGO */
.code-container{
    background:#1e1e1e;
    border-radius:8px;
    overflow:auto;
    border-left:4px solid var(--primary);
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

pre[class*="language-"]{
    margin:0 !important;
    font-size:14px !important;
    max-height:480px;
    overflow:auto;
    padding:20px !important;
}

/* FEEDBACK DA IA */
.ia-feedback-area{
    background:#f8f9fa;
    padding:16px;
    border-radius:8px;
    height:180px;
    overflow-y:auto;
    margin-bottom:18px;
    border:1px solid #e2e6ea;
    font-size:14px;
    line-height:1.5;
}

/* INPUTS DO FORM */
#modalAvaliacao input,
#modalAvaliacao select,
#modalAvaliacao textarea{
    font-size:14px;
}

/* BOTÃO */
#modalAvaliacao button[type="submit"]{
    font-weight:bold;
    letter-spacing:0.5px;
}

.bottom-buttons {
        position: fixed;
        bottom: 25px;       /* ajusta se quiser mais alto */
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 25px;
        z-index: 1000;
    }
.btn-nav {
        background: #00bcd4;
        color: white;
        padding: 12px 22px;
        font-size: 20px;
        border-radius: 8px;
        text-decoration: none;
        border: 2px solid white;
        transition: 0.25s ease-in-out;
        font-weight: bold;
    }

</style>

</head>

<body>

<header class="topbar">
<a href="bemvindos.php">
<img src="../assets/imagens/logo-cplat.png" class="logo">
</a>

<div class="right-group">
Professor: <strong><?=htmlspecialchars($nome_usuario)?></strong>
</div>
</header>

<div class="container">

<div class="dashboard-card">

<div class="w3-container w3-padding-24">
<h2>Fila de Correção</h2>
<p style="color:#95a5a6;">Avalie os desafios submetidos pelos alunos.</p>
</div>

<div class="w3-container w3-padding">

<form method="GET" class="w3-row-padding">

<div class="w3-col l2">
<label>Status</label>
<select name="revisado" class="w3-select w3-border">
<option value="">Todos</option>
<option value="0" <?=($filtro_revisado==='0')?'selected':''?>>Pendentes</option>
<option value="1" <?=($filtro_revisado==='1')?'selected':''?>>Revisados</option>
</select>
</div>

<div class="w3-col l3">
<label>Aluno</label>
<input type="text" name="aluno" value="<?=htmlspecialchars($filtro_aluno)?>" class="w3-input w3-border">
</div>

<div class="w3-col l3">
<label>Módulo</label>
<input type="text" name="modulo" value="<?=htmlspecialchars($filtro_modulo)?>" class="w3-input w3-border">
</div>

<div class="w3-col l2">
<label>&nbsp;</label>
<button type="submit" class="w3-button w3-cyan w3-block">Filtrar</button>
</div>

<div class="w3-col l2">
<label>&nbsp;</label>
<a href="painel_professor.php" class="w3-button w3-light-gray w3-block">Limpar</a>
</div>

</form>

</div>

<table class="w3-table-all">

<thead>
<tr>
<th>Aluno</th>
<th>Módulo</th>
<th class="w3-center">Nota IA</th>
<th class="w3-center">Status</th>
<th class="w3-center">Ação</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td><strong><?=htmlspecialchars($row['aluno_nome'])?></strong></td>

<td><?=htmlspecialchars($row['modulo_titulo'])?></td>

<td class="w3-center">
<span class="w3-tag w3-round w3-light-gray">
<b><?=htmlspecialchars($row['nota_ia'])?></b>/100
</span>
</td>

<td class="w3-center">
<span class="status-pill <?= $row['revisado'] ? 'status-done' : 'status-pending' ?>">
<?= $row['revisado'] ? '✔ REVISADO' : '⏳ PENDENTE' ?>
</span>
</td>

<td class="w3-center">
<button onclick="abrirAvaliacao(<?=$row['progresso_id']?>)" class="btn-action">
AVALIAR
</button>
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
            <span onclick="document.getElementById('modalAvaliacao').style.display='none'" 
            class="w3-button w3-display-topright w3-hover-red w3-large">&times;</span>
            <h3 style="color:white; margin:0; font-weight:bold;">Revisão de Desafio</h3>
        </header>

        <div class="w3-container w3-white w3-padding-32">
            <div class="w3-row-padding">

                <!-- COLUNA DO CÓDIGO -->
                <div class="w3-col l8 m12">
                    <label style="font-weight:bold; color: #7f8c8d;">💻 CÓDIGO DO ALUNO:</label>

                    <div class="code-container">
                        <pre><code id="codigoAluno" class="language-c"></code></pre>
                    </div>
                </div>

                <!-- COLUNA DA AVALIAÇÃO -->
                <div class="w3-col l4 m12">

                    <label style="font-weight:bold; color: #7f8c8d;">🤖 ANÁLISE IA:</label>

                    <div id="feedbackIA" class="ia-feedback-area"></div>

                    <form action="../../backend/salvar_revisao.php" method="POST">

                        <input type="hidden" name="progresso_id" id="progresso_id_input">

                        <div class="w3-row-padding" style="margin:0 -16px;">

                            <div class="w3-half">
                                <label style="font-weight:bold;">
                                    ⭐ NOTA DO PROFESSOR (0-100):
                                </label>

                                <input
                                    type="number"
                                    name="nota_professor"
                                    id="nota_prof_input"
                                    class="w3-input w3-border w3-round w3-margin-bottom"
                                    min="0"
                                    max="100"
                                    required
                                >
                            </div>

                            <div class="w3-half">
                                <label style="font-weight:bold;">
                                    📌 STATUS DO DESAFIO:
                                </label>

                                <select
                                    name="status"
                                    id="status_input"
                                    class="w3-select w3-border w3-round w3-margin-bottom"
                                >
                                    <option value="reprovado">❌ Reprovado</option>
                                    <option value="pre_aprovado">⏳ Pré-aprovado (IA)</option>
                                    <option value="finalizado">✅ Finalizado / Aprovado</option>
                                </select>
                            </div>

                        </div>

                        <label style="font-weight:bold;">
                            👨‍🏫 SEU COMENTÁRIO:
                        </label>

                        <textarea
                            name="comentario"
                            id="comentario_prof"
                            class="w3-input w3-border w3-round w3-margin-bottom"
                            rows="5"
                            style="resize:none;"
                        ></textarea>

                        <div class="w3-padding w3-light-gray w3-round w3-margin-bottom">
                            <input class="w3-check" type="checkbox" name="revisado" value="1" checked id="c1">
                            <label for="c1"> Marcar como revisado</label>
                        </div>

                        <button
                            type="submit"
                            class="w3-button w3-block w3-cyan w3-text-white w3-round-large w3-large"
                            style="font-weight:bold;"
                        >
                            SALVAR AVALIAÇÃO FINAL
                        </button>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-c.min.js"></script>

<script>

function abrirAvaliacao(id){

const modal=document.getElementById("modalAvaliacao")
const code=document.getElementById("codigoAluno")
const ia=document.getElementById("feedbackIA")

modal.style.display="block"

code.textContent="Carregando..."
ia.innerHTML="Aguardando..."

fetch(`../../backend/get_desafio_detalhes.php?id=${id}`)

.then(r=>r.json())

.then(data=>{

code.textContent=data.codigo_submetido

Prism.highlightElement(code)

ia.innerHTML=data.feedback_ia
? data.feedback_ia.replace(/\n/g,'<br>')
: "Sem análise"

document.getElementById("progresso_id_input").value=id
document.getElementById("comentario_prof").value=data.comentario_professor||""
document.getElementById("nota_prof_input").value=data.nota_professor ?? data.nota_ia
document.getElementById("status_input").value=data.status

})

}

</script>

<div style="height:50px;"></div> 
<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
</body>
</html>