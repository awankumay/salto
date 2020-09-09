-- MySQL dump 10.13  Distrib 8.0.16, for osx10.14 (x86_64)
--
-- Host: localhost    Database: rutandepok
-- ------------------------------------------------------
-- Server version	8.0.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_users` bigint(20) NOT NULL,
  `visitor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) DEFAULT NULL,
  `schedule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `no_antrian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auction_histories`
--

DROP TABLE IF EXISTS `auction_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `auction_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction_histories`
--

LOCK TABLES `auction_histories` WRITE;
/*!40000 ALTER TABLE `auction_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `auction_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auction_logs`
--

DROP TABLE IF EXISTS `auction_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `auction_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction_logs`
--

LOCK TABLES `auction_logs` WRITE;
/*!40000 ALTER TABLE `auction_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `auction_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auctions`
--

DROP TABLE IF EXISTS `auctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `auctions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_categories_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_categories_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tags` longtext COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` smallint(6) NOT NULL DEFAULT '2',
  `status` smallint(6) NOT NULL DEFAULT '2',
  `user_id` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_published` timestamp NULL DEFAULT NULL,
  `date_started` timestamp NULL DEFAULT NULL,
  `date_ended` timestamp NULL DEFAULT NULL,
  `buy_now` int(11) DEFAULT NULL,
  `price_buy_now` bigint(20) DEFAULT NULL,
  `start_price` bigint(20) DEFAULT NULL,
  `multiple_bid` bigint(20) DEFAULT NULL,
  `rate_donation` int(11) DEFAULT NULL,
  `beneficiary_account` bigint(20) NOT NULL,
  `beneficiary_account_issuer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auctions`
--

LOCK TABLES `auctions` WRITE;
/*!40000 ALTER TABLE `auctions` DISABLE KEYS */;
/*!40000 ALTER TABLE `auctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_histories`
--

DROP TABLE IF EXISTS `campaign_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `campaign_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_histories`
--

LOCK TABLES `campaign_histories` WRITE;
/*!40000 ALTER TABLE `campaign_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tags` longtext COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` smallint(6) NOT NULL DEFAULT '2',
  `status` smallint(6) NOT NULL DEFAULT '2',
  `user_id` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_published` timestamp NULL DEFAULT NULL,
  `date_started` timestamp NULL DEFAULT NULL,
  `date_ended` timestamp NULL DEFAULT NULL,
  `fund_target` bigint(20) DEFAULT NULL,
  `beneficiary_account` bigint(20) NOT NULL,
  `beneficiary_account_issuer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `set_fund_target` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaigns`
--

LOCK TABLES `campaigns` WRITE;
/*!40000 ALTER TABLE `campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `convicts`
--

DROP TABLE IF EXISTS `convicts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `convicts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` bigint(20) DEFAULT NULL,
  `identity` bigint(20) DEFAULT NULL,
  `identity_tipe` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_convict` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `violation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clause` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `block` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lockup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_created` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `convicts`
--

LOCK TABLES `convicts` WRITE;
/*!40000 ALTER TABLE `convicts` DISABLE KEYS */;
INSERT INTO `convicts` VALUES (1,NULL,NULL,NULL,'tst','2','b16cb8b9cff25c697b7bfebce027980e.jpg',NULL,'Kartu kuning',NULL,NULL,NULL,NULL,NULL,'B1 BLOK BARAK KIRI',1,'2020-09-08 18:44:28','2020-09-08 18:44:28');
/*!40000 ALTER TABLE `convicts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (170,'2014_10_12_000000_create_users_table',1),(171,'2014_10_12_100000_create_password_resets_table',1),(172,'2016_06_01_000001_create_oauth_auth_codes_table',1),(173,'2016_06_01_000002_create_oauth_access_tokens_table',1),(174,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),(175,'2016_06_01_000004_create_oauth_clients_table',1),(176,'2016_06_01_000005_create_oauth_personal_access_clients_table',1),(177,'2019_08_19_000000_create_failed_jobs_table',1),(178,'2020_04_19_200146_create_permission_tables',1),(179,'2020_04_25_072718_create_post_categories_table',1),(180,'2020_06_04_043108_add_detail_to_users_table',1),(181,'2020_06_14_165352_create_post_table',1),(182,'2020_06_21_021959_create_tags_table',1),(183,'2020_07_14_062126_create_campaigns_table',1),(184,'2020_07_14_062209_create_auctions_table',1),(185,'2020_07_14_062747_create_campaign_histories_table',1),(186,'2020_07_14_063818_create_auction_histories_table',1),(187,'2020_07_14_065602_create_auction_logs_table',1),(188,'2020_07_14_071343_create_transactions_table',1),(189,'2020_07_14_072925_create_transaction_auction_detail_table',1),(190,'2020_07_14_073018_create_transaction_campaign_detail_table',1),(191,'2020_07_16_212056_create_withdraw_transactions_table',1),(192,'2020_07_18_141949_add_fund_target_status',1),(193,'2020_07_25_195856_create_product_categories_table',1),(194,'2020_08_03_174617_add_image_to_product_categories_table',1),(195,'2020_09_06_051602_create_appointments_table',1),(196,'2020_09_07_231407_create_convict_table',1),(197,'2020_09_08_031938_create_user_has_convict',1),(198,'2020_09_08_213915_create_products_table',1),(199,'2020_09_08_214256_create_ratings_table',1),(200,'2020_09_08_214337_create_transhistory_table',1),(201,'2020_09_08_214357_create_pengaduan_table',1),(202,'2020_09_08_215545_add_categories_to_products_table',1),(203,'2020_09_08_225916_add_identity_to_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\User',1),(2,'App\\User',2),(4,'App\\User',3);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
INSERT INTO `oauth_access_tokens` VALUES ('d81d0ab07693d8e58738e25debf2d7039a61a0fde4ba696ffac2e3f15447836bba0ade6f4a9e3d28',3,3,'MyApp','[]',0,'2020-09-08 21:29:05','2020-09-08 21:29:05','2021-09-09 04:29:05');
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_auth_codes`
--

LOCK TABLES `oauth_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
INSERT INTO `oauth_clients` VALUES (1,NULL,'rutandepok Personal Access Client','iR45Cm0Eh2L0Ng8c80aiY7UJJLzqLXesF3kUnEP1',NULL,'http://localhost',1,0,0,'2020-09-08 21:07:51','2020-09-08 21:07:51'),(2,NULL,'rutandepok Password Grant Client','bEvXpmMYXcNAvioLTwlLU9ak01gNdWGufsNeOnGK','users','http://localhost',0,1,0,'2020-09-08 21:07:51','2020-09-08 21:07:51'),(3,NULL,'rutandepok Personal Access Client','uJkAWW3cPiaeFDKwcNgWbsVxXwQV4xt59VpxCADl',NULL,'http://localhost',1,0,0,'2020-09-08 21:14:23','2020-09-08 21:14:23');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_personal_access_clients`
--

LOCK TABLES `oauth_personal_access_clients` WRITE;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
INSERT INTO `oauth_personal_access_clients` VALUES (1,1,'2020-09-08 21:07:51','2020-09-08 21:07:51'),(2,3,'2020-09-08 21:14:23','2020-09-08 21:14:23');
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaduan`
--

DROP TABLE IF EXISTS `pengaduan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `pengaduan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaduan`
--

LOCK TABLES `pengaduan` WRITE;
/*!40000 ALTER TABLE `pengaduan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengaduan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'convict-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(2,'convict-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(3,'convict-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(4,'convict-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(5,'product-category-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(6,'product-category-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(7,'product-category-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(8,'product-category-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(9,'product-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(10,'product-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(11,'product-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(12,'product-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(13,'post-category-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(14,'post-category-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(15,'post-category-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(16,'post-category-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(17,'post-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(18,'post-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(19,'post-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(20,'post-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(21,'role-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(22,'role-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(23,'role-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(24,'role-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(25,'rating-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(26,'rating-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(27,'rating-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(28,'rating-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(29,'send-report-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(30,'send-report-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(31,'send-report-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(32,'send-report-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(33,'transaction-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(34,'transaction-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(35,'transaction-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(36,'transaction-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(37,'user-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(38,'user-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(39,'user-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(40,'user-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(41,'visitor-list','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(42,'visitor-create','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(43,'visitor-edit','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(44,'visitor-delete','web','2020-09-08 17:44:25','2020-09-08 17:44:25');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `post_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `author` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_categories`
--

LOCK TABLES `post_categories` WRITE;
/*!40000 ALTER TABLE `post_categories` DISABLE KEYS */;
INSERT INTO `post_categories` VALUES (1,'remisi',NULL,1,'superadmin',NULL,'2020-09-08 20:35:01','2020-09-08 20:35:01'),(2,'test','test',1,'superadmin',NULL,'2020-09-08 20:35:24','2020-09-08 20:35:24');
/*!40000 ALTER TABLE `post_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_categories_id` int(11) NOT NULL,
  `tags` longtext COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `author` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_published` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'a','a','a','b','b',1,'','<p>c</p>\n','314b2b2e4bb2031201b17bba661a6752.jpg',0,1,1,'superadmin',NULL,NULL,'2020-09-08 20:36:18','2020-09-08 20:36:18'),(2,'aaa','aaa','aa','aaa','bbbb',2,'','<p>ccccc</p>\n','88f85ea673e834c4035e340d82a6da79.jpg',0,1,1,'superadmin',NULL,NULL,'2020-09-08 20:41:18','2020-09-08 20:45:30');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `product_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `author` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'Mie Instant',NULL,1,'superadmin',NULL,'2020-09-08 18:25:15','2020-09-08 18:25:15',NULL,NULL);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `price` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_categories` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(5,2),(13,2),(5,3),(13,3),(1,4);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','web','2020-09-08 17:44:25','2020-09-08 17:44:25'),(2,'Admin','web','2020-09-08 17:44:26','2020-09-08 17:44:26'),(3,'User','web','2020-09-08 17:44:26','2020-09-08 17:44:26'),(4,'Pengunjung','web','2020-09-08 21:20:55','2020-09-08 21:20:55');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_updated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_auction_detail`
--

DROP TABLE IF EXISTS `transaction_auction_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `transaction_auction_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) NOT NULL,
  `auction_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auction_date_created` timestamp NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_price` bigint(20) NOT NULL,
  `price_higher` bigint(20) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_categories_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_categories_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auction_log_id` bigint(20) NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` bigint(20) NOT NULL,
  `customer_whatsapp` bigint(20) DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_zip_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL,
  `latest_updated` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_auction_detail`
--

LOCK TABLES `transaction_auction_detail` WRITE;
/*!40000 ALTER TABLE `transaction_auction_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_auction_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_campaign_detail`
--

DROP TABLE IF EXISTS `transaction_campaign_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `transaction_campaign_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) NOT NULL,
  `campaign_id` bigint(20) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` bigint(20) DEFAULT NULL,
  `customer_whatsapp` bigint(20) DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` timestamp NOT NULL,
  `latest_updated` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_campaign_detail`
--

LOCK TABLES `transaction_campaign_detail` WRITE;
/*!40000 ALTER TABLE `transaction_campaign_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_campaign_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_type` int(11) NOT NULL COMMENT '1:campaign, 2:auction, 3:campaign barang',
  `unique_id` bigint(20) NOT NULL,
  `reff_id` bigint(20) DEFAULT NULL,
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `unique_amount` bigint(20) NOT NULL DEFAULT '0',
  `total_amount` bigint(20) NOT NULL DEFAULT '0',
  `sender_account` bigint(20) DEFAULT NULL,
  `sender_account_issuer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  `date_created` timestamp NOT NULL,
  `date_exp` timestamp NULL DEFAULT NULL,
  `date_paid` timestamp NULL DEFAULT NULL,
  `date_confirm` timestamp NULL DEFAULT NULL,
  `date_reversal` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transhistory`
--

DROP TABLE IF EXISTS `transhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `transhistory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_trans` bigint(20) NOT NULL,
  `users_id` int(11) NOT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_product` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` bigint(20) NOT NULL,
  `date_payment` date NOT NULL,
  `status` int(11) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transhistory`
--

LOCK TABLES `transhistory` WRITE;
/*!40000 ALTER TABLE `transhistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `transhistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_convicts`
--

DROP TABLE IF EXISTS `user_has_convicts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_has_convicts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `convicts_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_convicts`
--

LOCK TABLES `user_has_convicts` WRITE;
/*!40000 ALTER TABLE `user_has_convicts` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_has_convicts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` bigint(20) NOT NULL,
  `whatsapp` bigint(20) DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `tagline` longtext COLLATE utf8mb4_unicode_ci,
  `sex` smallint(6) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `identity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'superadmin','super@nope.id',NULL,'$2y$10$0pD0D9FRxxgODerQURY0T.vSMS/F.wVWxerte1u2e/uKwm0MDvXl.',NULL,'2020-09-08 17:44:26','2020-09-08 17:44:26',6285716319806,6285716319806,NULL,NULL,NULL,2,1,NULL,NULL,NULL,NULL),(2,'fajar','fajar@mailinator.com',NULL,'$2y$10$dsMKkKc0NskITarWtQZGVe0aJMBg0H2P6prhN5WxAccDSz56EQ2Ka',NULL,'2020-09-08 21:19:51','2020-09-08 21:26:14',8989823,8989823,'jkt',NULL,NULL,1,1,'93d604167287e1b294e7a7254ae17922.jpg',NULL,'2020-09-08 21:26:14','023928'),(3,'fajar','fajar1@mailinator.com',NULL,'$2y$10$H.yYFMPmuC8iYjVG.BJC3uxmjluJdjz7e5oVtG5cQmFI0CDzaBfxy',NULL,'2020-09-08 21:28:53','2020-09-08 21:28:53',217520034,8577136123,'Jakarta',NULL,NULL,1,1,NULL,NULL,NULL,'98092389');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdraw_transactions`
--

DROP TABLE IF EXISTS `withdraw_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `withdraw_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_transactions_type` bigint(20) NOT NULL COMMENT '1:campaign, 2:auction',
  `user_id` bigint(20) NOT NULL,
  `beneficiary_account` bigint(20) NOT NULL,
  `beneficiary_account_issuer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auction_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_created` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unique_id` bigint(20) NOT NULL,
  `reff_id` bigint(20) DEFAULT NULL,
  `amount` bigint(20) NOT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  `sender_account` bigint(20) NOT NULL,
  `sender_account_issuer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL,
  `date_send` timestamp NULL DEFAULT NULL,
  `date_confirm` timestamp NULL DEFAULT NULL,
  `date_reversal` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw_transactions`
--

LOCK TABLES `withdraw_transactions` WRITE;
/*!40000 ALTER TABLE `withdraw_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraw_transactions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-09  5:13:32
