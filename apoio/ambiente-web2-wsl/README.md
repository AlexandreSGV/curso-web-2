# Preparação do ambiente de Web 2 no WSL

Este guia prepara o Windows para executar a aplicação Laravel da biblioteca dentro do Ubuntu. Use o **PowerShell** somente para instalar ou verificar o WSL. Depois disso, execute os comandos no terminal do **Ubuntu**.

## O que será instalado

- WSL 2 com Ubuntu 24.04 LTS;
- PHP 8.3 e extensões usadas pelo Laravel;
- Composer;
- Node.js 24 LTS e npm;
- MySQL;
- Visual Studio Code com a extensão WSL.

Web 2 usa Laravel 13, Blade, Vite, `laravel/ui` e Bootstrap. O pacote `laravel/ui` será adicionado mais adiante, quando a turma estudar autenticação.

## 1. Instale ou confirme o WSL

Abra o **PowerShell como administrador** e execute:

```powershell
wsl --install -d Ubuntu-24.04
```

Reinicie o Windows quando for solicitado. Ao abrir o Ubuntu pela primeira vez, crie seu nome de usuário e sua senha do Linux. A senha não aparece na tela enquanto é digitada.

Se o WSL já estava instalado, não reinstale. No PowerShell, execute:

```powershell
wsl -l -v
```

O Ubuntu deve aparecer com a versão `2`.

## 2. Prepare o Ubuntu

Abra o aplicativo **Ubuntu** pelo menu Iniciar. Os próximos comandos devem ser executados nesse terminal.

Atualize a lista de pacotes disponíveis:

```bash
sudo apt update
```

Digite a senha criada no primeiro acesso e pressione `Enter`.

Instale PHP, extensões, Composer, MySQL e utilitários necessários:

```bash
sudo apt install -y php-cli php-curl php-mbstring php-xml php-zip php-mysql composer mysql-server curl git unzip
```

O Ubuntu 24.04 fornece PHP 8.3, versão compatível com Laravel 13.

## 3. Instale Node.js e npm

Use o NVM para instalar a versão 24 LTS do Node.js:

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.6/install.sh | bash
source ~/.bashrc
nvm install 24
```

## 4. Confira as instalações

Execute:

```bash
php -v
composer --version
node -v
npm -v
mysql --version
```

Os resultados esperados são PHP `8.3.x`, Composer `2.x`, Node `v24.x` e MySQL `8.x`. O número do npm pode mudar junto com as atualizações do Node.

Para conferir as extensões do PHP, execute:

```bash
php -m
```

Na lista devem aparecer, entre outras, `curl`, `dom`, `mbstring`, `PDO`, `pdo_mysql`, `xml` e `zip`.

## 5. Prepare o Visual Studio Code

1. Instale o [Visual Studio Code no Windows](https://code.visualstudio.com/download), não dentro do Ubuntu.
2. No VS Code, abra **Extensions** e instale a extensão **WSL**, da Microsoft.
3. Volte ao terminal do Ubuntu.

Os projetos devem ficar no sistema de arquivos do Linux. Crie uma pasta para eles:

```bash
mkdir -p ~/projetos
cd ~/projetos
```

Evite desenvolver dentro de `/mnt/c`, pois as ferramentas do Linux ficam mais lentas nesse local.

Para abrir a pasta atual no VS Code conectado ao Ubuntu, execute:

```bash
code .
```

No canto inferior esquerdo do VS Code deve aparecer **WSL: Ubuntu-24.04**.

## 6. Prepare o banco da biblioteca

Inicie o MySQL e abra seu terminal administrativo:

```bash
sudo service mysql start
sudo mysql
```

No prompt `mysql>`, crie um banco e um usuário exclusivos para o projeto:

```sql
CREATE DATABASE biblioteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'biblioteca'@'localhost' IDENTIFIED BY 'SUA_SENHA_LOCAL';
GRANT ALL PRIVILEGES ON biblioteca.* TO 'biblioteca'@'localhost';
EXIT;
```

Troque `SUA_SENHA_LOCAL` por uma senha criada por você. Não publique essa senha nem o arquivo `.env` no GitHub.

## 7. Crie a aplicação Laravel

Entre na pasta de projetos e crie a aplicação acumulativa da disciplina:

```bash
cd ~/projetos
composer create-project laravel/laravel projeto-biblioteca "^13.0"
cd projeto-biblioteca
npm install
npm run build
```

O primeiro comando pode demorar porque baixa o Laravel e suas dependências.

Abra o projeto no VS Code:

```bash
code .
```

No arquivo `.env`, substitua a configuração do banco pelos campos abaixo:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=biblioteca
DB_PASSWORD=SUA_SENHA_LOCAL
```

Use a mesma senha criada no MySQL. O exemplo mostra apenas um marcador; não copie uma senha real para os materiais da disciplina.

Gere a chave da aplicação:

```bash
php artisan key:generate
```

O próximo comando **altera o banco de desenvolvimento** ao criar as tabelas iniciais. Execute somente depois de confirmar que o `.env` aponta para o banco `biblioteca`:

```bash
php artisan migrate
```

O resultado esperado termina sem erro e informa que as migrations foram executadas.

## 8. Execute e abra no navegador

Dentro de `~/projetos/projeto-biblioteca`, execute:

```bash
php artisan serve
```

Mantenha o terminal aberto e acesse [http://localhost:8000](http://localhost:8000) no navegador do Windows. A página inicial do Laravel deve aparecer.

Para encerrar o servidor, volte ao terminal e pressione `Ctrl+C`.

## Se o projeto já foi fornecido pelo professor

Entre na pasta do projeto e instale as dependências:

```bash
composer install
npm install
```

Se ainda não existir um arquivo `.env`, crie-o a partir do exemplo:

```bash
cp .env.example .env
php artisan key:generate
```

Depois configure o banco, execute `npm run build` e rode a migration somente no banco local de desenvolvimento conhecido.

## Problemas mais comuns

### `nvm`: comando não encontrado

Feche e abra o Ubuntu ou execute `source ~/.bashrc` antes de tentar novamente.

### `code`: comando não encontrado

Confirme que o VS Code foi instalado no Windows com a opção de adicioná-lo ao PATH e que a extensão WSL está instalada. Depois, reabra o Ubuntu.

### `Could not open input file: artisan`

O terminal está na pasta errada. Entre em `~/projetos/projeto-biblioteca` e repita o comando.

### O Laravel não conecta ao MySQL

Inicie o serviço com `sudo service mysql start` e confira banco, usuário, senha e porta no `.env`. Não altere credenciais aleatoriamente nem publique o arquivo para pedir ajuda.

### A porta 8000 está ocupada

Use outra porta:

```bash
php artisan serve --port=8001
```

Depois acesse [http://localhost:8001](http://localhost:8001).

## Referências oficiais

- [Instalação do WSL](https://learn.microsoft.com/windows/wsl/install)
- [Ambiente de desenvolvimento no WSL](https://learn.microsoft.com/windows/wsl/setup/environment)
- [VS Code com WSL](https://code.visualstudio.com/docs/remote/wsl)
- [PHP em distribuições baseadas em Debian](https://www.php.net/manual/en/install.unix.debian.php)
- [Composer no Linux](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- [Node.js 24 LTS](https://nodejs.org/en/about/previous-releases)
- [Instalação do MySQL no Ubuntu](https://ubuntu.com/server/docs/install-and-configure-a-mysql-server)
- [Instalação do Laravel 13](https://laravel.com/docs/13.x/installation)
- [Requisitos do Laravel 13](https://laravel.com/docs/13.x/deployment#server-requirements)
