<?php
$MODULO_ID = 9; // ID real do módulo
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
  <title>Módulo 9 - Modularização com funções</title>
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
  <source src="../assets/C-borg/animated/m9.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 9: Dividir para Conquistar: Introdução à Modularização com Funções em C</h1>
  <p>Olá! Em nossa jornada até aqui, aprendemos a criar programas que tomam decisões, repetem ações e organizam dados em estruturas cada vez mais complexas, como vetores e matrizes. À medida que nossos programas cresceram em capacidade, eles também se tornaram maiores e mais complexos. E se houvesse uma forma de organizar todo esse código em blocos lógicos, reutilizáveis e mais fáceis de entender? É exatamente isso que vamos explorar hoje: a modularização através de funções. Imagine que você está construindo uma casa. Em vez de tentar erguê-la como um único bloco maciço, você constrói as paredes, instala as janelas, coloca as portas — cada parte com uma função específica. Na programação, as funções são como essas "partes" especializadas que, quando unidas, formam o programa completo. Elas permitem que você divida tarefas complexas em partes menores e mais manejáveis, tornando o código mais limpo, organizado e reutilizável. Vamos aprender a criar e usar essas funções para dar mais estrutura e clareza aos nossos programas!</p>
    <h2>O Programa como um Conjunto de Funções: Saindo da Linearidade</h2>
      <p>Até agora, praticamente todo o nosso código foi escrito dentro da função main(). Em C, a main() é especial: é o ponto de partida obrigatório que o sistema operacional procura para executar o programa. Mas ela não precisa fazer tudo sozinha. Na verdade, ela não deveria. Um programa em C é, em sua essência, um conjunto de funções trabalhando juntas. A main() é a função principal, a "gerente" do projeto, que coordena a chamada de outras funções especializadas - sejam elas funções que já vêm prontas em bibliotecas (como printf(), scanf(), pow()), sejam funções que nós mesmos criamos.
      <br>Criar uma função é como escrever uma pequena receita com um nome. Você define:</p>
      <ol>
        <li>O que ela faz (o bloco de código entre chaves {}).</li>
        <li>Se ela precisa de ingredientes (os parâmetros ou argumentos).</li>
        <li>Se ela produz um resultado final (o valor de retorno).</li>
      </ol>
      <p>Vamos começar pelo tipo mais simples de função.</p>
    <h2>Funções void: Os Atores que Executam uma Ação</h2>
      <p>A palavra-chave void significa "vazio". Uma função void é aquela que não retorna um valor após sua execução. Ela é útil para realizar uma ação específica, como imprimir uma mensagem na tela ou desenhar um menu.
      <br>Vejamos nosso primeiro exemplo, retirado dos seus materiais:</p>
      <ul>
    <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
      
      <span class="comment">// Definição da função 'mensagem'</span>
      <span class="keyword">void</span> mensagem() {
          <span class="function">printf</span>(<span class="string">"\n\tOla!"</span>);
      }

      <span class="comment">// Função principal</span>
      <span class="type">int</span> main() {
          mensagem();   <span class="comment">// Chamada da função 'mensagem'</span>
          <span class="function">printf</span>(<span class="string">"Tudo bem?\n"</span>);
          <span class="keyword">return</span> <span class="number">0</span>;
      }
    </code></pre>
      </ul>
      <p>Neste código:</p>
      <ul>
        <li>void mensagem(): Declaramos uma função chamada mensagem que não recebe argumentos (parênteses vazios) e não retorna valor (void).</li>
        <li>{ ... }: O bloco de código dentro das chaves é o corpo da função, que será executado quando ela for chamada.</li>
        <li>mensagem();: Na main(), chamamos a função pelo seu nome. Quando o programa chega nessa linha, ele "pula" para executar o código dentro de mensagem() e, ao terminar, volta para a linha seguinte na main().</li>
      </ul>
      <p>A grande vantagem? Se precisarmos exibir "Olá!" em dez lugares diferentes do programa, basta escrever mensagem(); dez vezes. Se precisarmos mudar a mensagem, alteramos apenas um lugar: dentro da função.</p>

    <h2>Funções que Retornam Valores: Os Calculistas Especializados</h2>
      <p>Mais poderosas são as funções que realizam um cálculo ou operação e retornam um resultado. Elas funcionam como uma pergunta que fazemos ao programa. Por exemplo, "qual é o quadrado de 5?". Nesse caso, substituímos void pelo tipo de dado do valor que será retornado (como int, float, char). Para enviar o resultado de volta para quem a chamou, usamos o comando return.</p>
      <ul>
    <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
      
      <span class="comment">// Função que calcula o quadrado de um número inteiro</span>
      <span class="type">int</span> <span class="function">quadrado</span>(<span class="type">int</span> x) {   <span class="comment">// Recebe um inteiro 'x' como argumento</span>
          <span class="type">int</span> resultado = x * x;
          <span class="keyword">return</span> <span class="number">resultado</span>;    <span class="comment">// Retorna um valor inteiro</span>
      }

      <span class="type">int</span> <span class="function">main()</span> {
          <span class="type">int</span> numero, resultado;
          <span class="function">printf</span>(<span class="string">"Entre com um número: "</span>);
          <span class="function">scanf</span>(<span class="string">"%d"</span>, &numero);

          resultado = <span class="function">quadrado</span>(numero);   <span class="comment">// Chamada da função. O valor retornado é armazenado em 'resultado'</span>
          <span class="function">printf</span>(<span class="string">"O quadrado de %d é %d\n"</span>, numero, resultado);
          <span class="keyword">return</span> <span class="number">0</span>;  
      }
    </code></pre>
      </ul>
      <p>Aqui, a mágica acontece na linha "resultado = quadrado(numero);":</p>
      <ol>
        <li>O valor da variável numero é copiado para o parâmetro x da função quadrado.</li>
        <li>A função quadrado é executada, calculando x * x.</li>
        <li>O comando return envia o valor calculado de volta para a main().</li>
        <li>Na main(), esse valor retornado é atribuído à variável resultado.</li>
      </ol>
      <p>É crucial entender que a variável x dentro da função quadrado é independente da variável numero na main(). Elas podem até ter o mesmo valor, mas são entidades separadas. Isso nos leva a um conceito fundamental.</p>

    <h2>Escopo de Variáveis: O Mundo Particular de Cada Função</h2>
      <p>As variáveis declaradas dentro de uma função (incluindo a main) são chamadas de variáveis locais. Isso significa que elas só existem e são reconhecidas dentro da função onde foram declaradas. Pense em cada função como um cômodo de uma casa. Os móveis (variáveis) da sala não estão disponíveis no quarto, e vice-versa, a menos que você explicitamente os leve de um cômodo para outro (o que, em programação, fazemos passando valores como argumentos).</p>
      <ul>
    <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
      
      <span class="comment">// Definição da função 'mensagem'</span>
      <span class="keyword">void</span> funcaoExemplo() {
          <span class="type">int</span> var_local = 100;    <span class="comment">// Esta variável só existe dentro de funcaoExemplo</span>
          <span class="function">printf</span>(<span class="string">"Dentro da função: %d\n"</span>, var_local);
      }

      <span class="type">int</span> <span class="function">main()</span> {
        <span class="type">int</span> var_local = 50;   <span class="comment">// Esta é uma variável DIFERENTE, que só existe na main</span>
        <span class="function">printf</span>(<span class="string">"Dentro da função: %d\n"</span>, var_local);

        <span class="function">funcaoExemplo</span>();    <span class="comment">// Imprimirá 100</span>

        <span class="type">int</span> var_local = 50; <span class="comment">// Ainda é 50</span>
        <span class="keyword">return</span> <span class="number">0</span>; 
      }
    </code></pre>
      </ul>
      <p>O programa acima imprimirá:</p>
      <ul>
    <pre class="code-block"><code>
        Na main, antes da funcao: 50
        Dentro da funcao: 100
        Na main, depois da funcao: 50
    </code></pre>
      </ul>
      <p>Isso demonstra que as duas var_local são completamente independentes. Esse isolamento é uma grande vantagem, pois evita que funções interfiram acidentalmente umas nas outras.</p>
    
    <h2>Por que Modularizar? A Arte de Dominar a Complexidade</h2>
      <p>Escrever programas usando funções vai muito além de evitar repetição de código. É uma questão de organização mental e gerenciamento de complexidade.</p>
      <ol>
        <li>Divide para Conquistar: Um problema complexo (ex.: "desenvolver um sistema de gestão escolar") é quebrado em subproblemas menores e mais manejáveis (ex.: "calcular média", "verificar aprovação", "imprimir boletim"). Cada subproblema vira uma função.</li>
        <li>Facilita Testes e Depuração: É muito mais fácil testar e encontrar erros em uma pequena função que faz uma coisa só do que em um bloco gigante de código na main().</li>
        <li>Promove o Reuso: Uma função bem escrita para calcular uma média pode ser usada em dezenas de programas diferentes.</li>
        <li>Torna o Código Legível: A main() se torna uma sequência clara de chamadas de função, lendo-se quase como um sumário do que o programa faz: cadastrarAlunos(); calcularMedias(); imprimirRelatorios();.</li>
      </ol>
      <p>No próximo módulo, aprofundaremos nosso conhecimento em funções, explorando como trabalhar com diferentes tipos de parâmetros e como estruturar melhor nossos programas com protótipos de funções. Por hoje, o exercício é começar a pensar de forma modular. Ao enfrentar um problema de programação, pergunte-se: "Quais são as tarefas menores e repetitivas que compõem este problema?" Essas tarefas são fortes candidatas a se tornarem funções. Comece criando funções simples, como a que imprime uma linha de asteriscos ou que lê e retorna uma idade do usuário. Esse é o primeiro passo para escrever código não apenas que funcione, mas que seja elegante, robusto e fácil de manter.</p>

  <h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 9</h1>

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
<a href="desafio9.php" class="btn-nav">Desafio →</a>
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
        question: "Qual é o objetivo principal da modularização com funções em C?",
        options: [
            "Dividir tarefas complexas em partes menores, tornando o código mais organizado e reutilizável",
            "Aumentar a velocidade de execução dos programas",
            "Reduzir a quantidade de memória utilizada pelo programa"
        ],
        correctAnswer: 0
    },
    {
        question: "O que significa quando uma função é declarada com 'void' como tipo de retorno?",
        options: [
            "Que a função não retorna nenhum valor",
            "Que a função retorna um valor do tipo 'void'",
            "Que a função não recebe parâmetros"
        ],
        correctAnswer: 0
    },
    {
        question: "Como uma função retorna um valor em C?",
        options: [
            "Usando o comando 'result'",
            "Usando o comando 'return'",
            "Atribuindo o valor a uma variável global"
        ],
        correctAnswer: 1
    },
    {
        question: "O que são variáveis locais em C?",
        options: [
            "Variáveis que podem ser acessadas de qualquer parte do programa",
            "Variáveis que mantêm seus valores entre chamadas de função",
            "Variáveis que existem apenas dentro da função onde foram declaradas"
        ],
        correctAnswer: 2
    },
    {
        question: "No exemplo da função 'quadrado', o que acontece quando chamamos 'resultado = quadrado(numero)'?",
        options: [
            "A variável 'numero' é movida para dentro da função 'quadrado'",
            "O valor de 'numero' é copiado para o parâmetro 'x' da função",
            "A função 'quadrado' modifica diretamente a variável 'numero' na main"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual é a vantagem de usar funções para evitar repetição de código?",
        options: [
            "Torna o programa mais rápido",
            "Permite alterar o comportamento em apenas um lugar se necessário",
            "Reduz o tamanho do arquivo executável"
        ],
        correctAnswer: 1
    },
    {
        question: "O que significa o 'escopo' de uma variável?",
        options: [
            "O tipo de dados da variável",
            "Onde no código a variável pode ser acessada e modificada",
            "O valor inicial da variável"
        ],
        correctAnswer: 1
    },
    {
        question: "Por que a modularização facilita a depuração (debugging)?",
        options: [
            "Porque cada função é compilada separadamente",
            "Porque funções tornam os erros mais visíveis no compilador",
            "Porque é mais fácil testar pequenas funções que fazem uma coisa específica"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual das seguintes é uma função 'void' típica?",
        options: [
            "Uma função que imprime um menu na tela",
            "Uma função que calcula a média de números",
            "Uma função que retorna o maior de dois números"
        ],
        correctAnswer: 0
    },
    {
        question: "Como as funções ajudam na legibilidade do código?",
        options: [
            "Eliminando a necessidade de comentários",
            "Reduzindo o número de linhas de código necessárias",
            "Permitindo que a função main se torne uma sequência clara de chamadas de funções"
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
