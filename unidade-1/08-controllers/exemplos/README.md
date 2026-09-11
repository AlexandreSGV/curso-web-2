# Exemplo: Controller de livros

Complemento da [apostila de Controllers](../README.md). Reúne as sete actions do CRUD, rotas e telas mínimas em Blade, sem CSS, JavaScript ou instalação de pacotes front-end.

## Antes de começar

Use um projeto Laravel 13 local com a conexão de banco configurada. Este exemplo continua as etapas das apostilas de [Models](../../05-models/README.md) e [Eloquent](../../06-orm-eloquent/README.md); não é uma aplicação Laravel independente.

São necessários:

- tabela `books` com `id`, `title`, `isbn` único, `published_year` opcional, `author_id` e timestamps;
- tabela `authors` com `id`, `name` e timestamps;
- tabelas `categories` e `book_category`, conforme a relação muitos para muitos já estudada;
- Models `Book`, `Author` e `Category` em `App\Models`;
- métodos `author()` e `categories()` no Model `Book`;
- `title`, `isbn`, `published_year` e `author_id` autorizados no `Fillable` de `Book`.

Se `categories()` usa `withPivot('featured', 'position')->withTimestamps()`, essas colunas também precisam existir na pivot. Se adotou a pivot simples, mantenha a configuração simples correspondente. Não misture as duas versões.

> O diretório `projeto-biblioteca` do repositório contém uma etapa anterior, com `Book` sem esses relacionamentos. Somente copiar o Controller para ele não conclui a preparação: aplique antes as etapas de Models e Eloquent. Não recrie uma tabela que já existe nem apague dados para adaptar a estrutura.

As chaves de `book_details.book_id` (caso exista) e `book_category.book_id` usam `cascadeOnDelete()` nas migrations estudadas: excluir um livro remove os detalhes e as associações, não o autor nem as categorias. Outras restrições no seu projeto podem impedir a exclusão.

Tenha ao menos um autor cadastrado. Em um banco local de estudos, você pode usar o Tinker:

```bash
php artisan tinker
```

```php
App\Models\Author::firstOrCreate(['name' => 'Machado de Assis']);
```

O `Author` precisa permitir `name` no `Fillable`, como na apostila de Models. Saia do Tinker com `exit`.

## Onde colocar os arquivos

| Arquivo do exemplo | Destino no seu projeto |
|---|---|
| [BookController.php](BookController.php) | `app/Http/Controllers/BookController.php` |
| [web.php](web.php) | Acrescentar a definição ao `routes/web.php` existente |
| [views/books](views/books/) | `resources/views/books/` |

Se já houver um `BookController`, integre os métodos à classe existente; não declare duas classes com o mesmo nome. Esta versão de `index` dá continuidade à consulta paginada da apostila de Eloquent.

Ao copiar as rotas, não repita `<?php` nem os imports `use` que já existem, não apague as demais rotas e mantenha somente uma definição de `Route::resource('books', BookController::class)`. Remova definições manuais equivalentes do mesmo CRUD para evitar duplicação de caminhos e nomes.

Se incorporou o [exemplo de lazy/eager loading](../../06-orm-eloquent/exemplos/BookController.php), preserve as actions `index_lazy_loading` e `index_eager_loading` e suas rotas. Coloque as duas rotas específicas **antes** de `Route::resource(...)`; caso contrário, `/books/{book}` pode interpretar `lazy-loading` como identificador de livro.

## Executando

Na pasta do projeto Laravel:

```bash
php artisan route:list --path=books
php artisan serve
```

Acesse `http://127.0.0.1:8000/books`. O endereço pode variar conforme a porta informada pelo servidor.

As Views estão prontas para listar, cadastrar, detalhar, editar e excluir. `_form.blade.php` reúne os campos comuns ao cadastro e à edição. A paginação usa links simples para funcionar sem um framework CSS. Categorias existentes são exibidas, mas o formulário modifica apenas os quatro atributos de `Book`; não sincroniza nem apaga categorias.

## Roteiro de conferência

1. Abra a lista, inclusive sem livros cadastrados.
2. Cadastre `Dom Casmurro`, ISBN `9788535910663`, ano `1899`, escolhendo um autor existente. Após salvar, confira a página de detalhes.
3. Tente cadastrar outro livro com o mesmo ISBN: a validação deve rejeitar a duplicação.
4. Edite somente o título do primeiro livro, sem mudar seu ISBN: a alteração deve ser aceita.
5. Teste um título vazio ou um ano fora do intervalo de 1000 a 2100. A validação no navegador ajuda, mas a validação no servidor continua necessária.
6. Consulte `/books/999999` (usando um ID que não exista): o resultado esperado é 404.
7. Com mais de dez livros, busque parte de um título e navegue entre as páginas: o filtro deve permanecer na URL.
8. Exclua um livro de teste pela página de detalhes e confira o retorno à lista. Não use dados importantes.

O intervalo de anos e a validação de ISBN são simplificações didáticas; não verificamos o dígito verificador do ISBN.

## Limites deste exemplo

- Executar somente em ambiente local de aprendizagem: não há login nem Policies de autorização.
- `@csrf` protege os formulários contra requisições forjadas, mas não substitui autorização.
- Os Models e as migrations não são duplicados aqui; use as versões compatíveis indicadas acima.
- O preenchimento de autores, a gestão de categorias e as regras avançadas ficam fora deste CRUD introdutório.
