# CriticaJá 🎮

Esboço simplificado de um sistema estilo **Metacritic**, desenvolvido em **PHP puro + MySQL** para o Trabalho T2 da disciplina Web II.

Usuários se cadastram como **usuário comum** ou **crítico**. Críticos podem cadastrar jogos e suas notas contam como "Metascore" (crítica oficial); usuários comuns avaliam os jogos e formam a "Nota dos usuários" — exatamente como no Metacritic real, só que bem mais simples.

## Funcionalidades

- Cadastro de usuários com senha protegida por `password_hash`
- Login / logout com sessões PHP
- Recuperação de senha por token (simulação de e-mail — ver observação abaixo)
- CRUD completo de **Jogos** (criar, listar, editar, excluir) restrito a críticos
- Avaliações (notas de 0 a 100 + comentário) por usuários e críticos, com Metascore e nota de usuários calculados automaticamente (média)
- Filtros de busca por título, gênero e plataforma
- Relatório em tela + exportação **CSV** (abre no Excel) com os mesmos filtros
- Validação de formulários no front-end (HTML5 + JavaScript) e no back-end (PHP)
- Proteções de segurança: prepared statements (PDO), hash de senha, escape de saída (XSS) e token CSRF em todos os formulários

## Estrutura de pastas

```
Web-II-main/
├── config/
│   ├── config.php        # sessão, helpers de autenticação, CSRF, escape XSS
│   └── database.php      # conexão PDO com o MySQL
├── app/
│   ├── controllers/      # regras de negócio (Auth, Game, Review, Report)
│   ├── models/           # acesso a dados (Usuario, Jogo, Avaliacao)
│   └── views/             # telas (auth, games, relatorios, layout)
├── public/
│   ├── index.php         # front controller (roteamento via ?page=)
│   ├── css/style.css
│   └── js/validation.js
└── database.sql          # script de criação do banco + dados de exemplo
```

## Como executar

1. **Banco de dados**: crie o schema executando o script:
   ```bash
   mysql -u root -p < database.sql
   ```
2. **Credenciais**: ajuste usuário/senha do MySQL em `config/database.php` se necessário.
3. **Servidor**: aponte o *document root* para a pasta `public/`. Para testar rapidamente com o servidor embutido do PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Acesse `http://localhost:8000` no navegador.

### Login de teste
Os usuários de exemplo do `database.sql` usam a senha **`123456`**:
- `ana@criticaja.com` (crítico)
- `bruno@criticaja.com` (usuário comum)

### Sobre a recuperação de senha
Este é um esboço acadêmico sem servidor de e-mail configurado. Ao solicitar a recuperação, o sistema gera um token válido por 1 hora e exibe o link de redefinição diretamente na tela (no lugar de enviá-lo por e-mail), apenas para fins de demonstração.

## Tecnologias

PHP puro (PDO), MySQL, HTML5, CSS3, Bootstrap 5, JavaScript.

## Diagrama do banco (resumo)

```
usuarios (1) ──< jogos (criado_por)
usuarios (1) ──< redefinicoes_senha
usuarios (1) ──< avaliacoes >── (1) jogos
```
Chave única `(jogo_id, usuario_id)` em `avaliacoes` garante que cada usuário avalie um mesmo jogo apenas uma vez.
