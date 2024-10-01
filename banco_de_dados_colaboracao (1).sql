-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01/10/2024 às 12:51
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
-- Estrutura para tabela `formularios`
--

CREATE TABLE `formularios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `formularios`
--

INSERT INTO `formularios` (`id`, `nome`, `email`, `mensagem`, `data_envio`, `setor`) VALUES
(1, 'Fulano', 'fulano@gmail.com', 'Mensagem de teste', '2024-09-03 14:19:03', ''),
(2, 'Gustavo', 'gustavo@hotmail.com', 'Qualquer coisa.', '2024-09-03 14:21:59', ''),
(3, 'Luiz Augusto', 'luiz_augusto@gmail.com', 'Outra mensagem.', '2024-09-03 14:23:48', ''),
(4, 'fdsfdsaf', 'fulano@gmail.com', 'fdsfdsafasfsaf', '2024-09-24 12:13:04', ''),
(5, 'fdsafdsfdas', 'fulano@gmail.com', 'gfsdgfdgfdsgds', '2024-09-24 12:16:55', ''),
(6, 'teste', 'mmagriantonelli@gmail.com', 'gyfuvcie2t2vr', '2024-09-24 12:41:26', ''),
(7, 'teste', 'mmagriantonelli@gmail.com', 'segundo teste', '2024-09-24 12:44:44', ''),
(8, 'qualquer coisa ', 'alexandre.fortunati@sp.senai.br', 'fdfdsfsafsa', '2024-09-24 13:50:55', 'manutencao');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(140) NOT NULL,
  `email` varchar(140) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `cargo` enum('Professor','Manutenção') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `cargo`) VALUES
(1, '1234', '1234@gmail.com', '$2y$10$zIod2PK41lhfnRHF.QVNtOxyQEpQnSrGZAet1nslbidIenwe0vq/.', 'Professor'),
(2, '1234', 'root@gmail.com', '$2y$10$UG3nox8lxaDXM.bHrB56yu/xrI6z6jwUu5oAVcbECcELP3ItHz8qi', 'Professor'),
(3, '1234', '1234@outlook.com', '$2y$10$9LPFJoAEIQJnK15Be9oyj.yDYAh5oqZYjJSprWUgagw3P9cWjehSi', 'Manutenção');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `formularios`
--
ALTER TABLE `formularios`
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
-- AUTO_INCREMENT de tabela `formularios`
--
ALTER TABLE `formularios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
