-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: hidrantes_db
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bairros`
--

DROP TABLE IF EXISTS `bairros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bairros` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `municipio_id` bigint unsigned NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_ibge` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `geojson_referencia` longtext COLLATE utf8mb4_unicode_ci,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bairros_municipio_nome` (`municipio_id`,`nome`),
  UNIQUE KEY `uq_bairros_codigo_ibge` (`codigo_ibge`),
  KEY `idx_bairros_nome` (`nome`),
  KEY `idx_bairros_ativo` (`ativo`),
  CONSTRAINT `fk_bairros_municipio` FOREIGN KEY (`municipio_id`) REFERENCES `municipios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bairros`
--

LOCK TABLES `bairros` WRITE;
/*!40000 ALTER TABLE `bairros` DISABLE KEYS */;
INSERT INTO `bairros` VALUES (1,1501402,'Centro',NULL,NULL,1),(2,1501402,'Benguí',NULL,NULL,1),(3,1501402,'Jurunas',NULL,NULL,1),(4,1501402,'Pedreira',NULL,NULL,1),(5,1501402,'Marco',NULL,NULL,1),(6,1501402,'Umarizal',NULL,NULL,1),(7,1501402,'Batista Campos',NULL,NULL,1),(8,1501402,'Nazaré',NULL,NULL,1),(9,1501402,'Cremação',NULL,NULL,1),(10,1501402,'Sacramenta',NULL,NULL,1),(11,1500800,'Coqueiro',NULL,NULL,1),(12,1500800,'Cidade Nova',NULL,NULL,1),(13,1500800,'Paar',NULL,NULL,1),(14,1504422,'Almir Gabriel',NULL,NULL,1);
/*!40000 ALTER TABLE `bairros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hidrantes`
--

DROP TABLE IF EXISTS `hidrantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hidrantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_hidrante` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipe_responsavel` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` enum('urbano','industrial','rural') COLLATE utf8mb4_unicode_ci NOT NULL,
  `existe_no_local` enum('sim','nao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_hidrante` enum('coluna','subterraneo','parede','outro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `acessibilidade` enum('sim','nao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tampo_conexoes` enum('integra','danificadas','ausentes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tampas_ausentes` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caixa_protecao` enum('sim','nao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicao_caixa` enum('boa','regular','ruim') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presenca_agua_interior` enum('sim','nao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `teste_realizado` enum('sim','nao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultado_teste` enum('funcionando normalmente','vazamento','vazao insuficiente','nao funcionou') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_operacional` enum('operante','operante com restricao','inoperante') COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipio_id` bigint unsigned NOT NULL,
  `bairro_id` bigint unsigned DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `foto_01` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_02` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_03` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `criado_por_usuario_id` bigint unsigned DEFAULT NULL,
  `atualizado_por_usuario_id` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_por_usuario_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_hidrantes_numero` (`numero_hidrante`),
  KEY `fk_hidrantes_criado_por` (`criado_por_usuario_id`),
  KEY `fk_hidrantes_atualizado_por` (`atualizado_por_usuario_id`),
  KEY `idx_hidrantes_status_operacional` (`status_operacional`),
  KEY `idx_hidrantes_municipio_id` (`municipio_id`),
  KEY `idx_hidrantes_bairro_id` (`bairro_id`),
  KEY `idx_hidrantes_tipo_hidrante` (`tipo_hidrante`),
  KEY `idx_hidrantes_area` (`area`),
  KEY `idx_hidrantes_atualizado_em` (`atualizado_em`),
  KEY `idx_hidrantes_municipio_bairro` (`municipio_id`,`bairro_id`),
  KEY `idx_hidrantes_status_municipio` (`status_operacional`,`municipio_id`),
  KEY `fk_hidrantes_deleted_por` (`deleted_por_usuario_id`),
  KEY `idx_hidrantes_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_hidrantes_atualizado_por` FOREIGN KEY (`atualizado_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hidrantes_bairro` FOREIGN KEY (`bairro_id`) REFERENCES `bairros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hidrantes_criado_por` FOREIGN KEY (`criado_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hidrantes_deleted_por` FOREIGN KEY (`deleted_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hidrantes_municipio` FOREIGN KEY (`municipio_id`) REFERENCES `municipios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_hidrantes_latitude` CHECK (((`latitude` is null) or ((`latitude` >= -(90.0000000)) and (`latitude` <= 90.0000000)))),
  CONSTRAINT `chk_hidrantes_longitude` CHECK (((`longitude` is null) or ((`longitude` >= -(180.0000000)) and (`longitude` <= 180.0000000)))),
  CONSTRAINT `chk_hidrantes_resultado_teste` CHECK (((`teste_realizado` = _utf8mb4'sim') or ((`teste_realizado` = _utf8mb4'nao') and (`resultado_teste` is null))))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hidrantes`
--

LOCK TABLES `hidrantes` WRITE;
/*!40000 ALTER TABLE `hidrantes` DISABLE KEYS */;
INSERT INTO `hidrantes` VALUES (1,'HDT-TESTE-001','Equipe CEDEC Teste','urbano','sim','coluna','sim','danificadas','lateral','sim','regular','sim','sim','funcionando normalmente','operante com restricao',1501402,9,'Comando Geral do Corpo de Bombeiros Militar do Estado do Pará',-1.4560404,-48.5009885,'hidrante_69bdfb312a3590.09348752.png',NULL,NULL,'2026-03-20 22:55:54','2026-03-20 23:09:33',1,1,'2026-03-20 23:09:33',1),(2,'HDT-TESTE-002','Equipe CEDEC Teste','urbano','sim','coluna','sim','integra',NULL,'sim','boa','sim','sim','funcionando normalmente','operante com restricao',1501402,10,'Departamento de Transito do Pará - DETRAN',-1.3731216,-48.4399529,NULL,NULL,NULL,'2026-03-20 23:31:30','2026-03-20 23:35:36',1,1,'2026-03-20 23:35:36',1),(3,'HDT-TESTE-003','Equipe CEDEC Teste','rural','sim','coluna','sim','integra',NULL,'sim','regular','sim','sim','nao funcionou','inoperante',1501402,1,'Aldeia Amazônica – Av. Pedro Miranda s/n',-1.4224623,-48.4685257,'hidrante_238bcb1e3af6b950e86372c26a52a90b.png','hidrante_44db363fcb22c21b878e4f33eae28434.jpg','hidrante_0822a87ec094a0a5b7682080a85f4c3f.jpg','2026-03-20 23:32:53','2026-03-22 01:11:53',1,2,NULL,NULL),(4,'HDT-TESTE-004','Equipe CEDEC Teste','urbano','sim','coluna','sim','integra',NULL,'sim','regular','sim','sim','funcionando normalmente','operante',1501402,10,'praia grande de Outeiro-pa',-1.2555478,-48.4685751,'hidrante_8f64db27023fa744b2b968de762769df.jpg',NULL,NULL,'2026-03-20 23:34:38','2026-03-21 16:02:20',1,2,NULL,NULL),(5,'HDT-TESTE-005','Equipe CBMPA Teste','urbano','sim','coluna','sim','integra','cima','sim','regular','sim','sim','vazao insuficiente','operante com restricao',1501402,1,'R. João Diogo, 236 - Campina, Belém - PA, 66015-160',-1.4560297,-48.5010422,NULL,NULL,NULL,'2026-03-21 16:20:06','2026-03-22 01:10:32',2,2,NULL,NULL),(6,'HDT-TESTE-006','Equipe CEDEC Teste','urbano','sim','coluna','sim','integra',NULL,'sim','boa','sim','sim','vazao insuficiente','operante',1501402,2,'R. João Diogo, 236 - Campina, Belém - PA, 66015-160',-1.4560404,-48.4399529,'hidrante_4687010b70fada462db86b757abd86a7.jpg','hidrante_4c8bc0e258bd21f71abfe09ea552cc2b.jpg','hidrante_b189ee4f7ac73ba3529c3001cd827656.jpg','2026-03-21 17:16:46','2026-03-22 00:50:02',1,2,NULL,NULL),(7,'HDT-TESTE-007','Equipe CBMPA Teste','urbano','sim','subterraneo','sim','integra','não','sim','boa','sim','sim','funcionando normalmente','operante',1500800,12,'Conjunto Cidade Nova VII - Tv. SN 24, s/n - Coqueiro, Ananindeua - PA, 67140-500',-1.3498252,-48.4031278,'hidrante_79e67c1908e9f56b4a2d2dceffd5db18.jpg','hidrante_cb81b43d6e9b60f85a5da415b22d5405.jpg',NULL,'2026-03-21 18:24:39','2026-03-22 01:09:42',1,2,NULL,NULL),(8,'HDT-TESTE-008','Equipe CEDEC Teste','urbano','sim','coluna','sim','integra',NULL,'sim',NULL,'sim','sim','funcionando normalmente','operante',1500800,11,'passagem murnbi n° 05',-1.3797448,-48.4224599,NULL,NULL,NULL,'2026-03-22 00:34:11','2026-03-22 00:49:23',2,2,'2026-03-22 00:49:23',2),(11,'HDT-TESTE-009','Equipe CEDEC Teste','industrial','sim','coluna','sim','integra',NULL,'sim','boa','sim','sim','vazao insuficiente','operante',1500800,11,'rua murubi n° 05',-1.3797645,-48.4224412,'hidrante_d13edfa7d4afa3fdba20a6b584daf926.jpg','hidrante_00a7972021ade26e20de2df98492812a.jpg','hidrante_2a6d409b58eaf245a9254ec304774029.jpg','2026-03-22 01:13:31','2026-03-22 01:14:38',2,2,NULL,NULL),(12,'HDT-TESTE-010','Equipe CEDEC Teste','urbano','sim','coluna','sim','integra','lateral, cima','sim','regular','sim','sim','vazao insuficiente','operante com restricao',1500800,13,'Entre o Canteiro do Paar e o Posto de Saúde do Paar - Av. Rio Solimões, 36 - Qd 81 - Paar, Ananindeua - PA, 67145-895',-1.3355658,-48.3875542,'hidrante_c9ecf903f6f8a5d2b5cd57bda70b53a8.jpg','hidrante_a3c126debb528716d1a29ea9a914c81b.jpg','hidrante_8d62f9e9420d01941e76c6d440f8ac31.jpg','2026-03-23 08:57:22','2026-03-23 08:57:22',1,1,NULL,NULL),(13,'HDT-TESTE-011','Equipe CEDEC Teste','urbano','sim','coluna','sim','ausentes','Direita, Esquerda, Central','sim','regular','sim','sim','nao funcionou','inoperante',1504422,14,'R. do Fio, 708 - Centro, Marituba - PA, 67105-290',-1.3599553,-48.3330744,'hidrante_ccb08d3d4b4bd5e1ec194dab9d078d58.jpg',NULL,NULL,'2026-03-23 10:08:10','2026-03-23 10:08:10',1,1,NULL,NULL);
/*!40000 ALTER TABLE `hidrantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico_usuario`
--

DROP TABLE IF EXISTS `historico_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historico_usuario` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_acao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` bigint unsigned NOT NULL,
  `usuario_nome_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acao` enum('login','logout','cadastrar','editar','deletar','baixar csv','alterar senha','ativar','inativar','gerar relatorio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_registro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalhes` text COLLATE utf8mb4_unicode_ci,
  `ip_origem` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_historico_usuario_usuario_id` (`usuario_id`),
  KEY `idx_historico_usuario_acao` (`acao`),
  KEY `idx_historico_usuario_data_acao` (`data_acao`),
  KEY `idx_historico_usuario_usuario_acao` (`usuario_id`,`acao`),
  CONSTRAINT `fk_historico_usuario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_usuario`
--

LOCK TABLES `historico_usuario` WRITE;
/*!40000 ALTER TABLE `historico_usuario` DISABLE KEYS */;
INSERT INTO `historico_usuario` VALUES (1,'2026-03-19 17:49:47',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(2,'2026-03-19 17:56:10',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(3,'2026-03-19 17:56:22',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(4,'2026-03-19 18:05:08',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(5,'2026-03-20 20:29:18',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(6,'2026-03-20 20:40:45',1,'Administrador do Sistema','cadastrar','usuarios','2','Cadastro de usuário realizado.','127.0.0.1'),(7,'2026-03-20 20:40:59',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(8,'2026-03-20 20:41:07',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(9,'2026-03-20 20:44:50',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(10,'2026-03-20 20:44:58',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(11,'2026-03-20 22:49:28',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(12,'2026-03-20 22:55:54',1,'Administrador do Sistema','cadastrar','hidrantes','1','Cadastro de hidrante realizado.','127.0.0.1'),(13,'2026-03-20 22:57:30',1,'Administrador do Sistema','editar','hidrantes','1','Edição de hidrante realizada.','127.0.0.1'),(14,'2026-03-20 22:58:09',1,'Administrador do Sistema','editar','hidrantes','1','Edição de hidrante realizada.','127.0.0.1'),(15,'2026-03-20 23:00:34',1,'Administrador do Sistema','editar','hidrantes','1','Edição de hidrante realizada.','127.0.0.1'),(16,'2026-03-20 23:09:34',1,'Administrador do Sistema','deletar','hidrantes','1','Exclusão lógica de hidrante realizada.','127.0.0.1'),(17,'2026-03-20 23:31:30',1,'Administrador do Sistema','cadastrar','hidrantes','2','Cadastro de hidrante realizado.','127.0.0.1'),(18,'2026-03-20 23:31:50',1,'Administrador do Sistema','editar','hidrantes','2','Edição de hidrante realizada.','127.0.0.1'),(19,'2026-03-20 23:32:53',1,'Administrador do Sistema','cadastrar','hidrantes','3','Cadastro de hidrante realizado.','127.0.0.1'),(20,'2026-03-20 23:34:38',1,'Administrador do Sistema','cadastrar','hidrantes','4','Cadastro de hidrante realizado.','127.0.0.1'),(21,'2026-03-20 23:35:23',1,'Administrador do Sistema','editar','hidrantes','2','Edição de hidrante realizada.','127.0.0.1'),(22,'2026-03-20 23:35:36',1,'Administrador do Sistema','deletar','hidrantes','2','Exclusão lógica de hidrante realizada.','127.0.0.1'),(23,'2026-03-20 23:55:25',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(24,'2026-03-20 23:55:33',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(25,'2026-03-20 23:56:02',2,'Paula Fernanda Correa Lima','editar','hidrantes','4','Edição de hidrante realizada.','127.0.0.1'),(26,'2026-03-21 00:02:04',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(27,'2026-03-21 00:02:11',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(28,'2026-03-21 00:20:39',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(29,'2026-03-21 00:20:48',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(30,'2026-03-21 01:31:10',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(31,'2026-03-21 01:31:30',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(32,'2026-03-21 02:10:19',2,'Paula Fernanda Correa Lima','editar','hidrantes','4','Edição de hidrante realizada.','127.0.0.1'),(33,'2026-03-21 02:10:57',2,'Paula Fernanda Correa Lima','editar','hidrantes','4','Edição de hidrante realizada.','127.0.0.1'),(34,'2026-03-21 02:20:37',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(35,'2026-03-21 09:39:45',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(36,'2026-03-21 15:19:24',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(37,'2026-03-21 15:59:59',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(38,'2026-03-21 16:00:08',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(39,'2026-03-21 16:00:55',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(40,'2026-03-21 16:01:22',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(41,'2026-03-21 16:02:20',2,'Paula Fernanda Correa Lima','editar','hidrantes','4','Edicao de hidrante realizada.','127.0.0.1'),(42,'2026-03-21 16:20:07',2,'Paula Fernanda Correa Lima','cadastrar','hidrantes','5','Cadastro de hidrante realizado.','127.0.0.1'),(43,'2026-03-21 16:36:51',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(44,'2026-03-21 16:37:00',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(45,'2026-03-21 17:05:32',1,'Administrador do Sistema','editar','usuarios','2','Edicao de usuario realizada.','127.0.0.1'),(46,'2026-03-21 17:06:21',1,'Administrador do Sistema','alterar senha','usuarios','2','Senha do usuario alterada.','127.0.0.1'),(47,'2026-03-21 17:06:36',1,'Administrador do Sistema','inativar','usuarios','2','Usuario inativado.','127.0.0.1'),(48,'2026-03-21 17:06:50',1,'Administrador do Sistema','ativar','usuarios','2','Usuario ativado.','127.0.0.1'),(49,'2026-03-21 17:16:46',1,'Administrador do Sistema','cadastrar','hidrantes','6','Cadastro de hidrante realizado.','127.0.0.1'),(50,'2026-03-21 17:21:10',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(51,'2026-03-21 17:21:21',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(52,'2026-03-21 17:32:34',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(53,'2026-03-21 17:32:43',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(54,'2026-03-21 17:38:56',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(55,'2026-03-21 17:39:11',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(56,'2026-03-21 17:40:11',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(57,'2026-03-21 17:40:19',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(58,'2026-03-21 17:49:26',1,'Administrador do Sistema','editar','bairros','2','Edicao de bairro realizada. Nome anterior: Guamá. Nome atual: Benguí. Municipio: Belém.','127.0.0.1'),(59,'2026-03-21 17:50:50',1,'Administrador do Sistema','editar','hidrantes','6','Edicao de hidrante realizada.','127.0.0.1'),(60,'2026-03-21 18:21:38',1,'Administrador do Sistema','cadastrar','bairros','12','Cadastro de bairro Cidade Nova vinculado ao municipio Ananindeua.','127.0.0.1'),(61,'2026-03-21 18:24:39',1,'Administrador do Sistema','cadastrar','hidrantes','7','Cadastro de hidrante realizado.','127.0.0.1'),(62,'2026-03-21 19:29:37',1,'Administrador do Sistema','inativar','usuarios','2','Usuario inativado.','127.0.0.1'),(63,'2026-03-21 19:29:41',1,'Administrador do Sistema','ativar','usuarios','2','Usuario ativado.','127.0.0.1'),(64,'2026-03-21 19:29:48',1,'Administrador do Sistema','editar','usuarios','2','Edicao de usuario realizada.','127.0.0.1'),(65,'2026-03-21 19:41:31',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(66,'2026-03-21 19:59:29',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(67,'2026-03-21 19:59:47',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(68,'2026-03-21 21:42:29',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(69,'2026-03-21 21:51:35',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(70,'2026-03-21 22:02:28',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(71,'2026-03-21 22:16:36',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(72,'2026-03-21 22:29:25',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(73,'2026-03-21 23:28:41',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(74,'2026-03-21 23:37:17',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(75,'2026-03-21 23:44:55',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(76,'2026-03-22 00:16:12',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(77,'2026-03-22 00:21:07',2,'Paula Fernanda Correa Lima','login','usuarios','2','Login realizado com sucesso.','127.0.0.1'),(78,'2026-03-22 00:34:11',2,'Paula Fernanda Correa Lima','cadastrar','hidrantes','8','Cadastro de hidrante realizado.','127.0.0.1'),(79,'2026-03-22 00:35:30',2,'Paula Fernanda Correa Lima','editar','hidrantes','8','Edicao de hidrante realizada.','127.0.0.1'),(80,'2026-03-22 00:37:54',2,'Paula Fernanda Correa Lima','editar','hidrantes','6','Edicao de hidrante realizada.','127.0.0.1'),(81,'2026-03-22 00:47:12',2,'Paula Fernanda Correa Lima','editar','hidrantes','8','Edicao de hidrante realizada.','127.0.0.1'),(82,'2026-03-22 00:48:28',2,'Paula Fernanda Correa Lima','editar','hidrantes','8','Edicao de hidrante realizada.','127.0.0.1'),(83,'2026-03-22 00:49:24',2,'Paula Fernanda Correa Lima','deletar','hidrantes','8','Exclusao logica de hidrante realizada.','127.0.0.1'),(84,'2026-03-22 00:50:02',2,'Paula Fernanda Correa Lima','editar','hidrantes','6','Edicao de hidrante realizada.','127.0.0.1'),(85,'2026-03-22 00:54:30',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(86,'2026-03-22 00:55:06',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(87,'2026-03-22 01:02:52',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(88,'2026-03-22 01:03:54',1,'Administrador do Sistema','cadastrar','hidrantes','9','Cadastro de hidrante realizado.','127.0.0.1'),(89,'2026-03-22 01:04:38',1,'Administrador do Sistema','editar','hidrantes','9','Edicao de hidrante realizada.','127.0.0.1'),(90,'2026-03-22 01:06:21',1,'Administrador do Sistema','cadastrar','hidrantes','10','Cadastro de hidrante realizado.','127.0.0.1'),(91,'2026-03-22 01:09:19',2,'Paula Fernanda Correa Lima','editar','hidrantes','7','Edicao de hidrante realizada.','127.0.0.1'),(92,'2026-03-22 01:09:42',2,'Paula Fernanda Correa Lima','editar','hidrantes','7','Edicao de hidrante realizada.','127.0.0.1'),(93,'2026-03-22 01:10:11',2,'Paula Fernanda Correa Lima','editar','hidrantes','5','Edicao de hidrante realizada.','127.0.0.1'),(94,'2026-03-22 01:10:32',2,'Paula Fernanda Correa Lima','editar','hidrantes','5','Edicao de hidrante realizada.','127.0.0.1'),(95,'2026-03-22 01:11:03',2,'Paula Fernanda Correa Lima','editar','hidrantes','3','Edicao de hidrante realizada.','127.0.0.1'),(96,'2026-03-22 01:11:25',2,'Paula Fernanda Correa Lima','editar','hidrantes','3','Edicao de hidrante realizada.','127.0.0.1'),(97,'2026-03-22 01:11:53',2,'Paula Fernanda Correa Lima','editar','hidrantes','3','Edicao de hidrante realizada.','127.0.0.1'),(98,'2026-03-22 01:13:31',2,'Paula Fernanda Correa Lima','cadastrar','hidrantes','11','Cadastro de hidrante realizado.','127.0.0.1'),(99,'2026-03-22 01:14:18',2,'Paula Fernanda Correa Lima','editar','hidrantes','11','Edicao de hidrante realizada.','127.0.0.1'),(100,'2026-03-22 01:14:38',2,'Paula Fernanda Correa Lima','editar','hidrantes','11','Edicao de hidrante realizada.','127.0.0.1'),(101,'2026-03-22 01:15:09',2,'Paula Fernanda Correa Lima','logout','usuarios','2','Logout realizado com sucesso.','127.0.0.1'),(102,'2026-03-22 17:34:25',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(103,'2026-03-22 18:28:30',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(104,'2026-03-22 19:27:56',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(105,'2026-03-22 20:47:55',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(106,'2026-03-22 20:48:13',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(107,'2026-03-23 07:55:46',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(108,'2026-03-23 08:13:13',1,'Administrador do Sistema','cadastrar','bairros','13','Cadastro de bairro Paar vinculado ao municipio Ananindeua.','127.0.0.1'),(109,'2026-03-23 08:18:18',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(110,'2026-03-23 08:18:28',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(111,'2026-03-23 08:55:48',1,'Administrador do Sistema','logout','usuarios','1','Logout realizado com sucesso.','127.0.0.1'),(112,'2026-03-23 08:55:59',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(113,'2026-03-23 08:57:22',1,'Administrador do Sistema','cadastrar','hidrantes','12','Cadastro de hidrante realizado.','127.0.0.1'),(114,'2026-03-23 10:07:07',1,'Administrador do Sistema','cadastrar','bairros','14','Cadastro de bairro Almir Gabriel vinculado ao municipio Marituba.','127.0.0.1'),(115,'2026-03-23 10:08:10',1,'Administrador do Sistema','cadastrar','hidrantes','13','Cadastro de hidrante realizado.','127.0.0.1'),(116,'2026-03-23 10:11:39',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1'),(117,'2026-03-23 10:12:06',1,'Administrador do Sistema','login','usuarios','1','Login realizado com sucesso.','127.0.0.1');
/*!40000 ALTER TABLE `historico_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipios`
--

DROP TABLE IF EXISTS `municipios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_ibge` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uf` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PA',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_municipios_nome` (`nome`),
  UNIQUE KEY `uq_municipios_codigo_ibge` (`codigo_ibge`),
  KEY `idx_municipios_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=1508408 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipios`
--

LOCK TABLES `municipios` WRITE;
/*!40000 ALTER TABLE `municipios` DISABLE KEYS */;
INSERT INTO `municipios` VALUES (1500107,'Abaetetuba',NULL,'PA',1),(1500131,'Abel Figueiredo',NULL,'PA',1),(1500206,'Acará',NULL,'PA',1),(1500305,'Afuá',NULL,'PA',1),(1500347,'Água Azul do Norte',NULL,'PA',1),(1500404,'Alenquer',NULL,'PA',1),(1500503,'Almeirim',NULL,'PA',1),(1500602,'Altamira',NULL,'PA',1),(1500701,'Anajás',NULL,'PA',1),(1500800,'Ananindeua',NULL,'PA',1),(1500859,'Anapu',NULL,'PA',1),(1500909,'Augusto Corrêa',NULL,'PA',1),(1500958,'Aurora do Pará',NULL,'PA',1),(1501006,'Aveiro',NULL,'PA',1),(1501105,'Bagre',NULL,'PA',1),(1501204,'Baião',NULL,'PA',1),(1501253,'Bannach',NULL,'PA',1),(1501303,'Barcarena',NULL,'PA',1),(1501402,'Belém',NULL,'PA',1),(1501451,'Belterra',NULL,'PA',1),(1501501,'Benevides',NULL,'PA',1),(1501576,'Bom Jesus do Tocantins',NULL,'PA',1),(1501600,'Bonito',NULL,'PA',1),(1501709,'Bragança',NULL,'PA',1),(1501725,'Brasil Novo',NULL,'PA',1),(1501758,'Brejo Grande do Araguaia',NULL,'PA',1),(1501782,'Breu Branco',NULL,'PA',1),(1501808,'Breves',NULL,'PA',1),(1501907,'Bujaru',NULL,'PA',1),(1501956,'Cachoeira do Piriá',NULL,'PA',1),(1502004,'Cachoeira do Arari',NULL,'PA',1),(1502103,'Cametá',NULL,'PA',1),(1502152,'Canaã dos Carajás',NULL,'PA',1),(1502202,'Capanema',NULL,'PA',1),(1502301,'Capitão Poço',NULL,'PA',1),(1502400,'Castanhal',NULL,'PA',1),(1502509,'Chaves',NULL,'PA',1),(1502608,'Colares',NULL,'PA',1),(1502707,'Conceição do Araguaia',NULL,'PA',1),(1502756,'Concórdia do Pará',NULL,'PA',1),(1502764,'Cumaru do Norte',NULL,'PA',1),(1502772,'Curionópolis',NULL,'PA',1),(1502806,'Curralinho',NULL,'PA',1),(1502855,'Curuá',NULL,'PA',1),(1502905,'Curuçá',NULL,'PA',1),(1502939,'Dom Eliseu',NULL,'PA',1),(1502954,'Eldorado dos Carajás',NULL,'PA',1),(1503002,'Faro',NULL,'PA',1),(1503044,'Floresta do Araguaia',NULL,'PA',1),(1503077,'Garrafão do Norte',NULL,'PA',1),(1503093,'Goianésia do Pará',NULL,'PA',1),(1503101,'Gurupá',NULL,'PA',1),(1503200,'Igarapé-Açu',NULL,'PA',1),(1503309,'Igarapé-Miri',NULL,'PA',1),(1503408,'Inhangapi',NULL,'PA',1),(1503457,'Ipixuna do Pará',NULL,'PA',1),(1503507,'Irituia',NULL,'PA',1),(1503606,'Itaituba',NULL,'PA',1),(1503705,'Itupiranga',NULL,'PA',1),(1503754,'Jacareacanga',NULL,'PA',1),(1503804,'Jacundá',NULL,'PA',1),(1503903,'Juruti',NULL,'PA',1),(1504000,'Limoeiro do Ajuru',NULL,'PA',1),(1504059,'Mãe do Rio',NULL,'PA',1),(1504109,'Magalhães Barata',NULL,'PA',1),(1504208,'Marabá',NULL,'PA',1),(1504307,'Maracanã',NULL,'PA',1),(1504406,'Marapanim',NULL,'PA',1),(1504422,'Marituba',NULL,'PA',1),(1504455,'Medicilândia',NULL,'PA',1),(1504505,'Melgaço',NULL,'PA',1),(1504604,'Mocajuba',NULL,'PA',1),(1504703,'Moju',NULL,'PA',1),(1504752,'Mojuí dos Campos',NULL,'PA',1),(1504802,'Monte Alegre',NULL,'PA',1),(1504901,'Muaná',NULL,'PA',1),(1504950,'Nova Esperança do Piriá',NULL,'PA',1),(1504976,'Nova Ipixuna',NULL,'PA',1),(1505007,'Nova Timboteua',NULL,'PA',1),(1505031,'Novo Progresso',NULL,'PA',1),(1505064,'Novo Repartimento',NULL,'PA',1),(1505106,'Óbidos',NULL,'PA',1),(1505205,'Oeiras do Pará',NULL,'PA',1),(1505304,'Oriximiná',NULL,'PA',1),(1505403,'Ourém',NULL,'PA',1),(1505437,'Ourilândia do Norte',NULL,'PA',1),(1505486,'Pacajá',NULL,'PA',1),(1505494,'Palestina do Pará',NULL,'PA',1),(1505502,'Paragominas',NULL,'PA',1),(1505536,'Parauapebas',NULL,'PA',1),(1505551,'Pau d\'Arco',NULL,'PA',1),(1505601,'Peixe-Boi',NULL,'PA',1),(1505635,'Piçarra',NULL,'PA',1),(1505650,'Placas',NULL,'PA',1),(1505700,'Ponta de Pedras',NULL,'PA',1),(1505809,'Portel',NULL,'PA',1),(1505908,'Porto de Moz',NULL,'PA',1),(1506005,'Prainha',NULL,'PA',1),(1506104,'Primavera',NULL,'PA',1),(1506112,'Quatipuru',NULL,'PA',1),(1506138,'Redenção',NULL,'PA',1),(1506161,'Rio Maria',NULL,'PA',1),(1506187,'Rondon do Pará',NULL,'PA',1),(1506195,'Rurópolis',NULL,'PA',1),(1506203,'Salinópolis',NULL,'PA',1),(1506302,'Salvaterra',NULL,'PA',1),(1506351,'Santa Bárbara do Pará',NULL,'PA',1),(1506401,'Santa Cruz do Arari',NULL,'PA',1),(1506500,'Santa Izabel do Pará',NULL,'PA',1),(1506559,'Santa Luzia do Pará',NULL,'PA',1),(1506583,'Santa Maria das Barreiras',NULL,'PA',1),(1506609,'Santa Maria do Pará',NULL,'PA',1),(1506708,'Santana do Araguaia',NULL,'PA',1),(1506807,'Santarém',NULL,'PA',1),(1506906,'Santarém Novo',NULL,'PA',1),(1507003,'Santo Antônio do Tauá',NULL,'PA',1),(1507102,'São Caetano de Odivelas',NULL,'PA',1),(1507151,'São Domingos do Araguaia',NULL,'PA',1),(1507201,'São Domingos do Capim',NULL,'PA',1),(1507300,'São Félix do Xingu',NULL,'PA',1),(1507409,'São Francisco do Pará',NULL,'PA',1),(1507458,'São Geraldo do Araguaia',NULL,'PA',1),(1507466,'São João da Ponta',NULL,'PA',1),(1507474,'São João de Pirabas',NULL,'PA',1),(1507508,'São João do Araguaia',NULL,'PA',1),(1507607,'São Miguel do Guamá',NULL,'PA',1),(1507706,'São Sebastião da Boa Vista',NULL,'PA',1),(1507755,'Sapucaia',NULL,'PA',1),(1507805,'Senador José Porfírio',NULL,'PA',1),(1507904,'Soure',NULL,'PA',1),(1507953,'Tailândia',NULL,'PA',1),(1507961,'Terra Alta',NULL,'PA',1),(1507979,'Terra Santa',NULL,'PA',1),(1508001,'Tomé-Açu',NULL,'PA',1),(1508035,'Tracuateua',NULL,'PA',1),(1508050,'Trairão',NULL,'PA',1),(1508084,'Tucumã',NULL,'PA',1),(1508100,'Tucuruí',NULL,'PA',1),(1508126,'Ulianópolis',NULL,'PA',1),(1508159,'Uruará',NULL,'PA',1),(1508209,'Vigia',NULL,'PA',1),(1508308,'Viseu',NULL,'PA',1),(1508357,'Vitória do Xingu',NULL,'PA',1),(1508407,'Xinguara',NULL,'PA',1);
/*!40000 ALTER TABLE `municipios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricula_funcional` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perfil` enum('admin','gestor','operador') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_matricula` (`matricula_funcional`),
  KEY `idx_usuarios_nome` (`nome`),
  KEY `idx_usuarios_perfil` (`perfil`),
  KEY `idx_usuarios_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador do Sistema','57189088','$2a$12$WZ/X3tmS26MVZ4k4yNQKzei7Dso8LA3lw5QzUIx.2.tfA5ekby9Bu','admin','ativo','2026-03-19 14:17:13',NULL),(2,'Paula Fernanda Correa Lima','86145835249','$2y$10$yLFmbHxaq1.JVtZptqJ3WencVdBZ1di6Bgjh/hRv17GeC49iCtsNy','gestor','ativo','2026-03-20 20:40:45','2026-03-21 19:29:48');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'hidrantes_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-23 11:03:01
