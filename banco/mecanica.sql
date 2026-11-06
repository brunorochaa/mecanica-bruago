-- Banco da Mecânica Bruago (MySQL 8 ou MariaDB 10.4+).
-- Importe pelo phpMyAdmin ou: mysql -u root -p < banco/mecanica.sql
-- Os dados de exemplo são fictícios.

CREATE DATABASE IF NOT EXISTS mecanica
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mecanica;

DROP TABLE IF EXISTS servico;
DROP TABLE IF EXISTS cliente;

CREATE TABLE cliente (
    idCliente BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    sexo VARCHAR(15) NOT NULL,
    dataNascimento DATE NOT NULL,
    cep VARCHAR(10) NOT NULL,
    numeroCasa VARCHAR(10) NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    PRIMARY KEY (idCliente)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE servico (
    idServico BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    descricaoServico VARCHAR(200) NOT NULL,
    filial VARCHAR(50) NOT NULL,
    nomeMecanico VARCHAR(100) NOT NULL,
    carro VARCHAR(150) NOT NULL,
    cor VARCHAR(100) NOT NULL,
    placa VARCHAR(8) NOT NULL,
    tipoCombustivel VARCHAR(30) NOT NULL,
    urgencia VARCHAR(30) NOT NULL,
    PRIMARY KEY (idServico)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO cliente (nome, sexo, dataNascimento, cep, numeroCasa, telefone) VALUES
    ('Ana Exemplo', 'feminino', '1995-05-15', '90010-000', '56', '51900000001'),
    ('Carlos Teste', 'masculino', '1978-03-02', '90020-100', '345', '51900000002'),
    ('Marina Ficticia', 'feminino', '1999-10-02', '90030-200', '20B', '51900000003'),
    ('Paulo Modelo', 'masculino', '1985-10-20', '90040-300', '500', '51900000004');

INSERT INTO servico (descricaoServico, filial, nomeMecanico, carro, cor, placa, tipoCombustivel, urgencia) VALUES
    ('Aplicar insulfilme', 'santaRosa', 'Gabriel Exemplo', 'Ford Ka', 'Vermelho', 'ABC-1234', 'gas', 'comUrgencia'),
    ('Rebaixar suspensão', 'assisBrasil', 'Lucas Teste', 'Vw Saveiro', 'Cinza', 'ABC-2424', 'comum', 'semUrgencia'),
    ('Trocar pisca dianteiro esquerdo', 'santaRosa', 'Rafael Modelo', 'Vw Fusca', 'Azul', 'BRA2E19', 'etanol', 'semUrgencia'),
    ('Trocar câmara do pneu furado', 'assisBrasil', 'Eduardo Ficticio', 'Chevrolet Chevette', 'Amarelo', 'ABC-1571', 'diesel', 'comUrgencia');
