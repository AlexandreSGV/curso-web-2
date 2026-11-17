# Exemplo: matrículas de alunos em turmas

Arquivos criados ou alterados para a [aula de relacionamento N–N](../README.md). Copie-os para um projeto Laravel 13 novo, sem starter kit. O Laravel fornece o Model `User`, a estrutura inicial, as dependências e as migrations padrão.

## 1. Criar o projeto

Com PHP 8.3 ou superior, Composer e suporte a SQLite, baixe o repositório se ainda não o tiver. No terminal WSL/Ubuntu ou Linux:

```bash
git clone https://github.com/AlexandreSGV/curso-web-2.git
laravel new matriculas
```

Crie o projeto ao lado da pasta `curso-web-2`. No instalador, escolha **sem starter kit**, **Blade** quando solicitado e **SQLite**. Se o comando `laravel` não estiver instalado, prepare o ambiente conforme o [guia da disciplina](../../../apoio/ambiente-web2-wsl/).

## 2. Copiar os arquivos e rodar

Ainda na pasta que contém `curso-web-2` e `matriculas`:

```bash
origem=curso-web-2/unidade-1/12-relacionamento-n-n/exemplos
cp -R "$origem/app/." matriculas/app/
cp -R "$origem/database/." matriculas/database/
cp -R "$origem/resources/." matriculas/resources/
cp -R "$origem/routes/." matriculas/routes/
cd matriculas
php artisan migrate --seed
php artisan serve
```

A cópia substitui o `routes/web.php` e o `DatabaseSeeder.php` do projeto novo. O Seeder cria a conta de aula, um professor, duas turmas, três alunos e as matrículas iniciais. Os formulários usam HTML e Blade; não é preciso compilar arquivos de front-end.

Acesse [http://localhost:8000/login](http://localhost:8000/login) ou a porta informada no terminal. A conta de demonstração é `professor@example.com`, com senha `senha-aula-123`. Qualquer usuário autenticado pode operar o exemplo. O cadastro de professores representa quem leciona nas turmas; esse cadastro é separado da conta de login.

## 3. Encontrar as telas

| Endereço | O que fazer |
|---|---|
| `/professores` | Gerenciar professores e consultar suas turmas |
| `/turmas` | Gerenciar turmas e escolher o professor responsável |
| `/alunos` | Gerenciar alunos e consultar suas matrículas |
| `/matriculas/ids` | Informar os IDs da turma e do aluno |
| `/matriculas/selects` | Escolher a turma e o aluno nas listas |
| `/turmas/{turma}` | Matricular pelo select, consultar a lista e desmatricular |

Nos dados iniciais, **Ana Lima** está nas duas turmas, **Bruno Silva** está em Web 1 e **Carla Souza** ainda não está matriculada. Esses dados permitem experimentar as três formas sem precisar cadastrar os primeiros registros à mão.
