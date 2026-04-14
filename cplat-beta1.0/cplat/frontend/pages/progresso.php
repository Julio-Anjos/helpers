<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../../backend/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = (int) $_SESSION['usuario_id'];
$nome_usuario = $_SESSION['usuario'] ?? 'Estudante';
$email_usuario = $_SESSION['email'] ?? '';

// ==========================
// 1. BUSCA PROGRESSO TEÓRICO (QUIZZES)
// ==========================
$sql_teoria = "
SELECT m.id AS modulo_id, m.titulo, pm.status, pm.nota_quiz
FROM modulos m
LEFT JOIN progresso_modulo pm ON pm.modulo_id = m.id AND pm.usuario_id = ?
ORDER BY m.id ASC";

$stmt = $conn->prepare($sql_teoria);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res_teoria = $stmt->get_result();

$modulos_teoria = [];
$concluidos_teoria = 0;
while ($row = $res_teoria->fetch_assoc()) {
    if ($row['status'] === 'concluido') $concluidos_teoria++;
    $modulos_teoria[] = $row;
}
$total_modulos = count($modulos_teoria);
$percentual_teoria = $total_modulos > 0 ? round(($concluidos_teoria / $total_modulos) * 100) : 0;

// ==========================
// 2. BUSCA PROGRESSO PRÁTICO (DESAFIOS DETALHADOS)
// ==========================
$sql_pratica = "
SELECT 
    m.titulo, 
    pd.nota_ia, 
    pd.revisado, 
    pd.comentario_professor,
    pd.nota_professor,
    pd.status AS status_db
FROM modulos m
LEFT JOIN progresso_desafios pd ON pd.modulo_id = m.id AND pd.usuario_id = ?
ORDER BY m.id ASC";

$stmt2 = $conn->prepare($sql_pratica);
$stmt2->bind_param("i", $usuario_id);
$stmt2->execute();
$res_pratica = $stmt2->get_result();

$modulos_pratica = [];
$aprovados_ia = 0;
while ($row = $res_pratica->fetch_assoc()) {
    if (($row['nota_ia'] !== null && $row['nota_ia'] >= 70) || $row['status_db'] === 'finalizado') {
        $aprovados_ia++;
    }
    $modulos_pratica[] = $row;
}
$percentual_pratica = $total_modulos > 0 ? round(($aprovados_ia / $total_modulos) * 100) : 0;

$percentual_geral = round(($percentual_teoria + $percentual_pratica) / 2);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Progresso | C-Plat</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00bcd4; --bg: #f4f7f6; }
        body { font-family: 'Roboto', sans-serif; background: var(--bg); margin: 0; padding-bottom: 100px; color: #333; }

        /* TOPBAR */
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 20px; height: 70px; background-color: var(--primary); border-bottom: 3px solid white; box-shadow: 0 4px 8px rgba(0,0,0,0.2); position: sticky; top: 0; z-index: 1000; }
        .logo { width: 100px; cursor: pointer; }
        .right-group { display: flex; align-items: center; gap: 15px; color: white; }
        .avatar-btn { width: 44px; height: 44px; border-radius: 50%; background: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .user-dropdown { display: none; position: absolute; right: 20px; top: 60px; background: white; min-width: 200px; border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,.2); z-index: 9999; overflow: hidden; }
        .user-dropdown.show { display: block; }
        .user-dropdown a { display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px; }
        .user-dropdown a:hover { background: #f0f0f0; }

        /* CONTAINER E CARDS */
        .container { max-width: 1000px; margin: 40px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        h1 { color: var(--primary); text-align: center; margin-bottom: 30px; font-weight: 700; }

        .geral-box { background: #fff; padding: 25px; border-radius: 15px; border: 2px solid #eef2f3; margin-bottom: 35px; text-align: center; }
        .progress-bar-main { width: 100%; height: 30px; background: #e0e0e0; border-radius: 20px; overflow: hidden; margin: 15px 0; border: 1px solid #ddd; }
        .bar-fill-main { height: 100%; width: 0; background: linear-gradient(90deg, #00bcd4, #4caf50); transition: width 2s ease-in-out; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }

        /* ACCORDION COM BARRINHAS */
        details { background: #fff; border: 1px solid #ddd; border-radius: 12px; margin-bottom: 15px; overflow: hidden; }
        summary { padding: 20px; font-weight: bold; cursor: pointer; list-style: none; }
        .summary-content { display: flex; align-items: center; justify-content: space-between; }
        
        .mini-progress-container { width: 120px; height: 8px; background: #eee; border-radius: 10px; margin-left: 10px; overflow: hidden; display: inline-block; }
        .mini-bar-fill { height: 100%; transition: width 1.5s ease-in-out; }

        .content { padding: 0 20px 20px 20px; border-top: 1px solid #eee; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8f9fa; color: #777; font-size: 12px; text-transform: uppercase; padding: 12px; text-align: center; }
        td { padding: 12px; text-align: center; border-bottom: 1px solid #eee; font-size: 14px; }
        
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-ok { background: #e8f5e9; color: #2e7d32; }
        .status-erro { background: #ffebee; color: #c62828; }
        .status-alerta { background: #fff3e0; color: #ef6c00; }
        .comentario-box { font-size: 13px; color: #666; font-style: italic; text-align: left; max-width: 250px; }

        .btn-nav { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: var(--primary); color: white; padding: 15px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; box-shadow: 0 8px 20px rgba(0,188,212,0.3); z-index: 100; }
    </style>
</head>
<body>

<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" class="logo"></a>
  <div class="right-group">
    <span>Olá, <strong><?= htmlspecialchars($nome_usuario) ?></strong></span>
    <button class="avatar-btn" id="avatarToggle">👤</button>
    <div class="user-dropdown" id="userDropdown">
        <a href="bemvindos.php">🏠 Início</a>
        <a href="../../backend/logout.php" style="color:red;">🚪 Sair</a>
    </div>
  </div>
</header>

<div class="container">
    <h1>Dashboard de Evolução</h1>

    <div class="geral-box">
        <h3 style="margin:0; color:#444;">Progresso Geral</h3>
        <div class="progress-bar-main">
            <div class="bar-fill-main" id="geralBar" data-width="<?= $percentual_geral ?>%">0%</div>
        </div>
    </div>

    <details>
        <summary>
            <div class="summary-content">
                <span>📖 Teoria (Quizzes)</span>
                <div>
                    <span style="font-size: 13px; color: #888;"><?= $percentual_teoria ?>%</span>
                    <div class="mini-progress-container">
                        <div class="mini-bar-fill" style="width: <?= $percentual_teoria ?>%; background: var(--primary);"></div>
                    </div>
                </div>
            </div>
        </summary>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left">Módulo</th>
                        <th>Status</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modulos_teoria as $m): ?>
                    <tr>
                        <td style="text-align:left"><strong><?= htmlspecialchars($m['titulo']) ?></strong></td>
                        <td><span class="status-badge <?= ($m['status'] === 'concluido') ? 'status-ok' : 'status-erro' ?>"><?= $m['status'] === 'concluido' ? 'OK' : 'Pendente' ?></span></td>
                        <td><?= $m['nota_quiz'] ?? '-' ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>

    <details>
            <summary>
                <div class="summary-content">
                    <span>💻 Prática (Código C)</span>
                    <div>
                        <span style="font-size: 13px; color: #888;"><?= $percentual_pratica ?>%</span>
                        <div class="mini-progress-container">
                            <div class="mini-bar-fill" style="width: <?= $percentual_pratica ?>%; background: #4caf50;"></div>
                        </div>
                    </div>
                </div>
            </summary>
            <div class="content">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:left">Desafio</th>
                            <th>Nota IA (Parcial)</th>
                            <th>Nota Prof. (Final)</th>
                            <th>Status</th>
                            <th>Feedback Professor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modulos_pratica as $p): ?>
                        <tr>
                            <td style="text-align:left"><strong><?= htmlspecialchars($p['titulo']) ?></strong></td>
                            
                            <td style="color: #888;">
                                <?= $p['nota_ia'] !== null ? $p['nota_ia'] . '/100' : '-' ?>
                            </td>

                            <td>
                                <?php if ($p['nota_professor'] !== null): ?>
                                    <strong style="color: var(--primary); font-size: 1.1em;"><?= $p['nota_professor'] ?>/100</strong>
                                <?php else: ?>
                                    <span style="color: #ccc; font-style: italic;">Aguardando por Correções...</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($p['status_db'] === 'finalizado'): ?> 
                                    <span class="status-badge status-ok">Aprovado</span>
                                <?php elseif ($p['status_db'] === 'reprovado'): ?> 
                                    <span class="status-badge status-erro">Reprovado</span>
                                <?php elseif ($p['nota_ia'] !== null): ?> 
                                    <span class="status-badge status-alerta">Em análise</span>
                                <?php else: ?> 
                                    <span class="status-badge" style="background:#eee;">Não enviado</span> 
                                <?php endif; ?>
                            </td>
                            
                            <td class="comentario-box">
                                <?php if (!empty($p['comentario_professor'])): ?>
                                    <?= htmlspecialchars($p['comentario_professor']) ?>
                                <?php elseif ($p['revisado']): ?>
                                    <span style="color:#aaa;">Revisado sem comentários.</span>
                                <?php else: ?>
                                    <span style="color:#ccc;">Nenhum feedback ainda.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </details>
</div>

<a href="bemvindos.php" class="btn-nav">⟵ Voltar ao Início</a>

<script>
    const avatarBtn = document.getElementById('avatarToggle');
    const userDropdown = document.getElementById('userDropdown');
    avatarBtn.onclick = (e) => { e.stopPropagation(); userDropdown.classList.toggle('show'); };
    document.onclick = () => userDropdown.classList.remove('show');

    window.onload = () => {
        const bar = document.getElementById('geralBar');
        bar.style.width = bar.getAttribute('data-width');
        bar.innerText = bar.getAttribute('data-width');
    };
</script>
</body>
</html>