<?php
$MODULO_ID = 7; // ID real do módulo
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
  <title>Módulo 7 - Cadeia de Caracteres</title>

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
  <source src="../assets/C-borg/animated/m7.mp4" type="video/quicktime">
  Seu navegador não suporta vídeos HTML5.
</video>
    <img id="imagemFinal" src="../assets/C-borg/animated/cborg-stand.gif" class="video-centralizado" style="display:none; opacity:0;">
</div>


<div class="align-page">
  <h1 style="font-size:30px; text-align:left;">Módulo 7: Das Letras às Palavras: Dominando Cadeias de Caracteres (Strings) em C</h1>
    <h2>Introdução</h2>
    <p>No módulo anterior, fizemos um grande avanço: aprendemos a trabalhar com vetores (ou arrays), que nos permitem armazenar múltiplos valores do mesmo tipo sob um único nome. Isso abriu a possibilidade de lidar com listas de dados, como as notas de uma turma, de maneira mais eficiente e organizada. Hoje, vamos levar esse conhecimento ainda mais longe, aplicando-o a um dos tipos de dados mais essenciais e universais em qualquer área da computação: o texto. Vamos explorar as cadeias de caracteres, ou, como são mais conhecidas, strings. Assim como um vetor de int armazena uma lista de números, uma string em C é basicamente um vetor de char. No entanto, há um detalhe especial que define uma string e que é crucial para manipulá-la corretamente. Vamos desvendar esse segredo e aprender como trabalhar com strings de forma eficaz.</p>

    <h2>Do Caractere Solitário à Cadeia Organizada: A Essência da String</h2>
      <p>Até agora, trabalhamos com caracteres de forma individual.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> letra_inicial = 'A';
      <span class="type">char</span> pontuação = '!';
    </code></pre>
      <p>Usamos scanf("%c", &letra) ou getchar() para lê-los, e printf("%c", letra) para exibi-los. É como trabalhar com tijolos isolados. Mas e se quisermos construir uma palavra, uma frase, um parágrafo? Precisamos de uma sequência, uma cadeia desses tijolos. É aí que entram as strings. Em C, não existe um tipo de dado mágico chamado "string". Em vez disso, usamos um vetor de caracteres, com uma regra crucial: a string deve terminar com o caractere especial '\0' (o caractere nulo). Pense nisso como uma lista de convidados. O vetor de caracteres é o salão de festas, com um número limitado de cadeiras. A string são os convidados efetivamente presentes. O caractere '\0' é a pessoa que se senta logo após o último convidado real, sinalizando para todos que "a partir daqui, não há mais ninguém da nossa lista". Sem esse marcador, o programa continuaria "lendo" o que quer que esteja na memória após o último caractere válido, levando a comportamentos imprevisíveis - um verdadeiro pesadelo para qualquer programador.</p>

    <h2>Declarando e Inicializando Strings: Dando Nome aos Nossos Textos</h2>
      <p>A declaração de uma string é idêntica à de qualquer vetor. Precisamos definir seu tamanho máximo.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> nome = [<span class="number">20</span>];   <span class="comment">// Uma string que pode ter até 19 caracteres + 1 para o '\0'</span>
      <span class="type">char</span> cidade = [<span class="number">30</span>];   <span class="comment">// Até 29 caracteres + '\0'</span>
    </code></pre>
      <p>Por que 19 e 29, e não 20 e 30? Porque precisamos sempre reservar um espaço para o terminador '\0. Esquecer disso é um erro comum que pode levar ao "estouro de buffer". Agora, como colocar texto dentro dela? Temos algumas formas:</p>
        <h3>Inicialização na Declaração (quando sabemos o conteúdo de antemão):</h3>
    <pre class="code-block"><code>
      <span class="type">char</span> saudacao[] = <span class="string">"Ola"</span>;   <span class="comment">// O compilador conta 'O','l','a','\0' e cria um vetor de tamanho 4.</span>
      <span class="type">char</span> nome[<span class="number">20</span>] = <span class="string">"Maria"</span>;   <span class="comment">// Inicializa com "Maria" e o restante fica com lixo de memória.</span>
    </code></pre>
      <p>Note o uso de aspas duplas (") para strings, contrastando com as aspas simples (') para caracteres simples.</p>
        <li><h3>Inicialização por Leitura (o usuário fornece o texto):</h3></li>
      <p>Aqui mora um dos primeiros desafios práticos. Podemos usar scanf com o especificador %s.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> nome[<span class="number">20</span>];
      <span class="function">printf</span>(<span class="string">"Digite uma palavra (sem espaço): "</span>);
      <span class="function">scanf</span>(<span class="string">"%s"</span>, &palavra);     <span class="comment">// Note: não usamos o '&' aqui. Por quê?</span>
    </code></pre>
      <p>Porque o nome de um vetor (palavra), sem colchetes, já representa o endereço de memória onde ele começa. O scanf precisa desse endereço para saber onde colocar os caracteres lidos. No entanto, o %s do scanf para de ler ao encontrar um espaço, uma tabulação ou uma quebra de linha. Para ler uma frase inteira com espaços, precisamos de outra ferramenta.</p>


      <h2>Atenção: A Função gets() - Uma Faca de Dois Gumes</h2>
        <p>Os materiais apresentam a função gets() para ler linhas completas de texto.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> frase[<span class="number">100</span>];
      <span class="function">printf</span>(<span class="string">"Digite uma frase: "</span>);
      <span class="function">gets</span>(frase);     <span class="comment">// Lê tudo até o usuário pressionar Enter.</span>
    </code></pre>
      <p>A grande vantagem do gets() é que ele lê espaços sem problemas. A enorme desvantagem é que ele é perigosamente ingênuo. Se o usuário digitar mais de 99 caracteres (lembrando do '\0), a função vai continuar escrevendo na memória além do espaço reservado para a string frase. Isso corrompe a memória do programa e pode fazê-lo travar ou se comportar de maneira estranha. Esse é o famoso estouro de buffer (buffer overflow), uma das falhas de segurança mais exploradas na história da computação. Por isso, o uso de gets() é geralmente desencorajado em código moderno. Em módulos futuros, exploraremos alternativas mais seguras, como fgets(). Por enquanto, usaremos gets() com a consciência de seus riscos, tomando cuidado para declarar strings com tamanho suficiente para a entrada esperada.</p>

    <h2>A Biblioteca string.h: Nossa Caixa de Ferramentas para Texto</h2>
      <p>Manipular strings "na mão", caractere por caractere, é possível com loops, mas é trabalhoso. Felizmente, C oferece uma biblioteca padrão repleta de funções úteis: a string.h. Para usá-la, basta incluir #include &lt;string.h> no topo do seu programa. Vamos conhecer algumas das ferramentas mais importantes desta caixa:</p>
        <h3>strcpy (string copy): Copia uma string para outra.</h3>
        <p>Como vimos nos materiais, não podemos simplesmente fazer string_destino = string_origem.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> origem[<span class="number">20</span>] = <span class="string">"Texto Original"</span>;
      <span class="type">char</span> destino[<span class="number">20</span>];   
      <span class="function">strcpy</span>(<span class="string">"destino, origem"</span>);    <span class="comment">// Copia o conteúdo de 'origem' para 'destino'.</span>
      <span class="function">printf</span>(<span class="string">"Destino: %s"</span>, destino);    <span class="comment">// Imprime: Destino: Texto original.</span>
    </code></pre>
        <h3>strcat (string concatenate): Junta duas strings.</h3>
        <p>"Concatena" significa anexar o conteúdo de uma string ao final de outra.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> saudacao[<span class="number">50</span>] = <span class="string">"Olá, "</span>;
      <span class="type">char</span> nome[<span class="number">20</span>] = <span class="string">"Mundo"</span>;   
      <span class="function">strcpy</span>(<span class="string">"saudacao, nome"</span>);    <span class="comment">// Anexa "Mundo" ao final de "Ola."</span>
      <span class="function">puts</span>(<span class="string">"saudacao"</span>);    <span class="comment">// Imprime: Ola, Mundo</span>
      <span class="comment">// A função 'puts' é similar ao 'printf' com '%s', mas adiciona uma quebra de linha automaticamente.</span>
    </code></pre>
        <h3>strlen (string length): Retorna o comprimento da string.</h3>
        <p>Ela conta quantos caracteres existem antes do '\0'.</p>
    <pre class="code-block"><code>
      <span class="type">char</span> palavra[] = <span class="string">"Cachorro"</span>;
      <span class="type">int</span> comprimento = <span class="function">strlen</span>(palavra)
      <span class="function">printf</span>(<span class="string">"'%s' tem %d letras."</span>, palavra, comprimento); <span class="comment">// Imprime: 'Cachorro' tem 8 letras.</span>
      </code></pre>

        <h3>strcmp (string compare): Compara duas strings.</h3>
        <p>Não podemos comparar strings usando ==. Precisamos de strcmp. Ela retorna:</p>
        <ul>
          <li>0 se as strings são iguais.</li>
          <li>Um valor maior que 0 se a primeira string for "maior" (em ordem alfabética/ASCII).</li>
          <li>Um valor menor que 0 se a primeira string for "menor".</li>
        </ul>
    <pre class="code-block"><code>
      <span class="type">char</span> senha_correta[] = <span class="string">"secreta123"</span>;
      <span class="type">char</span> senha_digitada[<span class="number">20</span>];

      <span class="function">printf</span>(<span class="string">"Digite a senha: "</span>);
      <span class="function">scanf</span>(<span class="string">"%s"</span>, senhadigitada);

      <span class="keyword">if</span> (<span class="function">strcmp</span>(senha_digitada, senha_correta) == <span class="number">0</span>) {
          <span class="function">printf</span>(<span class="string">"Acesso permitido!\n"</span>);
      } <span class="keyword">else</span> {
          <span class="function">printf</span>(<span class="string">"Senha Incorreta!\n"</span>);
      }
        </code></pre>

    <h2>Unindo Tudo: Um Exemplo Prático</h2>
      <p>Vamos criar um programa que busca um caractere dentro de um texto, como no exemplo final do material.</p>
    <pre class="code-block"><code>
      #<span class="keyword">include</span> <span class="string">&lt;stdio.h&gt;</span>
      #<span class="keyword">include</span> <span class="string">&lt;string.h&gt;</span>    <span class="comment">// Para usar strlen</span>

      <span class="type">int</span> <span class="function">main</span>() {
            <span class="type">char</span> texto[<span class="number">100</span>];
            <span class="type">char</span> caractere_procurado;
            <span class="type">int</span> i, achou = <span class="number">0</span>;   <span class="comment">// Usando uma "flag" (sinalizador)</span>

            <span class="function">printf</span>(<span class="string">"Digite um texto: "</span>);
            <span class="function">gets</span>(texto);    <span class="comment">// Cuidado! Assume-se que o texto terá menos de 100 caracteres.</span>

            <span class="function">printf</span>(<span class="string">"Qual caractere deseja procurar? "</span>);
            <span class="function">scanf</span>(<span class="string">"%c"</span>, &caractere_procurado);

            <span class="comment">// Percorre a string caractere por caractere</span>
            <span class="keyword">for</span> (i = <span class="number">0</span>; i &lt; <span class="function">strlen</span>(texto); i++){
                <span class="keyword">if</span> (texto[i] == caractere_procurado) {
                    <span class="function">printf</span>(<span class="string">"Caractere '%c' não foi encontrado.\n"</span>, caractere_procurado);
                    achou = <span class="number">1</span>;    <span class="comment">// Ativa a flag</span>
                }
            }
            <span class="keyword">if</span> (!achou) { <span class="comment">// Se achou continua 0 (falso)</span>
                <span class="function">printf</span>(<span class="string">"Caracteres '%c' não foi encontrado.\n"</span>, caractere_procurado);
            }           
            <span class="keyword">return</span> <span class="number">0</span>;
      }
      </code></pre>
      <p>Este exemplo combina tudo: declaração de string, leitura com gets, acesso a caracteres individuais (texto[i]), uso de uma flag e a função strlen para controlar o loop. No próximo módulo, continuaremos nossa exploração das estruturas de dados, provavelmente avançando para os arranjos multidimensionais (matrizes), que nos permitirão trabalhar com tabelas e grades de dados. Por hoje, o importante é praticar. Crie pequenos programas que leiam nomes, comparem palavras, ou juntem textos. Familiarize-se com a ideia de que uma string é um vetor terminado em '\0'. Quando isso se tornar natural, você terá dominado um dos conceitos mais importantes para a criação de programas que interagem de verdade com o mundo.</p>
      <h2>Exercício para Fixação</h2>
  <div class="quiz-container">
        <h1>Quiz - Módulo 7</h1>

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
<a href="desafio7.php" class="btn-nav">Desafio →</a>
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
        question: "Em C, o que é uma string?",
        options: [
            "Um tipo de dado primitivo específico para texto",
            "Uma estrutura de dados que só pode ser criada com a biblioteca string.h",
            "Um vetor de caracteres terminado pelo caractere '\\0'"
        ],
        correctAnswer: 2
    },
    {
        question: "Qual caractere especial deve terminar toda string em C?",
        options: [
            "'\\0' (caractere nulo)",
            "'\\n' (nova linha)",
            "' ' (espaço em branco)"
        ],
        correctAnswer: 0
    },
    {
        question: "Se declaramos char nome[20], quantos caracteres úteis podemos armazenar?",
        options: [
            "21 caracteres incluindo '\\0'",
            "19 caracteres + '\\0'",
            "20 caracteres"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual é o problema da função gets() mencionada no módulo?",
        options: [
            "Pode causar estouro de buffer (buffer overflow) se a entrada for muito longa",
            "Não funciona com strings que contêm números",
            "Não consegue ler espaços em branco"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual função da biblioteca string.h é usada para copiar uma string para outra?",
        options: [
            "strcmp()",
            "strcpy()",
            "strcat()"
        ],
        correctAnswer: 1
    },
    {
        question: "Qual função retorna o comprimento de uma string (número de caracteres antes do '\\0')?",
        options: [
            "strcount()",
            "strlen()",
            "strsize()"
        ],
        correctAnswer: 1
    },
    {
        question: "Para comparar se duas strings são iguais em C, usamos:",
        options: [
            "strcmp(string1, string2) == 0",
            "string1.equals(string2)",
            "string1 == string2"
        ],
        correctAnswer: 0
    },
    {
        question: "Qual função concatena (junta) duas strings?",
        options: [
            "strjoin()",
            "strconcat()",
            "strcat()"
        ],
        correctAnswer: 2
    },
    {
        question: "No exemplo de busca de caractere, o que representa a variável 'achou'?",
        options: [
            "Um contador que armazena quantas vezes o caractere apareceu",
            "Uma flag (sinalizador) que indica se o caractere foi encontrado",
            "Um acumulador que guarda a posição do caractere"
        ],
        correctAnswer: 1
    },
    {
        question: "Por que não podemos usar o operador '=' para copiar strings em C?",
        options: [
            "Porque C não suporta operações com strings",
            "Porque o operador '=' só funciona com números",
            "Porque strings são arrays e arrays não podem ser copiados com '='"
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
