# Atividade - acrescentar o número de páginas

## Objetivo

Evoluir a entidade `Book` com uma mudança pequena e observável, preservando o histórico das migrations.

Continue na mesma aplicação `projeto-biblioteca`. Não crie outro projeto Laravel.

## O que deve ser feito

1. Crie uma nova migration chamada `add_pages_to_books_table` para modificar a tabela `books`.
2. No método `up`, acrescente a coluna `pages`:
   - número inteiro não negativo;
   - pode ficar vazia;
   - deve aparecer antes dos timestamps ou em outra posição coerente no banco.
3. No método `down`, remova somente a coluna `pages`.
4. Aplique a nova migration sem usar `migrate:fresh`, `migrate:refresh` ou `migrate:reset`.
5. Atualize o Model `Book` para permitir `pages` no mesmo mecanismo de atribuição em massa usado pelos outros campos.
6. No Tinker, crie um livro com número de páginas e consulte `id`, `title`, `isbn`, `published_year` e `pages`.

Não edite a migration `create_books_table` já executada. A mudança deve ficar registrada em um novo arquivo.

## Resultado esperado

- `php artisan migrate:status` mostra as duas migrations de `books` como executadas;
- a tabela `books` possui a coluna opcional `pages`;
- o Tinker retorna um livro com o número de páginas informado;
- nenhuma relação, tela CRUD, factory ou seeder foi adicionada.

## Critérios observados

- nova migration com nome e finalidade coerentes;
- métodos `up` e `down` que realizam operações opostas;
- tipo e nulabilidade adequados para `pages`;
- Model atualizado de acordo com o uso de `Book::create(...)`;
- aplicação da migration sem apagar todo o banco;
- código organizado no mesmo projeto da biblioteca.

## Entrega

Envie no Google Classroom somente o link geral do seu repositório privado de Web 2. Confirme antes que o professor `AlexandreSGV` possui acesso e que as alterações da biblioteca foram enviadas ao GitHub.
