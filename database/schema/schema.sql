-- =========================================================
-- SCHEMA FINAL
-- Sistema de Cadastramento e Gestao de Redes de Hidrantes
-- Banco: MySQL / MariaDB
-- Charset: utf8mb4
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================
-- DATABASE (opcional)
-- =========================================================
-- CREATE DATABASE IF NOT EXISTS hidrantes_db
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;
-- USE hidrantes_db;

-- =========================================================
-- DROP TABLES (ordem reversa das dependencias)
-- =========================================================
DROP TABLE IF EXISTS historico_usuario;
DROP TABLE IF EXISTS hidrantes;
DROP TABLE IF EXISTS bairros;
DROP TABLE IF EXISTS municipios;
DROP TABLE IF EXISTS usuarios;

-- =========================================================
-- TABELA: usuarios
-- =========================================================
CREATE TABLE usuarios (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    matricula_funcional VARCHAR(30) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'gestor', 'operador') NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL,

    CONSTRAINT pk_usuarios PRIMARY KEY (id),
    CONSTRAINT uq_usuarios_matricula UNIQUE (matricula_funcional)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_usuarios_nome ON usuarios (nome);
CREATE INDEX idx_usuarios_perfil ON usuarios (perfil);
CREATE INDEX idx_usuarios_status ON usuarios (status);

-- =========================================================
-- TABELA: municipios
-- =========================================================
CREATE TABLE municipios (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    codigo_ibge VARCHAR(20) NULL,
    uf CHAR(2) NOT NULL DEFAULT 'PA',
    ativo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT pk_municipios PRIMARY KEY (id),
    CONSTRAINT uq_municipios_nome UNIQUE (nome),
    CONSTRAINT uq_municipios_codigo_ibge UNIQUE (codigo_ibge)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_municipios_ativo ON municipios (ativo);

-- =========================================================
-- TABELA: bairros
-- =========================================================
CREATE TABLE bairros (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    municipio_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(150) NOT NULL,
    codigo_ibge VARCHAR(30) NULL,
    geojson_referencia LONGTEXT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT pk_bairros PRIMARY KEY (id),
    CONSTRAINT uq_bairros_municipio_nome UNIQUE (municipio_id, nome),
    CONSTRAINT uq_bairros_codigo_ibge UNIQUE (codigo_ibge),
    CONSTRAINT fk_bairros_municipio
        FOREIGN KEY (municipio_id)
        REFERENCES municipios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_bairros_nome ON bairros (nome);
CREATE INDEX idx_bairros_ativo ON bairros (ativo);

-- =========================================================
-- TABELA: hidrantes
-- =========================================================
CREATE TABLE hidrantes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    numero_hidrante VARCHAR(20) NOT NULL,
    equipe_responsavel VARCHAR(150) NOT NULL,
    area ENUM('urbano', 'industrial', 'rural') NOT NULL,
    existe_no_local ENUM('sim', 'nao') NOT NULL,
    tipo_hidrante ENUM('coluna', 'subterraneo', 'parede', 'outro') NOT NULL,
    acessibilidade ENUM('sim', 'nao') NOT NULL,
    tampo_conexoes ENUM('integra', 'danificadas', 'ausentes') NOT NULL,
    tampas_ausentes VARCHAR(100) NULL,
    caixa_protecao ENUM('sim', 'nao') NOT NULL,
    condicao_caixa ENUM('boa', 'regular', 'ruim') NULL,
    presenca_agua_interior ENUM('sim', 'nao') NOT NULL,
    teste_realizado ENUM('sim', 'nao') NOT NULL,
    resultado_teste ENUM('funcionando normalmente', 'vazamento', 'vazao insuficiente', 'nao funcionou') NULL,
    status_operacional ENUM('operante', 'operante com restricao', 'inoperante') NOT NULL,
    municipio_id BIGINT UNSIGNED NOT NULL,
    bairro_id BIGINT UNSIGNED NULL,
    endereco VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    foto_01 VARCHAR(255) NULL,
    foto_02 VARCHAR(255) NULL,
    foto_03 VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    criado_por_usuario_id BIGINT UNSIGNED NULL,
    atualizado_por_usuario_id BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_por_usuario_id BIGINT UNSIGNED NULL,

    CONSTRAINT pk_hidrantes PRIMARY KEY (id),
    CONSTRAINT uq_hidrantes_numero UNIQUE (numero_hidrante),
    CONSTRAINT fk_hidrantes_municipio
        FOREIGN KEY (municipio_id)
        REFERENCES municipios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_hidrantes_bairro
        FOREIGN KEY (bairro_id)
        REFERENCES bairros (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_hidrantes_criado_por
        FOREIGN KEY (criado_por_usuario_id)
        REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_hidrantes_atualizado_por
        FOREIGN KEY (atualizado_por_usuario_id)
        REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT chk_hidrantes_latitude
        CHECK (latitude IS NULL OR (latitude >= -90.0000000 AND latitude <= 90.0000000)),
    CONSTRAINT chk_hidrantes_longitude
        CHECK (longitude IS NULL OR (longitude >= -180.0000000 AND longitude <= 180.0000000)),
    CONSTRAINT chk_hidrantes_resultado_teste
        CHECK (
            (teste_realizado = 'sim')
            OR (teste_realizado = 'nao' AND resultado_teste IS NULL)
        )
    CONSTRAINT fk_hidrantes_deleted_por
        FOREIGN KEY (deleted_por_usuario_id)
        REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_hidrantes_status_operacional ON hidrantes (status_operacional);
CREATE INDEX idx_hidrantes_municipio_id ON hidrantes (municipio_id);
CREATE INDEX idx_hidrantes_bairro_id ON hidrantes (bairro_id);
CREATE INDEX idx_hidrantes_tipo_hidrante ON hidrantes (tipo_hidrante);
CREATE INDEX idx_hidrantes_area ON hidrantes (area);
CREATE INDEX idx_hidrantes_atualizado_em ON hidrantes (atualizado_em);
CREATE INDEX idx_hidrantes_municipio_bairro ON hidrantes (municipio_id, bairro_id);
CREATE INDEX idx_hidrantes_status_municipio ON hidrantes (status_operacional, municipio_id);
CREATE INDEX idx_hidrantes_deleted_at ON hidrantes (deleted_at);

-- =========================================================
-- TABELA: historico_usuario
-- =========================================================
CREATE TABLE historico_usuario (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    data_acao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id BIGINT UNSIGNED NOT NULL,
    usuario_nome_snapshot VARCHAR(150) NOT NULL,
    acao ENUM(
        'login',
        'logout',
        'cadastrar',
        'editar',
        'deletar',
        'baixar csv',
        'alterar senha',
        'ativar',
        'inativar',
        'gerar relatorio'
    ) NOT NULL,
    entidade VARCHAR(50) NULL,
    referencia_registro VARCHAR(100) NULL,
    detalhes TEXT NULL,
    ip_origem VARCHAR(45) NULL,

    CONSTRAINT pk_historico_usuario PRIMARY KEY (id),
    CONSTRAINT fk_historico_usuario_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_historico_usuario_usuario_id ON historico_usuario (usuario_id);
CREATE INDEX idx_historico_usuario_acao ON historico_usuario (acao);
CREATE INDEX idx_historico_usuario_data_acao ON historico_usuario (data_acao);
CREATE INDEX idx_historico_usuario_usuario_acao ON historico_usuario (usuario_id, acao);

-- =========================================================
-- DADOS INICIAIS OPCIONAIS
-- =========================================================
INSERT INTO usuarios (nome, matricula_funcional, senha_hash, perfil, status)
VALUES
('Administrador do Sistema', '000000', '$2y$10$abcdefghijklmnopqrstuvabcdefghijklmnopqrstuvabcd', 'admin', 'ativo');
-- OBS.: substituir senha_hash por um hash real gerado com password_hash no PHP.

SET FOREIGN_KEY_CHECKS = 1;
