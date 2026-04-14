<?php
$MODULO_ID = 1; // ID real do módulo
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
  <title>Módulo 1 - Primeiros Passos em C</title>
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

        li,l {
          font-size: 27px;
          text-align: justify;
          color: #000022;
          padding: 0 20px;
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
        h2 {
            color: #00bcd4;
            padding: 0px 30px;

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


</style>
</head>

<body>

<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
  <div class="right-group">
  </div>
</header>



<div class="helper">
<video id="introVideo" class="video-centralizado">
  <source src="../assets/C-borg/animated/m1.mov" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
<h1 style="font-size:30px; text-align:left;">
  Módulo 1 - Primeiros Passos em Programação
</h1>
  <h2>O Que São Algoritmos?</h2>
    <p>Para entender programação, primeiro precisamos conhecer o conceito de algoritmo: uma sequência de passos bem definidos para alcançar um objetivo. Por exemplo, uma receita de bolo indica os ingredientes e a ordem das ações — misturar, assar e, ao final, obter o bolo pronto. Trocar o pneu de um carro também é um algoritmo: levantar o carro, retirar a roda furada, colocar o estepe e recolocar os parafusos. Em ambos os casos, há início, sequência lógica e fim. Algoritmos estão em nosso dia a dia; programação apenas traduz essas sequências para uma linguagem que o computador entenda e execute.</p>

  <h2>Do Algoritmo Ao Programa</h2>
    <p>Embora algoritmos façam parte do nosso dia a dia, precisamos de linguagens de programação porque o computador só entende linguagem de máquina — combinações de 0s e 1s. Escrever diretamente nesse código seria quase impossível para humanos. Linguagens como C, Python e Java permitem descrever algoritmos de forma mais próxima do nosso raciocínio. Ainda assim, o computador não entende diretamente essas linguagens, então usamos compiladores: programas que traduzem o código-fonte para linguagem de máquina, como um tradutor que converte português para japonês.</p>

  <h2>Como Os Programas São Organizados</h2>
    <p>Existem diferentes formas de organizar programas, conhecidas como paradigmas de programação. No início do nosso aprendizado, vamos nos concentrar no paradigma imperativo (também chamado de procedural). Nesse paradigma, um programa é construído como uma sequência de instruções executadas passo a passo, em ordem lógica, até que se chegue a um resultado. Isso combina perfeitamente com a ideia de algoritmos: você descreve o que deve ser feito, na ordem em que deve ser feito, e o computador executa exatamente dessa forma.</p>

  <h2>O Primeiro Contato Com A linguagem C</h2>
    <p>Chegou a hora de olhar para a estrutura de um programa em C. Todo programa nessa linguagem segue um formato básico, que é quase como uma “receita” obrigatória. Veja o exemplo:</p>
        <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
        <span class="type">int</span> <span class="function">Main</span>()
        {
          <span class="keyword">return</span> <span class="number">0</span>;
        }
        </code></pre>
    <p> Esse é o esqueleto de qualquer programa em C. Vamos entender cada parte:</p>
    <ul>
      <li>A linha #include <stdio.h> serve para incluir uma biblioteca com funções de entrada e saída. Isso significa que, se quisermos mostrar algo na tela ou ler algo do teclado, precisamos dessa biblioteca. É como se estivéssemos abrindo uma caixa de ferramentas antes de começar um trabalho.</li>
      <li>A linha int main() define a função principal do programa. Todo programa em C começa a ser executado por essa função. É o ponto de partida, o “início da jornada”.</li>
      <li>As chaves { } delimitam o corpo do programa. Tudo o que está dentro delas será executado.</li>
      <li>Por fim, return 0; indica que o programa terminou com sucesso. Esse retorno é uma convenção, um jeito do programa avisar ao sistema operacional que deu tudo certo.</li>
    </ul>

<h2> O Clássico Hello World</h2>
    <p>Agora, vamos escrever o programa mais famoso do mundo da programação: o Hello World.</p>
      <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
        <span class="type">int</span> <span class="function">Main</span>()
        {
          <span class="function">printf</span>(<span class="string">"Hello World"</span>);
          <span class="keyword">return</span> <span class="number">0</span>;
        }
        </code></pre>
    <p>Esse pequeno programa tem apenas uma função: mostrar a frase “Hello, World!” na tela. Embora pareça simples, ele é simbólico porque representa o primeiro contato de qualquer pessoa com a programação: você escreve uma instrução, manda o computador executar, e ele obedece. Na linha printf("Hello, World!\n");, usamos a função printf para imprimir uma mensagem. O \n dentro das aspas indica uma quebra de linha, ou seja, é como se tivéssemos apertado a tecla Enter. Rodar esse programa e ver o texto aparecendo na tela pode parecer trivial, mas é o primeiro passo para entender que estamos, de fato, conversando com a máquina.</p>

<h2>Experimentos Com Printf</h2>
    <p>Para consolidar o aprendizado, é importante brincar com o printf. Vamos ver um exemplo mais elaborado:</p>
        <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
        <span class="type">int</span> <span class="function">Main</span>()
        {
          <span class="function">printf</span>(<span class="string">"Meu Nome É Ana\n"</span>);
          <span class="function">printf</span>(<span class="string">"Estou Aprendendo A Programar Em C\n"</span>);
          <span class="function">printf</span>(<span class="string">"E Este É Meu Primeiro Programa!\n"</span>);
          <span class="keyword">return</span> <span class="number">0</span>;
        }
        </code></pre>
    <p>A saída será:</p>
        <pre class="code-block"><code>
Meu nome é Ana
Estou aprendendo a programar em C
E este é meu primeiro programa!
        </code></pre>
    <p>Aqui, usamos "\n" para garantir que cada mensagem apareça em uma linha diferente. Sem o \n, todas as frases ficariam grudadas em uma única linha, o que deixaria a saída desorganizada. Essa é uma das primeiras lições práticas: aprender a controlar a saída na tela é fundamental para que possamos exibir resultados de forma legível.</p>

<h2> Do Código Ao Executável</h2>
    <p>Agora você deve estar se perguntando: como esse texto que escrevemos vira um programa de verdade? O processo acontece em etapas:</p>
    <ol>
        <li>Primeiro, você escreve o código em um arquivo de texto, que normalmente tem a extensão .c. Esse é o chamado código-fonte.</li>
        <li>Em seguida, o compilador entra em ação e transforma esse código em um formato intermediário chamado código objeto, que ainda não é diretamente executável.</li>
        <li>Por fim, o compilador gera o arquivo executável, que é o programa pronto para rodar no seu computador.</li>
    </ol>
    <p>No Windows, esse arquivo geralmente tem a extensão .exe. No Linux, costuma não ter extensão, mas pode ser executado do mesmo jeito. Para o programador iniciante, não é necessário se preocupar demais com esses detalhes técnicos. O importante é entender que o compilador é o responsável por traduzir o que você escreveu em C para a linguagem que o computador realmente entende.</p>

<h2>Erros: parte inevitável do processo</h2>
    <p>Nenhum programador, por mais experiente que seja, escreve código perfeito de primeira. Erros fazem parte da rotina, e aprender a lidar com eles é parte fundamental do aprendizado.
Um exemplo simples:</p>
    <pre class="code-block"><code>
        <span class="function">printf</span>(<span class="string">"Hello World"</span>)
    </code></pre>
    <p>Se você esquecer o ponto e vírgula no final, o compilador mostrará uma mensagem de erro parecida com esta:</p>
    <pre class="code-block"><code>
        <span class="function">error</span>: expected <span class="string">";"</span>before expected <span class="string">";"</span> token
    </code></pre>
    <p>Isso significa que o compilador esperava encontrar um ponto e vírgula antes da chave de fechamento. Para corrigir, basta adicionar o ;. Com o tempo, você perceberá que aprender a interpretar mensagens de erro é quase uma habilidade própria da programação. O compilador sempre “fala” com você — às vezes de forma meio enigmática —, mas o que ele está dizendo é exatamente onde e por que algo não deu certo.</p>

<h2> Exercicio Para Fixar</h2>

  <div class="quiz-container">
        <h1>Quiz - Módulo 1</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>

<h2>Conclusão</h2>
    <p>Nesta primeira aula, construímos a base da programação: entendemos que programar é dar instruções ao computador seguindo algoritmos, que ele só entende linguagem de máquina e que linguagens de programação e compiladores fazem a mediação. Vimos a estrutura mínima de um programa em C, escrevemos o “Hello World”, usamos printf para exibir mensagens, entendemos o papel do compilador e lidamos com erros básicos. Nos próximos módulos, aprenderemos a tornar os programas interativos, lendo dados do usuário e realizando cálculos, dando vida prática à programação.</p>

    <div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio1.php" class="btn-nav">Desafio →</a>
      </div>

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
        question: "O que é um algoritmo?",
        options: ["Uma linguagem que o computador entende", "Uma sequência de passos bem definidos para alcançar um objetivo", "O programa final que o computador executa"],
        correctAnswer: 1
    },
    {
        question: "O que significa a linha #include <stdio.h> em um programa em C?",
        options: ["Inclui uma biblioteca que permite o uso de funções de entrada e saída", 'Define uma variável chamada "stdio"', "Inicializa o programa"],
        correctAnswer: 0
    },
    {
        question: "Qual é a função principal de um programa em C?",
        options: ["def start()", "void main()", "int Main()"],
        correctAnswer: 2
    },
    {
        question: "O que acontece se você esquecer o ponto e vírgula no final de uma instrução em C?",
        options: ["O código é compilado com um erro de execução", "O compilador ignora o erro e continua a execução do programa", "O compilador mostra uma mensagem de erro informando a ausência do ponto e vírgula"],
        correctAnswer: 2
    },
    {
        question: "O que faz a função printf em C?",
        options: ["Imprime texto na tela", "Lê a entrada do usuário", "Cria um arquivo de texto"],
        correctAnswer: 0
    },
    {
        question: 'Qual é o propósito do caractere "\\n" dentro da função printf?',
        options: ["Ignorar a próxima instrução do programa", "Realizar uma quebra de linha na saída", "Indicar que o programa deve ser executado em segundo plano"],
        correctAnswer: 1
    },
    {
        question: "O que faz a função 'return' em C?",
        options: ["Finaliza o programa", "Retorna um valor", "Exibe um valor na tela"],
        correctAnswer: 1
    },
    {
        question: "Qual é o primeiro passo no processo de transformação de um código-fonte em um programa executável?",
        options: ["O código é transformado em linguagem de máquina manualmente", "O código é diretamente executado pelo computador", "O código é compilado para gerar o código objeto"],
        correctAnswer: 2
    },
    {
        question: "O que significa a linha int main() em um programa em C?",
        options: ["O ponto de início de execução do programa", "Uma declaração de variável", "Uma função de leitura de dados do usuário"],
        correctAnswer: 0
    },
    {
        question: "O que acontece dentro das chaves { } da função main()?",
        options: ["Nada; elas são opcionais", "Ali ficam as instruções que o computador vai executar", "Elas impedem o programa de rodar enquanto a função não é chamada"],
        correctAnswer: 1
    }
];

let currentQuestionIndex = 0;
let correctAnswers = 0;  // Variável para contar os acertos
const startTime = Date.now();

// Função para exibir as perguntas
function displayQuestion() {
    const question = questions[currentQuestionIndex];
    document.querySelector(".question").innerText = question.question;
    const buttons = document.querySelectorAll(".options button");

    question.options.forEach((option, index) => {
        buttons[index].innerText = option;
    });

    document.querySelector(".feedback").innerText = "";
    document.querySelector(".feedback").className = "feedback";
}

// Função para verificar a resposta
function checkAnswer(selectedIndex) {
    const question = questions[currentQuestionIndex];
    const feedbackElement = document.querySelector(".feedback");

    if (selectedIndex === question.correctAnswer) {
        feedbackElement.innerText = "Resposta Correta!";
        feedbackElement.className = "feedback correct";
        correctAnswers++;
    } else {
        feedbackElement.innerText = "Resposta Errada!";
        feedbackElement.className = "feedback wrong";
    }

    setTimeout(() => {
        currentQuestionIndex++;

        if (currentQuestionIndex < questions.length) {
            displayQuestion();
        } else {
            // QUIZ TERMINOU
            document.querySelector(".question").innerText =
                `Quiz Finalizado! Você acertou ${correctAnswers} de ${questions.length} perguntas.`;
            
            document.querySelector(".options").style.display = "none";
            document.querySelector(".feedback").style.display = "none";

            const endTime = Date.now();
            const tempoGasto = Math.floor((endTime - startTime) / 1000); // segundos

            // ENVIO DO RESULTADO PARA O BACKEND - FINALIZAR QUIZ
            fetch('../../backend/finalizar_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    modulo_id: MODULO_ID,
                    acertos: correctAnswers
                })
            })
            .then(res => res.json())
            .then(data => console.log('Quiz finalizado:', data))
            .catch(err => console.error('Erro finalizando quiz:', err));

            // ENVIO DO TEMPO EM PARALELO
            fetch('../../backend/update_tempo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    modulo_id: MODULO_ID,
                    tempo: tempoGasto
                })
            })
            .then(res => res.json())
            .then(data => console.log('Tempo enviado:', data))
            .catch(err => console.error('Erro enviando tempo:', err));
        }
    }, 1500);
}

// Inicialização do Quiz
displayQuestion();
</script>

    </div>
</body>
</html>
