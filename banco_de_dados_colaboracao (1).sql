-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/10/2024 às 04:10
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
-- Banco de dados: `banco_de_dados_colaboracao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmada` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente_id`, `destinatario_id`, `mensagem`, `timestamp`, `confirmada`) VALUES
(38, 1, 2, 'ola 2\r\n', '2024-10-12 17:12:12', 1),
(39, 1, 2, 'dfjlsçkaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskv sknsksksksksksksknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknskn', '2024-10-12 17:17:27', 1),
(40, 1, 2, 'ABaba', '2024-10-26 22:10:30', 0),
(41, 1, 2, '123412341234', '2024-10-26 22:12:21', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario` varchar(140) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `sala` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `motivo` text NOT NULL,
  `manutencao_id` int(11) DEFAULT NULL,
  `status` enum('pendente','confirmado','cancelado') DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `usuario`, `usuario_id`, `sala`, `data`, `horario_inicio`, `horario_fim`, `motivo`, `manutencao_id`, `status`, `data_criacao`) VALUES
(13, '', 3, 'Biblioteca', '2024-10-28', '20:17:00', '21:17:00', 'mentira\r\n', 2, 'pendente', '2024-10-27 22:17:44'),
(15, '', 1, 'Biblioteca', '2024-10-30', '23:23:00', '00:19:00', 'asdfasfasdfasdf', 2, 'pendente', '2024-10-27 22:19:30'),
(16, '', 1, 'Biblioteca', '2024-10-16', '20:51:00', '19:51:00', '', 2, 'pendente', '2024-10-27 22:52:02'),
(17, '', 1, 'Informatica', '2024-10-21', '20:53:00', '20:53:00', 'zbb', 2, 'pendente', '2024-10-27 22:53:29'),
(18, '', 2, 'Laboratorio de Quimica', '2024-10-15', '14:55:00', '18:56:00', 'Vo sim', 2, 'pendente', '2024-10-27 22:55:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(16) NOT NULL,
  `nome` varchar(240) NOT NULL,
  `email` varchar(140) NOT NULL,
  `senha` varchar(240) NOT NULL,
  `cargo` enum('Professor','Manutenção') NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `cargo`, `data_criacao`) VALUES
(1, '1234', '1234@gmail.com', '$2y$10$BdxRG/7Nwne4HyFdo6NzLOYWYe0j20T7TR3CmmBolS90khnW9BuQW', 'Professor', '2024-10-12 14:46:57'),
(2, '12345', '12345@gmail.com', '$2y$10$rS..FaiVsvZxcQHtkzhONOytKnSHrtbICUzSRgxJ2p6AlHcadap6.', 'Manutenção', '2024-10-12 14:58:47'),
(3, 'Lukinhas25Sx', 'lucasluizsx@gmail.com', '$2y$10$3DcPD9vyi2Htg0vxG7.O8eNVmMX40nmU1pGr8SW3lne6ak2E4HK1y', 'Professor', '2024-10-27 21:56:52');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
