/*
Navicat MySQL Data Transfer

Source Server         : 20.10.1.51
Source Server Version : 50718
Source Host           : 20.10.1.51:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50718
File Encoding         : 65001

Date: 2019-11-01 11:03:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for game_audit
-- ----------------------------
DROP TABLE IF EXISTS `game_audit`;
CREATE TABLE `game_audit` (
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '平台',
  `bundleid` varchar(64) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '' COMMENT '版本',
  PRIMARY KEY (`platform`,`bundleid`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of game_audit
-- ----------------------------
INSERT INTO `game_audit` VALUES ('appstore', 'com.manic.casino', '1.0.0');
INSERT INTO `game_audit` VALUES ('appstore', 'com.xxmanic.casino', '1.0.0');
INSERT INTO `game_audit` VALUES ('google', 'com.tencent.tmgp.paw', '1.0.0');
INSERT INTO `game_audit` VALUES ('google', 'com.tencent.tmgp.paw', '1.0.5');

-- ----------------------------
-- Table structure for yly_config_list
-- ----------------------------
DROP TABLE IF EXISTS `yly_config_list`;
CREATE TABLE `yly_config_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) DEFAULT NULL COMMENT '用户名',
  `value` text COMMENT '配置值',
  `tag` varchar(64) DEFAULT NULL COMMENT '标签',
  `create_time` int(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(29) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态：0 可用 1禁止',
  `ext1` varchar(512) DEFAULT NULL COMMENT '拓展1',
  `ext2` varchar(512) DEFAULT NULL COMMENT '拓展2',
  `ext3` varchar(512) DEFAULT NULL COMMENT '拓展3',
  `is_package` int(2) NOT NULL DEFAULT '0' COMMENT '0 全部包 1不是全部',
  `package` text NOT NULL COMMENT '选择的包',
  `is_config` int(2) NOT NULL DEFAULT '0' COMMENT '是否作用到config 0是 1否 ',
  `desc` int(11) NOT NULL DEFAULT '0' COMMENT '显示排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yly_config_list
-- ----------------------------
INSERT INTO `yly_config_list` VALUES ('66', '华为', '1.0.22', 'hw_audit', '1568100549', '1568180651', '0', '1', '', '\"配置值\"为华为版本号  \"拓展1\"设置为1oooo 设置为0为xxx', '1', '{\"com.reallycattle.longzhu\":\"0\",\"com.reallycattle.globalql\":\"0\",\"com.longzhu.fish.us\":\"0\",\"com.longzhu.ninegame\":\"0\",\"com.longzhu.mega\":\"0\",\"com.renzhi.chu\":\"0\",\"com.longmen.nova\":\"0\",\"com.longmen.novaslots\":\"0\",\"com.ceshi.yong\":\"0\",\"com.ren.renzc\":\"0\",\"com.xxmanic.casino\":\"0\",\"com.manic.casino\":\"0\",\"com.ml.paw\":\"1\",\"com.tencent.tmgp.paw\":\"0\",\"com.reallycattle.tzmj\":\"0\"}', '1', '0');
