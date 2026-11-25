# Biblioteca com cadastro de livro e detalhes

Esta pasta reúne os arquivos criados ou alterados nas aulas para executar a biblioteca em um projeto Laravel novo. O cadastro reaproveita a biblioteca da [aula de Storage](../../12-storage/) e usa `DB::transaction()` para gravar o livro e seus detalhes em uma única operação.

## 1. Criar o projeto e copiar os arquivos

No terminal Ubuntu no WSL, na pasta que contém o repositório `curso-web-2`, crie um projeto ao lado dele.

```bash
laravel new biblioteca-transacoes
```

Use Laravel 13, escolha SQLite e siga sem starter kit. Copie os arquivos da aula para o projeto.

```bash
origem=curso-web-2/unidade-1/14-transacoes/exemplos
cp -R "$origem/app/." biblioteca-transacoes/app/
cp -R "$origem/database/." biblioteca-transacoes/database/
cp -R "$origem/resources/." biblioteca-transacoes/resources/
cp -R "$origem/routes/." biblioteca-transacoes/routes/
cp -R "$origem/public/." biblioteca-transacoes/public/
cd biblioteca-transacoes
```

Se você já executou o exemplo da aula 12, atualize o [controller](app/Http/Controllers/BookController.php), a [página de cadastro](resources/views/books/create.blade.php) e o [formulário de detalhes](resources/views/book-details/_form.blade.php) nos mesmos caminhos do projeto. Com o banco e o link de Storage já preparados, siga para `php artisan serve`.

## 2. Preparar e iniciar

No projeto novo, rode as migrations, insira os dados de aula e crie o link de Storage.

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Abra a [página de login](http://localhost:8000/login) e entre como `bibliotecario@example.com` com a senha de aula `senha-aula-123`.

## 3. Ver o commit e o rollback

1. Em **Livros**, abra **Cadastrar livro**. Preencha título, um ISBN de 13 caracteres ainda livre, autor e quantidade de páginas. Salve e confira os detalhes na página do livro.
2. No [BookController](app/Http/Controllers/BookController.php) do seu projeto, coloque temporariamente a linha abaixo logo depois de `$book = Book::create($bookData);`, dentro da função passada a `DB::transaction()`.

   ```php
   throw new \RuntimeException('Falha simulada depois de gravar o livro.');
   ```

3. Envie outro cadastro, com um ISBN diferente. A requisição deve falhar. Volte à lista de livros e confira que o título usado no segundo cadastro não aparece. Se você anexou uma capa, confira também a pasta `storage/app/public/covers`.
4. Remova a linha de teste e envie novamente o cadastro com o mesmo ISBN. A página do livro deve exibir os detalhes.

A validação do formulário ocorre antes da transação. Preencha os campos corretamente na etapa 3 para que o código chegue à falha que você inseriu depois da primeira gravação.
