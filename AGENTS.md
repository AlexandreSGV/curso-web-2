# Orientações permanentes — Web 2

## Autoridade e fontes

- As decisões de `../planejamento/` têm prioridade. Havendo conflito, não reproduza a orientação antiga.
- Os slides, atividades e avaliações de `../fontes-atuais/web-2/` são a principal base de produção. Comece pelo catálogo `../fontes-atuais/README.md` e abra somente os arquivos e páginas relacionados ao assunto em produção.
- Prefira corrigir, atualizar, simplificar e reorganizar o material existente a recriá-lo. Preserve identidade, exemplos, sequências e explicações que continuem corretos.
- Não copie automaticamente todo o conteúdo antigo. Retire excessos e itens fora do plano; corrija erros técnicos/editoriais, links, versões e formas de entrega desatualizadas. Produza conteúdo novo quando houver lacuna real.

## Alunos e linguagem

- Use a mesma abordagem no Técnico e no Tecnólogo. Produza para alunos que avançam dos fundamentos web para framework, MVC, ORM e integração entre aplicação, banco e API, inclusive quem precisar recuperar uma aula sem a explicação presencial.
- Use português simples, apresente termos técnicos quando necessários e explique o propósito antes de comandos ou código. Divida construções em passos testáveis, com arquivos, checkpoints e resultados esperados claros.
- Diferencie o essencial do aprofundamento opcional, retire o apoio gradualmente e não use recursos ainda não ensinados apenas para sofisticar o projeto.

## Tecnologia e organização

- Use WSL com PHP, Composer, Node, MySQL e Laravel; siga as convenções de Blade, Eloquent, Vite, `laravel/ui` e Bootstrap. Cubra migrations, validação, autenticação, autorização, storage e API REST conforme a sequência aprovada.
- Corrija as inconsistências registradas no catálogo: não descreva eager loading como consulta única, não trate método de relacionamento como coleção, não concentre automaticamente toda regra no model, não edite migration já executada e não use Laravel Mix ou `$request->all()` nos exemplos atualizados.
- Use a biblioteca simplificada como domínio principal. Evolua uma única aplicação em `projeto-biblioteca/`, de Book até relacionamentos, autenticação, autorizações, empréstimos, upload e API.
- O `README.md` da raiz é o índice. Cada assunto fica em `unidade-1/` ou `unidade-2/`, com apostila em `README.md`; somente pequenos complementos e `atividade.md` ficam no assunto quando úteis. Google Slides e Forms são apenas vinculados.
- O kit é flexível: assuntos podem ser reunidos, ocupar vários encontros ou compartilhar recursos. Não imponha seções, páginas, slides, exemplos ou atividades artificiais. Tutoriais comuns devem ter uma única versão canônica apontada por `apoio/`.
- Não mantenha cópias quase iguais da aplicação Laravel. As apostilas apontam para a etapa correspondente; commits e, quando útil, tags preservam a evolução.
- React limita-se a duas práticas introdutórias em JavaScript, incluindo consumo da API, sem nota obrigatória. Não inclua SPA completa, Inertia, Tailwind obrigatório, Redux, Next.js, JWT ou TypeScript adicional.

## Restrições

- Não versione `vendor/`, `node_modules/`, `.env`, credenciais ou temporários; não exija branches por atividade, pull requests, GitHub Projects, testes automatizados ou PDF obrigatório dos slides.
- Mantenha gabaritos, rubricas internas e Eventix fora deste repositório. Em cada unidade: 4 a 6 atividades somam 20%, a teórica individual no Google Forms vale 40% e a prática em dupla vale 40%; os minitestes podem acrescentar até 0,5 ponto. Avaliações antigas servem apenas como fonte seletiva de questões fechadas.
- Google Classroom concentra avisos, prazos, entregas e notas. Cada aluno usa um repositório privado de Web 2, adiciona `AlexandreSGV`, mantém a biblioteca em uma pasta e entrega somente o link geral.
