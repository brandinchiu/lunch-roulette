<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190906160858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Initial schema load';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
            -- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
            --
            -- Host: localhost    Database: lunch-roulette
            -- ------------------------------------------------------
            -- Server version	5.7.22-0ubuntu0.16.04.1
            
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
            -- Table structure for table `history`
            --
            
            DROP TABLE IF EXISTS `history`;
            /*!40101 SET @saved_cs_client     = @@character_set_client */;
            /*!40101 SET character_set_client = utf8 */;
            CREATE TABLE `history` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `lunch_option_id` int(10) unsigned DEFAULT NULL,
              `slack_id` varchar(128) DEFAULT NULL,
              `slack_name` varchar(128) DEFAULT NULL,
              `date` date NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fkey_history_lunch_option_id_idx` (`lunch_option_id`),
              CONSTRAINT `fkey_history_lunch_option_id` FOREIGN KEY (`lunch_option_id`) REFERENCES `lunch_option` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
            /*!40101 SET character_set_client = @saved_cs_client */;
            
            --
            -- Table structure for table `lunch_option`
            --
            
            DROP TABLE IF EXISTS `lunch_option`;
            /*!40101 SET @saved_cs_client     = @@character_set_client */;
            /*!40101 SET character_set_client = utf8 */;
            CREATE TABLE `lunch_option` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(128) DEFAULT NULL,
              `url` text,
              `slack_id` varchar(128) DEFAULT NULL,
              `slack_name` varchar(128) DEFAULT NULL,
              `date_created` datetime NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `lunch_option_name_unq` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
            /*!40101 SET character_set_client = @saved_cs_client */;
            
            --
            -- Table structure for table `lunch_option_tag`
            --
            
            DROP TABLE IF EXISTS `lunch_option_tag`;
            /*!40101 SET @saved_cs_client     = @@character_set_client */;
            /*!40101 SET character_set_client = utf8 */;
            CREATE TABLE `lunch_option_tag` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `lunch_option_id` int(10) unsigned NOT NULL,
              `tag_id` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `lunch_option_tag_lunch_option_tag_unq` (`lunch_option_id`,`tag_id`),
              KEY `fkey_lunch_option_tag_lunch_option_id_idx` (`lunch_option_id`),
              KEY `fkey_lunch_option_tag_tag_id_idx` (`tag_id`),
              CONSTRAINT `fkey_lunch_option_tag_lunch_option_id` FOREIGN KEY (`lunch_option_id`) REFERENCES `lunch_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fkey_lunch_option_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
            /*!40101 SET character_set_client = @saved_cs_client */;
            
            --
            -- Table structure for table `tag`
            --
            
            DROP TABLE IF EXISTS `tag`;
            /*!40101 SET @saved_cs_client     = @@character_set_client */;
            /*!40101 SET character_set_client = utf8 */;
            CREATE TABLE `tag` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(64) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `tags_name_unq` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
            /*!40101 SET character_set_client = @saved_cs_client */;
            
            --
            -- Dumping routines for database 'lunch-roulette'
            --
            /*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
            
            /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
            /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
            /*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
            /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
            /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
            /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
            /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
            
            -- Dump completed on 2019-09-06 12:10:52
        ");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("
            DROP DATABASE `lunch-roulette`;
        ");
    }
}
