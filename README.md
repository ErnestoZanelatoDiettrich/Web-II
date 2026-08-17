# Trustic 🛡️🎮

**Trustic** é um sistema completo de notas e críticas de jogos, no estilo Metacritic, desenvolvido em **PHP puro + MySQL** (PDO) para o Trabalho T2 da disciplina Web II.

Usuários se cadastram como **usuário comum**, **crítico** ou **admin**. Críticos podem cadastrar jogos e suas notas contam como "Metascore" (crítica oficial); usuários comuns avaliam os jogos e formam a "Nota dos usuários"; administradores moderam a plataforma.

## Funcionalidades (v2.0)

### Já existentes na v1
- Cadastro, login, logout e recuperação de senha por token
- CRUD completo de jogos
- Avaliações com nota (0-100) e comentário, Metascore e nota de usuários calculados automaticamente
- Filtros de busca, relatório e exportação CSV
- Segurança: PDO com prepared statements, `password_hash`, escape de saída (XSS), token CSRF

### Novidades da v2.0 "Trustic"
- **Novo tema visual** dark, inspirado no Metacritic, com identidade própria (cores, tipografia, badges de nota)
- **Banco normalizado**: gêneros e plataformas agora são tabelas próprias (com `datalist` para sugerir os já cadastrados, mas permitindo criar novos)
- **Papel de administrador**: painel para promover/rebaixar usuários e excluir contas problemáticas
- **Perfis de usuário**: página pública mostrando bio, avatar, jogos cadastrados e avaliações; edição do próprio perfil com **upload de avatar**
- **Upload de imagem de capa** dos jogos (além da opção anterior de URL), com validação real do tipo de arquivo (`finfo`) e limite de tamanho
- **Favoritos**: qualquer usuário logado pode favoritar jogos e ver sua lista em "Meus favoritos"
- **Home page com destaques**: seções "Mais bem avaliados" e "Lançamentos recentes"
- **Ordenação e paginação** no catálogo de jogos
- **Moderação**: administradores podem excluir qualquer jogo ou avaliação, não apenas os próprios
- **Relatório aprimorado**: exportação CSV mantida + botão "Imprimir / Salvar como PDF" (usa o CSS de impressão do navegador — abordagem simples e sem dependências externas para gerar PDF)

## Estrutura de pastas

```
Trustic/
├── config/
│   ├── config.php        # sessão, helpers de autenticação/autorização, CSRF, escape XSS
│   └── database.php      # conexão PDO com o MySQL
├── app/
│   ├── controllers/      # Auth, Game, Review, Favorite, Profile, Admin, Report
│   ├── models/            # Usuario, Jogo, Avaliacao, Favorito, Taxonomia (Genero/Plataforma)
│   ├── helpers/
│   │   └── upload.php    # upload seguro de imagens (capas e avatares)
│   └── views/              # home, auth, games, perfil, admin, relatorios, layout
├── public/
│   ├── index.php         # front controller (roteamento via ?page=)
│   ├── css/style.css     # identidade visual do Trustic
│   ├── js/validation.js
│   ├── img/               # placeholders (avatar padrão, sem capa)
│   └── uploads/            # capas e avatares enviados pelos usuários
└── database.sql          # script de criação do banco + dados de exemplo
```

## Como executar

1. **Banco de dados**: crie o schema executando o script:
   ```bash
   mysql -u root -p < database.sql
   ```
2. **Credenciais**: ajuste usuário/senha do MySQL em `config/database.php` se necessário.
3. **Permissões de upload**: garanta que `public/uploads/capas` e `public/uploads/avatares` tenham permissão de escrita pelo servidor web.
4. **Servidor**: aponte o *document root* para a pasta `public/`. Para testar rapidamente com o servidor embutido do PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Acesse `http://localhost:8000` no navegador.

### Login de teste
Os usuários de exemplo do `database.sql` usam a senha **`123456`**:
- `admin@trustic.com` (admin — acessa o painel em "Admin")
- `ana@trustic.com` (crítico)
- `bruno@trustic.com` (usuário comum)

### Sobre a recuperação de senha
Este é um sistema acadêmico sem servidor de e-mail configurado. Ao solicitar a recuperação, o sistema gera um token válido por 1 hora e exibe o link de redefinição diretamente na tela, apenas para fins de demonstração.

### Sobre a exportação em PDF
Para manter o projeto livre de dependências externas (sem Composer/bibliotecas de terceiros), a exportação em PDF é feita via **impressão do navegador** (`window.print()` com CSS de impressão dedicado), que qualquer navegador salva como PDF. A exportação em CSV/Excel permanece disponível como alternativa direta.

## Tecnologias

PHP puro (PDO), MySQL, HTML5, CSS3 (customizado), Bootstrap 5, Bootstrap Icons, JavaScript.

## Diagrama do banco (resumo)

```
usuarios (1) ──< jogos (criado_por)
usuarios (1) ──< redefinicoes_senha
usuarios (1) ──< avaliacoes >── (1) jogos
usuarios (N) ──< favoritos >── (N) jogos
generos  (1) ──< jogos
plataformas (1) ──< jogos
```
- Chave única `(jogo_id, usuario_id)` em `avaliacoes` garante uma avaliação por usuário por jogo.
- Chave primária composta `(usuario_id, jogo_id)` em `favoritos` evita duplicidade na lista de favoritos.
