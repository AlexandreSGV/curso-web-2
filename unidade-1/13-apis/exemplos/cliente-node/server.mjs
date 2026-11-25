import { createServer } from 'node:http';
import { readFile } from 'node:fs/promises';

// Cada caminho da página aponta para um arquivo e para o tipo de conteúdo da resposta.
const files = {
    '/': ['index.html', 'text/html'],
    '/app.js': ['app.js', 'text/javascript'],
    '/style.css': ['style.css', 'text/css'],
};

createServer(async (request, response) => {
    // O pathname é a parte da URL usada para escolher o arquivo.
    const path = new URL(request.url, 'http://127.0.0.1:3000').pathname;
    const file = Object.hasOwn(files, path) ? files[path] : null;

    if (request.method !== 'GET' || !file) {
        response.writeHead(404).end('Página não encontrada.');
        return;
    }

    const [name, type] = file;
    try {
        // Usamos a URL de server.mjs como base para encontrar a pasta public.
        const content = await readFile(new URL('./public/' + name, import.meta.url));
        response.writeHead(200, { 'Content-Type': type + '; charset=utf-8' }).end(content);
    } catch {
        response.writeHead(500).end('Não foi possível ler o arquivo.');
    }
}).listen(3000, '127.0.0.1', () => console.log('Cliente em http://127.0.0.1:3000'));
