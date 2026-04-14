<?php
$MODULO_ID = 2; // ID real do módulo
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
  <title>Módulo 2 - Variáveis, Operadores e Entrada de Dados em C</title>
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
        h2 {
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

<body>
  
<header class="topbar">
  <a href="bemvindos.php"><img src="../assets/imagens/logo-cplat.png" alt="Logo C-Plat" class="logo"></a>
      </header>

<div class="helper">
<video id="introVideo" class="video-centralizado">
  <source src="../assets/C-borg/animated/m2.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
<h1 style="font-size:30px; text-align:left;">Módulo 2: Variáveis, Operadores e Entrada de Dados em C</h1>
<h2>Introdução</h2>  
<p>Nos módulos anteriores, exploramos os fundamentos da programação: algoritmos, conversão em código, variáveis, tipos de dados e interação com o usuário. Até agora, nossos programas eram simples e seguiam uma sequência fixa de passos. Porém, no mundo real, precisamos que os programas tomem decisões, adaptando-se a diferentes situações. Um caixa eletrônico, por exemplo, libera ou bloqueia o acesso dependendo da senha fornecida, e em um jogo, o jogador ganha ou perde pontos conforme suas ações. Neste módulo, vamos aprender a aplicar estruturas de seleção em C, permitindo que os programas escolham caminhos diferentes com base em condições específicas.
</p>

<h2>Variáveis e Caixinhas de Memória</h2>
  <p>Você pode imaginar uma variável como uma caixinha que guarda um valor dentro do computador. Essa caixinha tem três características:</p>
  <ol>
    <li>Um nome → para que possamos identificar a variável no programa.</li>
    <li>Um tipo → que define o tipo de informação que pode ser guardada (número inteiro, número real, caractere, etc.).</li>
    <li>Um valor → que é o dado armazenado naquele momento.</li>
  </ol>
  <p>Por exemplo, se criamos uma variável chamada idade do tipo inteiro e guardamos o valor 20 nela, podemos pensar assim:</p>
  <div class="exemplo-um">
  <img src="../assets/imagens/example-one-module-two.png" alt="Exemplo um" style=" display: block;  margin: 0 auto; width: 30%; height: 350px;"/>
  </div>
  <p>No C, declaramos uma variável escrevendo o tipo seguido do nome:</p>
  <pre class="code-block"><code>
    <span class="type">int</span> idade;
  </code></pre>
  <p>Aqui criamos uma variável chamada idade que pode guardar números inteiros.</br>
  Podemos também já atribuir um valor no momento da criação:
  </p>
  <pre class="code-block"><code>
    <span class="type">int</span> idade = <span class="number">20</span>;
  </code></pre>
  <p>Isso significa: crie uma caixinha chamada idade e coloque o número 20 dentro dela.</p>

<h2>Tipos Básicos de Dados em C</h2>
  <p>O C é uma linguagem que exige que você diga que tipo de informação cada variável vai armazenar. Os tipos básicos mais usados são:</p>
  <ul>
    <li>int → para números inteiros (ex.: -5, 0, 42).</li>
    <li>float → para números reais com ponto decimal (ex.: 3.14, -0.5).</li>
    <li>double → também para números reais, mas com maior precisão.</li>
    <li>char → para caracteres individuais (ex.: 'a', 'X', '9').</li>
  </ul>
  <p>Exemplos de declaração:</p>
    <pre class="code-block"><code>
    <span class="type">int</span> ano = <span class="number">2025</span>;
    <span class="type">float</span> altura = <span class="number">1.75</span>;
    <span class="type">double</span> pi = <span class="number">3.1415926535</span>;
    <span class="type">char</span> letra = <span class="string">'A'</span>;
  </code></pre>
  <p>Cada tipo ocupa um espaço diferente na memória e serve para finalidades distintas. Mais adiante, quando falarmos de eficiência, essa escolha fará diferença. Por enquanto, basta entender que cada variável precisa de um tipo adequado ao valor que vai armazenar.</p>

<h2>Atribuição: o sinal de igual não é igualdade!</h2>
  <p>No dia a dia, quando usamos o símbolo “=”, pensamos em igualdade matemática. Mas em C, o sinal de igual significa atribuição. Isso quer dizer: “o valor da direita deve ser colocado dentro da variável da esquerda”.
  </br>Exemplo:</p>
  <pre class="code-block"><code>
    x = <span class="number">2</span>;
  </code></pre>
  <p>Esse comando significa “coloque o valor 2 dentro da variável x”.
  </br>Um caso que costuma confundir iniciantes é:</p>
  <pre class="code-block"><code>
    x = x + <span class="number">1</span>;
  </code></pre>
  <p>Se pensarmos como matemática, essa frase parece absurda: “x é igual a x mais 1” nunca seria verdadeiro. Mas em programação isso faz sentido. O que acontece é o seguinte: o valor atual de x é usado no cálculo à direita, depois o resultado é guardado de novo na variável x.
  </br>Exemplo prático:</p>
  <ul>
    <li>Se x vale 2, ao executar x = x + 1;, o lado direito é calculado: 2 + 1 = 3.</li>
    <li>Esse valor 3 é então armazenado de volta em x.</li>
    <li>Agora x vale 3.</li>
  </ul>
  <p>Esse tipo de operação é muito comum para acumuladores e contadores.</p>

<h2>Operadores aritméticos</h2>
  <p>Com variáveis criadas, podemos fazer cálculos. O C possui operadores aritméticos parecidos com os da matemática:</p>
  <ul>
    <li>+ → adição</li>
    <li>- → subtração</li>
    <li>* → multiplicação</li>
    <li>/ → divisão</li>
    <li>% → resto da divisão inteira</li>
  </ul>
  <p>Exemplo:</p>
  <pre class="code-block"><code>
    <span class="type">int</span> a = <span class="number">10</span>, b = <span class="number">3</span>;
    <span class="type">int</span> soma = a + b; <span class="comment">// soma = 13</span>
    <span class="type">int</span> subtração = a - b; <span class="comment">// subtração = 7</span>
    <span class="type">int</span> multiplicação = a * b; <span class="comment">// multiplicação = 30</span>
    <span class="type">int</span> divisão = a / b; <span class="comment">// divisão = 3 (divisão inteira!)</span>
    <span class="type">int</span> resto = a % b; <span class="comment">// resto = 1</span>
  </code></pre>
  <p>Um ponto importante: quando usamos / entre dois inteiros, o resultado é também inteiro (a parte decimal é descartada).</p>

<h2>Ordem de precedência</h2>
  <p>Assim como na matemática, para realizarmos o cálculo de expressões numéricas devemos seguir uma ordem de precedência. A ordem de execução na linguagem C é a seguinte:</p>
  <ol>
    <li>Parênteses</li>
    <li>multiplicação, divisão e resto</li>
    <li>Adição e subtração</li>
  </ol>
  <p>Exemplo:</p>
  <pre class="code-block"><code>
    <span class="type">int</span> resultado = <span class="number">2</span> + <span class="number">3</span> * <span class="number">4</span>;
  </code></pre>
  <p>Aqui o resultado será 14, pois a multiplicação vem antes. Se quisermos que a soma seja feita primeiro, usamos parênteses:</p>
  <pre class="code-block"><code>
    <span class="type">int</span> resultado = (<span class="number">2</span> + <span class="number">3</span>) * <span class="number">4</span>; // agora resultado = 20
  </code></pre>

<h2>Entrada e saída de dados</h2>
  <p>Até agora, nossos programas apenas exibiram mensagens fixas. Agora vamos torná-los interativos: o usuário poderá digitar valores, e o programa fará cálculos com eles.
  </br>Para isso, usamos duas funções principais:</p>
  <ul>
    <li>printf → imprime mensagens na tela.</li>
    <li>scanf → lê valores digitados pelo usuário.</li>
  </ul>
  <p>Exemplo de programa que lê dois números e mostra a soma:</p>
  <pre class="code-block"><code>
    #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
        <span class="type">int</span> <span class="function">Main</span>()
        {
          <span class="type">int</span> a, b, soma;

          <span class="function">printf</span>(<span class="string">"Digite o primeiro número:\n"</span>);
          <span class="function">scanf</span>(<span class="string">"%d"</span>, &a);

          <span class="function">printf</span>(<span class="string">"Digite o segundo número:\n"</span>);
          <span class="function">scanf</span>(<span class="string">"%d"</span>, &b);

          soma = a + b;

          <span class="function">printf</span>(<span class="string">"A soma de %d e %d é %d\n"</span>, a, b, soma); 

          <span class="keyword">return</span> <span class="number">0</span>;
        }
  </code></pre>
  <p>Explicando:</p>
  <ul>
    <li>O %d dentro de scanf e printf é um especificador de formato, usado para inteiros.</li>
    <li>No scanf, usamos o símbolo & antes da variável para indicar o endereço de memória onde o valor digitado deve ser guardado.</li>
    <li>No printf, usamos %d para mostrar o valor armazenado.</li>
  </ul>
<h2>Exercício para Fixação</h2>

  <div class="quiz-container">
        <h1>Quiz - Módulo 2</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>
<h2>Conclusão</h2>
  <p>Neste módulo, avançamos bastante em relação ao primeiro contato. Agora entendemos que programas não servem apenas para imprimir frases fixas, mas sim para trabalhar com dados fornecidos pelo usuário. Aprendemos que variáveis são caixinhas de memória que armazenam valores, que existem diferentes tipos de dados, que o sinal de igual em C significa atribuição, que podemos realizar operações aritméticas com variáveis, respeitando regras de precedência, e que podemos interagir com o usuário através do scanf e printf. Com isso, já temos base para construir programas simples e úteis, que recebem dados, fazem cálculos e apresentam resultados. No próximo módulo, vamos explorar como os programas podem tomar decisões, utilizando estruturas condicionais como if e else. Isso permitirá que nossos programas se tornem inteligentes, capazes de reagir de forma diferente dependendo da situação.</p>

<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio2.php" class="btn-nav">Desafio →</a></div>
      </div>
      </div>
</body>
<script>
const MODULO_ID = <?= $MODULO_ID ?> // na parte do <script> do final do HTML

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
        question: "O que é uma variável em C?",
        options: [
            "Espaço de memória nomeado que armazena um valor de um tipo específico",
            "Um comando que executa operações matemáticas",
            "Valor constante que não pode ser alterado durante a execução"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual tipo de dado em C é indicado para armazenar números inteiros?",
        options: ["int", "float", "char"],
        correctAnswer: 0
    },
    {
        question: "Dado o código: int idade = 20; idade = idade + 5; Qual o valor final de idade?",
        options: ["20", "25", "Erro de compilação"],
        correctAnswer: 1
    },
    {
        question: "Qual operador em C retorna o resto da divisão inteira?",
        options: ["%", "/", "*"],
        correctAnswer: 0
    },
    {
        question: "Qual a função do símbolo '&' no scanf?",
        options: [
            "Indicar o endereço de memória da variável onde o valor digitado será armazenado",
            "Especificar o tipo da variável",
            "Declarar a variável"
        ],
        correctAnswer: 0
    },
    {
        question: "O que significa o operador '=' em C?",
        options: [
            "Atribuição de um valor à variável",
            "Comparação de igualdade entre valores",
            "Incremento de uma variável"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual a principal diferença entre float e double?",
        options: [
            "double armazena números reais com maior precisão que float",
            "float pode armazenar caracteres e double não",
            "float é usado para inteiros e double para reais"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual a função de printf em C?",
        options: ["Imprime texto na tela", "Lê valores do usuário", "Cria arquivos de texto"],
        correctAnswer: 0
    },
    {
        question: "Qual é a ordem correta de precedência em expressões aritméticas?",
        options: [
            "Parênteses → multiplicação/divisão → adição/subtração",
            "Multiplicação/divisão → parênteses → adição/subtração",
            "Adição/subtração → multiplicação/divisão → parênteses"
        ],
        correctAnswer: 0
    },
    {
        question: "No printf, o que faz '%d'?",
        options: [
            "Indica que o valor é um inteiro",
            "Declara a variável inteira",
            "Executa uma operação aritmética"
        ],
        correctAnswer: 0
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
