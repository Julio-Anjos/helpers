<?php
$MODULO_ID = 6; // ID real do módulo
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
  <title>Módulo 6 - Estruturas de Repetição Com For, Contadores e Acumuladores</title>
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
        h2, h3{
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
    <source src="../assets/C-borg/animated/m6.mp4" type="video/quicktime">
    Seu navegador não suporta vídeos HTML5.
  </video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 6: A Arte da Iteração: Domando Repetições com for, Contadores e Acumuladores</h1>
    <h2>Introdução</h2>
    <p>Nosso último módulo foi um marco importante: saímos do mundo das decisões sequenciais e mergulhamos no território dinâmico das repetições. Com o while e o do-while, você aprendeu a criar programas que podem repetir uma tarefa até que uma condição seja atendida. Hoje, vamos levar esse poder ainda mais longe. Imagine que a repetição é um martelo. O while é como um martelo versátil, ótimo para várias situações. Já o for, que veremos hoje, é como um martelo de precisão, ideal para quando você sabe exatamente quantas vezes precisa executar uma tarefa antes de terminar. Para usar essas ferramentas de maneira eficiente, precisamos entender os "assistentes" que ajudam dentro dos loops: contadores, acumuladores e sinalizadores. Esses conceitos são fundamentais para controlar a execução e garantir que as repetições ocorram de maneira exata e organizada.</p>
    <h2>Revisitando a Repetição: Uma Questão de Organização</h2>
      <p>Você se lembra da estrutura do while? Sempre há uma certa coreografia:</p>
      <ol>
        <li>Inicialização de uma variável de controle (antes do loop).</li>
        <li>Verificação de uma condição (no início do loop).</li>
        <li>Execução do bloco de código.</li>
        <li>Atualização da variável de controle (dentro do bloco).</li>
      </ol>
      <p>O desafio é que essa coreografia está espalhada pelo seu código. E se você se esquecer do passo 4 (a atualização)? O resultado é um loop infinito, onde a condição nunca se torna falsa. O comando for foi criado para resolver exatamente isso: organizar toda a lógica de controle do loop em um único lugar, tornando-o mais legível e menos propenso a erros. Pense no for como um while que passou por uma sessão de organização. Sua sintaxe é um concentrado de clareza:</p>
      <pre class="code-block"><code>
        <span class="keyword">for</span> (inicialização; condição; incremento){
            <span class="comment">// Bloco de código a ser repetido</span>
        }
      </code></pre>
      <p>Vamos traduzir um exemplo simples do while para o for. O objetivo: imprimir os números de 1 a 10.</p>
      <h3>Com While:</h3>
      <pre class="code-block"><code>
        <span class="type">int</span> i = <span class="number">1</span>;        <span class="comment">// inicialização (fora do loop)</span>
            <span class="keyword">while</span> (i &lt;= <span class="number">10</span>){<span class="comment"> Condição</span>
                <span class="function">printf</span>(<span class="string">"%d\n"</span>, i);
                i++;
            }
      </code></pre>
      <h3>Com for</h3>
      <pre class="code-block"><code>
        <span class="keyword">for</span> (<span class="type">int</span> i = <span class="number">1</span>; i &lt;=<span class="number">10</span>; i++){
          <span class="function">printf</span>(<span class="string">"%d\n"</span>, i);
        }
      </code></pre>
      <p>Percebe a elegância? A linha do for nos conta uma história completa: "Para uma variável i que começa em 1, e enquanto i for menor ou igual a 10, execute este bloco e, após cada execução, incremente i." Toda a lógica de controle está visível de uma vez. Isso não é apenas mais bonito; é mais seguro. A chance de você esquecer o i++ é praticamente zero, porque ele tem seu lugar dedicado.</p>
    
    <h2>Os Personagens Principais dos Loops: Contadores e Acumuladores</h2>
      <p>Agora que temos uma ferramenta mais organizada, vamos falar dos padrões clássicos que você encontrará em quase todo loop. São técnicas tão fundamentais que recebem nomes especiais.</p>
        <h3>O Contador (cont++)</h3>
        <p>O contador é a variável mais simples e comum em um loop. Sua missão é responder à pergunta: "Quantas vezes algo aconteceu?".</p>
        <ul>
          <li>Características: É inicializada com zero (0) e incrementada de 1 em 1 (contador++) cada vez que o evento de interesse ocorre.</li>
          <li>Analogia: É como marcar risquinhos numa folha de papel a cada vez que um evento acontece.</li>
        </ul>
        <p>O exemplo das médias da turma, presente no seu material, é perfeito. Queremos contar quantos alunos, em uma turma de 20, foram aprovados (média >= 6).</p>
        <pre class="code-block"><code>
          <span class="type">int</span> aprovados = <span class="number">0</span>;  <span class="comment">Inicializa o CONTADOR</span>

          <span class="keyword">for</span> (<span class="type">int</span> i = <span class="number">0</span>; i &lt;=<span class="number">20</span>; i++){
              <span class="type">float</span> media;
              <span class="function">printf</span>(<span class="string">"Digite a média do aluno %d: "</span>, i+1);
              <span class="function">scanf</span>(<span class="string">"%f"</span>, &media);

              <span class="keyword">if</span>(media >= <span class="number">6.0</span>) {
                  aprovados++   <span class="comment">// Incrementa o CONTADOR somente se a condição for verdadeira</span>
              }
          }

          <span class="function">printf</span>(<span class="string">"Total de aprovados: %d\n"</span>, aprovados);
        </code></pre>
        <p>A variável aprovados é nosso contador. Ela só aumenta quando encontramos um aluno aprovado. O i no for também é um contador, mas sua função é controlar o número de iterações (20 alunos). Já o aprovados conta um subconjunto dentro dessas iterações.</p>
        <h3>O Acumulador (soma += valor)</h3>
        <p>Enquanto o contador soma de 1 em 1, o acumulador soma valores variáveis. Sua missão é responder: "Qual é o total?".</p>
        <ul>
          <li>Características: Também é inicializada com zero (0), mas é incrementada por um valor variável (soma = soma + valor; ou soma += valor).</li>
          <li>Analogia: É como ir jogando moedas de valores diferentes em um cofrinho. A cada moeda, o total acumulado aumenta.</li>
        </ul>
        <p>O exemplo das despesas do mês ilustra isso. Somamos valores de despesas até que o usuário sinalize o fim com um valor negativo.</p>
        <pre class="code-block"><code>
          <span class="type">float</span> valor, total_despesas = 0.0;    <span class="comment">Inicializa o CONTADOR</span>
          <span class="function">printf</span>(<span class="string">"Digite as despesas (valor negativo para sair):\n"</span>);

          <span class="keyword">do</span> {   <span class="comment">// Acumula o valor lido na iteração anterior</span>
              total_despesas += valor;
              <span class="function">printf</span>(<span class="string">"Digite o valor da despesa: "</span>);
              <span class="function">scanf</span>(<span class="string">"%f"</span>, &valor);
              } <span class="keyword">while</span> (valor >= 0);    <span class="comment">// O loop para quando um valor negativo for digitado</span>

              <span class="function">printf</span>(<span class="string">"Digite o valor da despesa: R$ %.2f\n"</span>, total_despesas);
        </code></pre>
        <p>Note a lógica sutil aqui: o valor é acumulado antes de ler a nova entrada. Isso é necessário para não incluirmos o valor negativo de saída no total. É um detalhe que mostra a importância de entender o fluxo do seu algoritmo.</p>

        <h3>O Sinalizador (Flag) (flag = 1)</h3>
        <p>O sinalizador, ou flag, não se preocupa com "quantas vezes", mas sim com "se aconteceu pelo menos uma vez". É um interruptor lógico.</p>
        <ul>
          <li>Características: É inicializada com um valor que significa "falso" ou "não aconteceu" (geralmente 0). Se o evento ocorrer, ela é "ativada" (mudada para 1).</li>
          <li>Analogia: É como um alarme de incêndio. Ele está desligado (0). Se qualquer sensor detectar fumaça (o evento), o alarme soa (1), independente de quantos sensores foram acionados.</li>
        </ul>
        <p>O clássico exemplo é verificar se um número é primo. Um número é primo se for divisível apenas por 1 e por ele mesmo. Nossa estratégia é assumir que é primo (eh_primo = 1) e procurar por qualquer divisor que prove o contrário.</p>
        <pre class="code-block"><code>
          <span class="type">int</span> numero, eh_primo = 1;    <span class="comment">Inicializa a Flag como "verdadeiro" (é primo)</span>
          
          <span class="function">printf</span>(<span class="string">"Digite um número inteiro positivo: "</span>);
          <span class="function">scanf</span>(<span class="string">"%d"</span>, &numero);

          <span class="keyword">for</span>(<span class="type">int</span> i = <span class="number">2</span>; i &lt; numero; i++) { <span class="comment">// Começa a testar a partir a partir de 2</span>
              <span class="keyword">if</span> (numero % i == <span class="number">0</span>) {
                  <span class="comment">// Encontrou um divisor! Isso prova que o número NÃO é primo.</span>
                  eh_primo = <span class="number">0</span>;   <span class="comment">// "Desliga" a flag, sinalizando que não é primo.</span>
                  <span class="keyword">break</span>; <span class="comment">// Interrompe o loop precocemente, pois já temos a resposta.</span>
              }
          }

          <span class="keyword">if</span> (eh_primo == <span class="number">1</span> <span class="logic_operator">&&</span> numero > <span class="number">1</span>) {   <span class="comment">// Importante: 1 não é considerado primo</span>
              <span class="function">printf</span>(<span class="string">"%d é um número primo.\n"</span>, numero);
          } <span class="keyword">else</span> {
            <span class="function">printf</span>(<span class="string">"%d não é um número primo.\n"</span>, numero);
          }
        </code></pre>
      
    <h2>Quando usar for ou while? Uma Questão de Intenção</h2>
      <p>Com duas ferramentas disponíveis, como escolher?</p>
      <ul>
        <li>Use for: Quando o número de iterações é conhecido antes do loop começar. É ideal para "contagens": percorrer um array, repetir uma ação N vezes, processar uma quantidade fixa de elementos.</li>
        <li>Use while (ou do-while): Quando o número de iterações é desconhecido e depende de uma condição que só pode ser verificada durante a execução. É ideal para "eventos": ler entradas do usuário até que ele digite "sair", processar dados até encontrar um marcador de fim, esperar por uma condição externa.</li>
      </ul>
      <p>Em outras palavras, o for é para loops contados; o while é para loops condicionais.</p>

    <h2>Preparando o Terreno: Uma Palavra sobre Boas Práticas</h2>
      <p>Conforme seus programas ficam mais complexos, a organização se torna crucial. Os documentos de boas práticas destacam pontos essenciais:</p>
      <ul>
        <li>Indentação: Manter o código alinhado não é frescura. É uma ferramenta visual que permite que você e outras pessoas entendam a estrutura do programa num piscar de olhos. O bloco de código dentro de um for deve estar indentado em relação à linha do for.</li>
        <li>Nomes Significativos: Em vez de x ou a, use contador_alunos, soma_despesas, eh_numero_primo. O código se torna uma documentação de si mesmo.</li>
        <li>Comentários: Use para explicar o "porquê" de uma decisão complexa, não o "o quê" (o código já mostra o "o quê").</li>
      </ul>

<h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 6</h1>

        <div class="question" style="color:white;"></div>
        
        <div class="options">
            <button onclick="checkAnswer(0)">Opção 1</button>
            <button onclick="checkAnswer(1)">Opção 2</button>
            <button onclick="checkAnswer(2)">Opção 3</button>
        </div>

        <div class="feedback"></div>
    </div>

    <h2>Conclusão</h2>
    <p>Na próxima aula, levaremos esses conceitos a um novo patamar, explorando como armazenar múltiplos valores do mesmo tipo em uma única estrutura de dados: os vetores (arrays). Será a primeira vez que lidaremos com coleções de dados, um passo fundamental para programas verdadeiramente úteis. Por hoje, o desafio é internalizar esses padrões. Tente reescrever alguns dos seus programas anteriores usando for. Pense em situações onde um contador, um acumulador ou uma flag seriam úteis. A repetição é o coração da automação, e dominá-la é liberar um poder imenso em suas mãos.
    <br><br>Até a próxima!</p>

<div class="bottom-buttons">
<a href="bemvindos.php" class="btn-nav">⟵ Voltar</a>
      </div>
<div style="width:100%; text-align:center; margin: 80px 0;">
<a href="desafio6.php" class="btn-nav">Desafio →</a>
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
        question: "Qual é a principal vantagem do laço 'for' em relação ao 'while'?",
        options: [
            "É mais rápido na execução",
            "Organiza toda a lógica de controle do loop em uma única linha, reduzindo erros",
            "Pode ser usado para qualquer tipo de repetição, enquanto o while tem limitações"
        ],
        correctAnswer: 1  // Correto: organiza inicialização, condição e incremento em um lugar
    },
    {
        question: "Qual é a função de um 'contador' dentro de um loop?",
        options: [
            "Armazenar valores variáveis para cálculo de totais",
            "Responder à pergunta 'quantas vezes algo aconteceu?'",
            "Agir como um interruptor lógico para sinalizar eventos"
        ],
        correctAnswer: 1  // Correto: contador conta quantas vezes algo aconteceu
    },
    {
        question: "Qual é a diferença principal entre um contador e um acumulador?",
        options: [
            "O contador soma de 1 em 1, o acumulador soma valores variáveis",
            "O contador é sempre do tipo int, o acumulador pode ser float",
            "O acumulador é usado apenas em loops while, o contador em loops for"
        ],
        correctAnswer: 0  // Correto: contador soma 1 em 1, acumulador soma valores variáveis
    },
    {
        question: "Como funciona uma 'flag' ou sinalizador em programação?",
        options: [
            "Conta quantas vezes um evento específico ocorreu",
            "Acumula valores para calcular um total final",
            "Indica se um evento ocorreu pelo menos uma vez (sim/não)"
        ],
        correctAnswer: 2  // Correto: flag indica se algo aconteceu (0 ou 1)
    },
    {
        question: "Qual é a sintaxe correta do laço for em C?",
        options: [
            "for (condição; inicialização; incremento) {}",
            "for (inicialização; condição; incremento) {}",
            "for (incremento; condição; inicialização) {}"
        ],
        correctAnswer: 1  // Correto: for(inicialização; condição; incremento)
    },
    {
        question: "No exemplo de verificação de número primo, qual é a função do 'break'?",
        options: [
            "Interromper o programa completamente",
            "Interromper o loop precocemente quando já se tem a resposta",
            "Pular para a próxima iteração do loop"
        ],
        correctAnswer: 1  // Correto: interromper o loop quando encontra divisor
    },
    {
        question: "Quando devemos preferir usar 'while' em vez de 'for'?",
        options: [
            "Quando o número de iterações é conhecido antes do loop começar",
            "Quando o número de iterações é desconhecido e depende de uma condição durante a execução",
            "Sempre que precisamos de maior velocidade de execução"
        ],
        correctAnswer: 1  // Correto: while para loops condicionais (iterações desconhecidas)
    },
    {
        question: "No exemplo das despesas do mês, por que o valor é acumulado ANTES de ler a nova entrada?",
        options: [
            "Para garantir que o último valor negativo não seja incluído no total",
            "Para tornar o programa mais rápido",
            "Porque o do-while sempre executa pelo menos uma vez antes de testar"
        ],
        correctAnswer: 0  // Correto: para não incluir o valor negativo de saída
    },
    {
        question: "Qual deve ser o valor inicial típico de um acumulador?",
        options: [
            "1",
            "0",
            "O primeiro valor a ser processado"
        ],
        correctAnswer: 1  // Correto: acumulador começa em 0
    },
    {
        question: "Qual destes seria um exemplo ideal para usar um laço 'for'?",
        options: [
            "Percorrer um array de 100 elementos para calcular a média",
            "Ler entradas do usuário até que ele digite 'sair'",
            "Processar dados até encontrar um marcador de fim de arquivo"
        ],
        correctAnswer: 0  // Correto: for é ideal quando sabemos o número de iterações (100 elementos)
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
