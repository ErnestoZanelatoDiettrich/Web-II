-- =========================================================
-- Trustic 2.0 — Script de criação do banco de dados
-- Sistema completo de notas e críticas de jogos (estilo Metacritic)
-- =========================================================

CREATE DATABASE IF NOT EXISTS trustic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE trustic;

-- ---------------------------------------------------------
-- usuarios
-- ---------------------------------------------------------
CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120)                       NOT NULL,
    email         VARCHAR(150)                       NOT NULL UNIQUE,
    senha_hash    VARCHAR(255)                       NOT NULL,
    tipo          ENUM('usuario', 'critico', 'admin') NOT NULL DEFAULT 'usuario',
    avatar_url    VARCHAR(255)                       NULL,
    bio           VARCHAR(500)                       NULL,
    criado_em     DATETIME                           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- redefinicoes_senha (recuperação de senha)
-- ---------------------------------------------------------
CREATE TABLE redefinicoes_senha (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT          NOT NULL,
    token       VARCHAR(64)  NOT NULL UNIQUE,
    expira_em   DATETIME     NOT NULL,
    CONSTRAINT fk_reset_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- generos / plataformas (tabelas de apoio normalizadas)
-- ---------------------------------------------------------
CREATE TABLE generos (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE plataformas (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- jogos
-- ---------------------------------------------------------
CREATE TABLE jogos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(150)  NOT NULL,
    genero_id       INT           NOT NULL,
    plataforma_id   INT           NOT NULL,
    desenvolvedora  VARCHAR(120),
    publicadora     VARCHAR(120),
    ano_lancamento  SMALLINT      NOT NULL,
    descricao       TEXT,
    capa_url        VARCHAR(255),
    criado_por      INT           NOT NULL,
    criado_em       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_jogo_genero     FOREIGN KEY (genero_id)     REFERENCES generos(id),
    CONSTRAINT fk_jogo_plataforma FOREIGN KEY (plataforma_id) REFERENCES plataformas(id),
    CONSTRAINT fk_jogo_usuario    FOREIGN KEY (criado_por)    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- avaliacoes (notas e comentários de críticos/usuários)
-- ---------------------------------------------------------
CREATE TABLE avaliacoes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    jogo_id      INT                         NOT NULL,
    usuario_id   INT                         NOT NULL,
    nota         TINYINT UNSIGNED            NOT NULL,
    comentario   TEXT                        NOT NULL,
    tipo         ENUM('critica', 'usuario')  NOT NULL,
    criado_em    DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_avaliacao_jogo    FOREIGN KEY (jogo_id)    REFERENCES jogos(id)    ON DELETE CASCADE,
    CONSTRAINT fk_avaliacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT uq_avaliacao_unica   UNIQUE (jogo_id, usuario_id),
    CONSTRAINT chk_nota_valida      CHECK (nota BETWEEN 0 AND 100)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- favoritos (lista de desejos do usuário)
-- ---------------------------------------------------------
CREATE TABLE favoritos (
    usuario_id  INT       NOT NULL,
    jogo_id     INT       NOT NULL,
    criado_em   DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, jogo_id),
    CONSTRAINT fk_favorito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorito_jogo    FOREIGN KEY (jogo_id)    REFERENCES jogos(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Dados de exemplo (facilita a correção / demonstração)
-- Senha de todos os usuários de exemplo: "123456"
-- ---------------------------------------------------------
INSERT INTO usuarios (nome, email, senha_hash, tipo, bio) VALUES
('Ana Crítica',   'ana@trustic.com',   '$2y$10$Vh5b2G0d0aFhH1p1i3q2gOQeYVwZ0nTn3F1qEXk9G2tHkq0m0G1Sa', 'critico', 'Crítica de jogos há 8 anos. Foco em RPGs e narrativa.'),
('Bruno Player',  'bruno@trustic.com', '$2y$10$Vh5b2G0d0aFhH1p1i3q2gOQeYVwZ0nTn3F1qEXk9G2tHkq0m0G1Sa', 'usuario', 'Só jogo nos fins de semana, mas jogo bastante.'),
('Admin Trustic', 'admin@trustic.com', '$2y$10$Vh5b2G0d0aFhH1p1i3q2gOQeYVwZ0nTn3F1qEXk9G2tHkq0m0G1Sa', 'admin',   NULL);

INSERT INTO generos (nome) VALUES ('RPG de Ação'), ('Roguelike'), ('Simulação'), ('Aventura');
INSERT INTO plataformas (nome) VALUES ('PC'), ('PS5'), ('Switch'), ('Xbox Series X');

INSERT INTO jogos (titulo, genero_id, plataforma_id, desenvolvedora, publicadora, ano_lancamento, descricao, criado_por) VALUES
('Elden Ring',     1, 1, 'FromSoftware',    'Bandai Namco', 2022, 'Um vasto mundo aberto repleto de desafios.', 1),
('Hades',          2, 1, 'Supergiant Games','Supergiant Games', 2020, 'Fuja do submundo em um roguelike aclamado.', 1),
('Stardew Valley', 3, 1, 'ConcernedApe',    'ConcernedApe', 2016, 'Cuide de sua fazenda e conheça a vila.', 1);

INSERT INTO avaliacoes (jogo_id, usuario_id, nota, comentario, tipo) VALUES
(1, 1, 96, 'Obra-prima do gênero, design de mundo impecável.', 'critica'),
(1, 2, 90, 'Difícil, mas extremamente satisfatório.', 'usuario'),
(2, 1, 93, 'Loop de jogo viciante e narrativa muito bem escrita.', 'critica'),
(3, 2, 88, 'Relaxante e cheio de conteúdo, ótimo para relaxar.', 'usuario');

INSERT INTO favoritos (usuario_id, jogo_id) VALUES (2, 1), (2, 2);
