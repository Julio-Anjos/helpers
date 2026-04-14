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
  <title>Módulo 4 - Seleção Múltipla Com Switch-Case</title>
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
  <source src="../assets/C-borg/animated/m4.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>


<div class="align-page">
<h1 style="font-size:30px; text-align:left;">Módulo 4: Seleção Múltipla Com Switch-Case</h1>
<h2>Introdução</h2>
    <p>Nos módulos anteriores, aprendemos a usar estruturas de decisão como if e if...else, permitindo que nossos programas escolhessem caminhos diferentes com base nas condições fornecidas pelo usuário. Porém, quando precisamos verificar muitos casos diferentes para a mesma variável, os if...else encadeados podem se tornar longos, repetitivos e difíceis de entender. Um exemplo disso seria um programa que recebe um número de 1 a 7 e retorna o dia da semana correspondente. Usando apenas if...else, o código ficaria algo assim:</p>
    <pre class="code-block"><code>
          <span class="keyword">if</span>(dia == <span class="number">1</span>)
            <span class="function">printf</span>(<span class="string">"Domingo\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">2</span>)
            <span class="function">printf</span>(<span class="string">"Segunda-feira\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">3</span>)
            <span class="function">printf</span>(<span class="string">"Terça-feira\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">4</span>)
            <span class="function">printf</span>(<span class="string">"Quarta-feira\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">5</span>)
            <span class="function">printf</span>(<span class="string">"Quinta-feira\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">6</span>)
            <span class="function">printf</span>(<span class="string">"Sexta-feira\n"</span>);
          <span class="keyword">else if</span>(dia == <span class="number">7</span>)
            <span class="function">printf</span>(<span class="string">"Sábado\n"</span>);
          <span class="keyword">else</span>
            <span class="function">printf</span>(<span class="string">"Dia inválido\n"</span>);
    </code></pre>
    <p>Funciona, mas fica chato de escrever e de ler. É aí que entra o switch-case, uma estrutura criada para lidar com esse tipo de situação de maneira mais clara e organizada.</p>

<h2>O que é o switch-case?</h2>
    <p>O switch-case é uma estrutura de seleção múltipla. Ele é usado quando temos uma variável que pode assumir diferentes valores, e queremos executar uma ação específica para cada valor possível. A ideia é simples:</p>
    <ul>
        <li>O comando switch analisa o valor de uma expressão (geralmente uma variável).</li>
        <li>Cada case representa um valor possível dessa expressão.</li>
        <li>O programa executa o bloco correspondente ao case que for verdadeiro.</li>
        <li>O default (opcional) é executado se nenhum dos casos anteriores corresponder.</li>
    </ul>

<h2>Sintaxe Geral</h2>
    <p>Aqui está a forma básica:</p>
    <pre class="code-block"><code>
    <span class="keyword">Switch</span>(Expressão)
    {
        <span class="keyword">case</span> valor1:
        <span class="comment">// comandos para valor1</span>
            <span class="keyword">break</span>;

        <span class="keyword">case</span> valor2:
        <span class="comment">// comandos para valor2</span>
            <span class="keyword">break</span>;   

        <span class="keyword">case</span> valor3:
        <span class="comment">// comandos para valor3</span>
            <span class="keyword">break</span>;

        <span class="keyword">default</span>:
            <span class="comment">// comando se nenhum valor acima for correspondente</span>
    }
    </code></pre>
    <p>Explicando:</p>
    <ul>
        <li>Expressao é geralmente uma variável do tipo inteiro ou caractere.</li>
        <li>Cada case é comparado com o valor da expressão.</li>
        <li>O comando break é usado para sair do switch após executar aquele caso.</li>
        <li>Se não colocarmos o break, o programa continuará executando os próximos casos em sequência (isso pode ser usado de propósito, mas em geral causa confusão).</li>
        <li>O default funciona como um “else”: é executado se nenhum case for verdadeiro.</li>
    </ul>

<h2>Exemplo 1: dias da semana</h2>
    <p>Vamos reescrever o exemplo dos dias da semana com switch-case:</p>
    <pre class="code-block"><code>
        #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>

        <span class="type">int</span> <span class="function">Main</span>()
        {
            <span class="type">int</span> dia;

            <span class="function">printf</span>(<span class="string">"Digite um número de 1 a 7: "</span>);
            <span class="function">scanf</span>(<span class="string">"%d"</span>, &dia);

            <span class="keyword">Switch</span>(Expressão)
            {
                <span class="keyword">case</span> 1:
                    <span class="function">printf</span>(<span class="string">"Domingo\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> 2:
                    <span class="function">printf</span>(<span class="string">"Segunda-feira\n"</span>);
                    <span class="keyword">break</span>;   
                <span class="keyword">case</span> 3:
                    <span class="function">printf</span>(<span class="string">"Terça-feira\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> 4:
                    <span class="function">printf</span>(<span class="string">"Quarta-feira\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> 5:
                    <span class="function">printf</span>(<span class="string">"Quinta-feira\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> 6:
                    <span class="function">printf</span>(<span class="string">"Sexta-feira\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> 7:
                    <span class="function">printf</span>(<span class="string">"Domingo\n"</span>);
                    <span class="keyword">break</span>;
                <span class="keyword">default</span>:
                    <span class="function">printf</span>(<span class="string">"Dia inválido!\n"</span>);
            }

            <span class="keyword">return</span> <span class="number">0</span>;
        }
    </code></pre>
    <p>Muito mais organizado, não é? Em vez de escrever um monte de if...else, cada caso fica claro e independente.</p>

<h2>Exemplo 2: calculadora simples</h2>
    <p>Outro exemplo clássico é criar uma pequena calculadora que realiza diferentes operações de acordo com a escolha do usuário.</p>
    <pre class="code-block"><code>
        #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>

        <span class="type">int</span> <span class="function">Main</span>()
        {
            <span class="type">char</span> operacao;
            <span class="type">flaot</span> a, b;

            <span class="function">printf</span>(<span class="string">"Digite a operação (+, -, *, /): "</span>);
            <span class="function">scanf</span>(<span class="string">" %c"</span>, &operacao);

            <span class="function">printf</span>(<span class="string">"Digite dois numeros: "</span>);
            <span class="function">scanf</span>(<span class="string">" %f %f"</span>, &a &b);

            <span class="keyword">Switch</span>(operacao)
            {
                <span class="keyword">case</span> +:
                    <span class="function">printf</span>(<span class="string">"Resultado: %.2f\n"</span>, a + b);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> -:
                    <span class="function">printf</span>(<span class="string">"Resultado: %.2f\n"</span>, a - b);
                    <span class="keyword">break</span>;   
                <span class="keyword">case</span> *:
                    <span class="function">printf</span>(<span class="string">"Resultado: %.2f\n"</span>, a * b);
                    <span class="keyword">break</span>;
                <span class="keyword">case</span> /:
                    <span class="keyword">if</span>(b != <span class="number">0</span>)
                        <span class="function">printf</span>(<span class="string">"Resultado: %.2f\n"</span>, a / b);
                    <span class="keyword">else</span>
                        <span class="function">printf</span>(<span class="string">"Erro: divisão por 0!\n"</span>);
                        <span class="keyword">break</span>;
                <span class="keyword">default</span>:
                    <span class="function">printf</span>(<span class="string">"Operação inválida!\n"</span>);
            }
            <span class="keyword">return</span> <span class="number">0</span>;
        }
    </code></pre>
    <p>Aqui usamos um char para ler o símbolo da operação e escolhemos o que fazer com switch.</p>

<h2>Pontos importantes sobre o switch-case</h2>
    <ul>
        <li>O switch só aceita variáveis de tipos inteiros ou caracteres. Não é possível usar expressões do tipo x > 10 diretamente. Para isso, continuamos usando if...else.</li>
        <li>Cada case precisa ser um valor constante (número fixo ou caractere).</li>
        <li>O break é fundamental para evitar que a execução “caia” em outros casos sem querer</li>
        <li>O default é opcional, mas é boa prática usá-lo para tratar valores inesperados.</li>
    </ul>

<h2>Toques extras de saída</h2>
    <p>Embora não façam parte do switch, alguns comandos da biblioteca padrão podem deixar seus programas mais apresentáveis:</p>
    <ul>
        <li>system("cls"); → limpa a tela no Windows (no Linux/Mac, geralmente é system("clear");).</li>
        <li>system("color 0A"); → muda a cor do texto (0A significa fundo preto, texto verde).</li>
    </ul>
    <p>Esses comandos devem ser usados com cuidado, pois são dependentes do sistema operacional, mas dão uma boa sensação de “programa de verdade” para quem está começando.</p>

<h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 4</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>

<h2>Conclusão</h2>
    <p>Neste módulo, aprendemos a usar o switch-case, uma estrutura que simplifica programas que precisam escolher entre muitos casos possíveis de uma mesma variável. Vimos que ele torna o código mais limpo e organizado em comparação com longos blocos de if...else, e que é especialmente útil para construir menus e navegadores de opções.
    </br>Com isso, agora temos duas ferramentas de seleção:</p>
    <ul>
        <li>if...else → para condições gerais, incluindo comparações e intervalos.</li>
        <li>switch-case → para quando precisamos testar valores exatos e distintos.</li>
    </ul>
    <p>No próximo módulo, entraremos em um novo universo: as estruturas de repetição, que permitem executar blocos de código várias vezes sem precisar copiá-los manualmente.</p>

<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio4.php" class="btn-nav">Desafio →</a>
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
        question: "Para que tipo de situações o switch-case é mais adequado?",
        options: [
            "Quando precisamos testar valores exatos e distintos de uma variável",
            "Quando temos que verificar intervalos de valores",
            "Quando precisamos testar condições lógicas complexas com && e ||"
        ],
        correctAnswer: 1
    },
    {
        question: "Quais tipos de dados podem ser usados na expressão do switch?",
        options: [
            "Apenas tipos inteiros (int) e caracteres (char)",
            "Qualquer tipo de dado, incluindo float e double",
            "Apenas strings e arrays"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual é a função do comando 'break' dentro de um case?",
        options: [
            "Continuar executando os próximos cases mesmo após encontrar um correspondente",
            "Sair do switch após executar o bloco correspondente ao case",
            "Reiniciar o switch para testar novamente a expressão"
        ],
        correctAnswer: 2
    },
    {
        question: "O que acontece se não colocarmos o 'break' ao final de um case?",
        options: [
            "O switch será interrompido imediatamente",
            "Ocorrerá um erro de compilação",
            "O programa continuará executando os próximos cases em sequência"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual é a função do 'default' no switch-case?",
        options: [
            "Ser sempre o primeiro caso a ser executado",
            "Definir um valor padrão para a variável testada",
            "Executar um bloco se nenhum case corresponder ao valor da expressão"
        ],
        correctAnswer: 1
    },
    {
        question: "No exemplo da calculadora, por que usamos 'char operacao' no switch?",
        options: [
            "Porque char é mais eficiente que int para operações matemáticas",
            "Porque float não pode ser usado em switch-case",
            "Porque switch só aceita inteiros ou caracteres, e a operação é um caractere"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual é a principal vantagem do switch-case em relação a if...else encadeados?",
        options: [
            "Permite testar condições mais complexas do que if...else",
            "Torna o programa mais rápido em todos os cenários",
            "Deixa o código mais organizado e legível para múltiplos casos de uma mesma variável"
        ],
        correctAnswer: 1
    },
    {
        question: "No exemplo dos dias da semana, qual erro foi cometido no código apresentado?",
        options: [
            "Faltou o comando break em todos os cases",
            "Não foi incluído o bloco default",
            "Case 7 imprime 'Domingo' em vez de 'Sábado'"
        ],
        correctAnswer: 2
    },
    {
        question: "Por que não podemos usar diretamente 'x > 10' como case em um switch?",
        options: [
            "Porque C não permite operadores relacionais em switch",
            "Porque isso seria muito lento para o processador",
            "Porque switch só testa igualdade com valores constantes, não expressões"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual dos seguintes usos seria INADEQUADO para switch-case?",
        options: [
            "Mostrar o nome do mês correspondente a um número de 1 a 12",
            "Executar diferentes operações baseadas em um código de menu (1=Inserir, 2=Remover, etc.)",
            "Testar se uma nota está entre 0 e 10 para dar conceitos (A, B, C, D)"
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
