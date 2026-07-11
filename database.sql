CREATE DATABASE IF NOT EXISTS sistema_rifas_solidarias
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE sistema_rifas_solidarias;

CREATE TABLE IF NOT EXISTS perfil (
  id int NOT NULL AUTO_INCREMENT,
  descricao varchar(50) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuario (
  id bigint NOT NULL AUTO_INCREMENT,
  nome varchar(100) NOT NULL,
  endereco varchar(255) DEFAULT NULL,
  telefone varchar(20) DEFAULT NULL,
  id_perfil bigint NOT NULL,
  email varchar(150) NOT NULL,
  senha varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  KEY fk_usuario_perfil (id_perfil)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rifa (
  id bigint NOT NULL AUTO_INCREMENT,
  descricao_rifa varchar(255) NOT NULL,
  valor_numero decimal(10,2) NOT NULL,
  quantidade_numero int NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS numero_rifa (
  id bigint NOT NULL AUTO_INCREMENT,
  numero bigint NOT NULL,
  status varchar(50) NOT NULL,
  rifa_id bigint NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_numero_rifa (rifa_id, numero),
  KEY fk_numero_rifa_rifa (rifa_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reserva (
  id bigint NOT NULL AUTO_INCREMENT,
  numero_rifa_id bigint NOT NULL,
  usuario_id bigint NOT NULL,
  data_reserva datetime NOT NULL,
  PRIMARY KEY (id),
  KEY fk_reserva_numero_rifa (numero_rifa_id),
  KEY fk_reserva_usuario (usuario_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO perfil (id, descricao) VALUES
  (1, 'administrador'),
  (2, 'organizador'),
  (3, 'usuario');

INSERT IGNORE INTO usuario (id, nome, endereco, telefone, id_perfil, email, senha) VALUES
  (1, 'administrador', 'Rua A, 100', '(34)99999-1111', 1, 'joao@email.com', '123456');
