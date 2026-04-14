
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Desafio 1: Cartão de Visita</title>

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
    button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    filter: grayscale(30%);
}

</style>

<!-- MONACO EDITOR -->
<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>

</head>
<body>

<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
</header>

<h1>1° Desafio:</h1>

<p style="color: black">
Você foi contratado para criar um cartão de visita digital usando apenas o que aprendeu até agora:<br>
<b>»</b> #include &lt;stdio.h&gt;<br>
<b>»</b> função main()<br>
<b>»</b> comandos printf <br>
<b>»</b> uso correto de \n para quebrar linhas <br><br>

<b>Objetivo</b><br>
Seu objetivo é escrever um programa em C que imprima exatamente seu nome, idade, linguagem de programação que está utilizando e frase favorita.
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
        <button type="submit" id="submitBtn">Corrigir Código</button>
    </div>
</form>

<!-- BLOCO OUTPUT + TÍTULO -->
<div id="outputBlock">
    <div id="outputTitle">Console</div>
    <div id="output"></div>
</div>
<div style="height:50px;"></div> 
<div class="bottom-buttons">
<a href="modulo1.php" class="btn-nav">⟵ Voltar</a>
      </div>







      
<script>
fetch('../../backend/update_time.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ modulo_id: 1 })
});

// ===== MONACO =====
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});

let editor;

// ===== ELEMENTOS =====
const SERVER_URL = "http://192.168.18.253:8080";
const outputDiv = document.getElementById("output");
const clearBtn  = document.getElementById("clearBtn");
const submitBtn = document.getElementById("submitBtn");

const PROMPT_TEMPLATE = `
### PERSONA
Você é o Corretor Automático da plataforma C-Plat. Seu tom é profissional, encorajador, mas extremamente rigoroso com a sintaxe e requisitos.

### OBJETIVO
Avaliar o desafio "Cartão de Visita" atribuindo uma nota de 0 a 100 e decidindo o status de aprovação.

### OBJETIVO DO ALUNO
Criar um cartão de visita em C contendo: Nome, Idade, Linguagem (C) e Frase Favorita.

### TABELA DE PONTUAÇÃO (TOTAL: 100 PTS)
1. **Estrutura Básica (20 pts):** Inclusão correta de '#include <stdio.h>'.
2. **Função Principal (20 pts):** Presença da função 'int main()' com chaves corretamente abertas/fechadas.
3. **Conteúdo Obrigatório (40 pts):** Exibição das 4 informações (Nome, Idade, Linguagem e Frase). Valendo 10 pts cada.
4. **Organização Visual (20 pts):** Uso correto de '\\n' para cada informação em sua própria linha.

### REGRAS DE STATUS (Média para aprovação: 70 pts)
- **Se nota == 100:** "STATUS: DESAFIO CONCLUÍDO COM EXCELÊNCIA!"
- **Se nota >= 70 e < 100:** "STATUS: DESAFIO CONCLUÍDO! (Aprovado)"
- **Se nota < 70:** "STATUS:  DESAFIO REPROVADO (Necessário atingir 70 pontos)"

### REGRAS DE RESPOSTA (Siga estritamente)
- **Primeira linha:** Exiba a "NOTA: [valor]/100" em negrito.
- **Segunda linha:** Exiba o "STATUS" conforme as regras acima.
- Se o código estiver 100% correto: Inicie com "DESAFIO CONCLUÍDO!" seguido de um resumo do que foi feito.
- Se faltar algum critério: Inicie com "OPS, ALGO ESTÁ FALTANDO..." e liste o que falta em bullet points.
- Se houver erro de sintaxe: Explique o erro de forma simples e sugira como corrigir sem entregar o código completo de bandeja.
- Se o input não for código C: Responda apenas "Por favor, envie apenas o código em C referente ao desafio do Cartão de Visita."
- Evite estruturas de tabelas para o detalhamento da pontuação.

### CÓDIGO PARA ANALISAR:
`;

// ===== LIMPAR =====
clearBtn.addEventListener("click", () => {
  editor.setValue("");
  outputDiv.textContent = "Editor limpo.";
});

// ===== SUBMIT =====
document.getElementById("codeForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  
  const text = editor.getValue().trim();

  const requisitos = ["stdio.h", "main", "printf"];
  const termosFaltantes = requisitos.filter(termo => !text.toLowerCase().includes(termo));

  if (termosFaltantes.length > 0) {
      outputDiv.innerHTML = `<b style="color: #ff5252">❌ Erro de Requisitos:</b><br>
      Seu código precisa conter: ${termosFaltantes.join(", ")}.`;
      return;
  }

  if (!text) {
    outputDiv.textContent = "Digite seu código antes de enviar.";
    return;
  }

  submitBtn.disabled = true;
  outputDiv.style.color = "#00bcd4";
  outputDiv.textContent = "📤 Enviando código ao servidor...";

  try {
    const askResponse = await fetch(`${SERVER_URL}/ask`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        question: PROMPT_TEMPLATE + text,
        filename: "modulo1.c",
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

        const regexNota = /NOTA:\s*(\d+)/i;
        const match = respostaIA.match(regexNota);
        const notaFinal = match ? parseInt(match[1]) : 0;

        if (notaFinal >= 70) {
          outputDiv.style.color = "#4caf50";
          outputDiv.innerHTML += `<br><br><b>✅ PRÉ-APROVADO! Seu código foi enviado para revisão do professor.</b>`;
        } else {
          outputDiv.style.color = "#ffab40";
          outputDiv.innerHTML += `<br><br><b>⚠️ Nota insuficiente (${notaFinal}). Tente corrigir os erros e envie novamente.</b>`;
          submitBtn.disabled = false;
        }

        fetch('/helpers/cplat/backend/finalizar_desafio.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            desafio_id: 1, 
            modulo_id: 1,
            nota: notaFinal,
            feedback_ia: respostaIA,
            codigo: editor.getValue() 
          })
        })
        .then(res => res.json())
        .then(resDb => console.log("DB Status:", resDb))
        .catch(err => console.error("Erro DB:", err));;

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

// ===== AUTOSAVE =====
setInterval(() => {
  if (editor) {
    localStorage.setItem('codigo_desafio_1', editor.getValue());
  }
}, 2000);

// ===== MONACO INIT + BLOQUEIOS =====
require(['vs/editor/editor.main'], function () {
  const salvo = localStorage.getItem('codigo_desafio_1');

  editor = monaco.editor.create(document.getElementById('editorContainer'), {
    value: salvo || "// Escreva seu código aqui...\n",
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
