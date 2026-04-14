<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Desafio 10: Monitor de Consumo de Energia Elétrica</title>

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

<h1>10° Desafio:</h1>

<p style="color: black">
Você foi contratado para desenvolver um sistema simples de <b>monitoramento de consumo de energia elétrica</b> para um pequeno laboratório de informática. O objetivo é registrar, atualizar e analisar o consumo diário de energia dos equipamentos, mantendo as informações sempre atualizadas. Seu programa deverá permitir o gerenciamento eficiente desses dados por meio de <b>funções modularizadas</b>, utilizando <b>ponteiros e passagem por referência</b> para modificar diretamente as informações armazenadas.<br><br>

O laboratório trabalha com diferentes tipos de operações relacionadas ao consumo de energia:<br><br>

<b>1. Registro de consumo:</b> Quando o consumo de um equipamento é atualizado, o novo valor deve ser registrado no sistema.<br>
<b>2. Consulta de consumo:</b> A qualquer momento, é possível consultar o consumo atual de um equipamento específico.<br>
<b>3. Análise do consumo:</b> O sistema deve permitir o cálculo do consumo total e do consumo médio dos equipamentos cadastrados.<br><br>

Você terá um conjunto fixo com <b>5 equipamentos diferentes</b>. Para cada equipamento, será necessário registrar um nome e um valor de consumo inicial (em kWh), além de permitir que esse valor seja atualizado ao longo da execução do programa. O sistema deverá ser modularizado, ou seja, cada operação deverá ser realizada por funções específicas.<br><br>

<b>Checklist de Implementação</b><br>

<b>1.</b> Definição da Estrutura de Dados: Crie uma estrutura <b>"Equipamento"</b> que tenha os seguintes campos: <b>"nome"</b> (string ou array de char) e <b>"consumo"</b> (float).<br>

<b>2.</b> Funções:<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>registrarConsumo( )</b>: Recebe o vetor de equipamentos, o nome do equipamento e o novo valor de consumo, atualizando o consumo correspondente diretamente no vetor utilizando ponteiros.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>consultarConsumo( )</b>: Recebe o vetor de equipamentos e o nome do equipamento, exibindo o consumo atual.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>calcularConsumoTotal( )</b>: Recebe o vetor de equipamentos e um ponteiro para uma variável float, calculando e armazenando o consumo total por passagem por referência.<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>calcularConsumoMedio( )</b>: Recebe o vetor de equipamentos, o tamanho do vetor e um ponteiro para float, calculando e armazenando o consumo médio.<br>

<b>3.</b> Função Principal: Crie um menu interativo onde o usuário poderá: Atualizar o consumo de um equipamento; Consultar o consumo de um equipamento; Exibir o consumo total; Exibir o consumo médio; Encerrar o programa.<br>

<b>4.</b> Validação de Entrada: Garanta que o programa não aceite valores de consumo negativos, valide a existência do equipamento informado e trate entradas inválidas de forma adequada.<br><br>

<b>Objetivo</b><br>
Ao final, o programa deve ser capaz de gerenciar e analisar o consumo de energia de um conjunto de equipamentos, demonstrando domínio na utilização de <b>estruturas</b>, <b>funções</b> e, principalmente, de <b>ponteiros e passagem por referência</b> para modificação de dados fora do escopo da função, garantindo um código organizado, eficiente e seguro.
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
<a href="modulo10.php" class="btn-nav">⟵ Voltar</a>
      </div>






<script>
// Atualiza o tempo de estudo para o Módulo 3
fetch('../../backend/update_time.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ modulo_id: 10 })
});

// ===== MONACO EDITOR =====
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});

let editor;

// ===== ELEMENTOS =====
const SERVER_URL = "http://192.168.18.253:8080";
const outputDiv = document.getElementById("output");
const clearBtn  = document.getElementById("clearBtn");
const submitBtn = document.querySelector('button[type="submit"]');

// O Prompt Template será configurado posteriormente conforme combinado
const PROMPT_TEMPLATE = `
### PERSONA
Você é o Corretor Automático da plataforma C-Plat. Seu tom é profissional e técnico.

### OBJETIVO
Avaliar o desafio "Monitoramento de Energia" (Módulo 6 - Desafio 2). Nota: 0 a 100.

### REQUISITOS TÉCNICOS:
1. Definição da 'struct Equipamento' (nome e consumo float).
2. Uso de **ponteiros e passagem por referência** nas funções calcularConsumoTotal e calcularConsumoMedio (os resultados devem ser armazenados via ponteiro, não retornados por 'return').
3. Função registrarConsumo para atualizar valores no vetor.
4. Validação: Não aceitar valores de consumo negativos.
5. Menu interativo com as opções solicitadas.

### TABELA DE PONTUAÇÃO (TOTAL: 100 PTS)
1. **Uso de Ponteiros/Referência (40 pts):** As funções de cálculo devem receber um ponteiro float para salvar o resultado. Se usarem 'return' em vez de ponteiro, perdem 30 pts.
2. **Modularização e Estrutura (25 pts):** Definição da struct e criação das 4 funções obrigatórias.
3. **Lógica de Busca e Atualização (20 pts):** Localizar o equipamento pelo nome e atualizar o consumo corretamente.
4. **Validação e Interface (15 pts):** Impedir consumos negativos e manter o menu funcional.

### REGRAS DE STATUS (Média para aprovação: 70 pts)
- **Se nota == 100:** "STATUS: DESAFIO CONCLUÍDO COM EXCELÊNCIA!"
- **Se nota >= 70 e < 100:** "STATUS: DESAFIO CONCLUÍDO! (Aprovado)"
- **Se nota < 70:** "STATUS: DESAFIO REPROVADO"

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
        filename: "modulo10.c",
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
                        modulo_id: 10,
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
    localStorage.setItem('codigo_desafio_10', editor.getValue());
  }
}, 2000);

require(['vs/editor/editor.main'], function () {
  const salvo = localStorage.getItem('codigo_desafio_10');
  editor = monaco.editor.create(document.getElementById('editorContainer'), {
    value: salvo || "// Solução do Desafio 1 - Módulo 3\n",
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
