<?php
$MODULO_ID = 10; // ID real do módulo
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
  <title>Módulo 10 - Ponteiros e Passagem por Referência em Funções</title>

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
  <source src="../assets/C-borg/animated/m10.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>

<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 10: Além da Cópia: Ponteiro e Passagem por Referência em Funções</h1>
    <p>Olá! No módulo anterior, demos um salto fundamental na organização do nosso código ao aprender a criar e utilizar funções. Vimos como elas nos permitem dividir problemas complexos em partes menores, reutilizar código e tornar nossos programas mais legíveis. No entanto, encontramos uma limitação importante: quando passamos variáveis para uma função, o que ocorre é uma cópia dos valores. Isso significa que qualquer alteração feita dentro da função não afeta a variável original. Mas e se quisermos que uma função modifique diretamente uma variável que criamos na main()? E se precisarmos que uma função retorne mais de um valor? Para isso, precisamos ir além da simples cópia de valores. Precisamos aprender a trabalhar com endereços de memória e ponteiros. Este é um dos conceitos mais poderosos (e às vezes intimidadores) da linguagem C, e dominá-lo é o que separa um programador iniciante de um intermediário. Vamos desmistificá-lo juntos.</p>
    <h2>A Limitação da Passagem por Valor: Quando uma Cópia não Basta</h2>
      <p>Vamos revisitar rapidamente o exemplo clássico que ilustra a limitação da passagem por valor: uma função para trocar o valor de duas variáveis.</p>
      <ul>
    <pre class="code-block"><code>
      <span class="comment">// Tentativa (frustrada) de trocar valores usando passagem por valor</span>
      <span class="type">void</span> <span class="function">troca</span>(<span class="type">int</span> x, <span class="type">int</span> y) {
          <span class="type">int</span> temp = x;
          x = y;
          y = temp;
          <span class="function">printf</span>(<span class="string">"Dentro da função: x=%d, y=%d\n"</span>, x, y); <span class="comment">// Os valores são trocados aqui</span>
      }

      <span class="type">int</span> <span class="function">main</span>() {
          <span class="type">int</span> a = 5, b = 10;
          <span class="function">printf</span>(<span class="string">"Antes da troca: a=%d, b=%d\n"</span>, a, b); <span class="comment">// Saída: 5, 10</span>
          <span class="function">troca</span>(a,b);  <span class="comment">// Passamos os VALORES de 'a' e 'b' (5, 10)</span>
          <span class="function">printf</span>(<span class="string">"Depois da troca: a=%d, b=%d\n"</span>, a, b); <span class="comment">// Saída: 5, 10 (Nada mudou!)</span>
          <span class="keyword">return</span> <span class="number">0</span>;
      }
    </code></pre>
      </ul>
      <p>Por que a e b não foram alterados? Porque quando chamamos troca(a, b), os valores 5 e 10 são copiados para as variáveis locais x e y da função troca. A função trabalha com essas cópias. A troca acontece perfeitamente dentro do universo da função troca, mas quando ela termina, as cópias x e y são destruídas, e as variáveis originais a e b na main() permanecem intactas. Precisamos de um mecanismo que permita à função troca acessar e modificar o local de memória onde a e b estão guardadas, e não apenas uma cópia dos seus valores. É aí que entram os ponteiros.</p>

    <h2>Ponteiros: Os "Mapas do Tesouro" da Memória</h2>
      <p>Um ponteiro é, em essência, uma variável como qualquer outra. A diferença crucial é que, enquanto uma variável comum armazena um valor (como o número 5), um ponteiro armazena um endereço de memória. Vamos usar uma analogia: imagine que a memória do computador é uma grande cidade, e cada variável é uma casa. O valor da variável é o que está dentro da casa. O endereço da variável é o "número da casa" na rua.</p>
      <ul>
        <li>int a = 5;: É como ter uma casa na "Rua da Memória, nº 1000", e dentro dela está o número 5.</li>
        <li>Um ponteiro é como um papel onde anotamos o endereço "Rua da Memória, nº 1000". Esse papel não contém o número 5, mas sim a localização de onde o número 5 pode ser encontrado.</li>
      </ul>
      <p>Em C, usamos o operador & (e comercial) para obter o endereço de uma variável, e o operador * (asterisco) para declarar um ponteiro e para acessar o valor armazenado no endereço para o qual ele aponta (o que chamamos de "desreferenciar" o ponteiro).</p>
      <ul>
    <pre class="code-block"><code>
      <span class="type">int</span> a = <span class="number">5</span>;  <span class="comment">// Uma variável comum, que armazena o valor 5.</span>
      <span class="type">int</span> *ponteiro;  <span class="comment">// Declara um ponteiro para um inteiro. ASTERISCO indica que é um ponteiro.</span>

      ponteiro = &a <span class="comment">// // Atribui ao ponteiro o ENDEREÇO de 'a'. Agora, ponteiro "aponta" para 'a'.</span>

      <span class="function">printf</span>(<span class="string">"Valor de a: %d\n"</span>, a); <span class="comment">// Imprime: 5</span>
      <span class="function">printf</span>(<span class="string">"Endereço de a: %p\n"</span>, &a); <span class="comment">// Imprime: 5Imprime algo como: 0x7ffd42a (o endereço)</span>
      <span class="function">printf</span>(<span class="string">"Conteúdo do ponteiro: %p\n"</span>, ponteiro); <span class="comment">// Imprime o MESMO endereço de 'a'</span>
      <span class="function">printf</span>(<span class="string">"Valor apontado por ponteiro: %d\n"</span>, *ponteiro); <span class="comment">// Imprime: 5 (o valor DE 'a')</span>
      <span class="comment">// O ASTERISCO aqui é usado para ACESSAR o valor no endereço armazenado.</span> 

      *ponteiro = <span class="number">10</span>  <span class="comment">// Altera o valor NO ENDEREÇO apontado por 'ponteiro'. Como ele aponta para 'a', altera o valor de 'a'.</span> 
      <span class="function">printf</span>(<span class="string">"Novo valor de a: %d\n"</span>, a); <span class="comment">// Imprime: 10</span>
    </code></pre>
      </ul>
      <p>Esta é a ideia fundamental: com um ponteiro, podemos não apenas saber onde uma variável vive, mas também interagir com ela à distância.</p>
    
    <h2>Passagem por Referência: Modificando Variáveis à Distância</h2>
      <p>Agora, vamos consertar nossa função troca. Em vez de passar os valores de a e b, vamos passar os seus endereços. Dizemos que estamos passando os parâmetros por referência.</p>

      <ul>
    <pre class="code-block"><code>
      <span class="comment">// Função correta para trocar valores usando passagem por REFERÊNCIA (ponteiros)</span>
      <span class="type">void</span> <span class="function">troca</span>(<span class="type">int</span> *x, <span class="type">int</span> *y) { <span class="comment">// Recebe dois PONTEIROS para inteiros</span>
          <span class="type">int</span> temp = *x;  <span class="comment">// temp recebe o valor APONTADO por x (o valor de 'a')</span>
          x* = y*;  <span class="comment">// O valor APONTADO por x (a) recebe o valor APONTADO por y (b)</span>
          y* = temp;  <span class="comment">// O valor APONTADO por y (b) recebe o valor de temp</span>
      }

      <span class="type">int</span> <span class="function">main</span>() {
          <span class="type">int</span> a = 5, b = 10;
          <span class="function">printf</span>(<span class="string">"Antes da troca: a=%d, b=%d\n"</span>, a, b); <span class="comment">// Saída: 5, 10</span>
          <span class="function">troca</span>(&a,&b);  <span class="comment">// Passamos os ENDEREÇOS de 'a' e 'b' usando o operador &</span>
          <span class="function">printf</span>(<span class="string">"Depois da troca: a=%d, b=%d\n"</span>, a, b); <span class="comment">// Saída: 10, 5 (Agora funciona!)</span>
          <span class="keyword">return</span> <span class="number">0</span>;
      }
    </code></pre>
      </ul>

      <p>Vamos dissecar a mágica:</p>
      <ul>
        <li>troca(&a, &b);: A main() envia os endereços de a e b para a função.</li>
        <li>void troca(int *x, int *y);: A função recebe esses endereços e os armazena nos ponteiros x e y. Agora, x contém o endereço de a e y contém o endereço de b.</li>
        <li>int temp = *x;: Lê-se "temp recebe o valor contido no endereço armazenado em x". Isso é equivalente a temp = a;.</li>
        <li>*x = *y;: "O valor no endereço x recebe o valor no endereço y". Equivale a a = b;.</li>
        <li>*y = temp;: "O valor no endereço y recebe o valor de temp". Equivale a b = temp;.</li>
      </ul>
      <p>Dessa forma, a função troca opera diretamente sobre as variáveis a e b da main(), mesmo estando em um escopo diferente.</p>
    
    <h2>Aplicação Prática: Funções que Retornam Múltiplos Valores</h2>
      <p>A passagem por referência é a solução para um problema comum: como fazer uma função retornar mais de um valor? Um exemplo clássico é uma função para calcular as raízes de uma equação do segundo grau, que precisa retornar dois valores: x1 e x2.</p>
      <ul>
    <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
      #<span class="keyword">include</span> <span class="string">&lt;string.h&gt;</span>

      <span class="comment">// A função não retorna valor (void), mas modifica x1 e x2 através de ponteiros.</span>
      <span class="type">void</span> <span class="function">bhaskara</span>(<span class="type">float</span> a, <span class="type">float</span> b, <span class="type">float</span> c, <span class="type">float</span> *x1, <span class="type">float</span> *x2) {
          <span class="type">float</span> delta = b*b - 4*a*c;
          *x1 = (-b + <span class="function">sqrt</span>(delta)) / (<span class="number">2</span>*a);
          *x2 = (-b - <span class="function">sqrt</span>(delta)) / (<span class="number">2</span>*a);
      }

      <span class="type">int</span> <span class="function">main</span>() {
          <span class="type">float</span> coef_a, coef_b, coef_c, raiz1, raiz2;
          <span class="function">printf</span>(<span class="string">"Digite os coeficientes 'a', 'b' e 'c': "</span>);
          <span class="function">scanf</span>(<span class="string">"%f %f %f"</span>, &coef_a, &coef_b, &coef_c);
      
          <span class="comment">// Passa os endereços de raiz1 e raiz2 para que bhaskara possa preenchê-los.</span>
          <span class="function">bhaskara</span>(coef_a, coef_b, coef_c, &raiz1, &raiz2);

          <span class="function">printf</span>(<span class="string">"As raízes são: x1 = %.2f e x2 = %.2f\n"</span>, raiz1, raiz2);
          <span class="keyword">return</span> <span class="number">0</span>;
      }
    </code></pre>
      </ul>
      <p>Neste padrão, os parâmetros a, b e c são passados por valor (são apenas entradas), enquanto x1 e x2 são passados por referência para servirem como saídas da função.</p>
    <h2>Ponteiros e Vetores: Uma Relação Íntima</h2>
      <p>Há uma conexão profunda e natural entre ponteiros e vetores. Em C, o nome de um vetor é, na verdade, um ponteiro constante para o primeiro elemento do vetor.</p>
      <ul>
    <pre class="code-block"><code>
      <span class="type">int</span> vetor[<span class="number">5</span>] = {<span class="number">10</span>, <span class="number">20</span>, <span class="number">30</span>, <span class="number">40</span>, <span class="number">50</span>};
      <span class="function">printf</span>(<span class="string">"Endereço do vetor: %p\n"</span>, vetor);
      <span class="function">printf</span>(<span class="string">"Endereço do vetor[0]: %p\n"</span>, &vetor[0]);  <span class="comment">// Será o MESMO valor da linha acima.</span>
    </code></pre>
    </ul>

      <p>Isso significa que quando passamos um vetor para uma função, estamos automaticamente passando uma referência (o endereço do primeiro elemento). Por isso, funções que modificam vetores o fazem diretamente no original.</p>
      <ul>
    <pre class="code-block"><code>
      <span class="type">void</span> <span class="function">incrementa_vetor</span>(<span class="type">int</span> *v, <span class="type">int</span> tamanho) { <span class="comment">// ou int v[]</span>
          <span class="keyword">for</span>(<span class="type">int</span> i = <span class="number">0</span>; i &lt; tamanho; i++) {
              v[i] = v[i] + <span class="number">1</span>;  <span class="comment">// Modifica o vetor original;</span>
          }
      }

      <span class="type">int</span> <span class="function">main</span>() {
          <span class="type">int</span> meuvetor[<span class="number">3</span>] = {<span class="number">1</span>, <span class="number">2</span>, <span class="number">3</span>};
          <span class="function">incrementa_vetor</span>(meuvetor, <span class="number">3</span>);  <span class="comment">// Passa 'meuvetor' (que é um endereço)</span>
          <span class="comment">// meuvetor agora contém {2, 3, 4}</span>
      }
    </code></pre>
      </ul>
      
      <p>No próximo módulo, exploraremos como passar matrizes para funções, um tema que builds upon este conhecimento fundamental de ponteiros. Por hoje, o mais importante é praticar a mentalidade do ponteiro. Desenhe diagramas de memória. Trace a execução de programas simples com ponteiros no papel. Quando você internalizar que um ponteiro é um "guia" para um local na memória, e que *ponteiro é a forma de visitar esse local, um novo mundo de possibilidades se abrirá em sua programação.</p>

    <h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 10</h1>

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
<a href="desafio10.php" class="btn-nav">Desafio →</a>
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
        question: "Qual é a principal limitação da passagem por valor em funções C?",
        options: [
            "A função trabalha com cópias, não podendo modificar as variáveis originais",
            "Não permite usar tipos de dados diferentes",
            "A função não pode acessar os valores passados"
        ],
        correctAnswer: 0
    },
    {
        question: "O que armazena uma variável do tipo ponteiro em C?",
        options: [
            "Um endereço de memória",
            "Um valor numérico qualquer",
            "Uma cópia de outra variável"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual operador é usado para obter o endereço de uma variável em C?",
        options: [
            "# (cerquilha)",
            "& (e comercial)",
            "* (asterisco)"
        ],
        correctAnswer: 1
    },
    {
        question: "Na função 'troca' corrigida, por que usamos 'int *x' como parâmetro?",
        options: [
            "Para retornar dois valores",
            "Para receber o endereço da variável",
            "Para receber o valor da variável"
        ],
        correctAnswer: 1
    },
    {
        question: "O que significa 'desreferenciar' um ponteiro?",
        options: [
            "Liberar a memória apontada",
            "Acessar o valor armazenado no endereço apontado",
            "Atribuir um novo endereço ao ponteiro"
        ],
        correctAnswer: 1
    },
    {
        question: "Como corrigimos a função 'troca' para que funcione corretamente?",
        options: [
            "Passando os endereços das variáveis usando & e recebendo com ponteiros",
            "Usando variáveis globais",
            "Declarando a função como 'int' em vez de 'void'"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual é a relação entre vetores e ponteiros em C?",
        options: [
            "Vetores e ponteiros são conceitos completamente independentes",
            "Ponteiros não podem ser usados com vetores",
            "O nome de um vetor é um ponteiro constante para seu primeiro elemento"
        ],
        correctAnswer: 2
    },
    {
        question: "Por que a passagem por referência permite que uma função retorne múltiplos valores?",
        options: [
            "Porque os parâmetros ponteiros permitem modificar variáveis no escopo chamador",
            "Porque a função pode usar vários comandos 'return'",
            "Porque C permite funções com múltiplos tipos de retorno"
        ],
        correctAnswer: 0
    },
    {
        question: "No exemplo de Bhaskara, por que 'x1' e 'x2' são passados como ponteiros?",
        options: [
            "Para evitar cópias desnecessárias dos valores",
            "Para economizar memória",
            "Para permitir que a função modifique seus valores no main()"
        ],
        correctAnswer: 2
    },
    {
        question: "Quando passamos um vetor para uma função em C, o que realmente estamos passando?",
        options: [
            "O tamanho total do vetor em bytes",
            "Uma cópia de todos os elementos do vetor",
            "O endereço do primeiro elemento do vetor"
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
