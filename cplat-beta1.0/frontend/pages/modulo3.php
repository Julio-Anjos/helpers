<?php
$MODULO_ID = 3; // ID real do módulo
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
  <title>Módulo 3 - Estruturas de Decisão em C</title>
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
        h2,h3 {
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

/* Container do quiz */
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
  <source src="../assets/C-borg/animated/m3.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>


<div class="align-page">
<h1 style="font-size:30px; text-align:left;">Módulo 3: Estruturas de Condição em C</h1>
<h2>Introdução</h2>
  <p>Nos primeiros módulos, abordamos os conceitos essenciais: algoritmos, como transformá-los em código, variáveis, tipos de dados, operadores e a interação com o usuário. Até agora, os programas seguiam uma sequência fixa e linear. No entanto, no mundo real, os programas precisam tomar decisões baseadas em condições. Por exemplo, um caixa eletrônico concede ou bloqueia o acesso dependendo da senha, e em um jogo, a pontuação muda conforme as ações do jogador. Neste módulo, vamos aprender a implementar estruturas de seleção em C, permitindo que o programa tome diferentes caminhos de acordo com as condições estabelecidas.</p>

<h2>Condições lógicas: verdadeiros e falsos</h2>
  <p>Uma condição lógica é uma expressão que só pode ter dois resultados possíveis: verdadeiro ou falso.
  </br>Por exemplo:</p>
  <ul>
    <li>5 > 2 → verdadeiro.</li>
    <li>10 == 3 → falso.</li>
    <li>x != 0 → verdadeiro se x for diferente de zero.</li>
  </ul>
  <p>No C, o valor 0 é considerado falso. Qualquer outro valor é interpretado como verdadeiro.</p>
  <h3>Operadores Adicionais</h3>
  <p>São os que comparam valores:</p>
  <ul>
    <li>&lt; → menor que</li>
    <li>&lt;= → menor ou igual a</li>
    <li>> → maior que</li>
    <li>>= → maior ou igual a</li>
    <li>== → igual a</li>
    <li>!= → diferente de</li>
  </ul>
  <p>Exemplo:</p>
  <pre class="code-block"><code>
    x > y  <span class="comment">// verdadeiro se x for maior que y</span>
    idade == <span class="number"> 18</span>  <span class="comment">// verdadeiro se idade for exatamente 18</span>
    nota != <span class="number">10</span>  <span class="comment">// verdadeiro se nota não for igual a 10</span>
  </code></pre>

<h2>O comando if em C</h2>
  <p>O comando mais básico de consição em C é o if. Sua forma geral é:</p>
  <pre class="code-block"><code>
    <span class="keyword">if</span>(condicao)
    {
      <span class="comment">// comandos que serão executados se a condição for verdadeira</span>
    }
  </code></pre>
  <p>Se a condição dentro dos parênteses for verdadeira, o bloco de comandos entre chaves é executado. Se for falsa, o programa simplesmente pula o bloco e segue adiante.
  </br>Exemplo:</p>
  <pre class="code-block"><code>
    <span class="type">int</span> idade = <span class="number">20</span>;

    <span class="keyword">if</span>(idade >= <span class="number">18</span>)
    {
        <span class="function">printf</span>(<span class="string">"Você é maior de idade!\n"</span>);
    }
  </code></pre>
  <p>Aqui, como idade >= 18 é verdadeiro, a mensagem será exibida.</p>
  <h3>O if...else</h3>
  <p>Muitas vezes precisamos dizer o que fazer se for verdadeiro e o que fazer se for falso. Para isso existe o else:</p>
    <pre class="code-block"><code>
    <span class="type">int</span> idade = <span class="number">20</span>;

    <span class="keyword">if</span>(idade >= <span class="number">18</span>){
        <span class="function">printf</span>(<span class="string">"Você é maior de idade.\n"</span>);
    } <span class="keyword">else</span>{
        <span class="function">printf</span>(<span class="string">"Você é menor de idade.\n"</span>);
    }
  </code></pre>
  <p>Esse programa sempre exibirá uma das duas mensagens, dependendo da idade informada.</p>

<h2>Exemplo prático: ponto no plano cartesiano</h2>
  <p>Vamos aplicar isso em um problema clássico: identificar em que região do plano cartesiano um ponto (x, y) está localizado.</p>
  <ul>
    <li>Se x > 0 e y > 0 → o ponto está no primeiro quadrante.</li>
    <li>Se x &lt; 0 e y > 0 → está no segundo quadrante.</li>
    <li>Se x &lt; 0 e y &lt; 0 → está no terceiro quadrante.</li>
    <li>Se x > 0 e y &lt; 0 → está no quarto quadrante.</li>
    <li>Se x = 0 e y = 0 → está na origem.</li>
    <li>Se apenas x = 0 → está sobre o eixo Y.</li>
    <li>Se apenas y = 0 → está sobre o eixo X.</li>
  </ul>
  <p>Podemos representar esse raciocínio com um fluxograma ou em pseudocódigo, mas no C ele fica assim:</p>
  <pre class="code-block"><code>
    #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
        <span class="type">int</span> <span class="function">Main</span>()
        {
          <span class="type">int</span> x, y;

          <span class="function">printf</span>(<span class="string">"Digite as coordenadas x e y: "</span>);
          <span class="function">scanf</span>(<span class="string">"%d %d", </span>, &x, &y);

          <span class="keyword">if</span>(x == <span class="number">0</span> && y == <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Origem\n"</span>);
          <span class="keyword">else if</span>(x == <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Eixo Y\n"</span>);
          <span class="keyword">else if</span>(y == <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Eixo X\n"</span>);
          <span class="keyword">else if</span>(x > <span class="number">0</span> && y > <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Primeiro quadrante\n"</span>);
          <span class="keyword">else if</span>(x &lt; <span class="number">0</span> && y > <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Segundo quadrante\n"</span>);
          <span class="keyword">else if</span>(x &lt; <span class="number">0</span> && y &lt; <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Terceiro quadrante\n"</span>);
          <span class="keyword">else if</span>(x > <span class="number">0</span> && y &lt; <span class="number">0</span>)
            <span class="function">printf</span>(<span class="string">"Quarto quadrante\n"</span>);
          
          <span class="keyword">return</span> <span class="number">0</span>;
        }          
  </code></pre>
  <p>Perceba como o programa toma decisões sucessivas até chegar ao caso correto.</p>

<h2>Operadores lógicos</h2>
  <p>Muitas condições são compostas por mais de um teste. Para isso, usamos os operadores lógicos:</p>
  <ul>
    <li>&& (E lógico): verdadeiro apenas se ambas as condições forem verdadeiras.</li>
    <li>|| (OU lógico): verdadeiro se pelo menos uma das condições for verdadeira.</li>
    <li>! (NÃO lógico): inverte o valor da condição (verdadeiro vira falso, falso vira verdadeiro).</li>
  </ul>
  <p>Exemplos:</p>
  <pre class="code-block"><code>
    <span class="keyword">if</span>(idade >= <span class="number">18</span> && idade &lt;= <span class="number">100</span>)
        <span class="function">printf</span>(<span class="string">"Adulto\n"</span>);

    <span class="keyword">if</span>(nota == <span class="number">10</span> || nota == <span class="number">9</span>)
        <span class="function">printf</span>(<span class="string">"Excelente\n"</span>);

    <span class="keyword">if</span>(!(x == <span class="number">0</span>))
        <span class="function">printf</span>(<span class="string">"X não é igual a zero\n"</span>);
  </code></pre>
  <h3>Tabela verdade</h3>
  <ul>
    <li>Para &&: só é verdadeiro se os dois lados forem verdadeiros.</li>
    <li>Para ||: basta um dos lados ser verdadeiro.</li>
    <li>Para !: inverte o resultado.</li>
  </ul>
  <p>Isso é essencial para expressar situações complexas, como “o aluno passa de ano se a média for maior ou igual a 7 e a frequência for maior que 75%</p>

<h2>Decisões aninhadas</h2>
  <p>Às vezes, precisamos de um if dentro de outro if. Isso se chama seleção aninhada.
  </br>Exemplo:</p>
  <pre class="code-block"><code>
    <span class="keyword">if</span> (a > <span class="number">10</span>)
    {
      <span class="keyword">if</span> (b &lt; <span class="number">5</span>)
        <span class="function">printf</span>(<span class="string">"A é maior que 10 e B é menor que 5\n"</span>);
    }
  </code></pre>
  <p>Esse código só exibe a mensagem se ambas as condições forem verdadeiras. É equivalente a escrever:</p>
  <pre class="code-block"><code>
    <span class="keyword">if</span>(a > <span class="number">10</span> && b &lt; <span class="number">5</span>)
      <span class="function">printf</span>(<span class="string">"A é maior que 10 e B é menor que 5\n"</span>);
  </code></pre>
  <p>Aninhamento é útil, mas pode deixar o código confuso. Sempre que possível, simplifique usando operadores lógicos.</p>

<h2>Precedência em Operadores Lógicos</h2>
  <p>Assim como nos operadores númericos existe uma ordem de avaliação, no C, há ua precedência dos operadores lógicos e relacionais. Sua ordem é:</p>
  <ol>
    <li>&lt;, &lt;=, >, >=</li>
    <li>==, !=</li>
    <li>&&</li>
    <li>||</li>
  </ol>
  <pre class="code-block"><code>
    <span class="keyword">if</span> (x != <span class="number">10</span> || y > <span class="number">1</span> && y <span class="number">10</span>)
  </code></pre>
  <p>primeiro são avaliados os >, &lt;, depois o !=, em seguida o && e por último o ||.
  </br>Para evitar confusões e tornar o código mais claro, recomenda-se sempre usar parênteses:</p>
    <pre class="code-block"><code>
  <span class="keyword">if</span> ( (x != <span class="number">10</span>) || ( (y > <span class="number">1</span>) && (y <span class="number">10</span>) ) )
  </code></pre>
  <p>Isso facilita a leitura, tanto para você quanto para qualquer outra pessoa que veja o código.</p>

<h2>Exercício para Fixação</h2>

  <div class="quiz-container">
        <h1>Quiz - Módulo 3</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>

<h2>Conclusão</h2>
  <p>Neste módulo, demos um passo fundamental: aprendemos a fazer nossos programas tomarem decisões. Vimos como usar o comando if e if...else, como combinar condições com operadores lógicos, como organizar seleções aninhadas e como cuidar da precedência entre operadores. Agora, nossos programas já não são mais lineares. Eles podem responder de maneiras diferentes dependendo das entradas do usuário. Essa é a essência da programação condicional. No próximo módulo, vamos expandir ainda mais a capacidade dos nossos programas, aprendendo a repetir instruções várias vezes com as estruturas de repetição. Isso permitirá automatizar tarefas e processar grandes quantidades de dados com poucas linhas de código.</p>
<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio3.php" class="btn-nav">Desafio →</a>
      </div></div>
<script>
const MODULO_ID = <?= $MODULO_ID ?>; // na parte do <script> do final do HTML

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
        question: "No C, qual valor é considerado falso em uma condição?",
        options: [
            "O número -1",
            "O número 1",
            "O número 0"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual operador é usado para testar se um valor é maior ou igual a outro?",
        options: [
            ">",
            "<=",
            ">="
        ],
        correctAnswer: 2
    },
    {
        question: "O que o comando `if` faz em C?",
        options: [
            "Repete um bloco de código várias vezes",
            "Executa um bloco de código apenas se uma condição for verdadeira",
            "Declara uma variável"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual é a sintaxe correta para um `if...else`?",
        options: [
            "if (condicao) {comandos} elseif {comandos}",
            "if (condicao) {comandos} else {comandos}",
            "if {comandos} else (condicao) {comandos}"
        ],
        correctAnswer: 1
    },
    {
        question: "No exemplo do plano cartesiano, qual quadrante é identificado por `x > 0 && y < 0`?",
        options: [
            "Primeiro quadrante",
            "Segundo quadrante",
            "Quarto quadrante"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual operador lógico retorna verdadeiro apenas se ambas as condições forem verdadeiras?",
        options: [
            "||",
            "!",
            "&&"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual é a precedência CORRETA dos operadores lógicos e relacionais em C?",
        options: [
            "&&, ||, ==, >",
            ">, ==, &&, ||",
            "||, &&, >, =="
        ],
        correctAnswer: 1
    },
    {
        question: "O que é uma seleção aninhada?",
        options: [
            "Um `if` dentro de outro `if`",
            "Vários `else if` em sequência",
            "Um `if` com múltiplas condições usando `&&`"
        ],
        correctAnswer: 0
    },
    {
        question: "No exemplo: `if(!(x == 0))`, quando a mensagem 'X não é igual a zero' será exibida?",
        options: [
            "Quando x for igual a 0",
            "Quando x for diferente de 0",
            "Sempre"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual é a vantagem de usar parênteses em condições complexas?",
        options: [
            "Torna o código mais rápido",
            "Facilita a leitura e evita erros de precedência",
            "Reduz o número de linhas de código"
        ],
        correctAnswer: 1
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
