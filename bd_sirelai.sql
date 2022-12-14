-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Set-2021 às 03:56
-- Versão do servidor: 10.4.8-MariaDB
-- versão do PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `bd_sirelai`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `administrador`
--

CREATE TABLE `administrador` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `matricula` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `nivel_acesso` int(1) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `administrador`
--

INSERT INTO `administrador` (`id`, `nome`, `sobrenome`, `matricula`, `email`, `senha`, `cargo`, `sexo`, `imagem`, `nivel_acesso`, `status`) VALUES
(1, 'Joab', 'Torres', '2015790058', 'joabtorres1508@gmail.com', '47cafbff7d1c4463bbe7ba972a2b56e3', 'Coordernação de Curso de Tecnologia em Análise e Desenvolvimento de Sistemas (CTADS)', 'M', 'uploads/administradores/user_masculino.png', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `dias_uteis`
--

CREATE TABLE `dias_uteis` (
  `id` int(11) UNSIGNED NOT NULL,
  `categoria` varchar(20) DEFAULT NULL,
  `minimo` int(2) DEFAULT NULL,
  `maximo` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `dias_uteis`
--

INSERT INTO `dias_uteis` (`id`, `categoria`, `minimo`, `maximo`) VALUES
(1, 'Aluno(a)', 2, 4),
(2, 'Professor(a)', 3, 7),
(3, 'Usuario', 3, 7);

-- --------------------------------------------------------

--
-- Estrutura da tabela `horario`
--

CREATE TABLE `horario` (
  `id` int(11) NOT NULL,
  `id_laboratorio` int(11) NOT NULL,
  `hora_inicial` time NOT NULL,
  `hora_final` time NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `horario`
--

INSERT INTO `horario` (`id`, `id_laboratorio`, `hora_inicial`, `hora_final`, `status`) VALUES
(13, 4, '10:15:00', '11:00:00', 0),
(12, 4, '09:30:00', '10:15:00', 0),
(11, 4, '08:15:00', '09:00:00', 0),
(10, 4, '07:30:00', '08:15:00', 0),
(14, 4, '11:00:00', '11:45:00', 0),
(15, 4, '11:45:00', '12:30:00', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `laboratorio`
--

CREATE TABLE `laboratorio` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `qtd_computador` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `laboratorio`
--

INSERT INTO `laboratorio` (`id`, `nome`, `qtd_computador`, `status`) VALUES
(4, 'Laboratório de Informática 1', 20, 1),
(5, 'Laboratório de Informática 2', 20, 1),
(6, 'Laboratório de Informática 3', 20, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_laboratorio` int(11) NOT NULL,
  `data_inicial` date NOT NULL,
  `data_final` date NOT NULL,
  `horario_inicial` time NOT NULL,
  `horario_final` time NOT NULL,
  `turma` varchar(255) NOT NULL,
  `disciplina` varchar(255) NOT NULL,
  `segunda` int(1) NOT NULL,
  `terca` int(1) NOT NULL,
  `quarta` int(1) NOT NULL,
  `quinta` int(1) NOT NULL,
  `sexta` int(1) NOT NULL,
  `sabado` int(1) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `matricula` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `nascimento` date DEFAULT NULL,
  `categoria` varchar(15) NOT NULL,
  `curso` varchar(255) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `sobrenome`, `matricula`, `email`, `senha`, `cpf`, `nascimento`, `categoria`, `curso`, `sexo`, `imagem`, `status`) VALUES
(1, 'Joab', 'Torres Alencar', '2015790058', 'joabtorres1508@gmail.com', '47cafbff7d1c4463bbe7ba972a2b56e3', '000.378.402-90', '1993-08-15', 'Aluno(a)', 'Análise e Desenvolvimento de Sistemas', 'M', 'uploads/usuarios/user_masculino.png', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula_UNIQUE` (`matricula`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Índices para tabela `dias_uteis`
--
ALTER TABLE `dias_uteis`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_idLaboratorio` (`id_laboratorio`);

--
-- Índices para tabela `laboratorio`
--
ALTER TABLE `laboratorio`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reserva_usuario_idx` (`id_usuario`),
  ADD KEY `fk_reserva_laboratorio1_idx` (`id_laboratorio`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `matricula_UNIQUE` (`matricula`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `dias_uteis`
--
ALTER TABLE `dias_uteis`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `horario`
--
ALTER TABLE `horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `laboratorio`
--
ALTER TABLE `laboratorio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_laboratorio1` FOREIGN KEY (`id_laboratorio`) REFERENCES `laboratorio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
