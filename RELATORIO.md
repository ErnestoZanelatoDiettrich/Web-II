# Relatório Técnico — CriticaJá

## Tema escolhido
Sistema de avaliação de jogos no estilo Metacritic, com dois perfis de usuário (usuário comum e crítico) que geram, respectivamente, a "Nota dos usuários" e o "Metascore" de cada jogo.

## Arquitetura
O projeto segue uma estrutura **MVC simplificada** em PHP puro, sem frameworks:

- **Models** (`app/models`): classes estáticas responsáveis exclusivamente pelo acesso ao banco via PDO (Usuario, Jogo, Avaliacao).
- **Controllers** (`app/controllers`): concentram a lógica de negócio e validação (AuthController, GameController, ReviewController, ReportController), recebendo dados da requisição e devolvendo um array `['sucesso' => bool, ...]`.
- **Views** (`app/views`): apenas apresentação, com escape de saída via função `e()`.
- **Front Controller** (`public/index.php`): roteamento simples baseado no parâmetro `?page=`, evitando duplicar `<head>`/`<nav>` em cada arquivo e centralizando decisões como exigir login.

Essa separação foi escolhida por ser simples de entender e avaliar, mantendo ainda assim a separação de responsabilidades pedida no trabalho, sem a complexidade de um roteador de URLs amigáveis.

## Banco de dados
Quatro tabelas: `usuarios`, `jogos`, `avaliacoes` e `redefinicoes_senha`. A tabela `avaliacoes` tem uma constraint `UNIQUE(jogo_id, usuario_id)` para impedir notas duplicadas do mesmo usuário no mesmo jogo, e uma `CHECK` garantindo nota entre 0 e 100. O Metascore e a nota de usuários **não são armazenados**, são calculados via `AVG()` agrupando por tipo de avaliação (`critica` ou `usuario`) — isso evita inconsistência entre o valor salvo e as avaliações reais.

## Segurança
- Todas as queries usam **prepared statements** via PDO (`PDO::ATTR_EMULATE_PREPARES => false`), prevenindo SQL Injection.
- Senhas nunca são armazenadas em texto plano: usa-se `password_hash()`/`password_verify()`.
- Toda saída de dados do usuário passa pela função `e()` (wrapper de `htmlspecialchars`), mitigando XSS.
- Todos os formulários incluem um **token CSRF** validado no back-end antes de qualquer ação de escrita.
- Sessão é regenerada no login (`session_regenerate_id`) para reduzir risco de fixação de sessão.
- A recuperação de senha não revela se um e-mail existe ou não na base (mensagem genérica), reduzindo enumeração de usuários.

## Validação
Front-end: atributos HTML5 (`required`, `minlength`, `min`, `max`, `type=email`) combinados com a classe Bootstrap `needs-validation` e um pequeno script (`validation.js`) que também confirma se senha e confirmação de senha coincidem.
Back-end: cada controller revalida os mesmos campos antes de tocar o banco — a validação client-side é apenas uma conveniência de UX, nunca a única barreira.

## Relatórios
A página de relatório permite filtrar por gênero/plataforma e exportar os resultados em **CSV** (compatível com Excel), reaproveitando a mesma consulta usada na listagem de jogos, para manter uma única fonte de verdade dos dados exibidos.

## Limitações (por ser um esboço)
- E-mail de recuperação de senha é simulado (exibido em tela, não enviado de fato).
- Sem upload de imagem de capa (apenas URL externa).
- Sem paginação na listagem (aceitável para o volume de dados de um esboço acadêmico).
- Estilo visual mínimo, usando Bootstrap padrão.
