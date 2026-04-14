<?php
$MODULO_ID = 8; // ID real do módulo
session_start();
require '../../backend/init_progresso_modulo.php';

// VERIFICAÇÃO DE AUTENTICAÇÃO - MOSTRA ALERTA E REDIRECIONA
if (!isset($_SESSION['usuario'])) {
    // Armazena a URL atual para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Acesso Negado</title>
        <script>
            alert('Você precisa estar logado para acessar este módulo!');
            window.location.href = 'bemvindos.php';
        </script>
    </head>
    <body>
        <p>Redirecionando para o login...</p>
    </body>
    </html>
    <?php
    exit;
}

$logado = true; // Agora sabemos que está logado
$email_usuario = $_SESSION['usuario'];

// Verificar se o usuario_id está na sessão - SE NÃO ESTIVER, PRECISARÁ BUSCAR DO BANCO
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Se não tiver o usuario_id mas o usuário está logado, você precisa buscá-lo
if (!$usuario_id) {
    require '../../backend/conexao.php';
    
    $stmt = $pdo->prepare("SELECT id FROM Tabela_usuarios WHERE email = ?");
    $stmt->execute([$email_usuario]);
    $usuario = $stmt->fetch();
    
    $usuario_id = $usuario ? $usuario['id'] : null;
    $_SESSION['usuario_id'] = $usuario_id;
}

// Captura mensagens de login/cadastro
$msg_login = $_SESSION['msg_login'] ?? '';
$msg_cadastro = $_SESSION['msg_cadastro'] ?? '';
unset($_SESSION['msg_login'], $_SESSION['msg_cadastro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Módulo 8 - Arranjos multidimensionais</title>

    <style>
          html{
            margin: 0;
        }
        body{
            margin: 0;
            background-color: #eeeeee;
            font-family:sans-serif;
        }
        .align-page{
            margin-right: 30%;
        }
        .helper {
            position: fixed;
            top: 50%;
            left: 73%;
            transform: translateY(-50%);
            background-color: black;
            padding: 300px 200px; /* controla o “respiro” ao redor do vídeo */
            border: 2px solid #00bcd4; /* opcional: reforça a borda azul */
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .video-centralizado {
        padding: 50px 0px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        height: 600px;
        width: 400px;
        z-index: 10;
        transition: opacity 1s ease;
        display: block;
        }

        #imagemFinal { 
            width:300px;
            height:295px; 
            opacity:0; 
            display:none; 
            transition:opacity 1s ease; }
        .topbar {
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

        code {
            font-size: 20px;
            
        }
        span {
            font-size: 20px;
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

        li {
          font-size: 27px;
          text-align: justify;
          color: #000022;
          padding: 0 20px;
        }

        /* Título do Quiz */
        h1 {
            padding: 0 15px;
            text-align: center;
            color: #00bcd4;
            font-size: 50px;
            font-weight: bold;
            margin-bottom: 40px;
            letter-spacing: 1px;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.8);
        }
        h2, h3 {
            color: #00bcd4;
            padding: 0px 30px;

        }

        .code-block {
            background-color: #1e1e1e; /* fundo escuro estilo VS Code */
            color: #d4d4d4;           /* cor padrão do texto */
            padding: 15px;
            border-radius: 8px;
            font-family: "Fira Code", "Courier New", monospace;
            font-size: 14px;
            line-height: 1.5;
            overflow-x: auto;         /* rolagem horizontal se o código for longo */
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        .type {
            color: #4ec9b0;      /* um azul-esverdeado elegante */
            font-weight: bold;   /* destaque extra */
        }
        .parameter { color: #9cdcfe; }   /* azul claro para parâmetros */
        .property  { color: #dcdcaa; }   /* amarelinho para atributos */

/* exemplo de destaque de sintaxe simples */
        .code-block .keyword { color: #569cd6; }   /* azul para palavras-chave */
        .code-block .string { color: #ce9178; }    /* marrom-avermelhado para strings */
        .code-block .function { color: #dcdcaa; }  /* amarelo para funções */
        .code-block .number { color: #c0e9ff}
         
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
.quiz-container {
    background-color: #1e1e1e;
    border-radius: 15px;
    width: 80%;  /* Aumentei o tamanho da largura */
    max-width: 900px;  /* Aumentei o max-width */
    padding: 40px;  /* Aumentei o padding interno */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    text-align: center;
    transition: transform 0.5s ease;
    margin-right: auto;
    margin-left: auto;
  }
  
/* Estilos das perguntas */
.question {
    font-size: 28px;
    margin-bottom: 30px;
    font-weight: 500;
}
/* Estilos das opções de resposta */
.options button {
    background-color: #00b0ff;
    border: none;
    padding: 15px 30px;
    margin: 15px;
    font-size: 22px;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.options button:hover {
    background-color: #0088cc;
    transform: translateY(-5px);
}

/* Feedback sobre a resposta */
.feedback {
    text-align: center;
    font-size: 24px;
    margin-top: 20px;
    padding: 10px;
    border-radius: 10px;
}

.correct {
    color: #32cd32; /* Verde para respostas corretas */
    background-color: rgba(50, 205, 50, 0.1);
}

.wrong {
    color: #ff6347; /* Vermelho para respostas erradas */
    background-color: rgba(255, 99, 71, 0.1);
}

/* Decoração com elementos de programação */
.programming-icon {
    font-size: 24px;
    color: #00b0ff;
    text-align: center;
    margin-top: 40px;
}

.programming-icon i {
    margin-right: 10px;
}

/* Animações de feedback */
.feedback {
    opacity: 0;
    animation: fadeIn 1s forwards;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}   
  
</style>
</head>
<body style="font-family:sans-serif;">
<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
</header>

    <div class="helper">
      <video id="introVideo" class="video-centralizado">
        <source src="../assets/C-borg/animated/m8.mp4" type="video/quicktime">
        Seu navegador não suporta vídeos HTML5.
    </video>
      <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>


<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 8: Do Linear ao Tabular - Explorando Arranjos Multidimensionais (Matrizes) em C</h1>
    <h2>Introdução</h2>
    <p>Olá! Nos últimos dois módulos, fizemos avanços significativos. Primeiro, aprendemos a agrupar dados homogêneos em vetores, deixando para trás a ideia de variáveis isoladas e começando a trabalhar com coleções de dados. Em seguida, aplicamos essa ideia ao mundo do texto, entendendo que as strings são, na verdade, vetores de caracteres com uma característica importante: o terminador \0. Hoje, vamos levar esse conceito ainda mais longe. E se precisarmos de mais de uma dimensão? E se os nossos dados naturalmente se organizam em linhas e colunas, como uma planilha, uma imagem digital ou até um tabuleiro de jogo? É aí que entram os arranjos multidimensionais, especialmente as matrizes (arranjos bidimensionais). Vamos dar um passo à frente, deixando a rua (o vetor) e entrando no quarteirão (a matriz), para trabalhar com dados mais complexos e organizados de maneira mais sofisticada.</p>
    <h2>Da Fila à Grade: A Necessidade de uma Segunda Dimensão</h2>
      <p>Pense em um vetor como uma longa fila única de cadeiras em um auditório. Cada cadeira tem um número sequencial (o índice). Agora, imagine um auditório com fileiras e colunas de cadeiras. Para encontrar um assento específico, você precisa de duas informações: o número da fileira (linha) e o número da cadeira dentro da fileira (coluna). Uma matriz é exatamente isso: uma estrutura de dados organizada em linhas e colunas. Por exemplo, suponha que precisamos armazenar as notas de 5 alunos em 2 avaliações diferentes. Com vetores, poderíamos ter:</p>
      <ul>
        <li>float nota_av1[5]; para a primeira avaliação.</li>
        <li>float nota_av2[5]; para a segunda avaliação.</li>
      </ul>
      <p>Isso funciona, mas é desorganizado. E se fossem 5 avaliações? Teríamos 5 vetores separados. Uma matriz resolve isso elegantemente, agrupando todas as notas em uma única estrutura:</p>
      <ul>
    <pre class="code-block"><code>
      <span class="type">float</span> notas[<span class="number">5</span>][<span class="number">2</span>];    <span class="comment">// 5 linhas (alunos) x 2 colunas (avaliações)</span>
      </ul>
    </code></pre>
      <p>Nesta matriz notas:</p>
      <ul>
        <li>notas[0][0] é a nota da AV1 do primeiro aluno.</li>
        <li>notas[0][1] é a nota da AV2 do primeiro aluno.</li>
        <li>notas[1][0] é a nota da AV1 do segundo aluno.</li>
      </ul>
      <p>E assim por diante. Toda a informação relacionada está contida em um único "recipiente" lógico. A declaração geral é tipo nome_matriz[numero_linhas][numero_colunas];.</p>
    
    <h2>Inicializando e Percorrendo Matrizes: A Dança dos Laços Aninhados</h2>
      <p>Assim como percorremos um vetor com um loop for, percorrer uma matriz exige laços aninhados. Geralmente, usamos um loop externo para iterar pelas linhas e um loop interno para iterar pelas colunas de cada linha. É como ler um texto: você vai linha por linha, e em cada linha, palavra por palavra (ou coluna por coluna). Vamos preencher nossa matriz de notas:</p>
      <ul>
    <pre class="code-block"><code>
      #<span class="keyword">define</span> NUM_ALUNOS <span class="number">5</span>
      #<span class="keyword">define</span> NUM_NOTAS <span class="number">2</span>
      <span class="type">float</span> notas[NUM_ALUNOS][NUM_NOTAS];
      <span class="type">int</span> i, j;

      <span class="keyword">for</span> (i = <span class="number">0</span>; i &lt; NUM_ALUNOS; i++) {    <span class="comment">// Loop externo: para cada ALUNO (linha)</span>
          <span class="function">printf</span>(<span class="string">"Aluno %d:\n"</span>, i+1);
          <span class="keyword">for</span> (j = <span class="number">0</span>; j &lt; NUM_NOTAS; j++) {    <span class="comment">// Loop interno: para cada NOTA (coluna) desse aluno</span>
              <span class="function">printf</span>(<span class="string">"Digite a nota %d: "</span>, j+1);
              <span class="function">scanf</span>(<span class="string">"%f"</span>, &amp;notas[i][j]);
          }
      }
    </code></pre>
      </ul>
      <p>A chave aqui é entender a hierarquia: Para cada valor de i (cada aluno), o loop interno j executa completamente (para as duas notas). Primeiro, i=0 (primeiro aluno) e j varia de 0 a 1 (primeira e segunda nota). Só então i incrementa para 1, e o processo se repete. Podemos inicializar uma matriz na declaração, usando chaves aninhadas para maior clareza:</p>
      <ul>
    <pre class="code-block"><code>
      <span class="type">int</span> matriz_exemplo[<span class="number">2</span>][<span class="number">3</span>] = { {<span class="number">10</span>, <span class="number">20</span>, <span class="number">30</span>},    <span class="comment">// Valores da linha 0</span>
                                   {<span class="number">40</span>, <span class="number">50</span>, <span class="number">60</span>} }    <span class="comment">// Valores da linha 1</span>
      </ul>
    </code></pre>
    <h2>Operações Comuns com Matrizes: Extraindo Significado dos Dados</h2>
      <p>A verdadeira utilidade das matrizes aparece quando começamos a processá-las. Vamos revisitar alguns exemplos dos seus materiais, explicando a lógica por trás deles.</p>
        <h3>Encontrar o Maior Valor de Cada Linha:</h3>
        <p>A ideia é isolar cada linha e tratar seus elementos como um vetor independente.</p>
    <pre class="code-block"><code>
      <span class="type">int</span> maior;
      <span class="keyword">for</span> (i = <span class="number">0</span>; i &lt; NUM_ALUNOS; i++) {
          maior = notas[i][<span class="number">0</span>];    <span class="comment">// Assume que a primeira nota do aluno 'i' é a maior</span>
          <span class="keyword">for</span> (j = <span class="number">0</span>; j &lt; NUM_NOTAS; j++) {   <span class="comment">// Começa de 1 porque já usamos o índice 0</span>
              <span class="keyword">if</span> (notas[i][j] > maior) {
                  maior = notas[i][j];
              }
          }
          <span class="function">printf</span>(<span class="string">"Maior nota do aluno %d: %d\n"</span>, i+1, maior);
      }
    </code></pre>
        <p>Para cada aluno (linha), nós "esquecemos" das outras linhas e focamos apenas em comparar as notas dele entre si.</p>
        <h3>Calcular a Média de Cada Coluna:</h3>
        <p>Agora, a perspectiva muda. Queremos analisar como foi cada avaliação (coluna) considerando todos os alunos.</p>
    <pre class="code-block"><code>
      <span class="type">float</span> soma, media;
      <span class="keyword">for</span> (j = <span class="number">0</span>; j &lt; NUM_NOTAS; j++) {   <span class="comment">// Loop externo: para cada AVALIAÇÃO (coluna)</span>
          soma = <span class="number">0</span>
          <span class="keyword">for</span> (i = <span class="number">0</span>; i &lt; NUM_ALUNOS; i++) {
                soma += notas[i][j];
          }
          media = soma / NUM_ALUNOS;
          <span class="function">printf</span>(<span class="string">"Media da avaliação %d: %.2f\n"</span>, j+1, media);
      }
      </code></pre>
      <p>Perceba a inversão: o loop externo agora é sobre as colunas (j), e o interno sobre as linhas (i). Isso ilustra a flexibilidade do acesso aos dados.</p>
      
    <h2>Cuidados e Boas Práticas: Acessando o Mundo Tabular com Segurança</h2>
    <ol>
      <li>Índices Começam em Zero: Assim como nos vetores, a primeira linha é a 0 e a primeira coluna é a 0. matriz[0][0] é o canto superior esquerdo.</li>
      <li>Limites são Sagrados: Acessar matriz[5][2] em uma matriz declarada como [5][2] é um erro grave (estouro de limite). Os índices válidos vão de [0][0] a [4][1].</li>
      <li>Clareza na Lógica: Use nomes de variáveis significativos para os índices. Em vez de i e j, linha e coluna ou aluno e prova tornam o código muito mais legível.</li>
      <li>A Ordem dos Loops Importa: Percorrer uma matriz "por linhas" (linha externa, coluna interna) é geralmente mais eficiente devido à forma como a memória do computador é organizada (armazenamento row-major).</li>
    </ol>
    <h2>Para Além da Segunda Dimensão: Um Vislumbre do Cubo de Dados</h2>
    <p>A linguagem C não para nas duas dimensões. Podemos ter arranjos de três, quatro ou mais dimensões. Um arranjo tridimensional, por exemplo, pode ser pensado como um cubo de dados ou uma pilha de matrizes. O exemplo clássico do material é armazenar notas de alunos de várias turmas:</p>
    <ul>
    <pre class="code-block"><code>
      #<span class="keyword">define</span> NUM_TURMAS <span class="number">2</span>
      #<span class="keyword">define</span> NUM_ALUNOS <span class="number">5</span>
      #<span class="keyword">define</span> NUM_NOTAS <span class="number">2</span>
      <span class="type">float</span> boletim[NUM_TURMAS][NUM_ALUNOS][NUM_NOTAS];
    </code></pre>
    </ul>
    <p>Para acessar a segunda nota do terceiro aluno da primeira turma, usamos: boletim[0][2][1]. Percorrer uma estrutura dessas exigiria três loops aninhados. Embora menos comum, é poderoso para modelar problemas complexos. No próximo módulo, provavelmente consolidaremos esse conhecimento, talvez entrando em tópicos como busca e ordenação em vetores, ou então começaremos a explorar como organizar nosso código em funções, que é o próximo grande salto para escrever programas bem estruturados e modulares. Por hoje, o exercício é mentalizar a estrutura de grade. Desenhe matrizes no papel. Pratique a lógica dos loops aninhados, entendendo que o loop interno é executado integralmente para cada passo do loop externo. Quando você conseguir enxergar mentalmente esse processo, as matrizes terão deixado de ser um conceito abstrato para se tornar uma ferramenta concreta e extremamente útil.</p>
  <h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 8</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>
<p style="text-align:center">Até a próxima!</p>
<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio8.php" class="btn-nav">Desafio →</a>
      </div>
    </div>
</body>
    <script>
const MODULO_ID = <?= $MODULO_ID ?>;

const video = document.getElementById('introVideo');
const imagem = document.getElementById('imagemFinal');

// estado inicial
video.style.opacity = 1;
video.style.display = 'block';
video.style.zIndex = 1;

imagem.style.opacity = 0;
imagem.style.display = 'block';
imagem.style.zIndex = 0;

// quando o vídeo termina
video.addEventListener('ended', () => {
  const fadeTime = 600; // milissegundos

  video.style.transition = `opacity ${fadeTime}ms ease`;
  imagem.style.transition = `opacity ${fadeTime}ms ease`;

  // começa o fade
  video.style.opacity = 0;
  imagem.style.opacity = 1;
  imagem.style.zIndex = 2;

  // remove o vídeo logo depois
  setTimeout(() => {
    video.pause();
    video.style.display = 'none';
  }, fadeTime);
});

video.play();

const questions = [
    {
        question: "Como uma matriz bidimensional é declarada em C?",
        options: [
            "tipo nome[tamanho];",
            "tipo nome[colunas][linhas];",
            "tipo nome[linhas][colunas];"
        ],
        correctAnswer: 2
    },
    {
        question: "Para percorrer completamente uma matriz, geralmente precisamos de:",
        options: [
            "Dois loops for aninhados (um dentro do outro)",
            "Três loops for independentes",
            "Um único loop for"
        ],
        correctAnswer: 0
    },
    {
        question: "Em uma matriz declarada como 'int dados[3][4];', qual é o índice do último elemento?",
        options: [
            "dados[4][3]",
            "dados[2][3]",
            "dados[3][4]"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual é a melhor analogia para uma matriz bidimensional?",
        options: [
            "Um livro com capítulos",
            "Uma fila de pessoas",
            "Uma planilha com linhas e colunas"
        ],
        correctAnswer: 2
    },
    {
        question: "No exemplo das notas dos alunos, por que usamos laços aninhados?",
        options: [
            "Porque C exige essa sintaxe para matrizes",
            "Para tornar o programa mais rápido",
            "Para acessar cada elemento da matriz (cada aluno e cada nota)"
        ],
        correctAnswer: 2
    },
    {
        question: "Quando calculamos a média de cada coluna da matriz, como organizamos os loops?",
        options: [
            "Loop externo nas colunas, interno nas linhas",
            "Dois loops independentes sem aninhamento",
            "Loop externo nas linhas, interno nas colunas"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual é o principal cuidado ao acessar elementos de uma matriz?",
        options: [
            "Usar sempre índices negativos",
            "Inicializar sempre com valores decimais",
            "Respeitar os limites declarados para evitar estouro"
        ],
        correctAnswer: 2
    },
    {
        question: "O que representa uma matriz tridimensional como 'float dados[2][3][4]'?",
        options: [
            "Uma string multidimensional",
            "Um cubo de dados (ou pilha de matrizes)",
            "Uma matriz com 2 linhas e 3 colunas"
        ],
        correctAnswer: 1
    },
    {
        question: "Por que é mais eficiente percorrer uma matriz 'por linhas' (linha externa, coluna interna)?",
        options: [
            "Porque os compiladores C otimizam apenas esse padrão",
            "Porque a memória do computador armazena matrizes em ordem row-major",
            "Porque é mais fácil de programar"
        ],
        correctAnswer: 1
    },
    {
        question: "Na inicialização 'int matriz[2][3] = {{1,2,3},{4,5,6}}', qual é o valor de matriz[1][2]?",
        options: [
            "5",
            "3",
            "6"
        ],
        correctAnswer: 2
    }
];

let currentQuestionIndex = 0;
let correctAnswers = 0;
const startTime = Date.now();

function displayQuestion() {
    const question = questions[currentQuestionIndex];
    document.querySelector(".question").innerText = question.question;
    const buttons = document.querySelectorAll(".options button");

    question.options.forEach((option, index) => {
        buttons[index].innerText = option;
    });

    const feedback = document.querySelector(".feedback");
    feedback.innerText = "";
    feedback.className = "feedback";
}

function checkAnswer(selectedIndex) {
    const question = questions[currentQuestionIndex];
    const feedback = document.querySelector(".feedback");

    if (selectedIndex === question.correctAnswer) {
        feedback.innerText = "Resposta Correta!";
        feedback.className = "feedback correct";
        correctAnswers++;
    } else {
        feedback.innerText = "Resposta Errada!";
        feedback.className = "feedback wrong";
    }

    setTimeout(() => {
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            displayQuestion();
        } else {
            // Quiz finalizado
            document.querySelector(".question").innerText =
                `Quiz Finalizado! Você acertou ${correctAnswers} de ${questions.length} perguntas.`;
            document.querySelector(".options").style.display = "none";
            document.querySelector(".feedback").style.display = "none";

            const endTime = Date.now();
            const tempoGasto = Math.floor((endTime - startTime) / 1000);

            // Envio de acertos
            console.log('Quiz finalizado, enviando dados...');
            fetch('../../backend/finalizar_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    modulo_id: MODULO_ID,
                    acertos: correctAnswers
                })
            });

            // Envio de tempo
            console.log('Quiz finalizado, enviando dados2...');
            fetch('../../backend/update_tempo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    modulo_id: MODULO_ID,
                    tempo: tempoGasto
                })
            });
        }
    }, 1500);
}

// Inicializa o quiz
displayQuestion();
</script>
</html>
