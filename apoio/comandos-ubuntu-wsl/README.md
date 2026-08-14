# Comandos do Ubuntu e WSL para Web 2

Use esta folha para consultar os comandos mais frequentes da disciplina. Salvo quando indicado, execute tudo no terminal do **Ubuntu**.

## Localizar-se e navegar

Mostrar a pasta atual:

```bash
pwd
```

Listar os arquivos e pastas:

```bash
ls
```

Listar também os arquivos ocultos, como `.env`:

```bash
ls -la
```

Entrar na aplicação da biblioteca:

```bash
cd ~/projetos/projeto-biblioteca
```

Voltar uma pasta:

```bash
cd ..
```

Voltar para sua pasta pessoal:

```bash
cd ~
```

## Criar, copiar e renomear

Criar uma pasta:

```bash
mkdir exemplos
```

Criar um arquivo vazio:

```bash
touch anotacoes.md
```

Copiar o arquivo de exemplo do ambiente:

```bash
cp .env.example .env
```

Renomear um arquivo:

```bash
mv anotacoes.md comandos.md
```

Antes de copiar ou renomear, use `pwd` e `ls` para confirmar que está na pasta correta.

## Abrir e controlar o terminal

Abrir a pasta atual no VS Code conectado ao WSL:

```bash
code .
```

Limpar a tela:

```bash
clear
```

Interromper um servidor ou outro processo em execução:

```text
Ctrl+C
```

Fechar o terminal do Ubuntu:

```bash
exit
```

## Conferir o ambiente

```bash
php -v
composer --version
node -v
npm -v
mysql --version
```

No **PowerShell do Windows**, conferir se o Ubuntu usa WSL 2:

```powershell
wsl -l -v
```

## MySQL local

Iniciar o serviço:

```bash
sudo service mysql start
```

Entrar com o usuário da biblioteca:

```bash
mysql -u biblioteca -p
```

Digite a senha quando for solicitada. Para sair do prompt `mysql>`, use:

```sql
EXIT;
```

## Projeto Laravel da biblioteca

Entre no projeto antes de usar comandos Artisan:

```bash
cd ~/projetos/projeto-biblioteca
```

Ver informações do Laravel e do ambiente:

```bash
php artisan about
```

Listar as rotas da aplicação:

```bash
php artisan route:list
```

Iniciar o servidor local:

```bash
php artisan serve
```

Acesse [http://localhost:8000](http://localhost:8000) e encerre o servidor com `Ctrl+C`.

Instalar as dependências registradas no projeto:

```bash
composer install
npm install
```

Gerar os arquivos de CSS e JavaScript com Vite:

```bash
npm run build
```

Gerar a chave quando um `.env` novo tiver sido criado:

```bash
php artisan key:generate
```

Aplicar migrations pendentes:

```bash
php artisan migrate
```

> `php artisan migrate` altera o banco configurado no `.env`. Use-o somente depois de confirmar que a aplicação aponta para o banco local de desenvolvimento.

Os comandos de Git ficam no tutorial próprio da disciplina e não são repetidos nesta folha.
