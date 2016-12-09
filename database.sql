/*
Navicat MySQL Data Transfer

Source Server         : 192.168.56.1
Source Server Version : 50550
Source Host           : 192.168.56.1:3306
Source Database       : laravel

Target Server Type    : MYSQL
Target Server Version : 50550
File Encoding         : 65001

Date: 2016-11-29 09:43:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for b_article
-- ----------------------------
DROP TABLE IF EXISTS `b_article`;
CREATE TABLE `b_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(140) NOT NULL,
  `summary` varchar(255) DEFAULT NULL COMMENT '简短的描述',
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1正常，0异常',
  `reading` int(11) DEFAULT '0' COMMENT '阅读量',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '删除时间',
  `classify` int(11) NOT NULL DEFAULT '0' COMMENT '所属类目',
  `path` char(32) DEFAULT '' COMMENT 'note中path的MD5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='解析后的文章数据';

-- ----------------------------
-- Table structure for b_article_con_tag
-- ----------------------------
DROP TABLE IF EXISTS `b_article_con_tag`;
CREATE TABLE `b_article_con_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL DEFAULT '0' COMMENT '标签id',
  `article_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '建立时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签关联表';

-- ----------------------------
-- Table structure for b_classify
-- ----------------------------
DROP TABLE IF EXISTS `b_classify`;
CREATE TABLE `b_classify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` char(15) NOT NULL DEFAULT '' COMMENT '类目名称',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '删除时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1正常，0不启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 博客类目';

-- ----------------------------
-- Table structure for b_note
-- ----------------------------
DROP TABLE IF EXISTS `b_note`;
CREATE TABLE `b_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL DEFAULT 0 COMMENT '笔记数据所在的笔记本本地存储id',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='笔记原数据';

-- ----------------------------
-- Table structure for b_note_book
-- ----------------------------
DROP TABLE IF EXISTS `b_note_book`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='有道云笔记原笔记数据';

-- ----------------------------
-- Table structure for b_syn_log
-- ----------------------------
DROP TABLE IF EXISTS `b_syn_log`;
CREATE TABLE `b_syn_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '同步以及更新临时日志',
  `code` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 普通提醒，1 警告，2 错误 ，3 同步异常中断 4 完成',
  `type`  tinyint(4) NULL DEFAULT 0 COMMENT '0，远程同步日志，1本地同步日志',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for b_tag
-- ----------------------------
DROP TABLE IF EXISTS `b_tag`;
CREATE TABLE `b_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(15) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '删除时间',
  `path` char(32) DEFAULT '' COMMENT 'note_book中path的MD5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签信息';
