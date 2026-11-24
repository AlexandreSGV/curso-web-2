# Storage no Laravel: arquivos e capas de livros

Nesta aula, vamos guardar imagens de capa na biblioteca da [aula 11](../11-autenticacao-autorizacao/). O arquivo fica no disco; a tabela `books` guarda o caminho da capa. As mesmas ideias servem para fotos de perfil, anexos e outros arquivos enviados ao sistema.

## 1. Como o Storage funciona

O Laravel chama de **disco** uma configuração de armazenamento em `config/filesystems.php`. O disco indica como acessar os arquivos e onde guardá-los. Por exemplo, `Storage::disk('public')` seleciona o disco público, e `delete('covers/livro.jpg')` remove esse caminho relativo à raiz do disco.

| Disco | Onde ficam os arquivos | Exemplo de uso |
|---|---|---|
| `local` | `storage/app/private`, na configuração padrão | Comprovantes, relatórios internos e outros arquivos com acesso restrito |
| `public` | `storage/app/public`, na configuração padrão | Capas, fotos de produtos e outras imagens que podem ser abertas por URL |
| `s3` | Em um bucket, um contêiner de arquivos em um serviço de armazenamento | Arquivos compartilhados por várias instâncias da aplicação |

Os discos `local` e `public` usam o disco do próprio servidor. A diferença é como os arquivos ficam disponíveis à Web. Quando você não usa `disk(...)`, a facade `Storage` escolhe o disco padrão, definido por `FILESYSTEM_DISK`. Nesta aula selecionamos `public` diretamente, sem mudar o `.env`.

## 2. O que é o link de Storage?

O diretório `public/` é a parte do projeto atendida pelo servidor Web. Por padrão, uma imagem salva em `storage/app/public` fica fora dele. Execute, na raiz do projeto:

```bash
php artisan storage:link
```

O comando cria um **link simbólico**: a entrada `public/storage` aponta para `storage/app/public`. É como um atalho para a pasta. O arquivo continua no mesmo lugar; o link não faz uma cópia.

| Onde o Laravel grava | Caminho guardado no banco | Endereço que o navegador abre |
|---|---|---|
| `storage/app/public/covers/abc.jpg` | `covers/abc.jpg` | `/storage/covers/abc.jpg` |

O [Laravel também fornece](https://laravel.com/docs/13.x/filesystem#the-public-disk) `php artisan storage:unlink`: ele remove os links configurados e preserva os arquivos. Use `storage:link` de novo se precisar recriar o link. Em cada ambiente em que a aplicação rodar com o disco público local, o link deve existir.

**Arquivos públicos não passam pelas permissões da tela.** As rotas da biblioteca exigem login, mas quem tem a URL de uma capa pode abrir o arquivo diretamente. Para um documento restrito, use um disco privado e uma rota que confira a permissão antes de entregá-lo, ou gere uma URL temporária depois dessa verificação.

## 3. Operações comuns

Dentro de um Controller ou outro código PHP, importe a facade:

```php
use Illuminate\Support\Facades\Storage;

$disk = Storage::disk('public');
```

| Código | O que faz |
|---|---|
| `$file->store('covers', 'public')` | Grava um upload com nome gerado e devolve o caminho relativo se der certo |
| `$disk->put('arquivo.txt', 'Conteúdo')` | Grava conteúdo em um caminho escolhido |
| `$disk->exists($path)` | Verifica se o arquivo existe |
| `$disk->url($path)` | Gera a URL conforme o disco configurado |
| `$disk->download($path)` | Monta uma resposta de download; use depois de verificar o acesso |
| `$disk->delete($path)` | Remove o arquivo |

`storage:link` e `storage:unlink` são comandos do terminal. `store()`, `put()` e `delete()` são chamadas feitas no código da aplicação.

## 4. Quando usar disco local ou serviço externo?

| Opção | Quando faz sentido | Custos a considerar |
|---|---|---|
| Disco no computador ou servidor | Aula, desenvolvimento e aplicações com uma instância e volume controlado | Espaço em disco, servidor, cópias de segurança e manutenção |
| Volume persistente ou pasta de rede montada no servidor | Aplicações que precisam manter os arquivos entre implantações, ou servidores que usam um volume compartilhado | Capacidade, desempenho e operação do volume e dos backups |
| Serviço de objetos, como Amazon S3, Cloudflare R2 ou DigitalOcean Spaces | Várias instâncias da aplicação, volume crescente ou entrega de muitos arquivos | Volume armazenado, operações, saída de dados e, em alguns planos, recuperação de arquivos |
| Servidor externo via SFTP | Integração com um servidor de arquivos já usado pela instituição | Hospedagem do servidor ou mensalidade do serviço, transferência e administração |

O Laravel possui suporte a discos locais, SFTP e S3, além de serviços compatíveis com a API do S3. Para ligar um bucket S3 ou compatível, instale o adaptador e configure o disco:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

Em `config/filesystems.php`, a configuração do disco `s3` lê do `.env` as credenciais, a região e o bucket. Serviços compatíveis podem exigir `AWS_ENDPOINT`, o endereço do serviço. Esse disco é acessado com `Storage::disk('s3')`; ele não precisa de `storage:link`. Mudar a configuração não transfere os arquivos antigos: para migrar, é preciso copiar os objetos e conferir caminhos e permissões.

**Como estimar o custo.** No [Amazon S3](https://aws.amazon.com/s3/pricing/), confira classe, região, requisições e transferência de dados. No [Cloudflare R2 Standard](https://developers.cloudflare.com/r2/pricing/), a tarifa publicada de armazenamento é de US$ 0,015 por GB-mês. Se 100 GB fossem faturados nessa tarifa, só o armazenamento custaria US$ 1,50 no mês. O R2 cobra operações à parte e não cobra a saída direta de dados para a internet; confira franquias, serviços adicionais e preços atualizados antes de contratar.

**Quem usa muitos arquivos?** Uma loja virtual guarda fotos e miniaturas de produtos; uma plataforma de cursos armazena vídeos e materiais; um sistema de chamados recebe fotos e documentos; um portal de notícias publica imagens e áudios. Vídeos pesam no armazenamento e no tráfego. Já um catálogo com muitas fotos pequenas pode gerar muitas requisições. Os custos dependem tanto do tamanho dos arquivos quanto de quantas vezes são enviados ou abertos.

## 5. Capas de livros na biblioteca

O [código completo](exemplos/) já inclui os arquivos das aulas anteriores. A [nova migration](exemplos/database/migrations/2026_11_17_000001_add_cover_to_books_table.php) acrescenta à tabela `books`:

```php
$table->string('cover')->nullable();
```

Cada livro pode ter uma capa. Quando há imagem, `cover` recebe um caminho como `covers/abc.jpg`. Quando não há, recebe `null`. O [Model Book](exemplos/app/Models/Book.php) inclui esse campo em `Fillable`, e o Controller só atribui a ele o caminho gerado no servidor.

### Enviar uma imagem

As telas de cadastro e edição usam `enctype="multipart/form-data"` no formulário para que o navegador envie o arquivo. As duas reutilizam este campo:

```blade
<label for="cover">Capa (opcional)</label>
<input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp">
```

`accept` ajuda na escolha no navegador. O [BookController](exemplos/app/Http/Controllers/BookController.php) valida o arquivo no servidor e aceita JPG, PNG ou WebP de até 2 MB.

Depois da validação, as ações `store()` (cadastro) e `update()` (edição) usam `hasFile('cover')` para conferir se chegou uma imagem. As duas ações gravam o arquivo com a mesma chamada.

```php
$coverPath = $request->file('cover')->store('covers', 'public');
```

O Laravel gera um nome para a imagem, grava o arquivo em `storage/app/public/covers` e devolve um caminho como `covers/abc.jpg`. O Controller salva esse caminho no campo `cover` do livro.

### Trocar ou excluir uma capa

| Ação | O que acontece |
|---|---|
| Cadastrar sem imagem | `cover` fica nulo; aparece a capa padrão |
| Editar sem enviar imagem nem marcar remoção | O caminho e o arquivo atuais são mantidos |
| Enviar outra imagem | O Controller grava a imagem nova, salva seu caminho no livro e só depois exclui a antiga |
| Marcar **Remover a capa atual** sem enviar outra | O Controller salva `null` e remove o arquivo; a capa padrão volta a aparecer |
| Excluir o livro | Depois de excluir o registro, o Controller apaga o arquivo da capa se houver |

Se marcar **Remover a capa atual** e enviar outra imagem no mesmo formulário, a nova imagem prevalece. Se a gravação no banco falhar, o Controller remove a imagem recém-enviada e mantém a anterior. O banco e o disco são independentes: `$book->delete()` remove o registro, e `Storage::disk('public')->delete($path)` remove o arquivo.

### Exibir a capa

A [View parcial da capa](exemplos/resources/views/books/_cover.blade.php) é reutilizada na listagem, nos detalhes e na edição. Ela escolhe a imagem da seguinte forma:

```blade
{{ $book->cover === null
    ? asset('images/cover-default.svg')
    : asset('storage/' . $book->cover) }}
```

A [capa padrão](exemplos/public/images/cover-default.svg) fica em `public/images`, pois é parte da interface. Ela não é guardada em `cover` e continua disponível após excluir livros.

## 6. Praticar

Siga o [guia do exemplo](exemplos/README.md) para copiar os arquivos, rodar as migrations e criar o link de Storage.

1. Entre como bibliotecário e cadastre um livro sem capa. Confira a imagem padrão.
2. Envie uma imagem na edição. Abra o livro e localize o arquivo em `storage/app/public/covers`.
3. Edite só o título e veja que a capa continua a mesma. Depois envie uma imagem diferente e confira que a anterior saiu da pasta.
4. Marque **Remover a capa atual** e confirme que a imagem padrão voltou.
5. Adicione uma capa novamente. Entre como administrador, exclua o livro de teste e confira que o arquivo sumiu.

Referências: [Laravel 13 — File Storage](https://laravel.com/docs/13.x/filesystem) e [Laravel 13 — validação de arquivos](https://laravel.com/docs/13.x/validation#validating-files).
