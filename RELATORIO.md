# Relatório Técnico — Trustic 2.0

## Tema e evolução
O Trustic é a evolução de um esboço acadêmico anterior (CriticaJá) para um sistema mais completo de avaliação de jogos, mantendo os dois perfis de nota do Metacritic real (Metascore de críticos vs. nota da comunidade), agora com identidade visual própria, contas administrativas, perfis públicos e upload de imagens.

## Arquitetura
Estrutura **MVC simplificada** em PHP puro, sem frameworks, organizada em:

- **Models** (`app/models`): acesso a dados via PDO (`Usuario`, `Jogo`, `Avaliacao`, `Favorito`, `Genero`/`Plataforma` em `Taxonomia.php`).
- **Controllers** (`app/controllers`): validação e lógica de negócio (Auth, Game, Review, Favorite, Profile, Admin, Report), sempre retornando `['sucesso' => bool, ...]` para a view decidir o que exibir.
- **Helpers** (`app/helpers/upload.php`): função reutilizável de upload seguro de imagem, usada tanto para capas de jogos quanto para avatares de usuário.
- **Views** (`app/views`): apenas apresentação, com escape via `e()`.
- **Front Controller** (`public/index.php`): roteamento via `?page=`, centralizando checagens de autorização (`exigirLogin`, `exigirCritico`, `exigirAdmin`).

## Banco de dados — o que mudou da v1
A v1 guardava gênero e plataforma como texto livre repetido em cada jogo. Na v2, `generos` e `plataformas` viraram tabelas próprias referenciadas por chave estrangeira (`genero_id`, `plataforma_id`), eliminando redundância e inconsistência de digitação (ex.: "RPG" vs "rpg"). Ao cadastrar um jogo, o sistema busca o gênero/plataforma existente pelo nome ou cria um novo automaticamente (`Genero::buscarOuCriar`), com sugestões via `<datalist>` no formulário para orientar o usuário a reaproveitar os já existentes.

Foram adicionadas as tabelas `favoritos` (chave primária composta `usuario_id + jogo_id`, evitando duplicidade) e os campos `avatar_url`/`bio` em `usuarios`. O enum `tipo` de `usuarios` ganhou o valor `admin`.

Metascore e nota de usuários continuam **calculados via `AVG()`** agrupando por tipo de avaliação, nunca armazenados, para não haver risco de divergência entre o valor exibido e as avaliações reais.

## Segurança
- Todas as queries usam **prepared statements** via PDO (`PDO::ATTR_EMULATE_PREPARES => false`).
- Senhas com `password_hash()`/`password_verify()`; sessão regenerada no login.
- Toda saída passa por `e()` (wrapper de `htmlspecialchars`), mitigando XSS.
- Token **CSRF** validado no back-end em todo POST.
- **Upload de imagens**: o tipo do arquivo é verificado pelo conteúdo real (`finfo_file`), não pela extensão informada pelo navegador; nome do arquivo salvo é gerado aleatoriamente (`random_bytes`), evitando colisões e ataques de path traversal; tamanho limitado a 2MB.
- **Autorização em camadas**: além de exigir login, o sistema verifica papéis (`exigirCritico`, `exigirAdmin`) e posse do recurso (só o autor do jogo/avaliação ou um admin pode editar/excluir), reforçado tanto na view (esconder botões) quanto no controller (checagem real antes de qualquer escrita).

## Validação
Front-end: atributos HTML5 (`required`, `minlength`, `min`, `max`, `type=email`, `accept` para arquivos) combinados com Bootstrap `needs-validation` e `validation.js`, que também confere a confirmação de senha e mostra preview de imagem antes do envio.
Back-end: cada controller revalida os mesmos campos — a validação client-side é conveniência de UX, nunca a única barreira.

## Funcionalidades novas e a decisão por trás de cada uma
- **Perfis públicos e favoritos**: dão ao sistema uma dimensão social, permitindo entender quem avaliou o quê e reunir jogos de interesse — recursos centrais em qualquer agregador de notas real.
- **Painel admin**: necessário a partir do momento em que existem múltiplos críticos/usuários; permite moderar conteúdo problemático sem acesso direto ao banco.
- **Home com destaques**: evita que o usuário caia direto em uma lista fria de filtros, aproximando a experiência do Metacritic real.
- **Paginação e ordenação**: preparam o sistema para volumes maiores de dados do que os poucos registros de exemplo.
- **Relatório com opção de impressão/PDF**: como o enunciado aceita PDF *ou* Excel, e adicionar uma biblioteca de geração de PDF (ex. Dompdf) exigiria Composer e dependências externas fora do escopo "PHP puro", optou-se por uma solução nativa do navegador (CSS de impressão dedicado), mantendo o projeto livre de dependências, e o CSV continua disponível para quem preferir abrir no Excel.

## Limitações conhecidas
- E-mail de recuperação de senha é simulado (exibido em tela, não enviado de fato).
- PDF gerado via impressão do navegador, não por biblioteca server-side.
- Sem testes automatizados (fora do escopo do trabalho).
