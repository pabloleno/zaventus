-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: nxgestao
-- ------------------------------------------------------
-- Server version 	10.4.32-MariaDB
-- Date: Mon, 08 Jun 2026 23:53:11 -0400

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `anexos_os_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anexos_os_provisorio` (
  `id_anexo` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `arquivo` varchar(128) NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_anexo`),
  KEY `anexos_os_provisorio_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `anexos_os_provisorio_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos_provisorio` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anexos_os_provisorio`
--

LOCK TABLES `anexos_os_provisorio` WRITE;
/*!40000 ALTER TABLE `anexos_os_provisorio` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `anexos_os_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `anexos_os_provisorio` with 0 row(s)
--

--
-- Table structure for table `caixas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caixas` (
  `id_caixa` int(9) NOT NULL AUTO_INCREMENT,
  `data_de_abertura` date NOT NULL,
  `data_de_fechamento` date NOT NULL,
  `hora_de_abertura` time NOT NULL,
  `hora_de_fechamento` time NOT NULL,
  `valor_inicial` double NOT NULL,
  `valor_total` double NOT NULL,
  `valor_de_fechamento` double NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `status` varchar(18) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_caixa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixas`
--

LOCK TABLES `caixas` WRITE;
/*!40000 ALTER TABLE `caixas` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `caixas` VALUES (1,'2026-04-29','0000-00-00','14:23:52','00:00:00',100000,0,0,'','Aberto','2026-04-29 14:24:05','2026-04-29 14:24:05','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `caixas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `caixas` with 1 row(s)
--

--
-- Table structure for table `categorias_dos_produtos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_dos_produtos` (
  `id_categoria` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `descricao` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_dos_produtos`
--

LOCK TABLES `categorias_dos_produtos` WRITE;
/*!40000 ALTER TABLE `categorias_dos_produtos` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `categorias_dos_produtos` VALUES (1,'Nenhuma','Para produtos que não tem categoria.','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `categorias_dos_produtos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `categorias_dos_produtos` with 1 row(s)
--

--
-- Table structure for table `clientes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id_cliente` int(9) NOT NULL AUTO_INCREMENT,
  `tipo` int(11) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `data_de_nascimento` date NOT NULL,
  `rg` varchar(32) NOT NULL,
  `cpf` varchar(32) NOT NULL,
  `razao_social` varchar(128) NOT NULL,
  `nome_fantasia` varchar(128) NOT NULL,
  `cnpj` varchar(128) NOT NULL,
  `ie` varchar(128) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(128) NOT NULL,
  `numero` varchar(5) NOT NULL,
  `complemento` varchar(128) NOT NULL,
  `bairro` varchar(128) NOT NULL,
  `municipio` varchar(128) NOT NULL,
  `codigo_do_municipio` varchar(128) NOT NULL,
  `UF` varchar(128) NOT NULL,
  `celular` varchar(16) NOT NULL,
  `comercial` varchar(16) NOT NULL,
  `residencial` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `anotacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `clientes` VALUES (1,1,'Consumidor Final','0000-00-00','','','','','','','','','','','','','','','','','','','','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `clientes` with 1 row(s)
--

--
-- Table structure for table `config_empresa`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_empresa` (
  `id_config` int(9) NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(128) NOT NULL,
  `nome_fantasia` varchar(128) NOT NULL,
  `cnpj` varchar(128) NOT NULL,
  `inscricao_estadual` varchar(128) NOT NULL,
  `telefone` varchar(16) NOT NULL,
  `endereco` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  `favicon` varchar(255) NOT NULL DEFAULT 'favicon.ico',
  `logo_login` varchar(255) NOT NULL DEFAULT 'assets/img/zaventus-login-marca.png',
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_empresa`
--

LOCK TABLES `config_empresa` WRITE;
/*!40000 ALTER TABLE `config_empresa` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `config_empresa` VALUES (1,'','Sua Empresa','','','6335710000','Av. 01 Quadra 02 Lote 03','0000-00-00 00:00:00','2026-06-08 23:52:11','0000-00-00 00:00:00','favicon.ico','assets/img/zaventus-login-marca.png');
/*!40000 ALTER TABLE `config_empresa` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `config_empresa` with 1 row(s)
--

--
-- Table structure for table `config_nfce`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_nfce` (
  `id_config` int(9) NOT NULL AUTO_INCREMENT,
  `cUF` int(11) NOT NULL,
  `natOp` varchar(128) NOT NULL,
  `serie` int(11) NOT NULL,
  `nNF` int(11) NOT NULL,
  `cMunFG` varchar(128) NOT NULL,
  `tpAmb` int(11) NOT NULL,
  `verProc` varchar(128) NOT NULL,
  `CNPJ` varchar(32) NOT NULL,
  `xNome` varchar(128) NOT NULL,
  `xFant` varchar(128) NOT NULL,
  `IE` varchar(32) NOT NULL,
  `CRT` int(11) NOT NULL,
  `CEP` varchar(16) NOT NULL,
  `xLgr` varchar(128) NOT NULL,
  `nro` varchar(16) NOT NULL,
  `xCpl` varchar(128) NOT NULL,
  `xBairro` varchar(128) NOT NULL,
  `cMun` varchar(64) NOT NULL,
  `xMun` varchar(64) NOT NULL,
  `UF` varchar(5) NOT NULL,
  `cPais` varchar(16) NOT NULL,
  `xPais` varchar(128) NOT NULL,
  `fone` varchar(32) NOT NULL,
  `CNPJ_responsavel_tecnico` varchar(32) NOT NULL,
  `xContato` varchar(128) NOT NULL,
  `email_responsavel_tecnico` varchar(128) NOT NULL,
  `fone_responsavel_tecnico` varchar(32) NOT NULL,
  `certificado` int(11) NOT NULL,
  `senha` varchar(128) NOT NULL,
  `CSC` varchar(128) NOT NULL,
  `CSCid` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_nfce`
--

LOCK TABLES `config_nfce` WRITE;
/*!40000 ALTER TABLE `config_nfce` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `config_nfce` VALUES (1,13,'VENDA DE MERCADORIAS',1,1,'1302603',2,'ZaventusGestao-2026.04','00000000000000','EMITENTE EM HOMOLOGACAO - SUBSTITUIR CADASTRO','ZAVENTUS GESTAO HOMOLOGACAO','000000000',1,'69000000','ENDERECO DO EMITENTE','S/N','','CENTRO','1302603','Manaus','AM','1058','BRASIL','','00000000000000','RESPONSAVEL TECNICO - SUBSTITUIR','suporte@example.com','00000000000',0,'','0123456789','000001','0000-00-00 00:00:00','2026-04-29 23:33:24','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `config_nfce` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `config_nfce` with 1 row(s)
--

--
-- Table structure for table `config_nfe_nfce`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_nfe_nfce` (
  `id_config` int(9) NOT NULL AUTO_INCREMENT,
  `cUF` int(11) NOT NULL,
  `natOp` varchar(128) NOT NULL,
  `serie` int(11) NOT NULL,
  `nNF` int(11) NOT NULL,
  `cMunFG` varchar(128) NOT NULL,
  `tpAmb` int(11) NOT NULL,
  `verProc` varchar(128) NOT NULL,
  `CNPJ` varchar(32) NOT NULL,
  `xNome` varchar(128) NOT NULL,
  `xFant` varchar(128) NOT NULL,
  `IE` varchar(32) NOT NULL,
  `CRT` int(11) NOT NULL,
  `CEP` varchar(16) NOT NULL,
  `xLgr` varchar(128) NOT NULL,
  `nro` varchar(16) NOT NULL,
  `xCpl` varchar(128) NOT NULL,
  `xBairro` varchar(128) NOT NULL,
  `cMun` varchar(64) NOT NULL,
  `xMun` varchar(64) NOT NULL,
  `UF` varchar(5) NOT NULL,
  `cPais` varchar(16) NOT NULL,
  `xPais` varchar(128) NOT NULL,
  `fone` varchar(32) NOT NULL,
  `CNPJ_responsavel_tecnico` varchar(32) NOT NULL,
  `xContato` varchar(128) NOT NULL,
  `email_responsavel_tecnico` varchar(128) NOT NULL,
  `fone_responsavel_tecnico` varchar(32) NOT NULL,
  `certificado` varchar(128) NOT NULL,
  `senha` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_nfe_nfce`
--

LOCK TABLES `config_nfe_nfce` WRITE;
/*!40000 ALTER TABLE `config_nfe_nfce` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `config_nfe_nfce` VALUES (1,13,'VENDA DE MERCADORIAS',1,1,'1302603',2,'ZaventusGestao-2026.04','00000000000000','EMITENTE EM HOMOLOGACAO - SUBSTITUIR CADASTRO','ZAVENTUS GESTAO HOMOLOGACAO','000000000',1,'69000000','ENDERECO DO EMITENTE','S/N','','CENTRO','1302603','Manaus','AM','1058','BRASIL','','00000000000000','RESPONSAVEL TECNICO - SUBSTITUIR','suporte@example.com','00000000000','0','','0000-00-00 00:00:00','2026-04-29 23:33:24','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `config_nfe_nfce` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `config_nfe_nfce` with 1 row(s)
--

--
-- Table structure for table `contas_a_pagar`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contas_a_pagar` (
  `id_conta` int(9) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `data_de_vencimento` date NOT NULL,
  `valor` double NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_conta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contas_a_pagar`
--

LOCK TABLES `contas_a_pagar` WRITE;
/*!40000 ALTER TABLE `contas_a_pagar` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `contas_a_pagar` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `contas_a_pagar` with 0 row(s)
--

--
-- Table structure for table `contas_a_receber`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contas_a_receber` (
  `id_conta` int(9) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `data_de_vencimento` date NOT NULL,
  `valor` double NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_conta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contas_a_receber`
--

LOCK TABLES `contas_a_receber` WRITE;
/*!40000 ALTER TABLE `contas_a_receber` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `contas_a_receber` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `contas_a_receber` with 0 row(s)
--

--
-- Table structure for table `controle_de_acesso_funcionalidades`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controle_de_acesso_funcionalidades` (
  `id_ca_funcionalidade` int(9) NOT NULL AUTO_INCREMENT,
  `funcionalidade` varchar(512) NOT NULL,
  `permissao` int(11) NOT NULL,
  `id_ca_modulo` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_ca_funcionalidade`),
  KEY `controle_de_acesso_funcionalidades_id_ca_modulo_foreign` (`id_ca_modulo`),
  CONSTRAINT `controle_de_acesso_funcionalidades_id_ca_modulo_foreign` FOREIGN KEY (`id_ca_modulo`) REFERENCES `controle_de_acesso_modulos` (`id_ca_modulo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `controle_de_acesso_funcionalidades`
--

LOCK TABLES `controle_de_acesso_funcionalidades` WRITE;
/*!40000 ALTER TABLE `controle_de_acesso_funcionalidades` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `controle_de_acesso_funcionalidades` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `controle_de_acesso_funcionalidades` with 0 row(s)
--

--
-- Table structure for table `controle_de_acesso_modulos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `controle_de_acesso_modulos` (
  `id_ca_modulo` int(9) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(512) NOT NULL,
  `permissao` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_ca_modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `controle_de_acesso_modulos`
--

LOCK TABLES `controle_de_acesso_modulos` WRITE;
/*!40000 ALTER TABLE `controle_de_acesso_modulos` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `controle_de_acesso_modulos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `controle_de_acesso_modulos` with 0 row(s)
--

--
-- Table structure for table `despesas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `despesas` (
  `id_despesa` int(9) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(64) NOT NULL,
  `descricao` varchar(128) NOT NULL,
  `valor` double NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_despesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `despesas`
--

LOCK TABLES `despesas` WRITE;
/*!40000 ALTER TABLE `despesas` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `despesas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `despesas` with 0 row(s)
--

--
-- Table structure for table `equipamentos_os`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipamentos_os` (
  `id_equipamento` int(9) NOT NULL AUTO_INCREMENT,
  `equipamento` varchar(128) NOT NULL,
  `marca` varchar(128) NOT NULL,
  `modelo` varchar(128) NOT NULL,
  `serie` varchar(128) NOT NULL,
  `condicoes` varchar(2048) NOT NULL,
  `defeitos` varchar(2048) NOT NULL,
  `acessorios` varchar(2048) NOT NULL,
  `solucao` varchar(2048) NOT NULL,
  `laudo_tecnico` varchar(2048) NOT NULL,
  `termos_de_garantia` varchar(2048) NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_equipamento`),
  KEY `equipamentos_os_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `equipamentos_os_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentos_os`
--

LOCK TABLES `equipamentos_os` WRITE;
/*!40000 ALTER TABLE `equipamentos_os` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `equipamentos_os` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `equipamentos_os` with 0 row(s)
--

--
-- Table structure for table `equipamentos_os_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipamentos_os_provisorio` (
  `id_equipamento` int(9) NOT NULL AUTO_INCREMENT,
  `equipamento` varchar(128) NOT NULL,
  `marca` varchar(128) NOT NULL,
  `modelo` varchar(128) NOT NULL,
  `serie` varchar(128) NOT NULL,
  `condicoes` varchar(2048) NOT NULL,
  `defeitos` varchar(2048) NOT NULL,
  `acessorios` varchar(2048) NOT NULL,
  `solucao` varchar(2048) NOT NULL,
  `laudo_tecnico` varchar(2048) NOT NULL,
  `termos_de_garantia` varchar(2048) NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_equipamento`),
  KEY `equipamentos_os_provisorio_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `equipamentos_os_provisorio_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos_provisorio` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentos_os_provisorio`
--

LOCK TABLES `equipamentos_os_provisorio` WRITE;
/*!40000 ALTER TABLE `equipamentos_os_provisorio` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `equipamentos_os_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `equipamentos_os_provisorio` with 0 row(s)
--

--
-- Table structure for table `formas_de_pagamento`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formas_de_pagamento` (
  `id_forma` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `codigo_nfce` varchar(2) DEFAULT '99',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_forma`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formas_de_pagamento`
--

LOCK TABLES `formas_de_pagamento` WRITE;
/*!40000 ALTER TABLE `formas_de_pagamento` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `formas_de_pagamento` VALUES (1,'Dinheiro','01','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Cartão de Crédito','03','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Cartão de Débito','04','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Cheque','02','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Crédito Loja','05','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'Vale Alimentação','10','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,'Vale Refeição','11','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,'Vale Presente','12','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,'Vale Combustível','13','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,'Débito em Conta','16','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,'Boleto Bancário','15','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,'Transferência','18','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,'Depósito','16','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(14,'Nota Promissória','99','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00'),(15,'PayPal','99','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `formas_de_pagamento` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `formas_de_pagamento` with 15 row(s)
--

--
-- Table structure for table `fornecedores`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fornecedores` (
  `id_fornecedor` int(9) NOT NULL AUTO_INCREMENT,
  `nome_do_representante` varchar(128) NOT NULL,
  `nome_da_empresa` varchar(128) NOT NULL,
  `cnpj` varchar(128) NOT NULL,
  `ie` varchar(128) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(128) NOT NULL,
  `numero` varchar(5) NOT NULL,
  `complemento` varchar(128) NOT NULL,
  `bairro` varchar(128) NOT NULL,
  `municipio` varchar(128) NOT NULL,
  `codigo_do_municipio` varchar(128) NOT NULL,
  `celular` varchar(16) NOT NULL,
  `comercial` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `anotacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_fornecedor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedores`
--

LOCK TABLES `fornecedores` WRITE;
/*!40000 ALTER TABLE `fornecedores` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `fornecedores` VALUES (1,'GERAL','GERAL','S/N','','','','','','','','','','','','','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `fornecedores` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `fornecedores` with 1 row(s)
--

--
-- Table structure for table `funcionarios`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funcionarios` (
  `id_funcionario` int(9) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `data_de_nascimento` date NOT NULL,
  `rg` varchar(32) NOT NULL,
  `cpf` varchar(32) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(128) NOT NULL,
  `numero` varchar(5) NOT NULL,
  `complemento` varchar(128) NOT NULL,
  `bairro` varchar(128) NOT NULL,
  `municipio` varchar(128) NOT NULL,
  `celular` varchar(16) NOT NULL,
  `comercial` varchar(16) NOT NULL,
  `residencial` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `cargo` varchar(128) NOT NULL,
  `data_de_contratacao` date NOT NULL,
  `data_inicio_das_atividades` date NOT NULL,
  `salario` double NOT NULL,
  `detalhes_da_atividade` varchar(521) NOT NULL,
  `anotacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_funcionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionarios`
--

LOCK TABLES `funcionarios` WRITE;
/*!40000 ALTER TABLE `funcionarios` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `funcionarios` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `funcionarios` with 0 row(s)
--

--
-- Table structure for table `inventarios_do_estoque`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventarios_do_estoque` (
  `id_inventario` int(9) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(62) NOT NULL,
  `data` date NOT NULL,
  `observacoes` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_inventario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventarios_do_estoque`
--

LOCK TABLES `inventarios_do_estoque` WRITE;
/*!40000 ALTER TABLE `inventarios_do_estoque` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `inventarios_do_estoque` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `inventarios_do_estoque` with 0 row(s)
--

--
-- Table structure for table `lancamentos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lancamentos` (
  `id_lancamento` int(9) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(128) NOT NULL,
  `valor` double NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_lancamento`),
  KEY `lancamentos_id_caixa_foreign` (`id_caixa`),
  CONSTRAINT `lancamentos_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lancamentos`
--

LOCK TABLES `lancamentos` WRITE;
/*!40000 ALTER TABLE `lancamentos` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `lancamentos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `lancamentos` with 0 row(s)
--

--
-- Table structure for table `login`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `id_login` int(9) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(128) NOT NULL,
  `senha` varchar(128) NOT NULL,
  `primeiro_nome` varchar(128) NOT NULL,
  `ultimo_acesso` varchar(128) NOT NULL,
  `tema` int(2) NOT NULL,
  `controle_de_acesso` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `login` VALUES (1,'admin','$2y$10$YcL6CuPIfkK9Tw2YWlz98ewBj8GMjDzo7JAIz51Ns60yxlebiIwAO','Administrador','',0,'{\"vendas\":{\"modulo\":1,\"venda_rapida\":1,\"pdv\":1,\"pesq_produto\":1,\"hist_de_vendas\":1},\"controle_geral\":{\"modulo\":1,\"clientes\":1,\"fornecedores\":1,\"funcionarios\":1,\"vendedores\":1},\"estoque\":{\"modulo\":1,\"produtos\":1,\"reposicoes\":1,\"saida_de_mercadorias\":1,\"categorias_do_produto\":1},\"financeiro\":{\"modulo\":1,\"caixas\":1,\"lancamentos\":1,\"retiradas_do_caixa\":1,\"despesas\":1, \"contas_a_pagar\":1,\"contas_a_receber\":1,\"orcamentos\":1,\"pedidos\":1,\"relatorio_dre\":1,\"inventario_do_estoque\":1,\"controle_fiscal\":1},\"relatorios\":{\"modulo\":1,\"vendas\":1,\"estoque\":1,\"financeiro\":1,\"geral\":1},\"configs\":{\"modulo\":1,\"nfe\":1,\"nfce\":1,\"empresa\":1,\"sistema\":1,\"usuarios\":1,\"backup_de_dados\":1}}','0000-00-00 00:00:00','2026-06-08 23:37:41','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `login` with 1 row(s)
--

--
-- Table structure for table `migrations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` text NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `migrations` VALUES (1,'2020-03-05-162549','App\\Database\\Migrations\\Clientes','default','App',1589316019,1),(2,'2020-03-05-162613','App\\Database\\Migrations\\CategoriasDosProdutos','default','App',1589316019,1),(3,'2020-03-05-162621','App\\Database\\Migrations\\Fornecedores','default','App',1589316019,1),(4,'2020-03-05-162622','App\\Database\\Migrations\\Produtos','default','App',1589316020,1),(5,'2020-03-05-162633','App\\Database\\Migrations\\Caixas','default','App',1589316020,1),(6,'2020-03-05-162644','App\\Database\\Migrations\\Lancamentos','default','App',1589316020,1),(7,'2020-03-05-162654','App\\Database\\Migrations\\Retiradas','default','App',1589316021,1),(8,'2020-03-05-162704','App\\Database\\Migrations\\Despesas','default','App',1589316021,1),(9,'2020-03-05-162715','App\\Database\\Migrations\\Reposicoes','default','App',1589316021,1),(10,'2020-03-05-162725','App\\Database\\Migrations\\SaidaDeMercadorias','default','App',1589316022,1),(11,'2020-03-05-162734','App\\Database\\Migrations\\ContasAPagar','default','App',1589316022,1),(12,'2020-03-05-162749','App\\Database\\Migrations\\ContasAReceber','default','App',1589316022,1),(13,'2020-03-05-162800','App\\Database\\Migrations\\ProdutosDoPdv','default','App',1589316022,1),(14,'2020-03-05-162811','App\\Database\\Migrations\\ConfigNfeNfce','default','App',1589316022,1),(15,'2020-03-05-162821','App\\Database\\Migrations\\ConfigEmpresa','default','App',1589316023,1),(16,'2020-03-05-162849','App\\Database\\Migrations\\Login','default','App',1589316023,1),(17,'2020-03-05-162918','App\\Database\\Migrations\\FormasDePagamento','default','App',1589316023,1),(18,'2020-03-05-162928','App\\Database\\Migrations\\InventarioDoEstoque','default','App',1589316023,1),(19,'2020-03-05-162937','App\\Database\\Migrations\\ProdutosDoInventarioDoEstoque','default','App',1589316024,1),(20,'2020-03-05-162949','App\\Database\\Migrations\\Funcionarios','default','App',1589316025,1),(21,'2020-03-05-163007','App\\Database\\Migrations\\PagamentosDoCliente','default','App',1589316026,1),(22,'2020-03-05-163016','App\\Database\\Migrations\\Vendedores','default','App',1589316026,1),(23,'2020-03-05-163025','App\\Database\\Migrations\\Vendas','default','App',1589316027,1),(24,'2020-03-05-163035','App\\Database\\Migrations\\ProdutosDaVenda','default','App',1589316028,1),(25,'2020-03-05-163044','App\\Database\\Migrations\\VendaRapida','default','App',1589316029,1),(26,'2020-03-05-163059','App\\Database\\Migrations\\ProdutosDaVendaRapida','default','App',1589316030,1),(27,'2020-03-05-163109','App\\Database\\Migrations\\Nfces','default','App',1589316030,1),(28,'2020-03-05-163117','App\\Database\\Migrations\\Nfes','default','App',1589316031,1),(29,'2020-03-11-205355','App\\Database\\Migrations\\ConfigNfce','default','App',1589316031,1),(30,'2020-03-12-143912','App\\Database\\Migrations\\Orcamentos','default','App',1589316032,1),(31,'2020-03-12-144141','App\\Database\\Migrations\\ProdutosDoOrcamento','default','App',1589316032,1),(32,'2020-03-12-152203','App\\Database\\Migrations\\Pedidos','default','App',1589316032,1),(33,'2020-03-12-152215','App\\Database\\Migrations\\ProdutosDoPedido','default','App',1589316033,1),(34,'2020-04-12-124532','App\\Database\\Migrations\\Tecnicos','default','App',1589316036,1),(35,'2020-04-12-124605','App\\Database\\Migrations\\ServicosMaoDeObra','default','App',1589316036,1),(36,'2020-04-12-124632','App\\Database\\Migrations\\OrdensDeServicosProvisorio','default','App',1589316036,1),(37,'2020-04-12-124657','App\\Database\\Migrations\\PagamentosOsProvisorio','default','App',1589316037,1),(38,'2020-04-12-124744','App\\Database\\Migrations\\ParcelasDoPagamentoOsProvisorio','default','App',1589316037,1),(39,'2020-04-12-124809','App\\Database\\Migrations\\AnexosOsProvisorio','default','App',1589316037,1),(40,'2020-04-12-124831','App\\Database\\Migrations\\ServicosMaoDeObraProvisorio','default','App',1589316038,1),(41,'2020-04-12-124854','App\\Database\\Migrations\\EquipamentosOsProvisorio','default','App',1589316040,1),(42,'2020-04-12-124919','App\\Database\\Migrations\\ProdutosPecasOsProvisorio','default','App',1589316041,1),(43,'2020-04-13-162524','App\\Database\\Migrations\\OrdensDeServicos','default','App',1589316042,1),(44,'2020-04-13-162643','App\\Database\\Migrations\\PagamentosOs','default','App',1589316043,1),(45,'2020-04-13-162834','App\\Database\\Migrations\\ParcelasDoPagamentoOs','default','App',1589316043,1),(46,'2020-04-13-163231','App\\Database\\Migrations\\ServicosMaoDeObraDaOs','default','App',1589316043,1),(47,'2020-04-13-163337','App\\Database\\Migrations\\EquipamentosOs','default','App',1589316044,1),(48,'2020-04-13-163457','App\\Database\\Migrations\\ProdutosPecasOs','default','App',1589316044,1),(49,'2020-05-07-140303','App\\Database\\Migrations\\ProvisorioAddProdutoPorXml','default','App',1589316044,1),(50,'2020-05-08-155616','App\\Database\\Migrations\\ControleDeAcessoModulos','default','App',1589316045,1),(51,'2020-05-08-155757','App\\Database\\Migrations\\ControleDeAcessoFuncionalidades','default','App',1589316045,1),(52,'2020-05-14-155936','App\\Database\\Migrations\\ProvisorioReposicaoProdutosPorXml','default','App',1777490806,2),(53,'2026-04-29-021700','App\\Database\\Migrations\\AddIdCaixaToProdutosDoPdv','default','App',1777490806,2),(54,'2026-04-30-000000','App\\Database\\Migrations\\PrepareFiscalAmHomologation','default','App',1777523604,3),(55,'2026-04-30-010000','App\\Database\\Migrations\\RebrandToZaventusGestao','default','App',1777525222,4),(56,'2026-06-09-000001','App\\Database\\Migrations\\ConfigEmpresaPersonalizacao','default','App',1780977056,5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `migrations` with 56 row(s)
--

--
-- Table structure for table `nfces`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nfces` (
  `id_nfce` int(9) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `chave` varchar(128) NOT NULL,
  `xml` text NOT NULL,
  `arquivo_xml` varchar(128) NOT NULL,
  `status` varchar(32) NOT NULL,
  `erro` text NOT NULL,
  `id_venda` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_nfce`),
  KEY `nfces_id_venda_foreign` (`id_venda`),
  CONSTRAINT `nfces_id_venda_foreign` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nfces`
--

LOCK TABLES `nfces` WRITE;
/*!40000 ALTER TABLE `nfces` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `nfces` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `nfces` with 0 row(s)
--

--
-- Table structure for table `nfes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nfes` (
  `id_nfe` int(9) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `chave` varchar(128) NOT NULL,
  `xml` text NOT NULL,
  `protocolo` text NOT NULL,
  `status` varchar(32) NOT NULL,
  `erro` text NOT NULL,
  `xml_protocolado_cancelamento` text NOT NULL,
  `id_venda` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_nfe`),
  KEY `nfes_id_venda_foreign` (`id_venda`),
  CONSTRAINT `nfes_id_venda_foreign` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nfes`
--

LOCK TABLES `nfes` WRITE;
/*!40000 ALTER TABLE `nfes` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `nfes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `nfes` with 0 row(s)
--

--
-- Table structure for table `orcamentos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orcamentos` (
  `id_orcamento` int(9) NOT NULL AUTO_INCREMENT,
  `status` varchar(16) NOT NULL,
  `valor_a_pagar` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_recebido` double NOT NULL,
  `troco` double NOT NULL,
  `forma_de_pagamento` varchar(64) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_orcamento`),
  KEY `orcamentos_id_cliente_foreign` (`id_cliente`),
  KEY `orcamentos_id_vendedor_foreign` (`id_vendedor`),
  KEY `orcamentos_id_caixa_foreign` (`id_caixa`),
  CONSTRAINT `orcamentos_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orcamentos_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orcamentos_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orcamentos`
--

LOCK TABLES `orcamentos` WRITE;
/*!40000 ALTER TABLE `orcamentos` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `orcamentos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `orcamentos` with 0 row(s)
--

--
-- Table structure for table `ordens_de_servicos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordens_de_servicos` (
  `id_ordem` int(9) NOT NULL AUTO_INCREMENT,
  `situacao` varchar(128) NOT NULL,
  `data_de_entrada` date NOT NULL,
  `hora_de_entrada` time NOT NULL,
  `data_de_saida` date NOT NULL,
  `hora_de_saida` time NOT NULL,
  `canal_de_venda` varchar(128) NOT NULL,
  `centro_de_custo` varchar(128) NOT NULL,
  `frete` double NOT NULL,
  `outros` double NOT NULL,
  `desconto` double NOT NULL,
  `observacoes` varchar(2048) NOT NULL,
  `observacoes_internas` varchar(2048) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_ordem`),
  KEY `ordens_de_servicos_id_cliente_foreign` (`id_cliente`),
  KEY `ordens_de_servicos_id_vendedor_foreign` (`id_vendedor`),
  KEY `ordens_de_servicos_id_tecnico_foreign` (`id_tecnico`),
  CONSTRAINT `ordens_de_servicos_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ordens_de_servicos_id_tecnico_foreign` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ordens_de_servicos_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordens_de_servicos`
--

LOCK TABLES `ordens_de_servicos` WRITE;
/*!40000 ALTER TABLE `ordens_de_servicos` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `ordens_de_servicos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `ordens_de_servicos` with 0 row(s)
--

--
-- Table structure for table `ordens_de_servicos_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordens_de_servicos_provisorio` (
  `id_ordem` int(9) NOT NULL AUTO_INCREMENT,
  `situacao` varchar(128) NOT NULL,
  `data_de_entrada` date NOT NULL,
  `hora_de_entrada` time NOT NULL,
  `data_de_saida` date NOT NULL,
  `hora_de_saida` time NOT NULL,
  `canal_de_venda` varchar(128) NOT NULL,
  `centro_de_custo` varchar(128) NOT NULL,
  `frete` double NOT NULL,
  `outros` double NOT NULL,
  `desconto` double NOT NULL,
  `observacoes` varchar(2048) NOT NULL,
  `observacoes_internas` varchar(2048) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_ordem`),
  KEY `ordens_de_servicos_provisorio_id_cliente_foreign` (`id_cliente`),
  KEY `ordens_de_servicos_provisorio_id_vendedor_foreign` (`id_vendedor`),
  KEY `ordens_de_servicos_provisorio_id_tecnico_foreign` (`id_tecnico`),
  CONSTRAINT `ordens_de_servicos_provisorio_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ordens_de_servicos_provisorio_id_tecnico_foreign` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ordens_de_servicos_provisorio_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordens_de_servicos_provisorio`
--

LOCK TABLES `ordens_de_servicos_provisorio` WRITE;
/*!40000 ALTER TABLE `ordens_de_servicos_provisorio` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `ordens_de_servicos_provisorio` VALUES (1,'Aberto','2026-04-29','13:59:17','0000-00-00','00:00:00','Presencial','',0,0,0,'','',1,1,1,'2026-04-29 13:59:17','2026-04-29 13:59:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `ordens_de_servicos_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `ordens_de_servicos_provisorio` with 1 row(s)
--

--
-- Table structure for table `pagamentos_do_cliente`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentos_do_cliente` (
  `id_pagamento` int(9) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(128) NOT NULL,
  `valor` double NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_pagamento`),
  KEY `pagamentos_do_cliente_id_cliente_foreign` (`id_cliente`),
  CONSTRAINT `pagamentos_do_cliente_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos_do_cliente`
--

LOCK TABLES `pagamentos_do_cliente` WRITE;
/*!40000 ALTER TABLE `pagamentos_do_cliente` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `pagamentos_do_cliente` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `pagamentos_do_cliente` with 0 row(s)
--

--
-- Table structure for table `pagamentos_os`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentos_os` (
  `id_pagamento` int(9) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(128) NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_pagamento`),
  KEY `pagamentos_os_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `pagamentos_os_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos_os`
--

LOCK TABLES `pagamentos_os` WRITE;
/*!40000 ALTER TABLE `pagamentos_os` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `pagamentos_os` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `pagamentos_os` with 0 row(s)
--

--
-- Table structure for table `pagamentos_os_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentos_os_provisorio` (
  `id_pagamento` int(9) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(128) NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_pagamento`),
  KEY `pagamentos_os_provisorio_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `pagamentos_os_provisorio_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos_provisorio` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos_os_provisorio`
--

LOCK TABLES `pagamentos_os_provisorio` WRITE;
/*!40000 ALTER TABLE `pagamentos_os_provisorio` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `pagamentos_os_provisorio` VALUES (1,'À Vista',1,'2026-04-29 13:59:17','2026-04-29 13:59:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `pagamentos_os_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `pagamentos_os_provisorio` with 1 row(s)
--

--
-- Table structure for table `parcelas_do_pagamento_os`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parcelas_do_pagamento_os` (
  `id_parcela` int(9) NOT NULL AUTO_INCREMENT,
  `data_de_vencimento` date NOT NULL,
  `valor_da_parcela` double NOT NULL,
  `forma_de_pagamento` varchar(128) NOT NULL,
  `observacoes` varchar(2048) NOT NULL,
  `id_pagamento` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_parcela`),
  KEY `parcelas_do_pagamento_os_id_pagamento_foreign` (`id_pagamento`),
  CONSTRAINT `parcelas_do_pagamento_os_id_pagamento_foreign` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamentos_os` (`id_pagamento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcelas_do_pagamento_os`
--

LOCK TABLES `parcelas_do_pagamento_os` WRITE;
/*!40000 ALTER TABLE `parcelas_do_pagamento_os` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `parcelas_do_pagamento_os` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `parcelas_do_pagamento_os` with 0 row(s)
--

--
-- Table structure for table `parcelas_do_pagamento_os_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parcelas_do_pagamento_os_provisorio` (
  `id_parcela` int(9) NOT NULL AUTO_INCREMENT,
  `data_de_vencimento` date NOT NULL,
  `valor_da_parcela` double NOT NULL,
  `forma_de_pagamento` varchar(128) NOT NULL,
  `observacoes` varchar(2048) NOT NULL,
  `id_pagamento` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_parcela`),
  KEY `parcelas_do_pagamento_os_provisorio_id_pagamento_foreign` (`id_pagamento`),
  CONSTRAINT `parcelas_do_pagamento_os_provisorio_id_pagamento_foreign` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamentos_os_provisorio` (`id_pagamento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcelas_do_pagamento_os_provisorio`
--

LOCK TABLES `parcelas_do_pagamento_os_provisorio` WRITE;
/*!40000 ALTER TABLE `parcelas_do_pagamento_os_provisorio` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `parcelas_do_pagamento_os_provisorio` VALUES (1,'2026-04-29',0,'Dinheiro','',1,'2026-04-29 13:59:17','2026-04-29 13:59:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `parcelas_do_pagamento_os_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `parcelas_do_pagamento_os_provisorio` with 1 row(s)
--

--
-- Table structure for table `pedidos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id_pedido` int(9) NOT NULL AUTO_INCREMENT,
  `valor_a_pagar` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_recebido` double NOT NULL,
  `troco` double NOT NULL,
  `forma_de_pagamento` varchar(64) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `situacao` varchar(128) NOT NULL,
  `prazo_de_entrega` date NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `pedidos_id_cliente_foreign` (`id_cliente`),
  KEY `pedidos_id_vendedor_foreign` (`id_vendedor`),
  KEY `pedidos_id_caixa_foreign` (`id_caixa`),
  CONSTRAINT `pedidos_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pedidos_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pedidos_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `pedidos` with 0 row(s)
--

--
-- Table structure for table `produtos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos` (
  `id_produto` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `localizacao` varchar(128) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `quantidade_minima` int(11) NOT NULL,
  `valor_de_custo` double NOT NULL,
  `margem_de_lucro` double NOT NULL,
  `valor_de_venda` double NOT NULL,
  `lucro` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `arquivo` varchar(128) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_fornecedor` int(11) NOT NULL,
  `validade` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto`),
  KEY `produtos_id_categoria_foreign` (`id_categoria`),
  KEY `produtos_id_fornecedor_foreign` (`id_fornecedor`),
  CONSTRAINT `produtos_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_dos_produtos` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `produtos_id_fornecedor_foreign` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `produtos` VALUES (1,'iphone','UN','1010','armario A',3,1,8000,25,10000,2000,'0','0','0','1777487521_22ffb1962b75fdb4b95e.png',1,1,'0000-00-00','2026-04-29 13:32:01','2026-04-29 23:41:41','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos` with 1 row(s)
--

--
-- Table structure for table `produtos_da_venda`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_da_venda` (
  `id_produto_da_venda` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `subtotal` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_final` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto_da_venda`),
  KEY `produtos_da_venda_id_venda_foreign` (`id_venda`),
  CONSTRAINT `produtos_da_venda_id_venda_foreign` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_da_venda`
--

LOCK TABLES `produtos_da_venda` WRITE;
/*!40000 ALTER TABLE `produtos_da_venda` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `produtos_da_venda` VALUES (1,'iphone','UN','1010',1,10000,10000,0,10000,'0','0','0',1,1,'2026-04-29 14:31:25','2026-04-29 14:31:25','0000-00-00 00:00:00'),(2,'iphone','UN','1010',1,10000,10000,0,10000,'0','0','0',2,1,'2026-04-29 23:41:41','2026-04-29 23:41:41','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `produtos_da_venda` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_da_venda` with 2 row(s)
--

--
-- Table structure for table `produtos_da_venda_rapida`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_da_venda_rapida` (
  `id_produto_da_venda_rapida` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `subtotal` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_final` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `id_produto` int(11) NOT NULL,
  PRIMARY KEY (`id_produto_da_venda_rapida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_da_venda_rapida`
--

LOCK TABLES `produtos_da_venda_rapida` WRITE;
/*!40000 ALTER TABLE `produtos_da_venda_rapida` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_da_venda_rapida` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_da_venda_rapida` with 0 row(s)
--

--
-- Table structure for table `produtos_do_inventario_do_estoque`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_do_inventario_do_estoque` (
  `id_produto_do_inventario` int(9) NOT NULL AUTO_INCREMENT,
  `discriminacao` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `id_inventario` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto_do_inventario`),
  KEY `produtos_do_inventario_do_estoque_id_inventario_foreign` (`id_inventario`),
  CONSTRAINT `produtos_do_inventario_do_estoque_id_inventario_foreign` FOREIGN KEY (`id_inventario`) REFERENCES `inventarios_do_estoque` (`id_inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_do_inventario_do_estoque`
--

LOCK TABLES `produtos_do_inventario_do_estoque` WRITE;
/*!40000 ALTER TABLE `produtos_do_inventario_do_estoque` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_do_inventario_do_estoque` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_do_inventario_do_estoque` with 0 row(s)
--

--
-- Table structure for table `produtos_do_orcamento`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_do_orcamento` (
  `id_produto_do_orcamento` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `subtotal` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_final` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `id_orcamento` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto_do_orcamento`),
  KEY `produtos_do_orcamento_id_orcamento_foreign` (`id_orcamento`),
  CONSTRAINT `produtos_do_orcamento_id_orcamento_foreign` FOREIGN KEY (`id_orcamento`) REFERENCES `orcamentos` (`id_orcamento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_do_orcamento`
--

LOCK TABLES `produtos_do_orcamento` WRITE;
/*!40000 ALTER TABLE `produtos_do_orcamento` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_do_orcamento` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_do_orcamento` with 0 row(s)
--

--
-- Table structure for table `produtos_do_pdv`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_do_pdv` (
  `id_produto_pdv` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `subtotal` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_final` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `id_caixa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_produto_pdv`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_do_pdv`
--

LOCK TABLES `produtos_do_pdv` WRITE;
/*!40000 ALTER TABLE `produtos_do_pdv` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_do_pdv` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_do_pdv` with 0 row(s)
--

--
-- Table structure for table `produtos_do_pedido`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_do_pedido` (
  `id_produto_do_pedido` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `subtotal` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_final` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto_do_pedido`),
  KEY `produtos_do_pedido_id_pedido_foreign` (`id_pedido`),
  CONSTRAINT `produtos_do_pedido_id_pedido_foreign` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_do_pedido`
--

LOCK TABLES `produtos_do_pedido` WRITE;
/*!40000 ALTER TABLE `produtos_do_pedido` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_do_pedido` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_do_pedido` with 0 row(s)
--

--
-- Table structure for table `produtos_pecas_os`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_pecas_os` (
  `id_produto` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `desconto` double NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto`),
  KEY `produtos_pecas_os_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `produtos_pecas_os_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_pecas_os`
--

LOCK TABLES `produtos_pecas_os` WRITE;
/*!40000 ALTER TABLE `produtos_pecas_os` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_pecas_os` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_pecas_os` with 0 row(s)
--

--
-- Table structure for table `produtos_pecas_os_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_pecas_os_provisorio` (
  `id_produto` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` double NOT NULL,
  `desconto` double NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto`),
  KEY `produtos_pecas_os_provisorio_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `produtos_pecas_os_provisorio_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos_provisorio` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_pecas_os_provisorio`
--

LOCK TABLES `produtos_pecas_os_provisorio` WRITE;
/*!40000 ALTER TABLE `produtos_pecas_os_provisorio` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `produtos_pecas_os_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `produtos_pecas_os_provisorio` with 0 row(s)
--

--
-- Table structure for table `provisorio_add_produto_por_xml`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provisorio_add_produto_por_xml` (
  `id_produto` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(512) NOT NULL,
  `unidade` varchar(16) NOT NULL,
  `codigo_de_barras` varchar(13) NOT NULL,
  `localizacao` varchar(128) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `quantidade_minima` int(11) NOT NULL,
  `valor_de_custo` double NOT NULL,
  `margem_de_lucro` double NOT NULL,
  `valor_de_venda` double NOT NULL,
  `lucro` double NOT NULL,
  `NCM` varchar(8) NOT NULL,
  `CSOSN` varchar(3) NOT NULL,
  `CFOP` varchar(4) NOT NULL,
  `arquivo` varchar(128) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_fornecedor` int(11) NOT NULL,
  `validade` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_produto`),
  KEY `provisorio_add_produto_por_xml_id_categoria_foreign` (`id_categoria`),
  KEY `provisorio_add_produto_por_xml_id_fornecedor_foreign` (`id_fornecedor`),
  CONSTRAINT `provisorio_add_produto_por_xml_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_dos_produtos` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `provisorio_add_produto_por_xml_id_fornecedor_foreign` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provisorio_add_produto_por_xml`
--

LOCK TABLES `provisorio_add_produto_por_xml` WRITE;
/*!40000 ALTER TABLE `provisorio_add_produto_por_xml` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `provisorio_add_produto_por_xml` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `provisorio_add_produto_por_xml` with 0 row(s)
--

--
-- Table structure for table `provisorio_reposicao_produtos_por_xml`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provisorio_reposicao_produtos_por_xml` (
  `id_produto_provisorio` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) DEFAULT NULL,
  `quantidade_da_reposicao` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_produto_provisorio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provisorio_reposicao_produtos_por_xml`
--

LOCK TABLES `provisorio_reposicao_produtos_por_xml` WRITE;
/*!40000 ALTER TABLE `provisorio_reposicao_produtos_por_xml` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `provisorio_reposicao_produtos_por_xml` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `provisorio_reposicao_produtos_por_xml` with 0 row(s)
--

--
-- Table structure for table `reposicoes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reposicoes` (
  `id_reposicao` int(9) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `quantidade` int(11) NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_reposicao`),
  KEY `reposicoes_id_produto_foreign` (`id_produto`),
  CONSTRAINT `reposicoes_id_produto_foreign` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reposicoes`
--

LOCK TABLES `reposicoes` WRITE;
/*!40000 ALTER TABLE `reposicoes` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `reposicoes` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `reposicoes` with 0 row(s)
--

--
-- Table structure for table `retiradas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retiradas` (
  `id_retirada` int(9) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(64) NOT NULL,
  `descricao` varchar(128) NOT NULL,
  `valor` double NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_retirada`),
  KEY `retiradas_id_caixa_foreign` (`id_caixa`),
  CONSTRAINT `retiradas_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retiradas`
--

LOCK TABLES `retiradas` WRITE;
/*!40000 ALTER TABLE `retiradas` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `retiradas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `retiradas` with 0 row(s)
--

--
-- Table structure for table `saida_de_mercadorias`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saida_de_mercadorias` (
  `id_saida` int(9) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `quantidade` int(11) NOT NULL,
  `observacoes` varchar(512) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_saida`),
  KEY `saida_de_mercadorias_id_produto_foreign` (`id_produto`),
  CONSTRAINT `saida_de_mercadorias_id_produto_foreign` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saida_de_mercadorias`
--

LOCK TABLES `saida_de_mercadorias` WRITE;
/*!40000 ALTER TABLE `saida_de_mercadorias` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `saida_de_mercadorias` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `saida_de_mercadorias` with 0 row(s)
--

--
-- Table structure for table `servicos_mao_de_obra`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicos_mao_de_obra` (
  `id_servico` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `descricao` varchar(1024) NOT NULL,
  `valor` double NOT NULL,
  `observacoes` varchar(2048) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicos_mao_de_obra`
--

LOCK TABLES `servicos_mao_de_obra` WRITE;
/*!40000 ALTER TABLE `servicos_mao_de_obra` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `servicos_mao_de_obra` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `servicos_mao_de_obra` with 0 row(s)
--

--
-- Table structure for table `servicos_mao_de_obra_da_os`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicos_mao_de_obra_da_os` (
  `id_servico` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `descricao` varchar(1024) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor` double NOT NULL,
  `desconto` double NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_servico`),
  KEY `servicos_mao_de_obra_da_os_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `servicos_mao_de_obra_da_os_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicos_mao_de_obra_da_os`
--

LOCK TABLES `servicos_mao_de_obra_da_os` WRITE;
/*!40000 ALTER TABLE `servicos_mao_de_obra_da_os` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `servicos_mao_de_obra_da_os` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `servicos_mao_de_obra_da_os` with 0 row(s)
--

--
-- Table structure for table `servicos_mao_de_obra_provisorio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicos_mao_de_obra_provisorio` (
  `id_servico` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `descricao` varchar(1024) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor` double NOT NULL,
  `desconto` double NOT NULL,
  `id_ordem` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_servico`),
  KEY `servicos_mao_de_obra_provisorio_id_ordem_foreign` (`id_ordem`),
  CONSTRAINT `servicos_mao_de_obra_provisorio_id_ordem_foreign` FOREIGN KEY (`id_ordem`) REFERENCES `ordens_de_servicos_provisorio` (`id_ordem`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicos_mao_de_obra_provisorio`
--

LOCK TABLES `servicos_mao_de_obra_provisorio` WRITE;
/*!40000 ALTER TABLE `servicos_mao_de_obra_provisorio` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `servicos_mao_de_obra_provisorio` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `servicos_mao_de_obra_provisorio` with 0 row(s)
--

--
-- Table structure for table `tabela_municipios_ibge`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabela_municipios_ibge` (
  `id_tabela` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(128) DEFAULT NULL,
  `municipio` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_tabela`)
) ENGINE=InnoDB AUTO_INCREMENT=5571 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_municipios_ibge`
--

LOCK TABLES `tabela_municipios_ibge` WRITE;
/*!40000 ALTER TABLE `tabela_municipios_ibge` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `tabela_municipios_ibge` VALUES (1,'﻿1100015','Alta Floresta DOeste\r'),(2,'1100379','Alto Alegre dos Parecis\r'),(3,'1100403','Alto Paraíso\r'),(4,'1100346','Alvorada DOeste\r'),(5,'1100023','Ariquemes\r'),(6,'1100452','Buritis\r'),(7,'1100031','Cabixi\r'),(8,'1100601','Cacaulândia\r'),(9,'1100049','Cacoal\r'),(10,'1100700','Campo Novo de Rondônia\r'),(11,'1100809','Candeias do Jamari\r'),(12,'1100908','Castanheiras\r'),(13,'1100056','Cerejeiras\r'),(14,'1100924','Chupinguaia\r'),(15,'1100064','Colorado do Oeste\r'),(16,'1100072','Corumbiara\r'),(17,'1100080','Costa Marques\r'),(18,'1100940','Cujubim\r'),(19,'1100098','Espigão DOeste\r'),(20,'1101005','Governador Jorge Teixeira\r'),(21,'1100106','Guajará-Mirim\r'),(22,'1101104','Itapuã do Oeste\r'),(23,'1100114','Jaru\r'),(24,'1100122','Ji-Paraná\r'),(25,'1100130','Machadinho DOeste\r'),(26,'1101203','Ministro Andreazza\r'),(27,'1101302','Mirante da Serra\r'),(28,'1101401','Monte Negro\r'),(29,'1100148','Nova Brasilândia DOeste\r'),(30,'1100338','Nova Mamoré\r'),(31,'1101435','Nova União\r'),(32,'1100502','Novo Horizonte do Oeste\r'),(33,'1100155','Ouro Preto do Oeste\r'),(34,'1101450','Parecis\r'),(35,'1100189','Pimenta Bueno\r'),(36,'1101468','Pimenteiras do Oeste\r'),(37,'1100205','Porto Velho\r'),(38,'1100254','Presidente Médici\r'),(39,'1101476','Primavera de Rondônia\r'),(40,'1100262','Rio Crespo\r'),(41,'1100288','Rolim de Moura\r'),(42,'1100296','Santa Luzia DOeste\r'),(43,'1101484','São Felipe DOeste\r'),(44,'1101492','São Francisco do Guaporé\r'),(45,'1100320','São Miguel do Guaporé\r'),(46,'1101500','Seringueiras\r'),(47,'1101559','Teixeirópolis\r'),(48,'1101609','Theobroma\r'),(49,'1101708','Urupá\r'),(50,'1101757','Vale do Anari\r'),(51,'1101807','Vale do Paraíso\r'),(52,'1100304','Vilhena\r'),(53,'1200013','Acrelândia\r'),(54,'1200054','Assis Brasil\r'),(55,'1200104','Brasiléia\r'),(56,'1200138','Bujari\r'),(57,'1200179','Capixaba\r'),(58,'1200203','Cruzeiro do Sul\r'),(59,'1200252','Epitaciolândia\r'),(60,'1200302','Feijó\r'),(61,'1200328','Jordão\r'),(62,'1200336','Mâncio Lima\r'),(63,'1200344','Manoel Urbano\r'),(64,'1200351','Marechal Thaumaturgo\r'),(65,'1200385','Plácido de Castro\r'),(66,'1200807','Porto Acre\r'),(67,'1200393','Porto Walter\r'),(68,'1200401','Rio Branco\r'),(69,'1200427','Rodrigues Alves\r'),(70,'1200435','Santa Rosa do Purus\r'),(71,'1200500','Sena Madureira\r'),(72,'1200450','Senador Guiomard\r'),(73,'1200609','Tarauacá\r'),(74,'1200708','Xapuri\r'),(75,'1300029','Alvarães\r'),(76,'1300060','Amaturá\r'),(77,'1300086','Anamã\r'),(78,'1300102','Anori\r'),(79,'1300144','Apuí\r'),(80,'1300201','Atalaia do Norte\r'),(81,'1300300','Autazes\r'),(82,'1300409','Barcelos\r'),(83,'1300508','Barreirinha\r'),(84,'1300607','Benjamin Constant\r'),(85,'1300631','Beruri\r'),(86,'1300680','Boa Vista do Ramos\r'),(87,'1300706','Boca do Acre\r'),(88,'1300805','Borba\r'),(89,'1300839','Caapiranga\r'),(90,'1300904','Canutama\r'),(91,'1301001','Carauari\r'),(92,'1301100','Careiro\r'),(93,'1301159','Careiro da Várzea\r'),(94,'1301209','Coari\r'),(95,'1301308','Codajás\r'),(96,'1301407','Eirunepé\r'),(97,'1301506','Envira\r'),(98,'1301605','Fonte Boa\r'),(99,'1301654','Guajará\r'),(100,'1301704','Humaitá\r'),(101,'1301803','Ipixuna\r'),(102,'1301852','Iranduba\r'),(103,'1301902','Itacoatiara\r'),(104,'1301951','Itamarati\r'),(105,'1302009','Itapiranga\r'),(106,'1302108','Japurá\r'),(107,'1302207','Juruá\r'),(108,'1302306','Jutaí\r'),(109,'1302405','Lábrea\r'),(110,'1302504','Manacapuru\r'),(111,'1302553','Manaquiri\r'),(112,'1302603','Manaus\r'),(113,'1302702','Manicoré\r'),(114,'1302801','Maraã\r'),(115,'1302900','Maués\r'),(116,'1303007','Nhamundá\r'),(117,'1303106','Nova Olinda do Norte\r'),(118,'1303205','Novo Airão\r'),(119,'1303304','Novo Aripuanã\r'),(120,'1303403','Parintins\r'),(121,'1303502','Pauini\r'),(122,'1303536','Presidente Figueiredo\r'),(123,'1303569','Rio Preto da Eva\r'),(124,'1303601','Santa Isabel do Rio Negro\r'),(125,'1303700','Santo Antônio do Içá\r'),(126,'1303809','São Gabriel da Cachoeira\r'),(127,'1303908','São Paulo de Olivença\r'),(128,'1303957','São Sebastião do Uatumã\r'),(129,'1304005','Silves\r'),(130,'1304062','Tabatinga\r'),(131,'1304104','Tapauá\r'),(132,'1304203','Tefé\r'),(133,'1304237','Tonantins\r'),(134,'1304260','Uarini\r'),(135,'1304302','Urucará\r'),(136,'1304401','Urucurituba\r'),(137,'1400050','Alto Alegre\r'),(138,'1400027','Amajari\r'),(139,'1400100','Boa Vista\r'),(140,'1400159','Bonfim\r'),(141,'1400175','Cantá\r'),(142,'1400209','Caracaraí\r'),(143,'1400233','Caroebe\r'),(144,'1400282','Iracema\r'),(145,'1400308','Mucajaí\r'),(146,'1400407','Normandia\r'),(147,'1400456','Pacaraima\r'),(148,'1400472','Rorainópolis\r'),(149,'1400506','São João da Baliza\r'),(150,'1400605','São Luiz\r'),(151,'1400704','Uiramutã\r'),(152,'1500107','Abaetetuba\r'),(153,'1500131','Abel Figueiredo\r'),(154,'1500206','Acará\r'),(155,'1500305','Afuá\r'),(156,'1500347','Água Azul do Norte\r'),(157,'1500404','Alenquer\r'),(158,'1500503','Almeirim\r'),(159,'1500602','Altamira\r'),(160,'1500701','Anajás\r'),(161,'1500800','Ananindeua\r'),(162,'1500859','Anapu\r'),(163,'1500909','Augusto Corrêa\r'),(164,'1500958','Aurora do Pará\r'),(165,'1501006','Aveiro\r'),(166,'1501105','Bagre\r'),(167,'1501204','Baião\r'),(168,'1501253','Bannach\r'),(169,'1501303','Barcarena\r'),(170,'1501402','Belém\r'),(171,'1501451','Belterra\r'),(172,'1501501','Benevides\r'),(173,'1501576','Bom Jesus do Tocantins\r'),(174,'1501600','Bonito\r'),(175,'1501709','Bragança\r'),(176,'1501725','Brasil Novo\r'),(177,'1501758','Brejo Grande do Araguaia\r'),(178,'1501782','Breu Branco\r'),(179,'1501808','Breves\r'),(180,'1501907','Bujaru\r'),(181,'1502004','Cachoeira do Arari\r'),(182,'1501956','Cachoeira do Piriá\r'),(183,'1502103','Cametá\r'),(184,'1502152','Canaã dos Carajás\r'),(185,'1502202','Capanema\r'),(186,'1502301','Capitão Poço\r'),(187,'1502400','Castanhal\r'),(188,'1502509','Chaves\r'),(189,'1502608','Colares\r'),(190,'1502707','Conceição do Araguaia\r'),(191,'1502756','Concórdia do Pará\r'),(192,'1502764','Cumaru do Norte\r'),(193,'1502772','Curionópolis\r'),(194,'1502806','Curralinho\r'),(195,'1502855','Curuá\r'),(196,'1502905','Curuçá\r'),(197,'1502939','Dom Eliseu\r'),(198,'1502954','Eldorado do Carajás\r'),(199,'1503002','Faro\r'),(200,'1503044','Floresta do Araguaia\r'),(201,'1503077','Garrafão do Norte\r'),(202,'1503093','Goianésia do Pará\r'),(203,'1503101','Gurupá\r'),(204,'1503200','Igarapé-Açu\r'),(205,'1503309','Igarapé-Miri\r'),(206,'1503408','Inhangapi\r'),(207,'1503457','Ipixuna do Pará\r'),(208,'1503507','Irituia\r'),(209,'1503606','Itaituba\r'),(210,'1503705','Itupiranga\r'),(211,'1503754','Jacareacanga\r'),(212,'1503804','Jacundá\r'),(213,'1503903','Juruti\r'),(214,'1504000','Limoeiro do Ajuru\r'),(215,'1504059','Mãe do Rio\r'),(216,'1504109','Magalhães Barata\r'),(217,'1504208','Marabá\r'),(218,'1504307','Maracanã\r'),(219,'1504406','Marapanim\r'),(220,'1504422','Marituba\r'),(221,'1504455','Medicilândia\r'),(222,'1504505','Melgaço\r'),(223,'1504604','Mocajuba\r'),(224,'1504703','Moju\r'),(225,'1504752','Mojuí dos Campos\r'),(226,'1504802','Monte Alegre\r'),(227,'1504901','Muaná\r'),(228,'1504950','Nova Esperança do Piriá\r'),(229,'1504976','Nova Ipixuna\r'),(230,'1505007','Nova Timboteua\r'),(231,'1505031','Novo Progresso\r'),(232,'1505064','Novo Repartimento\r'),(233,'1505106','Óbidos\r'),(234,'1505205','Oeiras do Pará\r'),(235,'1505304','Oriximiná\r'),(236,'1505403','Ourém\r'),(237,'1505437','Ourilândia do Norte\r'),(238,'1505486','Pacajá\r'),(239,'1505494','Palestina do Pará\r'),(240,'1505502','Paragominas\r'),(241,'1505536','Parauapebas\r'),(242,'1505551','Pau DArco\r'),(243,'1505601','Peixe-Boi\r'),(244,'1505635','Piçarra\r'),(245,'1505650','Placas\r'),(246,'1505700','Ponta de Pedras\r'),(247,'1505809','Portel\r'),(248,'1505908','Porto de Moz\r'),(249,'1506005','Prainha\r'),(250,'1506104','Primavera\r'),(251,'1506112','Quatipuru\r'),(252,'1506138','Redenção\r'),(253,'1506161','Rio Maria\r'),(254,'1506187','Rondon do Pará\r'),(255,'1506195','Rurópolis\r'),(256,'1506203','Salinópolis\r'),(257,'1506302','Salvaterra\r'),(258,'1506351','Santa Bárbara do Pará\r'),(259,'1506401','Santa Cruz do Arari\r'),(260,'1506500','Santa Izabel do Pará\r'),(261,'1506559','Santa Luzia do Pará\r'),(262,'1506583','Santa Maria das Barreiras\r'),(263,'1506609','Santa Maria do Pará\r'),(264,'1506708','Santana do Araguaia\r'),(265,'1506807','Santarém\r'),(266,'1506906','Santarém Novo\r'),(267,'1507003','Santo Antônio do Tauá\r'),(268,'1507102','São Caetano de Odivelas\r'),(269,'1507151','São Domingos do Araguaia\r'),(270,'1507201','São Domingos do Capim\r'),(271,'1507300','São Félix do Xingu\r'),(272,'1507409','São Francisco do Pará\r'),(273,'1507458','São Geraldo do Araguaia\r'),(274,'1507466','São João da Ponta\r'),(275,'1507474','São João de Pirabas\r'),(276,'1507508','São João do Araguaia\r'),(277,'1507607','São Miguel do Guamá\r'),(278,'1507706','São Sebastião da Boa Vista\r'),(279,'1507755','Sapucaia\r'),(280,'1507805','Senador José Porfírio\r'),(281,'1507904','Soure\r'),(282,'1507953','Tailândia\r'),(283,'1507961','Terra Alta\r'),(284,'1507979','Terra Santa\r'),(285,'1508001','Tomé-Açu\r'),(286,'1508035','Tracuateua\r'),(287,'1508050','Trairão\r'),(288,'1508084','Tucumã\r'),(289,'1508100','Tucuruí\r'),(290,'1508126','Ulianópolis\r'),(291,'1508159','Uruará\r'),(292,'1508209','Vigia\r'),(293,'1508308','Viseu\r'),(294,'1508357','Vitória do Xingu\r'),(295,'1508407','Xinguara\r'),(296,'1600105','Amapá\r'),(297,'1600204','Calçoene\r'),(298,'1600212','Cutias\r'),(299,'1600238','Ferreira Gomes\r'),(300,'1600253','Itaubal\r'),(301,'1600279','Laranjal do Jari\r'),(302,'1600303','Macapá\r'),(303,'1600402','Mazagão\r'),(304,'1600501','Oiapoque\r'),(305,'1600154','Pedra Branca do Amapari\r'),(306,'1600535','Porto Grande\r'),(307,'1600550','Pracuúba\r'),(308,'1600600','Santana\r'),(309,'1600055','Serra do Navio\r'),(310,'1600709','Tartarugalzinho\r'),(311,'1600808','Vitória do Jari\r'),(312,'1700251','Abreulândia\r'),(313,'1700301','Aguiarnópolis\r'),(314,'1700350','Aliança do Tocantins\r'),(315,'1700400','Almas\r'),(316,'1700707','Alvorada\r'),(317,'1701002','Ananás\r'),(318,'1701051','Angico\r'),(319,'1701101','Aparecida do Rio Negro\r'),(320,'1701309','Aragominas\r'),(321,'1701903','Araguacema\r'),(322,'1702000','Araguaçu\r'),(323,'1702109','Araguaína\r'),(324,'1702158','Araguanã\r'),(325,'1702208','Araguatins\r'),(326,'1702307','Arapoema\r'),(327,'1702406','Arraias\r'),(328,'1702554','Augustinópolis\r'),(329,'1702703','Aurora do Tocantins\r'),(330,'1702901','Axixá do Tocantins\r'),(331,'1703008','Babaçulândia\r'),(332,'1703057','Bandeirantes do Tocantins\r'),(333,'1703073','Barra do Ouro\r'),(334,'1703107','Barrolândia\r'),(335,'1703206','Bernardo Sayão\r'),(336,'1703305','Bom Jesus do Tocantins\r'),(337,'1703602','Brasilândia do Tocantins\r'),(338,'1703701','Brejinho de Nazaré\r'),(339,'1703800','Buriti do Tocantins\r'),(340,'1703826','Cachoeirinha\r'),(341,'1703842','Campos Lindos\r'),(342,'1703867','Cariri do Tocantins\r'),(343,'1703883','Carmolândia\r'),(344,'1703891','Carrasco Bonito\r'),(345,'1703909','Caseara\r'),(346,'1704105','Centenário\r'),(347,'1705102','Chapada da Natividade\r'),(348,'1704600','Chapada de Areia\r'),(349,'1705508','Colinas do Tocantins\r'),(350,'1716703','Colméia\r'),(351,'1705557','Combinado\r'),(352,'1705607','Conceição do Tocantins\r'),(353,'1706001','Couto Magalhães\r'),(354,'1706100','Cristalândia\r'),(355,'1706258','Crixás do Tocantins\r'),(356,'1706506','Darcinópolis\r'),(357,'1707009','Dianópolis\r'),(358,'1707108','Divinópolis do Tocantins\r'),(359,'1707207','Dois Irmãos do Tocantins\r'),(360,'1707306','Dueré\r'),(361,'1707405','Esperantina\r'),(362,'1707553','Fátima\r'),(363,'1707652','Figueirópolis\r'),(364,'1707702','Filadélfia\r'),(365,'1708205','Formoso do Araguaia\r'),(366,'1708304','Goianorte\r'),(367,'1709005','Goiatins\r'),(368,'1709302','Guaraí\r'),(369,'1709500','Gurupi\r'),(370,'1709807','Ipueiras\r'),(371,'1710508','Itacajá\r'),(372,'1710706','Itaguatins\r'),(373,'1710904','Itapiratins\r'),(374,'1711100','Itaporã do Tocantins\r'),(375,'1711506','Jaú do Tocantins\r'),(376,'1711803','Juarina\r'),(377,'1711902','Lagoa da Confusão\r'),(378,'1711951','Lagoa do Tocantins\r'),(379,'1712009','Lajeado\r'),(380,'1712157','Lavandeira\r'),(381,'1712405','Lizarda\r'),(382,'1712454','Luzinópolis\r'),(383,'1712504','Marianópolis do Tocantins\r'),(384,'1712702','Mateiros\r'),(385,'1712801','Maurilândia do Tocantins\r'),(386,'1713205','Miracema do Tocantins\r'),(387,'1713304','Miranorte\r'),(388,'1713601','Monte do Carmo\r'),(389,'1713700','Monte Santo do Tocantins\r'),(390,'1713957','Muricilândia\r'),(391,'1714203','Natividade\r'),(392,'1714302','Nazaré\r'),(393,'1714880','Nova Olinda\r'),(394,'1715002','Nova Rosalândia\r'),(395,'1715101','Novo Acordo\r'),(396,'1715150','Novo Alegre\r'),(397,'1715259','Novo Jardim\r'),(398,'1715507','Oliveira de Fátima\r'),(399,'1721000','Palmas\r'),(400,'1715705','Palmeirante\r'),(401,'1713809','Palmeiras do Tocantins\r'),(402,'1715754','Palmeirópolis\r'),(403,'1716109','Paraíso do Tocantins\r'),(404,'1716208','Paranã\r'),(405,'1716307','Pau DArco\r'),(406,'1716505','Pedro Afonso\r'),(407,'1716604','Peixe\r'),(408,'1716653','Pequizeiro\r'),(409,'1717008','Pindorama do Tocantins\r'),(410,'1717206','Piraquê\r'),(411,'1717503','Pium\r'),(412,'1717800','Ponte Alta do Bom Jesus\r'),(413,'1717909','Ponte Alta do Tocantins\r'),(414,'1718006','Porto Alegre do Tocantins\r'),(415,'1718204','Porto Nacional\r'),(416,'1718303','Praia Norte\r'),(417,'1718402','Presidente Kennedy\r'),(418,'1718451','Pugmil\r'),(419,'1718501','Recursolândia\r'),(420,'1718550','Riachinho\r'),(421,'1718659','Rio da Conceição\r'),(422,'1718709','Rio dos Bois\r'),(423,'1718758','Rio Sono\r'),(424,'1718808','Sampaio\r'),(425,'1718840','Sandolândia\r'),(426,'1718865','Santa Fé do Araguaia\r'),(427,'1718881','Santa Maria do Tocantins\r'),(428,'1718899','Santa Rita do Tocantins\r'),(429,'1718907','Santa Rosa do Tocantins\r'),(430,'1719004','Santa Tereza do Tocantins\r'),(431,'1720002','Santa Terezinha do Tocantins\r'),(432,'1720101','São Bento do Tocantins\r'),(433,'1720150','São Félix do Tocantins\r'),(434,'1720200','São Miguel do Tocantins\r'),(435,'1720259','São Salvador do Tocantins\r'),(436,'1720309','São Sebastião do Tocantins\r'),(437,'1720499','São Valério\r'),(438,'1720655','Silvanópolis\r'),(439,'1720804','Sítio Novo do Tocantins\r'),(440,'1720853','Sucupira\r'),(441,'1708254','Tabocão\r'),(442,'1720903','Taguatinga\r'),(443,'1720937','Taipas do Tocantins\r'),(444,'1720978','Talismã\r'),(445,'1721109','Tocantínia\r'),(446,'1721208','Tocantinópolis\r'),(447,'1721257','Tupirama\r'),(448,'1721307','Tupiratins\r'),(449,'1722081','Wanderlândia\r'),(450,'1722107','Xambioá\r'),(451,'2100055','Açailândia\r'),(452,'2100105','Afonso Cunha\r'),(453,'2100154','Água Doce do Maranhão\r'),(454,'2100204','Alcântara\r'),(455,'2100303','Aldeias Altas\r'),(456,'2100402','Altamira do Maranhão\r'),(457,'2100436','Alto Alegre do Maranhão\r'),(458,'2100477','Alto Alegre do Pindaré\r'),(459,'2100501','Alto Parnaíba\r'),(460,'2100550','Amapá do Maranhão\r'),(461,'2100600','Amarante do Maranhão\r'),(462,'2100709','Anajatuba\r'),(463,'2100808','Anapurus\r'),(464,'2100832','Apicum-Açu\r'),(465,'2100873','Araguanã\r'),(466,'2100907','Araioses\r'),(467,'2100956','Arame\r'),(468,'2101004','Arari\r'),(469,'2101103','Axixá\r'),(470,'2101202','Bacabal\r'),(471,'2101251','Bacabeira\r'),(472,'2101301','Bacuri\r'),(473,'2101350','Bacurituba\r'),(474,'2101400','Balsas\r'),(475,'2101509','Barão de Grajaú\r'),(476,'2101608','Barra do Corda\r'),(477,'2101707','Barreirinhas\r'),(478,'2101772','Bela Vista do Maranhão\r'),(479,'2101731','Belágua\r'),(480,'2101806','Benedito Leite\r'),(481,'2101905','Bequimão\r'),(482,'2101939','Bernardo do Mearim\r'),(483,'2101970','Boa Vista do Gurupi\r'),(484,'2102002','Bom Jardim\r'),(485,'2102036','Bom Jesus das Selvas\r'),(486,'2102077','Bom Lugar\r'),(487,'2102101','Brejo\r'),(488,'2102150','Brejo de Areia\r'),(489,'2102200','Buriti\r'),(490,'2102309','Buriti Bravo\r'),(491,'2102325','Buriticupu\r'),(492,'2102358','Buritirana\r'),(493,'2102374','Cachoeira Grande\r'),(494,'2102408','Cajapió\r'),(495,'2102507','Cajari\r'),(496,'2102556','Campestre do Maranhão\r'),(497,'2102606','Cândido Mendes\r'),(498,'2102705','Cantanhede\r'),(499,'2102754','Capinzal do Norte\r'),(500,'2102804','Carolina\r'),(501,'2102903','Carutapera\r'),(502,'2103000','Caxias\r'),(503,'2103109','Cedral\r'),(504,'2103125','Central do Maranhão\r'),(505,'2103158','Centro do Guilherme\r'),(506,'2103174','Centro Novo do Maranhão\r'),(507,'2103208','Chapadinha\r'),(508,'2103257','Cidelândia\r'),(509,'2103307','Codó\r'),(510,'2103406','Coelho Neto\r'),(511,'2103505','Colinas\r'),(512,'2103554','Conceição do Lago-Açu\r'),(513,'2103604','Coroatá\r'),(514,'2103703','Cururupu\r'),(515,'2103752','Davinópolis\r'),(516,'2103802','Dom Pedro\r'),(517,'2103901','Duque Bacelar\r'),(518,'2104008','Esperantinópolis\r'),(519,'2104057','Estreito\r'),(520,'2104073','Feira Nova do Maranhão\r'),(521,'2104081','Fernando Falcão\r'),(522,'2104099','Formosa da Serra Negra\r'),(523,'2104107','Fortaleza dos Nogueiras\r'),(524,'2104206','Fortuna\r'),(525,'2104305','Godofredo Viana\r'),(526,'2104404','Gonçalves Dias\r'),(527,'2104503','Governador Archer\r'),(528,'2104552','Governador Edison Lobão\r'),(529,'2104602','Governador Eugênio Barros\r'),(530,'2104628','Governador Luiz Rocha\r'),(531,'2104651','Governador Newton Bello\r'),(532,'2104677','Governador Nunes Freire\r'),(533,'2104701','Graça Aranha\r'),(534,'2104800','Grajaú\r'),(535,'2104909','Guimarães\r'),(536,'2105005','Humberto de Campos\r'),(537,'2105104','Icatu\r'),(538,'2105153','Igarapé do Meio\r'),(539,'2105203','Igarapé Grande\r'),(540,'2105302','Imperatriz\r'),(541,'2105351','Itaipava do Grajaú\r'),(542,'2105401','Itapecuru Mirim\r'),(543,'2105427','Itinga do Maranhão\r'),(544,'2105450','Jatobá\r'),(545,'2105476','Jenipapo dos Vieiras\r'),(546,'2105500','João Lisboa\r'),(547,'2105609','Joselândia\r'),(548,'2105658','Junco do Maranhão\r'),(549,'2105708','Lago da Pedra\r'),(550,'2105807','Lago do Junco\r'),(551,'2105948','Lago dos Rodrigues\r'),(552,'2105906','Lago Verde\r'),(553,'2105922','Lagoa do Mato\r'),(554,'2105963','Lagoa Grande do Maranhão\r'),(555,'2105989','Lajeado Novo\r'),(556,'2106003','Lima Campos\r'),(557,'2106102','Loreto\r'),(558,'2106201','Luís Domingues\r'),(559,'2106300','Magalhães de Almeida\r'),(560,'2106326','Maracaçumé\r'),(561,'2106359','Marajá do Sena\r'),(562,'2106375','Maranhãozinho\r'),(563,'2106409','Mata Roma\r'),(564,'2106508','Matinha\r'),(565,'2106607','Matões\r'),(566,'2106631','Matões do Norte\r'),(567,'2106672','Milagres do Maranhão\r'),(568,'2106706','Mirador\r'),(569,'2106755','Miranda do Norte\r'),(570,'2106805','Mirinzal\r'),(571,'2106904','Monção\r'),(572,'2107001','Montes Altos\r'),(573,'2107100','Morros\r'),(574,'2107209','Nina Rodrigues\r'),(575,'2107258','Nova Colinas\r'),(576,'2107308','Nova Iorque\r'),(577,'2107357','Nova Olinda do Maranhão\r'),(578,'2107407','Olho dÁgua das Cunhãs\r'),(579,'2107456','Olinda Nova do Maranhão\r'),(580,'2107506','Paço do Lumiar\r'),(581,'2107605','Palmeirândia\r'),(582,'2107704','Paraibano\r'),(583,'2107803','Parnarama\r'),(584,'2107902','Passagem Franca\r'),(585,'2108009','Pastos Bons\r'),(586,'2108058','Paulino Neves\r'),(587,'2108108','Paulo Ramos\r'),(588,'2108207','Pedreiras\r'),(589,'2108256','Pedro do Rosário\r'),(590,'2108306','Penalva\r'),(591,'2108405','Peri Mirim\r'),(592,'2108454','Peritoró\r'),(593,'2108504','Pindaré-Mirim\r'),(594,'2108603','Pinheiro\r'),(595,'2108702','Pio XII\r'),(596,'2108801','Pirapemas\r'),(597,'2108900','Poção de Pedras\r'),(598,'2109007','Porto Franco\r'),(599,'2109056','Porto Rico do Maranhão\r'),(600,'2109106','Presidente Dutra\r'),(601,'2109205','Presidente Juscelino\r'),(602,'2109239','Presidente Médici\r'),(603,'2109270','Presidente Sarney\r'),(604,'2109304','Presidente Vargas\r'),(605,'2109403','Primeira Cruz\r'),(606,'2109452','Raposa\r'),(607,'2109502','Riachão\r'),(608,'2109551','Ribamar Fiquene\r'),(609,'2109601','Rosário\r'),(610,'2109700','Sambaíba\r'),(611,'2109759','Santa Filomena do Maranhão\r'),(612,'2109809','Santa Helena\r'),(613,'2109908','Santa Inês\r'),(614,'2110005','Santa Luzia\r'),(615,'2110039','Santa Luzia do Paruá\r'),(616,'2110104','Santa Quitéria do Maranhão\r'),(617,'2110203','Santa Rita\r'),(618,'2110237','Santana do Maranhão\r'),(619,'2110278','Santo Amaro do Maranhão\r'),(620,'2110302','Santo Antônio dos Lopes\r'),(621,'2110401','São Benedito do Rio Preto\r'),(622,'2110500','São Bento\r'),(623,'2110609','São Bernardo\r'),(624,'2110658','São Domingos do Azeitão\r'),(625,'2110708','São Domingos do Maranhão\r'),(626,'2110807','São Félix de Balsas\r'),(627,'2110856','São Francisco do Brejão\r'),(628,'2110906','São Francisco do Maranhão\r'),(629,'2111003','São João Batista\r'),(630,'2111029','São João do Carú\r'),(631,'2111052','São João do Paraíso\r'),(632,'2111078','São João do Soter\r'),(633,'2111102','São João dos Patos\r'),(634,'2111201','São José de Ribamar\r'),(635,'2111250','São José dos Basílios\r'),(636,'2111300','São Luís\r'),(637,'2111409','São Luís Gonzaga do Maranhão\r'),(638,'2111508','São Mateus do Maranhão\r'),(639,'2111532','São Pedro da Água Branca\r'),(640,'2111573','São Pedro dos Crentes\r'),(641,'2111607','São Raimundo das Mangabeiras\r'),(642,'2111631','São Raimundo do Doca Bezerra\r'),(643,'2111672','São Roberto\r'),(644,'2111706','São Vicente Ferrer\r'),(645,'2111722','Satubinha\r'),(646,'2111748','Senador Alexandre Costa\r'),(647,'2111763','Senador La Rocque\r'),(648,'2111789','Serrano do Maranhão\r'),(649,'2111805','Sítio Novo\r'),(650,'2111904','Sucupira do Norte\r'),(651,'2111953','Sucupira do Riachão\r'),(652,'2112001','Tasso Fragoso\r'),(653,'2112100','Timbiras\r'),(654,'2112209','Timon\r'),(655,'2112233','Trizidela do Vale\r'),(656,'2112274','Tufilândia\r'),(657,'2112308','Tuntum\r'),(658,'2112407','Turiaçu\r'),(659,'2112456','Turilândia\r'),(660,'2112506','Tutóia\r'),(661,'2112605','Urbano Santos\r'),(662,'2112704','Vargem Grande\r'),(663,'2112803','Viana\r'),(664,'2112852','Vila Nova dos Martírios\r'),(665,'2112902','Vitória do Mearim\r'),(666,'2113009','Vitorino Freire\r'),(667,'2114007','Zé Doca\r'),(668,'2200053','Acauã\r'),(669,'2200103','Agricolândia\r'),(670,'2200202','Água Branca\r'),(671,'2200251','Alagoinha do Piauí\r'),(672,'2200277','Alegrete do Piauí\r'),(673,'2200301','Alto Longá\r'),(674,'2200400','Altos\r'),(675,'2200459','Alvorada do Gurguéia\r'),(676,'2200509','Amarante\r'),(677,'2200608','Angical do Piauí\r'),(678,'2200707','Anísio de Abreu\r'),(679,'2200806','Antônio Almeida\r'),(680,'2200905','Aroazes\r'),(681,'2200954','Aroeiras do Itaim\r'),(682,'2201002','Arraial\r'),(683,'2201051','Assunção do Piauí\r'),(684,'2201101','Avelino Lopes\r'),(685,'2201150','Baixa Grande do Ribeiro\r'),(686,'2201176','Barra DAlcântara\r'),(687,'2201200','Barras\r'),(688,'2201309','Barreiras do Piauí\r'),(689,'2201408','Barro Duro\r'),(690,'2201507','Batalha\r'),(691,'2201556','Bela Vista do Piauí\r'),(692,'2201572','Belém do Piauí\r'),(693,'2201606','Beneditinos\r'),(694,'2201705','Bertolínia\r'),(695,'2201739','Betânia do Piauí\r'),(696,'2201770','Boa Hora\r'),(697,'2201804','Bocaina\r'),(698,'2201903','Bom Jesus\r'),(699,'2201919','Bom Princípio do Piauí\r'),(700,'2201929','Bonfim do Piauí\r'),(701,'2201945','Boqueirão do Piauí\r'),(702,'2201960','Brasileira\r'),(703,'2201988','Brejo do Piauí\r'),(704,'2202000','Buriti dos Lopes\r'),(705,'2202026','Buriti dos Montes\r'),(706,'2202059','Cabeceiras do Piauí\r'),(707,'2202075','Cajazeiras do Piauí\r'),(708,'2202083','Cajueiro da Praia\r'),(709,'2202091','Caldeirão Grande do Piauí\r'),(710,'2202109','Campinas do Piauí\r'),(711,'2202117','Campo Alegre do Fidalgo\r'),(712,'2202133','Campo Grande do Piauí\r'),(713,'2202174','Campo Largo do Piauí\r'),(714,'2202208','Campo Maior\r'),(715,'2202251','Canavieira\r'),(716,'2202307','Canto do Buriti\r'),(717,'2202406','Capitão de Campos\r'),(718,'2202455','Capitão Gervásio Oliveira\r'),(719,'2202505','Caracol\r'),(720,'2202539','Caraúbas do Piauí\r'),(721,'2202554','Caridade do Piauí\r'),(722,'2202604','Castelo do Piauí\r'),(723,'2202653','Caxingó\r'),(724,'2202703','Cocal\r'),(725,'2202711','Cocal de Telha\r'),(726,'2202729','Cocal dos Alves\r'),(727,'2202737','Coivaras\r'),(728,'2202752','Colônia do Gurguéia\r'),(729,'2202778','Colônia do Piauí\r'),(730,'2202802','Conceição do Canindé\r'),(731,'2202851','Coronel José Dias\r'),(732,'2202901','Corrente\r'),(733,'2203008','Cristalândia do Piauí\r'),(734,'2203107','Cristino Castro\r'),(735,'2203206','Curimatá\r'),(736,'2203230','Currais\r'),(737,'2203271','Curral Novo do Piauí\r'),(738,'2203255','Curralinhos\r'),(739,'2203305','Demerval Lobão\r'),(740,'2203354','Dirceu Arcoverde\r'),(741,'2203404','Dom Expedito Lopes\r'),(742,'2203453','Dom Inocêncio\r'),(743,'2203420','Domingos Mourão\r'),(744,'2203503','Elesbão Veloso\r'),(745,'2203602','Eliseu Martins\r'),(746,'2203701','Esperantina\r'),(747,'2203750','Fartura do Piauí\r'),(748,'2203800','Flores do Piauí\r'),(749,'2203859','Floresta do Piauí\r'),(750,'2203909','Floriano\r'),(751,'2204006','Francinópolis\r'),(752,'2204105','Francisco Ayres\r'),(753,'2204154','Francisco Macedo\r'),(754,'2204204','Francisco Santos\r'),(755,'2204303','Fronteiras\r'),(756,'2204352','Geminiano\r'),(757,'2204402','Gilbués\r'),(758,'2204501','Guadalupe\r'),(759,'2204550','Guaribas\r'),(760,'2204600','Hugo Napoleão\r'),(761,'2204659','Ilha Grande\r'),(762,'2204709','Inhuma\r'),(763,'2204808','Ipiranga do Piauí\r'),(764,'2204907','Isaías Coelho\r'),(765,'2205003','Itainópolis\r'),(766,'2205102','Itaueira\r'),(767,'2205151','Jacobina do Piauí\r'),(768,'2205201','Jaicós\r'),(769,'2205250','Jardim do Mulato\r'),(770,'2205276','Jatobá do Piauí\r'),(771,'2205300','Jerumenha\r'),(772,'2205359','João Costa\r'),(773,'2205409','Joaquim Pires\r'),(774,'2205458','Joca Marques\r'),(775,'2205508','José de Freitas\r'),(776,'2205516','Juazeiro do Piauí\r'),(777,'2205524','Júlio Borges\r'),(778,'2205532','Jurema\r'),(779,'2205557','Lagoa Alegre\r'),(780,'2205573','Lagoa de São Francisco\r'),(781,'2205565','Lagoa do Barro do Piauí\r'),(782,'2205581','Lagoa do Piauí\r'),(783,'2205599','Lagoa do Sítio\r'),(784,'2205540','Lagoinha do Piauí\r'),(785,'2205607','Landri Sales\r'),(786,'2205706','Luís Correia\r'),(787,'2205805','Luzilândia\r'),(788,'2205854','Madeiro\r'),(789,'2205904','Manoel Emídio\r'),(790,'2205953','Marcolândia\r'),(791,'2206001','Marcos Parente\r'),(792,'2206050','Massapê do Piauí\r'),(793,'2206100','Matias Olímpio\r'),(794,'2206209','Miguel Alves\r'),(795,'2206308','Miguel Leão\r'),(796,'2206357','Milton Brandão\r'),(797,'2206407','Monsenhor Gil\r'),(798,'2206506','Monsenhor Hipólito\r'),(799,'2206605','Monte Alegre do Piauí\r'),(800,'2206654','Morro Cabeça no Tempo\r'),(801,'2206670','Morro do Chapéu do Piauí\r'),(802,'2206696','Murici dos Portelas\r'),(803,'2206704','Nazaré do Piauí\r'),(804,'2206720','Nazária\r'),(805,'2206753','Nossa Senhora de Nazaré\r'),(806,'2206803','Nossa Senhora dos Remédios\r'),(807,'2207959','Nova Santa Rita\r'),(808,'2206902','Novo Oriente do Piauí\r'),(809,'2206951','Novo Santo Antônio\r'),(810,'2207009','Oeiras\r'),(811,'2207108','Olho DÁgua do Piauí\r'),(812,'2207207','Padre Marcos\r'),(813,'2207306','Paes Landim\r'),(814,'2207355','Pajeú do Piauí\r'),(815,'2207405','Palmeira do Piauí\r'),(816,'2207504','Palmeirais\r'),(817,'2207553','Paquetá\r'),(818,'2207603','Parnaguá\r'),(819,'2207702','Parnaíba\r'),(820,'2207751','Passagem Franca do Piauí\r'),(821,'2207777','Patos do Piauí\r'),(822,'2207793','Pau DArco do Piauí\r'),(823,'2207801','Paulistana\r'),(824,'2207850','Pavussu\r'),(825,'2207900','Pedro II\r'),(826,'2207934','Pedro Laurentino\r'),(827,'2208007','Picos\r'),(828,'2208106','Pimenteiras\r'),(829,'2208205','Pio IX\r'),(830,'2208304','Piracuruca\r'),(831,'2208403','Piripiri\r'),(832,'2208502','Porto\r'),(833,'2208551','Porto Alegre do Piauí\r'),(834,'2208601','Prata do Piauí\r'),(835,'2208650','Queimada Nova\r'),(836,'2208700','Redenção do Gurguéia\r'),(837,'2208809','Regeneração\r'),(838,'2208858','Riacho Frio\r'),(839,'2208874','Ribeira do Piauí\r'),(840,'2208908','Ribeiro Gonçalves\r'),(841,'2209005','Rio Grande do Piauí\r'),(842,'2209104','Santa Cruz do Piauí\r'),(843,'2209153','Santa Cruz dos Milagres\r'),(844,'2209203','Santa Filomena\r'),(845,'2209302','Santa Luz\r'),(846,'2209377','Santa Rosa do Piauí\r'),(847,'2209351','Santana do Piauí\r'),(848,'2209401','Santo Antônio de Lisboa\r'),(849,'2209450','Santo Antônio dos Milagres\r'),(850,'2209500','Santo Inácio do Piauí\r'),(851,'2209559','São Braz do Piauí\r'),(852,'2209609','São Félix do Piauí\r'),(853,'2209658','São Francisco de Assis do Piauí\r'),(854,'2209708','São Francisco do Piauí\r'),(855,'2209757','São Gonçalo do Gurguéia\r'),(856,'2209807','São Gonçalo do Piauí\r'),(857,'2209856','São João da Canabrava\r'),(858,'2209872','São João da Fronteira\r'),(859,'2209906','São João da Serra\r'),(860,'2209955','São João da Varjota\r'),(861,'2209971','São João do Arraial\r'),(862,'2210003','São João do Piauí\r'),(863,'2210052','São José do Divino\r'),(864,'2210102','São José do Peixe\r'),(865,'2210201','São José do Piauí\r'),(866,'2210300','São Julião\r'),(867,'2210359','São Lourenço do Piauí\r'),(868,'2210375','São Luis do Piauí\r'),(869,'2210383','São Miguel da Baixa Grande\r'),(870,'2210391','São Miguel do Fidalgo\r'),(871,'2210409','São Miguel do Tapuio\r'),(872,'2210508','São Pedro do Piauí\r'),(873,'2210607','São Raimundo Nonato\r'),(874,'2210623','Sebastião Barros\r'),(875,'2210631','Sebastião Leal\r'),(876,'2210656','Sigefredo Pacheco\r'),(877,'2210706','Simões\r'),(878,'2210805','Simplício Mendes\r'),(879,'2210904','Socorro do Piauí\r'),(880,'2210938','Sussuapara\r'),(881,'2210953','Tamboril do Piauí\r'),(882,'2210979','Tanque do Piauí\r'),(883,'2211001','Teresina\r'),(884,'2211100','União\r'),(885,'2211209','Uruçuí\r'),(886,'2211308','Valença do Piauí\r'),(887,'2211357','Várzea Branca\r'),(888,'2211407','Várzea Grande\r'),(889,'2211506','Vera Mendes\r'),(890,'2211605','Vila Nova do Piauí\r'),(891,'2211704','Wall Ferraz\r'),(892,'2300101','Abaiara\r'),(893,'2300150','Acarape\r'),(894,'2300200','Acaraú\r'),(895,'2300309','Acopiara\r'),(896,'2300408','Aiuaba\r'),(897,'2300507','Alcântaras\r'),(898,'2300606','Altaneira\r'),(899,'2300705','Alto Santo\r'),(900,'2300754','Amontada\r'),(901,'2300804','Antonina do Norte\r'),(902,'2300903','Apuiarés\r'),(903,'2301000','Aquiraz\r'),(904,'2301109','Aracati\r'),(905,'2301208','Aracoiaba\r'),(906,'2301257','Ararendá\r'),(907,'2301307','Araripe\r'),(908,'2301406','Aratuba\r'),(909,'2301505','Arneiroz\r'),(910,'2301604','Assaré\r'),(911,'2301703','Aurora\r'),(912,'2301802','Baixio\r'),(913,'2301851','Banabuiú\r'),(914,'2301901','Barbalha\r'),(915,'2301950','Barreira\r'),(916,'2302008','Barro\r'),(917,'2302057','Barroquinha\r'),(918,'2302107','Baturité\r'),(919,'2302206','Beberibe\r'),(920,'2302305','Bela Cruz\r'),(921,'2302404','Boa Viagem\r'),(922,'2302503','Brejo Santo\r'),(923,'2302602','Camocim\r'),(924,'2302701','Campos Sales\r'),(925,'2302800','Canindé\r'),(926,'2302909','Capistrano\r'),(927,'2303006','Caridade\r'),(928,'2303105','Cariré\r'),(929,'2303204','Caririaçu\r'),(930,'2303303','Cariús\r'),(931,'2303402','Carnaubal\r'),(932,'2303501','Cascavel\r'),(933,'2303600','Catarina\r'),(934,'2303659','Catunda\r'),(935,'2303709','Caucaia\r'),(936,'2303808','Cedro\r'),(937,'2303907','Chaval\r'),(938,'2303931','Choró\r'),(939,'2303956','Chorozinho\r'),(940,'2304004','Coreaú\r'),(941,'2304103','Crateús\r'),(942,'2304202','Crato\r'),(943,'2304236','Croatá\r'),(944,'2304251','Cruz\r'),(945,'2304269','Deputado Irapuan Pinheiro\r'),(946,'2304277','Ereré\r'),(947,'2304285','Eusébio\r'),(948,'2304301','Farias Brito\r'),(949,'2304350','Forquilha\r'),(950,'2304400','Fortaleza\r'),(951,'2304459','Fortim\r'),(952,'2304509','Frecheirinha\r'),(953,'2304608','General Sampaio\r'),(954,'2304657','Graça\r'),(955,'2304707','Granja\r'),(956,'2304806','Granjeiro\r'),(957,'2304905','Groaíras\r'),(958,'2304954','Guaiúba\r'),(959,'2305001','Guaraciaba do Norte\r'),(960,'2305100','Guaramiranga\r'),(961,'2305209','Hidrolândia\r'),(962,'2305233','Horizonte\r'),(963,'2305266','Ibaretama\r'),(964,'2305308','Ibiapina\r'),(965,'2305332','Ibicuitinga\r'),(966,'2305357','Icapuí\r'),(967,'2305407','Icó\r'),(968,'2305506','Iguatu\r'),(969,'2305605','Independência\r'),(970,'2305654','Ipaporanga\r'),(971,'2305704','Ipaumirim\r'),(972,'2305803','Ipu\r'),(973,'2305902','Ipueiras\r'),(974,'2306009','Iracema\r'),(975,'2306108','Irauçuba\r'),(976,'2306207','Itaiçaba\r'),(977,'2306256','Itaitinga\r'),(978,'2306306','Itapajé\r'),(979,'2306405','Itapipoca\r'),(980,'2306504','Itapiúna\r'),(981,'2306553','Itarema\r'),(982,'2306603','Itatira\r'),(983,'2306702','Jaguaretama\r'),(984,'2306801','Jaguaribara\r'),(985,'2306900','Jaguaribe\r'),(986,'2307007','Jaguaruana\r'),(987,'2307106','Jardim\r'),(988,'2307205','Jati\r'),(989,'2307254','Jijoca de Jericoacoara\r'),(990,'2307304','Juazeiro do Norte\r'),(991,'2307403','Jucás\r'),(992,'2307502','Lavras da Mangabeira\r'),(993,'2307601','Limoeiro do Norte\r'),(994,'2307635','Madalena\r'),(995,'2307650','Maracanaú\r'),(996,'2307700','Maranguape\r'),(997,'2307809','Marco\r'),(998,'2307908','Martinópole\r'),(999,'2308005','Massapê\r'),(1000,'2308104','Mauriti\r'),(1001,'2308203','Meruoca\r'),(1002,'2308302','Milagres\r'),(1003,'2308351','Milhã\r'),(1004,'2308377','Miraíma\r'),(1005,'2308401','Missão Velha\r'),(1006,'2308500','Mombaça\r'),(1007,'2308609','Monsenhor Tabosa\r'),(1008,'2308708','Morada Nova\r'),(1009,'2308807','Moraújo\r'),(1010,'2308906','Morrinhos\r'),(1011,'2309003','Mucambo\r'),(1012,'2309102','Mulungu\r'),(1013,'2309201','Nova Olinda\r'),(1014,'2309300','Nova Russas\r'),(1015,'2309409','Novo Oriente\r'),(1016,'2309458','Ocara\r'),(1017,'2309508','Orós\r'),(1018,'2309607','Pacajus\r'),(1019,'2309706','Pacatuba\r'),(1020,'2309805','Pacoti\r'),(1021,'2309904','Pacujá\r'),(1022,'2310001','Palhano\r'),(1023,'2310100','Palmácia\r'),(1024,'2310209','Paracuru\r'),(1025,'2310258','Paraipaba\r'),(1026,'2310308','Parambu\r'),(1027,'2310407','Paramoti\r'),(1028,'2310506','Pedra Branca\r'),(1029,'2310605','Penaforte\r'),(1030,'2310704','Pentecoste\r'),(1031,'2310803','Pereiro\r'),(1032,'2310852','Pindoretama\r'),(1033,'2310902','Piquet Carneiro\r'),(1034,'2310951','Pires Ferreira\r'),(1035,'2311009','Poranga\r'),(1036,'2311108','Porteiras\r'),(1037,'2311207','Potengi\r'),(1038,'2311231','Potiretama\r'),(1039,'2311264','Quiterianópolis\r'),(1040,'2311306','Quixadá\r'),(1041,'2311355','Quixelô\r'),(1042,'2311405','Quixeramobim\r'),(1043,'2311504','Quixeré\r'),(1044,'2311603','Redenção\r'),(1045,'2311702','Reriutaba\r'),(1046,'2311801','Russas\r'),(1047,'2311900','Saboeiro\r'),(1048,'2311959','Salitre\r'),(1049,'2312205','Santa Quitéria\r'),(1050,'2312007','Santana do Acaraú\r'),(1051,'2312106','Santana do Cariri\r'),(1052,'2312304','São Benedito\r'),(1053,'2312403','São Gonçalo do Amarante\r'),(1054,'2312502','São João do Jaguaribe\r'),(1055,'2312601','São Luís do Curu\r'),(1056,'2312700','Senador Pompeu\r'),(1057,'2312809','Senador Sá\r'),(1058,'2312908','Sobral\r'),(1059,'2313005','Solonópole\r'),(1060,'2313104','Tabuleiro do Norte\r'),(1061,'2313203','Tamboril\r'),(1062,'2313252','Tarrafas\r'),(1063,'2313302','Tauá\r'),(1064,'2313351','Tejuçuoca\r'),(1065,'2313401','Tianguá\r'),(1066,'2313500','Trairi\r'),(1067,'2313559','Tururu\r'),(1068,'2313609','Ubajara\r'),(1069,'2313708','Umari\r'),(1070,'2313757','Umirim\r'),(1071,'2313807','Uruburetama\r'),(1072,'2313906','Uruoca\r'),(1073,'2313955','Varjota\r'),(1074,'2314003','Várzea Alegre\r'),(1075,'2314102','Viçosa do Ceará\r'),(1076,'2400109','Acari\r'),(1077,'2400208','Açu\r'),(1078,'2400307','Afonso Bezerra\r'),(1079,'2400406','Água Nova\r'),(1080,'2400505','Alexandria\r'),(1081,'2400604','Almino Afonso\r'),(1082,'2400703','Alto do Rodrigues\r'),(1083,'2400802','Angicos\r'),(1084,'2400901','Antônio Martins\r'),(1085,'2401008','Apodi\r'),(1086,'2401107','Areia Branca\r'),(1087,'2401206','Arês\r'),(1088,'2401404','Baía Formosa\r'),(1089,'2401453','Baraúna\r'),(1090,'2401503','Barcelona\r'),(1091,'2401602','Bento Fernandes\r'),(1092,'2401651','Bodó\r'),(1093,'2401701','Bom Jesus\r'),(1094,'2401800','Brejinho\r'),(1095,'2401859','Caiçara do Norte\r'),(1096,'2401909','Caiçara do Rio do Vento\r'),(1097,'2402006','Caicó\r'),(1098,'2401305','Campo Grande\r'),(1099,'2402105','Campo Redondo\r'),(1100,'2402204','Canguaretama\r'),(1101,'2402303','Caraúbas\r'),(1102,'2402402','Carnaúba dos Dantas\r'),(1103,'2402501','Carnaubais\r'),(1104,'2402600','Ceará-Mirim\r'),(1105,'2402709','Cerro Corá\r'),(1106,'2402808','Coronel Ezequiel\r'),(1107,'2402907','Coronel João Pessoa\r'),(1108,'2403004','Cruzeta\r'),(1109,'2403103','Currais Novos\r'),(1110,'2403202','Doutor Severiano\r'),(1111,'2403301','Encanto\r'),(1112,'2403400','Equador\r'),(1113,'2403509','Espírito Santo\r'),(1114,'2403608','Extremoz\r'),(1115,'2403707','Felipe Guerra\r'),(1116,'2403756','Fernando Pedroza\r'),(1117,'2403806','Florânia\r'),(1118,'2403905','Francisco Dantas\r'),(1119,'2404002','Frutuoso Gomes\r'),(1120,'2404101','Galinhos\r'),(1121,'2404200','Goianinha\r'),(1122,'2404309','Governador Dix-Sept Rosado\r'),(1123,'2404408','Grossos\r'),(1124,'2404507','Guamaré\r'),(1125,'2404606','Ielmo Marinho\r'),(1126,'2404705','Ipanguaçu\r'),(1127,'2404804','Ipueira\r'),(1128,'2404853','Itajá\r'),(1129,'2404903','Itaú\r'),(1130,'2405009','Jaçanã\r'),(1131,'2405108','Jandaíra\r'),(1132,'2405207','Janduís\r'),(1133,'2405306','Januário Cicco\r'),(1134,'2405405','Japi\r'),(1135,'2405504','Jardim de Angicos\r'),(1136,'2405603','Jardim de Piranhas\r'),(1137,'2405702','Jardim do Seridó\r'),(1138,'2405801','João Câmara\r'),(1139,'2405900','João Dias\r'),(1140,'2406007','José da Penha\r'),(1141,'2406106','Jucurutu\r'),(1142,'2406155','Jundiá\r'),(1143,'2406205','Lagoa dAnta\r'),(1144,'2406304','Lagoa de Pedras\r'),(1145,'2406403','Lagoa de Velhos\r'),(1146,'2406502','Lagoa Nova\r'),(1147,'2406601','Lagoa Salgada\r'),(1148,'2406700','Lajes\r'),(1149,'2406809','Lajes Pintadas\r'),(1150,'2406908','Lucrécia\r'),(1151,'2407005','Luís Gomes\r'),(1152,'2407104','Macaíba\r'),(1153,'2407203','Macau\r'),(1154,'2407252','Major Sales\r'),(1155,'2407302','Marcelino Vieira\r'),(1156,'2407401','Martins\r'),(1157,'2407500','Maxaranguape\r'),(1158,'2407609','Messias Targino\r'),(1159,'2407708','Montanhas\r'),(1160,'2407807','Monte Alegre\r'),(1161,'2407906','Monte das Gameleiras\r'),(1162,'2408003','Mossoró\r'),(1163,'2408102','Natal\r'),(1164,'2408201','Nísia Floresta\r'),(1165,'2408300','Nova Cruz\r'),(1166,'2408409','Olho dÁgua do Borges\r'),(1167,'2408508','Ouro Branco\r'),(1168,'2408607','Paraná\r'),(1169,'2408706','Paraú\r'),(1170,'2408805','Parazinho\r'),(1171,'2408904','Parelhas\r'),(1172,'2403251','Parnamirim\r'),(1173,'2409100','Passa e Fica\r'),(1174,'2409209','Passagem\r'),(1175,'2409308','Patu\r'),(1176,'2409407','Pau dos Ferros\r'),(1177,'2409506','Pedra Grande\r'),(1178,'2409605','Pedra Preta\r'),(1179,'2409704','Pedro Avelino\r'),(1180,'2409803','Pedro Velho\r'),(1181,'2409902','Pendências\r'),(1182,'2410009','Pilões\r'),(1183,'2410108','Poço Branco\r'),(1184,'2410207','Portalegre\r'),(1185,'2410256','Porto do Mangue\r'),(1186,'2410405','Pureza\r'),(1187,'2410504','Rafael Fernandes\r'),(1188,'2410603','Rafael Godeiro\r'),(1189,'2410702','Riacho da Cruz\r'),(1190,'2410801','Riacho de Santana\r'),(1191,'2410900','Riachuelo\r'),(1192,'2408953','Rio do Fogo\r'),(1193,'2411007','Rodolfo Fernandes\r'),(1194,'2411106','Ruy Barbosa\r'),(1195,'2411205','Santa Cruz\r'),(1196,'2409332','Santa Maria\r'),(1197,'2411403','Santana do Matos\r'),(1198,'2411429','Santana do Seridó\r'),(1199,'2411502','Santo Antônio\r'),(1200,'2411601','São Bento do Norte\r'),(1201,'2411700','São Bento do Trairí\r'),(1202,'2411809','São Fernando\r'),(1203,'2411908','São Francisco do Oeste\r'),(1204,'2412005','São Gonçalo do Amarante\r'),(1205,'2412104','São João do Sabugi\r'),(1206,'2412203','São José de Mipibu\r'),(1207,'2412302','São José do Campestre\r'),(1208,'2412401','São José do Seridó\r'),(1209,'2412500','São Miguel\r'),(1210,'2412559','São Miguel do Gostoso\r'),(1211,'2412609','São Paulo do Potengi\r'),(1212,'2412708','São Pedro\r'),(1213,'2412807','São Rafael\r'),(1214,'2412906','São Tomé\r'),(1215,'2413003','São Vicente\r'),(1216,'2413102','Senador Elói de Souza\r'),(1217,'2413201','Senador Georgino Avelino\r'),(1218,'2410306','Serra Caiada\r'),(1219,'2413300','Serra de São Bento\r'),(1220,'2413359','Serra do Mel\r'),(1221,'2413409','Serra Negra do Norte\r'),(1222,'2413508','Serrinha\r'),(1223,'2413557','Serrinha dos Pintos\r'),(1224,'2413607','Severiano Melo\r'),(1225,'2413706','Sítio Novo\r'),(1226,'2413805','Taboleiro Grande\r'),(1227,'2413904','Taipu\r'),(1228,'2414001','Tangará\r'),(1229,'2414100','Tenente Ananias\r'),(1230,'2414159','Tenente Laurentino Cruz\r'),(1231,'2411056','Tibau\r'),(1232,'2414209','Tibau do Sul\r'),(1233,'2414308','Timbaúba dos Batistas\r'),(1234,'2414407','Touros\r'),(1235,'2414456','Triunfo Potiguar\r'),(1236,'2414506','Umarizal\r'),(1237,'2414605','Upanema\r'),(1238,'2414704','Várzea\r'),(1239,'2414753','Venha-Ver\r'),(1240,'2414803','Vera Cruz\r'),(1241,'2414902','Viçosa\r'),(1242,'2415008','Vila Flor\r'),(1243,'2500106','Água Branca\r'),(1244,'2500205','Aguiar\r'),(1245,'2500304','Alagoa Grande\r'),(1246,'2500403','Alagoa Nova\r'),(1247,'2500502','Alagoinha\r'),(1248,'2500536','Alcantil\r'),(1249,'2500577','Algodão de Jandaíra\r'),(1250,'2500601','Alhandra\r'),(1251,'2500734','Amparo\r'),(1252,'2500775','Aparecida\r'),(1253,'2500809','Araçagi\r'),(1254,'2500908','Arara\r'),(1255,'2501005','Araruna\r'),(1256,'2501104','Areia\r'),(1257,'2501153','Areia de Baraúnas\r'),(1258,'2501203','Areial\r'),(1259,'2501302','Aroeiras\r'),(1260,'2501351','Assunção\r'),(1261,'2501401','Baía da Traição\r'),(1262,'2501500','Bananeiras\r'),(1263,'2501534','Baraúna\r'),(1264,'2501609','Barra de Santa Rosa\r'),(1265,'2501575','Barra de Santana\r'),(1266,'2501708','Barra de São Miguel\r'),(1267,'2501807','Bayeux\r'),(1268,'2501906','Belém\r'),(1269,'2502003','Belém do Brejo do Cruz\r'),(1270,'2502052','Bernardino Batista\r'),(1271,'2502102','Boa Ventura\r'),(1272,'2502151','Boa Vista\r'),(1273,'2502201','Bom Jesus\r'),(1274,'2502300','Bom Sucesso\r'),(1275,'2502409','Bonito de Santa Fé\r'),(1276,'2502508','Boqueirão\r'),(1277,'2502706','Borborema\r'),(1278,'2502805','Brejo do Cruz\r'),(1279,'2502904','Brejo dos Santos\r'),(1280,'2503001','Caaporã\r'),(1281,'2503100','Cabaceiras\r'),(1282,'2503209','Cabedelo\r'),(1283,'2503308','Cachoeira dos Índios\r'),(1284,'2503407','Cacimba de Areia\r'),(1285,'2503506','Cacimba de Dentro\r'),(1286,'2503555','Cacimbas\r'),(1287,'2503605','Caiçara\r'),(1288,'2503704','Cajazeiras\r'),(1289,'2503753','Cajazeirinhas\r'),(1290,'2503803','Caldas Brandão\r'),(1291,'2503902','Camalaú\r'),(1292,'2504009','Campina Grande\r'),(1293,'2504033','Capim\r'),(1294,'2504074','Caraúbas\r'),(1295,'2504108','Carrapateira\r'),(1296,'2504157','Casserengue\r'),(1297,'2504207','Catingueira\r'),(1298,'2504306','Catolé do Rocha\r'),(1299,'2504355','Caturité\r'),(1300,'2504405','Conceição\r'),(1301,'2504504','Condado\r'),(1302,'2504603','Conde\r'),(1303,'2504702','Congo\r'),(1304,'2504801','Coremas\r'),(1305,'2504850','Coxixola\r'),(1306,'2504900','Cruz do Espírito Santo\r'),(1307,'2505006','Cubati\r'),(1308,'2505105','Cuité\r'),(1309,'2505238','Cuité de Mamanguape\r'),(1310,'2505204','Cuitegi\r'),(1311,'2505279','Curral de Cima\r'),(1312,'2505303','Curral Velho\r'),(1313,'2505352','Damião\r'),(1314,'2505402','Desterro\r'),(1315,'2505600','Diamante\r'),(1316,'2505709','Dona Inês\r'),(1317,'2505808','Duas Estradas\r'),(1318,'2505907','Emas\r'),(1319,'2506004','Esperança\r'),(1320,'2506103','Fagundes\r'),(1321,'2506202','Frei Martinho\r'),(1322,'2506251','Gado Bravo\r'),(1323,'2506301','Guarabira\r'),(1324,'2506400','Gurinhém\r'),(1325,'2506509','Gurjão\r'),(1326,'2506608','Ibiara\r'),(1327,'2502607','Igaracy\r'),(1328,'2506707','Imaculada\r'),(1329,'2506806','Ingá\r'),(1330,'2506905','Itabaiana\r'),(1331,'2507002','Itaporanga\r'),(1332,'2507101','Itapororoca\r'),(1333,'2507200','Itatuba\r'),(1334,'2507309','Jacaraú\r'),(1335,'2507408','Jericó\r'),(1336,'2507507','João Pessoa\r'),(1337,'2513653','Joca Claudino\r'),(1338,'2507606','Juarez Távora\r'),(1339,'2507705','Juazeirinho\r'),(1340,'2507804','Junco do Seridó\r'),(1341,'2507903','Juripiranga\r'),(1342,'2508000','Juru\r'),(1343,'2508109','Lagoa\r'),(1344,'2508208','Lagoa de Dentro\r'),(1345,'2508307','Lagoa Seca\r'),(1346,'2508406','Lastro\r'),(1347,'2508505','Livramento\r'),(1348,'2508554','Logradouro\r'),(1349,'2508604','Lucena\r'),(1350,'2508703','Mãe dÁgua\r'),(1351,'2508802','Malta\r'),(1352,'2508901','Mamanguape\r'),(1353,'2509008','Manaíra\r'),(1354,'2509057','Marcação\r'),(1355,'2509107','Mari\r'),(1356,'2509156','Marizópolis\r'),(1357,'2509206','Massaranduba\r'),(1358,'2509305','Mataraca\r'),(1359,'2509339','Matinhas\r'),(1360,'2509370','Mato Grosso\r'),(1361,'2509396','Maturéia\r'),(1362,'2509404','Mogeiro\r'),(1363,'2509503','Montadas\r'),(1364,'2509602','Monte Horebe\r'),(1365,'2509701','Monteiro\r'),(1366,'2509800','Mulungu\r'),(1367,'2509909','Natuba\r'),(1368,'2510006','Nazarezinho\r'),(1369,'2510105','Nova Floresta\r'),(1370,'2510204','Nova Olinda\r'),(1371,'2510303','Nova Palmeira\r'),(1372,'2510402','Olho dÁgua\r'),(1373,'2510501','Olivedos\r'),(1374,'2510600','Ouro Velho\r'),(1375,'2510659','Parari\r'),(1376,'2510709','Passagem\r'),(1377,'2510808','Patos\r'),(1378,'2510907','Paulista\r'),(1379,'2511004','Pedra Branca\r'),(1380,'2511103','Pedra Lavrada\r'),(1381,'2511202','Pedras de Fogo\r'),(1382,'2512721','Pedro Régis\r'),(1383,'2511301','Piancó\r'),(1384,'2511400','Picuí\r'),(1385,'2511509','Pilar\r'),(1386,'2511608','Pilões\r'),(1387,'2511707','Pilõezinhos\r'),(1388,'2511806','Pirpirituba\r'),(1389,'2511905','Pitimbu\r'),(1390,'2512002','Pocinhos\r'),(1391,'2512036','Poço Dantas\r'),(1392,'2512077','Poço de José de Moura\r'),(1393,'2512101','Pombal\r'),(1394,'2512200','Prata\r'),(1395,'2512309','Princesa Isabel\r'),(1396,'2512408','Puxinanã\r'),(1397,'2512507','Queimadas\r'),(1398,'2512606','Quixaba\r'),(1399,'2512705','Remígio\r'),(1400,'2512747','Riachão\r'),(1401,'2512754','Riachão do Bacamarte\r'),(1402,'2512762','Riachão do Poço\r'),(1403,'2512788','Riacho de Santo Antônio\r'),(1404,'2512804','Riacho dos Cavalos\r'),(1405,'2512903','Rio Tinto\r'),(1406,'2513000','Salgadinho\r'),(1407,'2513109','Salgado de São Félix\r'),(1408,'2513158','Santa Cecília\r'),(1409,'2513208','Santa Cruz\r'),(1410,'2513307','Santa Helena\r'),(1411,'2513356','Santa Inês\r'),(1412,'2513406','Santa Luzia\r'),(1413,'2513703','Santa Rita\r'),(1414,'2513802','Santa Teresinha\r'),(1415,'2513505','Santana de Mangueira\r'),(1416,'2513604','Santana dos Garrotes\r'),(1417,'2513851','Santo André\r'),(1418,'2513927','São Bentinho\r'),(1419,'2513901','São Bento\r'),(1420,'2513968','São Domingos\r'),(1421,'2513943','São Domingos do Cariri\r'),(1422,'2513984','São Francisco\r'),(1423,'2514008','São João do Cariri\r'),(1424,'2500700','São João do Rio do Peixe\r'),(1425,'2514107','São João do Tigre\r'),(1426,'2514206','São José da Lagoa Tapada\r'),(1427,'2514305','São José de Caiana\r'),(1428,'2514404','São José de Espinharas\r'),(1429,'2514503','São José de Piranhas\r'),(1430,'2514552','São José de Princesa\r'),(1431,'2514602','São José do Bonfim\r'),(1432,'2514651','São José do Brejo do Cruz\r'),(1433,'2514701','São José do Sabugi\r'),(1434,'2514800','São José dos Cordeiros\r'),(1435,'2514453','São José dos Ramos\r'),(1436,'2514909','São Mamede\r'),(1437,'2515005','São Miguel de Taipu\r'),(1438,'2515104','São Sebastião de Lagoa de Roça\r'),(1439,'2515203','São Sebastião do Umbuzeiro\r'),(1440,'2515401','São Vicente do Seridó\r'),(1441,'2515302','Sapé\r'),(1442,'2515500','Serra Branca\r'),(1443,'2515609','Serra da Raiz\r'),(1444,'2515708','Serra Grande\r'),(1445,'2515807','Serra Redonda\r'),(1446,'2515906','Serraria\r'),(1447,'2515930','Sertãozinho\r'),(1448,'2515971','Sobrado\r'),(1449,'2516003','Solânea\r'),(1450,'2516102','Soledade\r'),(1451,'2516151','Sossêgo\r'),(1452,'2516201','Sousa\r'),(1453,'2516300','Sumé\r'),(1454,'2516409','Tacima\r'),(1455,'2516508','Taperoá\r'),(1456,'2516607','Tavares\r'),(1457,'2516706','Teixeira\r'),(1458,'2516755','Tenório\r'),(1459,'2516805','Triunfo\r'),(1460,'2516904','Uiraúna\r'),(1461,'2517001','Umbuzeiro\r'),(1462,'2517100','Várzea\r'),(1463,'2517209','Vieirópolis\r'),(1464,'2505501','Vista Serrana\r'),(1465,'2517407','Zabelê\r'),(1466,'2600054','Abreu e Lima\r'),(1467,'2600104','Afogados da Ingazeira\r'),(1468,'2600203','Afrânio\r'),(1469,'2600302','Agrestina\r'),(1470,'2600401','Água Preta\r'),(1471,'2600500','Águas Belas\r'),(1472,'2600609','Alagoinha\r'),(1473,'2600708','Aliança\r'),(1474,'2600807','Altinho\r'),(1475,'2600906','Amaraji\r'),(1476,'2601003','Angelim\r'),(1477,'2601052','Araçoiaba\r'),(1478,'2601102','Araripina\r'),(1479,'2601201','Arcoverde\r'),(1480,'2601300','Barra de Guabiraba\r'),(1481,'2601409','Barreiros\r'),(1482,'2601508','Belém de Maria\r'),(1483,'2601607','Belém do São Francisco\r'),(1484,'2601706','Belo Jardim\r'),(1485,'2601805','Betânia\r'),(1486,'2601904','Bezerros\r'),(1487,'2602001','Bodocó\r'),(1488,'2602100','Bom Conselho\r'),(1489,'2602209','Bom Jardim\r'),(1490,'2602308','Bonito\r'),(1491,'2602407','Brejão\r'),(1492,'2602506','Brejinho\r'),(1493,'2602605','Brejo da Madre de Deus\r'),(1494,'2602704','Buenos Aires\r'),(1495,'2602803','Buíque\r'),(1496,'2602902','Cabo de Santo Agostinho\r'),(1497,'2603009','Cabrobó\r'),(1498,'2603108','Cachoeirinha\r'),(1499,'2603207','Caetés\r'),(1500,'2603306','Calçado\r'),(1501,'2603405','Calumbi\r'),(1502,'2603454','Camaragibe\r'),(1503,'2603504','Camocim de São Félix\r'),(1504,'2603603','Camutanga\r'),(1505,'2603702','Canhotinho\r'),(1506,'2603801','Capoeiras\r'),(1507,'2603900','Carnaíba\r'),(1508,'2603926','Carnaubeira da Penha\r'),(1509,'2604007','Carpina\r'),(1510,'2604106','Caruaru\r'),(1511,'2604155','Casinhas\r'),(1512,'2604205','Catende\r'),(1513,'2604304','Cedro\r'),(1514,'2604403','Chã de Alegria\r'),(1515,'2604502','Chã Grande\r'),(1516,'2604601','Condado\r'),(1517,'2604700','Correntes\r'),(1518,'2604809','Cortês\r'),(1519,'2604908','Cumaru\r'),(1520,'2605004','Cupira\r'),(1521,'2605103','Custódia\r'),(1522,'2605152','Dormentes\r'),(1523,'2605202','Escada\r'),(1524,'2605301','Exu\r'),(1525,'2605400','Feira Nova\r'),(1526,'2605459','Fernando de Noronha\r'),(1527,'2605509','Ferreiros\r'),(1528,'2605608','Flores\r'),(1529,'2605707','Floresta\r'),(1530,'2605806','Frei Miguelinho\r'),(1531,'2605905','Gameleira\r'),(1532,'2606002','Garanhuns\r'),(1533,'2606101','Glória do Goitá\r'),(1534,'2606200','Goiana\r'),(1535,'2606309','Granito\r'),(1536,'2606408','Gravatá\r'),(1537,'2606507','Iati\r'),(1538,'2606606','Ibimirim\r'),(1539,'2606705','Ibirajuba\r'),(1540,'2606804','Igarassu\r'),(1541,'2606903','Iguaracy\r'),(1542,'2607604','Ilha de Itamaracá\r'),(1543,'2607000','Inajá\r'),(1544,'2607109','Ingazeira\r'),(1545,'2607208','Ipojuca\r'),(1546,'2607307','Ipubi\r'),(1547,'2607406','Itacuruba\r'),(1548,'2607505','Itaíba\r'),(1549,'2607653','Itambé\r'),(1550,'2607703','Itapetim\r'),(1551,'2607752','Itapissuma\r'),(1552,'2607802','Itaquitinga\r'),(1553,'2607901','Jaboatão dos Guararapes\r'),(1554,'2607950','Jaqueira\r'),(1555,'2608008','Jataúba\r'),(1556,'2608057','Jatobá\r'),(1557,'2608107','João Alfredo\r'),(1558,'2608206','Joaquim Nabuco\r'),(1559,'2608255','Jucati\r'),(1560,'2608305','Jupi\r'),(1561,'2608404','Jurema\r'),(1562,'2608503','Lagoa de Itaenga\r'),(1563,'2608453','Lagoa do Carro\r'),(1564,'2608602','Lagoa do Ouro\r'),(1565,'2608701','Lagoa dos Gatos\r'),(1566,'2608750','Lagoa Grande\r'),(1567,'2608800','Lajedo\r'),(1568,'2608909','Limoeiro\r'),(1569,'2609006','Macaparana\r'),(1570,'2609105','Machados\r'),(1571,'2609154','Manari\r'),(1572,'2609204','Maraial\r'),(1573,'2609303','Mirandiba\r'),(1574,'2614303','Moreilândia\r'),(1575,'2609402','Moreno\r'),(1576,'2609501','Nazaré da Mata\r'),(1577,'2609600','Olinda\r'),(1578,'2609709','Orobó\r'),(1579,'2609808','Orocó\r'),(1580,'2609907','Ouricuri\r'),(1581,'2610004','Palmares\r'),(1582,'2610103','Palmeirina\r'),(1583,'2610202','Panelas\r'),(1584,'2610301','Paranatama\r'),(1585,'2610400','Parnamirim\r'),(1586,'2610509','Passira\r'),(1587,'2610608','Paudalho\r'),(1588,'2610707','Paulista\r'),(1589,'2610806','Pedra\r'),(1590,'2610905','Pesqueira\r'),(1591,'2611002','Petrolândia\r'),(1592,'2611101','Petrolina\r'),(1593,'2611200','Poção\r'),(1594,'2611309','Pombos\r'),(1595,'2611408','Primavera\r'),(1596,'2611507','Quipapá\r'),(1597,'2611533','Quixaba\r'),(1598,'2611606','Recife\r'),(1599,'2611705','Riacho das Almas\r'),(1600,'2611804','Ribeirão\r'),(1601,'2611903','Rio Formoso\r'),(1602,'2612000','Sairé\r'),(1603,'2612109','Salgadinho\r'),(1604,'2612208','Salgueiro\r'),(1605,'2612307','Saloá\r'),(1606,'2612406','Sanharó\r'),(1607,'2612455','Santa Cruz\r'),(1608,'2612471','Santa Cruz da Baixa Verde\r'),(1609,'2612505','Santa Cruz do Capibaribe\r'),(1610,'2612554','Santa Filomena\r'),(1611,'2612604','Santa Maria da Boa Vista\r'),(1612,'2612703','Santa Maria do Cambucá\r'),(1613,'2612802','Santa Terezinha\r'),(1614,'2612901','São Benedito do Sul\r'),(1615,'2613008','São Bento do Una\r'),(1616,'2613107','São Caitano\r'),(1617,'2613206','São João\r'),(1618,'2613305','São Joaquim do Monte\r'),(1619,'2613404','São José da Coroa Grande\r'),(1620,'2613503','São José do Belmonte\r'),(1621,'2613602','São José do Egito\r'),(1622,'2613701','São Lourenço da Mata\r'),(1623,'2613800','São Vicente Férrer\r'),(1624,'2613909','Serra Talhada\r'),(1625,'2614006','Serrita\r'),(1626,'2614105','Sertânia\r'),(1627,'2614204','Sirinhaém\r'),(1628,'2614402','Solidão\r'),(1629,'2614501','Surubim\r'),(1630,'2614600','Tabira\r'),(1631,'2614709','Tacaimbó\r'),(1632,'2614808','Tacaratu\r'),(1633,'2614857','Tamandaré\r'),(1634,'2615003','Taquaritinga do Norte\r'),(1635,'2615102','Terezinha\r'),(1636,'2615201','Terra Nova\r'),(1637,'2615300','Timbaúba\r'),(1638,'2615409','Toritama\r'),(1639,'2615508','Tracunhaém\r'),(1640,'2615607','Trindade\r'),(1641,'2615706','Triunfo\r'),(1642,'2615805','Tupanatinga\r'),(1643,'2615904','Tuparetama\r'),(1644,'2616001','Venturosa\r'),(1645,'2616100','Verdejante\r'),(1646,'2616183','Vertente do Lério\r'),(1647,'2616209','Vertentes\r'),(1648,'2616308','Vicência\r'),(1649,'2616407','Vitória de Santo Antão\r'),(1650,'2616506','Xexéu\r'),(1651,'2700102','Água Branca\r'),(1652,'2700201','Anadia\r'),(1653,'2700300','Arapiraca\r'),(1654,'2700409','Atalaia\r'),(1655,'2700508','Barra de Santo Antônio\r'),(1656,'2700607','Barra de São Miguel\r'),(1657,'2700706','Batalha\r'),(1658,'2700805','Belém\r'),(1659,'2700904','Belo Monte\r'),(1660,'2701001','Boca da Mata\r'),(1661,'2701100','Branquinha\r'),(1662,'2701209','Cacimbinhas\r'),(1663,'2701308','Cajueiro\r'),(1664,'2701357','Campestre\r'),(1665,'2701407','Campo Alegre\r'),(1666,'2701506','Campo Grande\r'),(1667,'2701605','Canapi\r'),(1668,'2701704','Capela\r'),(1669,'2701803','Carneiros\r'),(1670,'2701902','Chã Preta\r'),(1671,'2702009','Coité do Nóia\r'),(1672,'2702108','Colônia Leopoldina\r'),(1673,'2702207','Coqueiro Seco\r'),(1674,'2702306','Coruripe\r'),(1675,'2702355','Craíbas\r'),(1676,'2702405','Delmiro Gouveia\r'),(1677,'2702504','Dois Riachos\r'),(1678,'2702553','Estrela de Alagoas\r'),(1679,'2702603','Feira Grande\r'),(1680,'2702702','Feliz Deserto\r'),(1681,'2702801','Flexeiras\r'),(1682,'2702900','Girau do Ponciano\r'),(1683,'2703007','Ibateguara\r'),(1684,'2703106','Igaci\r'),(1685,'2703205','Igreja Nova\r'),(1686,'2703304','Inhapi\r'),(1687,'2703403','Jacaré dos Homens\r'),(1688,'2703502','Jacuípe\r'),(1689,'2703601','Japaratinga\r'),(1690,'2703700','Jaramataia\r'),(1691,'2703759','Jequiá da Praia\r'),(1692,'2703809','Joaquim Gomes\r'),(1693,'2703908','Jundiá\r'),(1694,'2704005','Junqueiro\r'),(1695,'2704104','Lagoa da Canoa\r'),(1696,'2704203','Limoeiro de Anadia\r'),(1697,'2704302','Maceió\r'),(1698,'2704401','Major Isidoro\r'),(1699,'2704906','Mar Vermelho\r'),(1700,'2704500','Maragogi\r'),(1701,'2704609','Maravilha\r'),(1702,'2704708','Marechal Deodoro\r'),(1703,'2704807','Maribondo\r'),(1704,'2705002','Mata Grande\r'),(1705,'2705101','Matriz de Camaragibe\r'),(1706,'2705200','Messias\r'),(1707,'2705309','Minador do Negrão\r'),(1708,'2705408','Monteirópolis\r'),(1709,'2705507','Murici\r'),(1710,'2705606','Novo Lino\r'),(1711,'2705705','Olho dÁgua das Flores\r'),(1712,'2705804','Olho dÁgua do Casado\r'),(1713,'2705903','Olho dÁgua Grande\r'),(1714,'2706000','Olivença\r'),(1715,'2706109','Ouro Branco\r'),(1716,'2706208','Palestina\r'),(1717,'2706307','Palmeira dos Índios\r'),(1718,'2706406','Pão de Açúcar\r'),(1719,'2706422','Pariconha\r'),(1720,'2706448','Paripueira\r'),(1721,'2706505','Passo de Camaragibe\r'),(1722,'2706604','Paulo Jacinto\r'),(1723,'2706703','Penedo\r'),(1724,'2706802','Piaçabuçu\r'),(1725,'2706901','Pilar\r'),(1726,'2707008','Pindoba\r'),(1727,'2707107','Piranhas\r'),(1728,'2707206','Poço das Trincheiras\r'),(1729,'2707305','Porto Calvo\r'),(1730,'2707404','Porto de Pedras\r'),(1731,'2707503','Porto Real do Colégio\r'),(1732,'2707602','Quebrangulo\r'),(1733,'2707701','Rio Largo\r'),(1734,'2707800','Roteiro\r'),(1735,'2707909','Santa Luzia do Norte\r'),(1736,'2708006','Santana do Ipanema\r'),(1737,'2708105','Santana do Mundaú\r'),(1738,'2708204','São Brás\r'),(1739,'2708303','São José da Laje\r'),(1740,'2708402','São José da Tapera\r'),(1741,'2708501','São Luís do Quitunde\r'),(1742,'2708600','São Miguel dos Campos\r'),(1743,'2708709','São Miguel dos Milagres\r'),(1744,'2708808','São Sebastião\r'),(1745,'2708907','Satuba\r'),(1746,'2708956','Senador Rui Palmeira\r'),(1747,'2709004','Tanque dArca\r'),(1748,'2709103','Taquarana\r'),(1749,'2709152','Teotônio Vilela\r'),(1750,'2709202','Traipu\r'),(1751,'2709301','União dos Palmares\r'),(1752,'2709400','Viçosa\r'),(1753,'2800100','Amparo de São Francisco\r'),(1754,'2800209','Aquidabã\r'),(1755,'2800308','Aracaju\r'),(1756,'2800407','Arauá\r'),(1757,'2800506','Areia Branca\r'),(1758,'2800605','Barra dos Coqueiros\r'),(1759,'2800670','Boquim\r'),(1760,'2800704','Brejo Grande\r'),(1761,'2801009','Campo do Brito\r'),(1762,'2801108','Canhoba\r'),(1763,'2801207','Canindé de São Francisco\r'),(1764,'2801306','Capela\r'),(1765,'2801405','Carira\r'),(1766,'2801504','Carmópolis\r'),(1767,'2801603','Cedro de São João\r'),(1768,'2801702','Cristinápolis\r'),(1769,'2801900','Cumbe\r'),(1770,'2802007','Divina Pastora\r'),(1771,'2802106','Estância\r'),(1772,'2802205','Feira Nova\r'),(1773,'2802304','Frei Paulo\r'),(1774,'2802403','Gararu\r'),(1775,'2802502','General Maynard\r'),(1776,'2802601','Gracho Cardoso\r'),(1777,'2802700','Ilha das Flores\r'),(1778,'2802809','Indiaroba\r'),(1779,'2802908','Itabaiana\r'),(1780,'2803005','Itabaianinha\r'),(1781,'2803104','Itabi\r'),(1782,'2803203','Itaporanga dAjuda\r'),(1783,'2803302','Japaratuba\r'),(1784,'2803401','Japoatã\r'),(1785,'2803500','Lagarto\r'),(1786,'2803609','Laranjeiras\r'),(1787,'2803708','Macambira\r'),(1788,'2803807','Malhada dos Bois\r'),(1789,'2803906','Malhador\r'),(1790,'2804003','Maruim\r'),(1791,'2804102','Moita Bonita\r'),(1792,'2804201','Monte Alegre de Sergipe\r'),(1793,'2804300','Muribeca\r'),(1794,'2804409','Neópolis\r'),(1795,'2804458','Nossa Senhora Aparecida\r'),(1796,'2804508','Nossa Senhora da Glória\r'),(1797,'2804607','Nossa Senhora das Dores\r'),(1798,'2804706','Nossa Senhora de Lourdes\r'),(1799,'2804805','Nossa Senhora do Socorro\r'),(1800,'2804904','Pacatuba\r'),(1801,'2805000','Pedra Mole\r'),(1802,'2805109','Pedrinhas\r'),(1803,'2805208','Pinhão\r'),(1804,'2805307','Pirambu\r'),(1805,'2805406','Poço Redondo\r'),(1806,'2805505','Poço Verde\r'),(1807,'2805604','Porto da Folha\r'),(1808,'2805703','Propriá\r'),(1809,'2805802','Riachão do Dantas\r'),(1810,'2805901','Riachuelo\r'),(1811,'2806008','Ribeirópolis\r'),(1812,'2806107','Rosário do Catete\r'),(1813,'2806206','Salgado\r'),(1814,'2806305','Santa Luzia do Itanhy\r'),(1815,'2806503','Santa Rosa de Lima\r'),(1816,'2806404','Santana do São Francisco\r'),(1817,'2806602','Santo Amaro das Brotas\r'),(1818,'2806701','São Cristóvão\r'),(1819,'2806800','São Domingos\r'),(1820,'2806909','São Francisco\r'),(1821,'2807006','São Miguel do Aleixo\r'),(1822,'2807105','Simão Dias\r'),(1823,'2807204','Siriri\r'),(1824,'2807303','Telha\r'),(1825,'2807402','Tobias Barreto\r'),(1826,'2807501','Tomar do Geru\r'),(1827,'2807600','Umbaúba\r'),(1828,'2900108','Abaíra\r'),(1829,'2900207','Abaré\r'),(1830,'2900306','Acajutiba\r'),(1831,'2900355','Adustina\r'),(1832,'2900405','Água Fria\r'),(1833,'2900603','Aiquara\r'),(1834,'2900702','Alagoinhas\r'),(1835,'2900801','Alcobaça\r'),(1836,'2900900','Almadina\r'),(1837,'2901007','Amargosa\r'),(1838,'2901106','Amélia Rodrigues\r'),(1839,'2901155','América Dourada\r'),(1840,'2901205','Anagé\r'),(1841,'2901304','Andaraí\r'),(1842,'2901353','Andorinha\r'),(1843,'2901403','Angical\r'),(1844,'2901502','Anguera\r'),(1845,'2901601','Antas\r'),(1846,'2901700','Antônio Cardoso\r'),(1847,'2901809','Antônio Gonçalves\r'),(1848,'2901908','Aporá\r'),(1849,'2901957','Apuarema\r'),(1850,'2902054','Araçás\r'),(1851,'2902005','Aracatu\r'),(1852,'2902104','Araci\r'),(1853,'2902203','Aramari\r'),(1854,'2902252','Arataca\r'),(1855,'2902302','Aratuípe\r'),(1856,'2902401','Aurelino Leal\r'),(1857,'2902500','Baianópolis\r'),(1858,'2902609','Baixa Grande\r'),(1859,'2902658','Banzaê\r'),(1860,'2902708','Barra\r'),(1861,'2902807','Barra da Estiva\r'),(1862,'2902906','Barra do Choça\r'),(1863,'2903003','Barra do Mendes\r'),(1864,'2903102','Barra do Rocha\r'),(1865,'2903201','Barreiras\r'),(1866,'2903235','Barro Alto\r'),(1867,'2903300','Barro Preto\r'),(1868,'2903276','Barrocas\r'),(1869,'2903409','Belmonte\r'),(1870,'2903508','Belo Campo\r'),(1871,'2903607','Biritinga\r'),(1872,'2903706','Boa Nova\r'),(1873,'2903805','Boa Vista do Tupim\r'),(1874,'2903904','Bom Jesus da Lapa\r'),(1875,'2903953','Bom Jesus da Serra\r'),(1876,'2904001','Boninal\r'),(1877,'2904050','Bonito\r'),(1878,'2904100','Boquira\r'),(1879,'2904209','Botuporã\r'),(1880,'2904308','Brejões\r'),(1881,'2904407','Brejolândia\r'),(1882,'2904506','Brotas de Macaúbas\r'),(1883,'2904605','Brumado\r'),(1884,'2904704','Buerarema\r'),(1885,'2904753','Buritirama\r'),(1886,'2904803','Caatiba\r'),(1887,'2904852','Cabaceiras do Paraguaçu\r'),(1888,'2904902','Cachoeira\r'),(1889,'2905008','Caculé\r'),(1890,'2905107','Caém\r'),(1891,'2905156','Caetanos\r'),(1892,'2905206','Caetité\r'),(1893,'2905305','Cafarnaum\r'),(1894,'2905404','Cairu\r'),(1895,'2905503','Caldeirão Grande\r'),(1896,'2905602','Camacan\r'),(1897,'2905701','Camaçari\r'),(1898,'2905800','Camamu\r'),(1899,'2905909','Campo Alegre de Lourdes\r'),(1900,'2906006','Campo Formoso\r'),(1901,'2906105','Canápolis\r'),(1902,'2906204','Canarana\r'),(1903,'2906303','Canavieiras\r'),(1904,'2906402','Candeal\r'),(1905,'2906501','Candeias\r'),(1906,'2906600','Candiba\r'),(1907,'2906709','Cândido Sales\r'),(1908,'2906808','Cansanção\r'),(1909,'2906824','Canudos\r'),(1910,'2906857','Capela do Alto Alegre\r'),(1911,'2906873','Capim Grosso\r'),(1912,'2906899','Caraíbas\r'),(1913,'2906907','Caravelas\r'),(1914,'2907004','Cardeal da Silva\r'),(1915,'2907103','Carinhanha\r'),(1916,'2907202','Casa Nova\r'),(1917,'2907301','Castro Alves\r'),(1918,'2907400','Catolândia\r'),(1919,'2907509','Catu\r'),(1920,'2907558','Caturama\r'),(1921,'2907608','Central\r'),(1922,'2907707','Chorrochó\r'),(1923,'2907806','Cícero Dantas\r'),(1924,'2907905','Cipó\r'),(1925,'2908002','Coaraci\r'),(1926,'2908101','Cocos\r'),(1927,'2908200','Conceição da Feira\r'),(1928,'2908309','Conceição do Almeida\r'),(1929,'2908408','Conceição do Coité\r'),(1930,'2908507','Conceição do Jacuípe\r'),(1931,'2908606','Conde\r'),(1932,'2908705','Condeúba\r'),(1933,'2908804','Contendas do Sincorá\r'),(1934,'2908903','Coração de Maria\r'),(1935,'2909000','Cordeiros\r'),(1936,'2909109','Coribe\r'),(1937,'2909208','Coronel João Sá\r'),(1938,'2909307','Correntina\r'),(1939,'2909406','Cotegipe\r'),(1940,'2909505','Cravolândia\r'),(1941,'2909604','Crisópolis\r'),(1942,'2909703','Cristópolis\r'),(1943,'2909802','Cruz das Almas\r'),(1944,'2909901','Curaçá\r'),(1945,'2910008','Dário Meira\r'),(1946,'2910057','Dias dÁvila\r'),(1947,'2910107','Dom Basílio\r'),(1948,'2910206','Dom Macedo Costa\r'),(1949,'2910305','Elísio Medrado\r'),(1950,'2910404','Encruzilhada\r'),(1951,'2910503','Entre Rios\r'),(1952,'2900504','Érico Cardoso\r'),(1953,'2910602','Esplanada\r'),(1954,'2910701','Euclides da Cunha\r'),(1955,'2910727','Eunápolis\r'),(1956,'2910750','Fátima\r'),(1957,'2910776','Feira da Mata\r'),(1958,'2910800','Feira de Santana\r'),(1959,'2910859','Filadélfia\r'),(1960,'2910909','Firmino Alves\r'),(1961,'2911006','Floresta Azul\r'),(1962,'2911105','Formosa do Rio Preto\r'),(1963,'2911204','Gandu\r'),(1964,'2911253','Gavião\r'),(1965,'2911303','Gentio do Ouro\r'),(1966,'2911402','Glória\r'),(1967,'2911501','Gongogi\r'),(1968,'2911600','Governador Mangabeira\r'),(1969,'2911659','Guajeru\r'),(1970,'2911709','Guanambi\r'),(1971,'2911808','Guaratinga\r'),(1972,'2911857','Heliópolis\r'),(1973,'2911907','Iaçu\r'),(1974,'2912004','Ibiassucê\r'),(1975,'2912103','Ibicaraí\r'),(1976,'2912202','Ibicoara\r'),(1977,'2912301','Ibicuí\r'),(1978,'2912400','Ibipeba\r'),(1979,'2912509','Ibipitanga\r'),(1980,'2912608','Ibiquera\r'),(1981,'2912707','Ibirapitanga\r'),(1982,'2912806','Ibirapuã\r'),(1983,'2912905','Ibirataia\r'),(1984,'2913002','Ibitiara\r'),(1985,'2913101','Ibititá\r'),(1986,'2913200','Ibotirama\r'),(1987,'2913309','Ichu\r'),(1988,'2913408','Igaporã\r'),(1989,'2913457','Igrapiúna\r'),(1990,'2913507','Iguaí\r'),(1991,'2913606','Ilhéus\r'),(1992,'2913705','Inhambupe\r'),(1993,'2913804','Ipecaetá\r'),(1994,'2913903','Ipiaú\r'),(1995,'2914000','Ipirá\r'),(1996,'2914109','Ipupiara\r'),(1997,'2914208','Irajuba\r'),(1998,'2914307','Iramaia\r'),(1999,'2914406','Iraquara\r'),(2000,'2914505','Irará\r'),(2001,'2914604','Irecê\r'),(2002,'2914653','Itabela\r'),(2003,'2914703','Itaberaba\r'),(2004,'2914802','Itabuna\r'),(2005,'2914901','Itacaré\r'),(2006,'2915007','Itaeté\r'),(2007,'2915106','Itagi\r'),(2008,'2915205','Itagibá\r'),(2009,'2915304','Itagimirim\r'),(2010,'2915353','Itaguaçu da Bahia\r'),(2011,'2915403','Itaju do Colônia\r'),(2012,'2915502','Itajuípe\r'),(2013,'2915601','Itamaraju\r'),(2014,'2915700','Itamari\r'),(2015,'2915809','Itambé\r'),(2016,'2915908','Itanagra\r'),(2017,'2916005','Itanhém\r'),(2018,'2916104','Itaparica\r'),(2019,'2916203','Itapé\r'),(2020,'2916302','Itapebi\r'),(2021,'2916401','Itapetinga\r'),(2022,'2916500','Itapicuru\r'),(2023,'2916609','Itapitanga\r'),(2024,'2916708','Itaquara\r'),(2025,'2916807','Itarantim\r'),(2026,'2916856','Itatim\r'),(2027,'2916906','Itiruçu\r'),(2028,'2917003','Itiúba\r'),(2029,'2917102','Itororó\r'),(2030,'2917201','Ituaçu\r'),(2031,'2917300','Ituberá\r'),(2032,'2917334','Iuiu\r'),(2033,'2917359','Jaborandi\r'),(2034,'2917409','Jacaraci\r'),(2035,'2917508','Jacobina\r'),(2036,'2917607','Jaguaquara\r'),(2037,'2917706','Jaguarari\r'),(2038,'2917805','Jaguaripe\r'),(2039,'2917904','Jandaíra\r'),(2040,'2918001','Jequié\r'),(2041,'2918100','Jeremoabo\r'),(2042,'2918209','Jiquiriçá\r'),(2043,'2918308','Jitaúna\r'),(2044,'2918357','João Dourado\r'),(2045,'2918407','Juazeiro\r'),(2046,'2918456','Jucuruçu\r'),(2047,'2918506','Jussara\r'),(2048,'2918555','Jussari\r'),(2049,'2918605','Jussiape\r'),(2050,'2918704','Lafaiete Coutinho\r'),(2051,'2918753','Lagoa Real\r'),(2052,'2918803','Laje\r'),(2053,'2918902','Lajedão\r'),(2054,'2919009','Lajedinho\r'),(2055,'2919058','Lajedo do Tabocal\r'),(2056,'2919108','Lamarão\r'),(2057,'2919157','Lapão\r'),(2058,'2919207','Lauro de Freitas\r'),(2059,'2919306','Lençóis\r'),(2060,'2919405','Licínio de Almeida\r'),(2061,'2919504','Livramento de Nossa Senhora\r'),(2062,'2919553','Luís Eduardo Magalhães\r'),(2063,'2919603','Macajuba\r'),(2064,'2919702','Macarani\r'),(2065,'2919801','Macaúbas\r'),(2066,'2919900','Macururé\r'),(2067,'2919926','Madre de Deus\r'),(2068,'2919959','Maetinga\r'),(2069,'2920007','Maiquinique\r'),(2070,'2920106','Mairi\r'),(2071,'2920205','Malhada\r'),(2072,'2920304','Malhada de Pedras\r'),(2073,'2920403','Manoel Vitorino\r'),(2074,'2920452','Mansidão\r'),(2075,'2920502','Maracás\r'),(2076,'2920601','Maragogipe\r'),(2077,'2920700','Maraú\r'),(2078,'2920809','Marcionílio Souza\r'),(2079,'2920908','Mascote\r'),(2080,'2921005','Mata de São João\r'),(2081,'2921054','Matina\r'),(2082,'2921104','Medeiros Neto\r'),(2083,'2921203','Miguel Calmon\r'),(2084,'2921302','Milagres\r'),(2085,'2921401','Mirangaba\r'),(2086,'2921450','Mirante\r'),(2087,'2921500','Monte Santo\r'),(2088,'2921609','Morpará\r'),(2089,'2921708','Morro do Chapéu\r'),(2090,'2921807','Mortugaba\r'),(2091,'2921906','Mucugê\r'),(2092,'2922003','Mucuri\r'),(2093,'2922052','Mulungu do Morro\r'),(2094,'2922102','Mundo Novo\r'),(2095,'2922201','Muniz Ferreira\r'),(2096,'2922250','Muquém do São Francisco\r'),(2097,'2922300','Muritiba\r'),(2098,'2922409','Mutuípe\r'),(2099,'2922508','Nazaré\r'),(2100,'2922607','Nilo Peçanha\r'),(2101,'2922656','Nordestina\r'),(2102,'2922706','Nova Canaã\r'),(2103,'2922730','Nova Fátima\r'),(2104,'2922755','Nova Ibiá\r'),(2105,'2922805','Nova Itarana\r'),(2106,'2922854','Nova Redenção\r'),(2107,'2922904','Nova Soure\r'),(2108,'2923001','Nova Viçosa\r'),(2109,'2923035','Novo Horizonte\r'),(2110,'2923050','Novo Triunfo\r'),(2111,'2923100','Olindina\r'),(2112,'2923209','Oliveira dos Brejinhos\r'),(2113,'2923308','Ouriçangas\r'),(2114,'2923357','Ourolândia\r'),(2115,'2923407','Palmas de Monte Alto\r'),(2116,'2923506','Palmeiras\r'),(2117,'2923605','Paramirim\r'),(2118,'2923704','Paratinga\r'),(2119,'2923803','Paripiranga\r'),(2120,'2923902','Pau Brasil\r'),(2121,'2924009','Paulo Afonso\r'),(2122,'2924058','Pé de Serra\r'),(2123,'2924108','Pedrão\r'),(2124,'2924207','Pedro Alexandre\r'),(2125,'2924306','Piatã\r'),(2126,'2924405','Pilão Arcado\r'),(2127,'2924504','Pindaí\r'),(2128,'2924603','Pindobaçu\r'),(2129,'2924652','Pintadas\r'),(2130,'2924678','Piraí do Norte\r'),(2131,'2924702','Piripá\r'),(2132,'2924801','Piritiba\r'),(2133,'2924900','Planaltino\r'),(2134,'2925006','Planalto\r'),(2135,'2925105','Poções\r'),(2136,'2925204','Pojuca\r'),(2137,'2925253','Ponto Novo\r'),(2138,'2925303','Porto Seguro\r'),(2139,'2925402','Potiraguá\r'),(2140,'2925501','Prado\r'),(2141,'2925600','Presidente Dutra\r'),(2142,'2925709','Presidente Jânio Quadros\r'),(2143,'2925758','Presidente Tancredo Neves\r'),(2144,'2925808','Queimadas\r'),(2145,'2925907','Quijingue\r'),(2146,'2925931','Quixabeira\r'),(2147,'2925956','Rafael Jambeiro\r'),(2148,'2926004','Remanso\r'),(2149,'2926103','Retirolândia\r'),(2150,'2926202','Riachão das Neves\r'),(2151,'2926301','Riachão do Jacuípe\r'),(2152,'2926400','Riacho de Santana\r'),(2153,'2926509','Ribeira do Amparo\r'),(2154,'2926608','Ribeira do Pombal\r'),(2155,'2926657','Ribeirão do Largo\r'),(2156,'2926707','Rio de Contas\r'),(2157,'2926806','Rio do Antônio\r'),(2158,'2926905','Rio do Pires\r'),(2159,'2927002','Rio Real\r'),(2160,'2927101','Rodelas\r'),(2161,'2927200','Ruy Barbosa\r'),(2162,'2927309','Salinas da Margarida\r'),(2163,'2927408','Salvador\r'),(2164,'2927507','Santa Bárbara\r'),(2165,'2927606','Santa Brígida\r'),(2166,'2927705','Santa Cruz Cabrália\r'),(2167,'2927804','Santa Cruz da Vitória\r'),(2168,'2927903','Santa Inês\r'),(2169,'2928059','Santa Luzia\r'),(2170,'2928109','Santa Maria da Vitória\r'),(2171,'2928406','Santa Rita de Cássia\r'),(2172,'2928505','Santa Terezinha\r'),(2173,'2928000','Santaluz\r'),(2174,'2928208','Santana\r'),(2175,'2928307','Santanópolis\r'),(2176,'2928604','Santo Amaro\r'),(2177,'2928703','Santo Antônio de Jesus\r'),(2178,'2928802','Santo Estêvão\r'),(2179,'2928901','São Desidério\r'),(2180,'2928950','São Domingos\r'),(2181,'2929107','São Felipe\r'),(2182,'2929008','São Félix\r'),(2183,'2929057','São Félix do Coribe\r'),(2184,'2929206','São Francisco do Conde\r'),(2185,'2929255','São Gabriel\r'),(2186,'2929305','São Gonçalo dos Campos\r'),(2187,'2929354','São José da Vitória\r'),(2188,'2929370','São José do Jacuípe\r'),(2189,'2929404','São Miguel das Matas\r'),(2190,'2929503','São Sebastião do Passé\r'),(2191,'2929602','Sapeaçu\r'),(2192,'2929701','Sátiro Dias\r'),(2193,'2929750','Saubara\r'),(2194,'2929800','Saúde\r'),(2195,'2929909','Seabra\r'),(2196,'2930006','Sebastião Laranjeiras\r'),(2197,'2930105','Senhor do Bonfim\r'),(2198,'2930204','Sento Sé\r'),(2199,'2930154','Serra do Ramalho\r'),(2200,'2930303','Serra Dourada\r'),(2201,'2930402','Serra Preta\r'),(2202,'2930501','Serrinha\r'),(2203,'2930600','Serrolândia\r'),(2204,'2930709','Simões Filho\r'),(2205,'2930758','Sítio do Mato\r'),(2206,'2930766','Sítio do Quinto\r'),(2207,'2930774','Sobradinho\r'),(2208,'2930808','Souto Soares\r'),(2209,'2930907','Tabocas do Brejo Velho\r'),(2210,'2931004','Tanhaçu\r'),(2211,'2931053','Tanque Novo\r'),(2212,'2931103','Tanquinho\r'),(2213,'2931202','Taperoá\r'),(2214,'2931301','Tapiramutá\r'),(2215,'2931350','Teixeira de Freitas\r'),(2216,'2931400','Teodoro Sampaio\r'),(2217,'2931509','Teofilândia\r'),(2218,'2931608','Teolândia\r'),(2219,'2931707','Terra Nova\r'),(2220,'2931806','Tremedal\r'),(2221,'2931905','Tucano\r'),(2222,'2932002','Uauá\r'),(2223,'2932101','Ubaíra\r'),(2224,'2932200','Ubaitaba\r'),(2225,'2932309','Ubatã\r'),(2226,'2932408','Uibaí\r'),(2227,'2932457','Umburanas\r'),(2228,'2932507','Una\r'),(2229,'2932606','Urandi\r'),(2230,'2932705','Uruçuca\r'),(2231,'2932804','Utinga\r'),(2232,'2932903','Valença\r'),(2233,'2933000','Valente\r'),(2234,'2933059','Várzea da Roça\r'),(2235,'2933109','Várzea do Poço\r'),(2236,'2933158','Várzea Nova\r'),(2237,'2933174','Varzedo\r'),(2238,'2933208','Vera Cruz\r'),(2239,'2933257','Vereda\r'),(2240,'2933307','Vitória da Conquista\r'),(2241,'2933406','Wagner\r'),(2242,'2933455','Wanderley\r'),(2243,'2933505','Wenceslau Guimarães\r'),(2244,'2933604','Xique-Xique\r'),(2245,'3100104','Abadia dos Dourados\r'),(2246,'3100203','Abaeté\r'),(2247,'3100302','Abre Campo\r'),(2248,'3100401','Acaiaca\r'),(2249,'3100500','Açucena\r'),(2250,'3100609','Água Boa\r'),(2251,'3100708','Água Comprida\r'),(2252,'3100807','Aguanil\r'),(2253,'3100906','Águas Formosas\r'),(2254,'3101003','Águas Vermelhas\r'),(2255,'3101102','Aimorés\r'),(2256,'3101201','Aiuruoca\r'),(2257,'3101300','Alagoa\r'),(2258,'3101409','Albertina\r'),(2259,'3101508','Além Paraíba\r'),(2260,'3101607','Alfenas\r'),(2261,'3101631','Alfredo Vasconcelos\r'),(2262,'3101706','Almenara\r'),(2263,'3101805','Alpercata\r'),(2264,'3101904','Alpinópolis\r'),(2265,'3102001','Alterosa\r'),(2266,'3102050','Alto Caparaó\r'),(2267,'3153509','Alto Jequitibá\r'),(2268,'3102100','Alto Rio Doce\r'),(2269,'3102209','Alvarenga\r'),(2270,'3102308','Alvinópolis\r'),(2271,'3102407','Alvorada de Minas\r'),(2272,'3102506','Amparo do Serra\r'),(2273,'3102605','Andradas\r'),(2274,'3102803','Andrelândia\r'),(2275,'3102852','Angelândia\r'),(2276,'3102902','Antônio Carlos\r'),(2277,'3103009','Antônio Dias\r'),(2278,'3103108','Antônio Prado de Minas\r'),(2279,'3103207','Araçaí\r'),(2280,'3103306','Aracitaba\r'),(2281,'3103405','Araçuaí\r'),(2282,'3103504','Araguari\r'),(2283,'3103603','Arantina\r'),(2284,'3103702','Araponga\r'),(2285,'3103751','Araporã\r'),(2286,'3103801','Arapuá\r'),(2287,'3103900','Araújos\r'),(2288,'3104007','Araxá\r'),(2289,'3104106','Arceburgo\r'),(2290,'3104205','Arcos\r'),(2291,'3104304','Areado\r'),(2292,'3104403','Argirita\r'),(2293,'3104452','Aricanduva\r'),(2294,'3104502','Arinos\r'),(2295,'3104601','Astolfo Dutra\r'),(2296,'3104700','Ataléia\r'),(2297,'3104809','Augusto de Lima\r'),(2298,'3104908','Baependi\r'),(2299,'3105004','Baldim\r'),(2300,'3105103','Bambuí\r'),(2301,'3105202','Bandeira\r'),(2302,'3105301','Bandeira do Sul\r'),(2303,'3105400','Barão de Cocais\r'),(2304,'3105509','Barão de Monte Alto\r'),(2305,'3105608','Barbacena\r'),(2306,'3105707','Barra Longa\r'),(2307,'3105905','Barroso\r'),(2308,'3106002','Bela Vista de Minas\r'),(2309,'3106101','Belmiro Braga\r'),(2310,'3106200','Belo Horizonte\r'),(2311,'3106309','Belo Oriente\r'),(2312,'3106408','Belo Vale\r'),(2313,'3106507','Berilo\r'),(2314,'3106655','Berizal\r'),(2315,'3106606','Bertópolis\r'),(2316,'3106705','Betim\r'),(2317,'3106804','Bias Fortes\r'),(2318,'3106903','Bicas\r'),(2319,'3107000','Biquinhas\r'),(2320,'3107109','Boa Esperança\r'),(2321,'3107208','Bocaina de Minas\r'),(2322,'3107307','Bocaiúva\r'),(2323,'3107406','Bom Despacho\r'),(2324,'3107505','Bom Jardim de Minas\r'),(2325,'3107604','Bom Jesus da Penha\r'),(2326,'3107703','Bom Jesus do Amparo\r'),(2327,'3107802','Bom Jesus do Galho\r'),(2328,'3107901','Bom Repouso\r'),(2329,'3108008','Bom Sucesso\r'),(2330,'3108107','Bonfim\r'),(2331,'3108206','Bonfinópolis de Minas\r'),(2332,'3108255','Bonito de Minas\r'),(2333,'3108305','Borda da Mata\r'),(2334,'3108404','Botelhos\r'),(2335,'3108503','Botumirim\r'),(2336,'3108701','Brás Pires\r'),(2337,'3108552','Brasilândia de Minas\r'),(2338,'3108602','Brasília de Minas\r'),(2339,'3108800','Braúnas\r'),(2340,'3108909','Brazópolis\r'),(2341,'3109006','Brumadinho\r'),(2342,'3109105','Bueno Brandão\r'),(2343,'3109204','Buenópolis\r'),(2344,'3109253','Bugre\r'),(2345,'3109303','Buritis\r'),(2346,'3109402','Buritizeiro\r'),(2347,'3109451','Cabeceira Grande\r'),(2348,'3109501','Cabo Verde\r'),(2349,'3109600','Cachoeira da Prata\r'),(2350,'3109709','Cachoeira de Minas\r'),(2351,'3102704','Cachoeira de Pajeú\r'),(2352,'3109808','Cachoeira Dourada\r'),(2353,'3109907','Caetanópolis\r'),(2354,'3110004','Caeté\r'),(2355,'3110103','Caiana\r'),(2356,'3110202','Cajuri\r'),(2357,'3110301','Caldas\r'),(2358,'3110400','Camacho\r'),(2359,'3110509','Camanducaia\r'),(2360,'3110608','Cambuí\r'),(2361,'3110707','Cambuquira\r'),(2362,'3110806','Campanário\r'),(2363,'3110905','Campanha\r'),(2364,'3111002','Campestre\r'),(2365,'3111101','Campina Verde\r'),(2366,'3111150','Campo Azul\r'),(2367,'3111200','Campo Belo\r'),(2368,'3111309','Campo do Meio\r'),(2369,'3111408','Campo Florido\r'),(2370,'3111507','Campos Altos\r'),(2371,'3111606','Campos Gerais\r'),(2372,'3111903','Cana Verde\r'),(2373,'3111705','Canaã\r'),(2374,'3111804','Canápolis\r'),(2375,'3112000','Candeias\r'),(2376,'3112059','Cantagalo\r'),(2377,'3112109','Caparaó\r'),(2378,'3112208','Capela Nova\r'),(2379,'3112307','Capelinha\r'),(2380,'3112406','Capetinga\r'),(2381,'3112505','Capim Branco\r'),(2382,'3112604','Capinópolis\r'),(2383,'3112653','Capitão Andrade\r'),(2384,'3112703','Capitão Enéas\r'),(2385,'3112802','Capitólio\r'),(2386,'3112901','Caputira\r'),(2387,'3113008','Caraí\r'),(2388,'3113107','Caranaíba\r'),(2389,'3113206','Carandaí\r'),(2390,'3113305','Carangola\r'),(2391,'3113404','Caratinga\r'),(2392,'3113503','Carbonita\r'),(2393,'3113602','Careaçu\r'),(2394,'3113701','Carlos Chagas\r'),(2395,'3113800','Carmésia\r'),(2396,'3113909','Carmo da Cachoeira\r'),(2397,'3114006','Carmo da Mata\r'),(2398,'3114105','Carmo de Minas\r'),(2399,'3114204','Carmo do Cajuru\r'),(2400,'3114303','Carmo do Paranaíba\r'),(2401,'3114402','Carmo do Rio Claro\r'),(2402,'3114501','Carmópolis de Minas\r'),(2403,'3114550','Carneirinho\r'),(2404,'3114600','Carrancas\r'),(2405,'3114709','Carvalhópolis\r'),(2406,'3114808','Carvalhos\r'),(2407,'3114907','Casa Grande\r'),(2408,'3115003','Cascalho Rico\r'),(2409,'3115102','Cássia\r'),(2410,'3115300','Cataguases\r'),(2411,'3115359','Catas Altas\r'),(2412,'3115409','Catas Altas da Noruega\r'),(2413,'3115458','Catuji\r'),(2414,'3115474','Catuti\r'),(2415,'3115508','Caxambu\r'),(2416,'3115607','Cedro do Abaeté\r'),(2417,'3115706','Central de Minas\r'),(2418,'3115805','Centralina\r'),(2419,'3115904','Chácara\r'),(2420,'3116001','Chalé\r'),(2421,'3116100','Chapada do Norte\r'),(2422,'3116159','Chapada Gaúcha\r'),(2423,'3116209','Chiador\r'),(2424,'3116308','Cipotânea\r'),(2425,'3116407','Claraval\r'),(2426,'3116506','Claro dos Poções\r'),(2427,'3116605','Cláudio\r'),(2428,'3116704','Coimbra\r'),(2429,'3116803','Coluna\r'),(2430,'3116902','Comendador Gomes\r'),(2431,'3117009','Comercinho\r'),(2432,'3117108','Conceição da Aparecida\r'),(2433,'3115201','Conceição da Barra de Minas\r'),(2434,'3117306','Conceição das Alagoas\r'),(2435,'3117207','Conceição das Pedras\r'),(2436,'3117405','Conceição de Ipanema\r'),(2437,'3117504','Conceição do Mato Dentro\r'),(2438,'3117603','Conceição do Pará\r'),(2439,'3117702','Conceição do Rio Verde\r'),(2440,'3117801','Conceição dos Ouros\r'),(2441,'3117836','Cônego Marinho\r'),(2442,'3117876','Confins\r'),(2443,'3117900','Congonhal\r'),(2444,'3118007','Congonhas\r'),(2445,'3118106','Congonhas do Norte\r'),(2446,'3118205','Conquista\r'),(2447,'3118304','Conselheiro Lafaiete\r'),(2448,'3118403','Conselheiro Pena\r'),(2449,'3118502','Consolação\r'),(2450,'3118601','Contagem\r'),(2451,'3118700','Coqueiral\r'),(2452,'3118809','Coração de Jesus\r'),(2453,'3118908','Cordisburgo\r'),(2454,'3119005','Cordislândia\r'),(2455,'3119104','Corinto\r'),(2456,'3119203','Coroaci\r'),(2457,'3119302','Coromandel\r'),(2458,'3119401','Coronel Fabriciano\r'),(2459,'3119500','Coronel Murta\r'),(2460,'3119609','Coronel Pacheco\r'),(2461,'3119708','Coronel Xavier Chaves\r'),(2462,'3119807','Córrego Danta\r'),(2463,'3119906','Córrego do Bom Jesus\r'),(2464,'3119955','Córrego Fundo\r'),(2465,'3120003','Córrego Novo\r'),(2466,'3120102','Couto de Magalhães de Minas\r'),(2467,'3120151','Crisólita\r'),(2468,'3120201','Cristais\r'),(2469,'3120300','Cristália\r'),(2470,'3120409','Cristiano Otoni\r'),(2471,'3120508','Cristina\r'),(2472,'3120607','Crucilândia\r'),(2473,'3120706','Cruzeiro da Fortaleza\r'),(2474,'3120805','Cruzília\r'),(2475,'3120839','Cuparaque\r'),(2476,'3120870','Curral de Dentro\r'),(2477,'3120904','Curvelo\r'),(2478,'3121001','Datas\r'),(2479,'3121100','Delfim Moreira\r'),(2480,'3121209','Delfinópolis\r'),(2481,'3121258','Delta\r'),(2482,'3121308','Descoberto\r'),(2483,'3121407','Desterro de Entre Rios\r'),(2484,'3121506','Desterro do Melo\r'),(2485,'3121605','Diamantina\r'),(2486,'3121704','Diogo de Vasconcelos\r'),(2487,'3121803','Dionísio\r'),(2488,'3121902','Divinésia\r'),(2489,'3122009','Divino\r'),(2490,'3122108','Divino das Laranjeiras\r'),(2491,'3122207','Divinolândia de Minas\r'),(2492,'3122306','Divinópolis\r'),(2493,'3122355','Divisa Alegre\r'),(2494,'3122405','Divisa Nova\r'),(2495,'3122454','Divisópolis\r'),(2496,'3122470','Dom Bosco\r'),(2497,'3122504','Dom Cavati\r'),(2498,'3122603','Dom Joaquim\r'),(2499,'3122702','Dom Silvério\r'),(2500,'3122801','Dom Viçoso\r'),(2501,'3122900','Dona Euzébia\r'),(2502,'3123007','Dores de Campos\r'),(2503,'3123106','Dores de Guanhães\r'),(2504,'3123205','Dores do Indaiá\r'),(2505,'3123304','Dores do Turvo\r'),(2506,'3123403','Doresópolis\r'),(2507,'3123502','Douradoquara\r'),(2508,'3123528','Durandé\r'),(2509,'3123601','Elói Mendes\r'),(2510,'3123700','Engenheiro Caldas\r'),(2511,'3123809','Engenheiro Navarro\r'),(2512,'3123858','Entre Folhas\r'),(2513,'3123908','Entre Rios de Minas\r'),(2514,'3124005','Ervália\r'),(2515,'3124104','Esmeraldas\r'),(2516,'3124203','Espera Feliz\r'),(2517,'3124302','Espinosa\r'),(2518,'3124401','Espírito Santo do Dourado\r'),(2519,'3124500','Estiva\r'),(2520,'3124609','Estrela Dalva\r'),(2521,'3124708','Estrela do Indaiá\r'),(2522,'3124807','Estrela do Sul\r'),(2523,'3124906','Eugenópolis\r'),(2524,'3125002','Ewbank da Câmara\r'),(2525,'3125101','Extrema\r'),(2526,'3125200','Fama\r'),(2527,'3125309','Faria Lemos\r'),(2528,'3125408','Felício dos Santos\r'),(2529,'3125606','Felisburgo\r'),(2530,'3125705','Felixlândia\r'),(2531,'3125804','Fernandes Tourinho\r'),(2532,'3125903','Ferros\r'),(2533,'3125952','Fervedouro\r'),(2534,'3126000','Florestal\r'),(2535,'3126109','Formiga\r'),(2536,'3126208','Formoso\r'),(2537,'3126307','Fortaleza de Minas\r'),(2538,'3126406','Fortuna de Minas\r'),(2539,'3126505','Francisco Badaró\r'),(2540,'3126604','Francisco Dumont\r'),(2541,'3126703','Francisco Sá\r'),(2542,'3126752','Franciscópolis\r'),(2543,'3126802','Frei Gaspar\r'),(2544,'3126901','Frei Inocêncio\r'),(2545,'3126950','Frei Lagonegro\r'),(2546,'3127008','Fronteira\r'),(2547,'3127057','Fronteira dos Vales\r'),(2548,'3127073','Fruta de Leite\r'),(2549,'3127107','Frutal\r'),(2550,'3127206','Funilândia\r'),(2551,'3127305','Galiléia\r'),(2552,'3127339','Gameleiras\r'),(2553,'3127354','Glaucilândia\r'),(2554,'3127370','Goiabeira\r'),(2555,'3127388','Goianá\r'),(2556,'3127404','Gonçalves\r'),(2557,'3127503','Gonzaga\r'),(2558,'3127602','Gouveia\r'),(2559,'3127701','Governador Valadares\r'),(2560,'3127800','Grão Mogol\r'),(2561,'3127909','Grupiara\r'),(2562,'3128006','Guanhães\r'),(2563,'3128105','Guapé\r'),(2564,'3128204','Guaraciaba\r'),(2565,'3128253','Guaraciama\r'),(2566,'3128303','Guaranésia\r'),(2567,'3128402','Guarani\r'),(2568,'3128501','Guarará\r'),(2569,'3128600','Guarda-Mor\r'),(2570,'3128709','Guaxupé\r'),(2571,'3128808','Guidoval\r'),(2572,'3128907','Guimarânia\r'),(2573,'3129004','Guiricema\r'),(2574,'3129103','Gurinhatã\r'),(2575,'3129202','Heliodora\r'),(2576,'3129301','Iapu\r'),(2577,'3129400','Ibertioga\r'),(2578,'3129509','Ibiá\r'),(2579,'3129608','Ibiaí\r'),(2580,'3129657','Ibiracatu\r'),(2581,'3129707','Ibiraci\r'),(2582,'3129806','Ibirité\r'),(2583,'3129905','Ibitiúra de Minas\r'),(2584,'3130002','Ibituruna\r'),(2585,'3130051','Icaraí de Minas\r'),(2586,'3130101','Igarapé\r'),(2587,'3130200','Igaratinga\r'),(2588,'3130309','Iguatama\r'),(2589,'3130408','Ijaci\r'),(2590,'3130507','Ilicínea\r'),(2591,'3130556','Imbé de Minas\r'),(2592,'3130606','Inconfidentes\r'),(2593,'3130655','Indaiabira\r'),(2594,'3130705','Indianópolis\r'),(2595,'3130804','Ingaí\r'),(2596,'3130903','Inhapim\r'),(2597,'3131000','Inhaúma\r'),(2598,'3131109','Inimutaba\r'),(2599,'3131158','Ipaba\r'),(2600,'3131208','Ipanema\r'),(2601,'3131307','Ipatinga\r'),(2602,'3131406','Ipiaçu\r'),(2603,'3131505','Ipuiúna\r'),(2604,'3131604','Iraí de Minas\r'),(2605,'3131703','Itabira\r'),(2606,'3131802','Itabirinha\r'),(2607,'3131901','Itabirito\r'),(2608,'3132008','Itacambira\r'),(2609,'3132107','Itacarambi\r'),(2610,'3132206','Itaguara\r'),(2611,'3132305','Itaipé\r'),(2612,'3132404','Itajubá\r'),(2613,'3132503','Itamarandiba\r'),(2614,'3132602','Itamarati de Minas\r'),(2615,'3132701','Itambacuri\r'),(2616,'3132800','Itambé do Mato Dentro\r'),(2617,'3132909','Itamogi\r'),(2618,'3133006','Itamonte\r'),(2619,'3133105','Itanhandu\r'),(2620,'3133204','Itanhomi\r'),(2621,'3133303','Itaobim\r'),(2622,'3133402','Itapagipe\r'),(2623,'3133501','Itapecerica\r'),(2624,'3133600','Itapeva\r'),(2625,'3133709','Itatiaiuçu\r'),(2626,'3133758','Itaú de Minas\r'),(2627,'3133808','Itaúna\r'),(2628,'3133907','Itaverava\r'),(2629,'3134004','Itinga\r'),(2630,'3134103','Itueta\r'),(2631,'3134202','Ituiutaba\r'),(2632,'3134301','Itumirim\r'),(2633,'3134400','Iturama\r'),(2634,'3134509','Itutinga\r'),(2635,'3134608','Jaboticatubas\r'),(2636,'3134707','Jacinto\r'),(2637,'3134806','Jacuí\r'),(2638,'3134905','Jacutinga\r'),(2639,'3135001','Jaguaraçu\r'),(2640,'3135050','Jaíba\r'),(2641,'3135076','Jampruca\r'),(2642,'3135100','Janaúba\r'),(2643,'3135209','Januária\r'),(2644,'3135308','Japaraíba\r'),(2645,'3135357','Japonvar\r'),(2646,'3135407','Jeceaba\r'),(2647,'3135456','Jenipapo de Minas\r'),(2648,'3135506','Jequeri\r'),(2649,'3135605','Jequitaí\r'),(2650,'3135704','Jequitibá\r'),(2651,'3135803','Jequitinhonha\r'),(2652,'3135902','Jesuânia\r'),(2653,'3136009','Joaíma\r'),(2654,'3136108','Joanésia\r'),(2655,'3136207','João Monlevade\r'),(2656,'3136306','João Pinheiro\r'),(2657,'3136405','Joaquim Felício\r'),(2658,'3136504','Jordânia\r'),(2659,'3136520','José Gonçalves de Minas\r'),(2660,'3136553','José Raydan\r'),(2661,'3136579','Josenópolis\r'),(2662,'3136652','Juatuba\r'),(2663,'3136702','Juiz de Fora\r'),(2664,'3136801','Juramento\r'),(2665,'3136900','Juruaia\r'),(2666,'3136959','Juvenília\r'),(2667,'3137007','Ladainha\r'),(2668,'3137106','Lagamar\r'),(2669,'3137205','Lagoa da Prata\r'),(2670,'3137304','Lagoa dos Patos\r'),(2671,'3137403','Lagoa Dourada\r'),(2672,'3137502','Lagoa Formosa\r'),(2673,'3137536','Lagoa Grande\r'),(2674,'3137601','Lagoa Santa\r'),(2675,'3137700','Lajinha\r'),(2676,'3137809','Lambari\r'),(2677,'3137908','Lamim\r'),(2678,'3138005','Laranjal\r'),(2679,'3138104','Lassance\r'),(2680,'3138203','Lavras\r'),(2681,'3138302','Leandro Ferreira\r'),(2682,'3138351','Leme do Prado\r'),(2683,'3138401','Leopoldina\r'),(2684,'3138500','Liberdade\r'),(2685,'3138609','Lima Duarte\r'),(2686,'3138625','Limeira do Oeste\r'),(2687,'3138658','Lontra\r'),(2688,'3138674','Luisburgo\r'),(2689,'3138682','Luislândia\r'),(2690,'3138708','Luminárias\r'),(2691,'3138807','Luz\r'),(2692,'3138906','Machacalis\r'),(2693,'3139003','Machado\r'),(2694,'3139102','Madre de Deus de Minas\r'),(2695,'3139201','Malacacheta\r'),(2696,'3139250','Mamonas\r'),(2697,'3139300','Manga\r'),(2698,'3139409','Manhuaçu\r'),(2699,'3139508','Manhumirim\r'),(2700,'3139607','Mantena\r'),(2701,'3139805','Mar de Espanha\r'),(2702,'3139706','Maravilhas\r'),(2703,'3139904','Maria da Fé\r'),(2704,'3140001','Mariana\r'),(2705,'3140100','Marilac\r'),(2706,'3140159','Mário Campos\r'),(2707,'3140209','Maripá de Minas\r'),(2708,'3140308','Marliéria\r'),(2709,'3140407','Marmelópolis\r'),(2710,'3140506','Martinho Campos\r'),(2711,'3140530','Martins Soares\r'),(2712,'3140555','Mata Verde\r'),(2713,'3140605','Materlândia\r'),(2714,'3140704','Mateus Leme\r'),(2715,'3171501','Mathias Lobato\r'),(2716,'3140803','Matias Barbosa\r'),(2717,'3140852','Matias Cardoso\r'),(2718,'3140902','Matipó\r'),(2719,'3141009','Mato Verde\r'),(2720,'3141108','Matozinhos\r'),(2721,'3141207','Matutina\r'),(2722,'3141306','Medeiros\r'),(2723,'3141405','Medina\r'),(2724,'3141504','Mendes Pimentel\r'),(2725,'3141603','Mercês\r'),(2726,'3141702','Mesquita\r'),(2727,'3141801','Minas Novas\r'),(2728,'3141900','Minduri\r'),(2729,'3142007','Mirabela\r'),(2730,'3142106','Miradouro\r'),(2731,'3142205','Miraí\r'),(2732,'3142254','Miravânia\r'),(2733,'3142304','Moeda\r'),(2734,'3142403','Moema\r'),(2735,'3142502','Monjolos\r'),(2736,'3142601','Monsenhor Paulo\r'),(2737,'3142700','Montalvânia\r'),(2738,'3142809','Monte Alegre de Minas\r'),(2739,'3142908','Monte Azul\r'),(2740,'3143005','Monte Belo\r'),(2741,'3143104','Monte Carmelo\r'),(2742,'3143153','Monte Formoso\r'),(2743,'3143203','Monte Santo de Minas\r'),(2744,'3143401','Monte Sião\r'),(2745,'3143302','Montes Claros\r'),(2746,'3143450','Montezuma\r'),(2747,'3143500','Morada Nova de Minas\r'),(2748,'3143609','Morro da Garça\r'),(2749,'3143708','Morro do Pilar\r'),(2750,'3143807','Munhoz\r'),(2751,'3143906','Muriaé\r'),(2752,'3144003','Mutum\r'),(2753,'3144102','Muzambinho\r'),(2754,'3144201','Nacip Raydan\r'),(2755,'3144300','Nanuque\r'),(2756,'3144359','Naque\r'),(2757,'3144375','Natalândia\r'),(2758,'3144409','Natércia\r'),(2759,'3144508','Nazareno\r'),(2760,'3144607','Nepomuceno\r'),(2761,'3144656','Ninheira\r'),(2762,'3144672','Nova Belém\r'),(2763,'3144706','Nova Era\r'),(2764,'3144805','Nova Lima\r'),(2765,'3144904','Nova Módica\r'),(2766,'3145000','Nova Ponte\r'),(2767,'3145059','Nova Porteirinha\r'),(2768,'3145109','Nova Resende\r'),(2769,'3145208','Nova Serrana\r'),(2770,'3136603','Nova União\r'),(2771,'3145307','Novo Cruzeiro\r'),(2772,'3145356','Novo Oriente de Minas\r'),(2773,'3145372','Novorizonte\r'),(2774,'3145406','Olaria\r'),(2775,'3145455','Olhos-dÁgua\r'),(2776,'3145505','Olímpio Noronha\r'),(2777,'3145604','Oliveira\r'),(2778,'3145703','Oliveira Fortes\r'),(2779,'3145802','Onça de Pitangui\r'),(2780,'3145851','Oratórios\r'),(2781,'3145877','Orizânia\r'),(2782,'3145901','Ouro Branco\r'),(2783,'3146008','Ouro Fino\r'),(2784,'3146107','Ouro Preto\r'),(2785,'3146206','Ouro Verde de Minas\r'),(2786,'3146255','Padre Carvalho\r'),(2787,'3146305','Padre Paraíso\r'),(2788,'3146552','Pai Pedro\r'),(2789,'3146404','Paineiras\r'),(2790,'3146503','Pains\r'),(2791,'3146602','Paiva\r'),(2792,'3146701','Palma\r'),(2793,'3146750','Palmópolis\r'),(2794,'3146909','Papagaios\r'),(2795,'3147105','Pará de Minas\r'),(2796,'3147006','Paracatu\r'),(2797,'3147204','Paraguaçu\r'),(2798,'3147303','Paraisópolis\r'),(2799,'3147402','Paraopeba\r'),(2800,'3147600','Passa Quatro\r'),(2801,'3147709','Passa Tempo\r'),(2802,'3147808','Passa Vinte\r'),(2803,'3147501','Passabém\r'),(2804,'3147907','Passos\r'),(2805,'3147956','Patis\r'),(2806,'3148004','Patos de Minas\r'),(2807,'3148103','Patrocínio\r'),(2808,'3148202','Patrocínio do Muriaé\r'),(2809,'3148301','Paula Cândido\r'),(2810,'3148400','Paulistas\r'),(2811,'3148509','Pavão\r'),(2812,'3148608','Peçanha\r'),(2813,'3148707','Pedra Azul\r'),(2814,'3148756','Pedra Bonita\r'),(2815,'3148806','Pedra do Anta\r'),(2816,'3148905','Pedra do Indaiá\r'),(2817,'3149002','Pedra Dourada\r'),(2818,'3149101','Pedralva\r'),(2819,'3149150','Pedras de Maria da Cruz\r'),(2820,'3149200','Pedrinópolis\r'),(2821,'3149309','Pedro Leopoldo\r'),(2822,'3149408','Pedro Teixeira\r'),(2823,'3149507','Pequeri\r'),(2824,'3149606','Pequi\r'),(2825,'3149705','Perdigão\r'),(2826,'3149804','Perdizes\r'),(2827,'3149903','Perdões\r'),(2828,'3149952','Periquito\r'),(2829,'3150000','Pescador\r'),(2830,'3150109','Piau\r'),(2831,'3150158','Piedade de Caratinga\r'),(2832,'3150208','Piedade de Ponte Nova\r'),(2833,'3150307','Piedade do Rio Grande\r'),(2834,'3150406','Piedade dos Gerais\r'),(2835,'3150505','Pimenta\r'),(2836,'3150539','Pingo dÁgua\r'),(2837,'3150570','Pintópolis\r'),(2838,'3150604','Piracema\r'),(2839,'3150703','Pirajuba\r'),(2840,'3150802','Piranga\r'),(2841,'3150901','Piranguçu\r'),(2842,'3151008','Piranguinho\r'),(2843,'3151107','Pirapetinga\r'),(2844,'3151206','Pirapora\r'),(2845,'3151305','Piraúba\r'),(2846,'3151404','Pitangui\r'),(2847,'3151503','Piumhi\r'),(2848,'3151602','Planura\r'),(2849,'3151701','Poço Fundo\r'),(2850,'3151800','Poços de Caldas\r'),(2851,'3151909','Pocrane\r'),(2852,'3152006','Pompéu\r'),(2853,'3152105','Ponte Nova\r'),(2854,'3152131','Ponto Chique\r'),(2855,'3152170','Ponto dos Volantes\r'),(2856,'3152204','Porteirinha\r'),(2857,'3152303','Porto Firme\r'),(2858,'3152402','Poté\r'),(2859,'3152501','Pouso Alegre\r'),(2860,'3152600','Pouso Alto\r'),(2861,'3152709','Prados\r'),(2862,'3152808','Prata\r'),(2863,'3152907','Pratápolis\r'),(2864,'3153004','Pratinha\r'),(2865,'3153103','Presidente Bernardes\r'),(2866,'3153202','Presidente Juscelino\r'),(2867,'3153301','Presidente Kubitschek\r'),(2868,'3153400','Presidente Olegário\r'),(2869,'3153608','Prudente de Morais\r'),(2870,'3153707','Quartel Geral\r'),(2871,'3153806','Queluzito\r'),(2872,'3153905','Raposos\r'),(2873,'3154002','Raul Soares\r'),(2874,'3154101','Recreio\r'),(2875,'3154150','Reduto\r'),(2876,'3154200','Resende Costa\r'),(2877,'3154309','Resplendor\r'),(2878,'3154408','Ressaquinha\r'),(2879,'3154457','Riachinho\r'),(2880,'3154507','Riacho dos Machados\r'),(2881,'3154606','Ribeirão das Neves\r'),(2882,'3154705','Ribeirão Vermelho\r'),(2883,'3154804','Rio Acima\r'),(2884,'3154903','Rio Casca\r'),(2885,'3155108','Rio do Prado\r'),(2886,'3155009','Rio Doce\r'),(2887,'3155207','Rio Espera\r'),(2888,'3155306','Rio Manso\r'),(2889,'3155405','Rio Novo\r'),(2890,'3155504','Rio Paranaíba\r'),(2891,'3155603','Rio Pardo de Minas\r'),(2892,'3155702','Rio Piracicaba\r'),(2893,'3155801','Rio Pomba\r'),(2894,'3155900','Rio Preto\r'),(2895,'3156007','Rio Vermelho\r'),(2896,'3156106','Ritápolis\r'),(2897,'3156205','Rochedo de Minas\r'),(2898,'3156304','Rodeiro\r'),(2899,'3156403','Romaria\r'),(2900,'3156452','Rosário da Limeira\r'),(2901,'3156502','Rubelita\r'),(2902,'3156601','Rubim\r'),(2903,'3156700','Sabará\r'),(2904,'3156809','Sabinópolis\r'),(2905,'3156908','Sacramento\r'),(2906,'3157005','Salinas\r'),(2907,'3157104','Salto da Divisa\r'),(2908,'3157203','Santa Bárbara\r'),(2909,'3157252','Santa Bárbara do Leste\r'),(2910,'3157278','Santa Bárbara do Monte Verde\r'),(2911,'3157302','Santa Bárbara do Tugúrio\r'),(2912,'3157336','Santa Cruz de Minas\r'),(2913,'3157377','Santa Cruz de Salinas\r'),(2914,'3157401','Santa Cruz do Escalvado\r'),(2915,'3157500','Santa Efigênia de Minas\r'),(2916,'3157609','Santa Fé de Minas\r'),(2917,'3157658','Santa Helena de Minas\r'),(2918,'3157708','Santa Juliana\r'),(2919,'3157807','Santa Luzia\r'),(2920,'3157906','Santa Margarida\r'),(2921,'3158003','Santa Maria de Itabira\r'),(2922,'3158102','Santa Maria do Salto\r'),(2923,'3158201','Santa Maria do Suaçuí\r'),(2924,'3159209','Santa Rita de Caldas\r'),(2925,'3159407','Santa Rita de Ibitipoca\r'),(2926,'3159308','Santa Rita de Jacutinga\r'),(2927,'3159357','Santa Rita de Minas\r'),(2928,'3159506','Santa Rita do Itueto\r'),(2929,'3159605','Santa Rita do Sapucaí\r'),(2930,'3159704','Santa Rosa da Serra\r'),(2931,'3159803','Santa Vitória\r'),(2932,'3158300','Santana da Vargem\r'),(2933,'3158409','Santana de Cataguases\r'),(2934,'3158508','Santana de Pirapama\r'),(2935,'3158607','Santana do Deserto\r'),(2936,'3158706','Santana do Garambéu\r'),(2937,'3158805','Santana do Jacaré\r'),(2938,'3158904','Santana do Manhuaçu\r'),(2939,'3158953','Santana do Paraíso\r'),(2940,'3159001','Santana do Riacho\r'),(2941,'3159100','Santana dos Montes\r'),(2942,'3159902','Santo Antônio do Amparo\r'),(2943,'3160009','Santo Antônio do Aventureiro\r'),(2944,'3160108','Santo Antônio do Grama\r'),(2945,'3160207','Santo Antônio do Itambé\r'),(2946,'3160306','Santo Antônio do Jacinto\r'),(2947,'3160405','Santo Antônio do Monte\r'),(2948,'3160454','Santo Antônio do Retiro\r'),(2949,'3160504','Santo Antônio do Rio Abaixo\r'),(2950,'3160603','Santo Hipólito\r'),(2951,'3160702','Santos Dumont\r'),(2952,'3160801','São Bento Abade\r'),(2953,'3160900','São Brás do Suaçuí\r'),(2954,'3160959','São Domingos das Dores\r'),(2955,'3161007','São Domingos do Prata\r'),(2956,'3161056','São Félix de Minas\r'),(2957,'3161106','São Francisco\r'),(2958,'3161205','São Francisco de Paula\r'),(2959,'3161304','São Francisco de Sales\r'),(2960,'3161403','São Francisco do Glória\r'),(2961,'3161502','São Geraldo\r'),(2962,'3161601','São Geraldo da Piedade\r'),(2963,'3161650','São Geraldo do Baixio\r'),(2964,'3161700','São Gonçalo do Abaeté\r'),(2965,'3161809','São Gonçalo do Pará\r'),(2966,'3161908','São Gonçalo do Rio Abaixo\r'),(2967,'3125507','São Gonçalo do Rio Preto\r'),(2968,'3162005','São Gonçalo do Sapucaí\r'),(2969,'3162104','São Gotardo\r'),(2970,'3162203','São João Batista do Glória\r'),(2971,'3162252','São João da Lagoa\r'),(2972,'3162302','São João da Mata\r'),(2973,'3162401','São João da Ponte\r'),(2974,'3162450','São João das Missões\r'),(2975,'3162500','São João del Rei\r'),(2976,'3162559','São João do Manhuaçu\r'),(2977,'3162575','São João do Manteninha\r'),(2978,'3162609','São João do Oriente\r'),(2979,'3162658','São João do Pacuí\r'),(2980,'3162708','São João do Paraíso\r'),(2981,'3162807','São João Evangelista\r'),(2982,'3162906','São João Nepomuceno\r'),(2983,'3162922','São Joaquim de Bicas\r'),(2984,'3162948','São José da Barra\r'),(2985,'3162955','São José da Lapa\r'),(2986,'3163003','São José da Safira\r'),(2987,'3163102','São José da Varginha\r'),(2988,'3163201','São José do Alegre\r'),(2989,'3163300','São José do Divino\r'),(2990,'3163409','São José do Goiabal\r'),(2991,'3163508','São José do Jacuri\r'),(2992,'3163607','São José do Mantimento\r'),(2993,'3163706','São Lourenço\r'),(2994,'3163805','São Miguel do Anta\r'),(2995,'3163904','São Pedro da União\r'),(2996,'3164100','São Pedro do Suaçuí\r'),(2997,'3164001','São Pedro dos Ferros\r'),(2998,'3164209','São Romão\r'),(2999,'3164308','São Roque de Minas\r'),(3000,'3164407','São Sebastião da Bela Vista\r'),(3001,'3164431','São Sebastião da Vargem Alegre\r'),(3002,'3164472','São Sebastião do Anta\r'),(3003,'3164506','São Sebastião do Maranhão\r'),(3004,'3164605','São Sebastião do Oeste\r'),(3005,'3164704','São Sebastião do Paraíso\r'),(3006,'3164803','São Sebastião do Rio Preto\r'),(3007,'3164902','São Sebastião do Rio Verde\r'),(3008,'3165008','São Tiago\r'),(3009,'3165107','São Tomás de Aquino\r'),(3010,'3165206','São Tomé das Letras\r'),(3011,'3165305','São Vicente de Minas\r'),(3012,'3165404','Sapucaí-Mirim\r'),(3013,'3165503','Sardoá\r'),(3014,'3165537','Sarzedo\r'),(3015,'3165560','Sem-Peixe\r'),(3016,'3165578','Senador Amaral\r'),(3017,'3165602','Senador Cortes\r'),(3018,'3165701','Senador Firmino\r'),(3019,'3165800','Senador José Bento\r'),(3020,'3165909','Senador Modestino Gonçalves\r'),(3021,'3166006','Senhora de Oliveira\r'),(3022,'3166105','Senhora do Porto\r'),(3023,'3166204','Senhora dos Remédios\r'),(3024,'3166303','Sericita\r'),(3025,'3166402','Seritinga\r'),(3026,'3166501','Serra Azul de Minas\r'),(3027,'3166600','Serra da Saudade\r'),(3028,'3166808','Serra do Salitre\r'),(3029,'3166709','Serra dos Aimorés\r'),(3030,'3166907','Serrania\r'),(3031,'3166956','Serranópolis de Minas\r'),(3032,'3167004','Serranos\r'),(3033,'3167103','Serro\r'),(3034,'3167202','Sete Lagoas\r'),(3035,'3165552','Setubinha\r'),(3036,'3167301','Silveirânia\r'),(3037,'3167400','Silvianópolis\r'),(3038,'3167509','Simão Pereira\r'),(3039,'3167608','Simonésia\r'),(3040,'3167707','Sobrália\r'),(3041,'3167806','Soledade de Minas\r'),(3042,'3167905','Tabuleiro\r'),(3043,'3168002','Taiobeiras\r'),(3044,'3168051','Taparuba\r'),(3045,'3168101','Tapira\r'),(3046,'3168200','Tapiraí\r'),(3047,'3168309','Taquaraçu de Minas\r'),(3048,'3168408','Tarumirim\r'),(3049,'3168507','Teixeiras\r'),(3050,'3168606','Teófilo Otoni\r'),(3051,'3168705','Timóteo\r'),(3052,'3168804','Tiradentes\r'),(3053,'3168903','Tiros\r'),(3054,'3169000','Tocantins\r'),(3055,'3169059','Tocos do Moji\r'),(3056,'3169109','Toledo\r'),(3057,'3169208','Tombos\r'),(3058,'3169307','Três Corações\r'),(3059,'3169356','Três Marias\r'),(3060,'3169406','Três Pontas\r'),(3061,'3169505','Tumiritinga\r'),(3062,'3169604','Tupaciguara\r'),(3063,'3169703','Turmalina\r'),(3064,'3169802','Turvolândia\r'),(3065,'3169901','Ubá\r'),(3066,'3170008','Ubaí\r'),(3067,'3170057','Ubaporanga\r'),(3068,'3170107','Uberaba\r'),(3069,'3170206','Uberlândia\r'),(3070,'3170305','Umburatiba\r'),(3071,'3170404','Unaí\r'),(3072,'3170438','União de Minas\r'),(3073,'3170479','Uruana de Minas\r'),(3074,'3170503','Urucânia\r'),(3075,'3170529','Urucuia\r'),(3076,'3170578','Vargem Alegre\r'),(3077,'3170602','Vargem Bonita\r'),(3078,'3170651','Vargem Grande do Rio Pardo\r'),(3079,'3170701','Varginha\r'),(3080,'3170750','Varjão de Minas\r'),(3081,'3170800','Várzea da Palma\r'),(3082,'3170909','Varzelândia\r'),(3083,'3171006','Vazante\r'),(3084,'3171030','Verdelândia\r'),(3085,'3171071','Veredinha\r'),(3086,'3171105','Veríssimo\r'),(3087,'3171154','Vermelho Novo\r'),(3088,'3171204','Vespasiano\r'),(3089,'3171303','Viçosa\r'),(3090,'3171402','Vieiras\r'),(3091,'3171600','Virgem da Lapa\r'),(3092,'3171709','Virgínia\r'),(3093,'3171808','Virginópolis\r'),(3094,'3171907','Virgolândia\r'),(3095,'3172004','Visconde do Rio Branco\r'),(3096,'3172103','Volta Grande\r'),(3097,'3172202','Wenceslau Braz\r'),(3098,'3200102','Afonso Cláudio\r'),(3099,'3200169','Água Doce do Norte\r'),(3100,'3200136','Águia Branca\r'),(3101,'3200201','Alegre\r'),(3102,'3200300','Alfredo Chaves\r'),(3103,'3200359','Alto Rio Novo\r'),(3104,'3200409','Anchieta\r'),(3105,'3200508','Apiacá\r'),(3106,'3200607','Aracruz\r'),(3107,'3200706','Atílio Vivacqua\r'),(3108,'3200805','Baixo Guandu\r'),(3109,'3200904','Barra de São Francisco\r'),(3110,'3201001','Boa Esperança\r'),(3111,'3201100','Bom Jesus do Norte\r'),(3112,'3201159','Brejetuba\r'),(3113,'3201209','Cachoeiro de Itapemirim\r'),(3114,'3201308','Cariacica\r'),(3115,'3201407','Castelo\r'),(3116,'3201506','Colatina\r'),(3117,'3201605','Conceição da Barra\r'),(3118,'3201704','Conceição do Castelo\r'),(3119,'3201803','Divino de São Lourenço\r'),(3120,'3201902','Domingos Martins\r'),(3121,'3202009','Dores do Rio Preto\r'),(3122,'3202108','Ecoporanga\r'),(3123,'3202207','Fundão\r'),(3124,'3202256','Governador Lindenberg\r'),(3125,'3202306','Guaçuí\r'),(3126,'3202405','Guarapari\r'),(3127,'3202454','Ibatiba\r'),(3128,'3202504','Ibiraçu\r'),(3129,'3202553','Ibitirama\r'),(3130,'3202603','Iconha\r'),(3131,'3202652','Irupi\r'),(3132,'3202702','Itaguaçu\r'),(3133,'3202801','Itapemirim\r'),(3134,'3202900','Itarana\r'),(3135,'3203007','Iúna\r'),(3136,'3203056','Jaguaré\r'),(3137,'3203106','Jerônimo Monteiro\r'),(3138,'3203130','João Neiva\r'),(3139,'3203163','Laranja da Terra\r'),(3140,'3203205','Linhares\r'),(3141,'3203304','Mantenópolis\r'),(3142,'3203320','Marataízes\r'),(3143,'3203346','Marechal Floriano\r'),(3144,'3203353','Marilândia\r'),(3145,'3203403','Mimoso do Sul\r'),(3146,'3203502','Montanha\r'),(3147,'3203601','Mucurici\r'),(3148,'3203700','Muniz Freire\r'),(3149,'3203809','Muqui\r'),(3150,'3203908','Nova Venécia\r'),(3151,'3204005','Pancas\r'),(3152,'3204054','Pedro Canário\r'),(3153,'3204104','Pinheiros\r'),(3154,'3204203','Piúma\r'),(3155,'3204252','Ponto Belo\r'),(3156,'3204302','Presidente Kennedy\r'),(3157,'3204351','Rio Bananal\r'),(3158,'3204401','Rio Novo do Sul\r'),(3159,'3204500','Santa Leopoldina\r'),(3160,'3204559','Santa Maria de Jetibá\r'),(3161,'3204609','Santa Teresa\r'),(3162,'3204658','São Domingos do Norte\r'),(3163,'3204708','São Gabriel da Palha\r'),(3164,'3204807','São José do Calçado\r'),(3165,'3204906','São Mateus\r'),(3166,'3204955','São Roque do Canaã\r'),(3167,'3205002','Serra\r'),(3168,'3205010','Sooretama\r'),(3169,'3205036','Vargem Alta\r'),(3170,'3205069','Venda Nova do Imigrante\r'),(3171,'3205101','Viana\r'),(3172,'3205150','Vila Pavão\r'),(3173,'3205176','Vila Valério\r'),(3174,'3205200','Vila Velha\r'),(3175,'3205309','Vitória\r'),(3176,'3300100','Angra dos Reis\r'),(3177,'3300159','Aperibé\r'),(3178,'3300209','Araruama\r'),(3179,'3300225','Areal\r'),(3180,'3300233','Armação dos Búzios\r'),(3181,'3300258','Arraial do Cabo\r'),(3182,'3300308','Barra do Piraí\r'),(3183,'3300407','Barra Mansa\r'),(3184,'3300456','Belford Roxo\r'),(3185,'3300506','Bom Jardim\r'),(3186,'3300605','Bom Jesus do Itabapoana\r'),(3187,'3300704','Cabo Frio\r'),(3188,'3300803','Cachoeiras de Macacu\r'),(3189,'3300902','Cambuci\r'),(3190,'3301009','Campos dos Goytacazes\r'),(3191,'3301108','Cantagalo\r'),(3192,'3300936','Carapebus\r'),(3193,'3301157','Cardoso Moreira\r'),(3194,'3301207','Carmo\r'),(3195,'3301306','Casimiro de Abreu\r'),(3196,'3300951','Comendador Levy Gasparian\r'),(3197,'3301405','Conceição de Macabu\r'),(3198,'3301504','Cordeiro\r'),(3199,'3301603','Duas Barras\r'),(3200,'3301702','Duque de Caxias\r'),(3201,'3301801','Engenheiro Paulo de Frontin\r'),(3202,'3301850','Guapimirim\r'),(3203,'3301876','Iguaba Grande\r'),(3204,'3301900','Itaboraí\r'),(3205,'3302007','Itaguaí\r'),(3206,'3302056','Italva\r'),(3207,'3302106','Itaocara\r'),(3208,'3302205','Itaperuna\r'),(3209,'3302254','Itatiaia\r'),(3210,'3302270','Japeri\r'),(3211,'3302304','Laje do Muriaé\r'),(3212,'3302403','Macaé\r'),(3213,'3302452','Macuco\r'),(3214,'3302502','Magé\r'),(3215,'3302601','Mangaratiba\r'),(3216,'3302700','Maricá\r'),(3217,'3302809','Mendes\r'),(3218,'3302858','Mesquita\r'),(3219,'3302908','Miguel Pereira\r'),(3220,'3303005','Miracema\r'),(3221,'3303104','Natividade\r'),(3222,'3303203','Nilópolis\r'),(3223,'3303302','Niterói\r'),(3224,'3303401','Nova Friburgo\r'),(3225,'3303500','Nova Iguaçu\r'),(3226,'3303609','Paracambi\r'),(3227,'3303708','Paraíba do Sul\r'),(3228,'3303807','Paraty\r'),(3229,'3303856','Paty do Alferes\r'),(3230,'3303906','Petrópolis\r'),(3231,'3303955','Pinheiral\r'),(3232,'3304003','Piraí\r'),(3233,'3304102','Porciúncula\r'),(3234,'3304110','Porto Real\r'),(3235,'3304128','Quatis\r'),(3236,'3304144','Queimados\r'),(3237,'3304151','Quissamã\r'),(3238,'3304201','Resende\r'),(3239,'3304300','Rio Bonito\r'),(3240,'3304409','Rio Claro\r'),(3241,'3304508','Rio das Flores\r'),(3242,'3304524','Rio das Ostras\r'),(3243,'3304557','Rio de Janeiro\r'),(3244,'3304607','Santa Maria Madalena\r'),(3245,'3304706','Santo Antônio de Pádua\r'),(3246,'3304805','São Fidélis\r'),(3247,'3304755','São Francisco de Itabapoana\r'),(3248,'3304904','São Gonçalo\r'),(3249,'3305000','São João da Barra\r'),(3250,'3305109','São João de Meriti\r'),(3251,'3305133','São José de Ubá\r'),(3252,'3305158','São José do Vale do Rio Preto\r'),(3253,'3305208','São Pedro da Aldeia\r'),(3254,'3305307','São Sebastião do Alto\r'),(3255,'3305406','Sapucaia\r'),(3256,'3305505','Saquarema\r'),(3257,'3305554','Seropédica\r'),(3258,'3305604','Silva Jardim\r'),(3259,'3305703','Sumidouro\r'),(3260,'3305752','Tanguá\r'),(3261,'3305802','Teresópolis\r'),(3262,'3305901','Trajano de Moraes\r'),(3263,'3306008','Três Rios\r'),(3264,'3306107','Valença\r'),(3265,'3306156','Varre-Sai\r'),(3266,'3306206','Vassouras\r'),(3267,'3306305','Volta Redonda\r'),(3268,'3500105','Adamantina\r'),(3269,'3500204','Adolfo\r'),(3270,'3500303','Aguaí\r'),(3271,'3500402','Águas da Prata\r'),(3272,'3500501','Águas de Lindóia\r'),(3273,'3500550','Águas de Santa Bárbara\r'),(3274,'3500600','Águas de São Pedro\r'),(3275,'3500709','Agudos\r'),(3276,'3500758','Alambari\r'),(3277,'3500808','Alfredo Marcondes\r'),(3278,'3500907','Altair\r'),(3279,'3501004','Altinópolis\r'),(3280,'3501103','Alto Alegre\r'),(3281,'3501152','Alumínio\r'),(3282,'3501202','Álvares Florence\r'),(3283,'3501301','Álvares Machado\r'),(3284,'3501400','Álvaro de Carvalho\r'),(3285,'3501509','Alvinlândia\r'),(3286,'3501608','Americana\r'),(3287,'3501707','Américo Brasiliense\r'),(3288,'3501806','Américo de Campos\r'),(3289,'3501905','Amparo\r'),(3290,'3502002','Analândia\r'),(3291,'3502101','Andradina\r'),(3292,'3502200','Angatuba\r'),(3293,'3502309','Anhembi\r'),(3294,'3502408','Anhumas\r'),(3295,'3502507','Aparecida\r'),(3296,'3502606','Aparecida dOeste\r'),(3297,'3502705','Apiaí\r'),(3298,'3502754','Araçariguama\r'),(3299,'3502804','Araçatuba\r'),(3300,'3502903','Araçoiaba da Serra\r'),(3301,'3503000','Aramina\r'),(3302,'3503109','Arandu\r'),(3303,'3503158','Arapeí\r'),(3304,'3503208','Araraquara\r'),(3305,'3503307','Araras\r'),(3306,'3503356','Arco-Íris\r'),(3307,'3503406','Arealva\r'),(3308,'3503505','Areias\r'),(3309,'3503604','Areiópolis\r'),(3310,'3503703','Ariranha\r'),(3311,'3503802','Artur Nogueira\r'),(3312,'3503901','Arujá\r'),(3313,'3503950','Aspásia\r'),(3314,'3504008','Assis\r'),(3315,'3504107','Atibaia\r'),(3316,'3504206','Auriflama\r'),(3317,'3504305','Avaí\r'),(3318,'3504404','Avanhandava\r'),(3319,'3504503','Avaré\r'),(3320,'3504602','Bady Bassitt\r'),(3321,'3504701','Balbinos\r'),(3322,'3504800','Bálsamo\r'),(3323,'3504909','Bananal\r'),(3324,'3505005','Barão de Antonina\r'),(3325,'3505104','Barbosa\r'),(3326,'3505203','Bariri\r'),(3327,'3505302','Barra Bonita\r'),(3328,'3505351','Barra do Chapéu\r'),(3329,'3505401','Barra do Turvo\r'),(3330,'3505500','Barretos\r'),(3331,'3505609','Barrinha\r'),(3332,'3505708','Barueri\r'),(3333,'3505807','Bastos\r'),(3334,'3505906','Batatais\r'),(3335,'3506003','Bauru\r'),(3336,'3506102','Bebedouro\r'),(3337,'3506201','Bento de Abreu\r'),(3338,'3506300','Bernardino de Campos\r'),(3339,'3506359','Bertioga\r'),(3340,'3506409','Bilac\r'),(3341,'3506508','Birigui\r'),(3342,'3506607','Biritiba Mirim\r'),(3343,'3506706','Boa Esperança do Sul\r'),(3344,'3506805','Bocaina\r'),(3345,'3506904','Bofete\r'),(3346,'3507001','Boituva\r'),(3347,'3507100','Bom Jesus dos Perdões\r'),(3348,'3507159','Bom Sucesso de Itararé\r'),(3349,'3507209','Borá\r'),(3350,'3507308','Boracéia\r'),(3351,'3507407','Borborema\r'),(3352,'3507456','Borebi\r'),(3353,'3507506','Botucatu\r'),(3354,'3507605','Bragança Paulista\r'),(3355,'3507704','Braúna\r'),(3356,'3507753','Brejo Alegre\r'),(3357,'3507803','Brodowski\r'),(3358,'3507902','Brotas\r'),(3359,'3508009','Buri\r'),(3360,'3508108','Buritama\r'),(3361,'3508207','Buritizal\r'),(3362,'3508306','Cabrália Paulista\r'),(3363,'3508405','Cabreúva\r'),(3364,'3508504','Caçapava\r'),(3365,'3508603','Cachoeira Paulista\r'),(3366,'3508702','Caconde\r'),(3367,'3508801','Cafelândia\r'),(3368,'3508900','Caiabu\r'),(3369,'3509007','Caieiras\r'),(3370,'3509106','Caiuá\r'),(3371,'3509205','Cajamar\r'),(3372,'3509254','Cajati\r'),(3373,'3509304','Cajobi\r'),(3374,'3509403','Cajuru\r'),(3375,'3509452','Campina do Monte Alegre\r'),(3376,'3509502','Campinas\r'),(3377,'3509601','Campo Limpo Paulista\r'),(3378,'3509700','Campos do Jordão\r'),(3379,'3509809','Campos Novos Paulista\r'),(3380,'3509908','Cananéia\r'),(3381,'3509957','Canas\r'),(3382,'3510005','Cândido Mota\r'),(3383,'3510104','Cândido Rodrigues\r'),(3384,'3510153','Canitar\r'),(3385,'3510203','Capão Bonito\r'),(3386,'3510302','Capela do Alto\r'),(3387,'3510401','Capivari\r'),(3388,'3510500','Caraguatatuba\r'),(3389,'3510609','Carapicuíba\r'),(3390,'3510708','Cardoso\r'),(3391,'3510807','Casa Branca\r'),(3392,'3510906','Cássia dos Coqueiros\r'),(3393,'3511003','Castilho\r'),(3394,'3511102','Catanduva\r'),(3395,'3511201','Catiguá\r'),(3396,'3511300','Cedral\r'),(3397,'3511409','Cerqueira César\r'),(3398,'3511508','Cerquilho\r'),(3399,'3511607','Cesário Lange\r'),(3400,'3511706','Charqueada\r'),(3401,'3557204','Chavantes\r'),(3402,'3511904','Clementina\r'),(3403,'3512001','Colina\r'),(3404,'3512100','Colômbia\r'),(3405,'3512209','Conchal\r'),(3406,'3512308','Conchas\r'),(3407,'3512407','Cordeirópolis\r'),(3408,'3512506','Coroados\r'),(3409,'3512605','Coronel Macedo\r'),(3410,'3512704','Corumbataí\r'),(3411,'3512803','Cosmópolis\r'),(3412,'3512902','Cosmorama\r'),(3413,'3513009','Cotia\r'),(3414,'3513108','Cravinhos\r'),(3415,'3513207','Cristais Paulista\r'),(3416,'3513306','Cruzália\r'),(3417,'3513405','Cruzeiro\r'),(3418,'3513504','Cubatão\r'),(3419,'3513603','Cunha\r'),(3420,'3513702','Descalvado\r'),(3421,'3513801','Diadema\r'),(3422,'3513850','Dirce Reis\r'),(3423,'3513900','Divinolândia\r'),(3424,'3514007','Dobrada\r'),(3425,'3514106','Dois Córregos\r'),(3426,'3514205','Dolcinópolis\r'),(3427,'3514304','Dourado\r'),(3428,'3514403','Dracena\r'),(3429,'3514502','Duartina\r'),(3430,'3514601','Dumont\r'),(3431,'3514700','Echaporã\r'),(3432,'3514809','Eldorado\r'),(3433,'3514908','Elias Fausto\r'),(3434,'3514924','Elisiário\r'),(3435,'3514957','Embaúba\r'),(3436,'3515004','Embu das Artes\r'),(3437,'3515103','Embu-Guaçu\r'),(3438,'3515129','Emilianópolis\r'),(3439,'3515152','Engenheiro Coelho\r'),(3440,'3515186','Espírito Santo do Pinhal\r'),(3441,'3515194','Espírito Santo do Turvo\r'),(3442,'3557303','Estiva Gerbi\r'),(3443,'3515301','Estrela do Norte\r'),(3444,'3515202','Estrela dOeste\r'),(3445,'3515350','Euclides da Cunha Paulista\r'),(3446,'3515400','Fartura\r'),(3447,'3515608','Fernando Prestes\r'),(3448,'3515509','Fernandópolis\r'),(3449,'3515657','Fernão\r'),(3450,'3515707','Ferraz de Vasconcelos\r'),(3451,'3515806','Flora Rica\r'),(3452,'3515905','Floreal\r'),(3453,'3516002','Flórida Paulista\r'),(3454,'3516101','Florínea\r'),(3455,'3516200','Franca\r'),(3456,'3516309','Francisco Morato\r'),(3457,'3516408','Franco da Rocha\r'),(3458,'3516507','Gabriel Monteiro\r'),(3459,'3516606','Gália\r'),(3460,'3516705','Garça\r'),(3461,'3516804','Gastão Vidigal\r'),(3462,'3516853','Gavião Peixoto\r'),(3463,'3516903','General Salgado\r'),(3464,'3517000','Getulina\r'),(3465,'3517109','Glicério\r'),(3466,'3517208','Guaiçara\r'),(3467,'3517307','Guaimbê\r'),(3468,'3517406','Guaíra\r'),(3469,'3517505','Guapiaçu\r'),(3470,'3517604','Guapiara\r'),(3471,'3517703','Guará\r'),(3472,'3517802','Guaraçaí\r'),(3473,'3517901','Guaraci\r'),(3474,'3518008','Guarani dOeste\r'),(3475,'3518107','Guarantã\r'),(3476,'3518206','Guararapes\r'),(3477,'3518305','Guararema\r'),(3478,'3518404','Guaratinguetá\r'),(3479,'3518503','Guareí\r'),(3480,'3518602','Guariba\r'),(3481,'3518701','Guarujá\r'),(3482,'3518800','Guarulhos\r'),(3483,'3518859','Guatapará\r'),(3484,'3518909','Guzolândia\r'),(3485,'3519006','Herculândia\r'),(3486,'3519055','Holambra\r'),(3487,'3519071','Hortolândia\r'),(3488,'3519105','Iacanga\r'),(3489,'3519204','Iacri\r'),(3490,'3519253','Iaras\r'),(3491,'3519303','Ibaté\r'),(3492,'3519402','Ibirá\r'),(3493,'3519501','Ibirarema\r'),(3494,'3519600','Ibitinga\r'),(3495,'3519709','Ibiúna\r'),(3496,'3519808','Icém\r'),(3497,'3519907','Iepê\r'),(3498,'3520004','Igaraçu do Tietê\r'),(3499,'3520103','Igarapava\r'),(3500,'3520202','Igaratá\r'),(3501,'3520301','Iguape\r'),(3502,'3520426','Ilha Comprida\r'),(3503,'3520442','Ilha Solteira\r'),(3504,'3520400','Ilhabela\r'),(3505,'3520509','Indaiatuba\r'),(3506,'3520608','Indiana\r'),(3507,'3520707','Indiaporã\r'),(3508,'3520806','Inúbia Paulista\r'),(3509,'3520905','Ipaussu\r'),(3510,'3521002','Iperó\r'),(3511,'3521101','Ipeúna\r'),(3512,'3521150','Ipiguá\r'),(3513,'3521200','Iporanga\r'),(3514,'3521309','Ipuã\r'),(3515,'3521408','Iracemápolis\r'),(3516,'3521507','Irapuã\r'),(3517,'3521606','Irapuru\r'),(3518,'3521705','Itaberá\r'),(3519,'3521804','Itaí\r'),(3520,'3521903','Itajobi\r'),(3521,'3522000','Itaju\r'),(3522,'3522109','Itanhaém\r'),(3523,'3522158','Itaoca\r'),(3524,'3522208','Itapecerica da Serra\r'),(3525,'3522307','Itapetininga\r'),(3526,'3522406','Itapeva\r'),(3527,'3522505','Itapevi\r'),(3528,'3522604','Itapira\r'),(3529,'3522653','Itapirapuã Paulista\r'),(3530,'3522703','Itápolis\r'),(3531,'3522802','Itaporanga\r'),(3532,'3522901','Itapuí\r'),(3533,'3523008','Itapura\r'),(3534,'3523107','Itaquaquecetuba\r'),(3535,'3523206','Itararé\r'),(3536,'3523305','Itariri\r'),(3537,'3523404','Itatiba\r'),(3538,'3523503','Itatinga\r'),(3539,'3523602','Itirapina\r'),(3540,'3523701','Itirapuã\r'),(3541,'3523800','Itobi\r'),(3542,'3523909','Itu\r'),(3543,'3524006','Itupeva\r'),(3544,'3524105','Ituverava\r'),(3545,'3524204','Jaborandi\r'),(3546,'3524303','Jaboticabal\r'),(3547,'3524402','Jacareí\r'),(3548,'3524501','Jaci\r'),(3549,'3524600','Jacupiranga\r'),(3550,'3524709','Jaguariúna\r'),(3551,'3524808','Jales\r'),(3552,'3524907','Jambeiro\r'),(3553,'3525003','Jandira\r'),(3554,'3525102','Jardinópolis\r'),(3555,'3525201','Jarinu\r'),(3556,'3525300','Jaú\r'),(3557,'3525409','Jeriquara\r'),(3558,'3525508','Joanópolis\r'),(3559,'3525607','João Ramalho\r'),(3560,'3525706','José Bonifácio\r'),(3561,'3525805','Júlio Mesquita\r'),(3562,'3525854','Jumirim\r'),(3563,'3525904','Jundiaí\r'),(3564,'3526001','Junqueirópolis\r'),(3565,'3526100','Juquiá\r'),(3566,'3526209','Juquitiba\r'),(3567,'3526308','Lagoinha\r'),(3568,'3526407','Laranjal Paulista\r'),(3569,'3526506','Lavínia\r'),(3570,'3526605','Lavrinhas\r'),(3571,'3526704','Leme\r'),(3572,'3526803','Lençóis Paulista\r'),(3573,'3526902','Limeira\r'),(3574,'3527009','Lindóia\r'),(3575,'3527108','Lins\r'),(3576,'3527207','Lorena\r'),(3577,'3527256','Lourdes\r'),(3578,'3527306','Louveira\r'),(3579,'3527405','Lucélia\r'),(3580,'3527504','Lucianópolis\r'),(3581,'3527603','Luís Antônio\r'),(3582,'3527702','Luiziânia\r'),(3583,'3527801','Lupércio\r'),(3584,'3527900','Lutécia\r'),(3585,'3528007','Macatuba\r'),(3586,'3528106','Macaubal\r'),(3587,'3528205','Macedônia\r'),(3588,'3528304','Magda\r'),(3589,'3528403','Mairinque\r'),(3590,'3528502','Mairiporã\r'),(3591,'3528601','Manduri\r'),(3592,'3528700','Marabá Paulista\r'),(3593,'3528809','Maracaí\r'),(3594,'3528858','Marapoama\r'),(3595,'3528908','Mariápolis\r'),(3596,'3529005','Marília\r'),(3597,'3529104','Marinópolis\r'),(3598,'3529203','Martinópolis\r'),(3599,'3529302','Matão\r'),(3600,'3529401','Mauá\r'),(3601,'3529500','Mendonça\r'),(3602,'3529609','Meridiano\r'),(3603,'3529658','Mesópolis\r'),(3604,'3529708','Miguelópolis\r'),(3605,'3529807','Mineiros do Tietê\r'),(3606,'3530003','Mira Estrela\r'),(3607,'3529906','Miracatu\r'),(3608,'3530102','Mirandópolis\r'),(3609,'3530201','Mirante do Paranapanema\r'),(3610,'3530300','Mirassol\r'),(3611,'3530409','Mirassolândia\r'),(3612,'3530508','Mococa\r'),(3613,'3530607','Mogi das Cruzes\r'),(3614,'3530706','Mogi Guaçu\r'),(3615,'3530805','Mogi Mirim\r'),(3616,'3530904','Mombuca\r'),(3617,'3531001','Monções\r'),(3618,'3531100','Mongaguá\r'),(3619,'3531209','Monte Alegre do Sul\r'),(3620,'3531308','Monte Alto\r'),(3621,'3531407','Monte Aprazível\r'),(3622,'3531506','Monte Azul Paulista\r'),(3623,'3531605','Monte Castelo\r'),(3624,'3531803','Monte Mor\r'),(3625,'3531704','Monteiro Lobato\r'),(3626,'3531902','Morro Agudo\r'),(3627,'3532009','Morungaba\r'),(3628,'3532058','Motuca\r'),(3629,'3532108','Murutinga do Sul\r'),(3630,'3532157','Nantes\r'),(3631,'3532207','Narandiba\r'),(3632,'3532306','Natividade da Serra\r'),(3633,'3532405','Nazaré Paulista\r'),(3634,'3532504','Neves Paulista\r'),(3635,'3532603','Nhandeara\r'),(3636,'3532702','Nipoã\r'),(3637,'3532801','Nova Aliança\r'),(3638,'3532827','Nova Campina\r'),(3639,'3532843','Nova Canaã Paulista\r'),(3640,'3532868','Nova Castilho\r'),(3641,'3532900','Nova Europa\r'),(3642,'3533007','Nova Granada\r'),(3643,'3533106','Nova Guataporanga\r'),(3644,'3533205','Nova Independência\r'),(3645,'3533304','Nova Luzitânia\r'),(3646,'3533403','Nova Odessa\r'),(3647,'3533254','Novais\r'),(3648,'3533502','Novo Horizonte\r'),(3649,'3533601','Nuporanga\r'),(3650,'3533700','Ocauçu\r'),(3651,'3533809','Óleo\r'),(3652,'3533908','Olímpia\r'),(3653,'3534005','Onda Verde\r'),(3654,'3534104','Oriente\r'),(3655,'3534203','Orindiúva\r'),(3656,'3534302','Orlândia\r'),(3657,'3534401','Osasco\r'),(3658,'3534500','Oscar Bressane\r'),(3659,'3534609','Osvaldo Cruz\r'),(3660,'3534708','Ourinhos\r'),(3661,'3534807','Ouro Verde\r'),(3662,'3534757','Ouroeste\r'),(3663,'3534906','Pacaembu\r'),(3664,'3535002','Palestina\r'),(3665,'3535101','Palmares Paulista\r'),(3666,'3535200','Palmeira dOeste\r'),(3667,'3535309','Palmital\r'),(3668,'3535408','Panorama\r'),(3669,'3535507','Paraguaçu Paulista\r'),(3670,'3535606','Paraibuna\r'),(3671,'3535705','Paraíso\r'),(3672,'3535804','Paranapanema\r'),(3673,'3535903','Paranapuã\r'),(3674,'3536000','Parapuã\r'),(3675,'3536109','Pardinho\r'),(3676,'3536208','Pariquera-Açu\r'),(3677,'3536257','Parisi\r'),(3678,'3536307','Patrocínio Paulista\r'),(3679,'3536406','Paulicéia\r'),(3680,'3536505','Paulínia\r'),(3681,'3536570','Paulistânia\r'),(3682,'3536604','Paulo de Faria\r'),(3683,'3536703','Pederneiras\r'),(3684,'3536802','Pedra Bela\r'),(3685,'3536901','Pedranópolis\r'),(3686,'3537008','Pedregulho\r'),(3687,'3537107','Pedreira\r'),(3688,'3537156','Pedrinhas Paulista\r'),(3689,'3537206','Pedro de Toledo\r'),(3690,'3537305','Penápolis\r'),(3691,'3537404','Pereira Barreto\r'),(3692,'3537503','Pereiras\r'),(3693,'3537602','Peruíbe\r'),(3694,'3537701','Piacatu\r'),(3695,'3537800','Piedade\r'),(3696,'3537909','Pilar do Sul\r'),(3697,'3538006','Pindamonhangaba\r'),(3698,'3538105','Pindorama\r'),(3699,'3538204','Pinhalzinho\r'),(3700,'3538303','Piquerobi\r'),(3701,'3538501','Piquete\r'),(3702,'3538600','Piracaia\r'),(3703,'3538709','Piracicaba\r'),(3704,'3538808','Piraju\r'),(3705,'3538907','Pirajuí\r'),(3706,'3539004','Pirangi\r'),(3707,'3539103','Pirapora do Bom Jesus\r'),(3708,'3539202','Pirapozinho\r'),(3709,'3539301','Pirassununga\r'),(3710,'3539400','Piratininga\r'),(3711,'3539509','Pitangueiras\r'),(3712,'3539608','Planalto\r'),(3713,'3539707','Platina\r'),(3714,'3539806','Poá\r'),(3715,'3539905','Poloni\r'),(3716,'3540002','Pompéia\r'),(3717,'3540101','Pongaí\r'),(3718,'3540200','Pontal\r'),(3719,'3540259','Pontalinda\r'),(3720,'3540309','Pontes Gestal\r'),(3721,'3540408','Populina\r'),(3722,'3540507','Porangaba\r'),(3723,'3540606','Porto Feliz\r'),(3724,'3540705','Porto Ferreira\r'),(3725,'3540754','Potim\r'),(3726,'3540804','Potirendaba\r'),(3727,'3540853','Pracinha\r'),(3728,'3540903','Pradópolis\r'),(3729,'3541000','Praia Grande\r'),(3730,'3541059','Pratânia\r'),(3731,'3541109','Presidente Alves\r'),(3732,'3541208','Presidente Bernardes\r'),(3733,'3541307','Presidente Epitácio\r'),(3734,'3541406','Presidente Prudente\r'),(3735,'3541505','Presidente Venceslau\r'),(3736,'3541604','Promissão\r'),(3737,'3541653','Quadra\r'),(3738,'3541703','Quatá\r'),(3739,'3541802','Queiroz\r'),(3740,'3541901','Queluz\r'),(3741,'3542008','Quintana\r'),(3742,'3542107','Rafard\r'),(3743,'3542206','Rancharia\r'),(3744,'3542305','Redenção da Serra\r'),(3745,'3542404','Regente Feijó\r'),(3746,'3542503','Reginópolis\r'),(3747,'3542602','Registro\r'),(3748,'3542701','Restinga\r'),(3749,'3542800','Ribeira\r'),(3750,'3542909','Ribeirão Bonito\r'),(3751,'3543006','Ribeirão Branco\r'),(3752,'3543105','Ribeirão Corrente\r'),(3753,'3543204','Ribeirão do Sul\r'),(3754,'3543238','Ribeirão dos Índios\r'),(3755,'3543253','Ribeirão Grande\r'),(3756,'3543303','Ribeirão Pires\r'),(3757,'3543402','Ribeirão Preto\r'),(3758,'3543600','Rifaina\r'),(3759,'3543709','Rincão\r'),(3760,'3543808','Rinópolis\r'),(3761,'3543907','Rio Claro\r'),(3762,'3544004','Rio das Pedras\r'),(3763,'3544103','Rio Grande da Serra\r'),(3764,'3544202','Riolândia\r'),(3765,'3543501','Riversul\r'),(3766,'3544251','Rosana\r'),(3767,'3544301','Roseira\r'),(3768,'3544400','Rubiácea\r'),(3769,'3544509','Rubinéia\r'),(3770,'3544608','Sabino\r'),(3771,'3544707','Sagres\r'),(3772,'3544806','Sales\r'),(3773,'3544905','Sales Oliveira\r'),(3774,'3545001','Salesópolis\r'),(3775,'3545100','Salmourão\r'),(3776,'3545159','Saltinho\r'),(3777,'3545209','Salto\r'),(3778,'3545308','Salto de Pirapora\r'),(3779,'3545407','Salto Grande\r'),(3780,'3545506','Sandovalina\r'),(3781,'3545605','Santa Adélia\r'),(3782,'3545704','Santa Albertina\r'),(3783,'3545803','Santa Bárbara dOeste\r'),(3784,'3546009','Santa Branca\r'),(3785,'3546108','Santa Clara dOeste\r'),(3786,'3546207','Santa Cruz da Conceição\r'),(3787,'3546256','Santa Cruz da Esperança\r'),(3788,'3546306','Santa Cruz das Palmeiras\r'),(3789,'3546405','Santa Cruz do Rio Pardo\r'),(3790,'3546504','Santa Ernestina\r'),(3791,'3546603','Santa Fé do Sul\r'),(3792,'3546702','Santa Gertrudes\r'),(3793,'3546801','Santa Isabel\r'),(3794,'3546900','Santa Lúcia\r'),(3795,'3547007','Santa Maria da Serra\r'),(3796,'3547106','Santa Mercedes\r'),(3797,'3547502','Santa Rita do Passa Quatro\r'),(3798,'3547403','Santa Rita dOeste\r'),(3799,'3547601','Santa Rosa de Viterbo\r'),(3800,'3547650','Santa Salete\r'),(3801,'3547205','Santana da Ponte Pensa\r'),(3802,'3547304','Santana de Parnaíba\r'),(3803,'3547700','Santo Anastácio\r'),(3804,'3547809','Santo André\r'),(3805,'3547908','Santo Antônio da Alegria\r'),(3806,'3548005','Santo Antônio de Posse\r'),(3807,'3548054','Santo Antônio do Aracanguá\r'),(3808,'3548104','Santo Antônio do Jardim\r'),(3809,'3548203','Santo Antônio do Pinhal\r'),(3810,'3548302','Santo Expedito\r'),(3811,'3548401','Santópolis do Aguapeí\r'),(3812,'3548500','Santos\r'),(3813,'3548609','São Bento do Sapucaí\r'),(3814,'3548708','São Bernardo do Campo\r'),(3815,'3548807','São Caetano do Sul\r'),(3816,'3548906','São Carlos\r'),(3817,'3549003','São Francisco\r'),(3818,'3549102','São João da Boa Vista\r'),(3819,'3549201','São João das Duas Pontes\r'),(3820,'3549250','São João de Iracema\r'),(3821,'3549300','São João do Pau dAlho\r'),(3822,'3549409','São Joaquim da Barra\r'),(3823,'3549508','São José da Bela Vista\r'),(3824,'3549607','São José do Barreiro\r'),(3825,'3549706','São José do Rio Pardo\r'),(3826,'3549805','São José do Rio Preto\r'),(3827,'3549904','São José dos Campos\r'),(3828,'3549953','São Lourenço da Serra\r'),(3829,'3550001','São Luiz do Paraitinga\r'),(3830,'3550100','São Manuel\r'),(3831,'3550209','São Miguel Arcanjo\r'),(3832,'3550308','São Paulo\r'),(3833,'3550407','São Pedro\r'),(3834,'3550506','São Pedro do Turvo\r'),(3835,'3550605','São Roque\r'),(3836,'3550704','São Sebastião\r'),(3837,'3550803','São Sebastião da Grama\r'),(3838,'3550902','São Simão\r'),(3839,'3551009','São Vicente\r'),(3840,'3551108','Sarapuí\r'),(3841,'3551207','Sarutaiá\r'),(3842,'3551306','Sebastianópolis do Sul\r'),(3843,'3551405','Serra Azul\r'),(3844,'3551603','Serra Negra\r'),(3845,'3551504','Serrana\r'),(3846,'3551702','Sertãozinho\r'),(3847,'3551801','Sete Barras\r'),(3848,'3551900','Severínia\r'),(3849,'3552007','Silveiras\r'),(3850,'3552106','Socorro\r'),(3851,'3552205','Sorocaba\r'),(3852,'3552304','Sud Mennucci\r'),(3853,'3552403','Sumaré\r'),(3854,'3552551','Suzanápolis\r'),(3855,'3552502','Suzano\r'),(3856,'3552601','Tabapuã\r'),(3857,'3552700','Tabatinga\r'),(3858,'3552809','Taboão da Serra\r'),(3859,'3552908','Taciba\r'),(3860,'3553005','Taguaí\r'),(3861,'3553104','Taiaçu\r'),(3862,'3553203','Taiúva\r'),(3863,'3553302','Tambaú\r'),(3864,'3553401','Tanabi\r'),(3865,'3553500','Tapiraí\r'),(3866,'3553609','Tapiratiba\r'),(3867,'3553658','Taquaral\r'),(3868,'3553708','Taquaritinga\r'),(3869,'3553807','Taquarituba\r'),(3870,'3553856','Taquarivaí\r'),(3871,'3553906','Tarabai\r'),(3872,'3553955','Tarumã\r'),(3873,'3554003','Tatuí\r'),(3874,'3554102','Taubaté\r'),(3875,'3554201','Tejupá\r'),(3876,'3554300','Teodoro Sampaio\r'),(3877,'3554409','Terra Roxa\r'),(3878,'3554508','Tietê\r'),(3879,'3554607','Timburi\r'),(3880,'3554656','Torre de Pedra\r'),(3881,'3554706','Torrinha\r'),(3882,'3554755','Trabiju\r'),(3883,'3554805','Tremembé\r'),(3884,'3554904','Três Fronteiras\r'),(3885,'3554953','Tuiuti\r'),(3886,'3555000','Tupã\r'),(3887,'3555109','Tupi Paulista\r'),(3888,'3555208','Turiúba\r'),(3889,'3555307','Turmalina\r'),(3890,'3555356','Ubarana\r'),(3891,'3555406','Ubatuba\r'),(3892,'3555505','Ubirajara\r'),(3893,'3555604','Uchoa\r'),(3894,'3555703','União Paulista\r'),(3895,'3555802','Urânia\r'),(3896,'3555901','Uru\r'),(3897,'3556008','Urupês\r'),(3898,'3556107','Valentim Gentil\r'),(3899,'3556206','Valinhos\r'),(3900,'3556305','Valparaíso\r'),(3901,'3556354','Vargem\r'),(3902,'3556404','Vargem Grande do Sul\r'),(3903,'3556453','Vargem Grande Paulista\r'),(3904,'3556503','Várzea Paulista\r'),(3905,'3556602','Vera Cruz\r'),(3906,'3556701','Vinhedo\r'),(3907,'3556800','Viradouro\r'),(3908,'3556909','Vista Alegre do Alto\r'),(3909,'3556958','Vitória Brasil\r'),(3910,'3557006','Votorantim\r'),(3911,'3557105','Votuporanga\r'),(3912,'3557154','Zacarias\r'),(3913,'4100103','Abatiá\r'),(3914,'4100202','Adrianópolis\r'),(3915,'4100301','Agudos do Sul\r'),(3916,'4100400','Almirante Tamandaré\r'),(3917,'4100459','Altamira do Paraná\r'),(3918,'4128625','Alto Paraíso\r'),(3919,'4100608','Alto Paraná\r'),(3920,'4100707','Alto Piquiri\r'),(3921,'4100509','Altônia\r'),(3922,'4100806','Alvorada do Sul\r'),(3923,'4100905','Amaporã\r'),(3924,'4101002','Ampére\r'),(3925,'4101051','Anahy\r'),(3926,'4101101','Andirá\r'),(3927,'4101150','Ângulo\r'),(3928,'4101200','Antonina\r'),(3929,'4101309','Antônio Olinto\r'),(3930,'4101408','Apucarana\r'),(3931,'4101507','Arapongas\r'),(3932,'4101606','Arapoti\r'),(3933,'4101655','Arapuã\r'),(3934,'4101705','Araruna\r'),(3935,'4101804','Araucária\r'),(3936,'4101853','Ariranha do Ivaí\r'),(3937,'4101903','Assaí\r'),(3938,'4102000','Assis Chateaubriand\r'),(3939,'4102109','Astorga\r'),(3940,'4102208','Atalaia\r'),(3941,'4102307','Balsa Nova\r'),(3942,'4102406','Bandeirantes\r'),(3943,'4102505','Barbosa Ferraz\r'),(3944,'4102703','Barra do Jacaré\r'),(3945,'4102604','Barracão\r'),(3946,'4102752','Bela Vista da Caroba\r'),(3947,'4102802','Bela Vista do Paraíso\r'),(3948,'4102901','Bituruna\r'),(3949,'4103008','Boa Esperança\r'),(3950,'4103024','Boa Esperança do Iguaçu\r'),(3951,'4103040','Boa Ventura de São Roque\r'),(3952,'4103057','Boa Vista da Aparecida\r'),(3953,'4103107','Bocaiúva do Sul\r'),(3954,'4103156','Bom Jesus do Sul\r'),(3955,'4103206','Bom Sucesso\r'),(3956,'4103222','Bom Sucesso do Sul\r'),(3957,'4103305','Borrazópolis\r'),(3958,'4103354','Braganey\r'),(3959,'4103370','Brasilândia do Sul\r'),(3960,'4103404','Cafeara\r'),(3961,'4103453','Cafelândia\r'),(3962,'4103479','Cafezal do Sul\r'),(3963,'4103503','Califórnia\r'),(3964,'4103602','Cambará\r'),(3965,'4103701','Cambé\r'),(3966,'4103800','Cambira\r'),(3967,'4103909','Campina da Lagoa\r'),(3968,'4103958','Campina do Simão\r'),(3969,'4104006','Campina Grande do Sul\r'),(3970,'4104055','Campo Bonito\r'),(3971,'4104105','Campo do Tenente\r'),(3972,'4104204','Campo Largo\r'),(3973,'4104253','Campo Magro\r'),(3974,'4104303','Campo Mourão\r'),(3975,'4104402','Cândido de Abreu\r'),(3976,'4104428','Candói\r'),(3977,'4104451','Cantagalo\r'),(3978,'4104501','Capanema\r'),(3979,'4104600','Capitão Leônidas Marques\r'),(3980,'4104659','Carambeí\r'),(3981,'4104709','Carlópolis\r'),(3982,'4104808','Cascavel\r'),(3983,'4104907','Castro\r'),(3984,'4105003','Catanduvas\r'),(3985,'4105102','Centenário do Sul\r'),(3986,'4105201','Cerro Azul\r'),(3987,'4105300','Céu Azul\r'),(3988,'4105409','Chopinzinho\r'),(3989,'4105508','Cianorte\r'),(3990,'4105607','Cidade Gaúcha\r'),(3991,'4105706','Clevelândia\r'),(3992,'4105805','Colombo\r'),(3993,'4105904','Colorado\r'),(3994,'4106001','Congonhinhas\r'),(3995,'4106100','Conselheiro Mairinck\r'),(3996,'4106209','Contenda\r'),(3997,'4106308','Corbélia\r'),(3998,'4106407','Cornélio Procópio\r'),(3999,'4106456','Coronel Domingos Soares\r'),(4000,'4106506','Coronel Vivida\r'),(4001,'4106555','Corumbataí do Sul\r'),(4002,'4106803','Cruz Machado\r'),(4003,'4106571','Cruzeiro do Iguaçu\r'),(4004,'4106605','Cruzeiro do Oeste\r'),(4005,'4106704','Cruzeiro do Sul\r'),(4006,'4106852','Cruzmaltina\r'),(4007,'4106902','Curitiba\r'),(4008,'4107009','Curiúva\r'),(4009,'4107108','Diamante do Norte\r'),(4010,'4107124','Diamante do Sul\r'),(4011,'4107157','Diamante DOeste\r'),(4012,'4107207','Dois Vizinhos\r'),(4013,'4107256','Douradina\r'),(4014,'4107306','Doutor Camargo\r'),(4015,'4128633','Doutor Ulysses\r'),(4016,'4107405','Enéas Marques\r'),(4017,'4107504','Engenheiro Beltrão\r'),(4018,'4107538','Entre Rios do Oeste\r'),(4019,'4107520','Esperança Nova\r'),(4020,'4107546','Espigão Alto do Iguaçu\r'),(4021,'4107553','Farol\r'),(4022,'4107603','Faxinal\r'),(4023,'4107652','Fazenda Rio Grande\r'),(4024,'4107702','Fênix\r'),(4025,'4107736','Fernandes Pinheiro\r'),(4026,'4107751','Figueira\r'),(4027,'4107850','Flor da Serra do Sul\r'),(4028,'4107801','Floraí\r'),(4029,'4107900','Floresta\r'),(4030,'4108007','Florestópolis\r'),(4031,'4108106','Flórida\r'),(4032,'4108205','Formosa do Oeste\r'),(4033,'4108304','Foz do Iguaçu\r'),(4034,'4108452','Foz do Jordão\r'),(4035,'4108320','Francisco Alves\r'),(4036,'4108403','Francisco Beltrão\r'),(4037,'4108502','General Carneiro\r'),(4038,'4108551','Godoy Moreira\r'),(4039,'4108601','Goioerê\r'),(4040,'4108650','Goioxim\r'),(4041,'4108700','Grandes Rios\r'),(4042,'4108809','Guaíra\r'),(4043,'4108908','Guairaçá\r'),(4044,'4108957','Guamiranga\r'),(4045,'4109005','Guapirama\r'),(4046,'4109104','Guaporema\r'),(4047,'4109203','Guaraci\r'),(4048,'4109302','Guaraniaçu\r'),(4049,'4109401','Guarapuava\r'),(4050,'4109500','Guaraqueçaba\r'),(4051,'4109609','Guaratuba\r'),(4052,'4109658','Honório Serpa\r'),(4053,'4109708','Ibaiti\r'),(4054,'4109757','Ibema\r'),(4055,'4109807','Ibiporã\r'),(4056,'4109906','Icaraíma\r'),(4057,'4110003','Iguaraçu\r'),(4058,'4110052','Iguatu\r'),(4059,'4110078','Imbaú\r'),(4060,'4110102','Imbituva\r'),(4061,'4110201','Inácio Martins\r'),(4062,'4110300','Inajá\r'),(4063,'4110409','Indianópolis\r'),(4064,'4110508','Ipiranga\r'),(4065,'4110607','Iporã\r'),(4066,'4110656','Iracema do Oeste\r'),(4067,'4110706','Irati\r'),(4068,'4110805','Iretama\r'),(4069,'4110904','Itaguajé\r'),(4070,'4110953','Itaipulândia\r'),(4071,'4111001','Itambaracá\r'),(4072,'4111100','Itambé\r'),(4073,'4111209','Itapejara dOeste\r'),(4074,'4111258','Itaperuçu\r'),(4075,'4111308','Itaúna do Sul\r'),(4076,'4111407','Ivaí\r'),(4077,'4111506','Ivaiporã\r'),(4078,'4111555','Ivaté\r'),(4079,'4111605','Ivatuba\r'),(4080,'4111704','Jaboti\r'),(4081,'4111803','Jacarezinho\r'),(4082,'4111902','Jaguapitã\r'),(4083,'4112009','Jaguariaíva\r'),(4084,'4112108','Jandaia do Sul\r'),(4085,'4112207','Janiópolis\r'),(4086,'4112306','Japira\r'),(4087,'4112405','Japurá\r'),(4088,'4112504','Jardim Alegre\r'),(4089,'4112603','Jardim Olinda\r'),(4090,'4112702','Jataizinho\r'),(4091,'4112751','Jesuítas\r'),(4092,'4112801','Joaquim Távora\r'),(4093,'4112900','Jundiaí do Sul\r'),(4094,'4112959','Juranda\r'),(4095,'4113007','Jussara\r'),(4096,'4113106','Kaloré\r'),(4097,'4113205','Lapa\r'),(4098,'4113254','Laranjal\r'),(4099,'4113304','Laranjeiras do Sul\r'),(4100,'4113403','Leópolis\r'),(4101,'4113429','Lidianópolis\r'),(4102,'4113452','Lindoeste\r'),(4103,'4113502','Loanda\r'),(4104,'4113601','Lobato\r'),(4105,'4113700','Londrina\r'),(4106,'4113734','Luiziana\r'),(4107,'4113759','Lunardelli\r'),(4108,'4113809','Lupionópolis\r'),(4109,'4113908','Mallet\r'),(4110,'4114005','Mamborê\r'),(4111,'4114104','Mandaguaçu\r'),(4112,'4114203','Mandaguari\r'),(4113,'4114302','Mandirituba\r'),(4114,'4114351','Manfrinópolis\r'),(4115,'4114401','Mangueirinha\r'),(4116,'4114500','Manoel Ribas\r'),(4117,'4114609','Marechal Cândido Rondon\r'),(4118,'4114708','Maria Helena\r'),(4119,'4114807','Marialva\r'),(4120,'4114906','Marilândia do Sul\r'),(4121,'4115002','Marilena\r'),(4122,'4115101','Mariluz\r'),(4123,'4115200','Maringá\r'),(4124,'4115309','Mariópolis\r'),(4125,'4115358','Maripá\r'),(4126,'4115408','Marmeleiro\r'),(4127,'4115457','Marquinho\r'),(4128,'4115507','Marumbi\r'),(4129,'4115606','Matelândia\r'),(4130,'4115705','Matinhos\r'),(4131,'4115739','Mato Rico\r'),(4132,'4115754','Mauá da Serra\r'),(4133,'4115804','Medianeira\r'),(4134,'4115853','Mercedes\r'),(4135,'4115903','Mirador\r'),(4136,'4116000','Miraselva\r'),(4137,'4116059','Missal\r'),(4138,'4116109','Moreira Sales\r'),(4139,'4116208','Morretes\r'),(4140,'4116307','Munhoz de Melo\r'),(4141,'4116406','Nossa Senhora das Graças\r'),(4142,'4116505','Nova Aliança do Ivaí\r'),(4143,'4116604','Nova América da Colina\r'),(4144,'4116703','Nova Aurora\r'),(4145,'4116802','Nova Cantu\r'),(4146,'4116901','Nova Esperança\r'),(4147,'4116950','Nova Esperança do Sudoeste\r'),(4148,'4117008','Nova Fátima\r'),(4149,'4117057','Nova Laranjeiras\r'),(4150,'4117107','Nova Londrina\r'),(4151,'4117206','Nova Olímpia\r'),(4152,'4117255','Nova Prata do Iguaçu\r'),(4153,'4117214','Nova Santa Bárbara\r'),(4154,'4117222','Nova Santa Rosa\r'),(4155,'4117271','Nova Tebas\r'),(4156,'4117297','Novo Itacolomi\r'),(4157,'4117305','Ortigueira\r'),(4158,'4117404','Ourizona\r'),(4159,'4117453','Ouro Verde do Oeste\r'),(4160,'4117503','Paiçandu\r'),(4161,'4117602','Palmas\r'),(4162,'4117701','Palmeira\r'),(4163,'4117800','Palmital\r'),(4164,'4117909','Palotina\r'),(4165,'4118006','Paraíso do Norte\r'),(4166,'4118105','Paranacity\r'),(4167,'4118204','Paranaguá\r'),(4168,'4118303','Paranapoema\r'),(4169,'4118402','Paranavaí\r'),(4170,'4118451','Pato Bragado\r'),(4171,'4118501','Pato Branco\r'),(4172,'4118600','Paula Freitas\r'),(4173,'4118709','Paulo Frontin\r'),(4174,'4118808','Peabiru\r'),(4175,'4118857','Perobal\r'),(4176,'4118907','Pérola\r'),(4177,'4119004','Pérola dOeste\r'),(4178,'4119103','Piên\r'),(4179,'4119152','Pinhais\r'),(4180,'4119251','Pinhal de São Bento\r'),(4181,'4119202','Pinhalão\r'),(4182,'4119301','Pinhão\r'),(4183,'4119400','Piraí do Sul\r'),(4184,'4119509','Piraquara\r'),(4185,'4119608','Pitanga\r'),(4186,'4119657','Pitangueiras\r'),(4187,'4119707','Planaltina do Paraná\r'),(4188,'4119806','Planalto\r'),(4189,'4119905','Ponta Grossa\r'),(4190,'4119954','Pontal do Paraná\r'),(4191,'4120002','Porecatu\r'),(4192,'4120101','Porto Amazonas\r'),(4193,'4120150','Porto Barreiro\r'),(4194,'4120200','Porto Rico\r'),(4195,'4120309','Porto Vitória\r'),(4196,'4120333','Prado Ferreira\r'),(4197,'4120358','Pranchita\r'),(4198,'4120408','Presidente Castelo Branco\r'),(4199,'4120507','Primeiro de Maio\r'),(4200,'4120606','Prudentópolis\r'),(4201,'4120655','Quarto Centenário\r'),(4202,'4120705','Quatiguá\r'),(4203,'4120804','Quatro Barras\r'),(4204,'4120853','Quatro Pontes\r'),(4205,'4120903','Quedas do Iguaçu\r'),(4206,'4121000','Querência do Norte\r'),(4207,'4121109','Quinta do Sol\r'),(4208,'4121208','Quitandinha\r'),(4209,'4121257','Ramilândia\r'),(4210,'4121307','Rancho Alegre\r'),(4211,'4121356','Rancho Alegre DOeste\r'),(4212,'4121406','Realeza\r'),(4213,'4121505','Rebouças\r'),(4214,'4121604','Renascença\r'),(4215,'4121703','Reserva\r'),(4216,'4121752','Reserva do Iguaçu\r'),(4217,'4121802','Ribeirão Claro\r'),(4218,'4121901','Ribeirão do Pinhal\r'),(4219,'4122008','Rio Azul\r'),(4220,'4122107','Rio Bom\r'),(4221,'4122156','Rio Bonito do Iguaçu\r'),(4222,'4122172','Rio Branco do Ivaí\r'),(4223,'4122206','Rio Branco do Sul\r'),(4224,'4122305','Rio Negro\r'),(4225,'4122404','Rolândia\r'),(4226,'4122503','Roncador\r'),(4227,'4122602','Rondon\r'),(4228,'4122651','Rosário do Ivaí\r'),(4229,'4122701','Sabáudia\r'),(4230,'4122800','Salgado Filho\r'),(4231,'4122909','Salto do Itararé\r'),(4232,'4123006','Salto do Lontra\r'),(4233,'4123105','Santa Amélia\r'),(4234,'4123204','Santa Cecília do Pavão\r'),(4235,'4123303','Santa Cruz de Monte Castelo\r'),(4236,'4123402','Santa Fé\r'),(4237,'4123501','Santa Helena\r'),(4238,'4123600','Santa Inês\r'),(4239,'4123709','Santa Isabel do Ivaí\r'),(4240,'4123808','Santa Izabel do Oeste\r'),(4241,'4123824','Santa Lúcia\r'),(4242,'4123857','Santa Maria do Oeste\r'),(4243,'4123907','Santa Mariana\r'),(4244,'4123956','Santa Mônica\r'),(4245,'4124020','Santa Tereza do Oeste\r'),(4246,'4124053','Santa Terezinha de Itaipu\r'),(4247,'4124004','Santana do Itararé\r'),(4248,'4124103','Santo Antônio da Platina\r'),(4249,'4124202','Santo Antônio do Caiuá\r'),(4250,'4124301','Santo Antônio do Paraíso\r'),(4251,'4124400','Santo Antônio do Sudoeste\r'),(4252,'4124509','Santo Inácio\r'),(4253,'4124608','São Carlos do Ivaí\r'),(4254,'4124707','São Jerônimo da Serra\r'),(4255,'4124806','São João\r'),(4256,'4124905','São João do Caiuá\r'),(4257,'4125001','São João do Ivaí\r'),(4258,'4125100','São João do Triunfo\r'),(4259,'4125308','São Jorge do Ivaí\r'),(4260,'4125357','São Jorge do Patrocínio\r'),(4261,'4125209','São Jorge dOeste\r'),(4262,'4125407','São José da Boa Vista\r'),(4263,'4125456','São José das Palmeiras\r'),(4264,'4125506','São José dos Pinhais\r'),(4265,'4125555','São Manoel do Paraná\r'),(4266,'4125605','São Mateus do Sul\r'),(4267,'4125704','São Miguel do Iguaçu\r'),(4268,'4125753','São Pedro do Iguaçu\r'),(4269,'4125803','São Pedro do Ivaí\r'),(4270,'4125902','São Pedro do Paraná\r'),(4271,'4126009','São Sebastião da Amoreira\r'),(4272,'4126108','São Tomé\r'),(4273,'4126207','Sapopema\r'),(4274,'4126256','Sarandi\r'),(4275,'4126272','Saudade do Iguaçu\r'),(4276,'4126306','Sengés\r'),(4277,'4126355','Serranópolis do Iguaçu\r'),(4278,'4126405','Sertaneja\r'),(4279,'4126504','Sertanópolis\r'),(4280,'4126603','Siqueira Campos\r'),(4281,'4126652','Sulina\r'),(4282,'4126678','Tamarana\r'),(4283,'4126702','Tamboara\r'),(4284,'4126801','Tapejara\r'),(4285,'4126900','Tapira\r'),(4286,'4127007','Teixeira Soares\r'),(4287,'4127106','Telêmaco Borba\r'),(4288,'4127205','Terra Boa\r'),(4289,'4127304','Terra Rica\r'),(4290,'4127403','Terra Roxa\r'),(4291,'4127502','Tibagi\r'),(4292,'4127601','Tijucas do Sul\r'),(4293,'4127700','Toledo\r'),(4294,'4127809','Tomazina\r'),(4295,'4127858','Três Barras do Paraná\r'),(4296,'4127882','Tunas do Paraná\r'),(4297,'4127908','Tuneiras do Oeste\r'),(4298,'4127957','Tupãssi\r'),(4299,'4127965','Turvo\r'),(4300,'4128005','Ubiratã\r'),(4301,'4128104','Umuarama\r'),(4302,'4128203','União da Vitória\r'),(4303,'4128302','Uniflor\r'),(4304,'4128401','Uraí\r'),(4305,'4128534','Ventania\r'),(4306,'4128559','Vera Cruz do Oeste\r'),(4307,'4128609','Verê\r'),(4308,'4128658','Virmond\r'),(4309,'4128708','Vitorino\r'),(4310,'4128500','Wenceslau Braz\r'),(4311,'4128807','Xambrê\r'),(4312,'4200051','Abdon Batista\r'),(4313,'4200101','Abelardo Luz\r'),(4314,'4200200','Agrolândia\r'),(4315,'4200309','Agronômica\r'),(4316,'4200408','Água Doce\r'),(4317,'4200507','Águas de Chapecó\r'),(4318,'4200556','Águas Frias\r'),(4319,'4200606','Águas Mornas\r'),(4320,'4200705','Alfredo Wagner\r'),(4321,'4200754','Alto Bela Vista\r'),(4322,'4200804','Anchieta\r'),(4323,'4200903','Angelina\r'),(4324,'4201000','Anita Garibaldi\r'),(4325,'4201109','Anitápolis\r'),(4326,'4201208','Antônio Carlos\r'),(4327,'4201257','Apiúna\r'),(4328,'4201273','Arabutã\r'),(4329,'4201307','Araquari\r'),(4330,'4201406','Araranguá\r'),(4331,'4201505','Armazém\r'),(4332,'4201604','Arroio Trinta\r'),(4333,'4201653','Arvoredo\r'),(4334,'4201703','Ascurra\r'),(4335,'4201802','Atalanta\r'),(4336,'4201901','Aurora\r'),(4337,'4201950','Balneário Arroio do Silva\r'),(4338,'4202057','Balneário Barra do Sul\r'),(4339,'4202008','Balneário Camboriú\r'),(4340,'4202073','Balneário Gaivota\r'),(4341,'4212809','Balneário Piçarras\r'),(4342,'4220000','Balneário Rincão\r'),(4343,'4202081','Bandeirante\r'),(4344,'4202099','Barra Bonita\r'),(4345,'4202107','Barra Velha\r'),(4346,'4202131','Bela Vista do Toldo\r'),(4347,'4202156','Belmonte\r'),(4348,'4202206','Benedito Novo\r'),(4349,'4202305','Biguaçu\r'),(4350,'4202404','Blumenau\r'),(4351,'4202438','Bocaina do Sul\r'),(4352,'4202503','Bom Jardim da Serra\r'),(4353,'4202537','Bom Jesus\r'),(4354,'4202578','Bom Jesus do Oeste\r'),(4355,'4202602','Bom Retiro\r'),(4356,'4202453','Bombinhas\r'),(4357,'4202701','Botuverá\r'),(4358,'4202800','Braço do Norte\r'),(4359,'4202859','Braço do Trombudo\r'),(4360,'4202875','Brunópolis\r'),(4361,'4202909','Brusque\r'),(4362,'4203006','Caçador\r'),(4363,'4203105','Caibi\r'),(4364,'4203154','Calmon\r'),(4365,'4203204','Camboriú\r'),(4366,'4203303','Campo Alegre\r'),(4367,'4203402','Campo Belo do Sul\r'),(4368,'4203501','Campo Erê\r'),(4369,'4203600','Campos Novos\r'),(4370,'4203709','Canelinha\r'),(4371,'4203808','Canoinhas\r'),(4372,'4203253','Capão Alto\r'),(4373,'4203907','Capinzal\r'),(4374,'4203956','Capivari de Baixo\r'),(4375,'4204004','Catanduvas\r'),(4376,'4204103','Caxambu do Sul\r'),(4377,'4204152','Celso Ramos\r'),(4378,'4204178','Cerro Negro\r'),(4379,'4204194','Chapadão do Lageado\r'),(4380,'4204202','Chapecó\r'),(4381,'4204251','Cocal do Sul\r'),(4382,'4204301','Concórdia\r'),(4383,'4204350','Cordilheira Alta\r'),(4384,'4204400','Coronel Freitas\r'),(4385,'4204459','Coronel Martins\r'),(4386,'4204558','Correia Pinto\r'),(4387,'4204509','Corupá\r'),(4388,'4204608','Criciúma\r'),(4389,'4204707','Cunha Porã\r'),(4390,'4204756','Cunhataí\r'),(4391,'4204806','Curitibanos\r'),(4392,'4204905','Descanso\r'),(4393,'4205001','Dionísio Cerqueira\r'),(4394,'4205100','Dona Emma\r'),(4395,'4205159','Doutor Pedrinho\r'),(4396,'4205175','Entre Rios\r'),(4397,'4205191','Ermo\r'),(4398,'4205209','Erval Velho\r'),(4399,'4205308','Faxinal dos Guedes\r'),(4400,'4205357','Flor do Sertão\r'),(4401,'4205407','Florianópolis\r'),(4402,'4205431','Formosa do Sul\r'),(4403,'4205456','Forquilhinha\r'),(4404,'4205506','Fraiburgo\r'),(4405,'4205555','Frei Rogério\r'),(4406,'4205605','Galvão\r'),(4407,'4205704','Garopaba\r'),(4408,'4205803','Garuva\r'),(4409,'4205902','Gaspar\r'),(4410,'4206009','Governador Celso Ramos\r'),(4411,'4206108','Grão Pará\r'),(4412,'4206207','Gravatal\r'),(4413,'4206306','Guabiruba\r'),(4414,'4206405','Guaraciaba\r'),(4415,'4206504','Guaramirim\r'),(4416,'4206603','Guarujá do Sul\r'),(4417,'4206652','Guatambú\r'),(4418,'4206702','Herval dOeste\r'),(4419,'4206751','Ibiam\r'),(4420,'4206801','Ibicaré\r'),(4421,'4206900','Ibirama\r'),(4422,'4207007','Içara\r'),(4423,'4207106','Ilhota\r'),(4424,'4207205','Imaruí\r'),(4425,'4207304','Imbituba\r'),(4426,'4207403','Imbuia\r'),(4427,'4207502','Indaial\r'),(4428,'4207577','Iomerê\r'),(4429,'4207601','Ipira\r'),(4430,'4207650','Iporã do Oeste\r'),(4431,'4207684','Ipuaçu\r'),(4432,'4207700','Ipumirim\r'),(4433,'4207759','Iraceminha\r'),(4434,'4207809','Irani\r'),(4435,'4207858','Irati\r'),(4436,'4207908','Irineópolis\r'),(4437,'4208005','Itá\r'),(4438,'4208104','Itaiópolis\r'),(4439,'4208203','Itajaí\r'),(4440,'4208302','Itapema\r'),(4441,'4208401','Itapiranga\r'),(4442,'4208450','Itapoá\r'),(4443,'4208500','Ituporanga\r'),(4444,'4208609','Jaborá\r'),(4445,'4208708','Jacinto Machado\r'),(4446,'4208807','Jaguaruna\r'),(4447,'4208906','Jaraguá do Sul\r'),(4448,'4208955','Jardinópolis\r'),(4449,'4209003','Joaçaba\r'),(4450,'4209102','Joinville\r'),(4451,'4209151','José Boiteux\r'),(4452,'4209177','Jupiá\r'),(4453,'4209201','Lacerdópolis\r'),(4454,'4209300','Lages\r'),(4455,'4209409','Laguna\r'),(4456,'4209458','Lajeado Grande\r'),(4457,'4209508','Laurentino\r'),(4458,'4209607','Lauro Müller\r'),(4459,'4209706','Lebon Régis\r'),(4460,'4209805','Leoberto Leal\r'),(4461,'4209854','Lindóia do Sul\r'),(4462,'4209904','Lontras\r'),(4463,'4210001','Luiz Alves\r'),(4464,'4210035','Luzerna\r'),(4465,'4210050','Macieira\r'),(4466,'4210100','Mafra\r'),(4467,'4210209','Major Gercino\r'),(4468,'4210308','Major Vieira\r'),(4469,'4210407','Maracajá\r'),(4470,'4210506','Maravilha\r'),(4471,'4210555','Marema\r'),(4472,'4210605','Massaranduba\r'),(4473,'4210704','Matos Costa\r'),(4474,'4210803','Meleiro\r'),(4475,'4210852','Mirim Doce\r'),(4476,'4210902','Modelo\r'),(4477,'4211009','Mondaí\r'),(4478,'4211058','Monte Carlo\r'),(4479,'4211108','Monte Castelo\r'),(4480,'4211207','Morro da Fumaça\r'),(4481,'4211256','Morro Grande\r'),(4482,'4211306','Navegantes\r'),(4483,'4211405','Nova Erechim\r'),(4484,'4211454','Nova Itaberaba\r'),(4485,'4211504','Nova Trento\r'),(4486,'4211603','Nova Veneza\r'),(4487,'4211652','Novo Horizonte\r'),(4488,'4211702','Orleans\r'),(4489,'4211751','Otacílio Costa\r'),(4490,'4211801','Ouro\r'),(4491,'4211850','Ouro Verde\r'),(4492,'4211876','Paial\r'),(4493,'4211892','Painel\r'),(4494,'4211900','Palhoça\r'),(4495,'4212007','Palma Sola\r'),(4496,'4212056','Palmeira\r'),(4497,'4212106','Palmitos\r'),(4498,'4212205','Papanduva\r'),(4499,'4212239','Paraíso\r'),(4500,'4212254','Passo de Torres\r'),(4501,'4212270','Passos Maia\r'),(4502,'4212304','Paulo Lopes\r'),(4503,'4212403','Pedras Grandes\r'),(4504,'4212502','Penha\r'),(4505,'4212601','Peritiba\r'),(4506,'4212650','Pescaria Brava\r'),(4507,'4212700','Petrolândia\r'),(4508,'4212908','Pinhalzinho\r'),(4509,'4213005','Pinheiro Preto\r'),(4510,'4213104','Piratuba\r'),(4511,'4213153','Planalto Alegre\r'),(4512,'4213203','Pomerode\r'),(4513,'4213302','Ponte Alta\r'),(4514,'4213351','Ponte Alta do Norte\r'),(4515,'4213401','Ponte Serrada\r'),(4516,'4213500','Porto Belo\r'),(4517,'4213609','Porto União\r'),(4518,'4213708','Pouso Redondo\r'),(4519,'4213807','Praia Grande\r'),(4520,'4213906','Presidente Castello Branco\r'),(4521,'4214003','Presidente Getúlio\r'),(4522,'4214102','Presidente Nereu\r'),(4523,'4214151','Princesa\r'),(4524,'4214201','Quilombo\r'),(4525,'4214300','Rancho Queimado\r'),(4526,'4214409','Rio das Antas\r'),(4527,'4214508','Rio do Campo\r'),(4528,'4214607','Rio do Oeste\r'),(4529,'4214805','Rio do Sul\r'),(4530,'4214706','Rio dos Cedros\r'),(4531,'4214904','Rio Fortuna\r'),(4532,'4215000','Rio Negrinho\r'),(4533,'4215059','Rio Rufino\r'),(4534,'4215075','Riqueza\r'),(4535,'4215109','Rodeio\r'),(4536,'4215208','Romelândia\r'),(4537,'4215307','Salete\r'),(4538,'4215356','Saltinho\r'),(4539,'4215406','Salto Veloso\r'),(4540,'4215455','Sangão\r'),(4541,'4215505','Santa Cecília\r'),(4542,'4215554','Santa Helena\r'),(4543,'4215604','Santa Rosa de Lima\r'),(4544,'4215653','Santa Rosa do Sul\r'),(4545,'4215679','Santa Terezinha\r'),(4546,'4215687','Santa Terezinha do Progresso\r'),(4547,'4215695','Santiago do Sul\r'),(4548,'4215703','Santo Amaro da Imperatriz\r'),(4549,'4215802','São Bento do Sul\r'),(4550,'4215752','São Bernardino\r'),(4551,'4215901','São Bonifácio\r'),(4552,'4216008','São Carlos\r'),(4553,'4216057','São Cristóvão do Sul\r'),(4554,'4216107','São Domingos\r'),(4555,'4216206','São Francisco do Sul\r'),(4556,'4216305','São João Batista\r'),(4557,'4216354','São João do Itaperiú\r'),(4558,'4216255','São João do Oeste\r'),(4559,'4216404','São João do Sul\r'),(4560,'4216503','São Joaquim\r'),(4561,'4216602','São José\r'),(4562,'4216701','São José do Cedro\r'),(4563,'4216800','São José do Cerrito\r'),(4564,'4216909','São Lourenço do Oeste\r'),(4565,'4217006','São Ludgero\r'),(4566,'4217105','São Martinho\r'),(4567,'4217154','São Miguel da Boa Vista\r'),(4568,'4217204','São Miguel do Oeste\r'),(4569,'4217253','São Pedro de Alcântara\r'),(4570,'4217303','Saudades\r'),(4571,'4217402','Schroeder\r'),(4572,'4217501','Seara\r'),(4573,'4217550','Serra Alta\r'),(4574,'4217600','Siderópolis\r'),(4575,'4217709','Sombrio\r'),(4576,'4217758','Sul Brasil\r'),(4577,'4217808','Taió\r'),(4578,'4217907','Tangará\r'),(4579,'4217956','Tigrinhos\r'),(4580,'4218004','Tijucas\r'),(4581,'4218103','Timbé do Sul\r'),(4582,'4218202','Timbó\r'),(4583,'4218251','Timbó Grande\r'),(4584,'4218301','Três Barras\r'),(4585,'4218350','Treviso\r'),(4586,'4218400','Treze de Maio\r'),(4587,'4218509','Treze Tílias\r'),(4588,'4218608','Trombudo Central\r'),(4589,'4218707','Tubarão\r'),(4590,'4218756','Tunápolis\r'),(4591,'4218806','Turvo\r'),(4592,'4218855','União do Oeste\r'),(4593,'4218905','Urubici\r'),(4594,'4218954','Urupema\r'),(4595,'4219002','Urussanga\r'),(4596,'4219101','Vargeão\r'),(4597,'4219150','Vargem\r'),(4598,'4219176','Vargem Bonita\r'),(4599,'4219200','Vidal Ramos\r'),(4600,'4219309','Videira\r'),(4601,'4219358','Vitor Meireles\r'),(4602,'4219408','Witmarsum\r'),(4603,'4219507','Xanxerê\r'),(4604,'4219606','Xavantina\r'),(4605,'4219705','Xaxim\r'),(4606,'4219853','Zortéa\r'),(4607,'4300034','Aceguá\r'),(4608,'4300059','Água Santa\r'),(4609,'4300109','Agudo\r'),(4610,'4300208','Ajuricaba\r'),(4611,'4300307','Alecrim\r'),(4612,'4300406','Alegrete\r'),(4613,'4300455','Alegria\r'),(4614,'4300471','Almirante Tamandaré do Sul\r'),(4615,'4300505','Alpestre\r'),(4616,'4300554','Alto Alegre\r'),(4617,'4300570','Alto Feliz\r'),(4618,'4300604','Alvorada\r'),(4619,'4300638','Amaral Ferrador\r'),(4620,'4300646','Ametista do Sul\r'),(4621,'4300661','André da Rocha\r'),(4622,'4300703','Anta Gorda\r'),(4623,'4300802','Antônio Prado\r'),(4624,'4300851','Arambaré\r'),(4625,'4300877','Araricá\r'),(4626,'4300901','Aratiba\r'),(4627,'4301008','Arroio do Meio\r'),(4628,'4301073','Arroio do Padre\r'),(4629,'4301057','Arroio do Sal\r'),(4630,'4301206','Arroio do Tigre\r'),(4631,'4301107','Arroio dos Ratos\r'),(4632,'4301305','Arroio Grande\r'),(4633,'4301404','Arvorezinha\r'),(4634,'4301503','Augusto Pestana\r'),(4635,'4301552','Áurea\r'),(4636,'4301602','Bagé\r'),(4637,'4301636','Balneário Pinhal\r'),(4638,'4301651','Barão\r'),(4639,'4301701','Barão de Cotegipe\r'),(4640,'4301750','Barão do Triunfo\r'),(4641,'4301859','Barra do Guarita\r'),(4642,'4301875','Barra do Quaraí\r'),(4643,'4301909','Barra do Ribeiro\r'),(4644,'4301925','Barra do Rio Azul\r'),(4645,'4301958','Barra Funda\r'),(4646,'4301800','Barracão\r'),(4647,'4302006','Barros Cassal\r'),(4648,'4302055','Benjamin Constant do Sul\r'),(4649,'4302105','Bento Gonçalves\r'),(4650,'4302154','Boa Vista das Missões\r'),(4651,'4302204','Boa Vista do Buricá\r'),(4652,'4302220','Boa Vista do Cadeado\r'),(4653,'4302238','Boa Vista do Incra\r'),(4654,'4302253','Boa Vista do Sul\r'),(4655,'4302303','Bom Jesus\r'),(4656,'4302352','Bom Princípio\r'),(4657,'4302378','Bom Progresso\r'),(4658,'4302402','Bom Retiro do Sul\r'),(4659,'4302451','Boqueirão do Leão\r'),(4660,'4302501','Bossoroca\r'),(4661,'4302584','Bozano\r'),(4662,'4302600','Braga\r'),(4663,'4302659','Brochier\r'),(4664,'4302709','Butiá\r'),(4665,'4302808','Caçapava do Sul\r'),(4666,'4302907','Cacequi\r'),(4667,'4303004','Cachoeira do Sul\r'),(4668,'4303103','Cachoeirinha\r'),(4669,'4303202','Cacique Doble\r'),(4670,'4303301','Caibaté\r'),(4671,'4303400','Caiçara\r'),(4672,'4303509','Camaquã\r'),(4673,'4303558','Camargo\r'),(4674,'4303608','Cambará do Sul\r'),(4675,'4303673','Campestre da Serra\r'),(4676,'4303707','Campina das Missões\r'),(4677,'4303806','Campinas do Sul\r'),(4678,'4303905','Campo Bom\r'),(4679,'4304002','Campo Novo\r'),(4680,'4304101','Campos Borges\r'),(4681,'4304200','Candelária\r'),(4682,'4304309','Cândido Godói\r'),(4683,'4304358','Candiota\r'),(4684,'4304408','Canela\r'),(4685,'4304507','Canguçu\r'),(4686,'4304606','Canoas\r'),(4687,'4304614','Canudos do Vale\r'),(4688,'4304622','Capão Bonito do Sul\r'),(4689,'4304630','Capão da Canoa\r'),(4690,'4304655','Capão do Cipó\r'),(4691,'4304663','Capão do Leão\r'),(4692,'4304689','Capela de Santana\r'),(4693,'4304697','Capitão\r'),(4694,'4304671','Capivari do Sul\r'),(4695,'4304713','Caraá\r'),(4696,'4304705','Carazinho\r'),(4697,'4304804','Carlos Barbosa\r'),(4698,'4304853','Carlos Gomes\r'),(4699,'4304903','Casca\r'),(4700,'4304952','Caseiros\r'),(4701,'4305009','Catuípe\r'),(4702,'4305108','Caxias do Sul\r'),(4703,'4305116','Centenário\r'),(4704,'4305124','Cerrito\r'),(4705,'4305132','Cerro Branco\r'),(4706,'4305157','Cerro Grande\r'),(4707,'4305173','Cerro Grande do Sul\r'),(4708,'4305207','Cerro Largo\r'),(4709,'4305306','Chapada\r'),(4710,'4305355','Charqueadas\r'),(4711,'4305371','Charrua\r'),(4712,'4305405','Chiapetta\r'),(4713,'4305439','Chuí\r'),(4714,'4305447','Chuvisca\r'),(4715,'4305454','Cidreira\r'),(4716,'4305504','Ciríaco\r'),(4717,'4305587','Colinas\r'),(4718,'4305603','Colorado\r'),(4719,'4305702','Condor\r'),(4720,'4305801','Constantina\r'),(4721,'4305835','Coqueiro Baixo\r'),(4722,'4305850','Coqueiros do Sul\r'),(4723,'4305871','Coronel Barros\r'),(4724,'4305900','Coronel Bicaco\r'),(4725,'4305934','Coronel Pilar\r'),(4726,'4305959','Cotiporã\r'),(4727,'4305975','Coxilha\r'),(4728,'4306007','Crissiumal\r'),(4729,'4306056','Cristal\r'),(4730,'4306072','Cristal do Sul\r'),(4731,'4306106','Cruz Alta\r'),(4732,'4306130','Cruzaltense\r'),(4733,'4306205','Cruzeiro do Sul\r'),(4734,'4306304','David Canabarro\r'),(4735,'4306320','Derrubadas\r'),(4736,'4306353','Dezesseis de Novembro\r'),(4737,'4306379','Dilermando de Aguiar\r'),(4738,'4306403','Dois Irmãos\r'),(4739,'4306429','Dois Irmãos das Missões\r'),(4740,'4306452','Dois Lajeados\r'),(4741,'4306502','Dom Feliciano\r'),(4742,'4306601','Dom Pedrito\r'),(4743,'4306551','Dom Pedro de Alcântara\r'),(4744,'4306700','Dona Francisca\r'),(4745,'4306734','Doutor Maurício Cardoso\r'),(4746,'4306759','Doutor Ricardo\r'),(4747,'4306767','Eldorado do Sul\r'),(4748,'4306809','Encantado\r'),(4749,'4306908','Encruzilhada do Sul\r'),(4750,'4306924','Engenho Velho\r'),(4751,'4306957','Entre Rios do Sul\r'),(4752,'4306932','Entre-Ijuís\r'),(4753,'4306973','Erebango\r'),(4754,'4307005','Erechim\r'),(4755,'4307054','Ernestina\r'),(4756,'4307203','Erval Grande\r'),(4757,'4307302','Erval Seco\r'),(4758,'4307401','Esmeralda\r'),(4759,'4307450','Esperança do Sul\r'),(4760,'4307500','Espumoso\r'),(4761,'4307559','Estação\r'),(4762,'4307609','Estância Velha\r'),(4763,'4307708','Esteio\r'),(4764,'4307807','Estrela\r'),(4765,'4307815','Estrela Velha\r'),(4766,'4307831','Eugênio de Castro\r'),(4767,'4307864','Fagundes Varela\r'),(4768,'4307906','Farroupilha\r'),(4769,'4308003','Faxinal do Soturno\r'),(4770,'4308052','Faxinalzinho\r'),(4771,'4308078','Fazenda Vilanova\r'),(4772,'4308102','Feliz\r'),(4773,'4308201','Flores da Cunha\r'),(4774,'4308250','Floriano Peixoto\r'),(4775,'4308300','Fontoura Xavier\r'),(4776,'4308409','Formigueiro\r'),(4777,'4308433','Forquetinha\r'),(4778,'4308458','Fortaleza dos Valos\r'),(4779,'4308508','Frederico Westphalen\r'),(4780,'4308607','Garibaldi\r'),(4781,'4308656','Garruchos\r'),(4782,'4308706','Gaurama\r'),(4783,'4308805','General Câmara\r'),(4784,'4308854','Gentil\r'),(4785,'4308904','Getúlio Vargas\r'),(4786,'4309001','Giruá\r'),(4787,'4309050','Glorinha\r'),(4788,'4309100','Gramado\r'),(4789,'4309126','Gramado dos Loureiros\r'),(4790,'4309159','Gramado Xavier\r'),(4791,'4309209','Gravataí\r'),(4792,'4309258','Guabiju\r'),(4793,'4309308','Guaíba\r'),(4794,'4309407','Guaporé\r'),(4795,'4309506','Guarani das Missões\r'),(4796,'4309555','Harmonia\r'),(4797,'4307104','Herval\r'),(4798,'4309571','Herveiras\r'),(4799,'4309605','Horizontina\r'),(4800,'4309654','Hulha Negra\r'),(4801,'4309704','Humaitá\r'),(4802,'4309753','Ibarama\r'),(4803,'4309803','Ibiaçá\r'),(4804,'4309902','Ibiraiaras\r'),(4805,'4309951','Ibirapuitã\r'),(4806,'4310009','Ibirubá\r'),(4807,'4310108','Igrejinha\r'),(4808,'4310207','Ijuí\r'),(4809,'4310306','Ilópolis\r'),(4810,'4310330','Imbé\r'),(4811,'4310363','Imigrante\r'),(4812,'4310405','Independência\r'),(4813,'4310413','Inhacorá\r'),(4814,'4310439','Ipê\r'),(4815,'4310462','Ipiranga do Sul\r'),(4816,'4310504','Iraí\r'),(4817,'4310538','Itaara\r'),(4818,'4310553','Itacurubi\r'),(4819,'4310579','Itapuca\r'),(4820,'4310603','Itaqui\r'),(4821,'4310652','Itati\r'),(4822,'4310702','Itatiba do Sul\r'),(4823,'4310751','Ivorá\r'),(4824,'4310801','Ivoti\r'),(4825,'4310850','Jaboticaba\r'),(4826,'4310876','Jacuizinho\r'),(4827,'4310900','Jacutinga\r'),(4828,'4311007','Jaguarão\r'),(4829,'4311106','Jaguari\r'),(4830,'4311122','Jaquirana\r'),(4831,'4311130','Jari\r'),(4832,'4311155','Jóia\r'),(4833,'4311205','Júlio de Castilhos\r'),(4834,'4311239','Lagoa Bonita do Sul\r'),(4835,'4311270','Lagoa dos Três Cantos\r'),(4836,'4311304','Lagoa Vermelha\r'),(4837,'4311254','Lagoão\r'),(4838,'4311403','Lajeado\r'),(4839,'4311429','Lajeado do Bugre\r'),(4840,'4311502','Lavras do Sul\r'),(4841,'4311601','Liberato Salzano\r'),(4842,'4311627','Lindolfo Collor\r'),(4843,'4311643','Linha Nova\r'),(4844,'4311718','Maçambará\r'),(4845,'4311700','Machadinho\r'),(4846,'4311734','Mampituba\r'),(4847,'4311759','Manoel Viana\r'),(4848,'4311775','Maquiné\r'),(4849,'4311791','Maratá\r'),(4850,'4311809','Marau\r'),(4851,'4311908','Marcelino Ramos\r'),(4852,'4311981','Mariana Pimentel\r'),(4853,'4312005','Mariano Moro\r'),(4854,'4312054','Marques de Souza\r'),(4855,'4312104','Mata\r'),(4856,'4312138','Mato Castelhano\r'),(4857,'4312153','Mato Leitão\r'),(4858,'4312179','Mato Queimado\r'),(4859,'4312203','Maximiliano de Almeida\r'),(4860,'4312252','Minas do Leão\r'),(4861,'4312302','Miraguaí\r'),(4862,'4312351','Montauri\r'),(4863,'4312377','Monte Alegre dos Campos\r'),(4864,'4312385','Monte Belo do Sul\r'),(4865,'4312401','Montenegro\r'),(4866,'4312427','Mormaço\r'),(4867,'4312443','Morrinhos do Sul\r'),(4868,'4312450','Morro Redondo\r'),(4869,'4312476','Morro Reuter\r'),(4870,'4312500','Mostardas\r'),(4871,'4312609','Muçum\r'),(4872,'4312617','Muitos Capões\r'),(4873,'4312625','Muliterno\r'),(4874,'4312658','Não-Me-Toque\r'),(4875,'4312674','Nicolau Vergueiro\r'),(4876,'4312708','Nonoai\r'),(4877,'4312757','Nova Alvorada\r'),(4878,'4312807','Nova Araçá\r'),(4879,'4312906','Nova Bassano\r'),(4880,'4312955','Nova Boa Vista\r'),(4881,'4313003','Nova Bréscia\r'),(4882,'4313011','Nova Candelária\r'),(4883,'4313037','Nova Esperança do Sul\r'),(4884,'4313060','Nova Hartz\r'),(4885,'4313086','Nova Pádua\r'),(4886,'4313102','Nova Palma\r'),(4887,'4313201','Nova Petrópolis\r'),(4888,'4313300','Nova Prata\r'),(4889,'4313334','Nova Ramada\r'),(4890,'4313359','Nova Roma do Sul\r'),(4891,'4313375','Nova Santa Rita\r'),(4892,'4313490','Novo Barreiro\r'),(4893,'4313391','Novo Cabrais\r'),(4894,'4313409','Novo Hamburgo\r'),(4895,'4313425','Novo Machado\r'),(4896,'4313441','Novo Tiradentes\r'),(4897,'4313466','Novo Xingu\r'),(4898,'4313508','Osório\r'),(4899,'4313607','Paim Filho\r'),(4900,'4313656','Palmares do Sul\r'),(4901,'4313706','Palmeira das Missões\r'),(4902,'4313805','Palmitinho\r'),(4903,'4313904','Panambi\r'),(4904,'4313953','Pantano Grande\r'),(4905,'4314001','Paraí\r'),(4906,'4314027','Paraíso do Sul\r'),(4907,'4314035','Pareci Novo\r'),(4908,'4314050','Parobé\r'),(4909,'4314068','Passa Sete\r'),(4910,'4314076','Passo do Sobrado\r'),(4911,'4314100','Passo Fundo\r'),(4912,'4314134','Paulo Bento\r'),(4913,'4314159','Paverama\r'),(4914,'4314175','Pedras Altas\r'),(4915,'4314209','Pedro Osório\r'),(4916,'4314308','Pejuçara\r'),(4917,'4314407','Pelotas\r'),(4918,'4314423','Picada Café\r'),(4919,'4314456','Pinhal\r'),(4920,'4314464','Pinhal da Serra\r'),(4921,'4314472','Pinhal Grande\r'),(4922,'4314498','Pinheirinho do Vale\r'),(4923,'4314506','Pinheiro Machado\r'),(4924,'4314548','Pinto Bandeira\r'),(4925,'4314555','Pirapó\r'),(4926,'4314605','Piratini\r'),(4927,'4314704','Planalto\r'),(4928,'4314753','Poço das Antas\r'),(4929,'4314779','Pontão\r'),(4930,'4314787','Ponte Preta\r'),(4931,'4314803','Portão\r'),(4932,'4314902','Porto Alegre\r'),(4933,'4315008','Porto Lucena\r'),(4934,'4315057','Porto Mauá\r'),(4935,'4315073','Porto Vera Cruz\r'),(4936,'4315107','Porto Xavier\r'),(4937,'4315131','Pouso Novo\r'),(4938,'4315149','Presidente Lucena\r'),(4939,'4315156','Progresso\r'),(4940,'4315172','Protásio Alves\r'),(4941,'4315206','Putinga\r'),(4942,'4315305','Quaraí\r'),(4943,'4315313','Quatro Irmãos\r'),(4944,'4315321','Quevedos\r'),(4945,'4315354','Quinze de Novembro\r'),(4946,'4315404','Redentora\r'),(4947,'4315453','Relvado\r'),(4948,'4315503','Restinga Sêca\r'),(4949,'4315552','Rio dos Índios\r'),(4950,'4315602','Rio Grande\r'),(4951,'4315701','Rio Pardo\r'),(4952,'4315750','Riozinho\r'),(4953,'4315800','Roca Sales\r'),(4954,'4315909','Rodeio Bonito\r'),(4955,'4315958','Rolador\r'),(4956,'4316006','Rolante\r'),(4957,'4316105','Ronda Alta\r'),(4958,'4316204','Rondinha\r'),(4959,'4316303','Roque Gonzales\r'),(4960,'4316402','Rosário do Sul\r'),(4961,'4316428','Sagrada Família\r'),(4962,'4316436','Saldanha Marinho\r'),(4963,'4316451','Salto do Jacuí\r'),(4964,'4316477','Salvador das Missões\r'),(4965,'4316501','Salvador do Sul\r'),(4966,'4316600','Sananduva\r'),(4967,'4316709','Santa Bárbara do Sul\r'),(4968,'4316733','Santa Cecília do Sul\r'),(4969,'4316758','Santa Clara do Sul\r'),(4970,'4316808','Santa Cruz do Sul\r'),(4971,'4316972','Santa Margarida do Sul\r'),(4972,'4316907','Santa Maria\r'),(4973,'4316956','Santa Maria do Herval\r'),(4974,'4317202','Santa Rosa\r'),(4975,'4317251','Santa Tereza\r'),(4976,'4317301','Santa Vitória do Palmar\r'),(4977,'4317004','Santana da Boa Vista\r'),(4978,'4317103','SantAna do Livramento\r'),(4979,'4317400','Santiago\r'),(4980,'4317509','Santo Ângelo\r'),(4981,'4317608','Santo Antônio da Patrulha\r'),(4982,'4317707','Santo Antônio das Missões\r'),(4983,'4317558','Santo Antônio do Palma\r'),(4984,'4317756','Santo Antônio do Planalto\r'),(4985,'4317806','Santo Augusto\r'),(4986,'4317905','Santo Cristo\r'),(4987,'4317954','Santo Expedito do Sul\r'),(4988,'4318002','São Borja\r'),(4989,'4318051','São Domingos do Sul\r'),(4990,'4318101','São Francisco de Assis\r'),(4991,'4318200','São Francisco de Paula\r'),(4992,'4318309','São Gabriel\r'),(4993,'4318408','São Jerônimo\r'),(4994,'4318424','São João da Urtiga\r'),(4995,'4318432','São João do Polêsine\r'),(4996,'4318440','São Jorge\r'),(4997,'4318457','São José das Missões\r'),(4998,'4318465','São José do Herval\r'),(4999,'4318481','São José do Hortêncio\r'),(5000,'4318499','São José do Inhacorá\r'),(5001,'4318507','São José do Norte\r'),(5002,'4318606','São José do Ouro\r'),(5003,'4318614','São José do Sul\r'),(5004,'4318622','São José dos Ausentes\r'),(5005,'4318705','São Leopoldo\r'),(5006,'4318804','São Lourenço do Sul\r'),(5007,'4318903','São Luiz Gonzaga\r'),(5008,'4319000','São Marcos\r'),(5009,'4319109','São Martinho\r'),(5010,'4319125','São Martinho da Serra\r'),(5011,'4319158','São Miguel das Missões\r'),(5012,'4319208','São Nicolau\r'),(5013,'4319307','São Paulo das Missões\r'),(5014,'4319356','São Pedro da Serra\r'),(5015,'4319364','São Pedro das Missões\r'),(5016,'4319372','São Pedro do Butiá\r'),(5017,'4319406','São Pedro do Sul\r'),(5018,'4319505','São Sebastião do Caí\r'),(5019,'4319604','São Sepé\r'),(5020,'4319703','São Valentim\r'),(5021,'4319711','São Valentim do Sul\r'),(5022,'4319737','São Valério do Sul\r'),(5023,'4319752','São Vendelino\r'),(5024,'4319802','São Vicente do Sul\r'),(5025,'4319901','Sapiranga\r'),(5026,'4320008','Sapucaia do Sul\r'),(5027,'4320107','Sarandi\r'),(5028,'4320206','Seberi\r'),(5029,'4320230','Sede Nova\r'),(5030,'4320263','Segredo\r'),(5031,'4320305','Selbach\r'),(5032,'4320321','Senador Salgado Filho\r'),(5033,'4320354','Sentinela do Sul\r'),(5034,'4320404','Serafina Corrêa\r'),(5035,'4320453','Sério\r'),(5036,'4320503','Sertão\r'),(5037,'4320552','Sertão Santana\r'),(5038,'4320578','Sete de Setembro\r'),(5039,'4320602','Severiano de Almeida\r'),(5040,'4320651','Silveira Martins\r'),(5041,'4320677','Sinimbu\r'),(5042,'4320701','Sobradinho\r'),(5043,'4320800','Soledade\r'),(5044,'4320859','Tabaí\r'),(5045,'4320909','Tapejara\r'),(5046,'4321006','Tapera\r'),(5047,'4321105','Tapes\r'),(5048,'4321204','Taquara\r'),(5049,'4321303','Taquari\r'),(5050,'4321329','Taquaruçu do Sul\r'),(5051,'4321352','Tavares\r'),(5052,'4321402','Tenente Portela\r'),(5053,'4321436','Terra de Areia\r'),(5054,'4321451','Teutônia\r'),(5055,'4321469','Tio Hugo\r'),(5056,'4321477','Tiradentes do Sul\r'),(5057,'4321493','Toropi\r'),(5058,'4321501','Torres\r'),(5059,'4321600','Tramandaí\r'),(5060,'4321626','Travesseiro\r'),(5061,'4321634','Três Arroios\r'),(5062,'4321667','Três Cachoeiras\r'),(5063,'4321709','Três Coroas\r'),(5064,'4321808','Três de Maio\r'),(5065,'4321832','Três Forquilhas\r'),(5066,'4321857','Três Palmeiras\r'),(5067,'4321907','Três Passos\r'),(5068,'4321956','Trindade do Sul\r'),(5069,'4322004','Triunfo\r'),(5070,'4322103','Tucunduva\r'),(5071,'4322152','Tunas\r'),(5072,'4322186','Tupanci do Sul\r'),(5073,'4322202','Tupanciretã\r'),(5074,'4322251','Tupandi\r'),(5075,'4322301','Tuparendi\r'),(5076,'4322327','Turuçu\r'),(5077,'4322343','Ubiretama\r'),(5078,'4322350','União da Serra\r'),(5079,'4322376','Unistalda\r'),(5080,'4322400','Uruguaiana\r'),(5081,'4322509','Vacaria\r'),(5082,'4322533','Vale do Sol\r'),(5083,'4322541','Vale Real\r'),(5084,'4322525','Vale Verde\r'),(5085,'4322558','Vanini\r'),(5086,'4322608','Venâncio Aires\r'),(5087,'4322707','Vera Cruz\r'),(5088,'4322806','Veranópolis\r'),(5089,'4322855','Vespasiano Corrêa\r'),(5090,'4322905','Viadutos\r'),(5091,'4323002','Viamão\r'),(5092,'4323101','Vicente Dutra\r'),(5093,'4323200','Victor Graeff\r'),(5094,'4323309','Vila Flores\r'),(5095,'4323358','Vila Lângaro\r'),(5096,'4323408','Vila Maria\r'),(5097,'4323457','Vila Nova do Sul\r'),(5098,'4323507','Vista Alegre\r'),(5099,'4323606','Vista Alegre do Prata\r'),(5100,'4323705','Vista Gaúcha\r'),(5101,'4323754','Vitória das Missões\r'),(5102,'4323770','Westfália\r'),(5103,'4323804','Xangri-lá\r'),(5104,'5000203','Água Clara\r'),(5105,'5000252','Alcinópolis\r'),(5106,'5000609','Amambai\r'),(5107,'5000708','Anastácio\r'),(5108,'5000807','Anaurilândia\r'),(5109,'5000856','Angélica\r'),(5110,'5000906','Antônio João\r'),(5111,'5001003','Aparecida do Taboado\r'),(5112,'5001102','Aquidauana\r'),(5113,'5001243','Aral Moreira\r'),(5114,'5001508','Bandeirantes\r'),(5115,'5001904','Bataguassu\r'),(5116,'5002001','Batayporã\r'),(5117,'5002100','Bela Vista\r'),(5118,'5002159','Bodoquena\r'),(5119,'5002209','Bonito\r'),(5120,'5002308','Brasilândia\r'),(5121,'5002407','Caarapó\r'),(5122,'5002605','Camapuã\r'),(5123,'5002704','Campo Grande\r'),(5124,'5002803','Caracol\r'),(5125,'5002902','Cassilândia\r'),(5126,'5002951','Chapadão do Sul\r'),(5127,'5003108','Corguinho\r'),(5128,'5003157','Coronel Sapucaia\r'),(5129,'5003207','Corumbá\r'),(5130,'5003256','Costa Rica\r'),(5131,'5003306','Coxim\r'),(5132,'5003454','Deodápolis\r'),(5133,'5003488','Dois Irmãos do Buriti\r'),(5134,'5003504','Douradina\r'),(5135,'5003702','Dourados\r'),(5136,'5003751','Eldorado\r'),(5137,'5003801','Fátima do Sul\r'),(5138,'5003900','Figueirão\r'),(5139,'5004007','Glória de Dourados\r'),(5140,'5004106','Guia Lopes da Laguna\r'),(5141,'5004304','Iguatemi\r'),(5142,'5004403','Inocência\r'),(5143,'5004502','Itaporã\r'),(5144,'5004601','Itaquiraí\r'),(5145,'5004700','Ivinhema\r'),(5146,'5004809','Japorã\r'),(5147,'5004908','Jaraguari\r'),(5148,'5005004','Jardim\r'),(5149,'5005103','Jateí\r'),(5150,'5005152','Juti\r'),(5151,'5005202','Ladário\r'),(5152,'5005251','Laguna Carapã\r'),(5153,'5005400','Maracaju\r'),(5154,'5005608','Miranda\r'),(5155,'5005681','Mundo Novo\r'),(5156,'5005707','Naviraí\r'),(5157,'5005806','Nioaque\r'),(5158,'5006002','Nova Alvorada do Sul\r'),(5159,'5006200','Nova Andradina\r'),(5160,'5006259','Novo Horizonte do Sul\r'),(5161,'5006275','Paraíso das Águas\r'),(5162,'5006309','Paranaíba\r'),(5163,'5006358','Paranhos\r'),(5164,'5006408','Pedro Gomes\r'),(5165,'5006606','Ponta Porã\r'),(5166,'5006903','Porto Murtinho\r'),(5167,'5007109','Ribas do Rio Pardo\r'),(5168,'5007208','Rio Brilhante\r'),(5169,'5007307','Rio Negro\r'),(5170,'5007406','Rio Verde de Mato Grosso\r'),(5171,'5007505','Rochedo\r'),(5172,'5007554','Santa Rita do Pardo\r'),(5173,'5007695','São Gabriel do Oeste\r'),(5174,'5007802','Selvíria\r'),(5175,'5007703','Sete Quedas\r'),(5176,'5007901','Sidrolândia\r'),(5177,'5007935','Sonora\r'),(5178,'5007950','Tacuru\r'),(5179,'5007976','Taquarussu\r'),(5180,'5008008','Terenos\r'),(5181,'5008305','Três Lagoas\r'),(5182,'5008404','Vicentina\r'),(5183,'5100102','Acorizal\r'),(5184,'5100201','Água Boa\r'),(5185,'5100250','Alta Floresta\r'),(5186,'5100300','Alto Araguaia\r'),(5187,'5100359','Alto Boa Vista\r'),(5188,'5100409','Alto Garças\r'),(5189,'5100508','Alto Paraguai\r'),(5190,'5100607','Alto Taquari\r'),(5191,'5100805','Apiacás\r'),(5192,'5101001','Araguaiana\r'),(5193,'5101209','Araguainha\r'),(5194,'5101258','Araputanga\r'),(5195,'5101308','Arenápolis\r'),(5196,'5101407','Aripuanã\r'),(5197,'5101605','Barão de Melgaço\r'),(5198,'5101704','Barra do Bugres\r'),(5199,'5101803','Barra do Garças\r'),(5200,'5101852','Bom Jesus do Araguaia\r'),(5201,'5101902','Brasnorte\r'),(5202,'5102504','Cáceres\r'),(5203,'5102603','Campinápolis\r'),(5204,'5102637','Campo Novo do Parecis\r'),(5205,'5102678','Campo Verde\r'),(5206,'5102686','Campos de Júlio\r'),(5207,'5102694','Canabrava do Norte\r'),(5208,'5102702','Canarana\r'),(5209,'5102793','Carlinda\r'),(5210,'5102850','Castanheira\r'),(5211,'5103007','Chapada dos Guimarães\r'),(5212,'5103056','Cláudia\r'),(5213,'5103106','Cocalinho\r'),(5214,'5103205','Colíder\r'),(5215,'5103254','Colniza\r'),(5216,'5103304','Comodoro\r'),(5217,'5103353','Confresa\r'),(5218,'5103361','Conquista DOeste\r'),(5219,'5103379','Cotriguaçu\r'),(5220,'5103403','Cuiabá\r'),(5221,'5103437','Curvelândia\r'),(5222,'5103452','Denise\r'),(5223,'5103502','Diamantino\r'),(5224,'5103601','Dom Aquino\r'),(5225,'5103700','Feliz Natal\r'),(5226,'5103809','Figueirópolis DOeste\r'),(5227,'5103858','Gaúcha do Norte\r'),(5228,'5103908','General Carneiro\r'),(5229,'5103957','Glória DOeste\r'),(5230,'5104104','Guarantã do Norte\r'),(5231,'5104203','Guiratinga\r'),(5232,'5104500','Indiavaí\r'),(5233,'5104526','Ipiranga do Norte\r'),(5234,'5104542','Itanhangá\r'),(5235,'5104559','Itaúba\r'),(5236,'5104609','Itiquira\r'),(5237,'5104807','Jaciara\r'),(5238,'5104906','Jangada\r'),(5239,'5105002','Jauru\r'),(5240,'5105101','Juara\r'),(5241,'5105150','Juína\r'),(5242,'5105176','Juruena\r'),(5243,'5105200','Juscimeira\r'),(5244,'5105234','Lambari DOeste\r'),(5245,'5105259','Lucas do Rio Verde\r'),(5246,'5105309','Luciara\r'),(5247,'5105580','Marcelândia\r'),(5248,'5105606','Matupá\r'),(5249,'5105622','Mirassol dOeste\r'),(5250,'5105903','Nobres\r'),(5251,'5106000','Nortelândia\r'),(5252,'5106109','Nossa Senhora do Livramento\r'),(5253,'5106158','Nova Bandeirantes\r'),(5254,'5106208','Nova Brasilândia\r'),(5255,'5106216','Nova Canaã do Norte\r'),(5256,'5108808','Nova Guarita\r'),(5257,'5106182','Nova Lacerda\r'),(5258,'5108857','Nova Marilândia\r'),(5259,'5108907','Nova Maringá\r'),(5260,'5108956','Nova Monte Verde\r'),(5261,'5106224','Nova Mutum\r'),(5262,'5106174','Nova Nazaré\r'),(5263,'5106232','Nova Olímpia\r'),(5264,'5106190','Nova Santa Helena\r'),(5265,'5106240','Nova Ubiratã\r'),(5266,'5106257','Nova Xavantina\r'),(5267,'5106273','Novo Horizonte do Norte\r'),(5268,'5106265','Novo Mundo\r'),(5269,'5106315','Novo Santo Antônio\r'),(5270,'5106281','Novo São Joaquim\r'),(5271,'5106299','Paranaíta\r'),(5272,'5106307','Paranatinga\r'),(5273,'5106372','Pedra Preta\r'),(5274,'5106422','Peixoto de Azevedo\r'),(5275,'5106455','Planalto da Serra\r'),(5276,'5106505','Poconé\r'),(5277,'5106653','Pontal do Araguaia\r'),(5278,'5106703','Ponte Branca\r'),(5279,'5106752','Pontes e Lacerda\r'),(5280,'5106778','Porto Alegre do Norte\r'),(5281,'5106802','Porto dos Gaúchos\r'),(5282,'5106828','Porto Esperidião\r'),(5283,'5106851','Porto Estrela\r'),(5284,'5107008','Poxoréu\r'),(5285,'5107040','Primavera do Leste\r'),(5286,'5107065','Querência\r'),(5287,'5107156','Reserva do Cabaçal\r'),(5288,'5107180','Ribeirão Cascalheira\r'),(5289,'5107198','Ribeirãozinho\r'),(5290,'5107206','Rio Branco\r'),(5291,'5107578','Rondolândia\r'),(5292,'5107602','Rondonópolis\r'),(5293,'5107701','Rosário Oeste\r'),(5294,'5107750','Salto do Céu\r'),(5295,'5107248','Santa Carmem\r'),(5296,'5107743','Santa Cruz do Xingu\r'),(5297,'5107768','Santa Rita do Trivelato\r'),(5298,'5107776','Santa Terezinha\r'),(5299,'5107263','Santo Afonso\r'),(5300,'5107792','Santo Antônio do Leste\r'),(5301,'5107800','Santo Antônio do Leverger\r'),(5302,'5107859','São Félix do Araguaia\r'),(5303,'5107297','São José do Povo\r'),(5304,'5107305','São José do Rio Claro\r'),(5305,'5107354','São José do Xingu\r'),(5306,'5107107','São José dos Quatro Marcos\r'),(5307,'5107404','São Pedro da Cipa\r'),(5308,'5107875','Sapezal\r'),(5309,'5107883','Serra Nova Dourada\r'),(5310,'5107909','Sinop\r'),(5311,'5107925','Sorriso\r'),(5312,'5107941','Tabaporã\r'),(5313,'5107958','Tangará da Serra\r'),(5314,'5108006','Tapurah\r'),(5315,'5108055','Terra Nova do Norte\r'),(5316,'5108105','Tesouro\r'),(5317,'5108204','Torixoréu\r'),(5318,'5108303','União do Sul\r'),(5319,'5108352','Vale de São Domingos\r'),(5320,'5108402','Várzea Grande\r'),(5321,'5108501','Vera\r'),(5322,'5105507','Vila Bela da Santíssima Trindade\r'),(5323,'5108600','Vila Rica\r'),(5324,'5200050','Abadia de Goiás\r'),(5325,'5200100','Abadiânia\r'),(5326,'5200134','Acreúna\r'),(5327,'5200159','Adelândia\r'),(5328,'5200175','Água Fria de Goiás\r'),(5329,'5200209','Água Limpa\r'),(5330,'5200258','Águas Lindas de Goiás\r'),(5331,'5200308','Alexânia\r'),(5332,'5200506','Aloândia\r'),(5333,'5200555','Alto Horizonte\r'),(5334,'5200605','Alto Paraíso de Goiás\r'),(5335,'5200803','Alvorada do Norte\r'),(5336,'5200829','Amaralina\r'),(5337,'5200852','Americano do Brasil\r'),(5338,'5200902','Amorinópolis\r'),(5339,'5201108','Anápolis\r'),(5340,'5201207','Anhanguera\r'),(5341,'5201306','Anicuns\r'),(5342,'5201405','Aparecida de Goiânia\r'),(5343,'5201454','Aparecida do Rio Doce\r'),(5344,'5201504','Aporé\r'),(5345,'5201603','Araçu\r'),(5346,'5201702','Aragarças\r'),(5347,'5201801','Aragoiânia\r'),(5348,'5202155','Araguapaz\r'),(5349,'5202353','Arenópolis\r'),(5350,'5202502','Aruanã\r'),(5351,'5202601','Aurilândia\r'),(5352,'5202809','Avelinópolis\r'),(5353,'5203104','Baliza\r'),(5354,'5203203','Barro Alto\r'),(5355,'5203302','Bela Vista de Goiás\r'),(5356,'5203401','Bom Jardim de Goiás\r'),(5357,'5203500','Bom Jesus de Goiás\r'),(5358,'5203559','Bonfinópolis\r'),(5359,'5203575','Bonópolis\r'),(5360,'5203609','Brazabrantes\r'),(5361,'5203807','Britânia\r'),(5362,'5203906','Buriti Alegre\r'),(5363,'5203939','Buriti de Goiás\r'),(5364,'5203962','Buritinópolis\r'),(5365,'5204003','Cabeceiras\r'),(5366,'5204102','Cachoeira Alta\r'),(5367,'5204201','Cachoeira de Goiás\r'),(5368,'5204250','Cachoeira Dourada\r'),(5369,'5204300','Caçu\r'),(5370,'5204409','Caiapônia\r'),(5371,'5204508','Caldas Novas\r'),(5372,'5204557','Caldazinha\r'),(5373,'5204607','Campestre de Goiás\r'),(5374,'5204656','Campinaçu\r'),(5375,'5204706','Campinorte\r'),(5376,'5204805','Campo Alegre de Goiás\r'),(5377,'5204854','Campo Limpo de Goiás\r'),(5378,'5204904','Campos Belos\r'),(5379,'5204953','Campos Verdes\r'),(5380,'5205000','Carmo do Rio Verde\r'),(5381,'5205059','Castelândia\r'),(5382,'5205109','Catalão\r'),(5383,'5205208','Caturaí\r'),(5384,'5205307','Cavalcante\r'),(5385,'5205406','Ceres\r'),(5386,'5205455','Cezarina\r'),(5387,'5205471','Chapadão do Céu\r'),(5388,'5205497','Cidade Ocidental\r'),(5389,'5205513','Cocalzinho de Goiás\r'),(5390,'5205521','Colinas do Sul\r'),(5391,'5205703','Córrego do Ouro\r'),(5392,'5205802','Corumbá de Goiás\r'),(5393,'5205901','Corumbaíba\r'),(5394,'5206206','Cristalina\r'),(5395,'5206305','Cristianópolis\r'),(5396,'5206404','Crixás\r'),(5397,'5206503','Cromínia\r'),(5398,'5206602','Cumari\r'),(5399,'5206701','Damianópolis\r'),(5400,'5206800','Damolândia\r'),(5401,'5206909','Davinópolis\r'),(5402,'5207105','Diorama\r'),(5403,'5208301','Divinópolis de Goiás\r'),(5404,'5207253','Doverlândia\r'),(5405,'5207352','Edealina\r'),(5406,'5207402','Edéia\r'),(5407,'5207501','Estrela do Norte\r'),(5408,'5207535','Faina\r'),(5409,'5207600','Fazenda Nova\r'),(5410,'5207808','Firminópolis\r'),(5411,'5207907','Flores de Goiás\r'),(5412,'5208004','Formosa\r'),(5413,'5208103','Formoso\r'),(5414,'5208152','Gameleira de Goiás\r'),(5415,'5208400','Goianápolis\r'),(5416,'5208509','Goiandira\r'),(5417,'5208608','Goianésia\r'),(5418,'5208707','Goiânia\r'),(5419,'5208806','Goianira\r'),(5420,'5208905','Goiás\r'),(5421,'5209101','Goiatuba\r'),(5422,'5209150','Gouvelândia\r'),(5423,'5209200','Guapó\r'),(5424,'5209291','Guaraíta\r'),(5425,'5209408','Guarani de Goiás\r'),(5426,'5209457','Guarinos\r'),(5427,'5209606','Heitoraí\r'),(5428,'5209705','Hidrolândia\r'),(5429,'5209804','Hidrolina\r'),(5430,'5209903','Iaciara\r'),(5431,'5209937','Inaciolândia\r'),(5432,'5209952','Indiara\r'),(5433,'5210000','Inhumas\r'),(5434,'5210109','Ipameri\r'),(5435,'5210158','Ipiranga de Goiás\r'),(5436,'5210208','Iporá\r'),(5437,'5210307','Israelândia\r'),(5438,'5210406','Itaberaí\r'),(5439,'5210562','Itaguari\r'),(5440,'5210604','Itaguaru\r'),(5441,'5210802','Itajá\r'),(5442,'5210901','Itapaci\r'),(5443,'5211008','Itapirapuã\r'),(5444,'5211206','Itapuranga\r'),(5445,'5211305','Itarumã\r'),(5446,'5211404','Itauçu\r'),(5447,'5211503','Itumbiara\r'),(5448,'5211602','Ivolândia\r'),(5449,'5211701','Jandaia\r'),(5450,'5211800','Jaraguá\r'),(5451,'5211909','Jataí\r'),(5452,'5212006','Jaupaci\r'),(5453,'5212055','Jesúpolis\r'),(5454,'5212105','Joviânia\r'),(5455,'5212204','Jussara\r'),(5456,'5212253','Lagoa Santa\r'),(5457,'5212303','Leopoldo de Bulhões\r'),(5458,'5212501','Luziânia\r'),(5459,'5212600','Mairipotaba\r'),(5460,'5212709','Mambaí\r'),(5461,'5212808','Mara Rosa\r'),(5462,'5212907','Marzagão\r'),(5463,'5212956','Matrinchã\r'),(5464,'5213004','Maurilândia\r'),(5465,'5213053','Mimoso de Goiás\r'),(5466,'5213087','Minaçu\r'),(5467,'5213103','Mineiros\r'),(5468,'5213400','Moiporá\r'),(5469,'5213509','Monte Alegre de Goiás\r'),(5470,'5213707','Montes Claros de Goiás\r'),(5471,'5213756','Montividiu\r'),(5472,'5213772','Montividiu do Norte\r'),(5473,'5213806','Morrinhos\r'),(5474,'5213855','Morro Agudo de Goiás\r'),(5475,'5213905','Mossâmedes\r'),(5476,'5214002','Mozarlândia\r'),(5477,'5214051','Mundo Novo\r'),(5478,'5214101','Mutunópolis\r'),(5479,'5214408','Nazário\r'),(5480,'5214507','Nerópolis\r'),(5481,'5214606','Niquelândia\r'),(5482,'5214705','Nova América\r'),(5483,'5214804','Nova Aurora\r'),(5484,'5214838','Nova Crixás\r'),(5485,'5214861','Nova Glória\r'),(5486,'5214879','Nova Iguaçu de Goiás\r'),(5487,'5214903','Nova Roma\r'),(5488,'5215009','Nova Veneza\r'),(5489,'5215207','Novo Brasil\r'),(5490,'5215231','Novo Gama\r'),(5491,'5215256','Novo Planalto\r'),(5492,'5215306','Orizona\r'),(5493,'5215405','Ouro Verde de Goiás\r'),(5494,'5215504','Ouvidor\r'),(5495,'5215603','Padre Bernardo\r'),(5496,'5215652','Palestina de Goiás\r'),(5497,'5215702','Palmeiras de Goiás\r'),(5498,'5215801','Palmelo\r'),(5499,'5215900','Palminópolis\r'),(5500,'5216007','Panamá\r'),(5501,'5216304','Paranaiguara\r'),(5502,'5216403','Paraúna\r'),(5503,'5216452','Perolândia\r'),(5504,'5216809','Petrolina de Goiás\r'),(5505,'5216908','Pilar de Goiás\r'),(5506,'5217104','Piracanjuba\r'),(5507,'5217203','Piranhas\r'),(5508,'5217302','Pirenópolis\r'),(5509,'5217401','Pires do Rio\r'),(5510,'5217609','Planaltina\r'),(5511,'5217708','Pontalina\r'),(5512,'5218003','Porangatu\r'),(5513,'5218052','Porteirão\r'),(5514,'5218102','Portelândia\r'),(5515,'5218300','Posse\r'),(5516,'5218391','Professor Jamil\r'),(5517,'5218508','Quirinópolis\r'),(5518,'5218607','Rialma\r'),(5519,'5218706','Rianápolis\r'),(5520,'5218789','Rio Quente\r'),(5521,'5218805','Rio Verde\r'),(5522,'5218904','Rubiataba\r'),(5523,'5219001','Sanclerlândia\r'),(5524,'5219100','Santa Bárbara de Goiás\r'),(5525,'5219209','Santa Cruz de Goiás\r'),(5526,'5219258','Santa Fé de Goiás\r'),(5527,'5219308','Santa Helena de Goiás\r'),(5528,'5219357','Santa Isabel\r'),(5529,'5219407','Santa Rita do Araguaia\r'),(5530,'5219456','Santa Rita do Novo Destino\r'),(5531,'5219506','Santa Rosa de Goiás\r'),(5532,'5219605','Santa Tereza de Goiás\r'),(5533,'5219704','Santa Terezinha de Goiás\r'),(5534,'5219712','Santo Antônio da Barra\r'),(5535,'5219738','Santo Antônio de Goiás\r'),(5536,'5219753','Santo Antônio do Descoberto\r'),(5537,'5219803','São Domingos\r'),(5538,'5219902','São Francisco de Goiás\r'),(5539,'5220058','São João da Paraúna\r'),(5540,'5220009','São João dAliança\r'),(5541,'5220108','São Luís de Montes Belos\r'),(5542,'5220157','São Luiz do Norte\r'),(5543,'5220207','São Miguel do Araguaia\r'),(5544,'5220264','São Miguel do Passa Quatro\r'),(5545,'5220280','São Patrício\r'),(5546,'5220405','São Simão\r'),(5547,'5220454','Senador Canedo\r'),(5548,'5220504','Serranópolis\r'),(5549,'5220603','Silvânia\r'),(5550,'5220686','Simolândia\r'),(5551,'5220702','Sítio dAbadia\r'),(5552,'5221007','Taquaral de Goiás\r'),(5553,'5221080','Teresina de Goiás\r'),(5554,'5221197','Terezópolis de Goiás\r'),(5555,'5221304','Três Ranchos\r'),(5556,'5221403','Trindade\r'),(5557,'5221452','Trombas\r'),(5558,'5221502','Turvânia\r'),(5559,'5221551','Turvelândia\r'),(5560,'5221577','Uirapuru\r'),(5561,'5221601','Uruaçu\r'),(5562,'5221700','Uruana\r'),(5563,'5221809','Urutaí\r'),(5564,'5221858','Valparaíso de Goiás\r'),(5565,'5221908','Varjão\r'),(5566,'5222005','Vianópolis\r'),(5567,'5222054','Vicentinópolis\r'),(5568,'5222203','Vila Boa\r'),(5569,'5222302','Vila Propício\r'),(5570,'5300108','Brasília');
/*!40000 ALTER TABLE `tabela_municipios_ibge` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `tabela_municipios_ibge` with 5570 row(s)
--

--
-- Table structure for table `tabela_ufs_ibge`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabela_ufs_ibge` (
  `id_tabela` int(11) NOT NULL AUTO_INCREMENT,
  `unidade_da_federacao` varchar(128) DEFAULT NULL,
  `uf` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_tabela`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabela_ufs_ibge`
--

LOCK TABLES `tabela_ufs_ibge` WRITE;
/*!40000 ALTER TABLE `tabela_ufs_ibge` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `tabela_ufs_ibge` VALUES (1,'﻿Rondônia','RO\r'),(2,'Acre','AC\r'),(3,'Amazonas','AM\r'),(4,'Roraima','RR\r'),(5,'Pará','PA\r'),(6,'Amapá','AP\r'),(7,'Tocantins','TO\r'),(8,'Maranhão','MA\r'),(9,'Piauí','PI\r'),(10,'Ceará','CE\r'),(11,'Rio Grande do Norte','RN\r'),(12,'Paraíba','PB\r'),(13,'Pernambuco','PE\r'),(14,'Alagoas','AL\r'),(15,'Sergipe','SE\r'),(16,'Bahia','BA\r'),(17,'Minas Gerais','MG\r'),(18,'Espírito Santo','ES\r'),(19,'Rio de Janeiro','RJ\r'),(20,'São Paulo','SP\r'),(21,'Paraná','PR\r'),(22,'Santa Catarina','SC\r'),(23,'Rio Grande do Sul','RS\r'),(24,'Mato Grosso do Sul','MS\r'),(25,'Mato Grosso','MT\r'),(26,'Goiás','GO\r'),(27,'Distrito Federal','DF');
/*!40000 ALTER TABLE `tabela_ufs_ibge` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `tabela_ufs_ibge` with 27 row(s)
--

--
-- Table structure for table `tecnicos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tecnicos` (
  `id_tecnico` int(9) NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) NOT NULL,
  `cpf` varchar(128) NOT NULL,
  `rg` varchar(128) NOT NULL,
  `data_de_nascimento` varchar(128) NOT NULL,
  `sexo` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `comissao` double NOT NULL,
  `observacoes` varchar(128) NOT NULL,
  `foto` varchar(128) NOT NULL,
  `fixo` varchar(128) NOT NULL,
  `celular_1` varchar(128) NOT NULL,
  `celular_2` varchar(128) NOT NULL,
  `cep` varchar(128) NOT NULL,
  `logradouro` varchar(128) NOT NULL,
  `numero` varchar(128) NOT NULL,
  `complemento` varchar(128) NOT NULL,
  `bairro` varchar(128) NOT NULL,
  `cidade` varchar(128) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_tecnico`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tecnicos`
--

LOCK TABLES `tecnicos` WRITE;
/*!40000 ALTER TABLE `tecnicos` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `tecnicos` VALUES (1,'GERAL','S/N','','','','',0,'','','','S/N','','','','','','','','','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tecnicos` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `tecnicos` with 1 row(s)
--

--
-- Table structure for table `vendas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendas` (
  `id_venda` int(9) NOT NULL AUTO_INCREMENT,
  `valor_a_pagar` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_recebido` double NOT NULL,
  `troco` double NOT NULL,
  `forma_de_pagamento` varchar(64) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_venda`),
  KEY `vendas_id_cliente_foreign` (`id_cliente`),
  KEY `vendas_id_vendedor_foreign` (`id_vendedor`),
  KEY `vendas_id_caixa_foreign` (`id_caixa`),
  CONSTRAINT `vendas_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendas_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vendas_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendas`
--

LOCK TABLES `vendas` WRITE;
/*!40000 ALTER TABLE `vendas` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `vendas` VALUES (1,10000,0,11000,1000,'Dinheiro','2026-04-29','14:31:25',1,1,1,'2026-04-29 14:31:25','2026-04-29 14:31:25','0000-00-00 00:00:00'),(2,10000,0,10000,0,'Dinheiro','2026-04-29','23:41:41',1,1,1,'2026-04-29 23:41:41','2026-04-29 23:41:41','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `vendas` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `vendas` with 2 row(s)
--

--
-- Table structure for table `venda_rapida`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venda_rapida` (
  `id_venda` int(9) NOT NULL AUTO_INCREMENT,
  `valor_a_pagar` double NOT NULL,
  `desconto` double NOT NULL,
  `valor_recebido` double NOT NULL,
  `troco` double NOT NULL,
  `forma_de_pagamento` varchar(64) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_caixa` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_venda`),
  KEY `venda_rapida_id_cliente_foreign` (`id_cliente`),
  KEY `venda_rapida_id_caixa_foreign` (`id_caixa`),
  KEY `venda_rapida_id_vendedor_foreign` (`id_vendedor`),
  CONSTRAINT `venda_rapida_id_caixa_foreign` FOREIGN KEY (`id_caixa`) REFERENCES `caixas` (`id_caixa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `venda_rapida_id_cliente_foreign` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `venda_rapida_id_vendedor_foreign` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`id_vendedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venda_rapida`
--

LOCK TABLES `venda_rapida` WRITE;
/*!40000 ALTER TABLE `venda_rapida` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `venda_rapida` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `venda_rapida` with 0 row(s)
--

--
-- Table structure for table `vendedores`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendedores` (
  `id_vendedor` int(9) NOT NULL AUTO_INCREMENT,
  `status` varchar(32) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `data_inicio_das_atividades` date NOT NULL,
  `anotacoes` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`id_vendedor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendedores`
--

LOCK TABLES `vendedores` WRITE;
/*!40000 ALTER TABLE `vendedores` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `vendedores` VALUES (1,'Ativo','GERAL','2020-05-12','Vendedor para vendas em geral.','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `vendedores` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `vendedores` with 1 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Mon, 08 Jun 2026 23:53:11 -0400
