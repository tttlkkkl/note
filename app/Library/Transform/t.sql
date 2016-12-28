-- MySQL dump 10.16  Distrib 10.1.18-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: m_blog
-- ------------------------------------------------------
-- Server version	10.1.18-MariaDB

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
-- Table structure for table `b_article`
--

DROP TABLE IF EXISTS `b_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(140) NOT NULL,
  `summary` varchar(255) DEFAULT NULL COMMENT '简短的描述',
  `content` text NOT NULL,
  `created_at` datetime NOT NULL COMMENT '添加时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1正常，0异常',
  `reading` int(11) DEFAULT '0' COMMENT '阅读量',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  `classify` int(11) NOT NULL DEFAULT '0' COMMENT '所属类目',
  `path` char(32) DEFAULT '' COMMENT 'note中path的MD5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='解析后的文章数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_article_con_tag`
--

DROP TABLE IF EXISTS `b_article_con_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_article_con_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL DEFAULT '0' COMMENT '标签id',
  `article_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '建立时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_classify`
--

DROP TABLE IF EXISTS `b_classify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_classify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` char(15) NOT NULL DEFAULT '' COMMENT '类目名称',
  `created_at` datetime NOT NULL COMMENT '添加时间',
  `updated_at` datetime NOT NULL COMMENT '修改时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1正常，0不启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 博客类目';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_note`
--

DROP TABLE IF EXISTS `b_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL DEFAULT '0' COMMENT '笔记数据所在的笔记本本地存储id',
  `summary` varchar(255) DEFAULT '',
  `path` char(128) NOT NULL,
  `thumbnail` varchar(255) DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0' COMMENT '笔记大小包括所有资源附件',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `author` varchar(20) DEFAULT '' COMMENT '作者',
  `modify_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '本地更新时间',
  `source` varchar(255) DEFAULT '' COMMENT '修改后的笔记来源',
  `title` char(140) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COMMENT='笔记原数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_note_book`
--

DROP TABLE IF EXISTS `b_note_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_note_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` char(64) NOT NULL DEFAULT '' COMMENT '笔记路径',
  `name` char(20) NOT NULL DEFAULT '' COMMENT '笔记本名称',
  `notes_num` int(11) NOT NULL DEFAULT '0' COMMENT '笔记数目',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '同步更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `node_index` (`path`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='有道云笔记原笔记数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_syn_log`
--

DROP TABLE IF EXISTS `b_syn_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_syn_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(128) NOT NULL DEFAULT '' COMMENT '同步以及更新临时日志',
  `code` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 普通提醒，1 警告，2 错误 ，3 同步异常中断 4 完成',
  `type` tinyint(4) DEFAULT '0' COMMENT '0，远程同步日志，1本地同步日志',
  `created_at` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2071 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `b_tag`
--

DROP TABLE IF EXISTS `b_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `b_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(15) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '添加时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  `path` char(32) DEFAULT '' COMMENT 'note_book中path的MD5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='标签信息';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-12-28 23:28:34
