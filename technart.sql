-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Set-2022 às 04:32
-- Versão do servidor: 10.4.24-MariaDB
-- versão do PHP: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `technart`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `email`, `password`) VALUES
(1, 'Admin_teste', 'admin@admin.pt', 'aa1bf4646de67fd9086cf6c79007026c'),
(7, 'Admin2', 'admin2@admin.com', '09151a42659cfc08aff86820f973f640');

-- --------------------------------------------------------

--
-- Estrutura da tabela `investigadores`
--

CREATE TABLE `investigadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ciencia_id` varchar(100) NOT NULL,
  `sobre` mediumtext NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `fotografia` varchar(100) NOT NULL,
  `areasdeinteresse` mediumtext NOT NULL,
  `orcid` varchar(255) NOT NULL,
  `scholar` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ultimologin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `investigadores`
--

INSERT INTO `investigadores` (`id`, `nome`, `email`, `ciencia_id`, `sobre`, `tipo`, `fotografia`, `areasdeinteresse`, `orcid`, `scholar`, `password`, `ultimologin`) VALUES
(11, 'Joana', 'R.micaela@live.com.pt', '0C1F-9648-2A48', 'miniin inininii ununun ininini', 'Integrado', 'FotoRapariga.png', 'xcvbnmuexrcfvgbhnjmkrxrcvgbhnjm', 'https://noticias.uc.pt/artigos/estudo-da-universidade-de-coimbra-aponta-desigualdades-sociais-no-acesso-a-ciclovias-e-sistema-de-bicicletas-partilhadas-de/', 'https://www.uc.pt/estudantes', '698dc19d489c4e4db73e28a713eab07b', NULL),
(12, 'Berto', 'berto_bertinho@gmail.com', '2A13-632C-D743', 'a', 'Integrado', '480006581462697f48b6ff44be2ea3d141def7edr1-334-441v2_uhq.jpg', '', '', '', '', NULL),
(19, 'Marta', 'marta@hotmail.com', '2A13-632C-D743', 'nao', 'Aluno', 'o_exorcista_remake.jpg', 'Ciências da natureza', 'https://noticias.uc.pt/artigos/estudo-da-universidade-de-coimbra-aponta-desigualdades-sociais-no-acesso-a-ciclovias-e-sistema-de-bicicletas-partilhadas-de/', 'https://www.uc.pt/estudantes', 'a763a66f984948ca463b081bf0f0e6d0', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `investigadores_projetos`
--

CREATE TABLE `investigadores_projetos` (
  `investigadores_id` int(11) NOT NULL,
  `projetos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `investigadores_projetos`
--

INSERT INTO `investigadores_projetos` (`investigadores_id`, `projetos_id`) VALUES
(11, 23),
(11, 24),
(12, 20),
(12, 24),
(12, 25),
(12, 26),
(19, 21),
(19, 26);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projetos`
--

CREATE TABLE `projetos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` mediumtext NOT NULL,
  `sobreprojeto` mediumtext NOT NULL,
  `referencia` varchar(100) NOT NULL,
  `areapreferencial` varchar(255) NOT NULL,
  `financiamento` varchar(20) NOT NULL,
  `ambito` varchar(100) NOT NULL,
  `fotografia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `projetos`
--

INSERT INTO `projetos` (`id`, `nome`, `descricao`, `sobreprojeto`, `referencia`, `areapreferencial`, `financiamento`, `ambito`, `fotografia`) VALUES
(20, 'Figura', 'Integer a erat massa. Nunc sed pretium nulla. Donec tempor velit dui, sagittis gravida erat hendrerit ut.', 'A história de todos sobre o euismod. Diz-se que viveu nesta rua. Até os ultricies na urna, a massa da garganta, mas sempre diam. Fãs de futebol ao vivo, suaves ou justos, ele quer colocá-lo no ringue.', '543212345', 'Maecenas', '4321€', 'Phasellus', 'P1310166.JPG'),
(21, 'INSIGNIA', 'Morbi mauris sem, convallis ut commodo quis, consequat ac velit.', 'Phasellus dapibus eros vel fringilla ullamcorper. Donec sit amet tempor neque, sit amet facilisis ligula. Fusce eget lacinia lectus. Morbi laoreet auctor vehicula. Cras eget semper sem.', '123456789', 'Aliquam', '3333€', 'Vivamus', 'Castelo de Abrantes1.jpg'),
(23, 'FesTab', 'Proin blandit sagittis dolor quis porttitor. Phasellus tortor felis, eleifend at nisi ac, pulvinar malesuada lectus.', 'Nunc non justo vel mauris semper rutrum. Curabitur at feugiat felis, nec cursus leo. Vivamus euismod sollicitudin tempor. Nunc non augue diam. Mauris rutrum, lorem a pellentesque finibus, tellus ante vulputate elit, at venenatis lorem nunc nec risus.', '123454321', 'Scelerisque', '1234€', 'Phasellus', 'IMG_6431.JPG'),
(24, 'MurArte', 'Donec in urna ultricies, faucibus massa sed, semper diam.', 'Maecenas auctor semper metus consectetur malesuada. Phasellus feugiat tellus tellus, eu convallis turpis malesuada id. Nullam ac laoreet neque, sed euismod augue.', '123456789', 'Fringilla', '3214€', 'Volutpat', '5c.JPG'),
(25, 'NATBIO', 'Quisque pellentesque euismod condimentum. In hac habitasse platea dictumst.', 'Donec in urna ultricies, faucibus massa sed, semper diam. Vivamus turpis nisl, mollis vel justo consectetur, fringilla posuere velit. Maecenas auctor semper metus consectetur malesuada.', '654321789', 'Pellentesque', '7453€', 'Porttitor', '20220208_103402.jpg'),
(26, 'POR1FIO', 'Nullam ac laoreet neque, sed euismod augue.', 'Nunc non justo vel mauris semper rutrum. Curabitur at feugiat felis, nec cursus leo. Vivamus euismod sollicitudin tempor. Nunc non augue diam.', '56787654', 'Consectetur', '4760€', 'Lacinia', 'DSC_0010.JPG');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `investigadores`
--
ALTER TABLE `investigadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `investigadores_projetos`
--
ALTER TABLE `investigadores_projetos`
  ADD PRIMARY KEY (`investigadores_id`,`projetos_id`),
  ADD KEY `projetos_id` (`projetos_id`);

--
-- Índices para tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `investigadores`
--
ALTER TABLE `investigadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `investigadores_projetos`
--
ALTER TABLE `investigadores_projetos`
  ADD CONSTRAINT `investigadores_projetos_ibfk_1` FOREIGN KEY (`investigadores_id`) REFERENCES `investigadores` (`id`),
  ADD CONSTRAINT `investigadores_projetos_ibfk_2` FOREIGN KEY (`projetos_id`) REFERENCES `projetos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;