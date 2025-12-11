-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 00:16
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
-- Banco de dados: `financialapp_php`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `type` varchar(50) DEFAULT 'Outro',
  `currency` varchar(10) DEFAULT 'BRL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `name`, `balance`, `type`, `currency`, `created_at`, `updated_at`) VALUES
(18, 25, 'XP Investimentos', 5000.00, 'Cartão de Crédito', 'BRL', '2025-12-03 00:06:19', '2025-12-03 00:06:19'),
(19, 25, 'Santander', 9160.00, 'Banco Tradicional', 'BRL', '2025-12-03 00:07:18', '2025-12-03 00:12:03'),
(20, 25, 'Bradesco', -150.00, 'Investimentos', 'BRL', '2025-12-03 00:07:41', '2025-12-03 00:07:41'),
(21, 25, 'Nubank', 29430.00, 'Banco Digital', 'BRL', '2025-12-03 00:08:14', '2025-12-03 00:10:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  `deadline_type` enum('diario','semanal','mensal','anual') NOT NULL,
  `current_progress` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `name`, `target_value`, `deadline_type`, `current_progress`, `created_at`) VALUES
(17, 25, 'Nova casa', 500000.00, 'anual', 0.00, '2025-12-02 21:21:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 17, '59d3b1804f569787057da86ef35db0a01ef4df1d227cadd43c6157b11f872a13', '2025-11-27 18:20:01', '2025-11-27 16:20:01'),
(2, 17, '015e68e58b11f67ebd72c6ec06a766bf123f181abaff8d783e5f61102efad5e4', '2025-11-27 18:21:25', '2025-11-27 16:21:25'),
(3, 17, 'e1d6a8fd1a9955ed4ecb00c6b9205892ea19e815073e0d5496fbc15fe4d2c143', '2025-11-27 18:21:29', '2025-11-27 16:21:29'),
(4, 17, 'ba2ebd9eed3e1ec4fccbdabaa1b0c10984181a1ff2eae0113626263a82cbb114', '2025-11-27 18:49:34', '2025-11-27 16:49:34'),
(5, 17, '4e261159be49a54e0b684529eab6b14af57fadca8c6a22489bc702e343a3877b', '2025-11-27 18:53:18', '2025-11-27 16:53:18'),
(6, 17, '2fa0d6a31166aaf636f934e1de5eeaeee9d65e0bc9e92e5b43eb1db1b535415a', '2025-11-27 19:01:45', '2025-11-27 17:01:45'),
(7, 18, '87964071743ec9cf3eeeb09460d9bbf0fa9627155213c200f1c988137f711cb1', '2025-11-27 19:04:18', '2025-11-27 17:04:18'),
(13, 17, 'c2da5ce77c6e5ee5ee8b414ea4f18478d6a1839434a3cf96246b7c454ef7c2b6', '2025-11-28 21:09:28', '2025-11-28 19:09:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('Receita','Despesa') NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `account_id`, `description`, `amount`, `type`, `category`, `transaction_date`, `created_at`) VALUES
(5, 25, 21, 'Pagamento de boleto atrasado', -2570.00, 'Despesa', 'Investimentos', '2025-12-03', '2025-12-02 21:10:25'),
(6, 25, 19, 'Conta de luz', 200.00, 'Receita', 'Moradia', '2025-06-12', '2025-12-02 21:12:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `contact_method` enum('email','phone') NOT NULL DEFAULT 'email',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('feminino','masculino','outros') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `phone_number` varchar(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('ativo','inativo','bloqueado') DEFAULT 'ativo',
  `role` enum('usuario','admin') DEFAULT 'usuario',
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `is_2fa_enabled` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `date_of_birth`, `gender`, `created_at`, `phone_number`, `last_login`, `status`, `role`, `password_reset_token`, `token_expires`, `is_2fa_enabled`) VALUES
(17, 'Lorrayne Ramos Da Silva', 'lorrayne.ramosdasilva@gmail.com', '$2y$10$0KideApxRUfRN4Xpqi8VJOeUruAp05qeDwnK7glModrbC52BQn7c.', '2005-02-17', 'feminino', '2025-11-14 12:43:50', '(21) 99364-5772', NULL, 'ativo', 'usuario', NULL, NULL, 1),
(18, 'Rondinelli da Silva', 'ronnyy123@hotmail.com', '$2y$10$7Xc6EKwCjnuJD/cGgh3zLOo2w/MSZS6ujObzp/97NwwS40YSTcQvq', '1973-09-14', 'masculino', '2025-11-14 14:43:05', '(21) 98022-6535', NULL, 'ativo', 'admin', NULL, NULL, 1),
(21, 'Emanuella Brito', 'manubrito322@gmail.com', '$2y$10$Q3tZcAtPIXmILbETRezkvO76xt/Zhp0j4aC/l.21GH.xz5gXSmg2u', '2000-06-20', 'feminino', '2025-11-26 23:55:47', '(21) 96710-2084', NULL, 'ativo', 'usuario', NULL, NULL, 1),
(22, 'Nicoly Reis do Nascimento', 'nicknascimento15@gmail.com', '$2y$10$lwW8K.cjpaPNIiyOh50SRuvJWv5g5FDjkUBIZ.Q85dOLg0GdV1rxW', '1996-08-15', 'feminino', '2025-11-26 23:58:03', '(21) 98733-6584', NULL, 'ativo', 'usuario', NULL, NULL, 1),
(24, 'Victor Matheus dos Santos Ribeiro', 'victor.matheus690@yahoo.com.br', '$2y$10$Up9METBJNXGrLU3R0Xkz9.XdsfXnIukM4jesivVWlp5YtNk2idqFa', '2003-01-08', 'feminino', '2025-11-27 21:00:21', '(21) 96875-2921', NULL, 'ativo', 'usuario', NULL, NULL, 1),
(25, 'Hyure dos Campos Vieira', 'mrscampos@hotmail.com', '$2y$10$yXuTR/ePF8qw2CLrqk4nG.Lw.pn3DK8G8WBDLtKBBmRxDmrRbXb1q', '2001-12-25', 'masculino', '2025-12-02 21:03:27', '(21) 98074-6532', NULL, 'ativo', 'usuario', NULL, NULL, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
