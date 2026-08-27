# Frameworks: uma base pronta para desenvolver aplicações

Desenvolver uma aplicação envolve vários trabalhos: organizar arquivos, receber requisições, validar dados, acessar o banco, montar interfaces, autenticar usuários e tratar erros.

Muitos desses problemas aparecem repetidamente em diferentes projetos. Os **frameworks** oferecem estruturas, ferramentas e convenções que ajudam a resolvê-los de maneira organizada.

Frameworks estarão presentes em diversos momentos da vida profissional de um desenvolvedor. Mais importante do que decorar os comandos de uma ferramenta é compreender por que ela existe, como organiza uma aplicação e quais problemas procura resolver.

## Índice

1. [O que é um framework?](#1-o-que-é-um-framework)
2. [Framework e biblioteca](#2-framework-e-biblioteca)
3. [Frameworks no desenvolvimento Web](#3-frameworks-no-desenvolvimento-web)
4. [Benefícios dos frameworks](#4-benefícios-dos-frameworks)
5. [Curva de aprendizagem e produtividade](#5-curva-de-aprendizagem-e-produtividade)
6. [Frameworks e ferramentas conhecidas](#6-frameworks-e-ferramentas-conhecidas)
7. [Frameworks, novidades e modismos](#7-frameworks-novidades-e-modismos)
8. [Como escolher um framework?](#8-como-escolher-um-framework)
9. [Como aprender um framework?](#9-como-aprender-um-framework)
10. [Quadro de consulta rápida](#10-quadro-de-consulta-rápida)

## 1. O que é um framework?

Um **framework** é uma estrutura reutilizável que serve como base para desenvolver determinado tipo de aplicação.

Ele normalmente oferece:

- uma organização inicial para o projeto;
- convenções sobre onde colocar cada parte do código;
- ferramentas para tarefas frequentes;
- componentes reutilizáveis;
- pontos nos quais o desenvolvedor acrescenta o código específico da aplicação.

Podemos comparar um framework a um conjunto de peças e instruções para montar um móvel. As peças mais comuns já estão disponíveis e existe uma forma recomendada de organizá-las. Entretanto, o desenvolvedor ainda precisa decidir o que será construído e implementar as características específicas do projeto.

No Laravel, por exemplo, já existem locais convencionais para rotas, Models, Controllers, Views, migrations e configurações. O desenvolvedor não precisa inventar uma organização diferente para cada projeto.

Um framework não entrega a aplicação pronta. Ele fornece a base sobre a qual a aplicação será construída.

### Framework não é linguagem de programação

Framework e linguagem não são a mesma coisa.

Uma **linguagem de programação** fornece a sintaxe e os recursos usados para escrever programas. Um framework utiliza uma linguagem para oferecer uma estrutura de desenvolvimento.

| Linguagem ou plataforma | Exemplo de framework |
|---|---|
| PHP | Laravel |
| Python | Django |
| Java | Spring Boot |
| C# e .NET | ASP.NET Core |
| JavaScript ou TypeScript | Angular, Vue, Express e NestJS |

Quando desenvolvemos com Laravel, continuamos programando em PHP. O Laravel organiza e simplifica o trabalho, mas não elimina a necessidade de conhecer funções, classes, objetos, decisões e outros fundamentos da linguagem.

> Conhecer somente os comandos de um framework permite repetir tutoriais. Conhecer a linguagem e os fundamentos permite compreender, adaptar e corrigir a aplicação.

## 2. Framework e biblioteca

Frameworks e bibliotecas fornecem código reutilizável, mas possuem alcances e formas de uso diferentes.

| Característica | Biblioteca | Framework |
|---|---|---|
| Finalidade | Resolver uma tarefa ou grupo específico de tarefas | Organizar uma parte significativa da aplicação |
| Estrutura do projeto | Geralmente não determina | Normalmente propõe ou determina |
| Fluxo da aplicação | Nosso código chama a biblioteca | O framework controla parte do fluxo e chama nosso código |
| Compromisso com a ferramenta | Menor e localizado | Maior, pois o projeto segue suas convenções |
| Exemplos | React, Chart.js e bibliotecas de manipulação de arquivos | Laravel, Angular, Vue, Django e Spring Boot |

Uma regra prática bastante conhecida é:

> Com uma biblioteca, seu código chama a ferramenta. Com um framework, a ferramenta também chama seu código.

### Exemplo de biblioteca

No exemplo conceitual abaixo, nosso código decide quando chamar a biblioteca:

```javascript
const resultado = biblioteca.validar(dados);
```

### Exemplo de framework

Em uma aplicação Laravel, registramos o código que deverá atender determinado endereço:

```php
Route::get('/livros', function () {
    return view('livros.index');
});
```

Não chamamos essa função diretamente. Quando uma requisição para `/livros` chega, o Laravel identifica a rota e executa o código registrado.

Esse comportamento é chamado de **inversão de controle**: o framework coordena o fluxo geral e executa nosso código nos momentos apropriados.

### A separação nem sempre é perfeita

Algumas ferramentas ficam entre as duas categorias ou fazem parte de ecossistemas maiores.

O React, por exemplo, apresenta-se oficialmente como uma **biblioteca para construir interfaces**. Entretanto, é frequentemente chamado informalmente de framework porque costuma ser utilizado com roteamento, gerenciamento de estado e outras ferramentas.

O mais importante é compreender o alcance e o funcionamento da ferramenta, e não apenas o nome dado a ela. A [MDN também apresenta essa diferença entre bibliotecas e frameworks](https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Frameworks_libraries/Introduction).

## 3. Frameworks no desenvolvimento Web

Frameworks Web podem atuar no front-end, no back-end ou nas duas partes da aplicação.

### Frameworks de front-end

Eles auxiliam na construção da interface executada ou processada pelo navegador.

Podem oferecer:

- componentes reutilizáveis;
- atualização da interface;
- tratamento de eventos;
- gerenciamento do estado da tela;
- validação de formulários;
- navegação entre telas;
- organização do HTML, CSS e JavaScript;
- ferramentas de compilação e testes.

Essas ferramentas não criam novas capacidades para o navegador. Elas ajudam o desenvolvedor a utilizar melhor os recursos que já existem na plataforma Web.

### Frameworks de back-end

Eles auxiliam no desenvolvimento da parte executada no servidor.

Podem oferecer:

- sistema de rotas;
- Controllers;
- validação de dados;
- autenticação e autorização;
- acesso ao banco de dados;
- ORM;
- templates;
- sessões;
- tratamento de erros;
- logs;
- testes;
- envio de e-mails;
- construção de APIs.

Nem todo framework fornece todos esses recursos. Alguns são completos e possuem muitas ferramentas integradas. Outros fornecem apenas um núcleo pequeno, que pode ser complementado por bibliotecas e extensões.

A [MDN apresenta as principais tarefas simplificadas pelos frameworks de servidor](https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Server-side/First_steps/Web_frameworks).

## 4. Benefícios dos frameworks

### Produtividade

Tarefas comuns já possuem soluções prontas ou caminhos recomendados. O desenvolvedor pode dedicar mais tempo às regras específicas da aplicação.

No Laravel, por exemplo, não precisamos criar do zero os sistemas de rotas, templates, migrations e acesso ao banco.

### Padronização

O framework estabelece uma estrutura conhecida para pastas, arquivos e componentes.

Essa padronização ajuda a responder perguntas como:

- Onde ficam as rotas?
- Onde estão as regras de acesso?
- Qual classe recebe uma requisição?
- Onde estão as configurações?
- Como os dados são acessados?

### Manutenção e trabalho em equipe

Quando os integrantes conhecem as convenções do framework, conseguem localizar e compreender partes do sistema com maior facilidade.

Um novo integrante não precisa aprender uma organização completamente inventada pela equipe para cada projeto.

### Reutilização

Frameworks evitam que problemas comuns sejam implementados repetidamente. Muitas soluções já foram utilizadas e testadas em diferentes projetos.

### Ecossistema e comunidade

Frameworks consolidados normalmente possuem:

- documentação;
- fóruns e comunidades;
- extensões;
- tutoriais;
- ferramentas de desenvolvimento;
- integração com serviços externos;
- atualizações e correções.

Uma comunidade ativa é importante tanto para o aprendizado quanto para a continuidade da tecnologia.

### Segurança

Frameworks podem oferecer mecanismos contra problemas conhecidos, como falsificação de requisições, injeção de código e armazenamento inadequado de senhas.

Isso não torna uma aplicação automaticamente segura. O desenvolvedor precisa utilizar os recursos corretamente, validar os dados e manter o framework atualizado.

### Um framework não é obrigatório

Nem todo projeto precisa de um framework completo.

Um site com poucas páginas e quase nenhuma interação, por exemplo, pode não precisar de um grande framework JavaScript. Utilizar uma ferramenta apenas porque ela está popular pode aumentar desnecessariamente a complexidade.

## 5. Curva de aprendizagem e produtividade

A produtividade não costuma aumentar imediatamente.

No começo, é necessário aprender:

- a estrutura de pastas;
- os comandos;
- as convenções;
- o ciclo de execução;
- os principais componentes;
- a forma recomendada de escrever o código.

Por isso, o primeiro projeto com um framework pode parecer mais lento do que uma implementação pequena feita diretamente com a linguagem.

| Etapa | Situação comum |
|---|---|
| Primeiro contato | Muitos termos, arquivos e comandos desconhecidos |
| Aprendizagem inicial | O aluno começa a reconhecer a estrutura |
| Prática | As convenções passam a fazer sentido |
| Produtividade | Tarefas comuns são realizadas mais rapidamente |
| Experiência | O desenvolvedor consegue adaptar e investigar problemas |

Depois que o desenvolvedor conhece a ferramenta, seus componentes e convenções podem reduzir bastante o tempo necessário para implementar e manter funcionalidades.

Existe, portanto, uma troca:

- no início, há um custo de aprendizagem;
- depois, o conhecimento acumulado aumenta a produtividade.

Esse ganho costuma ser mais visível em aplicações maiores, na manutenção e no trabalho em equipe.

## 6. Frameworks e ferramentas conhecidas

As listas seguintes apresentam exemplos consolidados, não um ranking definitivo. A popularidade das tecnologias muda com o tempo.

### Front-end

| Ferramenta | Classificação | Característica geral |
|---|---|---|
| [Angular](https://angular.dev/) | Framework | Estrutura ampla baseada em componentes e TypeScript |
| [Vue](https://vuejs.org/) | Framework progressivo | Pode ser adotado gradualmente |
| [Svelte](https://svelte.dev/) | Framework de interface | Utiliza compilação para produzir o JavaScript |
| [Next.js](https://nextjs.org/docs) | Framework baseado em React | Acrescenta rotas, renderização e recursos para aplicações completas |
| [React](https://react.dev/) | Biblioteca | Constrói interfaces por meio de componentes |
| [Bootstrap](https://getbootstrap.com/) | Ferramenta de front-end | Oferece grid, estilos e componentes visuais |
| [Tailwind CSS](https://tailwindcss.com/) | Framework CSS | Fornece classes utilitárias para construir estilos |

### Back-end

| Framework | Linguagem ou plataforma | Característica geral |
|---|---|---|
| [Laravel](https://laravel.com/docs/13.x) | PHP | Oferece rotas, Blade, Eloquent, migrations e outros recursos |
| [Django](https://docs.djangoproject.com/) | Python | Possui diversas funcionalidades integradas |
| [Spring Boot](https://spring.io/projects/spring-boot) | Java | Muito utilizado em aplicações e serviços corporativos |
| [ASP.NET Core](https://learn.microsoft.com/aspnet/core/) | C# e .NET | Framework multiplataforma para aplicações Web e APIs |
| [Express](https://expressjs.com/) | JavaScript e Node.js | Framework minimalista e flexível |
| [NestJS](https://docs.nestjs.com/) | TypeScript e Node.js | Organiza aplicações por meio de módulos e componentes |

> Node.js não é um framework: é um ambiente para executar JavaScript fora do navegador. PHP, Python, Java, C# e JavaScript são linguagens.

A pesquisa anual do Stack Overflow pode ajudar a acompanhar tecnologias utilizadas e desejadas pelos desenvolvedores, mas não deve ser o único critério de escolha. [Stack Overflow Developer Survey 2025](https://survey.stackoverflow.co/2025/technology).

## 7. Frameworks, novidades e modismos

Novos frameworks aparecem frequentemente. Alguns trazem boas soluções e conquistam comunidades duradouras. Outros recebem muita atenção por pouco tempo e depois perdem usuários, suporte ou desenvolvimento ativo.

Essa popularidade intensa e passageira costuma ser chamada de **hype**.

Uma tecnologia nova não é necessariamente ruim. O problema é escolhê-la apenas porque está sendo comentada nas redes sociais.

Antes de seguir uma novidade, pergunte:

- Ela resolve um problema relevante?
- Está sendo utilizada em projetos reais?
- Possui documentação de qualidade?
- Tem comunidade e manutenção ativa?
- Existe um caminho de atualização?
- A equipe consegue aprendê-la e mantê-la?
- O benefício compensa a troca de tecnologia?

### Os fundamentos duram mais

Frameworks mudam, mas vários conhecimentos continuam úteis:

- lógica de programação;
- HTML, CSS e JavaScript;
- HTTP;
- PHP ou outra linguagem de back-end;
- orientação a objetos;
- SQL e bancos de dados;
- arquitetura cliente-servidor;
- segurança;
- testes;
- Git.

Uma rota, um Controller ou um ORM podem possuir sintaxes diferentes em Laravel, Django ou Spring Boot, mas resolvem problemas semelhantes.

> Não tente aprender todos os frameworks que surgem. Aprenda bem os fundamentos e utilize um framework para compreender conceitos que poderão ser aproveitados em outras tecnologias.

## 8. Como escolher um framework?

Não existe um framework que seja o melhor para todos os projetos.

| Critério | Pergunta |
|---|---|
| Problema | A ferramenta é adequada ao tipo e ao tamanho da aplicação? |
| Equipe | A equipe conhece a linguagem ou o framework? |
| Aprendizagem | Quanto tempo será necessário para utilizá-lo corretamente? |
| Documentação | Existem guias oficiais claros e atualizados? |
| Comunidade | Há usuários, exemplos, extensões e suporte? |
| Manutenção | O projeto recebe atualizações e correções de segurança? |
| Ecossistema | Existem bibliotecas para as necessidades da aplicação? |
| Mercado | A tecnologia aparece nas empresas e vagas da área pretendida? |
| Infraestrutura | A hospedagem e as ferramentas necessárias são viáveis? |

Para reduzir riscos, a equipe pode criar um pequeno protótipo antes de adotar uma tecnologia em um projeto maior.

Para quem está começando, uma boa estratégia é:

1. escolher um framework relacionado à linguagem que já conhece;
2. desenvolver mais de um projeto com ele;
3. compreender os conceitos, e não somente os comandos;
4. depois comparar com outra ferramenta.

Nesta disciplina, o Laravel permite aproveitar o conhecimento de PHP e estudar MVC, rotas, migrations, Models, Controllers, templates e ORM. Isso não significa que seja a única opção, mas que é adequado à sequência de aprendizagem proposta.

## 9. Como aprender um framework?

Um caminho recomendado é:

1. revisar os fundamentos da linguagem;
2. compreender qual problema o framework resolve;
3. conhecer a estrutura geral do projeto;
4. acompanhar o fluxo de uma requisição;
5. seguir um tutorial oficial curto;
6. construir uma pequena funcionalidade;
7. modificar o exemplo sem apenas copiar uma solução;
8. investigar erros e consultar a documentação;
9. desenvolver um projeto um pouco maior.

Ao encontrar um trecho de código, procure compreender:

- quem executa aquele código;
- quando ele será executado;
- quais dados recebe;
- qual resultado produz;
- onde está conectado ao restante da aplicação.

A documentação oficial deve ser uma das primeiras fontes de consulta. Vídeos e tutoriais também são úteis, mas podem utilizar versões antigas.

Depois da base, podem ser estudados assuntos como ciclo de vida do framework, injeção de dependências, middleware, testes automatizados, gerenciamento de estado e otimização de desempenho.

## 10. Quadro de consulta rápida

| Conceito | Lembrete |
|---|---|
| Framework | Estrutura reutilizável para desenvolver aplicações |
| Biblioteca | Código utilizado para resolver tarefas específicas |
| Inversão de controle | O framework coordena o fluxo e chama partes do nosso código |
| Convenção | Forma recomendada de organizar e implementar |
| Ecossistema | Documentação, extensões, ferramentas e comunidade |
| Framework de front-end | Ajuda a construir e organizar interfaces |
| Framework de back-end | Ajuda a tratar requisições, regras, dados e respostas |
| Curva de aprendizagem | Esforço necessário antes de utilizar a ferramenta com produtividade |
| Hype | Popularidade intensa que pode ser passageira |
| Fundamentos | Conhecimentos aproveitados entre diferentes tecnologias |

## O que você precisa guardar

1. Um framework fornece estrutura, convenções e ferramentas para desenvolver aplicações.
2. Framework não é uma linguagem de programação.
3. Bibliotecas resolvem tarefas específicas; frameworks organizam uma parte maior da aplicação.
4. Em geral, nosso código chama uma biblioteca, enquanto um framework também chama nosso código.
5. Frameworks podem aumentar produtividade, padronização e facilidade de manutenção.
6. Esses benefícios exigem tempo de aprendizagem e uso correto.
7. Nem todo projeto precisa de um framework completo.
8. React se apresenta como biblioteca, embora seja frequentemente tratado como framework.
9. Popularidade não deve ser o único critério de escolha.
10. Fundamentos sólidos facilitam aprender diferentes frameworks durante a vida profissional.

## Referências

- [MDN — Introdução aos frameworks e bibliotecas do lado cliente](https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Frameworks_libraries/Introduction)
- [MDN — Frameworks Web do lado servidor](https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Server-side/First_steps/Web_frameworks)
- [Laravel — Documentação oficial](https://laravel.com/docs/13.x)
- [Stack Overflow — Developer Survey 2025: tecnologias](https://survey.stackoverflow.co/2025/technology)
