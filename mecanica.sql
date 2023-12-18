-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Out-2019 às 16:44
-- Versão do servidor: 10.4.6-MariaDB
-- versão do PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `mecanica`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente`
--

CREATE TABLE `cliente` (
  `idCliente` bigint(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `sexo` varchar(15) NOT NULL,
  `dataNascimento` date NOT NULL,
  `cep` varchar(10) NOT NULL,
  `numeroCasa` varchar(10) NOT NULL,
  `telefone` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`idCliente`, `nome`, `sexo`, `dataNascimento`, `cep`, `numeroCasa`, `telefone`) VALUES
(1, 'Bruno Rocha Do Amaral', 'masculino', '2000-11-29', '91.160-020', '78', '51985788704'),
(3, 'Fernanda Souza', 'feminino', '1995-05-15', '91.111-020', '56', '51986788704'),
(4, 'Robson Almeida', 'masculino', '1978-05-15', '49.150-030', '345', '51985781544'),
(5, 'Kleyton Valdecir', 'masculino', '2000-08-29', '91.120-131', '78A2', '51984393212'),
(6, 'Jurema Silva', 'feminino', '1950-06-17', '30.260-092', '56B', '51985788493'),
(7, 'Gabriela Paula', 'feminino', '1999-10-02', '91.140-020', '20', '51989788700'),
(8, 'Mauricio Programador', 'masculino', '1985-10-20', '91.280-600', '500', '55933623290'),
(9, 'Jobson Joarir', 'masculino', '2000-11-23', '91.160-020', '742', '52995897870');

-- --------------------------------------------------------

--
-- Estrutura da tabela `servico`
--

CREATE TABLE `servico` (
  `idServico` bigint(20) NOT NULL,
  `descricaoServico` varchar(200) NOT NULL,
  `filial` varchar(50) NOT NULL,
  `nomeMecanico` varchar(100) NOT NULL,
  `carro` varchar(150) NOT NULL,
  `cor` varchar(100) NOT NULL,
  `placa` varchar(8) NOT NULL,
  `tipoCombustivel` varchar(30) NOT NULL,
  `urgencia` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `servico`
--

INSERT INTO `servico` (`idServico`, `descricaoServico`, `filial`, `nomeMecanico`, `carro`, `cor`, `placa`, `tipoCombustivel`, `urgencia`) VALUES
(1, 'Aplicando Insufilme', 'santaRosa', 'Gabriel Maciel', 'Ford, Ka', 'Vermelho', 'ABC-1234', 'gas', 'comUrgencia'),
(2, 'Rebaixar', 'assisBrasil', 'Lazarin', 'Vw, Saveiro', 'Cinza', 'ABC-2424', 'comum', 'semUrgencia'),
(4, 'Trocar O Pisca Esquerdo Da Frente', 'santaRosa', 'Jonisvaldo', 'Vw, Fusca', 'Azul', 'ABC-6969', 'etanol', 'semUrgencia'),
(5, 'Trocar a cam&ecirc;ra do pneu furado', 'assisBrasil', 'Eduardo Gomes', 'Chevrolet, Chevette', 'Amarelo', 'ABC-1571', 'diesel', 'comUrgencia'),
(6, 'troca de parachoque j&atilde;o', 'assisBrasil', 'Jo&atilde;o Borracheiro', 'Corsinha, Sedan', 'Rosa', 'AAS-1234', 'etanol', 'comUrgencia');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`);

--
-- Índices para tabela `servico`
--
ALTER TABLE `servico`
  ADD PRIMARY KEY (`idServico`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idCliente` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `servico`
--
ALTER TABLE `servico`
  MODIFY `idServico` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
