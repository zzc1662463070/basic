/*
Navicat MySQL Data Transfer

Source Server         : tangdashuai
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : basic

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-12-07 21:20:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `salt` char(3) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `last_login_ip` varchar(100) DEFAULT NULL,
  `last_login_time` int(11) DEFAULT NULL,
  `expire_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for guanggao
-- ----------------------------
DROP TABLE IF EXISTS `guanggao`;
CREATE TABLE `guanggao` (
  `g_lianjie_A` varchar(255) NOT NULL COMMENT '安卓下载链接',
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `g_name` varchar(100) NOT NULL COMMENT '广告名称',
  `g_type` tinyint(1) NOT NULL COMMENT '广告类型',
  `g_info` varchar(500) NOT NULL COMMENT '广告内容描述',
  `picname` varchar(50) DEFAULT NULL,
  `plan_id` int(10) NOT NULL COMMENT '计划id',
  `status` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1，开启 2关闭',
  `show_number` int(20) unsigned NOT NULL DEFAULT '0' COMMENT '展示次数',
  `g_lianjie_I` varchar(255) NOT NULL COMMENT '苹果下载链接',
  PRIMARY KEY (`g_id`),
  KEY `show_number` (`show_number`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for plan
-- ----------------------------
DROP TABLE IF EXISTS `plan`;
CREATE TABLE `plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `limit` int(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `start_time` int(10) DEFAULT NULL,
  `end_time` int(10) DEFAULT NULL,
  `num` int(11) unsigned zerofill DEFAULT '00000000000',
  `plan_width` double DEFAULT NULL,
  `plan_height` double DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `p_lianjie` varchar(32) NOT NULL,
  PRIMARY KEY (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for record
-- ----------------------------
DROP TABLE IF EXISTS `record`;
CREATE TABLE `record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userip` varchar(10) NOT NULL,
  `userid` varchar(10) DEFAULT NULL,
  `plan_id` int(10) unsigned NOT NULL,
  `planname` varchar(20) NOT NULL COMMENT '计划名称',
  `g_id` int(10) unsigned NOT NULL,
  `g_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '广告名称',
  `shownumber` int(20) unsigned NOT NULL COMMENT '广告展示次数',
  `num` int(10) NOT NULL DEFAULT '0' COMMENT '点击次数',
  `time` int(20) NOT NULL COMMENT '访问时间',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '展示和统计的类型1表示展示2表示点击',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
