# Atividade - livro sem ano informado

## Objetivo

Evoluir a factory e o seeder para representar um livro cujo ano de publicação ainda não foi informado, e depois localizá-lo com Eloquent.

Continue na mesma aplicação `projeto-biblioteca`. Não crie outro projeto Laravel.

## O que deve ser feito

1. Acrescente em `BookFactory` um estado chamado `withoutPublishedYear` que substitua `published_year` por `null`.
2. No final de `DatabaseSeeder`, use esse estado para criar um livro previsível:
   - título: `Livro sem ano informado`;
   - ISBN: `9790000000070`;
   - ano de publicação nulo.
3. Confirme que o `.env` aponta para um banco de desenvolvimento descartável e, somente nele, execute `php artisan migrate:fresh --seed`. Se o banco atual contém dados que precisam ser preservados, use outro banco e não execute esse comando nele.
4. No Tinker, faça uma consulta com `whereNull('published_year')` e retorne `id`, `title`, `isbn` e `published_year`.
5. Ordene os demais livros por `published_year` em ordem crescente e limite o resultado às colunas `title` e `published_year`.

Não crie migration, relacionamento, controller ou página Blade para esta atividade.

## Resultado esperado

- a factory possui um estado reutilizável para ano desconhecido;
- o seeder cria exatamente um livro conhecido usando esse estado;
- o banco descartável recriado contém os seis livros do kit e o novo livro da atividade;
- a consulta `whereNull` encontra **Livro sem ano informado**;
- a ordenação é executada pelo Eloquent no Tinker;
- nenhuma funcionalidade futura foi antecipada.

## Critérios observados

- estado da factory com nome e retorno coerentes;
- uso do estado pelo `DatabaseSeeder`;
- ISBN diferente dos seis registros do kit;
- consulta `whereNull` correta;
- ordenação com seleção apenas das colunas solicitadas;
- alterações mantidas na mesma biblioteca.

## Entrega

Envie no Google Classroom somente o link geral do seu repositório privado de Web 2. Confirme que o professor `AlexandreSGV` possui acesso e que suas alterações foram enviadas ao GitHub.
