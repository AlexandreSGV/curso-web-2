# Exemplo: biblioteca com autenticação e autorização

Arquivos da aplicação desenvolvidos nas aulas, reunidos para copiar em um **projeto Laravel 13 novo**. Incluem os Models, migrations, Factories, Seeders, Controllers e Views das aulas anteriores, com as permissões da [apostila de autenticação e autorização](../README.md).

O Laravel fornece a estrutura inicial do projeto. Esta pasta contém somente os arquivos criados ou editados para o exemplo.

## 1. Criar um projeto Laravel novo

Use o ambiente da disciplina, com PHP 8.3 ou superior, Composer e as extensões necessárias ao Laravel, incluindo suporte a SQLite. Os comandos abaixo consideram o terminal do WSL/Ubuntu ou Linux.

Se ainda não baixou o repositório:

```bash
git clone https://github.com/AlexandreSGV/curso-web-2.git
```

Se o comando `laravel` ainda não estiver disponível, instale o instalador:

```bash
composer global require laravel/installer
```

Na pasta que contém `curso-web-2`, crie o projeto ao lado do repositório:

```bash
laravel new biblioteca-acesso
```

No instalador, escolha **sem starter kit**, **Blade** quando solicitado e **SQLite**, mantendo as demais configurações padrão. O instalador prepara as dependências, o `.env`, a chave da aplicação e o banco inicial.

## 2. Copiar os arquivos da aplicação

Ainda na pasta que contém `curso-web-2` e `biblioteca-acesso`, execute:

```bash
cp -R curso-web-2/unidade-1/11-autenticacao-autorizacao/exemplos/app/. biblioteca-acesso/app/
cp -R curso-web-2/unidade-1/11-autenticacao-autorizacao/exemplos/database/. biblioteca-acesso/database/
cp -R curso-web-2/unidade-1/11-autenticacao-autorizacao/exemplos/resources/. biblioteca-acesso/resources/
cp -R curso-web-2/unidade-1/11-autenticacao-autorizacao/exemplos/routes/. biblioteca-acesso/routes/
```

O `cp -R` copia as subpastas. O `/.` indica que queremos copiar o conteúdo para a pasta correspondente do novo projeto. Você também pode fazer essa cópia pelo gerenciador de arquivos.

Em um projeto novo, a cópia substitui `routes/web.php`, `app/Providers/AppServiceProvider.php` e `database/seeders/DatabaseSeeder.php` pelas versões da aula. Se usar um projeto que já tenha personalizações, integre as alterações desses arquivos.

Preserve os demais arquivos gerados pelo Laravel, incluindo o Model `User`, a migration de `users`, as migrations de infraestrutura, `artisan`, `bootstrap/`, `config/` e os arquivos de dependências. O `User` padrão já permite `name`, `email` e `password`; mantenha `role` fora de seus atributos permitidos em `Fillable`.

Os arquivos de código já estão gerados: não repita os comandos `make:model`, `make:migration` e similares apenas para executar o exemplo.

## 3. Criar as tabelas e popular o banco

Entre no projeto e execute:

```bash
cd biblioteca-acesso
php artisan migrate --seed
```

O [DatabaseSeeder](database/seeders/DatabaseSeeder.php) chama:

1. [LibrarySeeder](database/seeders/LibrarySeeder.php): cria autores, livros, detalhes, categorias e vínculos;
2. [AccessSeeder](database/seeders/AccessSeeder.php): cria as três contas para comparar permissões.

São usadas as configurações padrão do Laravel. As dependências, inclusive o Faker utilizado pelas Factories, são as do projeto novo; não é necessário copiar `vendor/` de outro projeto.

## 4. Iniciar a aplicação

```bash
php artisan serve
```

Acesse [http://localhost:8000/login](http://localhost:8000/login), ou a porta indicada no terminal. Deixe o servidor aberto; use `Ctrl+C` para encerrá-lo.

As Views usam Tailwind via CDN, como na aula 09. Para essas páginas, não é necessário compilar CSS/JavaScript com npm. É preciso acesso à internet para carregar o estilo do CDN.

Para conferir as rotas em outro terminal, na pasta `biblioteca-acesso`:

```bash
php artisan route:list
```

## 5. Entrar com as contas de aula

| E-mail | Senha | Papel |
|---|---|---|
| `admin@example.com` | `senha-aula-123` | `admin` |
| `bibliotecario@example.com` | `senha-aula-123` | `bibliotecario` |
| `cliente@example.com` | `senha-aula-123` | `cliente` |

Use **Sair** para trocar de conta. As senhas são gravadas com hash. Essas contas são destinadas à demonstração local.

O envio do login tem limite de cinco requisições por minuto por IP. Se receber 429 durante os testes, aguarde um minuto antes de tentar novamente.

| Operação | Cliente | Bibliotecário | Administrador |
|---|---|---|---|
| Listar e consultar livros, com autor, detalhes e categorias | Sim | Sim | Sim |
| Cadastrar e editar livros | Não | Sim | Sim |
| Criar, editar e remover detalhes de um livro | Não | Sim | Sim |
| Associar, editar e remover vínculos com categorias | Não | Sim | Sim |
| Excluir livros | Não | Não | Sim |
| Acessar os CRUDs de autores e categorias | Não | Não | Sim |
| Listar, consultar e editar usuários | Não | Não | Sim |
| Executar as duas rotas de medição de loading | Não | Não | Sim |

O bibliotecário escolhe autores e categorias já cadastrados. O administrador pode cadastrar novos autores e categorias. Seus nomes aparecem para todos os papéis na página do livro; os links administrativos aparecem apenas para o administrador.

## 6. Localizar os arquivos

| Pasta ou arquivo | Conteúdo |
|---|---|
| [app/Models](app/Models/) | `Author`, `Book`, `BookDetail` e `Category` |
| [app/Http/Controllers](app/Http/Controllers/) | Login/logout, usuários e os cinco Controllers da biblioteca |
| [app/Policies/BookPolicy.php](app/Policies/BookPolicy.php) | Permissões das operações com livros |
| [app/Policies/UserPolicy.php](app/Policies/UserPolicy.php) | Consulta e edição de usuários somente por administradores |
| [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) | Provider editado para definir o Gate `manage-catalog` |
| [database/migrations](database/migrations/) | Cinco migrations da biblioteca e uma para acrescentar `users.role` |
| [database/factories](database/factories/) | Geração de autores, livros, detalhes e categorias |
| [database/seeders](database/seeders/) | `DatabaseSeeder` editado, `LibrarySeeder` e `AccessSeeder` |
| [resources/views](resources/views/) | Layout, login, CRUDs, formulários dos relacionamentos e parciais |
| [routes/web.php](routes/web.php) | Rotas protegidas da aplicação |

Os Models, Factories, migrations e o `LibrarySeeder` preservam o contexto da aula 07. Os Controllers e Views preservam os CRUDs da aula 09, com as adaptações de autenticação e autorização da aula 11.

Todas as rotas internas da biblioteca exigem `auth`. Os CRUDs de autores/categorias e as medições exigem `manage-catalog`. As alterações em detalhes e vínculos consultam `update` do livro. Os formulários mantêm `@csrf` e os métodos HTTP das aulas anteriores.

### Telas de usuários

O administrador acessa **Usuários** no menu. A listagem é paginada; **Detalhes** abre a conta e **Editar** permite alterar nome, e-mail e papel. O e-mail deve ser único, e o papel deve ser `admin`, `bibliotecario` ou `cliente`. A senha permanece inalterada.

| Operação | Rota |
|---|---|
| Listar contas | `GET /users` |
| Consultar uma conta | `GET /users/{user}` |
| Abrir a edição | `GET /users/{user}/edit` |
| Salvar a edição | `PUT/PATCH /users/{user}` |

Todas essas rotas exigem login e autorização pela `UserPolicy`, também consultada pelo Controller. O Model `User` continua sendo o padrão do Laravel; não é necessário acrescentar `role` a `Fillable`, pois a atribuição é explícita na action administrativa.

Uma alteração de papel passa a valer nos próximos acessos. Se o administrador mudar o próprio papel, será encaminhado à lista de livros.

## 7. Conferir em sala

1. Sem login, abra `/books`: deve ser redirecionado para `/login`.
2. Tente uma senha incorreta: confira a mensagem e a preservação somente do e-mail.
3. Como cliente, consulte livros e relacionamentos. Digite `/books/create` e `/authors` diretamente: deve receber 403.
4. Como bibliotecário, cadastre/edite um livro e mantenha seus detalhes e vínculos. Excluir o livro e gerenciar autores/categorias continuam proibidos.
5. Como administrador, confira as operações da biblioteca e o menu **Usuários**: abra a listagem, os detalhes e a edição de uma conta. Salve mantendo o e-mail atual e depois teste um e-mail já usado por outra conta.
6. Como cliente ou bibliotecário, tente abrir `/users`, `/users/1` e `/users/1/edit` diretamente, usando um ID existente: deve receber 403. O envio da atualização também é bloqueado.
7. Após sair, abra uma página interna: o login deve ser exigido novamente.

## Reutilizar o banco nas próximas execuções

Depois da preparação inicial, basta iniciar o servidor novamente. Se forem acrescentadas migrations, execute `php artisan migrate`.

O `LibrarySeeder` acrescenta um novo conjunto de autores, livros e detalhes a cada execução. Por isso, não repita `db:seed` ou `migrate --seed` apenas para iniciar a aplicação.

Para recriar ou atualizar somente as três contas de aula:

```bash
php artisan db:seed --class=AccessSeeder
```

Esse Seeder reutiliza os e-mails e redefine nome, papel e senha dessas contas.
