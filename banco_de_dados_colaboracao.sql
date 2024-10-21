-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/10/2024 às 00:27
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
(39, 1, 2, 'dfjlsçkaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskaskv sknsksksksksksksknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknsknskn', '2024-10-12 17:17:27', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
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
(2, '12345', '12345@gmail.com', '$2y$10$rS..FaiVsvZxcQHtkzhONOytKnSHrtbICUzSRgxJ2p6AlHcadap6.', 'Manutenção', '2024-10-12 14:58:47');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `manutencao_id` (`manutencao_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`manutencao_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
