<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Desafio 5: Controle de Acesso Com Tentativas Limitadas</title>

<!-- ESTILOS -->
<style>
    *{
      box-sizing: border-box;

    }

    html { margin: 0; }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #eeeeee;
        color: #eaeaea;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .topbar {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        height: 70px;
        background-color: #00bcd4;
        border-bottom: 3px solid white;
        box-sizing: border-box;
    }

    .logo {
        width: 100px;
        height: auto;
        cursor: pointer;
    }
    .logo:hover { transform: scale(1.05); }

    h1 { color: #00bcd4; }

    form {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
    }

    p, ul, ol {
        font-size: 27px;
        text-align: justify;
        color: #000022;
        padding: 10px 30px;
        background: #ffffff;
        border-radius: 10px;
        margin: 10px;
    }

    button {
        background: #00bcd4;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 18px;
    }
    button:hover { background: #0097a7; }

    /* CONTEINER DOS CONSOLES */
    .console-block {
        width: 100%;
        max-width: 1100px;
        margin-top: 25px;
    }

    .console-title {
        font-size: 20px;
        font-weight: bold;
        color: #ffffff;
        background: #222222;
        border-radius: 8px 8px 0 0;
        padding: 10px 15px;
        border: 1px solid #444;
        border-bottom: none;
    }

    #editorContainer {
        width: 100%;
        height: 350px;
        border: 1px solid #444;
        border-radius: 0 0 10px 10px;
    }

    #outputBlock {
        width: 100%;
        max-width: 1100px;
        margin-top: 25px;
    }

    #outputTitle {
        font-size: 20px;
        font-weight: bold;
        color: #ffffff;
        background: #222222;
        border-radius: 8px 8px 0 0;
        padding: 10px 15px;
        border: 1px solid #444;
        border-bottom: none;
    }

    #output {
        background: #222;
        color: #f4f4f4;
        padding: 1em;
        border-radius: 0 0 10px 10px;
        border: 1px solid #444;
        border-top: none;
        white-space: pre-wrap;
        max-height: 400px;
        overflow-y: auto;
        font-family: "JetBrains Mono", "Fira Code", monospace;
        line-height: 1.5;
        width: 100%;
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

    .btn-nav:hover {
        background: #0096a8;
        transform: translateY(-3px);
    }
</style>

<!-- MONACO EDITOR -->
<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>

</head>
<body>

<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
</header>

<h1>5° Desafio:</h1>

<p style="color: black">
Imagine que você foi contratado para melhorar o sistema de acesso de um pequeno laboratório. O terminal de entrada precisa validar uma senha numérica. O problema? Os usuários insistem em digitar a senha errada, e o sistema atual não limita tentativas — fica travado num looping humano de teimosia. Sua missão é criar um programa em C que controle essas tentativas usando estruturas de repetição.<br><br>

<b>Checklist de Implementação</b><br>
<b>1.</b> O sistema deve solicitar ao usuário a senha de acesso.<br>
<b>2.</b> A senha correta é 1234.<br>
<b>3.</b> O usuário tem no máximo 3 tentativas para acertar.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>-</b> Se acertar antes de esgotar as tentativas, o programa exibe uma mensagem de "Acesso liberado" e encerra.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>-</b> Se errar as 3 vezes, o programa deve informar "Acesso bloqueado" e parar.<br>
<b>4.</b> Você deve usar laços de repetição (while ou do-while) para controlar as tentativas.<br><br>

<b>Objetivo</b><br>
Ao final, o programa deve ser capaz de solicitar a senha repetidamente, verificar corretamente a entrada e interromper o fluxo conforme o acerto ou o limite de tentativas, demonstrando domínio do uso de condições dentro de laços, contadores de repetição e controle claro do fluxo de execução.
</p>

<!-- BLOCO EDITOR + TÍTULO -->
<div class="console-block">
    <div class="console-title">Editor de Código</div>
    <div id="editorContainer"></div>
</div>

<!-- FORM -->
<form id="codeForm">
    <input type="hidden" name="codigo" id="campoCodigo">
    <div>
        <button type="button" id="clearBtn">Limpar</button>
        <button type="submit">Corrigir Código</button>
    </div>
</form>

<!-- BLOCO OUTPUT + TÍTULO -->
<div id="outputBlock">
    <div id="outputTitle">Console</div>
    <div id="output"></div>
</div>
<div style="height:50px;"></div> 
<div class="bottom-buttons">
<a href="modulo5.php" class="btn-nav">⟵ Voltar</a>
      </div>






<script>
// Atualiza o tempo de estudo para o Módulo 3
fetch('../../backend/update_time.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ modulo_id: 5 })
});

// ===== MONACO EDITOR =====
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});

let editor;

// ===== ELEMENTOS =====
const SERVER_URL = "http://192.168.0.253:8080";
const outputDiv = document.getElementById("output");
const clearBtn  = document.getElementById("clearBtn");
const submitBtn = document.querySelector('button[type="submit"]');

// O Prompt Template será configurado posteriormente conforme combinado
const PROMPT_TEMPLATE = `
### PERSONA
Você é o Corretor Automático da plataforma C-Plat. Seu tom é profissional e técnico.

### OBJETIVO
Avaliar o desafio "Validador de Senha com Limite". Nota: 0 a 100.

### REQUISITOS TÉCNICOS:
1. Uso obrigatório de estrutura de repetição ('while' ou 'do-while').
2. Senha correta definida como '1234'.
3. Limite máximo de 3 tentativas.
4. Lógica de interrupção: O programa deve parar se o usuário acertar OU se errar 3 vezes.
5. Mensagens específicas: "Acesso liberado" para acerto e "Acesso bloqueado" para erro após 3 tentativas.

### TABELA DE PONTUAÇÃO (TOTAL: 100 PTS)
1. **Estrutura de Repetição e Controle (40 pts):** Uso correto do laço com uma condição que respeite o limite de 3 tentativas.
2. **Lógica de Verificação (30 pts):** Uso de 'if' dentro do laço para comparar a senha e 'break' (ou condição no laço) para sair ao acertar.
3. **Gerenciamento de Contador (15 pts):** Incremento correto da variável que conta as tentativas.
4. **Interação e Mensagens (15 pts):** Exibição das mensagens corretas de "liberado" ou "bloqueado" nos cenários certos.

### REGRAS DE STATUS (Média para aprovação: 70 pts)
- **Se nota == 100:** "STATUS: DESAFIO CONCLUÍDO COM EXCELÊNCIA!"
- **Se nota >= 70 e < 100:** "STATUS: DESAFIO CONCLUÍDO! (Aprovado)"
- **Se nota < 70:** "STATUS: DESAFIO REPROVADO (Necessário atingir 70 pontos)"

### REGRAS DE RESPOSTA (Siga estritamente)
- **Primeira linha:** Exiba a **NOTA: [valor]/100** em negrito.
- **Segunda linha:** Exiba o "STATUS" conforme as regras acima.
- Se o código estiver 100% correto: Inicie com "DESAFIO CONCLUÍDO!" seguido de um resumo do que foi feito.
- Se faltar algum critério: Inicie com "OPS, ALGO ESTÁ FALTANDO..." e liste o que falta em bullet points.
- Se houver erro de sintaxe: Explique o erro de forma simples e sugira como corrigir sem entregar o código de bandeja.
- Explique a pontuação do aluno em cada um dos critérios citados na "TABELA DE PONTUAÇÃO", detalhando onde houve perda de pontos.
- Se o código não compilar, nota zero.
- Evite estruturas de tabelas para o detalhamento da pontuação.
### CÓDIGO PARA ANALISAR:
`;

// ===== LIMPAR =====
clearBtn.addEventListener("click", () => {
  editor.setValue("");
  outputDiv.textContent = "Editor limpo.";
  submitBtn.disabled = false;
});

// ===== SUBMIT =====
document.getElementById("codeForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  
  const text = editor.getValue().trim();

  // 1. Validação Prévia (Ajustar requisitos conforme o tema do Módulo 3)
  const requisitos = ["stdio.h", "main"]; 
  const termosFaltantes = requisitos.filter(termo => !text.toLowerCase().includes(termo));

  if (termosFaltantes.length > 0) {
      outputDiv.style.color = "#ff5252";
      outputDiv.innerHTML = `<b>❌ Erro de Requisitos:</b><br>
      Seu código precisa conter a estrutura básica de C.`;
      return;
  }

  if (!text) {
    outputDiv.textContent = "Digite seu código antes de enviar.";
    return;
  }

  // 2. Preparação para o envio
  submitBtn.disabled = true;
  outputDiv.style.color = "#00bcd4";
  outputDiv.textContent = "📤 Enviando código ao servidor...";

  try {
    const askResponse = await fetch(`${SERVER_URL}/ask`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        question: PROMPT_TEMPLATE + text,
        filename: "modulo5.c",
        language: "c"
      })
    });

    const askData = await askResponse.json();
    if (!askResponse.ok) throw new Error(askData.error);

    const requestId = askData.request_id;
    let status = "queued";

    while (status === "queued" || status === "processing") {
      await new Promise(r => setTimeout(r, 2000));

      const res = await fetch(`${SERVER_URL}/status/${requestId}`);
      const data = await res.json();
      status = data.status;

      if (status === "completed") {
                const respostaIA = data.answer;
                outputDiv.textContent = respostaIA;

                const match = respostaIA.match(/NOTA:\s*(\d+)/i);
                const notaFinal = match ? parseInt(match[1]) : 0;

                // Feedback visual
                if (notaFinal >= 70) {
                    outputDiv.style.color = "#4caf50";
                    outputDiv.innerHTML += `<br><br><b>✅ PRÉ-APROVADO! Enviado para revisão.</b>`;
                } else {
                    outputDiv.style.color = "#ffab40";
                    outputDiv.innerHTML += `<br><br><b>⚠️ Nota insuficiente (${notaFinal}). Tente corrigir os erros.</b>`;
                    submitBtn.disabled = false;
                }

                // 3. Envio para o Banco (Módulo 3, Desafio 1)
                fetch('../../backend/finalizar_desafio.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        desafio_id: 1, 
                        modulo_id: 5,
                        nota: notaFinal,
                        feedback_ia: respostaIA,
                        codigo: editor.getValue() 
                    })
                })
                .then(res => res.json())
                .then(resDb => console.log("DB Status:", resDb))
                .catch(err => console.error("Erro DB:", err));

                break;
            }

      if (status === "error") {
        outputDiv.style.color = "#ff5252";
        outputDiv.textContent = `❌ Erro: ${data.error}`;
        submitBtn.disabled = false;
        break;
      }

      outputDiv.textContent = `⌛ Processando (${status})...`;
    }

  } catch (err) {
    outputDiv.style.color = "#ff5252";
    outputDiv.textContent = `❌ Falha: ${err.message}`;
    submitBtn.disabled = false;
  }
});

// Salva o rascunho localmente para o Módulo 3 Desafio 1
setInterval(() => {
  if (editor) {
    localStorage.setItem('codigo_desafio_5', editor.getValue());
  }
}, 2000);

require(['vs/editor/editor.main'], function () {
  const salvo = localStorage.getItem('codigo_desafio_5');
  editor = monaco.editor.create(document.getElementById('editorContainer'), {
    value: salvo || "// Solução do Desafio 1 - Módulo 5\n",
    language: "c",
    theme: "vs-dark",
    automaticLayout: true,
    fontSize: 16,
    minimap: { enabled: false }
  });


  // 🔒 BLOQUEIO DE COLAGEM
  editor.getDomNode().addEventListener("paste", (e) => {
    e.preventDefault();
    outputDiv.textContent = "⚠️ Cola bloqueada.";
  });

  editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyV, () => {
    outputDiv.textContent = "⚠️ Ctrl+V bloqueado.";
  });

  editor.getDomNode().addEventListener("drop", (e) => {
    e.preventDefault();
    outputDiv.textContent = "⚠️ Drag and drop bloqueado.";
  });

  // 🧠 DETECÇÃO DE COLA “DISFARÇADA”
  let lastLength = editor.getValue().length;

  editor.onDidChangeModelContent(() => {
    const currentLength = editor.getValue().length;

    if (currentLength - lastLength > 20) {
      outputDiv.textContent = "⚠️ Inserção suspeita detectada.";
    }

    lastLength = currentLength;
  });

});

</script>

</body>
</html>
