# Exemplo: acesso à biblioteca por papéis

Complemento da [apostila de autenticação e autorização](../README.md). Integre estes arquivos ao projeto Laravel 13 da [aula 09](../../09-views/exemplos/README.md), já preparado com os Models, migrations e dados da [aula 07](../../07-seeders-factories/exemplos/README.md).

## Preparação

Mantenha o Model `User`, a tabela `users` e as migrations iniciais de um projeto Laravel padrão, além das cinco tabelas da biblioteca. O `User` deve manter `name`, `email` e `password` como atributos permitidos e a senha oculta na serialização. A coluna `role` será acrescentada por esta etapa; mantenha-a fora do `Fillable`.

As configurações do Laravel permanecem padrão. Se já houver autenticação de um starter kit no seu projeto, integre as permissões a ela, preservando seu fluxo de login; os arquivos de login desta pasta são para a biblioteca sem starter kit da aula 09.

## Arquivos e destinos

Copie os arquivos novos e integre as substituições nos caminhos equivalentes do projeto:

| Arquivo ou pasta | Função |
|---|---|
| [Migration](database/migrations/2026_11_03_000001_add_role_to_users_table.php) | Acrescentar `users.role`, com padrão `cliente` |
| [AccessSeeder.php](database/seeders/AccessSeeder.php) | Criar ou atualizar três contas locais de demonstração |
| [AuthController.php](app/Http/Controllers/AuthController.php) | Login e logout |
| [BookPolicy.php](app/Policies/BookPolicy.php) | Permissões dos livros |
| [AppServiceProvider.php](app/Providers/AppServiceProvider.php) | Gate `manage-catalog`; integre o `boot` e os imports ao provider existente |
| [BookController.php](app/Http/Controllers/BookController.php) | Versão da aula 09 com autorização antes das sete ações do CRUD |
| [routes/web.php](routes/web.php) | Rotas de acesso e operações protegidas da biblioteca |
| [resources/views](resources/views/) | Layout, login e páginas de livros adaptadas às permissões |

Se preferir gerar os arquivos antes de preenchê-los:

```bash
php artisan make:migration add_role_to_users_table --table=users
php artisan make:seeder AccessSeeder
php artisan make:controller AuthController
php artisan make:policy BookPolicy --model=Book
php artisan make:view auth.login
```

Use apenas uma migration para acrescentar `role`: preencha a gerada **ou** copie a fornecida. Não execute duas migrations que adicionem a mesma coluna.

O Controller base e os Controllers de autores, categorias, detalhes e vínculos continuam sendo os da aula 09. As demais Views, inclusive formulários e parciais, também continuam sendo usadas. Esta pasta é um complemento daquele projeto.

Ao integrar `web.php`, substitua as definições anteriores da biblioteca, inclusive as rotas de medição, mantendo apenas uma definição de cada rota. As sete rotas de livros estão explícitas para mostrar as permissões; não acrescente outra chamada a `Route::resource('books', ...)`. Preserve rotas de outros assuntos que já existam no projeto.

## Preparar os dados e executar

Na raiz do projeto:

```bash
php artisan migrate
php artisan db:seed --class=AccessSeeder
php artisan route:list
php artisan serve
```

Se a biblioteca ainda não tiver sido populada, execute também o Seeder da aula 07:

```bash
php artisan db:seed --class=LibrarySeeder
```

O `AccessSeeder` pode ser repetido: reutiliza os três e-mails e redefine nome, papel e senha dessas contas. A senha é gravada com hash. São contas para a demonstração local, não contas administrativas de uma implantação real.

Acesse [http://localhost:8000/login](http://localhost:8000/login), ou a porta informada no terminal. Use o botão **Sair** para trocar de conta.

| E-mail | Senha de aula | Papel |
|---|---|---|
| `admin@example.com` | `senha-aula-123` | `admin` |
| `bibliotecario@example.com` | `senha-aula-123` | `bibliotecario` |
| `cliente@example.com` | `senha-aula-123` | `cliente` |

O envio do login tem limite de cinco requisições por minuto por IP. Se receber 429 durante os testes, aguarde um minuto antes de tentar novamente.

## Permissões e continuidade dos relacionamentos

| Parte | Cliente | Bibliotecário | Administrador |
|---|---|---|---|
| Listar e consultar livros, com autor, detalhes e categorias | Sim | Sim | Sim |
| Cadastrar e editar livros | Não | Sim | Sim |
| Criar, editar e remover os detalhes do livro | Não | Sim | Sim |
| Associar, editar e remover vínculos com categorias | Não | Sim | Sim |
| Excluir livro | Não | Não | Sim |
| Acessar os CRUDs de autores e categorias | Não | Não | Sim |
| Executar as duas rotas de medição de loading | Não | Não | Sim |

O bibliotecário pode escolher um autor e associar categorias já cadastradas; novos autores e categorias são cadastrados pelo administrador. A página de livros apresenta seus nomes para todos os três papéis. Links para os CRUDs administrativos aparecem apenas para o administrador.

Todos os caminhos internos da biblioteca exigem `auth`. Os CRUDs de autores/categorias e as medições exigem o Gate `manage-catalog`. As rotas dos detalhes (1–1) e da pivot (N–N) consultam a permissão `update` do livro. A FK `author_id` e os demais relacionamentos mantêm a estrutura das aulas anteriores.

Os formulários preservam `@csrf`, e as alterações/exclusões preservam `@method`. As verificações de pertencimento da categoria ao livro, implementadas na aula 09, continuam ativas.

## Conferir em sala

1. Como visitante, abra `/books` e confira o redirecionamento para `/login`.
2. Tente entrar com senha errada; confira o erro e a preservação somente do e-mail.
3. Como cliente, confira a leitura dos três relacionamentos e a ausência dos controles de alteração. Digite `/books/create` e `/authors` diretamente: deve receber 403.
4. Como bibliotecário, cadastre/edite um livro e mantenha seus detalhes e vínculos. Confira que excluir o livro e gerenciar autores/categorias permanecem proibidos.
5. Como administrador, confira todas as operações, usando registros de teste para excluir.
6. Após sair, tente abrir uma página interna e confira que o acesso exige login novamente.

O papel é comparado diretamente no layout para personalizar uma mensagem. As permissões de operações são consultadas com `@can`, usando as mesmas regras do servidor.
