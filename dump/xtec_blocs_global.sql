-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: xtec_blocs_global
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `wp_blog_versions`
--

DROP TABLE IF EXISTS `wp_blog_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_blog_versions` (
  `blog_id` bigint NOT NULL DEFAULT '0',
  `db_version` varchar(20) NOT NULL DEFAULT '',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`blog_id`),
  KEY `db_version` (`db_version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_blog_versions`
--

LOCK TABLES `wp_blog_versions` WRITE;
/*!40000 ALTER TABLE `wp_blog_versions` DISABLE KEYS */;
INSERT INTO `wp_blog_versions` VALUES (1,'36686','2014-01-10 14:15:36'),(6,'29630','2014-01-10 14:26:31'),(5,'36686','2014-01-10 14:26:33'),(4,'36686','2014-01-10 14:26:34'),(3,'36686','2014-01-10 14:26:35');
/*!40000 ALTER TABLE `wp_blog_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_blogmeta`
--

DROP TABLE IF EXISTS `wp_blogmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_blogmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `meta_key` (`meta_key`(191)),
  KEY `blog_id` (`blog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_blogmeta`
--

LOCK TABLES `wp_blogmeta` WRITE;
/*!40000 ALTER TABLE `wp_blogmeta` DISABLE KEYS */;
INSERT INTO `wp_blogmeta` VALUES (1,1,'db_version','61833'),(2,1,'db_last_updated','0.85875300 1790762058');
/*!40000 ALTER TABLE `wp_blogmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_blogs`
--

DROP TABLE IF EXISTS `wp_blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_blogs` (
  `blog_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned NOT NULL DEFAULT '0',
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public` tinyint NOT NULL DEFAULT '1',
  `archived` tinyint NOT NULL DEFAULT '0',
  `mature` tinyint NOT NULL DEFAULT '0',
  `spam` tinyint NOT NULL DEFAULT '0',
  `deleted` tinyint NOT NULL DEFAULT '0',
  `lang_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`blog_id`),
  KEY `domain` (`domain`(50),`path`(5)),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_blogs`
--

LOCK TABLES `wp_blogs` WRITE;
/*!40000 ALTER TABLE `wp_blogs` DISABLE KEYS */;
INSERT INTO `wp_blogs` VALUES (1,1,'blocs-aws.xtec.cat','/','2012-12-19 13:08:45','2015-03-11 10:50:12',1,0,0,0,0,0),(5,1,'blocs-aws.xtec.cat','/lestortugues/','2012-12-20 11:12:26','2015-04-20 12:15:47',1,0,0,0,0,1),(3,1,'blocs-aws.xtec.cat','/elsdofins/','2012-12-20 08:51:02','2015-04-20 12:16:10',1,0,0,0,0,0),(4,1,'blocs-aws.xtec.cat','/elscargols/','2012-12-20 09:20:19','2015-04-20 12:16:16',1,0,0,0,0,1);
/*!40000 ALTER TABLE `wp_blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_cas_count`
--

DROP TABLE IF EXISTS `wp_cas_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_cas_count` (
  `id` int NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_cas_count`
--

LOCK TABLES `wp_cas_count` WRITE;
/*!40000 ALTER TABLE `wp_cas_count` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_cas_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_cas_image`
--

DROP TABLE IF EXISTS `wp_cas_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_cas_image` (
  `id` int NOT NULL,
  `createtime` int NOT NULL,
  `word` varchar(20) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_cas_image`
--

LOCK TABLES `wp_cas_image` WRITE;
/*!40000 ALTER TABLE `wp_cas_image` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_cas_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_commentmeta`
--

DROP TABLE IF EXISTS `wp_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_commentmeta`
--

LOCK TABLES `wp_commentmeta` WRITE;
/*!40000 ALTER TABLE `wp_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_comments`
--

DROP TABLE IF EXISTS `wp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT 'comment',
  `comment_parent` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_comments`
--

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_delblocs`
--

DROP TABLE IF EXISTS `wp_delblocs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_delblocs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_id` varchar(60) NOT NULL,
  `site_path` varchar(100) NOT NULL,
  `blogname` varchar(255) NOT NULL,
  `del_date` datetime NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_delblocs`
--

LOCK TABLES `wp_delblocs` WRITE;
/*!40000 ALTER TABLE `wp_delblocs` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_delblocs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_delblocs_users`
--

DROP TABLE IF EXISTS `wp_delblocs_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_delblocs_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blog_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_login` varchar(60) NOT NULL,
  `display_name` varchar(60) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `meta_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_delblocs_users`
--

LOCK TABLES `wp_delblocs_users` WRITE;
/*!40000 ALTER TABLE `wp_delblocs_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_delblocs_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_descriptors`
--

DROP TABLE IF EXISTS `wp_descriptors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_descriptors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descriptor` varchar(50) NOT NULL DEFAULT '',
  `number` int NOT NULL DEFAULT '0',
  `blogs` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descriptor` (`descriptor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_descriptors`
--

LOCK TABLES `wp_descriptors` WRITE;
/*!40000 ALTER TABLE `wp_descriptors` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_descriptors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_descriptors_pre`
--

DROP TABLE IF EXISTS `wp_descriptors_pre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_descriptors_pre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descriptor` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `descriptor` (`descriptor`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_descriptors_pre`
--

LOCK TABLES `wp_descriptors_pre` WRITE;
/*!40000 ALTER TABLE `wp_descriptors_pre` DISABLE KEYS */;
INSERT INTO `wp_descriptors_pre` VALUES (1,'matemàtiques'),(2,'socials'),(3,'català'),(4,'castellà'),(5,'descoberta'),(6,'comunicació'),(7,'literatura'),(8,'aranès'),(9,'idiomes'),(10,'naturals'),(11,'música'),(12,'art'),(13,'visual'),(14,'plàstica'),(15,'física'),(16,'drets'),(17,'ciutadania'),(18,'tutoria'),(19,'religió'),(20,'tecnologia'),(21,'clàssiques'),(22,'filosofia'),(23,'història'),(24,'biologia'),(25,'química'),(26,'dibuix'),(27,'economia'),(28,'organització'),(29,'empresa'),(30,'geografia'),(31,'grec'),(32,'contemporani'),(33,'món'),(34,'electrotècnia'),(35,'llatí'),(36,'industrial'),(37,'mecànica'),(38,'disseny'),(39,'imatge'),(40,'expressió'),(41,'volum'),(42,'recerca'),(43,'primària'),(44,'batxillerat'),(45,'secundària'),(46,'cicles');
/*!40000 ALTER TABLE `wp_descriptors_pre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_globalposts`
--

DROP TABLE IF EXISTS `wp_globalposts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_globalposts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blogId` int NOT NULL DEFAULT '0',
  `time` varchar(20) NOT NULL DEFAULT '',
  `postType` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_globalposts`
--

LOCK TABLES `wp_globalposts` WRITE;
/*!40000 ALTER TABLE `wp_globalposts` DISABLE KEYS */;
INSERT INTO `wp_globalposts` VALUES (2,6,'1424337300',1),(3,3,'1424347939',1),(4,3,'1424348206',1),(5,18,'1425287276',1),(6,1,'1425887281',1),(7,1,'1425887377',1),(8,3,'1426064756',1),(9,1,'1426071012',1),(10,3,'1426604451',1),(11,3,'1427783806',1),(12,4,'1427783812',1),(13,5,'1427783821',1),(14,4,'1428389390',1),(15,52,'1428389399',1),(16,3,'1428389407',1),(17,5,'1428389429',1);
/*!40000 ALTER TABLE `wp_globalposts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_links`
--

DROP TABLE IF EXISTS `wp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_links` (
  `link_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint unsigned NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_links`
--

LOCK TABLES `wp_links` WRITE;
/*!40000 ALTER TABLE `wp_links` DISABLE KEYS */;
INSERT INTO `wp_links` VALUES (1,'http://codex.wordpress.org/','Documentation','','','','Y',1,0,'0000-00-00 00:00:00','','',''),(2,'http://wordpress.org/news/','WordPress Blog','','','','Y',1,0,'0000-00-00 00:00:00','','','http://wordpress.org/news/feed/'),(3,'http://wordpress.org/extend/ideas/','Suggest Ideas','','','','Y',1,0,'0000-00-00 00:00:00','','',''),(4,'http://wordpress.org/support/','Support Forum','','','','Y',1,0,'0000-00-00 00:00:00','','',''),(5,'http://wordpress.org/extend/plugins/','Plugins','','','','Y',1,0,'0000-00-00 00:00:00','','',''),(6,'http://wordpress.org/extend/themes/','Themes','','','','Y',1,0,'0000-00-00 00:00:00','','',''),(7,'http://planet.wordpress.org/','WordPress Planet','','','','Y',1,0,'0000-00-00 00:00:00','','','');
/*!40000 ALTER TABLE `wp_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=MyISAM AUTO_INCREMENT=1121 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_options`
--

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` VALUES (1,'siteurl','https://blocs-aws.xtec.cat','yes'),(2,'blogname','XTECBlocs','yes'),(3,'blogdescription','Portal XTECBlocs','yes'),(4,'users_can_register','0','yes'),(5,'admin_email','admin@blocs.xtec.cat','yes'),(6,'start_of_week','1','yes'),(7,'use_balanceTags','0','yes'),(8,'use_smilies','1','yes'),(9,'require_name_email','','yes'),(10,'comments_notify','1','yes'),(11,'posts_per_rss','10','yes'),(12,'rss_use_excerpt','0','yes'),(13,'mailserver_url','mail.example.com','yes'),(14,'mailserver_login','login@example.com','yes'),(15,'mailserver_pass','password','yes'),(16,'mailserver_port','110','yes'),(17,'default_category','1','yes'),(18,'default_comment_status','closed','yes'),(19,'default_ping_status','open','yes'),(20,'default_pingback_flag','','yes'),(22,'posts_per_page','10','yes'),(23,'date_format','d/M/y','yes'),(24,'time_format','g:i a','yes'),(25,'links_updated_date_format','j F Y G:i','yes'),(29,'comment_moderation','','yes'),(30,'moderation_notify','1','yes'),(31,'permalink_structure','/blog/%year%/%monthnum%/%day%/%postname%/','yes'),(33,'hack_file','0','yes'),(34,'blog_charset','UTF-8','yes'),(35,'moderation_keys','','no'),(36,'active_plugins','a:4:{i:0;s:25:\"add-to-any/add-to-any.php\";i:1;s:49:\"google-calendar-events/google-calendar-events.php\";i:2;s:57:\"multisite-clone-duplicator/multisite-clone-duplicator.php\";i:4;s:33:\"xtec-weekblog2/xtec-weekblog2.php\";}','yes'),(37,'home','https://blocs-aws.xtec.cat','yes'),(38,'category_base','','yes'),(39,'ping_sites','https://rpc.pingomatic.com/','yes'),(41,'comment_max_links','2','yes'),(42,'gmt_offset','','yes'),(43,'default_email_category','1','yes'),(44,'recently_edited','a:3:{i:0;s:51:\"/srv/www/blocs/src/wp-content/themes/home/style.css\";i:1;s:71:\"/srv/www/blocs/src/wp-content/plugins/addthis/addthis_social_widget.php\";i:2;s:0:\"\";}','no'),(45,'template','twentytwentyfive','yes'),(46,'stylesheet','twentytwentyfive','yes'),(1119,'finished_updating_comment_type','0','auto'),(49,'comment_registration','1','yes'),(51,'html_type','text/html','yes'),(52,'use_trackback','0','yes'),(53,'default_role','subscriber','yes'),(54,'db_version','61833','yes'),(55,'uploads_use_yearmonth_folders','1','yes'),(56,'upload_path','wp-content/uploads','yes'),(57,'blog_public','1','yes'),(58,'default_link_category','2','yes'),(59,'show_on_front','posts','yes'),(60,'tag_base','','yes'),(61,'show_avatars','1','yes'),(62,'avatar_rating','G','yes'),(63,'upload_url_path','','yes'),(64,'thumbnail_size_w','150','yes'),(65,'thumbnail_size_h','150','yes'),(66,'thumbnail_crop','1','yes'),(67,'medium_size_w','300','yes'),(68,'medium_size_h','300','yes'),(69,'avatar_default','mystery','yes'),(72,'large_size_w','1024','yes'),(73,'large_size_h','1024','yes'),(74,'image_default_link_type','file','yes'),(75,'image_default_size','','yes'),(76,'image_default_align','','yes'),(77,'close_comments_for_old_posts','','yes'),(78,'close_comments_days_old','14','yes'),(79,'thread_comments','','yes'),(80,'thread_comments_depth','5','yes'),(81,'page_comments','1','yes'),(82,'comments_per_page','50','yes'),(83,'default_comments_page','newest','yes'),(84,'comment_order','asc','yes'),(85,'sticky_posts','a:0:{}','yes'),(86,'widget_categories','a:2:{i:2;a:4:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:12:\"hierarchical\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),(87,'widget_text','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(88,'widget_rss','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(89,'timezone_string','Europe/Madrid','yes'),(91,'embed_size_w','','yes'),(92,'embed_size_h','600','yes'),(93,'page_for_posts','0','yes'),(94,'page_on_front','0','yes'),(95,'default_post_format','0','yes'),(96,'wp_user_roles','a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:64:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:45:\"slideshow-jquery-image-gallery-add-slideshows\";b:1;s:46:\"slideshow-jquery-image-gallery-edit-slideshows\";b:1;s:48:\"slideshow-jquery-image-gallery-delete-slideshows\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:37:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:45:\"slideshow-jquery-image-gallery-add-slideshows\";b:1;s:46:\"slideshow-jquery-image-gallery-edit-slideshows\";b:1;s:48:\"slideshow-jquery-image-gallery-delete-slideshows\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:13:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:45:\"slideshow-jquery-image-gallery-add-slideshows\";b:1;s:46:\"slideshow-jquery-image-gallery-edit-slideshows\";b:1;s:48:\"slideshow-jquery-image-gallery-delete-slideshows\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:6:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:12:\"upload_files\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}','yes'),(97,'widget_search','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),(98,'widget_recent-posts','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),(99,'widget_recent-comments','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),(100,'widget_archives','a:2:{i:2;a:3:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),(101,'widget_meta','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),(102,'sidebars_widgets','a:2:{s:19:\"wp_inactive_widgets\";a:13:{i:0;s:7:\"pages-2\";i:1;s:10:\"calendar-2\";i:2;s:7:\"links-2\";i:3;s:6:\"text-2\";i:4;s:5:\"rss-2\";i:5;s:11:\"tag_cloud-2\";i:6;s:10:\"nav_menu-2\";i:7;s:8:\"search-2\";i:8;s:14:\"recent-posts-2\";i:9;s:17:\"recent-comments-2\";i:10;s:10:\"archives-2\";i:11;s:12:\"categories-2\";i:12;s:6:\"meta-2\";}s:13:\"array_version\";i:3;}','yes'),(185,'$xtec_descriptors_db_version','1.0','yes'),(103,'cron','a:8:{i:1790762118;a:1:{s:28:\"wp_update_comment_type_batch\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:2:{s:8:\"schedule\";b:0;s:4:\"args\";a:0:{}}}}i:1790762320;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1790773517;a:3:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1790773602;a:1:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1790773902;a:1:{s:21:\"update_network_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1790840875;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1790845120;a:2:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}s:41:\"wp_privacy_personal_data_cleanup_requests\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}s:7:\"version\";i:2;}','on'),(1077,'slideshow-plugin-updated-from-v2-to-v2-1-20','updated','no'),(1075,'simple-calendar_settings_calendars','a:1:{s:7:\"general\";s:9:\"post,page\";}','yes'),(1076,'slideshow-plugin-updated-from-v1-x-x-to-v2-0-1','updated','no'),(265,'theme_mods_twentytwelve','a:2:{i:0;b:0;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1389360134;s:4:\"data\";a:2:{s:19:\"wp_inactive_widgets\";a:13:{i:0;s:7:\"pages-2\";i:1;s:10:\"calendar-2\";i:2;s:7:\"links-2\";i:3;s:6:\"text-2\";i:4;s:5:\"rss-2\";i:5;s:11:\"tag_cloud-2\";i:6;s:10:\"nav_menu-2\";i:7;s:8:\"search-2\";i:8;s:14:\"recent-posts-2\";i:9;s:17:\"recent-comments-2\";i:10;s:10:\"archives-2\";i:11;s:12:\"categories-2\";i:12;s:6:\"meta-2\";}s:9:\"sidebar-1\";N;}}}','off'),(105,'_site_transient_update_core','O:8:\"stdClass\":3:{s:7:\"updates\";a:2:{i:0;O:8:\"stdClass\":7:{s:8:\"response\";s:7:\"upgrade\";s:3:\"url\";s:24:\"http://ca.wordpress.org/\";s:7:\"package\";s:44:\"http://ca.wordpress.org/wordpress-3.5-ca.zip\";s:7:\"current\";s:3:\"3.5\";s:6:\"locale\";s:2:\"ca\";s:11:\"php_version\";s:5:\"5.2.4\";s:13:\"mysql_version\";s:3:\"5.0\";}i:1;O:8:\"stdClass\":7:{s:8:\"response\";s:7:\"upgrade\";s:3:\"url\";s:30:\"http://wordpress.org/download/\";s:7:\"package\";s:38:\"http://wordpress.org/wordpress-3.5.zip\";s:7:\"current\";s:3:\"3.5\";s:6:\"locale\";s:5:\"en_US\";s:11:\"php_version\";s:5:\"5.2.4\";s:13:\"mysql_version\";s:3:\"5.0\";}}s:12:\"last_checked\";i:1355993076;s:15:\"version_checked\";s:5:\"3.1.4\";}','yes'),(106,'_site_transient_update_plugins','O:8:\"stdClass\":3:{s:12:\"last_checked\";i:1355993076;s:7:\"checked\";a:32:{s:33:\"addthis/addthis_social_widget.php\";s:5:\"2.3.0\";s:37:\"blogger-importer/blogger-importer.php\";s:3:\"0.5\";s:51:\"creative-commons-configurator-1/cc-configurator.php\";s:5:\"1.4.0\";s:43:\"google-analyticator/google-analyticator.php\";s:5:\"6.1.3\";s:43:\"multisite-plugin-manager/plugin-manager.php\";s:5:\"3.1.2\";s:50:\"peters-custom-anti-spam-image/custom_anti_spam.php\";s:5:\"3.1.4\";s:32:\"simpler-ipaper/scribd-ipaper.php\";s:5:\"1.3.1\";s:97:\"simple-trackback-validation-with-topsy-blocker/simple-trackback-validation-with-topsy-blocker.php\";s:3:\"0.7\";s:25:\"slideshare/slideshare.php\";s:5:\"1.7.2\";s:49:\"vipers-video-quicktags/vipers-video-quicktags.php\";s:5:\"6.3.0\";s:41:\"wordpress-importer/wordpress-importer.php\";s:3:\"0.6\";s:29:\"wp-recaptcha/wp-recaptcha.php\";s:5:\"3.1.6\";s:27:\"wp-super-cache/wp-cache.php\";s:3:\"1.2\";s:37:\"xtec-allowedTags/xtec-allowedTags.php\";s:3:\"1.0\";s:21:\"xtec-api/xtec-api.php\";s:3:\"1.0\";s:37:\"xtec-descriptors/xtec-descriptors.php\";s:3:\"1.1\";s:33:\"xtec-favorites/xtec-favorites.php\";s:3:\"1.0\";s:23:\"xtec-info/xtec-info.php\";s:3:\"1.1\";s:31:\"xtec-iso2utf8/xtec-iso2utf8.php\";s:3:\"1.1\";s:41:\"xtec-lastest-posts/xtec-lastest-posts.php\";s:3:\"1.0\";s:35:\"xtec-ldap-login/xtec-ldap-login.php\";s:3:\"1.1\";s:37:\"xtec-link-player/xtec-link-player.php\";s:3:\"1.1\";s:23:\"xtec-mail/xtec-mail.php\";s:3:\"2.0\";s:37:\"xtec-maintenance/xtec-maintenance.php\";s:3:\"1.1\";s:35:\"xtec-real-media/xtec-real-media.php\";s:3:\"1.0\";s:27:\"xtec-search/xtec-search.php\";s:3:\"1.1\";s:31:\"xtec-settings/xtec-settings.php\";s:3:\"1.1\";s:27:\"xtec-signup/xtec-signup.php\";s:3:\"1.1\";s:28:\"xtec-tinymce/xtectinymce.php\";s:3:\"1.0\";s:25:\"xtec-users/xtec-users.php\";s:3:\"1.1\";s:35:\"xtec-viquiatles/xtec-viquiatles.php\";s:3:\"1.1\";s:33:\"xtec-weekblog2/xtec-weekblog2.php\";s:3:\"1.0\";}s:8:\"response\";a:4:{s:33:\"addthis/addthis_social_widget.php\";O:8:\"stdClass\":6:{s:2:\"id\";s:4:\"5710\";s:4:\"slug\";s:7:\"addthis\";s:11:\"new_version\";s:5:\"3.0.2\";s:14:\"upgrade_notice\";s:10:\"Bug fixes.\";s:3:\"url\";s:44:\"http://wordpress.org/extend/plugins/addthis/\";s:7:\"package\";s:55:\"http://downloads.wordpress.org/plugin/addthis.3.0.2.zip\";}s:97:\"simple-trackback-validation-with-topsy-blocker/simple-trackback-validation-with-topsy-blocker.php\";O:8:\"stdClass\":5:{s:2:\"id\";s:5:\"14827\";s:4:\"slug\";s:46:\"simple-trackback-validation-with-topsy-blocker\";s:11:\"new_version\";s:5:\"1.1.5\";s:3:\"url\";s:83:\"http://wordpress.org/extend/plugins/simple-trackback-validation-with-topsy-blocker/\";s:7:\"package\";s:88:\"http://downloads.wordpress.org/plugin/simple-trackback-validation-with-topsy-blocker.zip\";}s:25:\"slideshare/slideshare.php\";O:8:\"stdClass\":5:{s:2:\"id\";s:4:\"1569\";s:4:\"slug\";s:10:\"slideshare\";s:11:\"new_version\";s:3:\"1.8\";s:3:\"url\";s:47:\"http://wordpress.org/extend/plugins/slideshare/\";s:7:\"package\";s:58:\"http://downloads.wordpress.org/plugin/slideshare.1.8.1.zip\";}s:49:\"vipers-video-quicktags/vipers-video-quicktags.php\";O:8:\"stdClass\":6:{s:2:\"id\";s:3:\"530\";s:4:\"slug\";s:22:\"vipers-video-quicktags\";s:11:\"new_version\";s:5:\"6.4.4\";s:14:\"upgrade_notice\";s:108:\"Updates to support new version of jQuery UI that is included in WordPress 3.5. Fixes dialog box not opening.\";s:3:\"url\";s:59:\"http://wordpress.org/extend/plugins/vipers-video-quicktags/\";s:7:\"package\";s:70:\"http://downloads.wordpress.org/plugin/vipers-video-quicktags.6.4.4.zip\";}}}','yes'),(107,'_transient_random_seed','0deba9f27f5e55fbbbfa3772847167ae','yes'),(108,'auth_salt','Y,z|6YO7Z+GV7C$-DD}gCu |7/IV3-gX*(dO9;DF}u$>FC&MI(IDBe |w#Vd?/K4','yes'),(109,'logged_in_salt','[fYsPk&Ugm@;bc[&)Vg Zl#z!5}/<-k$=$xjJ`/uzzf-5KKX~Uxxaa0.MzlV*phA','yes'),(110,'widget_pages','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(111,'widget_calendar','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(112,'widget_links','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(113,'widget_tag_cloud','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(114,'widget_nav_menu','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(117,'_site_transient_update_themes','O:8:\"stdClass\":3:{s:12:\"last_checked\";i:1355993077;s:7:\"checked\";a:28:{s:13:\"almost-spring\";s:3:\"1.0\";s:7:\"anarchy\";s:3:\"1.1\";s:9:\"andreas09\";s:3:\"2.1\";s:11:\"big-blue-01\";s:3:\"0.1\";s:7:\"classic\";s:3:\"1.5\";s:7:\"default\";s:5:\"1.7.2\";s:10:\"digg-3-col\";s:5:\"1.0.1\";s:4:\"flex\";s:3:\"1.0\";s:9:\"freshy-10\";s:3:\"1.0\";s:7:\"freshy2\";s:5:\"2.1.2\";s:11:\"gentle_calm\";s:3:\"1.0\";s:14:\"glossyblue-1-2\";s:3:\"1.2\";s:4:\"home\";s:3:\"1.0\";s:12:\"home_hipolit\";s:3:\"1.0\";s:8:\"light-10\";s:3:\"1.0\";s:7:\"mandigo\";s:5:\"1.7.1\";s:17:\"manycolorsidea-10\";s:3:\"2.1\";s:10:\"newsportal\";s:3:\"1.0\";s:14:\"quadruple-blue\";s:3:\"1.0\";s:6:\"simpla\";s:4:\"1.01\";s:12:\"stardust-v10\";s:3:\"2.7\";s:5:\"steam\";s:3:\"1.5\";s:14:\"tranquility-10\";s:3:\"1.2\";s:9:\"twentyten\";s:3:\"1.2\";s:11:\"whiteasmilk\";s:3:\"1.8\";s:9:\"xtec-v1.1\";s:3:\"1.1\";s:4:\"xtec\";s:3:\"1.0\";s:14:\"xtec898_encurs\";s:4:\"v2.0\";}s:8:\"response\";a:1:{s:9:\"twentyten\";a:3:{s:11:\"new_version\";s:3:\"1.5\";s:3:\"url\";s:44:\"http://wordpress.org/extend/themes/twentyten\";s:7:\"package\";s:61:\"http://wordpress.org/extend/themes/download/twentyten.1.5.zip\";}}}','yes'),(118,'dashboard_widget_options','a:4:{s:25:\"dashboard_recent_comments\";a:1:{s:5:\"items\";i:5;}s:24:\"dashboard_incoming_links\";a:5:{s:4:\"home\";s:26:\"https://blocs-aws.xtec.cat\";s:4:\"link\";s:94:\"http://blogsearch.google.com/blogsearch?scoring=d&partner=wordpress&q=link:http://agora/blocs/\";s:3:\"url\";s:127:\"http://blogsearch.google.com/blogsearch_feeds?scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:http://agora/blocs/\";s:5:\"items\";i:10;s:9:\"show_date\";b:0;}s:17:\"dashboard_primary\";a:7:{s:4:\"link\";s:26:\"http://wordpress.org/news/\";s:3:\"url\";s:31:\"http://wordpress.org/news/feed/\";s:5:\"title\";s:18:\"Bloc del WordPress\";s:5:\"items\";i:2;s:12:\"show_summary\";i:1;s:11:\"show_author\";i:0;s:9:\"show_date\";i:1;}s:19:\"dashboard_secondary\";a:7:{s:4:\"link\";s:28:\"http://planet.wordpress.org/\";s:3:\"url\";s:33:\"http://planet.wordpress.org/feed/\";s:5:\"title\";s:30:\"Altres notícies del WordPress\";s:5:\"items\";i:5;s:12:\"show_summary\";i:0;s:11:\"show_author\";i:0;s:9:\"show_date\";i:0;}}','off'),(119,'nonce_salt','6faUl0psNyW2>W _ygM;=QqfW@2O0LK[%1X ?bPR@<HMl;He4eAFFEw/:MO.6#_s','yes'),(183,'current_theme','Twenty Twenty-Five','yes'),(178,'recently_activated','a:1:{s:42:\"wordpress-social-login/wp-social-login.php\";i:1489495650;}','off'),(148,'fileupload_url','https://blocs-aws.xtec.cat/wp-content/uploads','yes'),(427,'ossdl_https','1','yes'),(625,'rewrite_rules','a:167:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"blog/slideshow/?$\";s:29:\"index.php?post_type=slideshow\";s:47:\"blog/slideshow/feed/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?post_type=slideshow&feed=$matches[1]\";s:42:\"blog/slideshow/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?post_type=slideshow&feed=$matches[1]\";s:34:\"blog/slideshow/page/([0-9]{1,})/?$\";s:47:\"index.php?post_type=slideshow&paged=$matches[1]\";s:20:\"blog/xtecweekblog/?$\";s:32:\"index.php?post_type=xtecweekblog\";s:50:\"blog/xtecweekblog/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?post_type=xtecweekblog&feed=$matches[1]\";s:45:\"blog/xtecweekblog/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?post_type=xtecweekblog&feed=$matches[1]\";s:37:\"blog/xtecweekblog/page/([0-9]{1,})/?$\";s:50:\"index.php?post_type=xtecweekblog&paged=$matches[1]\";s:52:\"blog/category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:47:\"blog/category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:28:\"blog/category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:40:\"blog/category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:22:\"blog/category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:49:\"blog/tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:44:\"blog/tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:25:\"blog/tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:37:\"blog/tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:19:\"blog/tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:50:\"blog/type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:45:\"blog/type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:26:\"blog/type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:38:\"blog/type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:20:\"blog/type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:59:\"blog/calendar_feed/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?calendar_feed=$matches[1]&feed=$matches[2]\";s:54:\"blog/calendar_feed/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?calendar_feed=$matches[1]&feed=$matches[2]\";s:35:\"blog/calendar_feed/([^/]+)/embed/?$\";s:46:\"index.php?calendar_feed=$matches[1]&embed=true\";s:47:\"blog/calendar_feed/([^/]+)/page/?([0-9]{1,})/?$\";s:53:\"index.php?calendar_feed=$matches[1]&paged=$matches[2]\";s:29:\"blog/calendar_feed/([^/]+)/?$\";s:35:\"index.php?calendar_feed=$matches[1]\";s:59:\"blog/calendar_type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?calendar_type=$matches[1]&feed=$matches[2]\";s:54:\"blog/calendar_type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?calendar_type=$matches[1]&feed=$matches[2]\";s:35:\"blog/calendar_type/([^/]+)/embed/?$\";s:46:\"index.php?calendar_type=$matches[1]&embed=true\";s:47:\"blog/calendar_type/([^/]+)/page/?([0-9]{1,})/?$\";s:53:\"index.php?calendar_type=$matches[1]&paged=$matches[2]\";s:29:\"blog/calendar_type/([^/]+)/?$\";s:35:\"index.php?calendar_type=$matches[1]\";s:63:\"blog/calendar_category/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:56:\"index.php?calendar_category=$matches[1]&feed=$matches[2]\";s:58:\"blog/calendar_category/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:56:\"index.php?calendar_category=$matches[1]&feed=$matches[2]\";s:39:\"blog/calendar_category/([^/]+)/embed/?$\";s:50:\"index.php?calendar_category=$matches[1]&embed=true\";s:51:\"blog/calendar_category/([^/]+)/page/?([0-9]{1,})/?$\";s:57:\"index.php?calendar_category=$matches[1]&paged=$matches[2]\";s:33:\"blog/calendar_category/([^/]+)/?$\";s:39:\"index.php?calendar_category=$matches[1]\";s:36:\"calendar/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:46:\"calendar/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:66:\"calendar/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"calendar/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:61:\"calendar/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:42:\"calendar/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:25:\"calendar/([^/]+)/embed/?$\";s:41:\"index.php?calendar=$matches[1]&embed=true\";s:29:\"calendar/([^/]+)/trackback/?$\";s:35:\"index.php?calendar=$matches[1]&tb=1\";s:37:\"calendar/([^/]+)/page/?([0-9]{1,})/?$\";s:48:\"index.php?calendar=$matches[1]&paged=$matches[2]\";s:44:\"calendar/([^/]+)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?calendar=$matches[1]&cpage=$matches[2]\";s:33:\"calendar/([^/]+)(?:/([0-9]+))?/?$\";s:47:\"index.php?calendar=$matches[1]&page=$matches[2]\";s:25:\"calendar/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:35:\"calendar/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:55:\"calendar/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:50:\"calendar/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:50:\"calendar/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:31:\"calendar/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:42:\"blog/slideshow/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:52:\"blog/slideshow/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:72:\"blog/slideshow/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:67:\"blog/slideshow/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:67:\"blog/slideshow/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:48:\"blog/slideshow/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:31:\"blog/slideshow/([^/]+)/embed/?$\";s:42:\"index.php?slideshow=$matches[1]&embed=true\";s:35:\"blog/slideshow/([^/]+)/trackback/?$\";s:36:\"index.php?slideshow=$matches[1]&tb=1\";s:55:\"blog/slideshow/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?slideshow=$matches[1]&feed=$matches[2]\";s:50:\"blog/slideshow/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?slideshow=$matches[1]&feed=$matches[2]\";s:43:\"blog/slideshow/([^/]+)/page/?([0-9]{1,})/?$\";s:49:\"index.php?slideshow=$matches[1]&paged=$matches[2]\";s:50:\"blog/slideshow/([^/]+)/comment-page-([0-9]{1,})/?$\";s:49:\"index.php?slideshow=$matches[1]&cpage=$matches[2]\";s:39:\"blog/slideshow/([^/]+)(?:/([0-9]+))?/?$\";s:48:\"index.php?slideshow=$matches[1]&page=$matches[2]\";s:31:\"blog/slideshow/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:41:\"blog/slideshow/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:61:\"blog/slideshow/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:56:\"blog/slideshow/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:56:\"blog/slideshow/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:37:\"blog/slideshow/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:45:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:55:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:75:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:70:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:70:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:51:\"blog/xtecweekblog/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:34:\"blog/xtecweekblog/([^/]+)/embed/?$\";s:45:\"index.php?xtecweekblog=$matches[1]&embed=true\";s:38:\"blog/xtecweekblog/([^/]+)/trackback/?$\";s:39:\"index.php?xtecweekblog=$matches[1]&tb=1\";s:58:\"blog/xtecweekblog/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?xtecweekblog=$matches[1]&feed=$matches[2]\";s:53:\"blog/xtecweekblog/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?xtecweekblog=$matches[1]&feed=$matches[2]\";s:46:\"blog/xtecweekblog/([^/]+)/page/?([0-9]{1,})/?$\";s:52:\"index.php?xtecweekblog=$matches[1]&paged=$matches[2]\";s:53:\"blog/xtecweekblog/([^/]+)/comment-page-([0-9]{1,})/?$\";s:52:\"index.php?xtecweekblog=$matches[1]&cpage=$matches[2]\";s:42:\"blog/xtecweekblog/([^/]+)(?:/([0-9]+))?/?$\";s:51:\"index.php?xtecweekblog=$matches[1]&page=$matches[2]\";s:34:\"blog/xtecweekblog/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:44:\"blog/xtecweekblog/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:64:\"blog/xtecweekblog/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:59:\"blog/xtecweekblog/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:59:\"blog/xtecweekblog/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:40:\"blog/xtecweekblog/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:16:\".*wp-signup.php$\";s:21:\"index.php?signup=true\";s:18:\".*wp-activate.php$\";s:23:\"index.php?activate=true\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:52:\"blog/author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:47:\"blog/author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:28:\"blog/author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:40:\"blog/author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:22:\"blog/author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:74:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:69:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:50:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:62:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:44:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:61:\"blog/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:56:\"blog/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:37:\"blog/([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:49:\"blog/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:31:\"blog/([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:48:\"blog/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:43:\"blog/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:24:\"blog/([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:36:\"blog/([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:18:\"blog/([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:63:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:73:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:93:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:88:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:88:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:69:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:58:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$\";s:91:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true\";s:62:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:85:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1\";s:82:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:77:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:70:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]\";s:77:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]\";s:66:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]\";s:52:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:62:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:82:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:77:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:77:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:58:\"blog/[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:69:\"blog/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]\";s:56:\"blog/([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:43:\"blog/([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}','yes'),(181,'wpsupercache_gc_time','1489495449','yes'),(182,'allowedthemes','a:1:{s:4:\"home\";b:1;}','off'),(184,'theme_mods_home','a:2:{i:0;b:0;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1389360121;s:4:\"data\";a:1:{s:19:\"wp_inactive_widgets\";a:13:{i:0;s:7:\"pages-2\";i:1;s:10:\"calendar-2\";i:2;s:7:\"links-2\";i:3;s:6:\"text-2\";i:4;s:5:\"rss-2\";i:5;s:11:\"tag_cloud-2\";i:6;s:10:\"nav_menu-2\";i:7;s:8:\"search-2\";i:8;s:14:\"recent-posts-2\";i:9;s:17:\"recent-comments-2\";i:10;s:10:\"archives-2\";i:11;s:12:\"categories-2\";i:12;s:6:\"meta-2\";}}}}','off'),(186,'$xtec_favorites_db_version','1.0','yes'),(187,'ga_status','disabled','yes'),(188,'ga_uid','XX-XXXXX-X','yes'),(189,'ga_admin_status','enabled','yes'),(190,'ga_admin_disable','remove','yes'),(191,'ga_admin_role','a:1:{i:0;s:13:\"administrator\";}','yes'),(192,'ga_dashboard_role','a:1:{i:0;s:13:\"administrator\";}','yes'),(193,'ga_adsense','','yes'),(194,'ga_extra','','yes'),(195,'ga_extra_after','','yes'),(196,'ga_event','enabled','yes'),(197,'ga_outbound','enabled','yes'),(198,'ga_outbound_prefix','outgoing','yes'),(199,'ga_downloads','','yes'),(200,'ga_downloads_prefix','download','yes'),(201,'ga_profileid','','yes'),(202,'ga_widgets','enabled','yes'),(203,'ga_google_token','','yes'),(204,'ga_compatibility','off','yes'),(205,'widget_googlestats','a:2:{i:2;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(206,'vvq_options','a:2:{i:0;b:0;s:7:\"version\";s:5:\"6.3.0\";}','yes'),(207,'ossdl_off_cdn_url','https://blocs-aws.xtec.cat','yes'),(208,'ossdl_off_include_dirs','wp-content,wp-includes','yes'),(209,'ossdl_off_exclude','.php','yes'),(210,'ossdl_cname','','yes'),(262,'uninstall_plugins','a:4:{i:0;b:0;s:27:\"wp-super-cache/wp-cache.php\";s:22:\"wpsupercache_uninstall\";s:57:\"multisite-clone-duplicator/multisite-clone-duplicator.php\";a:2:{i:0;s:4:\"MUCD\";i:1;s:9:\"uninstall\";}s:45:\"simple-local-avatars/simple-local-avatars.php\";s:30:\"simple_local_avatars_uninstall\";}','no'),(212,'$xtec_maintenance_db_version','1.0','yes'),(213,'$xtec_sea_db_version','','yes'),(303,'tadv_settings','a:6:{s:7:\"options\";s:15:\"menubar,advlist\";s:9:\"toolbar_1\";s:117:\"bold,italic,blockquote,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,table,fullscreen,undo,redo,wp_adv\";s:9:\"toolbar_2\";s:121:\"formatselect,alignjustify,strikethrough,outdent,indent,pastetext,removeformat,charmap,wp_more,emoticons,forecolor,wp_help\";s:9:\"toolbar_3\";s:0:\"\";s:9:\"toolbar_4\";s:0:\"\";s:7:\"plugins\";s:107:\"anchor,code,insertdatetime,nonbreaking,print,searchreplace,table,visualblocks,visualchars,emoticons,advlist\";}','yes'),(304,'tadv_admin_settings','a:1:{s:7:\"options\";a:0:{}}','yes'),(732,'avatar_default_wp_user_avatar','','yes'),(733,'wp_user_avatar_allow_upload','0','yes'),(734,'wp_user_avatar_disable_gravatar','0','yes'),(735,'wp_user_avatar_edit_avatar','1','yes'),(736,'wp_user_avatar_resize_crop','0','yes'),(737,'wp_user_avatar_resize_h','96','yes'),(738,'wp_user_avatar_resize_upload','0','yes'),(739,'wp_user_avatar_resize_w','96','yes'),(740,'wp_user_avatar_tinymce','1','yes'),(741,'wp_user_avatar_upload_size_limit','0','yes'),(742,'wp_user_avatar_default_avatar_updated','1','yes'),(743,'wp_user_avatar_users_updated','1','yes'),(744,'wp_user_avatar_media_updated','1','yes'),(902,'recaptcha_options','a:5:{s:8:\"site_key\";s:40:\"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg\";s:6:\"secret\";s:40:\"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL\";s:14:\"comments_theme\";s:8:\"standard\";s:18:\"recaptcha_language\";s:2:\"ca\";s:17:\"no_response_error\";s:58:\"<strong>ERROR</strong>: Please fill in the reCAPTCHA form.\";}','yes'),(258,'ga_version','6.4.3','yes'),(259,'ga_annon','','yes'),(260,'ga_defaults','yes','yes'),(261,'link_manager_enabled','1','yes'),(263,'db_upgraded','1','yes'),(266,'theme_switched','xtecblocsdefault','yes'),(267,'freshy_options','a:20:{s:15:\"highlight_color\";s:7:\"#FF3C00\";s:17:\"description_color\";s:7:\"#ADCF20\";s:12:\"author_color\";s:7:\"#a3cb00\";s:10:\"sidebar_bg\";s:7:\"#FFFFFF\";s:20:\"sidebar_titles_color\";s:7:\"#f78b0c\";s:17:\"sidebar_titles_bg\";s:7:\"#FFFFFF\";s:7:\"menu_bg\";s:21:\"menu_start_triple.gif\";s:10:\"menu_color\";s:7:\"#000000\";s:9:\"header_bg\";s:10:\"header.jpg\";s:16:\"header_bg_custom\";s:0:\"\";s:19:\"sidebar_titles_type\";s:7:\"stripes\";s:16:\"first_menu_label\";s:5:\"Inici\";s:15:\"blog_menu_label\";s:4:\"Blog\";s:15:\"last_menu_label\";s:7:\"Contact\";s:14:\"last_menu_type\";s:0:\"\";s:13:\"contact_email\";s:0:\"\";s:12:\"contact_link\";s:0:\"\";s:9:\"menu_type\";s:4:\"auto\";s:10:\"args_pages\";s:32:\"sort_column=menu_order&title_li=\";s:9:\"args_cats\";s:168:\"hide_empty=0&sort_column=name&optioncount=1&title_li=&hierarchical=1&feed=RSS&feed_image=http://agora/blocs/wp-content/themes/xtec-v1.1/images/icons/feed-icon-10x10.gif\";}','yes'),(271,'tadv_version','4000','yes'),(268,'category_children','a:0:{}','yes'),(269,'theme_mods_xtec-v1.1','a:2:{i:0;b:0;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1389360150;s:4:\"data\";a:2:{s:19:\"wp_inactive_widgets\";a:13:{i:0;s:7:\"pages-2\";i:1;s:10:\"calendar-2\";i:2;s:7:\"links-2\";i:3;s:6:\"text-2\";i:4;s:5:\"rss-2\";i:5;s:11:\"tag_cloud-2\";i:6;s:10:\"nav_menu-2\";i:7;s:8:\"search-2\";i:8;s:14:\"recent-posts-2\";i:9;s:17:\"recent-comments-2\";i:10;s:10:\"archives-2\";i:11;s:12:\"categories-2\";i:12;s:6:\"meta-2\";}s:9:\"sidebar-1\";N;}}}','off'),(270,'theme_mods_xtecblocsdefault','a:2:{i:0;b:0;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1790762081;s:4:\"data\";a:1:{s:19:\"wp_inactive_widgets\";a:13:{i:0;s:7:\"pages-2\";i:1;s:10:\"calendar-2\";i:2;s:7:\"links-2\";i:3;s:6:\"text-2\";i:4;s:5:\"rss-2\";i:5;s:11:\"tag_cloud-2\";i:6;s:10:\"nav_menu-2\";i:7;s:8:\"search-2\";i:8;s:14:\"recent-posts-2\";i:9;s:17:\"recent-comments-2\";i:10;s:10:\"archives-2\";i:11;s:12:\"categories-2\";i:12;s:6:\"meta-2\";}}}}','off'),(283,'ga_disable_gasites','disabled','yes'),(284,'ga_analytic_snippet','enabled','yes'),(285,'ga_admin_disable_DimentionIndex','','yes'),(286,'key_ga_show_ad','1','yes'),(287,'ga_enhanced_link_attr','disabled','yes'),(360,'wpsupercache_count','0','yes'),(958,'bwp_capt_theme','a:4:{s:9:\"input_tab\";s:1:\"0\";s:10:\"enable_css\";s:3:\"yes\";s:11:\"select_lang\";s:2:\"es\";s:12:\"select_theme\";s:3:\"red\";}','yes'),(954,'bwp_capt_version','1.1.3','yes'),(957,'bwp_capt_general','a:18:{s:12:\"input_pubkey\";s:40:\"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg\";s:12:\"input_prikey\";s:40:\"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL\";s:11:\"input_error\";s:80:\"<strong>ERROR:</strong> Incorrect or empty reCAPTCHA response, please try again.\";s:10:\"input_back\";s:127:\"Error: Incorrect or empty reCAPTCHA response, please click the back button on your browser\'s toolbar or click on %s to go back.\";s:14:\"input_approved\";s:1:\"1\";s:14:\"enable_comment\";s:3:\"yes\";s:19:\"enable_registration\";s:0:\"\";s:12:\"enable_login\";s:0:\"\";s:14:\"enable_akismet\";s:0:\"\";s:15:\"use_global_keys\";s:3:\"yes\";s:10:\"select_cap\";s:14:\"manage_options\";s:14:\"select_cf7_tag\";s:9:\"recaptcha\";s:15:\"select_response\";s:8:\"redirect\";s:15:\"select_position\";s:19:\"after_comment_field\";s:20:\"select_akismet_react\";s:4:\"hold\";s:15:\"hide_registered\";s:3:\"yes\";s:8:\"hide_cap\";s:0:\"\";s:13:\"hide_approved\";s:0:\"\";}','yes'),(463,'wsl_settings_welcome_panel_enabled','2.2.3','yes'),(464,'wsl_settings_redirect_url','https://blocs-aws.xtec.cat','yes'),(465,'wsl_settings_force_redirect_url','2','yes'),(466,'wsl_settings_connect_with_label','Connecta amb:','yes'),(467,'wsl_settings_users_avatars','1','yes'),(468,'wsl_settings_use_popup','2','yes'),(469,'wsl_settings_widget_display','1','yes'),(470,'wsl_settings_authentication_widget_css','.wp-social-login-connect-with {}\n.wp-social-login-provider-list {}\n.wp-social-login-provider-list a {}\n.wp-social-login-provider-list img {}\n.wsl_connect_with_provider {}','yes'),(471,'wsl_settings_bouncer_registration_enabled','1','yes'),(472,'wsl_settings_bouncer_authentication_enabled','1','yes'),(473,'wsl_settings_bouncer_profile_completion_require_email','2','yes'),(474,'wsl_settings_bouncer_profile_completion_change_username','2','yes'),(475,'wsl_settings_bouncer_new_users_moderation_level','1','yes'),(476,'wsl_settings_bouncer_new_users_membership_default_role','default','yes'),(477,'wsl_settings_bouncer_new_users_restrict_domain_enabled','2','yes'),(478,'wsl_settings_bouncer_new_users_restrict_domain_text_bounce','<strong>This website is restricted to invited readers only.</strong><p>It doesn\'t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>','yes'),(479,'wsl_settings_bouncer_new_users_restrict_email_enabled','2','yes'),(480,'wsl_settings_bouncer_new_users_restrict_email_text_bounce','<strong>This website is restricted to invited readers only.</strong><p>It doesn\'t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>','yes'),(481,'wsl_settings_bouncer_new_users_restrict_profile_enabled','2','yes'),(482,'wsl_settings_bouncer_new_users_restrict_profile_text_bounce','<strong>This website is restricted to invited readers only.</strong><p>It doesn\'t look like you have been invited to access this site. If you think this is a mistake, you might want to contact the website owner and request an invitation.<p>','yes'),(483,'wsl_settings_contacts_import_facebook','2','yes'),(484,'wsl_settings_contacts_import_google','2','yes'),(485,'wsl_settings_contacts_import_twitter','2','yes'),(486,'wsl_settings_contacts_import_live','2','yes'),(487,'wsl_settings_contacts_import_linkedin','2','yes'),(488,'wsl_settings_buddypress_enable_mapping','2','yes'),(489,'wsl_settings_buddypress_xprofile_map','','yes'),(490,'wsl_settings_Google_enabled','0','yes'),(491,'wsl_settings_Moodle_enabled','0','yes'),(492,'wsl_components_core_enabled','1','yes'),(493,'wsl_components_networks_enabled','1','yes'),(494,'wsl_components_login-widget_enabled','1','yes'),(495,'wsl_components_bouncer_enabled','1','yes'),(496,'wsl_settings_Google_app_scope','profile https://www.googleapis.com/auth/plus.profile.emails.read','yes'),(497,'supercache_stats','a:3:{s:9:\"generated\";i:1429271272;s:10:\"supercache\";a:5:{s:7:\"expired\";i:0;s:12:\"expired_list\";a:0:{}s:6:\"cached\";i:0;s:11:\"cached_list\";a:0:{}s:2:\"ts\";i:1429271272;}s:7:\"wpcache\";a:3:{s:6:\"cached\";i:0;s:7:\"expired\";i:0;s:5:\"fsize\";s:3:\"0KB\";}}','yes'),(339,'mucd_duplicable','no','yes'),(359,'wpsupercache_start','1424430200','yes'),(582,'post_count','1','yes'),(624,'gce_settings_general','a:1:{s:13:\"save_settings\";i:1;}','yes'),(626,'gce_cpt_setup','1','yes'),(560,'WPLANG','ca','yes'),(561,'new_admin_email','admin@blocs.xtec.cat','yes'),(1038,'ga_analyticator_global_notification','1','yes'),(1039,'widget_gce_widget','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(1074,'calendar_feed_children','a:0:{}','yes'),(1044,'calendar_type_children','a:0:{}','yes'),(1045,'simple-calendar_settings_feeds','a:1:{s:6:\"google\";a:1:{s:7:\"api_key\";s:0:\"\";}}','yes'),(1046,'simple-calendar_settings_advanced','a:1:{s:6:\"assets\";a:1:{s:11:\"disable_css\";s:0:\"\";}}','yes'),(1049,'simple-calendar_version','3.1.9','yes'),(1120,'theme_mods_twentytwentyfive','a:1:{s:19:\"wp_classic_sidebars\";a:0:{}}','on'),(1068,'finished_splitting_shared_terms','1','yes'),(1069,'site_icon','0','yes'),(1070,'medium_large_size_w','768','yes'),(1071,'medium_large_size_h','0','yes'),(1078,'slideshow-jquery-image-gallery-updated-from-v2-1-20-to-v2-1-22','updated','no'),(1079,'slideshow-jquery-image-gallery-updated-from-v2-1-20-to-v2-1-23','updated','no'),(1080,'slideshow-jquery-image-gallery-updated-from-v2-1-23-to-v2-2-0','updated','no'),(1081,'slideshow-jquery-image-gallery-updated-from-v2-2-0-to-v2-2-8','updated','no'),(1082,'slideshow-jquery-image-gallery-updated-from-v2-2-8-to-v2-2-12','updated','no'),(1083,'slideshow-jquery-image-gallery-updated-from-v2-2-12-to-v2-2-16','updated','no'),(1084,'slideshow-jquery-image-gallery-updated-from-v2-2-16-to-v2-2-17','updated','no'),(1085,'slideshow-jquery-image-gallery-updated-from-v2-2-17-to-v2-2-20','updated','no'),(1086,'slideshow-jquery-image-gallery-plugin-version','2.3.1','yes'),(1087,'widget_slideshowwidget','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(1088,'widget_users_data_widget','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(1092,'widget_a2a_share_save_widget','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(1093,'widget_a2a_follow_widget','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(1096,'simple_local_avatars','a:2:{s:4:\"caps\";i:0;s:4:\"only\";i:0;}','yes'),(1099,'xtecweekblog_default_msg','Des de XTECBlocs, el professorat i els centres podeu crear tants blocs com necessiteu i convidar a l\'alumnat a participar-hi.','yes'),(1100,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1101,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1102,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1103,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1104,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1105,'widget_block','a:1:{s:12:\"_multiwidget\";i:1;}','auto'),(1107,'wp_page_for_privacy_policy','0','on'),(1108,'show_comments_cookies_opt_in','1','on'),(1109,'admin_email_lifespan','0','on'),(1110,'disallowed_keys','','off'),(1111,'comment_previously_approved','1','on'),(1112,'auto_plugin_theme_update_emails','a:0:{}','off'),(1113,'auto_update_core_dev','enabled','on'),(1114,'auto_update_core_minor','enabled','on'),(1115,'auto_update_core_major','unset','on'),(1116,'wp_force_deactivated_plugins','a:0:{}','off'),(1117,'wp_attachment_pages_enabled','1','on'),(1118,'wp_notes_notify','1','on');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_postmeta`
--

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default'),(2,1,'_oembed_9898809c08baff43cc22eefcdcdff8dc','<iframe class=\"scribd_iframe_embed\" src=\"https://www.scribd.com/embeds/34439974/content\" scrolling=\"no\" id=\"34439974\" width=\"500\" height=\"750\" frameborder=\"0\"></iframe><script type=\"text/javascript\">          (function() { var scribd = document.createElement(\"script\"); scribd.type = \"text/javascript\"; scribd.async = true; scribd.src = \"https://www.scribd.com/javascripts/embed_code/inject.js\"; var s = document.getElementsByTagName(\"script\")[0]; s.parentNode.insertBefore(scribd, s); })()        </script>'),(3,1,'_oembed_time_9898809c08baff43cc22eefcdcdff8dc','1424428104'),(25,12,'gce_list_max_num','7'),(26,12,'gce_list_max_length','days'),(29,12,'gce_feed_start_interval','months'),(31,12,'gce_feed_end_interval','years'),(44,12,'_edit_lock','1426070871:1'),(41,12,'_edit_last','1'),(73,12,'_calendar_version','3.0.0'),(53,12,'_calendar_view','a:1:{s:16:\"default-calendar\";s:4:\"grid\";}'),(54,12,'_default_calendar_list_range_type','daily'),(55,12,'_default_calendar_list_range_span','7'),(56,12,'_calendar_begins','today'),(57,12,'_feed_earliest_event_date','months_before'),(58,12,'_feed_earliest_event_date_range','1'),(59,12,'_feed_latest_event_date','years_after'),(60,12,'_feed_latest_event_date_range','2'),(61,12,'_default_calendar_event_bubble_trigger','hover'),(62,12,'_default_calendar_expand_multi_day_events','yes'),(63,12,'_google_calendar_id',''),(64,12,'_google_events_max_results','2500'),(65,12,'_google_events_recurring','show'),(66,12,'_calendar_date_format_setting','use_site'),(67,12,'_calendar_time_format_setting','use_site'),(68,12,'_calendar_datetime_separator','@'),(69,12,'_calendar_week_starts_on_setting','use_site'),(70,12,'_feed_cache_user_unit','3600'),(71,12,'_feed_cache_user_amount','12'),(72,12,'_feed_cache','43200');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_posts`
--

DROP TABLE IF EXISTS `wp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_posts` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_author` (`post_type`,`post_status`,`post_author`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_posts`
--

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;
INSERT INTO `wp_posts` VALUES (1,1,'2012-12-19 13:05:13','2012-12-19 13:05:13','Benvingut al WordPress. Aquest és el vostre primer article. Editeu-lo o suprimiu-lo ..... i comenceu a publicar!','Hola, món!','','publish','open','open','','hola-mon','','','2012-12-19 13:05:13','2012-12-19 13:05:13','',0,'http://agora/blocs/?p=1',0,'post','',0),(2,1,'2012-12-19 13:05:13','2012-12-19 13:05:13','Aquest és un exemple de pàgina. És diferent a un article perquè romandrà en un lloc i es mostrarà en la navegació del bloc (en la majoria d\'aparences). Molta gent comença amb una pàgina \"Quant a\" que els presenta als visitants potencials del bloc. Es podria dir quelcom així: \n<blockquote>Hola a tothom! Treballo de missatger de dia, sóc aspirant a actor de nit, i aquest és el meu bloc. Visc a Barcelona, tinc un gosa meravellosa que es diu Lluna, i m\'agraden les calçotades. (I quedar atrapat per la pluja.)</blockquote>\n\n... o quelcom així:\n\n<blockquote>La Companyia d\'Adobs XYZ es va fundar el 1971, i ha estat proporcionant adobs de qualitat des de llavors. Situada en Gotham City, XYZ dóna feina a més de 2,000 persones i fa tot tipus de meravelloses tasques per a la comunitat de Gotham.</blockquote>\n \n\n \nCom a usuari nou del WordPress, heu d\'anar al <a href=\"http://agora/blocs/wp-admin/\">tauler</a> a esborrar aquesta pàgina i crear pàgines noves amb el vostre contingut. Que es diverteixin!','Pàgina d\'exemple','','publish','open','open','','pagina-exemple','','','2012-12-19 13:05:13','2012-12-19 13:05:13','',0,'http://agora/blocs/?page_id=2',0,'page','',0),(12,1,'2015-03-11 10:50:12','2015-03-11 10:50:12','<div class=\"gce-list-event gce-tooltip-event\">[event-title]</div>\r\n[if-not-all-day]\r\n[if-single-day]<div><span>Quan:</span> [start-time]-[end-time]</div>[/if-single-day]\r\n[/if-not-all-day]\r\n[if-multi-day]<div>Del [start-date] fins al [end-date]</div>[/if-multi-day]\r\n[if-location]<div><span>On:</span> [location]</div>[/if-location]\r\n[if-description]<div>[description]</div>[/if-description]\r\n<div>[link newwindow=\"true\"]Més detalls...[/link]</div>\r\n','','','publish','closed','closed','','12','','','2015-03-11 10:50:12','2015-03-11 10:50:12','',0,'http://agora/blocs/?post_type=gce_feed&#038;p=12',0,'calendar','',0);
/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_registration_log`
--

DROP TABLE IF EXISTS `wp_registration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_registration_log` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `IP` varchar(30) NOT NULL DEFAULT '',
  `blog_id` bigint unsigned NOT NULL DEFAULT '0',
  `date_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `IP` (`IP`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_registration_log`
--

LOCK TABLES `wp_registration_log` WRITE;
/*!40000 ALTER TABLE `wp_registration_log` DISABLE KEYS */;
INSERT INTO `wp_registration_log` VALUES (1,'admin@blocs.xtec.cat','192.168.56.1',2,'2012-12-20 08:27:07'),(2,'admin@blocs.xtec.cat','192.168.56.1',3,'2012-12-20 08:51:03'),(3,'admin@blocs.xtec.cat','192.168.56.1',4,'2012-12-20 09:20:20'),(4,'admin@blocs.xtec.cat','192.168.56.1',5,'2012-12-20 11:12:27'),(5,'admin@blocs.xtec.cat','192.168.56.1',6,'2012-12-20 11:15:28'),(6,'admin@blocs.xtec.cat','192.168.56.1',7,'2015-02-25 08:51:20'),(7,'admin@blocs.xtec.cat','192.168.56.1',8,'2015-02-25 08:52:42'),(8,'admin@blocs.xtec.cat','192.168.56.1',9,'2015-02-25 09:00:23'),(9,'admin@blocs.xtec.cat','192.168.56.1',10,'2015-02-25 09:11:50'),(10,'admin@blocs.xtec.cat','192.168.56.1',11,'2015-02-25 09:31:25'),(11,'admin@blocs.xtec.cat','192.168.56.1',12,'2015-02-25 11:43:43'),(12,'admin@blocs.xtec.cat','192.168.56.1',13,'2015-02-26 14:01:11'),(13,'admin@blocs.xtec.cat','192.168.56.1',14,'2015-02-27 07:55:50'),(14,'admin@blocs.xtec.cat','192.168.56.1',15,'2015-02-27 07:57:47'),(15,'admin@blocs.xtec.cat','192.168.56.1',16,'2015-02-27 07:58:34'),(16,'admin@blocs.xtec.cat','192.168.56.1',19,'2015-03-02 12:52:52'),(17,'admin@blocs.xtec.cat','192.168.56.1',20,'2015-03-11 07:30:56'),(18,'admin@blocs.xtec.cat','192.168.56.1',21,'2015-03-11 07:33:14'),(19,'admin@blocs.xtec.cat','192.168.56.1',22,'2015-03-11 07:35:00'),(20,'admin@blocs.xtec.cat','192.168.56.1',23,'2015-03-11 07:40:31'),(21,'admin@blocs.xtec.cat','192.168.56.1',24,'2015-03-11 08:16:59'),(22,'admin@blocs.xtec.cat','192.168.56.1',25,'2015-03-11 08:17:54'),(23,'admin@blocs.xtec.cat','192.168.56.1',26,'2015-03-11 08:31:52'),(24,'admin@blocs.xtec.cat','192.168.56.1',27,'2015-03-11 08:36:25'),(25,'admin@blocs.xtec.cat','192.168.56.1',28,'2015-03-11 08:39:23'),(26,'admin@blocs.xtec.cat','192.168.56.1',29,'2015-03-11 08:57:32'),(27,'admin@blocs.xtec.cat','192.168.56.1',30,'2015-03-11 09:06:26'),(28,'admin@blocs.xtec.cat','192.168.56.1',31,'2015-03-12 08:41:30'),(29,'admin@blocs.xtec.cat','192.168.56.1',32,'2015-03-13 12:11:24'),(30,'admin@blocs.xtec.cat','192.168.56.1',33,'2015-03-13 12:13:23'),(31,'admin@blocs.xtec.cat','192.168.56.1',34,'2015-03-13 12:16:55'),(32,'admin@blocs.xtec.cat','192.168.56.1',35,'2015-03-13 12:17:40'),(33,'admin@blocs.xtec.cat','192.168.56.1',36,'2015-03-13 12:25:45'),(34,'admin@blocs.xtec.cat','192.168.56.1',37,'2015-03-17 08:35:41'),(35,'admin@blocs.xtec.cat','192.168.56.1',38,'2015-03-17 08:38:38'),(36,'admin@blocs.xtec.cat','192.168.56.1',39,'2015-03-17 08:44:06'),(37,'admin@blocs.xtec.cat','192.168.56.1',40,'2015-03-17 08:48:49'),(38,'admin@blocs.xtec.cat','192.168.56.1',41,'2015-03-17 08:50:23'),(39,'admin@blocs.xtec.cat','192.168.56.1',42,'2015-03-17 08:53:28'),(40,'admin@blocs.xtec.cat','192.168.56.1',43,'2015-03-17 08:55:47'),(41,'admin@blocs.xtec.cat','192.168.56.1',45,'2015-03-17 09:12:23'),(42,'admin@blocs.xtec.cat','192.168.56.1',46,'2015-03-17 09:14:03'),(43,'admin@blocs.xtec.cat','192.168.56.1',47,'2015-03-17 09:17:21'),(44,'admin@blocs.xtec.cat','192.168.56.1',48,'2015-03-17 09:18:15'),(45,'admin@blocs.xtec.cat','192.168.56.1',49,'2015-03-17 09:19:57'),(46,'admin@blocs.xtec.cat','192.168.56.1',50,'2015-03-17 09:40:43'),(47,'admin@blocs.xtec.cat','192.168.56.1',51,'2015-03-19 09:35:06'),(48,'admin@blocs.xtec.cat','192.168.56.1',52,'2015-04-02 10:18:15'),(49,'admin@blocs.xtec.cat','192.168.56.1',53,'2015-04-13 14:22:41'),(50,'admin@blocs.xtec.cat','192.168.56.1',54,'2015-04-13 14:33:02'),(51,'admin@blocs.xtec.cat','192.168.56.1',55,'2015-04-13 14:50:13'),(52,'admin@blocs.xtec.cat','192.168.56.1',56,'2015-04-17 11:49:17');
/*!40000 ALTER TABLE `wp_registration_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_request_types`
--

DROP TABLE IF EXISTS `wp_request_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_request_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `state` tinyint NOT NULL DEFAULT '1',
  `name` varchar(200) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `comments_text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_request_types`
--

LOCK TABLES `wp_request_types` WRITE;
/*!40000 ALTER TABLE `wp_request_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_request_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_requests`
--

DROP TABLE IF EXISTS `wp_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `state` tinyint NOT NULL DEFAULT '1',
  `blog_id` int NOT NULL DEFAULT '0',
  `request_type_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `time_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_edition` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  `response` text NOT NULL,
  `priv_notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `blog_id` (`blog_id`),
  KEY `request_type_id` (`request_type_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_requests`
--

LOCK TABLES `wp_requests` WRITE;
/*!40000 ALTER TABLE `wp_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_search`
--

DROP TABLE IF EXISTS `wp_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_search` (
  `blogid` int NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`blogid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_search`
--

LOCK TABLES `wp_search` WRITE;
/*!40000 ALTER TABLE `wp_search` DISABLE KEYS */;
INSERT INTO `wp_search` VALUES (4,'Bloc dels cargols','Un altre bloc XTEC Blocs ','agora','/elscargols/'),(5,'Bloc de les tortugues','Un altre bloc XTEC Blocs','agora','/lestortugues/'),(6,'Blocs dels pingüins','Un altre bloc XTEC Blocs ','agora','/elspinguins/');
/*!40000 ALTER TABLE `wp_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_signups`
--

DROP TABLE IF EXISTS `wp_signups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_signups` (
  `signup_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `title` longtext NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_key` varchar(50) NOT NULL DEFAULT '',
  `meta` longtext,
  PRIMARY KEY (`signup_id`),
  KEY `activation_key` (`activation_key`),
  KEY `user_email` (`user_email`),
  KEY `user_login_email` (`user_login`,`user_email`),
  KEY `domain_path` (`domain`,`path`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_signups`
--

LOCK TABLES `wp_signups` WRITE;
/*!40000 ALTER TABLE `wp_signups` DISABLE KEYS */;
INSERT INTO `wp_signups` VALUES (1,'','','','prova1','saved1_3@hotmail.com','2015-02-20 10:08:35','0000-00-00 00:00:00',0,'2fd536305e1ec008','a:2:{s:11:\"add_to_blog\";s:1:\"3\";s:8:\"new_role\";s:10:\"subscriber\";}'),(2,'','','','nacho','ignacio.benito.abejaro@upcnet.es','2015-03-13 11:24:53','0000-00-00 00:00:00',0,'b04784f68a359dac','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(4,'','','','victor','Saved1.3@gmail.com','2015-03-27 07:41:41','0000-00-00 00:00:00',0,'adad29c41b16ed92','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(5,'','','','victore','Saved1.3@gmail.com','2015-03-27 07:41:51','0000-00-00 00:00:00',0,'2f7a33cfe34fbba8','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(6,'','','','victore','Saved1.3@gmail.com','2015-03-27 07:42:13','0000-00-00 00:00:00',0,'05807348a80e6ae5','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(7,'','','','victore','Saved1.3@gmail.com','2015-03-27 07:42:26','0000-00-00 00:00:00',0,'da330ee3750cb638','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(8,'','','','victore','Saved1.3@gmail.com','2015-03-27 07:42:43','0000-00-00 00:00:00',0,'e64debbde9969568','a:2:{s:11:\"add_to_blog\";s:1:\"1\";s:8:\"new_role\";s:10:\"subscriber\";}'),(9,'','','','est_colex','est_colex@blocs.xtec.cat','2015-04-20 12:17:51','0000-00-00 00:00:00',0,'e96073fab7f0c33c','a:2:{s:11:\"add_to_blog\";s:1:\"3\";s:8:\"new_role\";s:10:\"subscriber\";}');
/*!40000 ALTER TABLE `wp_signups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_site`
--

DROP TABLE IF EXISTS `wp_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_site` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`,`path`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_site`
--

LOCK TABLES `wp_site` WRITE;
/*!40000 ALTER TABLE `wp_site` DISABLE KEYS */;
INSERT INTO `wp_site` VALUES (1,'agora','/');
/*!40000 ALTER TABLE `wp_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_sitemeta`
--

DROP TABLE IF EXISTS `wp_sitemeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_sitemeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `meta_key` (`meta_key`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=679 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_sitemeta`
--

LOCK TABLES `wp_sitemeta` WRITE;
/*!40000 ALTER TABLE `wp_sitemeta` DISABLE KEYS */;
INSERT INTO `wp_sitemeta` VALUES (1,1,'site_name','XTECBlocs'),(2,1,'admin_email','admin@blocs.xtec.cat'),(3,1,'admin_user_id','1'),(4,1,'registration','blog'),(5,1,'upload_filetypes','jpg jpeg png gif mp3 mov avi wmv midi mid pdf css odt doc docx ppt pptx swf flv'),(6,1,'blog_upload_space','70'),(7,1,'fileupload_maxk','4000'),(8,1,'site_admins','a:1:{i:0;s:5:\"admin\";}'),(9,1,'allowedthemes','a:14:{s:18:\"classic-chalkboard\";b:1;s:8:\"delicacy\";b:1;s:7:\"freshy2\";b:1;s:8:\"mystique\";b:1;s:6:\"reddle\";b:1;s:12:\"twentyeleven\";b:1;s:9:\"twentyten\";b:1;s:12:\"twentytwelve\";b:1;s:9:\"xtec-v1.1\";b:1;s:8:\"fukasawa\";b:1;s:13:\"twentyfifteen\";b:1;s:14:\"twentyfourteen\";b:1;s:13:\"twentysixteen\";b:1;s:14:\"twentythirteen\";b:1;}'),(10,1,'illegal_names','a:62:{i:0;s:3:\"www\";i:1;s:3:\"web\";i:2;s:4:\"root\";i:3;s:5:\"admin\";i:4;s:4:\"main\";i:5;s:6:\"invite\";i:6;s:13:\"administrator\";i:7;s:12:\"Audiovisuals\";i:8;s:4:\"xtec\";i:9;s:10:\"xtecràdio\";i:10;s:10:\"xteccinema\";i:11;s:6:\"cinema\";i:12;s:4:\"clic\";i:13;s:5:\"jclic\";i:14;s:2:\"qv\";i:15;s:8:\"quaderns\";i:16;s:16:\"quadernsvirtuals\";i:17;s:7:\"quadern\";i:18;s:14:\"quadernvirtual\";i:19;s:5:\"colex\";i:20;s:6:\"collex\";i:21;s:8:\"intraweb\";i:22;s:8:\"intranet\";i:23;s:3:\"web\";i:24;s:6:\"linkat\";i:25;s:6:\"lincat\";i:26;s:5:\"linux\";i:27;s:8:\"gnulinux\";i:28;s:4:\"bloc\";i:29;s:5:\"blocs\";i:30;s:4:\"blog\";i:31;s:5:\"blogs\";i:32;s:5:\"forum\";i:33;s:6:\"forums\";i:34;s:6:\"moodle\";i:35;s:5:\"agora\";i:36;s:10:\"xtecmoodle\";i:37;s:9:\"xtecblocs\";i:38;s:9:\"xtecforum\";i:39;s:6:\"edu365\";i:40;s:4:\"smav\";i:41;s:9:\"videoteca\";i:42;s:3:\"tic\";i:43;s:3:\"tac\";i:44;s:7:\"diedrom\";i:45;s:7:\"isodrom\";i:46;s:12:\"xtecwebquest\";i:47;s:7:\"caspian\";i:48;s:15:\"caspianlearning\";i:49;s:6:\"slxtec\";i:50;s:16:\"gustperlaparaula\";i:51;s:5:\"agora\";i:52;s:12:\"prestatgeria\";i:53;s:15:\"catala2allengua\";i:54;s:5:\"gepse\";i:55;s:11:\"manteniment\";i:56;s:6:\"suport\";i:57;s:5:\"heura\";i:58;s:10:\"equipament\";i:59;s:14:\"paisoscatalans\";i:60;s:16:\"dominilinguistic\";i:61;s:14:\"blocs_formacio\";}'),(11,1,'wpmu_upgrade_site','61833'),(12,1,'welcome_email','Blocaire,\r\n\r\nEl nou bloc SITE_NAME s\'ha configurat correctament en:\r\nBLOG_URL\r\n\r\nPodeu iniciar sessió en el compte d\'administrador amb la següent informació:\r\nNom d\'usuari: USERNAME\r\nContrasenya: PASSWORD\r\nEntreu aquí: BLOG_URLwp-login.php\r\n\r\nEsperem que gaudiu del vostre nou bloc.\r\nGràcies!\r\n\r\n--L\'equip @ SITE_NAME'),(13,1,'first_post','Benvingut a <a href=\"SITE_URL\">SITE_NAME</a>. Aquest és el vostre primer article. Editeu-lo o suprimiu-lo, aleshores comenceu a publicar!'),(14,1,'siteurl','https://blocs-aws.xtec.cat/'),(15,1,'add_new_users','0'),(16,1,'upload_space_check_disabled','0'),(17,1,'subdomain_install','0'),(18,1,'global_terms_enabled','0'),(576,1,'bwp_capt_general','a:18:{s:12:\"input_pubkey\";s:40:\"6LdeRAUTAAAAAElOIZz-mWS21zDs6pe43Uhg4Btg\";s:12:\"input_prikey\";s:40:\"6LdeRAUTAAAAADdO3-Odt7C097AzBOMHGO1I6zeL\";s:11:\"input_error\";s:80:\"<strong>ERROR:</strong> Incorrect or empty reCAPTCHA response, please try again.\";s:10:\"input_back\";s:127:\"Error: Incorrect or empty reCAPTCHA response, please click the back button on your browser\'s toolbar or click on %s to go back.\";s:14:\"input_approved\";s:1:\"1\";s:14:\"enable_comment\";s:3:\"yes\";s:19:\"enable_registration\";s:0:\"\";s:12:\"enable_login\";s:0:\"\";s:14:\"enable_akismet\";s:0:\"\";s:15:\"use_global_keys\";s:3:\"yes\";s:10:\"select_cap\";s:14:\"manage_options\";s:14:\"select_cf7_tag\";s:9:\"recaptcha\";s:15:\"select_response\";s:8:\"redirect\";s:15:\"select_position\";s:19:\"after_comment_field\";s:20:\"select_akismet_react\";s:4:\"hold\";s:15:\"hide_registered\";s:3:\"yes\";s:8:\"hide_cap\";s:0:\"\";s:13:\"hide_approved\";s:0:\"\";}'),(363,1,'secure_auth_key','4=S1AH}CYV-to!af9q)N@(ag=@jO;YDom,3#qr^KCgIFAZIXsV@9-J*1j]x XH4+'),(364,1,'secure_auth_salt','pA~0{ke5_WMQi>ti:vYbxlu-}[r|e/BDJ+G4]SIs&gyXW~>0$_n>Lj7={NH>Lw)v'),(365,1,'logged_in_key','IodwAlt~[O7(CLXsJ9.q</vK7T_#O`)j&blr{I9,~H(&#y|HKQRpdw12u>K((kR>'),(74,1,'auto_core_update_notified','a:4:{s:4:\"type\";s:6:\"manual\";s:5:\"email\";s:20:\"admin@blocs.xtec.cat\";s:7:\"version\";s:5:\"4.0.1\";s:9:\"timestamp\";i:1424257160;}'),(673,1,'_site_transient_timeout_wp_theme_files_patterns-f138d15633b1422f4aec0398aebfceeb','1790763883'),(674,1,'_site_transient_wp_theme_files_patterns-f138d15633b1422f4aec0398aebfceeb','a:2:{s:7:\"version\";b:0;s:8:\"patterns\";a:0:{}}'),(366,1,'logged_in_salt','IY(w@%y6uTpAThY9~;/x=IjGrv)^f(YnAH5)a7HoHu5mDLCwYdmx[H>#<(PITZ;>'),(367,1,'nonce_key','CO67QeHfvTK3o`8ioxAmy[b/J;dAp#$aR*D0X}AqundITH _T&jfK=_JkdW7i~@R'),(368,1,'nonce_salt','Zy3dj(E?Rt($d;55WR:<QfFl3vNU>enu=;Lr7@ Sa9<!2[t0N,UZU_x9?zaxKExp'),(24,1,'blog_count','4'),(25,1,'user_count','2'),(26,1,'can_compress_scripts','1'),(47,1,'initial_db_version','17516'),(31,1,'active_sitewide_plugins','a:30:{s:37:\"blogger-importer/blogger-importer.php\";i:1389360202;s:43:\"google-analyticator/google-analyticator.php\";i:1389360204;s:29:\"link-manager/link-manager.php\";i:1389360209;s:43:\"multisite-plugin-manager/plugin-manager.php\";i:1389360211;s:25:\"slideshare/slideshare.php\";i:1389360218;s:37:\"tinymce-advanced/tinymce-advanced.php\";i:1389360220;s:49:\"vipers-video-quicktags/vipers-video-quicktags.php\";i:1389360222;s:41:\"wordpress-importer/wordpress-importer.php\";i:1389360225;s:27:\"wp-super-cache/wp-cache.php\";i:1389360229;s:21:\"xtec-api/xtec-api.php\";i:1389360231;s:37:\"xtec-descriptors/xtec-descriptors.php\";i:1389360233;s:33:\"xtec-favorites/xtec-favorites.php\";i:1389360236;s:41:\"xtec-lastest-posts/xtec-lastest-posts.php\";i:1389360238;s:35:\"xtec-ldap-login/xtec-ldap-login.php\";i:1389360240;s:37:\"xtec-link-player/xtec-link-player.php\";i:1389360242;s:23:\"xtec-mail/xtec-mail.php\";i:1389360244;s:37:\"xtec-maintenance/xtec-maintenance.php\";i:1389360246;s:31:\"xtec-settings/xtec-settings.php\";i:1389360250;s:27:\"xtec-signup/xtec-signup.php\";i:1389360252;s:25:\"xtec-users/xtec-users.php\";i:1389360254;s:45:\"simple-local-avatars/simple-local-avatars.php\";i:1426749718;s:29:\"wp-recaptcha/wp-recaptcha.php\";i:1429014106;s:23:\"anti-spam/anti-spam.php\";i:1429023307;s:49:\"google-calendar-events/google-calendar-events.php\";i:1489494617;s:57:\"multisite-clone-duplicator/multisite-clone-duplicator.php\";i:1489494637;s:34:\"scribd-doc-embedder/scribd_doc.php\";i:1489494653;s:44:\"slideshow-jquery-image-gallery/slideshow.php\";i:1489494708;s:41:\"wordpress-php-info/wordpress-php-info.php\";i:1489494734;s:35:\"xtec-ms-manager/xtec-ms-manager.php\";i:1489494744;s:47:\"xtec-widget-data-users/xtec-class-data-user.php\";i:1489494763;}'),(32,1,'recaptcha_options','a:14:{s:10:\"public_key\";s:40:\"6LdYmdoSAAAAAJI5whFCwEiXBik7H6CwBMptVJ1O\";s:11:\"private_key\";s:40:\"6LdYmdoSAAAAAIgSS-jRH-UB65b1YdWAIwlk-VZk\";s:16:\"show_in_comments\";i:1;s:27:\"bypass_for_registered_users\";i:1;s:20:\"minimum_bypass_level\";s:4:\"read\";s:14:\"comments_theme\";s:3:\"red\";s:18:\"comments_tab_index\";s:1:\"5\";s:20:\"show_in_registration\";i:1;s:18:\"registration_theme\";s:3:\"red\";s:22:\"registration_tab_index\";s:2:\"30\";s:18:\"recaptcha_language\";s:2:\"es\";s:16:\"xhtml_compliance\";i:0;s:17:\"no_response_error\";s:58:\"<strong>ERROR</strong>: Please fill in the reCAPTCHA form.\";s:24:\"incorrect_response_error\";s:62:\"<strong>ERROR</strong>: That reCAPTCHA response was incorrect.\";}'),(53,1,'pm_user_control_list','a:1:{i:0;s:42:\"wordpress-social-login/wp-social-login.php\";}'),(54,1,'pm_auto_activate_list','a:1:{i:0;s:25:\"add-to-any/add-to-any.php\";}'),(675,1,'_site_transient_timeout_theme_roots','1790763881'),(676,1,'_site_transient_theme_roots','a:4:{s:16:\"twentytwentyfive\";s:7:\"/themes\";s:16:\"twentytwentyfour\";s:7:\"/themes\";s:17:\"twentytwentythree\";s:7:\"/themes\";s:15:\"twentytwentytwo\";s:7:\"/themes\";}'),(677,1,'_site_transient_timeout_wp_theme_files_patterns-52550485e0af39d79b13c3eb07cdbd4d','1790763882'),(678,1,'_site_transient_wp_theme_files_patterns-52550485e0af39d79b13c3eb07cdbd4d','a:2:{s:7:\"version\";s:3:\"1.5\";s:8:\"patterns\";a:98:{s:21:\"banner-about-book.php\";a:4:{s:5:\"title\";s:28:\"Banner with book description\";s:4:\"slug\";s:34:\"twentytwentyfive/banner-about-book\";s:11:\"description\";s:66:\"Banner with book description and accompanying image for promotion.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:28:\"banner-cover-big-heading.php\";a:4:{s:5:\"title\";s:22:\"Cover with big heading\";s:4:\"slug\";s:41:\"twentytwentyfive/banner-cover-big-heading\";s:11:\"description\";s:82:\"A full-width cover section with a large background image and an oversized heading.\";s:10:\"categories\";a:3:{i:0;s:6:\"banner\";i:1;s:5:\"about\";i:2;s:8:\"featured\";}}s:22:\"banner-intro-image.php\";a:4:{s:5:\"title\";s:49:\"Short heading and paragraph and image on the left\";s:4:\"slug\";s:35:\"twentytwentyfive/banner-intro-image\";s:11:\"description\";s:68:\"A Intro pattern with Short heading, paragraph and image on the left.\";s:10:\"categories\";a:2:{i:0;s:6:\"banner\";i:1;s:8:\"featured\";}}s:16:\"banner-intro.php\";a:4:{s:5:\"title\";s:35:\"Intro with left-aligned description\";s:4:\"slug\";s:29:\"twentytwentyfive/banner-intro\";s:11:\"description\";s:66:\"A large left-aligned heading with a brand name emphasized in bold.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:17:\"banner-poster.php\";a:4:{s:5:\"title\";s:19:\"Poster-like section\";s:4:\"slug\";s:30:\"twentytwentyfive/banner-poster\";s:11:\"description\";s:78:\"A section that can be used as a banner or a landing page to announce an event.\";s:10:\"categories\";a:2:{i:0;s:6:\"banner\";i:1;s:5:\"media\";}}s:43:\"banner-with-description-and-images-grid.php\";a:4:{s:5:\"title\";s:39:\"Banner with description and images grid\";s:4:\"slug\";s:47:\"twentytwentyfive/banner-description-images-grid\";s:11:\"description\";s:75:\"A banner with a short paragraph, and two images displayed in a grid layout.\";s:10:\"categories\";a:2:{i:0;s:6:\"banner\";i:1;s:8:\"featured\";}}s:18:\"binding-format.php\";a:4:{s:5:\"title\";s:16:\"Post format name\";s:4:\"slug\";s:31:\"twentytwentyfive/binding-format\";s:11:\"description\";s:75:\"Prints the name of the post format with the help of the Block Bindings API.\";s:10:\"categories\";a:1:{i:0;s:28:\"twentytwentyfive_post-format\";}}s:12:\"comments.php\";a:5:{s:5:\"title\";s:8:\"Comments\";s:4:\"slug\";s:25:\"twentytwentyfive/comments\";s:11:\"description\";s:63:\"Comments area with comments list, pagination, and comment form.\";s:10:\"categories\";a:1:{i:0;s:4:\"text\";}s:10:\"blockTypes\";a:1:{i:0;s:13:\"core/comments\";}}s:32:\"contact-centered-social-link.php\";a:5:{s:5:\"title\";s:30:\"Centered link and social links\";s:4:\"slug\";s:45:\"twentytwentyfive/contact-centered-social-link\";s:11:\"description\";s:73:\"Centered contact section with a prominent message and social media links.\";s:10:\"categories\";a:1:{i:0;s:7:\"contact\";}s:8:\"keywords\";a:3:{i:0;s:7:\"contact\";i:1;s:3:\"faq\";i:2;s:9:\"questions\";}}s:26:\"contact-info-locations.php\";a:6:{s:5:\"title\";s:27:\"Contact, info and locations\";s:4:\"slug\";s:39:\"twentytwentyfive/contact-info-locations\";s:11:\"description\";s:78:\"Contact section with social media links, email, and multiple location details.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:7:\"contact\";}s:8:\"keywords\";a:2:{i:0;s:7:\"contact\";i:1;s:8:\"location\";}}s:29:\"contact-location-and-link.php\";a:4:{s:5:\"title\";s:25:\"Contact location and link\";s:4:\"slug\";s:42:\"twentytwentyfive/contact-location-and-link\";s:11:\"description\";s:89:\"Contact section with a location address, a directions link, and an image of the location.\";s:10:\"categories\";a:2:{i:0;s:7:\"contact\";i:1;s:8:\"featured\";}}s:18:\"cta-book-links.php\";a:4:{s:5:\"title\";s:30:\"Call to action with book links\";s:4:\"slug\";s:31:\"twentytwentyfive/cta-book-links\";s:11:\"description\";s:74:\"A call to action section with links to get the book in different websites.\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:22:\"cta-book-locations.php\";a:4:{s:5:\"title\";s:29:\"Call to action with locations\";s:4:\"slug\";s:35:\"twentytwentyfive/cta-book-locations\";s:11:\"description\";s:82:\"A call to action section with links to get the book in the most popular locations.\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:24:\"cta-centered-heading.php\";a:4:{s:5:\"title\";s:16:\"Centered heading\";s:4:\"slug\";s:37:\"twentytwentyfive/cta-centered-heading\";s:11:\"description\";s:53:\"A hero with a centered heading, paragraph and button.\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:19:\"cta-events-list.php\";a:4:{s:5:\"title\";s:11:\"Events list\";s:4:\"slug\";s:32:\"twentytwentyfive/cta-events-list\";s:11:\"description\";s:37:\"A list of events with call to action.\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:26:\"cta-grid-products-link.php\";a:5:{s:5:\"title\";s:54:\"Call to action with grid layout with products and link\";s:4:\"slug\";s:39:\"twentytwentyfive/cta-grid-products-link\";s:11:\"description\";s:42:\"A call to action featuring product images.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:14:\"call-to-action\";i:1;s:8:\"featured\";}}s:22:\"cta-heading-search.php\";a:4:{s:5:\"title\";s:23:\"Heading and search form\";s:4:\"slug\";s:35:\"twentytwentyfive/cta-heading-search\";s:11:\"description\";s:54:\"Large heading with a search form for quick navigation.\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:18:\"cta-newsletter.php\";a:5:{s:5:\"title\";s:18:\"Newsletter sign-up\";s:4:\"slug\";s:31:\"twentytwentyfive/cta-newsletter\";s:11:\"description\";s:0:\"\";s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}s:8:\"keywords\";a:2:{i:0;s:14:\"call-to-action\";i:1;s:10:\"newsletter\";}}s:15:\"event-3-col.php\";a:5:{s:5:\"title\";s:46:\"Events, 3 columns with event images and titles\";s:4:\"slug\";s:28:\"twentytwentyfive/event-3-col\";s:11:\"description\";s:95:\"A header with title and text and three columns that show 3 events with their images and titles.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}s:8:\"keywords\";a:3:{i:0;s:6:\"events\";i:1;s:7:\"columns\";i:2;s:6:\"images\";}}s:14:\"event-rsvp.php\";a:7:{s:5:\"title\";s:10:\"Event RSVP\";s:4:\"slug\";s:27:\"twentytwentyfive/event-rsvp\";s:11:\"description\";s:64:\"RSVP for an upcoming event with a cover image and event details.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}s:8:\"keywords\";a:3:{i:0;s:14:\"call-to-action\";i:1;s:4:\"rsvp\";i:2;s:5:\"event\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}}s:18:\"event-schedule.php\";a:5:{s:5:\"title\";s:14:\"Event schedule\";s:4:\"slug\";s:31:\"twentytwentyfive/event-schedule\";s:11:\"description\";s:54:\"A section with specified dates and times for an event.\";s:10:\"categories\";a:1:{i:0;s:5:\"about\";}s:8:\"keywords\";a:4:{i:0;s:6:\"events\";i:1;s:6:\"agenda\";i:2;s:8:\"schedule\";i:3;s:8:\"lectures\";}}s:19:\"footer-centered.php\";a:5:{s:5:\"title\";s:15:\"Centered footer\";s:4:\"slug\";s:32:\"twentytwentyfive/footer-centered\";s:11:\"description\";s:44:\"Footer with centered site title and tagline.\";s:10:\"categories\";a:1:{i:0;s:6:\"footer\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/footer\";}}s:18:\"footer-columns.php\";a:5:{s:5:\"title\";s:19:\"Footer with columns\";s:4:\"slug\";s:31:\"twentytwentyfive/footer-columns\";s:11:\"description\";s:45:\"Footer columns with title, tagline and links.\";s:10:\"categories\";a:1:{i:0;s:6:\"footer\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/footer\";}}s:21:\"footer-newsletter.php\";a:5:{s:5:\"title\";s:29:\"Footer with newsletter signup\";s:4:\"slug\";s:34:\"twentytwentyfive/footer-newsletter\";s:11:\"description\";s:51:\"Footer with large site title and newsletter signup.\";s:10:\"categories\";a:1:{i:0;s:6:\"footer\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/footer\";}}s:17:\"footer-social.php\";a:5:{s:5:\"title\";s:33:\"Centered footer with social links\";s:4:\"slug\";s:30:\"twentytwentyfive/footer-social\";s:11:\"description\";s:49:\"Footer with centered site title and social links.\";s:10:\"categories\";a:1:{i:0;s:6:\"footer\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/footer\";}}s:10:\"footer.php\";a:5:{s:5:\"title\";s:6:\"Footer\";s:4:\"slug\";s:23:\"twentytwentyfive/footer\";s:11:\"description\";s:51:\"Footer columns with logo, title, tagline and links.\";s:10:\"categories\";a:1:{i:0;s:6:\"footer\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/footer\";}}s:16:\"format-audio.php\";a:4:{s:5:\"title\";s:12:\"Audio format\";s:4:\"slug\";s:29:\"twentytwentyfive/format-audio\";s:11:\"description\";s:73:\"An audio post format with an image, title, audio player, and description.\";s:10:\"categories\";a:1:{i:0;s:28:\"twentytwentyfive_post-format\";}}s:15:\"format-link.php\";a:4:{s:5:\"title\";s:11:\"Link format\";s:4:\"slug\";s:28:\"twentytwentyfive/format-link\";s:11:\"description\";s:77:\"A link post format with a description and an emphasized link for key content.\";s:10:\"categories\";a:1:{i:0;s:28:\"twentytwentyfive_post-format\";}}s:15:\"grid-videos.php\";a:4:{s:5:\"title\";s:16:\"Grid with videos\";s:4:\"slug\";s:28:\"twentytwentyfive/grid-videos\";s:11:\"description\";s:19:\"A grid with videos.\";s:10:\"categories\";a:1:{i:0;s:5:\"about\";}}s:24:\"grid-with-categories.php\";a:5:{s:5:\"title\";s:20:\"Grid with categories\";s:4:\"slug\";s:37:\"twentytwentyfive/grid-with-categories\";s:11:\"description\";s:41:\"A grid section with different categories.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:19:\"header-centered.php\";a:5:{s:5:\"title\";s:20:\"Centered site header\";s:4:\"slug\";s:32:\"twentytwentyfive/header-centered\";s:11:\"description\";s:52:\"Site header with centered site title and navigation.\";s:10:\"categories\";a:1:{i:0;s:6:\"header\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/header\";}}s:18:\"header-columns.php\";a:5:{s:5:\"title\";s:19:\"Header with columns\";s:4:\"slug\";s:31:\"twentytwentyfive/header-columns\";s:11:\"description\";s:54:\"Site header with site title and navigation in columns.\";s:10:\"categories\";a:1:{i:0;s:6:\"header\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/header\";}}s:22:\"header-large-title.php\";a:5:{s:5:\"title\";s:23:\"Header with large title\";s:4:\"slug\";s:35:\"twentytwentyfive/header-large-title\";s:11:\"description\";s:63:\"Site header with large site title and right-aligned navigation.\";s:10:\"categories\";a:1:{i:0;s:6:\"header\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/header\";}}s:10:\"header.php\";a:5:{s:5:\"title\";s:6:\"Header\";s:4:\"slug\";s:23:\"twentytwentyfive/header\";s:11:\"description\";s:43:\"Site header with site title and navigation.\";s:10:\"categories\";a:1:{i:0;s:6:\"header\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/template-part/header\";}}s:36:\"heading-and-paragraph-with-image.php\";a:4:{s:5:\"title\";s:45:\"Heading and paragraph with image on the right\";s:4:\"slug\";s:49:\"twentytwentyfive/heading-and-paragraph-with-image\";s:11:\"description\";s:89:\"A two-column section with a heading and paragraph on the left, and an image on the right.\";s:10:\"categories\";a:1:{i:0;s:5:\"about\";}}s:13:\"hero-book.php\";a:5:{s:5:\"title\";s:9:\"Hero book\";s:4:\"slug\";s:26:\"twentytwentyfive/hero-book\";s:11:\"description\";s:66:\"A hero section for the book with a description and pre-order link.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}s:8:\"keywords\";a:3:{i:0;s:7:\"podcast\";i:1;s:4:\"hero\";i:2;s:7:\"stories\";}}s:25:\"hero-full-width-image.php\";a:4:{s:5:\"title\";s:22:\"Hero, full width image\";s:4:\"slug\";s:38:\"twentytwentyfive/hero-full-width-image\";s:11:\"description\";s:68:\"A hero with a full width image, heading, short paragraph and button.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:41:\"hero-overlapped-book-cover-with-links.php\";a:4:{s:5:\"title\";s:38:\"Hero, overlapped book cover with links\";s:4:\"slug\";s:54:\"twentytwentyfive/hero-overlapped-book-cover-with-links\";s:11:\"description\";s:47:\"A hero with an overlapped book cover and links.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:16:\"hero-podcast.php\";a:5:{s:5:\"title\";s:12:\"Hero podcast\";s:4:\"slug\";s:29:\"twentytwentyfive/hero-podcast\";s:11:\"description\";s:0:\"\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}s:8:\"keywords\";a:3:{i:0;s:7:\"podcast\";i:1;s:4:\"hero\";i:2;s:7:\"stories\";}}s:14:\"hidden-404.php\";a:4:{s:5:\"title\";s:3:\"404\";s:4:\"slug\";s:27:\"twentytwentyfive/hidden-404\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:23:\"hidden-blog-heading.php\";a:4:{s:5:\"title\";s:19:\"Hidden blog heading\";s:4:\"slug\";s:36:\"twentytwentyfive/hidden-blog-heading\";s:11:\"description\";s:52:\"Hidden heading for the home page and index template.\";s:8:\"inserter\";b:0;}s:17:\"hidden-search.php\";a:4:{s:5:\"title\";s:6:\"Search\";s:4:\"slug\";s:30:\"twentytwentyfive/hidden-search\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:18:\"hidden-sidebar.php\";a:4:{s:5:\"title\";s:7:\"Sidebar\";s:4:\"slug\";s:31:\"twentytwentyfive/hidden-sidebar\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:21:\"hidden-written-by.php\";a:4:{s:5:\"title\";s:10:\"Written by\";s:4:\"slug\";s:34:\"twentytwentyfive/hidden-written-by\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:9:\"logos.php\";a:4:{s:5:\"title\";s:5:\"Logos\";s:4:\"slug\";s:22:\"twentytwentyfive/logos\";s:11:\"description\";s:77:\"Showcasing the podcast\'s clients with a heading and a series of client logos.\";s:10:\"categories\";a:1:{i:0;s:6:\"banner\";}}s:24:\"media-instagram-grid.php\";a:5:{s:5:\"title\";s:14:\"Instagram grid\";s:4:\"slug\";s:37:\"twentytwentyfive/media-instagram-grid\";s:11:\"description\";s:62:\"A grid section with photos and a link to an Instagram profile.\";s:13:\"viewportWidth\";i:1440;s:10:\"categories\";a:3:{i:0;s:5:\"media\";i:1;s:7:\"gallery\";i:2;s:8:\"featured\";}}s:14:\"more-posts.php\";a:5:{s:5:\"title\";s:10:\"More posts\";s:4:\"slug\";s:27:\"twentytwentyfive/more-posts\";s:11:\"description\";s:45:\"Displays a list of posts with title and date.\";s:10:\"categories\";a:1:{i:0;s:5:\"query\";}s:10:\"blockTypes\";a:1:{i:0;s:10:\"core/query\";}}s:21:\"overlapped-images.php\";a:4:{s:5:\"title\";s:41:\"Overlapping images and paragraph on right\";s:4:\"slug\";s:34:\"twentytwentyfive/overlapped-images\";s:11:\"description\";s:53:\"A section with overlapping images, and a description.\";s:10:\"categories\";a:2:{i:0;s:5:\"about\";i:1;s:8:\"featured\";}}s:22:\"page-business-home.php\";a:8:{s:5:\"title\";s:17:\"Business homepage\";s:4:\"slug\";s:35:\"twentytwentyfive/page-business-home\";s:11:\"description\";s:28:\"A business homepage pattern.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:20:\"page-coming-soon.php\";a:8:{s:5:\"title\";s:11:\"Coming soon\";s:4:\"slug\";s:33:\"twentytwentyfive/page-coming-soon\";s:11:\"description\";s:96:\"A full-width cover banner that can be applied to a page or it can work as a single landing page.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:15:\"page-cv-bio.php\";a:7:{s:5:\"title\";s:6:\"CV/bio\";s:4:\"slug\";s:28:\"twentytwentyfive/page-cv-bio\";s:11:\"description\";s:36:\"A pattern for a CV/Bio landing page.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:3:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:5:\"about\";i:2;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}}s:21:\"page-landing-book.php\";a:8:{s:5:\"title\";s:21:\"Landing page for book\";s:4:\"slug\";s:34:\"twentytwentyfive/page-landing-book\";s:11:\"description\";s:104:\"A landing page for the book with a hero section, pre-order links, locations, FAQs and newsletter signup.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:22:\"page-landing-event.php\";a:8:{s:5:\"title\";s:22:\"Landing page for event\";s:4:\"slug\";s:35:\"twentytwentyfive/page-landing-event\";s:11:\"description\";s:87:\"A landing page for the event with a hero section, description, FAQs and call to action.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:24:\"page-landing-podcast.php\";a:8:{s:5:\"title\";s:24:\"Landing page for podcast\";s:4:\"slug\";s:37:\"twentytwentyfive/page-landing-podcast\";s:11:\"description\";s:111:\"A landing page for the podcast with a hero section, description, logos, grid with videos and newsletter signup.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:50:\"page-link-in-bio-heading-paragraph-links-image.php\";a:7:{s:5:\"title\";s:59:\"Link in bio heading, paragraph, links and full-height image\";s:4:\"slug\";s:63:\"twentytwentyfive/page-link-in-bio-heading-paragraph-links-image\";s:11:\"description\";s:84:\"A link in bio landing page with a heading, paragraph, links and a full height image.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:3:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:6:\"banner\";i:2;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}}s:33:\"page-link-in-bio-wide-margins.php\";a:7:{s:5:\"title\";s:48:\"Link in bio with profile, links and wide margins\";s:4:\"slug\";s:46:\"twentytwentyfive/page-link-in-bio-wide-margins\";s:11:\"description\";s:86:\"A link in bio landing page with social links, a profile photo and a brief description.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:3:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:6:\"banner\";i:2;s:8:\"featured\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}}s:39:\"page-link-in-bio-with-tight-margins.php\";a:8:{s:5:\"title\";s:30:\"Link in bio with tight margins\";s:4:\"slug\";s:52:\"twentytwentyfive/page-link-in-bio-with-tight-margins\";s:11:\"description\";s:90:\"A full-width, full-height link in bio section with an image, a paragraph and social links.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:6:\"banner\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:23:\"page-portfolio-home.php\";a:8:{s:5:\"title\";s:18:\"Portfolio homepage\";s:4:\"slug\";s:36:\"twentytwentyfive/page-portfolio-home\";s:11:\"description\";s:29:\"A portfolio homepage pattern.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:21:\"twentytwentyfive_page\";i:1;s:5:\"posts\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:18:\"page-shop-home.php\";a:8:{s:5:\"title\";s:13:\"Shop homepage\";s:4:\"slug\";s:31:\"twentytwentyfive/page-shop-home\";s:11:\"description\";s:24:\"A shop homepage pattern.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:21:\"twentytwentyfive_page\";}s:8:\"keywords\";a:1:{i:0;s:7:\"starter\";}s:10:\"blockTypes\";a:1:{i:0;s:17:\"core/post-content\";}s:9:\"postTypes\";a:2:{i:0;s:4:\"page\";i:1;s:11:\"wp_template\";}}s:19:\"post-navigation.php\";a:5:{s:5:\"title\";s:15:\"Post navigation\";s:4:\"slug\";s:32:\"twentytwentyfive/post-navigation\";s:11:\"description\";s:29:\"Next and previous post links.\";s:10:\"categories\";a:1:{i:0;s:4:\"text\";}s:10:\"blockTypes\";a:1:{i:0;s:25:\"core/post-navigation-link\";}}s:17:\"pricing-2-col.php\";a:5:{s:5:\"title\";s:18:\"Pricing, 2 columns\";s:4:\"slug\";s:30:\"twentytwentyfive/pricing-2-col\";s:11:\"description\";s:88:\"Pricing section with two columns, pricing plan, description, and call-to-action buttons.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:14:\"call-to-action\";}}s:17:\"pricing-3-col.php\";a:4:{s:5:\"title\";s:18:\"Pricing, 3 columns\";s:4:\"slug\";s:30:\"twentytwentyfive/pricing-3-col\";s:11:\"description\";s:100:\"A three-column boxed pricing table designed to showcase services, descriptions, and pricing options.\";s:10:\"categories\";a:3:{i:0;s:14:\"call-to-action\";i:1;s:6:\"banner\";i:2;s:8:\"services\";}}s:18:\"services-3-col.php\";a:4:{s:5:\"title\";s:19:\"Services, 3 columns\";s:4:\"slug\";s:31:\"twentytwentyfive/services-3-col\";s:11:\"description\";s:56:\"Three columns with images and text to showcase services.\";s:10:\"categories\";a:3:{i:0;s:14:\"call-to-action\";i:1;s:6:\"banner\";i:2;s:8:\"services\";}}s:36:\"services-subscriber-only-section.php\";a:4:{s:5:\"title\";s:33:\"Services, subscriber only section\";s:4:\"slug\";s:49:\"twentytwentyfive/services-subscriber-only-section\";s:11:\"description\";s:72:\"A subscriber-only section highlighting exclusive services and offerings.\";s:10:\"categories\";a:2:{i:0;s:14:\"call-to-action\";i:1;s:8:\"services\";}}s:24:\"services-team-photos.php\";a:4:{s:5:\"title\";s:21:\"Services, team photos\";s:4:\"slug\";s:37:\"twentytwentyfive/services-team-photos\";s:11:\"description\";s:59:\"Display team photos in a services section with grid layout.\";s:10:\"categories\";a:3:{i:0;s:6:\"banner\";i:1;s:14:\"call-to-action\";i:2;s:8:\"featured\";}}s:37:\"template-404-vertical-header-blog.php\";a:5:{s:5:\"title\";s:17:\"Right-aligned 404\";s:4:\"slug\";s:50:\"twentytwentyfive/template-404-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:13:\"templateTypes\";a:1:{i:0;s:3:\"404\";}}s:30:\"template-archive-news-blog.php\";a:6:{s:5:\"title\";s:17:\"News blog archive\";s:4:\"slug\";s:43:\"twentytwentyfive/template-archive-news-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:7:\"archive\";}}s:31:\"template-archive-photo-blog.php\";a:6:{s:5:\"title\";s:18:\"Photo blog archive\";s:4:\"slug\";s:44:\"twentytwentyfive/template-archive-photo-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:7:\"archive\";}}s:30:\"template-archive-text-blog.php\";a:6:{s:5:\"title\";s:17:\"Text blog archive\";s:4:\"slug\";s:43:\"twentytwentyfive/template-archive-text-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:7:\"archive\";}}s:41:\"template-archive-vertical-header-blog.php\";a:6:{s:5:\"title\";s:21:\"Right-aligned archive\";s:4:\"slug\";s:54:\"twentytwentyfive/template-archive-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:7:\"archive\";}}s:27:\"template-home-news-blog.php\";a:6:{s:5:\"title\";s:14:\"News blog home\";s:4:\"slug\";s:40:\"twentytwentyfive/template-home-news-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:3:{i:0;s:10:\"front-page\";i:1;s:5:\"index\";i:2;s:4:\"home\";}}s:28:\"template-home-photo-blog.php\";a:6:{s:5:\"title\";s:15:\"Photo blog home\";s:4:\"slug\";s:41:\"twentytwentyfive/template-home-photo-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:3:{i:0;s:10:\"front-page\";i:1;s:5:\"index\";i:2;s:4:\"home\";}}s:38:\"template-home-posts-grid-news-blog.php\";a:5:{s:5:\"title\";s:34:\"News blog with featured posts grid\";s:4:\"slug\";s:51:\"twentytwentyfive/template-home-posts-grid-news-blog\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:3:{i:0;s:10:\"front-page\";i:1;s:5:\"index\";i:2;s:4:\"home\";}}s:27:\"template-home-text-blog.php\";a:6:{s:5:\"title\";s:14:\"Text blog home\";s:4:\"slug\";s:40:\"twentytwentyfive/template-home-text-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:10:\"front-page\";i:1;s:4:\"home\";}}s:38:\"template-home-vertical-header-blog.php\";a:6:{s:5:\"title\";s:18:\"Right-aligned home\";s:4:\"slug\";s:51:\"twentytwentyfive/template-home-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:3:{i:0;s:10:\"front-page\";i:1;s:5:\"index\";i:2;s:4:\"home\";}}s:40:\"template-home-with-sidebar-news-blog.php\";a:6:{s:5:\"title\";s:22:\"News blog with sidebar\";s:4:\"slug\";s:53:\"twentytwentyfive/template-home-with-sidebar-news-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:3:{i:0;s:10:\"front-page\";i:1;s:5:\"index\";i:2;s:4:\"home\";}}s:28:\"template-page-photo-blog.php\";a:5:{s:5:\"title\";s:15:\"Photo blog page\";s:4:\"slug\";s:41:\"twentytwentyfive/template-page-photo-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:13:\"templateTypes\";a:1:{i:0;s:4:\"page\";}}s:38:\"template-page-vertical-header-blog.php\";a:5:{s:5:\"title\";s:18:\"Right-aligned page\";s:4:\"slug\";s:51:\"twentytwentyfive/template-page-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:13:\"templateTypes\";a:1:{i:0;s:4:\"page\";}}s:33:\"template-query-loop-news-blog.php\";a:4:{s:5:\"title\";s:20:\"News blog query loop\";s:4:\"slug\";s:46:\"twentytwentyfive/template-query-loop-news-blog\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:34:\"template-query-loop-photo-blog.php\";a:6:{s:5:\"title\";s:16:\"Photo blog posts\";s:4:\"slug\";s:47:\"twentytwentyfive/template-query-loop-photo-blog\";s:11:\"description\";s:54:\"A list of posts, 3 columns, with only featured images.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:1:{i:0;s:5:\"query\";}s:10:\"blockTypes\";a:1:{i:0;s:10:\"core/query\";}}s:33:\"template-query-loop-text-blog.php\";a:4:{s:5:\"title\";s:20:\"Text blog query loop\";s:4:\"slug\";s:46:\"twentytwentyfive/template-query-loop-text-blog\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:44:\"template-query-loop-vertical-header-blog.php\";a:4:{s:5:\"title\";s:24:\"Right-aligned query loop\";s:4:\"slug\";s:57:\"twentytwentyfive/template-query-loop-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:8:\"inserter\";b:0;}s:23:\"template-query-loop.php\";a:5:{s:5:\"title\";s:23:\"List of posts, 1 column\";s:4:\"slug\";s:36:\"twentytwentyfive/template-query-loop\";s:11:\"description\";s:61:\"A list of posts, 1 column, with featured image and post date.\";s:10:\"categories\";a:1:{i:0;s:5:\"query\";}s:10:\"blockTypes\";a:1:{i:0;s:10:\"core/query\";}}s:29:\"template-search-news-blog.php\";a:6:{s:5:\"title\";s:24:\"News blog search results\";s:4:\"slug\";s:42:\"twentytwentyfive/template-search-news-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:6:\"search\";}}s:30:\"template-search-photo-blog.php\";a:6:{s:5:\"title\";s:25:\"Photo blog search results\";s:4:\"slug\";s:43:\"twentytwentyfive/template-search-photo-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:6:\"search\";}}s:29:\"template-search-text-blog.php\";a:6:{s:5:\"title\";s:24:\"Text blog search results\";s:4:\"slug\";s:42:\"twentytwentyfive/template-search-text-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:6:\"search\";}}s:40:\"template-search-vertical-header-blog.php\";a:6:{s:5:\"title\";s:26:\"Right-aligned blog, search\";s:4:\"slug\";s:53:\"twentytwentyfive/template-search-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:1:{i:0;s:6:\"search\";}}s:40:\"template-single-left-aligned-content.php\";a:6:{s:5:\"title\";s:30:\"Post with left-aligned content\";s:4:\"slug\";s:47:\"twentytwentyfive/post-with-left-aligned-content\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:29:\"template-single-news-blog.php\";a:6:{s:5:\"title\";s:34:\"News blog single post with sidebar\";s:4:\"slug\";s:42:\"twentytwentyfive/template-single-news-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:26:\"template-single-offset.php\";a:6:{s:5:\"title\";s:34:\"Offset post without featured image\";s:4:\"slug\";s:39:\"twentytwentyfive/template-single-offset\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:30:\"template-single-photo-blog.php\";a:6:{s:5:\"title\";s:22:\"Photo blog single post\";s:4:\"slug\";s:43:\"twentytwentyfive/template-single-photo-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:29:\"template-single-text-blog.php\";a:6:{s:5:\"title\";s:21:\"Text blog single post\";s:4:\"slug\";s:42:\"twentytwentyfive/template-single-text-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:40:\"template-single-vertical-header-blog.php\";a:6:{s:5:\"title\";s:25:\"Right-aligned single post\";s:4:\"slug\";s:53:\"twentytwentyfive/template-single-vertical-header-blog\";s:11:\"description\";s:0:\"\";s:13:\"viewportWidth\";i:1400;s:8:\"inserter\";b:0;s:13:\"templateTypes\";a:2:{i:0;s:5:\"posts\";i:1;s:6:\"single\";}}s:22:\"testimonials-2-col.php\";a:5:{s:5:\"title\";s:21:\"2 columns with avatar\";s:4:\"slug\";s:35:\"twentytwentyfive/testimonials-2-col\";s:11:\"description\";s:42:\"Two columns with testimonials and avatars.\";s:10:\"categories\";a:1:{i:0;s:12:\"testimonials\";}s:8:\"keywords\";a:1:{i:0;s:11:\"testimonial\";}}s:22:\"testimonials-6-col.php\";a:5:{s:5:\"title\";s:35:\"3 column layout with 6 testimonials\";s:4:\"slug\";s:35:\"twentytwentyfive/testimonials-6-col\";s:11:\"description\";s:86:\"A section with three columns and two rows, each containing a testimonial and citation.\";s:10:\"categories\";a:1:{i:0;s:12:\"testimonials\";}s:8:\"keywords\";a:1:{i:0;s:11:\"testimonial\";}}s:22:\"testimonials-large.php\";a:5:{s:5:\"title\";s:32:\"Review with large image on right\";s:4:\"slug\";s:35:\"twentytwentyfive/testimonials-large\";s:11:\"description\";s:46:\"A testimonial with a large image on the right.\";s:10:\"categories\";a:1:{i:0;s:12:\"testimonials\";}s:8:\"keywords\";a:1:{i:0;s:11:\"testimonial\";}}s:13:\"text-faqs.php\";a:6:{s:5:\"title\";s:4:\"FAQs\";s:4:\"slug\";s:26:\"twentytwentyfive/text-faqs\";s:11:\"description\";s:68:\"A FAQs section with a FAQ heading and list of questions and answers.\";s:13:\"viewportWidth\";i:1400;s:10:\"categories\";a:2:{i:0;s:4:\"text\";i:1;s:5:\"about\";}s:8:\"keywords\";a:5:{i:0;s:3:\"faq\";i:1;s:5:\"about\";i:2;s:10:\"frequently\";i:3;s:5:\"asked\";i:4;s:9:\"questions\";}}s:19:\"vertical-header.php\";a:6:{s:5:\"title\";s:20:\"Vertical site header\";s:4:\"slug\";s:32:\"twentytwentyfive/vertical-header\";s:11:\"description\";s:52:\"Vertical site header with site title and navigation.\";s:13:\"viewportWidth\";i:300;s:10:\"categories\";a:1:{i:0;s:6:\"header\";}s:10:\"blockTypes\";a:1:{i:0;s:34:\"core/template-part/vertical-header\";}}}}'),(33,1,'registrationnotification','no'),(34,1,'welcome_user_email','Blocaire,\r\n\r\nEl vostre compre està configurat.\r\n\r\nPodeu iniciar una sessió amb la següent informació:\r\nUsuari: USERNAME\r\nContrasenya: PASSWORD\r\nLOGINLINK\r\n\r\nGràcies!\r\n\r\n--L\'equip @ SITE_NAME'),(109,1,'mucd_duplicables','all'),(110,1,'mucd_log_dir','/srv/www/blocs/src/wp-content/plugins/multisite-clone-duplicator/logs/'),(52,1,'pm_supporter_control_list','EMPTY'),(635,1,'_site_transient_update_themes','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1790758721;s:7:\"checked\";a:4:{s:16:\"twentytwentyfive\";s:3:\"1.5\";s:16:\"twentytwentyfour\";s:3:\"1.6\";s:17:\"twentytwentythree\";s:3:\"1.7\";s:15:\"twentytwentytwo\";s:3:\"2.2\";}s:8:\"response\";a:0:{}s:9:\"no_update\";a:4:{s:16:\"twentytwentyfive\";a:6:{s:5:\"theme\";s:16:\"twentytwentyfive\";s:11:\"new_version\";s:3:\"1.5\";s:3:\"url\";s:46:\"https://wordpress.org/themes/twentytwentyfive/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/theme/twentytwentyfive.1.5.zip\";s:8:\"requires\";s:3:\"6.7\";s:12:\"requires_php\";s:3:\"7.2\";}s:16:\"twentytwentyfour\";a:6:{s:5:\"theme\";s:16:\"twentytwentyfour\";s:11:\"new_version\";s:3:\"1.6\";s:3:\"url\";s:46:\"https://wordpress.org/themes/twentytwentyfour/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/theme/twentytwentyfour.1.6.zip\";s:8:\"requires\";s:3:\"6.4\";s:12:\"requires_php\";s:3:\"7.0\";}s:17:\"twentytwentythree\";a:6:{s:5:\"theme\";s:17:\"twentytwentythree\";s:11:\"new_version\";s:3:\"1.7\";s:3:\"url\";s:47:\"https://wordpress.org/themes/twentytwentythree/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/theme/twentytwentythree.1.7.zip\";s:8:\"requires\";s:3:\"6.1\";s:12:\"requires_php\";s:3:\"5.6\";}s:15:\"twentytwentytwo\";a:6:{s:5:\"theme\";s:15:\"twentytwentytwo\";s:11:\"new_version\";s:3:\"2.2\";s:3:\"url\";s:45:\"https://wordpress.org/themes/twentytwentytwo/\";s:7:\"package\";s:61:\"https://downloads.wordpress.org/theme/twentytwentytwo.2.2.zip\";s:8:\"requires\";s:3:\"5.9\";s:12:\"requires_php\";s:3:\"5.6\";}}s:12:\"translations\";a:1:{i:0;a:7:{s:4:\"type\";s:5:\"theme\";s:4:\"slug\";s:15:\"twentytwentytwo\";s:8:\"language\";s:2:\"ca\";s:7:\"version\";s:3:\"2.2\";s:7:\"updated\";s:19:\"2024-11-03 08:47:11\";s:7:\"package\";s:76:\"https://downloads.wordpress.org/translation/theme/twentytwentytwo/2.2/ca.zip\";s:10:\"autoupdate\";b:1;}}}'),(636,1,'_site_transient_update_plugins','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1790758722;s:8:\"response\";a:0:{}s:12:\"translations\";a:1:{i:0;a:7:{s:4:\"type\";s:6:\"plugin\";s:4:\"slug\";s:11:\"hello-dolly\";s:8:\"language\";s:2:\"ca\";s:7:\"version\";s:5:\"1.7.2\";s:7:\"updated\";s:19:\"2019-06-16 10:33:42\";s:7:\"package\";s:75:\"https://downloads.wordpress.org/translation/plugin/hello-dolly/1.7.2/ca.zip\";s:10:\"autoupdate\";b:1;}}s:9:\"no_update\";a:2:{s:19:\"akismet/akismet.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:21:\"w.org/plugins/akismet\";s:4:\"slug\";s:7:\"akismet\";s:6:\"plugin\";s:19:\"akismet/akismet.php\";s:11:\"new_version\";s:5:\"5.7.2\";s:3:\"url\";s:38:\"https://wordpress.org/plugins/akismet/\";s:7:\"package\";s:56:\"https://downloads.wordpress.org/plugin/akismet.5.7.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:60:\"https://ps.w.org/akismet/assets/icon-256x256.png?rev=2818463\";s:2:\"1x\";s:60:\"https://ps.w.org/akismet/assets/icon-128x128.png?rev=2818463\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:63:\"https://ps.w.org/akismet/assets/banner-1544x500.png?rev=2900731\";s:2:\"1x\";s:62:\"https://ps.w.org/akismet/assets/banner-772x250.png?rev=2900731\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"5.8\";}s:9:\"hello.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:25:\"w.org/plugins/hello-dolly\";s:4:\"slug\";s:11:\"hello-dolly\";s:6:\"plugin\";s:9:\"hello.php\";s:11:\"new_version\";s:5:\"1.7.2\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/hello-dolly/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-256x256.jpg?rev=2052855\";s:2:\"1x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-128x128.jpg?rev=2052855\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/hello-dolly/assets/banner-1544x500.jpg?rev=2645582\";s:2:\"1x\";s:66:\"https://ps.w.org/hello-dolly/assets/banner-772x250.jpg?rev=2052855\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"4.6\";}}s:7:\"checked\";a:2:{s:19:\"akismet/akismet.php\";s:5:\"5.7.2\";s:9:\"hello.php\";s:5:\"1.7.2\";}}'),(642,1,'recently_activated','a:2:{s:33:\"xtec-weekblog2/xtec-weekblog2.php\";i:1489494759;s:32:\"simpler-ipaper/scribd-ipaper.php\";i:1489494684;}'),(643,1,'mucd_copy_files','yes'),(644,1,'mucd_keep_users','yes'),(645,1,'mucd_log','no'),(646,1,'xmm_quota_percentage','75'),(647,1,'xmm_send_email','1'),(648,1,'xmm_email_addresses',''),(621,1,'auth_key','+sxLD,5 dv?}H<#$=t2F}*fH, 1Vk0Ok&W:p|nmE9XpkHso^UI1)q5Y+*li6>~6J'),(622,1,'auth_salt','zcW>;<}?p&Eg!2#JHR_1fYQHU68gtwg!cV@O]&#i6k)|h G4SV5d[5]r>/9Yx1?5'),(641,1,'_site_transient_update_core','O:8:\"stdClass\":4:{s:7:\"updates\";a:2:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:7:\"upgrade\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-7.1.2.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-7.1.2.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-7.1.2-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-7.1.2-new-bundled.zip\";s:7:\"partial\";s:69:\"https://downloads.wordpress.org/release/wordpress-7.1.2-partial-0.zip\";s:8:\"rollback\";s:0:\"\";}s:7:\"current\";s:5:\"7.1.2\";s:7:\"version\";s:5:\"7.1.2\";s:11:\"php_version\";s:3:\"7.4\";s:13:\"mysql_version\";s:5:\"5.5.5\";s:11:\"new_bundled\";s:3:\"6.7\";s:15:\"partial_version\";s:3:\"7.1\";}i:1;O:8:\"stdClass\":11:{s:8:\"response\";s:10:\"autoupdate\";s:8:\"download\";s:51:\"https://downloads.w.org/release/wordpress-7.1.2.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:51:\"https://downloads.w.org/release/wordpress-7.1.2.zip\";s:10:\"no_content\";s:62:\"https://downloads.w.org/release/wordpress-7.1.2-no-content.zip\";s:11:\"new_bundled\";s:63:\"https://downloads.w.org/release/wordpress-7.1.2-new-bundled.zip\";s:7:\"partial\";s:61:\"https://downloads.w.org/release/wordpress-7.1.2-partial-0.zip\";s:8:\"rollback\";s:62:\"https://downloads.w.org/release/wordpress-7.1.2-rollback-0.zip\";}s:7:\"current\";s:5:\"7.1.2\";s:7:\"version\";s:5:\"7.1.2\";s:11:\"php_version\";s:3:\"7.4\";s:13:\"mysql_version\";s:5:\"5.5.5\";s:11:\"new_bundled\";s:3:\"6.7\";s:15:\"partial_version\";s:3:\"7.1\";s:9:\"new_files\";s:0:\"\";}}s:12:\"last_checked\";i:1790758721;s:15:\"version_checked\";s:3:\"7.1\";s:12:\"translations\";a:1:{i:0;a:7:{s:4:\"type\";s:4:\"core\";s:4:\"slug\";s:7:\"default\";s:8:\"language\";s:2:\"ca\";s:7:\"version\";s:3:\"7.1\";s:7:\"updated\";s:19:\"2026-09-11 11:51:08\";s:7:\"package\";s:59:\"https://downloads.wordpress.org/translation/core/7.1/ca.zip\";s:10:\"autoupdate\";b:1;}}}'),(600,1,'xtec_mail_replyto','blocs-noreply@xtec.invalid'),(601,1,'xtec_mail_sender','educacio'),(602,1,'xtec_mail_log','1'),(603,1,'xtec_mail_debug','0'),(604,1,'xtec_mail_logpath','/var/log/apache2/correulog.txt'),(599,1,'xtec_mail_idapp','XTECBLOCS'),(573,1,'xtec_ldap_login_type','LDAP'),(577,1,'bwp_capt_theme','a:4:{s:9:\"input_tab\";s:1:\"0\";s:10:\"enable_css\";s:3:\"yes\";s:11:\"select_lang\";s:2:\"es\";s:12:\"select_theme\";s:3:\"red\";}'),(668,1,'site_meta_supported','1'),(669,1,'_site_transient_timeout_wp_theme_files_patterns-f7da5bca0ee2c1e4e4a97c11492a0ff9','1790763882'),(670,1,'_site_transient_wp_theme_files_patterns-f7da5bca0ee2c1e4e4a97c11492a0ff9','a:2:{s:7:\"version\";b:0;s:8:\"patterns\";a:0:{}}'),(671,1,'_site_transient_timeout_wp_theme_files_patterns-f5a87d64e55cc63496845d8cf3d6659c','1790763882'),(672,1,'_site_transient_wp_theme_files_patterns-f5a87d64e55cc63496845d8cf3d6659c','a:2:{s:7:\"version\";b:0;s:8:\"patterns\";a:0:{}}'),(651,1,'menu_items','a:1:{s:7:\"plugins\";s:1:\"1\";}'),(652,1,'first_page',''),(653,1,'first_comment',''),(654,1,'first_comment_url',''),(655,1,'first_comment_author',''),(656,1,'limited_email_domains',''),(657,1,'banned_email_domains',''),(658,1,'WPLANG','ca'),(659,1,'xtec_signup_maxblogsday','20'),(660,1,'xtec_ldap_host','oid-xtec.educacio.intranet'),(661,1,'xtec_ldap_port','389'),(662,1,'xtec_ldap_version','3'),(663,1,'xtec_ldap_base_dn','cn=users,dc=educacio,dc=intranet'),(664,1,'_site_transient_timeout_wp_theme_files_patterns-341f1eb6993534488381e42c48374501','1790763881'),(665,1,'_site_transient_wp_theme_files_patterns-341f1eb6993534488381e42c48374501','a:2:{s:7:\"version\";b:0;s:8:\"patterns\";a:0:{}}');
/*!40000 ALTER TABLE `wp_sitemeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_relationships`
--

DROP TABLE IF EXISTS `wp_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_relationships`
--

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;
INSERT INTO `wp_term_relationships` VALUES (1,2,0),(2,2,0),(3,2,0),(4,2,0),(5,2,0),(6,2,0),(7,2,0),(1,1,0),(12,3,0),(12,4,0);
/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_taxonomy`
--

DROP TABLE IF EXISTS `wp_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint unsigned NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_taxonomy`
--

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
INSERT INTO `wp_term_taxonomy` VALUES (1,1,'category','',0,1),(2,2,'link_category','',0,7),(3,3,'calendar_feed','',0,0),(4,4,'calendar_type','',0,0),(5,5,'calendar_feed','',0,0);
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_termmeta`
--

DROP TABLE IF EXISTS `wp_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_termmeta`
--

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_terms`
--

DROP TABLE IF EXISTS `wp_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_terms` (
  `term_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_terms`
--

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;
INSERT INTO `wp_terms` VALUES (1,'General','general',0),(2,'Blogroll','blogroll',0),(3,'google','google',0),(4,'default-calendar','default-calendar',0),(5,'grouped-calendar','grouped-calendar',0);
/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_user_blogs`
--

DROP TABLE IF EXISTS `wp_user_blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_user_blogs` (
  `ubid` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL DEFAULT '0',
  `blogId` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ubid`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_user_blogs`
--

LOCK TABLES `wp_user_blogs` WRITE;
/*!40000 ALTER TABLE `wp_user_blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_user_blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM AUTO_INCREMENT=392 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_usermeta`
--

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (1,1,'first_name','Administrador'),(2,1,'last_name',''),(3,1,'nickname','admin'),(4,1,'description',''),(5,1,'rich_editing','true'),(6,1,'comment_shortcuts','false'),(7,1,'admin_color','modern'),(8,1,'use_ssl','0'),(9,1,'show_admin_bar_front','true'),(66,1,'wp_5_user-settings-time','1489496058'),(11,1,'aim',''),(12,1,'yim',''),(13,1,'jabber',''),(14,1,'wp_capabilities','a:1:{s:13:\"administrator\";s:1:\"1\";}'),(15,1,'wp_user_level','10'),(16,1,'wp_dashboard_quick_press_last_post_id','20'),(17,1,'source_domain','agora'),(18,1,'primary_blog','1'),(28,1,'wp_5_capabilities','a:1:{s:13:\"administrator\";s:1:\"1\";}'),(21,1,'wp_3_capabilities','a:1:{s:13:\"administrator\";s:1:\"1\";}'),(22,1,'wp_3_user_level','10'),(24,1,'wp_4_capabilities','a:1:{s:13:\"administrator\";s:1:\"1\";}'),(25,1,'wp_4_user_level','10'),(26,1,'wp_3_dashboard_quick_press_last_post_id','122'),(27,1,'wp_4_dashboard_quick_press_last_post_id','177'),(29,1,'wp_5_user_level','10'),(302,1,'meta-box-order_post','a:3:{s:4:\"side\";s:56:\"submitdiv,postimagediv,postexcerpt,metabox1,tagsdiv-post\";s:6:\"normal\";s:11:\"categorydiv\";s:8:\"advanced\";s:0:\"\";}'),(32,1,'wp_5_dashboard_quick_press_last_post_id','11'),(65,1,'wp_5_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce'),(64,1,'wp_user-settings-time','1489494506'),(63,1,'wp_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce'),(67,1,'dismissed_wp_pointers','wp330_toolbar,wp340_customize_current_theme_link,wp350_media,wp360_revisions,addtoany_settings_pointer'),(68,1,'wp_6_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),(69,1,'wp_6_user-settings-time','1424705365'),(70,1,'wp_6_dashboard_quick_press_last_post_id','12'),(71,1,'wp_3_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce'),(72,1,'wp_3_user-settings-time','1489495977'),(73,1,'wp_4_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce'),(74,1,'wp_4_user-settings-time','1489496035'),(75,1,'closedpostboxes_dashboard','a:0:{}'),(76,1,'metaboxhidden_dashboard','a:0:{}'),(77,1,'closedpostboxes_dashboard-network','a:0:{}'),(78,1,'metaboxhidden_dashboard-network','a:0:{}'),(104,1,'managenav-menuscolumnshidden','a:4:{i:0;s:11:\"link-target\";i:1;s:11:\"css-classes\";i:2;s:3:\"xfn\";i:3;s:11:\"description\";}'),(105,1,'metaboxhidden_nav-menus','a:4:{i:0;s:8:\"add-post\";i:1;s:12:\"add-gce_feed\";i:2;s:12:\"add-post_tag\";i:3;s:15:\"add-post_format\";}'),(123,1,'wp_7_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),(124,1,'wp_7_user-settings-time','1424854295'),(125,1,'wp_7_dashboard_quick_press_last_post_id','3'),(130,1,'wp_9_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),(131,1,'wp_9_user-settings-time','1424854980'),(132,1,'wp_9_dashboard_quick_press_last_post_id','3'),(137,1,'wp_11_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606'),(138,1,'wp_11_user-settings-time','1424856720'),(139,1,'wp_11_dashboard_quick_press_last_post_id','3'),(168,1,'wp_18_dashboard_quick_press_last_post_id','46'),(270,1,'wp_36_dashboard_quick_press_last_post_id','3'),(348,1,'wp_52_dashboard_quick_press_last_post_id','6'),(367,1,'wp_53_dashboard_quick_press_last_post_id','3'),(269,1,'wp_36_user-settings-time','1426249582'),(166,1,'wp_18_user-settings','m6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606'),(167,1,'wp_18_user-settings-time','1425287273'),(195,1,'wp_19_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(196,1,'wp_19_user-settings-time','1425300779'),(197,1,'wp_19_dashboard_quick_press_last_post_id','3'),(347,1,'wp_52_user-settings-time','1427970091'),(250,1,'wp_30_user-settings','m6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),(224,1,'wp_26_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(225,1,'wp_26_user-settings-time','1426062713'),(226,1,'wp_26_dashboard_quick_press_last_post_id','111'),(252,1,'wp_30_dashboard_quick_press_last_post_id','112'),(251,1,'wp_30_user-settings-time','1426064784'),(365,1,'wp_53_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(366,1,'wp_53_user-settings-time','1428934970'),(233,1,'wp_27_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(234,1,'wp_27_user-settings-time','1426063042'),(235,1,'wp_27_dashboard_quick_press_last_post_id','111'),(268,1,'wp_36_user-settings','m6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),(255,1,'wp_31_user-settings','m6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),(256,1,'wp_31_user-settings-time','1426149689'),(257,1,'wp_31_dashboard_quick_press_last_post_id','112'),(346,1,'wp_52_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(287,1,'wp_44_user-settings','m6=o&m8=o&libraryContent=browse&editor=html&dfw_width=606&posts_list_mode=list'),(288,1,'wp_44_user-settings-time','1426583453'),(289,1,'wp_44_dashboard_quick_press_last_post_id','112'),(303,1,'metaboxhidden_post','a:2:{i:0;s:7:\"slugdiv\";i:1;s:9:\"authordiv\";}'),(305,1,'meta-box-order_page','a:3:{s:4:\"side\";s:23:\"submitdiv,pageparentdiv\";s:6:\"normal\";s:16:\"commentstatusdiv\";s:8:\"advanced\";s:0:\"\";}'),(304,1,'closedpostboxes_post','a:0:{}'),(306,1,'metaboxhidden_page','a:3:{i:0;s:16:\"commentstatusdiv\";i:1;s:7:\"slugdiv\";i:2;s:9:\"authordiv\";}'),(376,1,'wp_55_user-settings-time','1428936615'),(307,1,'closedpostboxes_page','a:0:{}'),(371,1,'wp_54_user-settings-time','1428935582'),(372,1,'wp_54_dashboard_quick_press_last_post_id','3'),(375,1,'wp_55_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(387,11,'use_ssl','0'),(388,11,'show_admin_bar_front','true'),(391,11,'dismissed_wp_pointers','wp350_media,wp360_revisions,wp360_locks,wp390_widgets'),(383,11,'description',''),(337,1,'wp_51_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(338,1,'wp_51_user-settings-time','1426757741'),(339,1,'wp_51_dashboard_quick_press_last_post_id','3'),(384,11,'rich_editing','true'),(385,11,'comment_shortcuts','false'),(386,11,'admin_color','modern'),(381,11,'first_name',''),(380,11,'nickname','est_colex'),(370,1,'wp_54_user-settings','m6=o&m8=o&libraryContent=browse&editor=tinymce&dfw_width=606&posts_list_mode=list'),(377,1,'wp_55_dashboard_quick_press_last_post_id','3'),(382,11,'last_name','');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `spam` tinyint NOT NULL DEFAULT '0',
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_users`
--

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (1,'admin','$P$B0BqNdVBE.ATTO79Lz.szWyh1QRonh.','admin','admin@blocs.xtec.cat','','2012-12-19 13:05:13','',0,'admin',0,0),(11,'est_colex','$P$BG97aVa8N.KCw0mH38BbV5XzY8b7xR0','est_colex','est_colex@blocs.xtec.cat','','2015-04-20 12:17:51','',0,'est_colex',0,0);
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
