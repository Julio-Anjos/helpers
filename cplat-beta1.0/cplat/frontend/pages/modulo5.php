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
  <title>Módulo 5 - Estruturas de Repetição: While e Do-While</title>
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
  <source src="../assets/C-borg/animated/m5.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 5: Estruturas de Repetição: While e Do-While</h1>
    <h2>Introdução</h2>
      <p>Até agora, nossos programas seguiram um fluxo linear: começavam na função main, executavam as instruções uma a uma e terminavam. Já aprendemos a tomar decisões com if e switch, permitindo que o programa escolhesse diferentes caminhos. No entanto, há uma limitação importante: se precisarmos repetir uma mesma instrução várias vezes, seremos forçados a escrevê-la várias vezes no código. Por exemplo, se quisermos imprimir os números de 1 a 100, sem laços de repetição, precisaríamos de cem linhas de printf, o que não faz sentido. É aqui que entram as estruturas de repetição, ou laços/loops. Com elas, podemos instruir o computador a repetir um bloco de código enquanto uma condição for verdadeira, tornando o programa mais compacto, organizado e eficiente. Isso facilita a escrita de tarefas repetitivas e torna o código muito mais poderoso.</p>

    <h2>O conceito de iteração</h2>
        <p>Iterar significa repetir. Em algoritmos, a iteração é usada quando precisamos executar um mesmo conjunto de instruções várias vezes, seja um número determinado de vezes, seja até que uma condição seja atendida.
        <br>Exemplos do cotidiano:</p>
        <ul>
          <li>Contar moedas até terminar o pote.</li>
          <li>Perguntar a senha até que o usuário digite a correta.</li>
          <li>Somar notas até que todas as provas tenham sido corrigidas.</li>
        </ul>
        <p>O computador segue a mesma lógica: executa um bloco de código repetidamente até que uma condição de parada seja atingida.</p>

    <h2>O laço while</h2>
        <p>O laço while é a forma mais simples de repetição em C. Sua sintaxe é:</p>
        <pre class="code-block"><code>
          <span class="keyword">While</span> (condição)
          {
            <span class="comment">// comandos a serem repetidos</span>
          }
        </code></pre>
        <p>O funcionamento é o seguinte:</p>
        <ol>
          <li>O programa avalia a condicao.</li>
          <li>Se for verdadeira, executa os comandos dentro do bloco.</li>
          <li>Ao terminar, volta ao passo 1.</li>
          <li>Quando a condicao se torna falsa, o laço é encerrado.</li>
        </ol>
        <p>Ou seja: o while testa antes de executar.</p>
        <h3>Exemplo: imprimir números de 0 a 9</h3>
        <pre class="code-block"><code>
          #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>

          <span class="type">int</span> <span class="function">Main</span>()
          {
            <span class="type">int</span> i = <span class="number">0</span>;

            <span class="keyword">while</span> (i &lt; <span class="number">10</span>)
            {
              <span class="function">printf</span>(<span class="string">"%d\n"</span>, i);
              i++;
            }
            <span class="keyword">return</span> <span class="number">0</span>;
          }
        </code></pre>
        <p>Explicando:</p>
        <ul>
          <li>Começamos com i = 0.</li>
          <li>A condição é i &lt; 10. Enquanto for verdadeira, o bloco será repetido.</li>
          <li>A cada repetição, i é incrementado em 1.</li>
          <li>Quando i chega a 10, a condição se torna falsa e o laço termina.</li>
        </ul>
    <h2>Cuidados com o while</h2>
        <p>Existem dois cuidados essenciais ao usar while:</p>
        <pre class="code-block"><code><span class="comment">// Inicialização da variável de controle: se esquecermos de inicializar, o comportamento pode ser<br> imprevisível.</span>
            
        <span class="type">int</span> i;
            <span class="keyword">while</span> (i &lt; <span class="number">10</span>) <span class="comment">// valor de i indefinido!!</span>
              <span class="function">printf</span>(<span class="string">"%d\n"</span>, i);
        </code></pre>
        
        <h3></h3>
        <pre class="code-block"><code><span class="comment">// Atualização da variável de controle: se a condição nunca mudar, o laço será infinito.</span>
            <span class="type">int</span> i = 0;
            <span class="keyword">while</span> (i &lt; <span class="number">10</span>) <span class="comment">// valor de i indefinido!!</span>
            {
              <span class="function">printf</span>(<span class="string">"%d\n"</span>, i);
              <span class="comment">// esqueci de incrementar o i!</span>
            }
        </code></pre>
        <p>Esses são erros comuns, mas com prática fica fácil evitá-los.</p>

    <h2>Exemplo prático: Fatorial</h2>
        <p>O cálculo do fatorial de um número é um ótimo exemplo de repetição. O fatorial de n (escrito como n!) é o produto de todos os inteiros positivos até n.
        <br>Exemplo:</p>
        <ul>
          <li>5! = 5 × 4 × 3 × 2 × 1 = 120</li>
        </ul>
        <p>Podemos calcular isso com while:</p>
        <pre class="code-block"><code>
            #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>

            <span class="type">int</span> <span class="function">Main</span>()
            {
              int n, i = <span class="number">1</span>, fatorial = <span class="number">1</span>;

              <span class="function">printf</span>(<span class="string">"Digite um número inteiro: "</span>);
              <span class="function">scanf</span>(<span class="string">"%d"</span>, &n);

              <span class="keyword">while</span> (i &lt;= n)
              {
                fatorial = fatorial * i;
                i++;
              }
              <span class="function">printf</span>(<span class="string">"o fatorial de %d é %d\n"</span>, n, fatorial);

              <span class="keyword">return</span> <span class="number">0</span>;
            }
        </code></pre>
    <h2>O laço do...while</h2>
        <p>O do...while é parecido com o while, mas com uma diferença importante: ele executa o bloco primeiro e só depois testa a condição.</p>
        <pre class="code-block"><code>
          <span class="keyword">do</span>
          {
            <span class="comment">// comandos</span>
          } <span class="keyword">while</span> (condicao);
        </code></pre>
        <p>Perceba que aqui o ponto e vírgula aparece no final do while.</p>
        <h3>Diferença fundamental</h3>
        <ul>
          <li>while: testa antes → pode não executar nenhuma vez.</li>
          <li>do...while: executa primeiro → garante que o bloco será executado pelo menos uma vez.</li>
        </ul>
        <h3>Exemplo: ler números até digitar 0</h3>
          <pre class="code-block"><code>
            #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>

            <span class="type">int</span> <span class="function">Main</span>()
            {
              int n, i = numero;

              <span class="keyword">do</span>
              {
                <span class="function">printf</span>(<span class="string">"Digite um número (0 para sair): "</span>);
                <span class="function">scanf</span>(<span class="string">"%d"</span>, &numero);

              } <span class="keyword">while</span> (numero != 0);

              <span class="keyword">return</span> <span class="number">0</span>;
            }            
        </code></pre>
        <p>Aqui, mesmo que o usuário digite 0 logo de cara, o programa pelo menos pediu uma vez o número.</p>
    <h2>Comparando while e do...while</h2>
        <p>Imagine que o usuário só possa digitar valores entre 0 e 100.</p>
        <ul>
          <li>Com while, você precisa inicializar a variável e só depois pedir ao usuário, testando antes.</li>
          <li>Com do...while, você simplesmente pede o valor e repete até ser válido.</li>
        </ul>
        <p>Por isso, do...while é ótimo para validação de dados.</p>


<h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 5</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>

    <h2>Conclusão</h2>
    <p>Neste módulo, aprendemos a repetir blocos de instruções com while e do...while. Entendemos que a diferença entre eles está no momento em que a condição é testada: antes (while) ou depois (do...while). Vimos exemplos práticos como imprimir sequências, calcular fatoriais, validar dados e ler números até que uma condição de parada seja atingida. Com isso, nossos programas ganharam muito mais poder: agora podemos automatizar tarefas repetitivas sem precisar escrever código duplicado. No próximo módulo, vamos conhecer outra forma de repetição: o laço for, que é especialmente útil quando já sabemos exatamente quantas vezes o bloco deve ser executado.</p>


<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio5.php" class="btn-nav">Desafio →</a>
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
        question: "Qual é a principal utilidade das estruturas de repetição em C?",
        options: [
            "Permitir que o programa tome decisões entre diferentes caminhos",
            "Repetir um bloco de código várias vezes sem precisar escrevê-lo múltiplas vezes",
            "Declarar variáveis de diferentes tipos de dados"
        ],
        correctAnswer: 1  // Correto: repetir bloco de código
    },
    {
        question: "Como funciona o laço while em C?",
        options: [
            "Executa o bloco primeiro e depois testa a condição",
            "Testa a condição e, se for verdadeira, executa o bloco repetidamente",
            "Executa o bloco um número fixo de vezes independentemente da condição"
        ],
        correctAnswer: 1  // Correto: testa condição antes de executar
    },
    {
        question: "Qual é a diferença fundamental entre while e do-while?",
        options: [
            "while testa a condição antes de executar, do-while executa antes de testar",
            "do-while é mais rápido que while para todos os casos",
            "while só funciona com números inteiros, do-while aceita qualquer tipo"
        ],
        correctAnswer: 0  // Correto: while testa antes, do-while executa antes de testar
    },
    {
        question: "No exemplo do fatorial, qual é o papel da variável 'i' no laço while?",
        options: [
            "Armazenar o resultado final do cálculo",
            "Controlar quantas vezes o laço será executado (contador)",
            "Receber a entrada do usuário"
        ],
        correctAnswer: 1  // Correto: controlar quantas vezes o laço será executado
    },
    {
        question: "Qual erro comum pode causar um laço infinito no while?",
        options: [
            "Esquecer de inicializar a variável de controle",
            "Não incluir um ponto e vírgula no final do while",
            "Esquecer de atualizar a variável de controle dentro do laço"
        ],
        correctAnswer: 2  // Correto: esquecer de atualizar a variável de controle
    },
    {
        question: "Para que tipo de situação o do-while é especialmente útil?",
        options: [
            "Quando sabemos exatamente quantas vezes o laço deve executar",
            "Para validação de dados, garantindo que o bloco execute pelo menos uma vez",
            "Quando precisamos executar condições complexas com múltiplos operadores lógicos"
        ],
        correctAnswer: 1  // Correto: validação de dados
    },
    {
        question: "No código: 'do { printf(\"Hello\"); } while (0 > 1);', quantas vezes 'Hello' será impresso?",
        options: [
            "0 vezes",
            "1 vez",
            "Infinitas vezes (laço infinito)"
        ],
        correctAnswer: 1  // Correto: 1 vez (executa antes de testar condição falsa)
    },
    {
        question: "No exemplo de leitura de números até digitar 0, qual seria um problema se usássemos while em vez de do-while?",
        options: [
            "Teríamos que inicializar a variável com um valor diferente de 0 antes do laço",
            "O programa não compilaria",
            "O laço nunca terminaria"
        ],
        correctAnswer: 0  // Correto: teríamos que inicializar a variável
    },
    {
        question: "Qual é o significado do termo 'iteração' no contexto de programação?",
        options: [
            "Tomar decisões com base em condições",
            "Repetir um conjunto de instruções múltiplas vezes",
            "Declarar e inicializar variáveis"
        ],
        correctAnswer: 1  // Correto: repetir instruções
    },
    {
        question: "No cálculo do fatorial com while, qual é a condição de parada do laço?",
        options: [
            "Quando i for maior que n",
            "Quando fatorial for igual a zero",
            "Quando i for menor ou igual a n"
        ],
        correctAnswer: 0  // Correto: quando i for maior que n
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
