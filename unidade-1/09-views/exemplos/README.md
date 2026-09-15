# Exemplo: interfaces da biblioteca com Blade

Complemento da [apostila de Views](../README.md), para Laravel 13. As páginas compartilham um layout Blade com Tailwind CSS 4 via CDN.

## O que está pronto

| Parte | Operações |
|---|---|
| Livros | Listar, buscar, paginar, cadastrar, consultar, editar e excluir |
| Autores | CRUD e consulta dos livros do autor (1–N) |
| Categorias | CRUD e consulta dos livros da categoria (N–N) |
| Detalhes de um livro | Cadastrar, consultar, editar e remover páginas e resumo (1–1) |
| Categorias de um livro | Associar, consultar, editar destaque/posição e desassociar (N–N) |

Os detalhes e as associações são consultados em `books.show`. Seus Controllers têm as actions necessárias à manutenção, sem repetir uma listagem ou uma página de leitura.

## Preparar o projeto e o banco

Use um projeto Laravel 13 com PHP 8.3 ou superior e execute `composer install` caso as dependências ainda não estejam instaladas.

São necessários os quatro Models e as cinco tabelas da versão completa da biblioteca:

| Model / relação | Estrutura necessária |
|---|---|
| `Author` | `authors`: `id`, `name`, timestamps; relação `books()` |
| `Book` | `books`: `id`, `title`, `isbn` único, `published_year`, `author_id`, timestamps; relações `author()`, `detail()` e `categories()` |
| `BookDetail` | `book_details`: `id`, `book_id` único, `pages`, `summary`, timestamps; relação `book()` |
| `Category` | `categories`: `id`, `name` único, timestamps; relação `books()` |
| Pivot `book_category` | `book_id`, `category_id`, `featured`, `position`, `created_at`, `updated_at`; par de IDs único |

Utilize os [Models, migrations, Factories e Seeders da apostila 07](../../07-seeders-factories/exemplos/README.md). Os relacionamentos N–N precisam de `withPivot('featured', 'position')->withTimestamps()` e os Models devem manter seus atributos permitidos em `Fillable`.

Em um projeto novo, copie os arquivos da apostila 07 conforme seu guia. Em um projeto já iniciado, integre o que falta, preservando migrations já executadas e evitando duas migrations de criação da mesma tabela. O `projeto-biblioteca` da raiz ainda é uma etapa inicial e precisa dessa preparação.

Confira a conexão no `.env`. Para este exemplo local, você pode usar sessões e cache em arquivos, sem depender de tabelas adicionais:

```dotenv
APP_LOCALE=pt_BR
SESSION_DRIVER=file
CACHE_STORE=file
```

A pasta `lang/pt_BR` fornecida no exemplo traduz as mensagens de validação utilizadas pelos Controllers. Integre esse arquivo se o projeto já tiver traduções.

A sessão é usada pelo Laravel para o token CSRF, os erros de validação, `old()` e as mensagens após redirecionamentos. Os dados de livros e seus relacionamentos são gravados no banco.

Na pasta do projeto:

```bash
php artisan config:clear
php artisan migrate
php artisan db:seed --class=LibrarySeeder
```

Se já populou o banco, não é necessário executar o Seeder novamente. Cada execução acrescenta autores, livros e detalhes. O exemplo também funciona começando com as tabelas vazias: cadastre primeiro um autor e uma categoria pelas próprias telas.

## Arquivos e destinos

| Arquivo ou pasta do exemplo | Destino no projeto Laravel |
|---|---|
| [BookController.php](app/Http/Controllers/BookController.php) | `app/Http/Controllers/BookController.php` |
| [AuthorController.php](app/Http/Controllers/AuthorController.php) | `app/Http/Controllers/AuthorController.php` |
| [CategoryController.php](app/Http/Controllers/CategoryController.php) | `app/Http/Controllers/CategoryController.php` |
| [BookDetailController.php](app/Http/Controllers/BookDetailController.php) | `app/Http/Controllers/BookDetailController.php` |
| [BookCategoryController.php](app/Http/Controllers/BookCategoryController.php) | `app/Http/Controllers/BookCategoryController.php` |
| [resources/views](resources/views/) | `resources/views/`, preservando as subpastas |
| [validation.php](lang/pt_BR/validation.php) | `lang/pt_BR/validation.php` |
| [routes/web.php](routes/web.php) | Integrar as definições ao `routes/web.php` existente |

O `Controller.php` base já faz parte do projeto Laravel. Não crie duas classes com o mesmo nome: esta versão de `BookController` amplia a da apostila 08. As Views de `books` também passam a usar o layout desta etapa. Se tiver outras personalizações nesses arquivos, integre-as antes de substituí-los.

O `BookController` fornecido mantém `index_lazy_loading` e `index_eager_loading`. O arquivo de rotas inclui os dois endereços de medição antes das rotas do CRUD.

Ao integrar `web.php`, mantenha uma única definição de cada rota e de cada import `use`. Remova definições antigas equivalentes do CRUD, sem apagar outras rotas do projeto. Não repita a abertura `<?php`.

## Organização das Views

| Pasta | Conteúdo |
|---|---|
| [layouts](resources/views/layouts/) | Estrutura HTML, menu, mensagens e rodapé |
| [partials](resources/views/partials/) | Resumo de erros e paginação em português |
| [books](resources/views/books/) | CRUD de livros e apresentação dos relacionamentos |
| [authors](resources/views/authors/) | CRUD de autores e seus livros |
| [categories](resources/views/categories/) | CRUD de categorias e seus livros |
| [book-details](resources/views/book-details/) | Formulários dos detalhes de um livro |
| [book-categories](resources/views/book-categories/) | Formulários da associação livro/categoria |

Os arquivos `_form.blade.php` contêm campos compartilhados entre cadastro e edição. O formulário externo define `action`, `@csrf` e, quando necessário, `@method`.

As classes Tailwind estão diretamente no HTML. Para estas Views, basta o CDN; não é necessário executar `npm install` ou `npm run dev`. É preciso acesso à internet para carregar o estilo. O CDN é uma opção de desenvolvimento; a preparação do CSS para produção é outro passo.

## Rotas e navegação

```bash
php artisan route:list --path=books
php artisan route:list --path=authors
php artisan route:list --path=categories
php artisan serve
```

Acesse [http://127.0.0.1:8000/books](http://127.0.0.1:8000/books), ou a porta informada pelo servidor. A página inicial do projeto não precisa ser alterada: os links do menu navegam entre `/books`, `/authors` e `/categories`.

| Operação relacionada ao livro | Endereço |
|---|---|
| Consultar livro, detalhes e categorias | `GET /books/{book}` |
| Abrir cadastro dos detalhes | `GET /books/{book}/detail/create` |
| Salvar detalhes | `POST /books/{book}/detail` |
| Abrir edição dos detalhes | `GET /books/{book}/detail/edit` |
| Atualizar / remover detalhes | `PUT / DELETE /books/{book}/detail` |
| Abrir associação de categoria | `GET /books/{book}/categories/create` |
| Associar categoria | `POST /books/{book}/categories` |
| Abrir edição do vínculo | `GET /books/{book}/categories/{category}/edit` |
| Atualizar / remover vínculo | `PUT / DELETE /books/{book}/categories/{category}` |

As rotas de manutenção da pivot recebem os dois Models. O Controller também verifica se a categoria está associada àquele livro antes de editar ou remover o vínculo.

## Roteiro para a aula

1. Abra a listagem populada pelo Seeder; busque um título e experimente a paginação.
2. Cadastre um autor, uma categoria e um livro de teste. Escolha o autor no formulário do livro.
3. Tente repetir o ISBN; observe os erros e a preservação dos campos. Depois, edite o livro mantendo seu próprio ISBN.
4. No livro novo, cadastre páginas e resumo. Edite os detalhes, remova-os e observe que o livro continua existindo.
5. Associe duas categorias. Marque destaque em uma delas e informe posições diferentes.
6. Edite o vínculo: desmarque o destaque e altere a posição. Reabra a edição para conferir os valores.
7. Tente associar a mesma categoria novamente: a operação deve ser rejeitada.
8. Associe uma dessas categorias a outro livro. Desassocie-a somente do primeiro e confira que o outro vínculo foi preservado.
9. Abra um autor com livros e tente excluí-lo: o sistema deve informar a restrição.
10. Exclua um livro de teste e confira a remoção de seus detalhes e vínculos. Exclua uma categoria de teste e confira que seus livros foram mantidos.

O exemplo não impõe que haja somente um destaque ou que as posições sejam exclusivas: esses campos são atributos de cada vínculo. A combinação `book_id` + `category_id`, por sua vez, deve ser única.

## Se algo não funcionar

| Sintoma | O que conferir |
|---|---|
| `View [...] not found` | Pasta `resources/views`, nomes dos arquivos e extensão `.blade.php` |
| Rota não definida | Imports e definições de `routes/web.php`; consulte `route:list` |
| Coluna ausente na pivot | A versão completa exige os campos extras e os dois timestamps |
| Erro 419 ao enviar | Presença de `@csrf`, sessão funcionando e página recém-carregada |
| Relação ou método inexistente | Uso dos Models da apostila 07 e dos Controllers desta pasta |
| Layout sem estilo | Acesso ao script CDN no navegador |

Este é um exemplo de estudo local, sem login ou autorização. A interface evita consultas ao banco dentro dos templates e mantém validação e persistência nos Controllers. As restrições das migrations continuam garantindo a integridade dos relacionamentos.
