-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 20, 2026 at 05:23 PM
-- Server version: 8.0.34
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bancodadosteste`
--

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `nota` int NOT NULL,
  `comentario` text,
  `data_avaliacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `usuario_id`, `produto_id`, `nota`, `comentario`, `data_avaliacao`) VALUES
(31, 9, 58, 4, '', '2026-01-18 21:35:07'),
(32, 9, 59, 4, '', '2026-01-18 21:35:09'),
(33, 9, 55, 5, '', '2026-01-18 21:35:12'),
(34, 9, 56, 5, '', '2026-01-18 21:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `carrinho`
--

CREATE TABLE `carrinho` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carrinho`
--

INSERT INTO `carrinho` (`id`, `usuario_id`, `data_atualizacao`) VALUES
(4, 3, '2025-10-27 20:48:01'),
(5, 4, '2025-10-28 16:23:10'),
(6, 9, '2025-11-22 14:59:13'),
(7, 6, '2025-12-29 19:37:42'),
(8, 16, '2026-01-12 13:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `carrinho_itens`
--

CREATE TABLE `carrinho_itens` (
  `id` int NOT NULL,
  `carrinho_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`) VALUES
(1, 'Brinquedos', 'Brinquedos de montar, bonecos e figuras de ação.'),
(2, 'TVs', 'Televisores, Smart TVs e acessórios de vídeo.'),
(3, 'Ferramentas', 'Ferramentas manuais, elétricas e equipamentos.'),
(4, 'Eletrônicos', 'Celulares, notebooks, computadores, áudio e acessórios.'),
(5, 'Robótica', 'Drones, Robôs quadrúpedes, Assistentes inteligentes, Entretenimento high-tech, Robôs inteligentes e Robôs autônomos.'),
(6, 'Esporte e Fitness', 'Equipamentos de ginástica, bicicletas ergométricas, acessórios esportivos e de fitness.'),
(7, 'Eletrodomésticos', 'Aparelhos elétricos para uso doméstico, como cafeteiras, liquidificadores, batedeiras e outros.'),
(8, 'Energia Solar', 'Painéis solares, inversores, baterias e equipamentos para geração de energia solar.'),
(9, 'Motores e Equipamentos Agrícolas', 'Motores estacionários, geradores, motobombas e equipamentos para uso agrícola e industrial.'),
(10, 'Móveis e Escritório', 'Cadeiras de escritório, cadeiras gamer, mesas e outros móveis para casa e escritório.'),
(11, 'Eletrônicos & Tech', NULL),
(12, 'Moda Masculina', NULL),
(13, 'Casa e Decoração', NULL),
(14, 'Esporte e Lazer', NULL),
(15, 'Papelaria', NULL),
(16, 'Acessórios', NULL),
(17, 'Automotivo', NULL),
(18, 'Beleza', NULL),
(19, 'Cozinha & Gourmet', NULL),
(20, 'Literatura & Hobbies', NULL),
(21, 'Beleza e Cuidados', NULL),
(22, 'Ferramentas e Construção', NULL),
(23, 'Escritório & Home Office', NULL),
(24, 'Games & Consoles', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `conversas`
--

CREATE TABLE `conversas` (
  `id` int NOT NULL,
  `comprador_id` int NOT NULL,
  `fornecedor_id` int NOT NULL,
  `pedido_id` int DEFAULT NULL,
  `produto_id` int DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversas`
--

INSERT INTO `conversas` (`id`, `comprador_id`, `fornecedor_id`, `pedido_id`, `produto_id`, `data_criacao`, `data_atualizacao`) VALUES
(1, 3, 6, NULL, 22, '2025-12-29 18:21:31', '2025-12-29 18:21:31'),
(5, 9, 6, NULL, 25, '2025-12-29 18:26:23', '2025-12-29 19:09:47'),
(6, 6, 6, NULL, 22, '2025-12-29 18:28:33', '2025-12-29 18:28:33'),
(7, 6, 6, NULL, 26, '2025-12-29 18:33:01', '2025-12-29 18:33:01'),
(8, 9, 9, NULL, NULL, '2025-12-29 18:59:55', '2025-12-29 19:02:58'),
(9, 3, 9, NULL, NULL, '2025-12-29 19:00:13', '2025-12-29 19:00:42'),
(10, 9, 6, NULL, NULL, '2025-12-29 19:07:06', '2025-12-29 19:07:06'),
(11, 9, 6, NULL, 32, '2025-12-29 19:07:13', '2025-12-29 19:07:13'),
(12, 9, 6, NULL, 23, '2025-12-29 19:08:18', '2025-12-29 19:08:18'),
(13, 6, 9, NULL, NULL, '2025-12-29 19:08:50', '2025-12-29 19:26:37'),
(14, 9, 6, NULL, 30, '2025-12-29 19:14:36', '2025-12-29 19:56:05'),
(15, 9, 6, NULL, 22, '2025-12-29 19:14:46', '2025-12-29 19:34:36'),
(16, 9, 6, NULL, NULL, '2025-12-29 19:50:25', '2025-12-29 19:50:28'),
(17, 6, 9, NULL, NULL, '2025-12-29 19:50:59', '2025-12-29 19:50:59'),
(18, 6, 9, NULL, NULL, '2025-12-31 16:15:59', '2025-12-31 16:16:42'),
(19, 9, 6, NULL, NULL, '2026-01-07 13:01:48', '2026-01-07 13:01:56'),
(20, 9, 6, NULL, NULL, '2026-01-07 14:27:12', '2026-01-07 14:45:43'),
(21, 9, 6, NULL, NULL, '2026-01-07 14:29:55', '2026-01-07 14:29:58'),
(22, 16, 6, NULL, NULL, '2026-01-07 14:54:06', '2026-01-07 14:54:08'),
(23, 16, 6, NULL, NULL, '2026-01-07 15:14:46', '2026-01-07 15:14:49'),
(24, 16, 6, NULL, NULL, '2026-01-07 15:25:55', '2026-01-07 15:25:58'),
(25, 9, 16, NULL, NULL, '2026-01-07 15:32:35', '2026-01-07 17:41:23'),
(26, 9, 6, NULL, NULL, '2026-01-07 17:06:20', '2026-01-07 17:28:41'),
(27, 9, 6, NULL, NULL, '2026-01-07 17:26:32', '2026-01-07 17:26:35'),
(28, 16, 6, NULL, NULL, '2026-01-07 17:39:20', '2026-01-07 17:39:22'),
(29, 9, 6, NULL, 70, '2026-01-09 12:49:09', '2026-01-09 13:11:21'),
(30, 9, 6, NULL, 64, '2026-01-19 12:33:35', '2026-01-19 12:33:35');

-- --------------------------------------------------------

--
-- Table structure for table `cupom_uso`
--

CREATE TABLE `cupom_uso` (
  `id` int NOT NULL,
  `cupom_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `pedido_id` int DEFAULT NULL,
  `data_uso` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cupom_uso`
--

INSERT INTO `cupom_uso` (`id`, `cupom_id`, `usuario_id`, `pedido_id`, `data_uso`) VALUES
(1, 6, 9, 29, '2026-01-12 12:34:05'),
(2, 6, 3, 30, '2026-01-12 12:35:52'),
(3, 7, 9, 31, '2026-01-12 13:05:03'),
(4, 7, 3, 32, '2026-01-12 13:06:30'),
(5, 7, 6, 33, '2026-01-12 13:35:38'),
(6, 8, 9, 34, '2026-01-12 13:40:51'),
(7, 8, 16, 35, '2026-01-12 13:49:43'),
(8, 9, 9, 37, '2026-01-12 13:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `cupons`
--

CREATE TABLE `cupons` (
  `id` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo_desconto` enum('porcentagem','fixo') NOT NULL,
  `valor_desconto` decimal(10,2) NOT NULL,
  `valor_minimo` decimal(10,2) DEFAULT '0.00',
  `data_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_fim` datetime DEFAULT NULL,
  `limite_uso` int DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `usuario_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cupons`
--

INSERT INTO `cupons` (`id`, `codigo`, `descricao`, `tipo_desconto`, `valor_desconto`, `valor_minimo`, `data_inicio`, `data_fim`, `limite_uso`, `ativo`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST-1768180608', 'Cupom de Teste', 'porcentagem', 10.00, 50.00, '2026-01-11 22:16:48', '2026-02-11 23:59:59', 100, 1, 3, '2026-01-12 01:16:48', '2026-01-12 01:16:48'),
(2, 'TEST-1768180638', 'Cupom de Teste', 'porcentagem', 10.00, 50.00, '2026-01-11 22:17:18', '2026-02-11 23:59:59', 100, 1, 3, '2026-01-12 01:17:18', '2026-01-12 01:17:18'),
(4, 'ABC10', 'Cupom de Fornecedor', 'porcentagem', 10.00, 5000.00, '2026-01-11 22:20:23', '2026-01-12 23:59:59', 2, 1, 6, '2026-01-12 01:20:23', '2026-01-12 01:20:23'),
(6, 'ABC123', 'Cupom de Fornecedor', 'porcentagem', 10.00, 123.00, '2026-01-12 12:27:06', '2026-01-15 23:59:59', 2, 1, 9, '2026-01-12 15:27:06', '2026-01-12 15:27:06'),
(7, 'A123', 'Cupom de Fornecedor', 'porcentagem', 10.00, 123.00, '2026-01-12 12:56:13', '2026-01-14 23:59:59', 3, 1, 9, '2026-01-12 15:56:13', '2026-01-12 15:56:13'),
(8, 'A147', 'Cupom de Fornecedor', 'porcentagem', 10.00, 100.00, '2026-01-12 13:40:27', '2026-01-13 23:59:59', 3, 0, 9, '2026-01-12 16:40:27', '2026-01-12 16:56:50'),
(9, 'A1235', 'Cupom de Fornecedor', 'porcentagem', 10.00, 200.00, '2026-01-12 13:57:43', '2026-01-14 23:59:59', 3, 0, 9, '2026-01-12 16:57:43', '2026-01-12 16:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `cep` varchar(10) NOT NULL,
  `rua` varchar(255) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `pais` varchar(50) NOT NULL DEFAULT 'Brasil'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enderecos`
--

INSERT INTO `enderecos` (`id`, `usuario_id`, `cep`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`) VALUES
(6, 4, '09687-100', 'Rua General Izidoro Dias Lopes', '314', 'Casa', 'Paulicéia', 'São Bernardo do Campo', 'SP', 'Brasil'),
(7, 8, '13058-011', 'Rua Padre Josimo Moraes Tavares', 'S/N', 'casa', 'Conjunto Habitacional Parque Itajaí', 'Campinas', 'SP', 'Brasil'),
(11, 6, '13184000', 'Rua Sete de Setembro', '123', 'casa', 'Parque Ortolândia', 'Hortolândia', 'SP', 'Brasil'),
(13, 9, '90880-310', 'Avenida Moab Caldas', '159', 'Casa', 'Santa Tereza', 'Porto Alegre', 'RS', 'Brasil'),
(14, 3, '85806-470', 'Avenida Presidente Tancredo Neves', '123', NULL, 'Santa Cruz', 'Cascavel', 'PR', 'Brasil');

-- --------------------------------------------------------

--
-- Table structure for table `entregas`
--

CREATE TABLE `entregas` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `codigo_rastreio` varchar(100) DEFAULT NULL,
  `transportadora` varchar(100) DEFAULT NULL,
  `data_envio` datetime DEFAULT NULL,
  `status_entrega` varchar(50) NOT NULL DEFAULT 'nao_enviado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int NOT NULL,
  `conversa_id` int NOT NULL,
  `remetente_id` int NOT NULL,
  `conteudo` text NOT NULL,
  `lida` tinyint(1) DEFAULT '0',
  `data_envio` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mensagens`
--

INSERT INTO `mensagens` (`id`, `conversa_id`, `remetente_id`, `conteudo`, `lida`, `data_envio`) VALUES
(1, 1, 3, 'Olá, este produto ainda está disponível? 22:21:31', 1, '2025-12-29 18:21:31'),
(2, 1, 6, 'Sim, temos em estoque! 22:21:31', 1, '2025-12-29 18:21:31'),
(3, 5, 9, 'Olá, Sandra. Gostaria que fosse informado qual é a voltagem do aparalho.', 1, '2025-12-29 18:27:44'),
(4, 5, 6, 'Boa tarde, Marcelo. Como o produto ainda está em fase de teste, não foi determinada a voltagem do aparelho, pois, nesse momento, só temos a foto do produto que gerado por AI.', 1, '2025-12-29 18:30:03'),
(5, 9, 3, 'Olá, Marcelo. Eu gostaria de saber mais detalhes sobre esse Robô', 1, '2025-12-29 19:00:42'),
(6, 8, 9, 'adssadsad', 0, '2025-12-29 19:02:58'),
(7, 5, 6, 'Lembrando que o produto está sendo vendido é uma cadeira', 1, '2025-12-29 19:09:47'),
(8, 15, 9, 'Boa tarde, gostaria de saber qual é a voltagem de parafusadeira', 1, '2025-12-29 19:15:22'),
(9, 13, 6, 'asdasdasdasdsa', 0, '2025-12-29 19:16:08'),
(10, 13, 6, 'asdasdasdasda', 0, '2025-12-29 19:26:37'),
(11, 15, 9, 'asdadasdas', 0, '2025-12-29 19:34:36'),
(12, 14, 9, 'dgergdgdz\\fafafaf', 0, '2025-12-29 19:36:14'),
(13, 16, 9, 'asdasdasdasdas', 1, '2025-12-29 19:50:28'),
(14, 14, 9, 'sfsfsdfsdf', 0, '2025-12-29 19:54:55'),
(15, 14, 9, 'adadasdasdasdsa', 0, '2025-12-29 19:56:05'),
(16, 18, 6, 'Eu gostaria de informar que esse produto  fictiício', 0, '2025-12-31 16:16:42'),
(17, 19, 9, 'Boa tarde', 0, '2026-01-07 13:01:56'),
(18, 20, 9, 'asdsadsadsadd', 0, '2026-01-07 14:27:15'),
(19, 21, 9, 'asdasdasdsadasdsad', 0, '2026-01-07 14:29:58'),
(20, 20, 9, 'asdsadasda', 0, '2026-01-07 14:45:43'),
(21, 22, 16, 'asdsadsadas', 0, '2026-01-07 14:54:08'),
(22, 23, 16, 'asdsadasdasdas', 1, '2026-01-07 15:14:49'),
(23, 24, 16, 'asdasdasdsadasdassada', 1, '2026-01-07 15:25:58'),
(24, 25, 9, 'sdasdasdasdasd', 1, '2026-01-07 15:32:37'),
(25, 26, 9, '\\zx\\zx\\zxz\\xz\\', 1, '2026-01-07 17:06:23'),
(26, 26, 9, 'asdasdsadadasd', 1, '2026-01-07 17:24:18'),
(27, 27, 9, 'adadadas', 1, '2026-01-07 17:26:35'),
(28, 26, 9, 'asdsadsadasd', 1, '2026-01-07 17:28:41'),
(29, 28, 16, 'A', 1, '2026-01-07 17:39:22'),
(30, 25, 16, 'asdsadasdasd', 1, '2026-01-07 17:41:23'),
(31, 29, 9, 'Boa tarde, Sandra. Tudo bem ?  Eu tenho uma loja de produtos e estou querendo montar um estoque com esse chaleira (50 unidades). Por conta disso, gostaria de saber se é possível haver algum desconto.', 1, '2026-01-09 12:54:48'),
(32, 29, 6, 'Boa tarde! Tudo bem? Agradecemos seu interesse em nossos produtos. Para compras em atacado, como no caso das 50 unidades da chaleira, conseguimos oferecer um desconto especial de 10% sobre o valor total. Ficamos à disposição para prosseguir com o pedido ou esclarecer qualquer dúvida.', 1, '2026-01-09 13:11:21');

-- --------------------------------------------------------

--
-- Table structure for table `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `mensagem` text,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `tipo` varchar(50) DEFAULT 'primary',
  `link` varchar(255) DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `mensagem`, `lida`, `tipo`, `link`, `data_criacao`) VALUES
(1, 3, 'Teste de notificação executado em 29/12/2025 21:04:12', 1, 'success', 'tela_minha_conta.php', '2025-12-29 17:04:12'),
(2, 6, 'Nova mensagem: Olá, este produto ainda está disponível? 22:21:...', 1, 'primary', 'tela_chat.php?chat_id=1', '2025-12-29 18:21:31'),
(3, 3, 'Nova mensagem: Sim, temos em estoque! 22:21:31', 1, 'primary', 'tela_chat.php?chat_id=1', '2025-12-29 18:21:31'),
(4, 6, 'Nova mensagem: Olá, Sandra. Gostaria que fosse informado qual é...', 1, 'primary', 'tela_chat.php?chat_id=5', '2025-12-29 18:27:44'),
(5, 9, 'Nova mensagem: Boa tarde, Marcelo. Como o produto ainda está em ...', 1, 'primary', 'tela_chat.php?chat_id=5', '2025-12-29 18:30:03'),
(6, 9, 'Nova mensagem: Olá, Marcelo. Eu gostaria de saber mais detalhes ...', 1, 'primary', 'tela_chat.php?chat_id=9', '2025-12-29 19:00:42'),
(7, 9, 'Nova mensagem: adssadsad', 1, 'primary', 'tela_chat.php?chat_id=8', '2025-12-29 19:02:58'),
(8, 9, 'Nova mensagem: Lembrando que o produto está sendo vendido é uma...', 1, 'primary', 'tela_chat.php?chat_id=5', '2025-12-29 19:09:47'),
(9, 6, 'Nova mensagem: Boa tarde, gostaria de saber qual é a voltagem de...', 1, 'primary', 'tela_chat.php?chat_id=15', '2025-12-29 19:15:22'),
(10, 9, 'Nova mensagem: asdasdasdasdsa', 1, 'primary', 'tela_chat.php?chat_id=13', '2025-12-29 19:16:08'),
(11, 9, 'Nova mensagem: asdasdasdasda', 1, 'primary', 'tela_chat.php?chat_id=13', '2025-12-29 19:26:37'),
(12, 3, 'Teste Notificação DEBUG 1767047612', 1, 'info', '#', '2025-12-29 19:33:32'),
(13, 6, 'Teste Notificação DEBUG 1767047612', 1, 'info', '#', '2025-12-29 19:33:32'),
(14, 6, 'Nova mensagem: asdadasdas', 1, 'primary', 'tela_chat.php?chat_id=15', '2025-12-29 19:34:36'),
(15, 6, 'Nova mensagem: dgergdgdz\\fafafaf', 1, 'primary', 'tela_chat.php?chat_id=14', '2025-12-29 19:36:14'),
(16, 6, 'Nova mensagem: asdasdasdasdas', 1, 'primary', 'tela_chat.php?chat_id=16', '2025-12-29 19:50:28'),
(17, 6, 'Nova mensagem: sfsfsdfsdf', 1, 'primary', 'tela_chat.php?chat_id=14', '2025-12-29 19:54:55'),
(18, 6, 'Nova mensagem: adadasdasdasdsa', 1, 'primary', 'tela_chat.php?chat_id=14', '2025-12-29 19:56:05'),
(19, 9, 'Nova mensagem: Eu gostaria de informar que esse produto  fictiíc...', 1, 'primary', 'tela_chat.php?chat_id=18', '2025-12-31 16:16:42'),
(20, 6, 'Nova mensagem: Boa tarde', 1, 'primary', 'tela_chat.php?chat_id=19', '2026-01-07 13:01:56'),
(21, 6, 'Marcelo enviou uma mensagem referente ao produto MacBook Pro M3', 1, 'primary', 'tela_chat.php?chat_id=20', '2026-01-07 14:27:15'),
(22, 6, 'Marcelo enviou uma mensagem referente ao produto Sony WH-1000XM5', 1, 'primary', 'tela_chat.php?chat_id=21', '2026-01-07 14:29:58'),
(23, 4, 'Test Notification from Debug Script', 0, 'primary', '#', '2026-01-07 14:42:16'),
(24, 4, 'Test Notification from Debug Script', 0, 'primary', '#', '2026-01-07 14:42:55'),
(25, 6, 'Marcelo enviou uma mensagem referente ao produto MacBook Pro M3', 1, 'primary', 'tela_chat.php?chat_id=20', '2026-01-07 14:45:43'),
(26, 6, 'AAAA enviou uma mensagem referente ao produto MacBook Pro M3', 1, 'primary', 'tela_chat.php?chat_id=22', '2026-01-07 14:54:08'),
(27, 6, 'AAAA enviou uma mensagem referente ao produto Skincare Premium', 1, 'primary', 'tela_chat.php?chat_id=23', '2026-01-07 15:14:49'),
(28, 6, 'AAAA enviou uma mensagem referente ao produto Pen Premium', 1, 'primary', 'tela_chat.php?chat_id=24', '2026-01-07 15:25:58'),
(29, 16, 'Marcelo enviou uma mensagem referente ao produto produto robo teste', 1, 'primary', 'tela_chat.php?chat_id=25', '2026-01-07 15:32:37'),
(31, 6, 'Marcelo enviou uma mensagem referente ao produto Pen Premium', 1, 'primary', 'tela_chat.php?chat_id=26', '2026-01-07 17:06:23'),
(32, 6, 'Teste Manul para Usuário 6', 1, 'primary', '#', '2026-01-07 17:14:18'),
(33, 6, 'Marcelo enviou uma mensagem referente ao produto Pen Premium', 1, 'primary', 'tela_chat.php?chat_id=26', '2026-01-07 17:24:18'),
(34, 6, 'Marcelo enviou uma mensagem referente ao produto AAAA', 1, 'primary', 'tela_chat.php?chat_id=27', '2026-01-07 17:26:35'),
(35, 6, 'Marcelo enviou uma mensagem referente ao produto Pen Premium', 1, 'primary', 'tela_chat.php?chat_id=26', '2026-01-07 17:28:41'),
(36, 6, 'AAAA enviou uma mensagem referente ao produto AAAA', 1, 'primary', 'tela_chat.php?chat_id=28', '2026-01-07 17:39:22'),
(37, 9, 'AAAA enviou uma mensagem referente ao produto um produto', 1, 'primary', 'tela_chat.php?chat_id=25', '2026-01-07 17:41:23'),
(38, 6, 'Marcelo enviou uma mensagem referente ao produto Chaleira Elétrica EEK10', 1, 'primary', 'tela_chat.php?chat_id=29', '2026-01-09 12:54:48'),
(39, 9, 'Sandra Gomes Fictícia enviou uma mensagem referente ao produto Chaleira Elétrica EEK10', 1, 'primary', 'tela_chat.php?chat_id=29', '2026-01-09 13:11:21'),
(40, 6, 'Novo pedido #68 recebido! Valor: R$ 189,00', 1, 'success', 'tela_minha_conta.php?tab=painel-pedidos-recebidos', '2026-01-20 12:57:32'),
(41, 3, 'Seu pedido #68 mudou de status para: PROCESSING', 0, 'info', 'tela_minha_conta.php?tab=painel-compras', '2026-01-20 13:00:03'),
(42, 3, 'Seu pedido #68 mudou de status para: SHIPPED', 1, 'info', 'tela_minha_conta.php?tab=painel-compras', '2026-01-20 13:00:07'),
(43, 6, 'Novo pedido #69 recebido! Valor: R$ 15.699,00', 1, 'success', 'tela_minha_conta.php?tab=painel-pedidos-recebidos', '2026-01-20 13:00:50'),
(44, 16, 'Seu pedido #69 mudou de status para: PROCESSING', 1, 'info', 'tela_minha_conta.php?tab=painel-compras', '2026-01-20 13:01:11'),
(45, 16, 'Seu pedido #69 mudou de status para: SHIPPED', 1, 'info', 'tela_minha_conta.php?tab=painel-compras', '2026-01-20 13:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_events`
--

CREATE TABLE `order_events` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `description` text,
  `actor_type` varchar(20) NOT NULL,
  `actor_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_events`
--

INSERT INTO `order_events` (`id`, `order_id`, `old_status`, `new_status`, `description`, `actor_type`, `actor_id`, `created_at`) VALUES
(1, 63, NULL, 'CREATED', 'Pedido criado', 'client', 9, '2026-01-19 14:05:54'),
(2, 63, 'CREATED', 'CANCELED', 'Cancelado pelo usuário', 'supplier', 9, '2026-01-19 14:08:02'),
(3, 64, NULL, 'CREATED', 'Pedido criado', 'client', 9, '2026-01-19 20:23:38'),
(4, 64, 'CREATED', 'PROCESSING', '', 'supplier', 6, '2026-01-20 12:29:26'),
(5, 64, 'PROCESSING', 'SHIPPED', '', 'supplier', 6, '2026-01-20 12:30:37'),
(6, 66, NULL, 'CREATED', 'Pedido criado', 'client', 3, '2026-01-20 12:36:45'),
(7, 66, 'CREATED', 'PROCESSING', '', 'supplier', 6, '2026-01-20 12:37:08'),
(8, 66, 'PROCESSING', 'SHIPPED', '', 'supplier', 6, '2026-01-20 12:37:14'),
(9, 66, 'SHIPPED', 'DELIVERED', '', 'client', 3, '2026-01-20 12:37:29'),
(10, 68, NULL, 'CREATED', 'Pedido criado', 'client', 3, '2026-01-20 12:57:32'),
(11, 68, 'CREATED', 'PROCESSING', '', 'supplier', 6, '2026-01-20 13:00:03'),
(12, 68, 'PROCESSING', 'SHIPPED', '', 'supplier', 6, '2026-01-20 13:00:07'),
(13, 68, 'SHIPPED', 'DELIVERED', '', 'client', 3, '2026-01-20 13:00:32'),
(14, 69, NULL, 'CREATED', 'Pedido criado', 'client', 16, '2026-01-20 13:00:50'),
(15, 69, 'CREATED', 'PROCESSING', '', 'supplier', 6, '2026-01-20 13:01:11'),
(16, 69, 'PROCESSING', 'SHIPPED', '', 'supplier', 6, '2026-01-20 13:01:19'),
(17, 69, 'SHIPPED', 'DELIVERED', '', 'client', 16, '2026-01-20 13:01:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_issues`
--

CREATE TABLE `order_issues` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text,
  `status` varchar(20) DEFAULT 'open',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pendente',
  `data_pagamento` datetime DEFAULT NULL,
  `valor_pago` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `endereco_id` int DEFAULT NULL,
  `data_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'pendente',
  `valor_total` decimal(10,2) NOT NULL,
  `cupom_id` int DEFAULT NULL,
  `valor_desconto` decimal(10,2) DEFAULT '0.00',
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `canceled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `supplier_id`, `endereco_id`, `data_pedido`, `status`, `valor_total`, `cupom_id`, `valor_desconto`, `shipped_at`, `delivered_at`, `canceled_at`, `created_at`) VALUES
(1, 4, NULL, 6, '2025-10-27 10:00:00', 'processando', 839.00, NULL, 0.00, NULL, NULL, NULL, NULL),
(2, 4, NULL, 6, '2025-10-28 20:34:28', 'processando', 24999.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(3, 3, NULL, NULL, '2025-10-28 20:50:28', 'processando', 135.98, NULL, 0.00, NULL, NULL, NULL, NULL),
(4, 3, NULL, NULL, '2025-10-28 20:54:21', 'processando', 75005.96, NULL, 0.00, NULL, NULL, NULL, NULL),
(5, 3, NULL, NULL, '2025-10-28 22:08:20', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(6, 3, NULL, NULL, '2025-10-28 22:24:31', 'processando', 634.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(7, 6, NULL, NULL, '2025-11-21 15:03:10', 'processando', 25005.98, NULL, 0.00, NULL, NULL, NULL, NULL),
(8, 8, NULL, NULL, '2025-11-21 19:30:35', 'processando', 1509.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(9, 8, NULL, 7, '2025-11-21 19:43:02', 'processando', 629.00, NULL, 0.00, NULL, NULL, NULL, NULL),
(10, 9, NULL, NULL, '2025-11-22 15:04:55', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(11, 9, NULL, NULL, '2025-12-01 16:34:57', 'processando', 634.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(12, 9, NULL, NULL, '2025-12-02 14:14:57', 'processando', 701.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(13, 9, NULL, NULL, '2025-12-02 14:32:26', 'processando', 575.04, NULL, 0.00, NULL, NULL, NULL, NULL),
(14, 9, NULL, NULL, '2025-12-02 15:31:51', 'processando', 215.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(15, 9, NULL, NULL, '2025-12-02 15:36:06', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(16, 9, NULL, NULL, '2025-12-02 15:37:27', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(17, 9, NULL, NULL, '2025-12-02 15:38:48', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(18, 9, NULL, NULL, '2025-12-02 15:41:11', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(19, 9, NULL, NULL, '2025-12-02 15:43:57', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(20, 9, NULL, NULL, '2025-12-02 15:45:23', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(21, 9, NULL, NULL, '2025-12-02 15:47:32', 'processando', 194.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(23, 9, NULL, NULL, '2025-12-25 19:19:40', 'processando', 1517.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(24, 9, NULL, NULL, '2025-12-29 17:48:38', 'processando', 3652.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(25, 9, NULL, NULL, '2025-12-29 18:45:39', 'processando', 1509.99, NULL, 0.00, NULL, NULL, NULL, NULL),
(26, 6, NULL, NULL, '2025-12-29 19:37:48', 'processando', 25005.98, NULL, 0.00, NULL, NULL, NULL, NULL),
(27, 6, NULL, 11, '2026-01-07 19:26:41', 'processando', 90000.00, NULL, 0.00, NULL, NULL, NULL, NULL),
(29, 9, NULL, 13, '2026-01-12 12:34:05', 'processando', 766.84, 6, 85.20, NULL, NULL, NULL, NULL),
(30, 3, NULL, NULL, '2026-01-12 12:35:52', 'processando', 7525.46, 6, 836.16, NULL, NULL, NULL, NULL),
(31, 9, NULL, NULL, '2026-01-12 13:05:03', 'processando', 565.47, 7, 62.83, NULL, NULL, NULL, NULL),
(32, 3, NULL, NULL, '2026-01-12 13:06:30', 'processando', 2222.88, 7, 246.99, NULL, NULL, NULL, NULL),
(33, 6, NULL, NULL, '2026-01-12 13:35:38', 'processando', 401.48, 7, 44.61, NULL, NULL, NULL, NULL),
(34, 9, NULL, NULL, '2026-01-12 13:40:51', 'processando', 287.90, 8, 31.99, NULL, NULL, NULL, NULL),
(35, 16, NULL, NULL, '2026-01-12 13:49:43', 'processando', 1490.45, 8, 165.61, NULL, NULL, NULL, NULL),
(36, 16, NULL, NULL, '2026-01-12 13:53:56', 'processando', 135.89, NULL, 0.00, NULL, NULL, NULL, NULL),
(37, 9, NULL, NULL, '2026-01-12 13:58:20', 'processando', 667.81, 9, 74.20, NULL, NULL, NULL, NULL),
(38, 3, NULL, 14, '2026-01-16 15:53:06', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(39, 3, NULL, 14, '2026-01-16 18:27:22', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(40, 3, NULL, 14, '2026-01-16 18:44:57', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(41, 3, NULL, 14, '2026-01-16 18:46:09', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(42, 3, NULL, 14, '2026-01-16 19:08:33', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(43, 3, NULL, 14, '2026-01-16 19:14:24', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(44, 3, NULL, 14, '2026-01-16 19:31:42', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(45, 3, NULL, 14, '2026-01-16 19:44:58', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(46, 3, NULL, 14, '2026-01-16 19:53:03', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(47, 3, NULL, 14, '2026-01-16 20:01:14', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(48, 3, NULL, 14, '2026-01-16 20:15:28', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(49, 3, NULL, 14, '2026-01-16 20:31:51', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(50, 3, NULL, 14, '2026-01-16 20:56:14', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(51, 3, NULL, 14, '2026-01-16 21:24:20', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(52, 3, NULL, 14, '2026-01-17 15:14:16', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(53, 3, NULL, 14, '2026-01-17 19:28:23', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(54, 3, NULL, 14, '2026-01-18 20:16:21', 'processando', 129.90, NULL, 0.00, NULL, NULL, NULL, NULL),
(63, 9, 6, NULL, '2026-01-19 14:05:54', 'CANCELED', 30792.00, NULL, 0.00, NULL, NULL, '2026-01-19 14:08:02', '2026-01-19 14:05:54'),
(64, 9, 6, 13, '2026-01-19 20:23:38', 'SHIPPED', 1551.00, NULL, 0.00, '2026-01-20 12:30:37', NULL, NULL, '2026-01-19 20:23:38'),
(66, 3, 6, 13, '2026-01-20 12:36:45', 'DELIVERED', 629.00, NULL, 0.00, '2026-01-20 12:37:14', '2026-01-20 12:37:29', NULL, '2026-01-20 12:36:45'),
(68, 3, 6, NULL, '2026-01-20 12:57:32', 'DELIVERED', 189.00, NULL, 0.00, '2026-01-20 13:00:07', '2026-01-20 13:00:32', NULL, '2026-01-20 12:57:32'),
(69, 16, 6, NULL, '2026-01-20 13:00:50', 'DELIVERED', 15699.00, NULL, 0.00, '2026-01-20 13:01:19', '2026-01-20 13:01:36', NULL, '2026-01-20 13:00:50');

-- --------------------------------------------------------

--
-- Table structure for table `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 23, 1, 629.00),
(2, 1, 22, 1, 210.00),
(3, 2, 30, 1, 24999.99),
(4, 3, 32, 1, 129.99),
(5, 4, 30, 3, 24999.99),
(6, 5, 22, 1, 189.00),
(7, 6, 23, 1, 629.00),
(8, 7, 30, 1, 24999.99),
(9, 8, 24, 1, 1504.00),
(10, 9, 23, 1, 629.00),
(11, 10, 22, 1, 189.00),
(12, 11, 23, 1, 629.00),
(13, 12, 26, 1, 696.00),
(14, 13, 25, 1, 569.05),
(15, 14, 22, 1, 210.00),
(16, 15, 22, 1, 189.00),
(17, 16, 22, 1, 189.00),
(18, 17, 22, 1, 189.00),
(19, 18, 22, 1, 189.00),
(20, 19, 22, 1, 189.00),
(21, 20, 22, 1, 189.00),
(22, 21, 22, 1, 189.00),
(23, 23, 22, 8, 189.00),
(24, 24, 25, 2, 599.00),
(25, 24, 24, 1, 1504.00),
(26, 24, 22, 5, 189.00),
(27, 25, 24, 1, 1504.00),
(28, 26, 30, 1, 24999.99),
(29, 27, 30, 50, 1800.00),
(31, 29, 59, 1, 629.00),
(32, 29, 69, 1, 223.05),
(33, 30, 68, 10, 569.09),
(34, 30, 67, 6, 36.95),
(35, 30, 60, 1, 2449.02),
(36, 31, 69, 2, 314.15),
(37, 32, 66, 13, 189.99),
(38, 33, 69, 2, 223.05),
(39, 34, 70, 1, 129.90),
(40, 34, 66, 1, 189.99),
(41, 35, 68, 3, 552.02),
(42, 36, 70, 1, 129.90),
(43, 37, 68, 1, 552.02),
(44, 37, 66, 1, 189.99),
(45, 38, 70, 1, 129.90),
(46, 39, 70, 1, 129.90),
(47, 40, 70, 1, 129.90),
(48, 41, 70, 1, 129.90),
(49, 42, 70, 1, 129.90),
(50, 43, 70, 1, 129.90),
(51, 44, 70, 1, 129.90),
(52, 45, 70, 1, 129.90),
(53, 46, 70, 1, 129.90),
(54, 47, 70, 1, 129.90),
(55, 48, 70, 1, 129.90),
(56, 49, 70, 1, 129.90),
(57, 50, 70, 1, 129.90),
(58, 51, 70, 1, 129.90),
(59, 52, 70, 1, 129.90),
(60, 53, 70, 1, 129.90),
(61, 54, 70, 1, 129.90),
(62, 63, 64, 1, 30792.00),
(63, 64, 55, 1, 1551.00),
(64, 66, 59, 1, 629.00),
(65, 68, 58, 1, 189.00),
(66, 69, 65, 1, 15699.00);

-- --------------------------------------------------------

--
-- Table structure for table `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text,
  `preco` decimal(10,2) DEFAULT NULL,
  `desconto` int DEFAULT '0',
  `categoria_id` int DEFAULT NULL,
  `estoque` int DEFAULT '0',
  `imagem_url` varchar(1024) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `usuario_id` int DEFAULT NULL,
  `ordem_destaque` int DEFAULT '999999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `desconto`, `categoria_id`, `estoque`, `imagem_url`, `status`, `usuario_id`, `ordem_destaque`) VALUES
(22, 'Camisa Social Slim', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n--- ESPECIFICAÇÕES ---\nProduto original\n--- DESCRIÇÃO ---\nElegância para o trabalho.', 180.00, 0, 12, 50, 'https://cdn.dummyjson.com/product-images/mens-shirts/blue-&-black-check-shirt/thumbnail.webp', 'inativo', 6, -22),
(23, 'Calça Jeans Premium', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n--- ESPECIFICAÇÕES ---\nProduto original\n--- DESCRIÇÃO ---\nDurabilidade e estilo.', 220.00, 0, 12, 50, 'https://placehold.co/600x400?text=Jeans', 'inativo', 6, -23),
(24, 'Tênis Casual Branco', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n--- ESPECIFICAÇÕES ---\nProduto original\n--- DESCRIÇÃO ---\nCombina com tudo.', 300.00, 0, 12, 50, 'https://cdn.dummyjson.com/product-images/mens-shoes/sports-sneakers-off-white-&-red/thumbnail.webp', 'inativo', 6, -24),
(25, 'Jaqueta de Couro', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n--- ESPECIFICAÇÕES ---\nProduto original\n--- DESCRIÇÃO ---\nEstilo atemporal.', 450.00, 0, 12, 50, 'https://placehold.co/600x400?text=Jacket', 'inativo', 6, -25),
(26, 'Sapato Oxford', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n--- ESPECIFICAÇÕES ---\nProduto original\n--- DESCRIÇÃO ---\nPara ocasiões formais.', 280.00, 0, 12, 50, 'https://cdn.dummyjson.com/product-images/womens-shoes/calvin-klein-heel-shoes/thumbnail.webp', 'inativo', 6, -26),
(30, 'Bicicleta Mountain Bike', '--- CARACTERÍSTICAS ---\nMarca: Genérica\n\n--- ESPECIFICAÇÕES ---\nProduto original\n\n--- DESCRIÇÃO ---\nAventura em qualquer terreno.', 1800.00, 0, 14, 100, 'https://cdn.dummyjson.com/product-images/motorcycle/generic-motorcycle/thumbnail.webp', 'inativo', 6, -30),
(32, 'Perfume Premium', '--- CARACTERÍSTICAS ---\nMarca: Importada\n\n--- ESPECIFICAÇÕES ---\nAlta qualidade\n\n--- DESCRIÇÃO ---\nProduto excelente.', 99.90, 0, 1, 20, 'https://placehold.co/600x400?text=perfume', 'inativo', 6, -32),
(55, 'Bicicleta Ergométrica Para Spinning', '--- CARACTERÍSTICAS ---\nPeso máximo suportado: 120 kg\nÉ dobrável: Não\nCor: Preto/Vermelho\nAltura: 1.2 m\n\n--- ESPECIFICAÇÕES ---\nSistema de resistência mecânica que é operado a partir do botão de ajuste.\n\n--- DESCRIÇÃO ---\nA Bicicleta Ergométrica Spinning PACE3000 Odin Fit é ideal para uso residencial, proporcionando exercícios aeróbicos que fortalecem a musculatura e melhoram o condicionamento cardiorrespiratório. Oferece benefícios como queima calórica, aumento da disposição e baixo impacto nas articulações, sendo indicada também para reabilitação. Possui ajustes no selim, guidão e pedais, além de roda de inércia de 8kg com resistência mecânica ajustável. Inclui monitor multifunções com sensor de pulso e suporte para tablet ou smartphone.', 1551.00, 5, 6, 99, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767826978/loja_ponto_com/produtos/taxpkh7a5xaym7kfzqmf.webp', 'ativo', 6, 2),
(56, 'Cadeira De Escritório Ergonômica Giratória', '--- CARACTERÍSTICAS ---\nÉ gamer: Sim\nCom apoio de braços ajustável: Sim\nÉ giratória: Sim\nMaterial Do Estofamento: Mesh Espuma Látex\n\n--- ESPECIFICAÇÕES ---\n\n--- DESCRIÇÃO ---\nCadeira De Escritório Ergonômica Com Apoio De Braços 3D Plus\r\n\r\nApresentamos a nossa excepcional Cadeira de Escritório Ergonômica 3D Plus, uma fusão perfeita de design elegante e funcionalidade excepcional. O revestimento em tecido Mesh oferece conforto respirável, enquanto o encosto de cabeça ajustável em dois sentidos proporciona suporte personalizado. Com encosto reclinável, altura ajustável e apoio de braço reversível, esta cadeira se adapta perfeitamente às suas preferências.\r\n', 599.32, 6, 23, 25, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827660/loja_ponto_com/produtos/hzoadq8dwjng4y6urnbw.webp', 'ativo', 6, 3),
(57, 'Cafeteira Elétrica Electrolux', '--- CARACTERÍSTICAS ---\n Nome da marca: Electrolux\nFabricante: ‎Electrolux\nModelo: ‎ECM25\nCor: ‎Granite Grey\n\n--- ESPECIFICAÇÕES ---\nNúmero da peça: ‎ECM25\nCaracterísticas especiais: filtro permanente\nPeças para montagem: ‎filtro permanente, jarra\n\n--- DESCRIÇÃO ---\nA Cafeteira Elétrica Programável Experience Electrolux possui capacidade de 1,2L, permitindo preparar até 30 cafezinhos. Com timer de 24 horas, painel programável e função manter aquecido, garante praticidade e café quente a qualquer momento. Conta com sistema corta pingos, desligamento automático e placa de aquecimento antiaderente. Seu filtro permanente removível dispensa o uso de filtros de papel, sendo mais econômico e sustentável. Possui acabamento em aço inox escovado e indicador de nível de água para maior modernidade e facilidade no uso.', 259.90, 1, 7, 20, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828021/loja_ponto_com/produtos/ttqlyl7k1bhyvvvzvaaj.jpg', 'ativo', 6, 5),
(58, 'Parafusadeira E Furadeira Impacto', '--- CARACTERÍSTICAS ---\nÉ sem fio: Sim\nCom função percutor: Sim\nTamanho do mandril: 10 mm\n\n--- ESPECIFICAÇÕES ---\nCom função reversa.\nVem com maleta de transporte.\nSua frequência é de 60Hz.\nPossui função parafusadeira.\nInclui função martelete.\n\n--- DESCRIÇÃO ---\nA Parafusadeira/Furadeira de Impacto Profissional The Black Tools TB-21PW 21V é ideal para uso profissional e doméstico, oferecendo potência, durabilidade e praticidade. Com velocidade variável, função reversível e impacto, garante excelente desempenho em metal, madeira e plástico. Possui empunhadura Soft Grip e indicador de carga da bateria para maior conforto e controle. Acompanha maleta completa com brocas, soquetes, adaptadores e bits. Compacta e leve, é a escolha certa para quem busca eficiência e qualidade nas tarefas do dia a dia.', 189.00, 9, 22, 24, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828265/loja_ponto_com/produtos/n4fanmphoqecxzyvx9kq.webp', 'ativo', 6, 0),
(59, 'Motor Estacionário Gasolina 7hp Kawashima ', '--- CARACTERÍSTICAS ---\nTipo De Motor: Monocilíndrico 4T OHV\nRefrigeração: Ar\nCombustível: Gasolina\nDiâmetro x Curso: 68 x 45 mm\nCilindrada: 208 cc\n\n--- ESPECIFICAÇÕES ---\nTipo de ignição: manual.\nDimensões: 39cm de largura x 39cm de comprimento x 39cm de altura.\nPeso: 16g.\nTipo de combustível: Gasolina.\n\n--- DESCRIÇÃO ---\nOs motores estacionários Kawashima Serie E são equipamentos robustos que proporcionam economia e alto desempenho. Podem ser acoplados em diversos equipamentos como microtratores, motobombas, geradores, rabetas para barcos, moenda de cana e outros.\r\n', 629.00, 2, 17, 23, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828488/loja_ponto_com/produtos/inrgglai9zdqu5xam2n1.webp', 'ativo', 6, 1),
(60, 'Placa Solar 550w Peimar Monocristalino', '--- CARACTERÍSTICAS ---\nMarca: Peimar\nModelo: 550w\nCor: Prateado\nVoltagem de circuito aberto: 49.6V\nQuantidade de células: 144\n\n--- ESPECIFICAÇÕES ---\n\n--- DESCRIÇÃO ---\nPainel Solar Monocristalino Peimar OR10H550M de 550W, com tecnologia italiana Half Cell M10 | PERC, garantindo alta eficiência de 21,28% e excelente desempenho em projetos residenciais, comerciais e industriais. O kit inclui 3 unidades, cada uma com estrutura resistente em alumínio anodizado e vidro temperado antirreflexo. Suporta condições extremas, possui classe de proteção IP67 e certificações internacionais (IEC 61215 / 61730). Oferece 30 anos de garantia de performance e 25 anos de fabricação. Produto original com NF-e e seguro de responsabilidade civil incluso.\r\n', 2499.00, 2, 8, 499, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833502/loja_ponto_com/produtos/ovpsfpduhycxtrk9cj9k.webp', 'ativo', 6, 12),
(61, 'Tomada Inteligente Wifi', '--- CARACTERÍSTICAS ---\nMarca: ‎Coibeu\nFabricante: C &amp; B Global Importação e Exportação LTDA\nCertificação: ‎INMETRO:0124\nAparelhos compatíveis: ‎smartphones, tablets, computadores\n\n--- ESPECIFICAÇÕES ---\n\n--- DESCRIÇÃO ---\nFiltro de linha multifuncional com 3 tomadas, 2 portas USB e 1 Type-C, permitindo alimentar até 6 dispositivos simultaneamente. Possui carcaça resistente, materiais de alta condutividade e proteção contra sobrecarga e raios. Conta com interruptor centralizado e indicadores luminosos para uso prático e seguro. Permite controle remoto via aplicativo, temporização e integração com Alexa e Google Assistant. Oferece estatísticas de consumo de energia no app (função paga).\r\n', 129.99, 0, 4, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833718/loja_ponto_com/produtos/btvi5yvbzq8rijs6mtar.jpg', 'ativo', 6, 11),
(62, 'Samsung S85F 4K OLED Smart TV ', '--- CARACTERÍSTICAS ---\nTipo de tela: OLED\nÉ smart: Sim\nResolução: 4k\n Quantidade de portas HDM: 4\n\n--- ESPECIFICAÇÕES ---\nAlexa Embutido.\nPossui 4 portas HDMI.\nEquipado com conexão USB.\nConta com wi-fi e porto de rede\n\n--- DESCRIÇÃO ---\n65\" OLED S85F 4K Samsung Vision AI Smart TV / Smart TV Samsung Vision AI OLED 4K 2025 é uma Smart TV 4K OLED com painel de pixels autoiluminados que entrega pretos profundos, cores vivas e contraste realista. Seu processador Vision AI aprimora automaticamente brilho, cor e detalhes, além de upscaling inteligente para conteúdos mais antigos. Conta com taxa de atualização de 120 Hz, áudio imersivo e sistema Smart TV Tizen com comandos de voz e conectividade ampla. O design é fino e elegante, com controle SolarCell sustentável incluso', 5019.99, 0, 2, 125, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834110/loja_ponto_com/produtos/t79m3ex6rtsnkxjicd5l.webp', 'ativo', 6, 4),
(63, 'Kit De Internet Via Satelite Starlink Mini', '--- CARACTERÍSTICAS ---\nMarca: Starlink\nModelo: Mini\nTipo de antena: Omnidirecional\nCor: Branco\n\n--- ESPECIFICAÇÕES ---\nUnidades por kit: 1.\nFormato de venda: Unidade.\nÉ uma antena de internet via satélite.\n\n--- DESCRIÇÃO ---\nKit de Internet via Satélite Starlink Mini, ideal para quem precisa de conexão rápida e estável em qualquer lugar.\r\nCompacto, portátil e fácil de instalar, funciona mesmo em áreas remotas.\r\n\r\nOferece alta velocidade e baixa latência para trabalho, estudos e lazer.\r\n\r\nPerfeito para viagens, zonas rurais e situações sem infraestrutura tradicional.', 799.99, 6, 11, 200, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834929/loja_ponto_com/produtos/kov7jsbnjzo6hojfnxji.jpg', 'ativo', 6, 6),
(64, 'Unitree Go2 ', '--- CARACTERÍSTICAS ---\nMarca: Unitree Go2\nModelo: Unitree Go2 Pro\nCor: Cinza\n\n--- ESPECIFICAÇÕES ---\nPersonagem: CAO.\nTem forma de cão.\nFaz parte do mundo Robô.\n\n--- DESCRIÇÃO ---\nUnitree Go2 — robô-cão quadrúpede inteligente com sensor 4D LIDAR ultra-wide para reconhecimento ambiente e navegação autônoma. \r\n\r\nEquipado com bateria de longa duração, controle via app e conectividade Wi-Fi/Bluetooth/4G para monitoramento em tempo real. \r\n\r\nMovimenta-se com agilidade por terrenos variados e realiza ações programadas como seguir, mapear e evitar obstáculos. \r\n\r\nProjetado para pesquisa, educação e entretenimento com IA integrada para tomadas de decisão. \r\n\r\nCompacto, robusto e versátil, ideal para robótica avançada e aplicações exploratórias.', 30792.00, 13, 5, 499, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835170/loja_ponto_com/produtos/fhx9zivznaydpsald9ga.webp', 'ativo', 6, 9),
(65, 'MSI Raider 18 HX AI ', '--- CARACTERÍSTICAS ---\nTipo de resolução: QHD\nCom tela tátil: Não\nTamanho da tela: 18 &quot;\nCom leitor de impressão digital: Sim\n\n--- ESPECIFICAÇÕES ---\nMarca de placa gráfica dedicada: NVIDIA\nLinha de placa gráfica dedicada: GeForce RTX\nModelo de placa gráfica dedicada: 5080\n\n--- DESCRIÇÃO ---\nMSI 18\'\' Raider 18 HX AI Gaming Laptop 5090 64GB RAM 2TB SSD é um laptop gamer top-de-linha com hardware de última geração, combinando processador Intel Core Ultra 9 285HX e GPU NVIDIA GeForce RTX 5090/5080 para desempenho bruto em jogos e tarefas pesadas. Sua tela Mini-LED de 18″ UHD+ com 120 Hz e amplo gamut de cores entrega imagens detalhadas e fluidez visual impressionante. Com Wi-Fi 7, áudio Dynaudio de 6 alto-falantes e sistema térmico avançado, oferece experiência imersiva e conectividade de ponta. Além disso, conta com SSD ultrarrápido, Thunderbolt 5 e recursos de IA integrados para produtividade, streaming e criação de conteúdo. Ideal para quem busca potência, qualidade de visual e versatilidade em um único portátil.', 15699.00, 5, 11, 49, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835566/loja_ponto_com/produtos/tncqps5nm5dcnupr6en9.jpg', 'ativo', 6, 7),
(66, 'Lego Ninjago Zane ZX Mini-figurinha', '--- CARACTERÍSTICAS ---\nNúmero do modelo: ‎Ninjago\nNúmero de peças: ‎6\nFunciona a bateria ou pilha?: Não\nTipo(s) de material: ‎Acrilonitrila butadieno estireno\n\n--- ESPECIFICAÇÕES ---\nDimensões do produto: ‎9,14 x 5,84 x 1,52 cm; 9,07 g\nMarca: ‎LEGO\n\n--- DESCRIÇÃO ---\nEGO Ninjago Zane ZX Minifigure é uma mini-figura colecionável LEGO® Ninjago do personagem Zane ZX, representando o ninja branco do gelo em um visual clássico da série. Ela vem com detalhes autênticos e acessórios (dependendo da versão), perfeita para fãs e colecionadores exibirem ou usarem em construções e aventuras LEGO. Zane é conhecido por seu papel como mestre elemental do gelo, sendo um dos protagonistas da história Ninjago, valorizado por sua inteligência, coragem e lealdade. Esta mini-figura remete ao estilo das primeiras temporadas, tornando-a um item nostálgico e divertido tanto para brincar quanto para completar coleções.', 189.99, 0, 1, 110, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835867/loja_ponto_com/produtos/j19z9mi9apr1bjhyst02.jpg', 'ativo', 6, 8),
(67, 'Termômetro Infravermelho Digital Industrial', '--- CARACTERÍSTICAS ---\nFabricante: ALTOMEX\nNúmero do modelo: A641\nCor: Amarelo E Preto\nComponentes incluídos: 1 Termômetro Infravermelho, Manual De Instruções\n\n--- ESPECIFICAÇÕES ---\nPilha(s) ou bateria(s): ‏ 2AAA baterias necessárias. (inclusas)\nDimensões do produto: 8 x 3,8 x 1,5 cm; 150 g\nDISPLAY LCD: Tela digital de fácil leitura com iluminação de fundo e indicação em tempo real da temperatura medida\nFAIXA DE MEDIÇÃO: Termômetro infravermelho com ampla faixa de temperatura de -50°C a 380°C, ideal para uso industrial e culinário\n\n--- DESCRIÇÃO ---\nTermômetro infravermelho digital KLX GM320, desenvolvido para medições de temperatura sem contato com alta precisão, possui ampla faixa de medição de -50 °C a 380 °C, sendo ideal para uso industrial, culinário e em ambientes de armazenamento, conta com display LCD de fácil leitura que fornece resultados rápidos e claros, design ergonômico tipo pistola para maior conforto durante o uso contínuo, função de alternância entre Celsius e Fahrenheit, retenção de leitura para maior praticidade e construção resistente, ideal para medir fornos, freezers, alimentos e equipamentos industriais.', 41.99, 12, 4, 119, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767895185/loja_ponto_com/produtos/swyqoxvkcva8knjyqvnq.jpg', 'ativo', 6, 14),
(68, 'RoWood Quebra-cabeças 3D', '--- CARACTERÍSTICAS ---\nObjetivos educativos: ‎Habilidade de resolução de problemas\nNúmero de jogadores: ‎1\nMontagem necessária: Sim\nDimensões do produto: ‎46,99 x 30,68 x 0,1 cm; 2,32 quilogramas\nIdade recomendada: ‎14 anos e acima\n\n--- ESPECIFICAÇÕES ---\nMaterial em madeira: feito com peças de madeira cortadas a laser, exigindo cuidado no manuseio para evitar quebras.\nNão requer cola ou ferramentas elétricas: as peças são projetadas para encaixe preciso, facilitando a montagem.\nMontagem manual: o produto é um quebra-cabeça/modelo 3D que precisa ser montado pelo usuário, não vem pronto.\n\n--- DESCRIÇÃO ---\nQuebra-cabeça 3D em madeira com design detalhado de navio Viking, desenvolvido para proporcionar uma experiência envolvente de montagem manual, ideal para quem aprecia desafios criativos e atividades artesanais. Produzido em madeira cortada a laser, oferece encaixes precisos e acabamento de qualidade, sem necessidade de cola ou ferramentas. Indicado para adolescentes e adultos, estimula concentração, raciocínio lógico e coordenação motora durante a montagem. Após finalizado, transforma-se em uma peça decorativa elegante, perfeita para ambientes residenciais ou escritórios. Uma excelente opção de presente para entusiastas de quebra-cabeças, modelismo e temas históricos.', 569.09, 3, 1, 186, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896218/loja_ponto_com/produtos/bf8cndd3qgxm07o7idul.jpg', 'ativo', 6, 13),
(69, 'Escada Extensível 3 Em 1 Metalon Galvanizado 6 Degraus', '--- CARACTERÍSTICAS ---\nMarca: Cardoso\nLinha: Multifuncional\nCor: Prateado\n\n--- ESPECIFICAÇÕES ---\nProduto com 3 posições.\nVersátil e durável.\nÉ extensível.\nÉ dobrável e conveniente para armazenamento.\nProtege o trabalho com suas sapatas anti-derrapantes.\nContém trava de segurança.\n\n--- DESCRIÇÃO ---\nEscada extensível 3 em 1 Metalon galvanizado com 6 degraus, ideal para uso doméstico e profissional, oferece versatilidade para ser usada em três posições (extensível, tipo pintor/cavalete e em duas partes apoiada na parede), com estrutura em metalon reforçado que garante durabilidade e segurança; suporta até cerca de 120 kg e alcança altura máxima estendida de aproximadamente 3,1 m, facilitando trabalhos de manutenção, pintura e alcance de locais altos, possui sapatas antiderrapantes e trava de segurança para maior estabilidade e é resistente à corrosão, sendo uma solução prática e robusta para suas necessidades em casa ou no trabalho.', 314.15, 29, 22, 95, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896959/loja_ponto_com/produtos/ol2wvggpzmtypar9yrs5.webp', 'ativo', 6, 10),
(70, 'Chaleira Elétrica EEK10', '--- CARACTERÍSTICAS ---\nMateriais: Aço inoxidável\nCom desligamento automático: Sim\nCom função chimarrão: Não\nCom controle de temperatura: Não\n\n--- ESPECIFICAÇÕES ---\nCom capacidade de 1.8 litros.\nCom desligamento automático.\nPossui luz indicadora de funcionamento.\nPossui base giratória.\nTecnologia e velocidade para suas infusões.\n\n--- DESCRIÇÃO ---\nChaleira elétrica Electrolux EEK10 com 1200 W de potência e capacidade de 1,8 litros, ideal para aquecer água rapidamente para chás, cafés e preparo de alimentos no dia a dia. Possui desligamento automático ao atingir fervura e luz indicadora de funcionamento, garantindo mais segurança durante o uso. Seu corpo em aço inox proporciona durabilidade e um visual moderno para a cozinha. Conta com base giratória 360°, tampa com abertura facilitada e alça ergonômica, oferecendo praticidade no manuseio. O design sem fio com porta-cabo torna o uso e o armazenamento mais simples e eficientes.', 129.90, 0, 19, 31, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767897754/loja_ponto_com/produtos/chguxiwaf5wve7xwubec.webp', 'ativo', 6, 15),
(71, 'Produto Teste Cypress 1768588817468', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768588854/loja_ponto_com/produtos/knrdy9zx1clciq1hyx76.png', 'inativo', 16, 999999),
(72, 'Produto Teste Cypress 1768589045756', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768589082/loja_ponto_com/produtos/zg9h1vxapaiqvktwoevm.png', 'inativo', 16, 999999),
(73, 'Produto Teste Cypress 1768589210199', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768589248/loja_ponto_com/produtos/z7suvj21jjlg5y0fiuhj.png', 'inativo', 16, 999999),
(74, 'Produto Teste Cypress 1768589396496', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768589433/loja_ponto_com/produtos/zhjnej1ohm9fueburvws.png', 'inativo', 16, 999999),
(75, 'Produto Teste Cypress 1768589571111', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768589608/loja_ponto_com/produtos/ra6qywjrvhxro1s4azdc.png', 'inativo', 16, 999999),
(76, 'Produto Teste Cypress 1768598828041', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768598866/loja_ponto_com/produtos/d3wxaduqfxphb5nhdvar.png', 'inativo', 16, 999999),
(77, 'Produto Teste Cypress 1768599951066', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768599990/loja_ponto_com/produtos/urzcvd3qcnc95zn0gonc.png', 'inativo', 16, 999999),
(78, 'Produto Teste Cypress 1768601299473', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768601336/loja_ponto_com/produtos/gwuh3hfu7eygbixun3jb.png', 'inativo', 16, 999999),
(79, 'Produto Teste Cypress 1768601650843', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768601688/loja_ponto_com/produtos/gpszxu5ru9u0qidmx8jf.png', 'inativo', 16, 999999),
(80, 'Produto Teste Cypress 1768602690368', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768602727/loja_ponto_com/produtos/rwsovu2mbnrvjwzdg8w3.png', 'inativo', 16, 999999),
(81, 'Produto Teste Cypress 1768603480603', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768603519/loja_ponto_com/produtos/d9g0midxikrplrbaqnbx.png', 'inativo', 16, 999999),
(82, 'Produto Teste Cypress 1768603970780', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768604008/loja_ponto_com/produtos/m848vakmn7bqd6wh696b.png', 'inativo', 16, 999999),
(83, 'Produto Teste Cypress 1768604459650', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768604497/loja_ponto_com/produtos/pxx1agss99meohb6jhor.png', 'inativo', 16, 999999),
(84, 'Produto Teste Cypress 1768605311456', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768605351/loja_ponto_com/produtos/v8ocgugwmnfovchuvzg7.png', 'inativo', 16, 999999),
(85, 'Produto Teste Cypress 1768606298828', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768606336/loja_ponto_com/produtos/npl9pf1gzunk778njoxy.png', 'inativo', 16, 999999),
(86, 'Produto Teste Cypress 1768607762105', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768607799/loja_ponto_com/produtos/h6swwet6vrbs7iotdtne.png', 'inativo', 16, 999999),
(87, 'Produto Teste Cypress 1768609443183', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768609482/loja_ponto_com/produtos/h6wezc0fiebl1qn8qkpq.png', 'inativo', 16, 999999),
(88, 'Produto Teste Cypress 1768778167479', '--- CARACTERÍSTICAS ---\nMarca: Marca Teste\nModelo: Modelo Teste\n\n--- ESPECIFICAÇÕES ---\nEspecificação Teste\n\n--- DESCRIÇÃO ---\nDescrição automática gerada pelo Cypress.', 150.50, 10, 16, 50, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1768778206/loja_ponto_com/produtos/sutbcurdzhy8uxb3voi9.png', 'inativo', 16, 999999);

-- --------------------------------------------------------

--
-- Table structure for table `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int NOT NULL,
  `produto_id` int NOT NULL,
  `url_imagem` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produto_imagens`
--

INSERT INTO `produto_imagens` (`id`, `produto_id`, `url_imagem`) VALUES
(20, 55, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767826981/loja_ponto_com/produtos/qb6ycjqm6cc9cbkr2ilk.webp'),
(21, 55, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767826983/loja_ponto_com/produtos/fgmfsetz4ly0cz4isasg.webp'),
(22, 55, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767826986/loja_ponto_com/produtos/cmjbkype6azczjejkrou.webp'),
(23, 55, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767826987/loja_ponto_com/produtos/law4g7pgi2gbzbugvcno.webp'),
(24, 56, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827662/loja_ponto_com/produtos/ushajak9uelua0xem0ad.webp'),
(25, 56, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827664/loja_ponto_com/produtos/lwxffphl7a7kgp5t24oh.webp'),
(26, 56, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827666/loja_ponto_com/produtos/d9jwadfovf6mmtbqyis9.webp'),
(27, 56, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827668/loja_ponto_com/produtos/sgd6w9bfrqcrvwwodrql.webp'),
(28, 56, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767827671/loja_ponto_com/produtos/pbjlkpciqxnb7x3gzu66.webp'),
(29, 57, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828022/loja_ponto_com/produtos/yxjgtpegkbhk6izqskg8.jpg'),
(30, 57, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828025/loja_ponto_com/produtos/rya0jv2xzk5qzufidnkf.jpg'),
(31, 57, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828026/loja_ponto_com/produtos/suujrbztsokvyovexkst.jpg'),
(32, 57, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828027/loja_ponto_com/produtos/difwscjionrpxrz5hqwb.jpg'),
(33, 57, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828029/loja_ponto_com/produtos/ym72br6xf5yzjncfyagw.jpg'),
(34, 58, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828266/loja_ponto_com/produtos/hew4cmod4ujscb3od0xw.webp'),
(35, 58, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828268/loja_ponto_com/produtos/oratxcpr2hqulykns8bk.webp'),
(36, 58, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828270/loja_ponto_com/produtos/ycpse0sfqpi7qv0ml9jw.webp'),
(37, 58, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828271/loja_ponto_com/produtos/oaz7ttirtvdiucwl1a6k.webp'),
(38, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828489/loja_ponto_com/produtos/mqdt49qcjjcygwgukxui.webp'),
(39, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828491/loja_ponto_com/produtos/o7suxhgs3qkhdtqnepjg.webp'),
(40, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828492/loja_ponto_com/produtos/w7ovzsgv3trwfelacqse.webp'),
(41, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828494/loja_ponto_com/produtos/ydcfonls9f7yfobjcpyh.webp'),
(42, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828495/loja_ponto_com/produtos/cbrezqph37qzfzqgq6go.webp'),
(43, 59, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767828499/loja_ponto_com/produtos/snzxoztgqjhy0ybnoc12.webp'),
(44, 60, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833503/loja_ponto_com/produtos/rcwpqpiakmtcsz9dqwtn.webp'),
(45, 60, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833505/loja_ponto_com/produtos/v2giatbtnh7swtg5vtso.webp'),
(46, 60, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833506/loja_ponto_com/produtos/kmwfiix80k6lnqmp4qhq.webp'),
(47, 60, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833508/loja_ponto_com/produtos/zmqmv1xmamhsmuxasy6r.webp'),
(48, 61, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833720/loja_ponto_com/produtos/rx1rzoy7enaoo7kwsraj.jpg'),
(49, 61, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833721/loja_ponto_com/produtos/pgg6bykvxyjvicl4sahw.jpg'),
(50, 61, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767833722/loja_ponto_com/produtos/l9bdefwc5nz1fgjbwmti.jpg'),
(51, 62, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834111/loja_ponto_com/produtos/gn1naakfbyntvnylzmvy.webp'),
(52, 62, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834113/loja_ponto_com/produtos/fvxzzamwrhfojiuvfi2p.webp'),
(53, 62, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834114/loja_ponto_com/produtos/uc4ku0yzkeg09m3jic0z.webp'),
(54, 62, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834116/loja_ponto_com/produtos/lgynj4sz4dmfhiiwfqih.webp'),
(55, 63, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834930/loja_ponto_com/produtos/l8qi9fyi7njnm16zgvnz.jpg'),
(56, 63, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834932/loja_ponto_com/produtos/mad5evlyh6snuxzmsjb2.jpg'),
(57, 63, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834933/loja_ponto_com/produtos/qkegnmgkudlmnpd71rhc.jpg'),
(58, 63, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767834935/loja_ponto_com/produtos/fwwvew90ftztjgxdldev.jpg'),
(59, 64, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835172/loja_ponto_com/produtos/cpmylopayi2tboj7yvvg.webp'),
(60, 64, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835174/loja_ponto_com/produtos/yep5zogc1mrnbrsofh0n.webp'),
(61, 64, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835175/loja_ponto_com/produtos/bbstlqfaojgnxvxzyfib.webp'),
(62, 65, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835567/loja_ponto_com/produtos/jqahseofyaz62y6ciyn9.jpg'),
(63, 65, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835569/loja_ponto_com/produtos/tq4quihy8z1sosxigfpb.jpg'),
(64, 65, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835571/loja_ponto_com/produtos/d1fct2m0zgndy461pwro.jpg'),
(65, 65, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835572/loja_ponto_com/produtos/onjiexukxgmadtafsukt.jpg'),
(66, 66, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835868/loja_ponto_com/produtos/famg2f6ubzvlpxzqxwed.jpg'),
(67, 66, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835871/loja_ponto_com/produtos/fbixnsnzxi8nqtcjp7fm.jpg'),
(68, 66, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767835873/loja_ponto_com/produtos/ucic5rgnatumzwvzykth.webp'),
(69, 67, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767895187/loja_ponto_com/produtos/nzycmxdkgpmibta0pxwl.jpg'),
(70, 67, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767895191/loja_ponto_com/produtos/dgwufwddezuf3k0payxd.jpg'),
(71, 67, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767895192/loja_ponto_com/produtos/yr0egelb13qmy5xt67aa.jpg'),
(72, 68, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896220/loja_ponto_com/produtos/wsby513yvrleyuukc7m8.jpg'),
(73, 68, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896224/loja_ponto_com/produtos/hsbptaq5k0rhmz32bizt.jpg'),
(74, 68, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896226/loja_ponto_com/produtos/m2htcm2d4yqwwpqx0prk.jpg'),
(75, 69, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896961/loja_ponto_com/produtos/l95darbegvoknd7kenow.webp'),
(76, 69, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896962/loja_ponto_com/produtos/whodydgnhebjvnmjhktw.webp'),
(77, 69, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896963/loja_ponto_com/produtos/f50yqadulrdslgawudfr.webp'),
(78, 69, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767896964/loja_ponto_com/produtos/pejvudbslxnw7qptffy9.webp'),
(79, 70, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767897756/loja_ponto_com/produtos/go7nqywyjjbor6mcrvb7.webp'),
(80, 70, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767897757/loja_ponto_com/produtos/vem0eoxjckrmnstgtnmn.webp'),
(81, 70, 'https://res.cloudinary.com/dp30gyor3/image/upload/v1767897760/loja_ponto_com/produtos/oj60mguk3t8jdem9qsri.webp');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` varchar(20) NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `cpf`, `senha`, `telefone`, `data_cadastro`, `tipo`) VALUES
(3, 'João Silva Teste', 'joao.teste+qa@example.com', '123.456.789-00', '$2y$10$BzliJoZptHJnsCFGKk4ADO8MOXxT89I3LfYgG/QSqC7CXCjLgEfzO', '11 91234-5678', '2025-10-27 19:17:53', 'cliente'),
(4, 'Pedro Souza Fictício', 'pedro.souza+fake@mailinator.com', '111.222.333-44', '$2y$10$b32qw2nn.NS8UfQ47y6creN05WalRd3ydpm9MLVY47ryZNqxn1m.C', '31 91919-1919', '2025-10-28 10:56:46', 'cliente'),
(5, 'Roberto Alves Teste', 'roberto.alves@LojaLTDA.com', '777.888.999-00', '$2y$10$VpEj//DGv.3Yiee1zvtuFe0LjUks7s8h03jGMj.pkWEGyQE3q.gz2', '61 98765-4321', '2025-10-28 23:07:32', 'cliente'),
(6, 'Sandra Gomes Fictícia', 'sandra.gomes@LojaLTDA.com', '707.808.909-00', '$2y$10$87ZxH.N.bJtnM.2Od6txi.Vky0Rs7rzFyU/dV0xa3f.irbaDbymwe', '31 96666-5555', '2025-10-28 23:11:37', 'fornecedor'),
(8, 'fulano', 'fulano@LojaLTDA.com', '123456789', '$2y$10$iPth3cP0OwK65PZ9EGcD6ulvTzwgRH08DrHVLJVhXiJAOyt2CoN1u', '123456789', '2025-11-21 19:13:23', 'fornecedor'),
(9, 'Marcelo', 'marcelo@LojaLTDA.com', '123.145.645-64', '$2y$10$JVZPwoY7emEttqyzDJZuWe/PqxipIDunSyIDvbzgyakwPTQx3jRhC', '(12) 13213-2132', '2025-11-22 02:27:54', 'fornecedor'),
(16, 'AAAA', 'AAAA@LojaLTDA.com', '313.123.212-11', '$2y$10$cFTZEdnAKVvuoLremv5p1eLEmnBBL/MaCkpcR1AQ/UWyhGxG7vv7W', '(21) 31321-3132', '2026-01-07 14:53:58', 'fornecedor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_produto_UNIQUE` (`usuario_id`,`produto_id`),
  ADD KEY `fk_AVALIACOES_USUARIOS_idx` (`usuario_id`),
  ADD KEY `fk_AVALIACOES_PRODUTOS_idx` (`produto_id`);

--
-- Indexes for table `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id_UNIQUE` (`usuario_id`);

--
-- Indexes for table `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carrinho_produto_UNIQUE` (`carrinho_id`,`produto_id`),
  ADD KEY `fk_CARRINHO_ITENS_CARRINHO_idx` (`carrinho_id`),
  ADD KEY `fk_CARRINHO_ITENS_PRODUTOS_idx` (`produto_id`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_UNIQUE` (`nome`);

--
-- Indexes for table `conversas`
--
ALTER TABLE `conversas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comprador_id` (`comprador_id`),
  ADD KEY `fornecedor_id` (`fornecedor_id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Indexes for table `cupom_uso`
--
ALTER TABLE `cupom_uso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cupom_id` (`cupom_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ENDERECOS_USUARIOS_idx` (`usuario_id`);

--
-- Indexes for table `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pedido_id_UNIQUE` (`pedido_id`);

--
-- Indexes for table `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversa_id` (`conversa_id`),
  ADD KEY `remetente_id` (`remetente_id`);

--
-- Indexes for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_NOTIFICACOES_USUARIOS_idx` (`usuario_id`);

--
-- Indexes for table `order_events`
--
ALTER TABLE `order_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `order_issues`
--
ALTER TABLE `order_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_PAGAMENTOS_PEDIDOS_idx` (`pedido_id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_PEDIDOS_USUARIOS_idx` (`usuario_id`),
  ADD KEY `fk_PEDIDOS_ENDERECOS_idx` (`endereco_id`),
  ADD KEY `fk_pedidos_cupom` (`cupom_id`);

--
-- Indexes for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_PEDIDO_ITENS_PEDIDOS_idx` (`pedido_id`),
  ADD KEY `fk_PEDIDO_ITENS_PRODUTOS_idx` (`produto_id`);

--
-- Indexes for table `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_PRODUTOS_CATEGORIAS_idx` (`categoria_id`),
  ADD KEY `fk_PRODUTOS_USUARIOS` (`usuario_id`);

--
-- Indexes for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_IMAGENS_PRODUTOS_idx` (`produto_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `cpf_UNIQUE` (`cpf`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `conversas`
--
ALTER TABLE `conversas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `cupom_uso`
--
ALTER TABLE `cupom_uso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `order_events`
--
ALTER TABLE `order_events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_issues`
--
ALTER TABLE `order_issues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_AVALIACOES_PRODUTOS` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_AVALIACOES_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_CARRINHO_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `fk_CARRINHO_ITENS_CARRINHO` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinho` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_CARRINHO_ITENS_PRODUTOS` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversas`
--
ALTER TABLE `conversas`
  ADD CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`comprador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversas_ibfk_2` FOREIGN KEY (`fornecedor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversas_ibfk_3` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conversas_ibfk_4` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cupom_uso`
--
ALTER TABLE `cupom_uso`
  ADD CONSTRAINT `cupom_uso_ibfk_1` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cupom_uso_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cupons`
--
ALTER TABLE `cupons`
  ADD CONSTRAINT `cupons_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `fk_ENDERECOS_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `fk_ENTREGAS_PEDIDOS` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_NOTIFICACOES_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_events`
--
ALTER TABLE `order_events`
  ADD CONSTRAINT `order_events_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_issues`
--
ALTER TABLE `order_issues`
  ADD CONSTRAINT `order_issues_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_PAGAMENTOS_PEDIDOS` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`),
  ADD CONSTRAINT `fk_PEDIDOS_ENDERECOS` FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PEDIDOS_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `fk_PEDIDO_ITENS_PEDIDOS` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PEDIDO_ITENS_PRODUTOS` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_PRODUTOS_CATEGORIAS` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PRODUTOS_USUARIOS` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `fk_IMAGENS_PRODUTOS` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
