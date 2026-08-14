<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    <main class="container">
        <p class="etapa">Projeto acumulativo de Web 2</p>
        <h1>{{ $titulo }}</h1>
        <p class="mensagem">{{ $mensagem }}</p>

        <section aria-labelledby="fluxo">
            <h2 id="fluxo">Fluxo desta página</h2>
            <ol>
                <li>A rota recebe o acesso a <code>/</code>.</li>
                <li>O controller prepara o título e a mensagem.</li>
                <li>A view Blade monta o HTML mostrado no navegador.</li>
            </ol>
        </section>
    </main>
</body>
</html>
