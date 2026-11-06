-- Mesmas tabelas de banco/mecanica.sql, no dialeto do SQLite (usado só nos testes).
CREATE TABLE cliente (
    idCliente INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    sexo TEXT NOT NULL,
    dataNascimento TEXT NOT NULL,
    cep TEXT NOT NULL,
    numeroCasa TEXT NOT NULL,
    telefone TEXT NOT NULL
);

CREATE TABLE servico (
    idServico INTEGER PRIMARY KEY AUTOINCREMENT,
    descricaoServico TEXT NOT NULL,
    filial TEXT NOT NULL,
    nomeMecanico TEXT NOT NULL,
    carro TEXT NOT NULL,
    cor TEXT NOT NULL,
    placa TEXT NOT NULL,
    tipoCombustivel TEXT NOT NULL,
    urgencia TEXT NOT NULL
);
