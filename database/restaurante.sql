-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/05/2026 às 16:26
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `restaurante`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `adimin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL DEFAULT 'Manuela Eusebio',
  `email` varchar(100) NOT NULL DEFAULT 'gerentemm1@gmail.com',
  `senha` varchar(255) NOT NULL DEFAULT '$2y$10$yoK5WumgmYG87C8vS.bzVOK5SpjjGFciuZ0iI5o9I7q6k7Fa4JdVC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `adimin`
--

INSERT INTO `admin` (`id`, `nome`, `email`, `senha`) VALUES
(1, 'Manuela Eusebio', 'gerentemm1@gmail.com', '$2y$10$yoK5WumgmYG87C8vS.bzVOK5SpjjGFciuZ0iI5o9I7q6k7Fa4JdVC');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pratos`
--

CREATE TABLE `pratos` (
  `id` int(11) NOT NULL,
  `nome` varchar(75) NOT NULL,
  `preco` double NOT NULL,
  `qtdprato` int(11) NOT NULL,
  `descricao` varchar(555) NOT NULL,
  `categoria` varchar(455) NOT NULL,
  `subcategoria` varchar(255) NOT NULL,
  `imagem` varchar(555) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pratos`
--

INSERT INTO `pratos` (`id`, `nome`, `preco`, `qtdprato`, `descricao`, `categoria`, `subcategoria`, `imagem`) VALUES
(20, 'Cocacola em Lata', 5000, 20, 'Cocacola em Lata...', 'Refrigerantes', 'Refrigerante', '6a0be470f3bd2.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` int(12) NOT NULL,
  `num_pessoa` int(10) NOT NULL,
  `data_reserva` date NOT NULL,
  `hora_reserva` time NOT NULL,
  `mesa` varchar(20) NOT NULL,
  `estado` varchar(75) NOT NULL DEFAULT 'Pendente',
  `codigo_reserva` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `nome`, `email`, `telefone`, `num_pessoa`, `data_reserva`, `hora_reserva`, `mesa`, `estado`, `codigo_reserva`) VALUES
(11, 'Daniel Gaspar', 'danieldani@gmail.com', 945673846, 10, '2026-05-16', '10:00:00', '5', 'Aprovada', 'RES44116'),
(12, 'Vânia Gaspar', 'vania@gmail.com', 956748389, 6, '2026-05-16', '11:01:00', '5', 'Cancelada', 'RES55492'),
(13, 'David Nascimento', 'davidnascimento@gmail.com', 956348697, 8, '2026-05-15', '10:03:00', '5', 'Aprovada', 'RES20379'),
(14, 'Adilson Manuel Gaspar', 'adilsonmanuel@gmail.com', 945673854, 10, '2026-05-16', '16:44:00', '5', 'Cancelada', 'RES77805'),
(15, 'Cecelmo Joaquin', 'ceselmo@gmaim.com', 956784534, 10, '2026-05-15', '22:00:00', '5', 'Aprovada', 'RES27508'),
(16, 'Cecelmo Joaquin', 'cecelmo@gmail.com', 956784597, 10, '2026-05-16', '10:00:00', '3', 'Cancelada', 'RES19541'),
(17, 'Cecelmo Joaquin', 'cecelmo@gmail.com', 956748345, 10, '2026-05-15', '13:01:00', '5', 'Aprovada', 'RES40024'),
(18, 'Adilson Manuel Gaspar', 'adilsonmanuel1@gmail.com', 923501214, 10, '2026-05-16', '10:14:00', '5', 'Cancelada', 'RES34339'),
(19, 'Adilson Manuel Gaspar', 'adilsonmanuelgaspar1@gmail.com', 923501214, 6, '2026-05-15', '15:25:00', '3', 'Aprovada', 'RES58547'),
(20, 'Adilson Manuel Gaspar', 'adilsonmanuelgaspar1@gmail.com', 923, 6, '2026-05-15', '16:26:00', '1', 'Cancelada', 'RES53371'),
(21, 'osvaldo', 'osvaldo@gmail.com', 923, 10, '2026-05-16', '19:08:00', '1', 'Aprovada', 'RES57760'),
(22, 'Olívio Mateus', 'olivio@gmail.com', 949486137, 10, '2026-05-16', '19:15:00', '9', 'Cancelada', 'RES67592'),
(24, 'Luciano Jorge', 'lucianojorge@gmail.com', 949, 6, '2026-05-20', '12:20:00', '5', 'Aprovada', 'RES79982'),
(25, 'Jorge Gaspar', 'jorgeaugustogaspar17@gmail.com', 949, 8, '2026-05-20', '20:12:00', '5', 'Pendente', 'RES56833'),
(26, 'Joge Gaspar', 'jorgeaugustogaspar17@gmail.com', 949, 8, '2026-05-20', '09:22:00', '2', 'Pendente', 'RES23809'),
(27, 'Jorge Gaspar', 'jorgeaugustogaspar17@gmail.com', 949, 8, '2026-05-19', '08:30:00', '2', 'Aprovada', 'RES85168');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `adimin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pratos`
--
ALTER TABLE `pratos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `adimin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pratos`
--
ALTER TABLE `pratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
