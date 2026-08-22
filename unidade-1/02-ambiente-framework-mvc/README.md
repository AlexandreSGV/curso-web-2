# Ambiente, framework e MVC

> Ao final deste assunto, você conseguirá executar a aplicação da biblioteca e explicar o caminho de uma requisição pela rota, pelo controller e pela view Blade.

## Antes de começar

Use o Ubuntu no WSL. Se o ambiente ainda não estiver pronto, siga o [guia de preparação do WSL](../../apoio/ambiente-web2-wsl/). Para lembrar como navegar entre pastas, consulte a [folha de comandos do Ubuntu](../../apoio/comandos-ubuntu-wsl/).

No terminal do Ubuntu, confirme as ferramentas principais:

```bash
php -v
composer --version
node -v
npm -v
```

Os comandos devem terminar sem erro. O guia de ambiente mostra as versões adotadas na disciplina.

## Por que usar um framework?

Em uma aplicação pequena, podemos escrever PHP e HTML em poucos arquivos. Conforme o sistema cresce, precisamos decidir onde ficam as rotas, as regras, o acesso aos dados e a interface. Sem uma organização comum, cada parte pode acabar misturada em um lugar diferente.

Um **framework** oferece uma estrutura e ferramentas prontas para problemas frequentes. É como montar um móvel com um kit: as peças principais e a ordem de montagem já existem, mas o resultado ainda depende das decisões do projeto.

O Laravel oferece, entre outros recursos:

- roteamento de endereços;
- controllers para organizar o fluxo;
- Blade para criar páginas HTML;
- validação, banco de dados e autenticação, que serão estudados gradualmente.

O framework também coordena a execução. Quando o navegador acessa uma rota, o Laravel encontra e chama o código indicado. Essa ideia recebe o nome de **inversão de controle**: em vez de nosso código controlar sozinho toda a aplicação, ele se encaixa no fluxo oferecido pelo framework.

## Uma única aplicação da biblioteca

A disciplina usa uma única base em [`projeto-biblioteca/`](../../projeto-biblioteca/). Ela será ampliada ao longo do semestre. Não crie uma cópia completa do Laravel para cada assunto.

Neste primeiro estado, a aplicação não usa banco nem possui um Model próprio. Ela contém somente o necessário para observar este caminho:

```text
navegador → rota → controller → view Blade → resposta HTML
```

## Prepare o projeto

No terminal do Ubuntu, entre na pasta `projeto-biblioteca` e execute:

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run build
```

- `composer install` baixa as dependências PHP registradas no projeto;
- `key:generate` cria uma chave somente no seu `.env` local;
- `npm install` baixa as ferramentas do front-end;
- `npm run build` gera o arquivo de CSS com Vite.

O arquivo `.env` contém configurações locais e não deve ser enviado ao GitHub. Não é necessário executar migrations neste assunto.

> **Checkpoint:** as pastas `vendor/` e `node_modules/` foram criadas localmente, e os comandos terminaram sem erro.

## Siga a primeira requisição

### 1. A rota reconhece o endereço

Abra [`routes/web.php`](../../projeto-biblioteca/routes/web.php):

```php
use App\Http\Controllers\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');
```

Essa linha informa: quando chegar uma requisição `GET` para `/`, execute o método `index` de `InicioController`.

Confira a rota registrada:

```bash
php artisan route:list
```

O resultado deve incluir a rota `GET|HEAD /`, com o nome `inicio`, ligada a `InicioController@index`.

### 2. O controller prepara a resposta

Abra [`app/Http/Controllers/InicioController.php`](../../projeto-biblioteca/app/Http/Controllers/InicioController.php):

```php
public function index(): View
{
    return view('biblioteca.inicio', [
        'titulo' => 'Biblioteca Web 2',
        'mensagem' => 'A aplicação Laravel está funcionando.',
    ]);
}
```

O controller recebe o fluxo iniciado pela rota, prepara duas informações e escolhe a view `biblioteca.inicio`.

No nome da view, o ponto representa uma pasta. Portanto, `biblioteca.inicio` corresponde a:

```text
resources/views/biblioteca/inicio.blade.php
```

### 3. Blade monta o HTML

Na view, Blade permite mostrar os valores recebidos do controller:

```blade
<title>{{ $titulo }}</title>

<h1>{{ $titulo }}</h1>
<p>{{ $mensagem }}</p>
```

As chaves duplas `{{ }}` exibem um valor no HTML. O navegador recebe o resultado pronto; ele não executa o código PHP ou Blade.

## Onde está o MVC?

**MVC** é uma forma de separar responsabilidades:

- **Model:** representa e manipula dados e comportamentos do domínio;
- **View:** apresenta a interface ao usuário;
- **Controller:** coordena a requisição e escolhe a resposta.

Neste exemplo, `InicioController` é o Controller e `inicio.blade.php` é a View. Ainda não existe um Model da biblioteca porque a página não consulta nem altera dados. O primeiro Model será acrescentado no assunto de migrations e models.

A rota ajuda a entrada da requisição, mas não é uma quarta camada do MVC. Ela apenas associa um endereço a uma ação do controller.

MVC não significa decorar três definições nem colocar automaticamente toda regra no Model. O objetivo é reconhecer responsabilidades e evitar misturar acesso a dados, controle da requisição e HTML no mesmo arquivo.

## Execute a aplicação

Dentro de `projeto-biblioteca`, execute:

```bash
php artisan serve
```

Mantenha o terminal aberto e acesse [http://localhost:8000](http://localhost:8000). A página deve exibir:

- o título **Biblioteca Web 2**;
- a mensagem de que a aplicação está funcionando;
- três passos que resumem o fluxo da página.

Para encerrar o servidor, pressione `Ctrl+C`.

> **Checkpoint final:** você consegue apontar, no código, qual arquivo recebe o endereço, qual método prepara os dados e qual arquivo gera o HTML.

## Se algo não funcionar

- `Could not open input file: artisan`: entre na pasta `projeto-biblioteca`;
- erro sobre `vendor/autoload.php`: execute `composer install`;
- erro sobre Vite ou `manifest.json`: execute `npm install` e `npm run build`;
- erro sobre `APP_KEY`: execute `php artisan key:generate`;
- VS Code não mostra **WSL: Ubuntu-24.04**: reabra o projeto pelo terminal do Ubuntu com `code .`.

Para outros casos, consulte [Problemas comuns de ambiente](https://github.com/AlexandreSGV/curso-web-1/blob/main/apoio/compartilhado/problemas-comuns/README.md).

## O que você precisa guardar

1. O framework fornece uma estrutura e coordena partes do fluxo.
2. A rota associa o endereço a uma ação.
3. O controller prepara os dados e escolhe a resposta.
4. A view Blade monta o HTML.
5. Um Model será necessário quando a biblioteca começar a trabalhar com dados.

Próximo assunto: **migrations e models**.

## Referências

- [Controllers no Laravel 13](https://laravel.com/docs/13.x/controllers)
- [Views no Laravel 13](https://laravel.com/docs/13.x/views)
- [Blade no Laravel 13](https://laravel.com/docs/13.x/blade)
- [Rotas no Laravel 13](https://laravel.com/docs/13.x/routing)
