/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50626
Source Host           : localhost:3306
Source Database       : xdpaper

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2016-01-18 15:35:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for teacher
-- ----------------------------
DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `last_login_ip` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `last_login_time` int(10) DEFAULT NULL,
  `login` int(11) DEFAULT '0',
  `password` varchar(20) CHARACTER SET latin1 NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
