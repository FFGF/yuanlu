/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : yuanlu_fund1

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-09-28 14:44:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `branch`
-- ----------------------------
DROP TABLE IF EXISTS `branch`;
CREATE TABLE `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '部门的名称',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '日期',
  `finish` tinyint(4) DEFAULT '1' COMMENT '1代表存在，0代表删除',
  `finish_date` date DEFAULT NULL COMMENT '删除部门日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of branch
-- ----------------------------
INSERT INTO `branch` VALUES ('1', '现货1', '2016-09-08 22:49:57', '1', null);
INSERT INTO `branch` VALUES ('2', '现货2', '2016-09-08 22:50:03', '1', null);
INSERT INTO `branch` VALUES ('3', '现货3', '2016-09-08 22:50:10', '1', null);
INSERT INTO `branch` VALUES ('4', '对冲交易1', '2016-09-08 22:50:26', '1', null);
INSERT INTO `branch` VALUES ('5', '对冲交易2', '2016-09-08 22:50:44', '1', null);
INSERT INTO `branch` VALUES ('6', '期货投机1', '2016-09-08 22:51:20', '1', null);
INSERT INTO `branch` VALUES ('7', '海外现货1', '2016-09-08 22:51:28', '1', null);
INSERT INTO `branch` VALUES ('9', '现货4', '2016-09-08 22:51:50', '1', null);
INSERT INTO `branch` VALUES ('10', '超级管理员', '2016-09-08 23:03:53', '1', null);
INSERT INTO `branch` VALUES ('11', '测试部门', '2016-09-22 18:17:48', '1', null);
INSERT INTO `branch` VALUES ('12', '银行部门', '2016-09-27 18:34:42', '1', null);

-- ----------------------------
-- Table structure for `exchange_rate`
-- ----------------------------
DROP TABLE IF EXISTS `exchange_rate`;
CREATE TABLE `exchange_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `onshore_exchange_rate` float DEFAULT NULL COMMENT '在岸汇率',
  `offshore_exchange_rate` float DEFAULT NULL COMMENT '离岸汇率',
  `effect_date` date DEFAULT NULL COMMENT '汇率正真日期',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '汇率插入数据库日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of exchange_rate
-- ----------------------------
INSERT INTO `exchange_rate` VALUES ('1', '6.6908', '6.6906', '2016-09-16', '2016-09-16 00:00:00');
INSERT INTO `exchange_rate` VALUES ('2', '7', '8', '2016-09-24', '2016-09-24 00:00:00');
INSERT INTO `exchange_rate` VALUES ('4', '5.5555', '6.6666', '2016-09-29', '2016-09-23 17:08:52');
INSERT INTO `exchange_rate` VALUES ('5', '5.5555', '7.2156', '2016-09-13', '2016-09-23 17:09:38');
INSERT INTO `exchange_rate` VALUES ('6', '6.6666', '7.7777', '2016-09-30', '2016-09-23 17:23:14');
INSERT INTO `exchange_rate` VALUES ('7', '6.6666', '7.2156', '2016-09-25', '2016-09-24 18:52:29');
INSERT INTO `exchange_rate` VALUES ('8', '6.6666', '7.2156', '2016-09-26', '2016-09-26 11:01:20');
INSERT INTO `exchange_rate` VALUES ('9', '5.5555', '7.2156', '2016-09-27', '2016-09-27 18:30:59');
INSERT INTO `exchange_rate` VALUES ('10', '5', '5', '2016-09-15', '2016-09-27 20:01:45');

-- ----------------------------
-- Table structure for `perday_data_item`
-- ----------------------------
DROP TABLE IF EXISTS `perday_data_item`;
CREATE TABLE `perday_data_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '插入者ID',
  `branch_id` int(11) DEFAULT NULL COMMENT '数据所属部门',
  `project_id` int(11) DEFAULT NULL COMMENT '项目ID',
  `asset_money` double(20,4) DEFAULT NULL COMMENT '资产现金',
  `asset_product` double(20,4) DEFAULT NULL COMMENT '资产品',
  `finance_debt` double(20,4) DEFAULT NULL COMMENT '融资负债/授信',
  `receivable` double(20,4) DEFAULT NULL COMMENT '应收',
  `payable` double(20,4) DEFAULT NULL COMMENT '应付',
  `remark` text COMMENT '备注',
  `effect_date` date DEFAULT NULL COMMENT '日报生效日期',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '数据插入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of perday_data_item
-- ----------------------------
INSERT INTO `perday_data_item` VALUES ('1', '4', '3', '7', '0.0000', '41774475.3250', '0.0000', '363900.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('2', '4', '3', '8', '113470.3400', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('3', '4', '3', '9', '-32319914.1600', '9772226.0000', '32636976.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('4', '4', '3', '10', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('5', '4', '3', '11', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('6', '2', '1', '1', '0.0000', '3437332.4400', '0.0000', '892661.4127', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('7', '2', '1', '2', '2500184.6200', '0.0000', '0.0000', '0.0000', '0.0000', '加备注', '2016-09-16', '2016-09-27 15:28:07');
INSERT INTO `perday_data_item` VALUES ('8', '2', '1', '3', '-168784.9800', '330615.0200', '499400.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('9', '3', '2', '4', '0.0000', '10532233.5000', '0.0000', '363900.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('10', '3', '2', '5', '4158083.3200', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('11', '3', '2', '6', '-163014.6400', '626156.8000', '2584176.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('12', '5', '4', '12', '13135952.3100', '16599240.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('13', '5', '4', '13', '-67267.0700', '977218.0000', '2000000.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('14', '5', '4', '14', '-921493.5400', '1420882.0000', '3000000.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('15', '5', '4', '15', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('16', '6', '5', '16', '2566556.8700', '1771663.7500', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('17', '6', '5', '17', '696844.0400', '543020.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('18', '7', '6', '18', '2465000.0600', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('19', '8', '7', '19', '0.0000', '0.0000', '0.0000', '99000.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('20', '8', '7', '20', '4086886.8200', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('21', '8', '7', '21', '285695.3500', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('22', '10', '9', '22', '0.0000', '21900000.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('23', '10', '9', '23', '141596.2100', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('24', '10', '9', '24', '2385066.1800', '4870140.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16', '2016-09-23 15:15:19');
INSERT INTO `perday_data_item` VALUES ('30', '4', '3', '7', '1.0000', '2.0000', '3.0000', '4.0000', '5.0000', null, '2016-09-24', '2016-09-23 14:52:29');
INSERT INTO `perday_data_item` VALUES ('31', '4', '3', '8', '6.0000', '7.0000', '8.0000', '9.0000', '10.0000', '备注', '2016-09-24', '2016-09-23 14:52:29');
INSERT INTO `perday_data_item` VALUES ('32', '4', '3', '9', '1.0000', '0.0000', '0.0000', '5.0000', '0.0000', 'fuck', '2016-09-24', '2016-09-23 14:52:29');
INSERT INTO `perday_data_item` VALUES ('33', '4', '3', '10', '0.0000', '2.0000', '0.0000', '0.0000', '5.0000', '', '2016-09-24', '2016-09-23 14:52:29');
INSERT INTO `perday_data_item` VALUES ('34', '4', '3', '11', '0.0000', '0.0000', '4.0000', '0.0000', '0.0000', '', '2016-09-24', '2016-09-23 14:52:29');
INSERT INTO `perday_data_item` VALUES ('35', '4', '3', '7', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', null, '2016-09-24', '2016-09-24 13:45:25');
INSERT INTO `perday_data_item` VALUES ('36', '4', '3', '9', '1.0000', '0.0000', '0.0000', '1.0000', '0.0000', '今天数据', '2016-09-24', '2016-09-24 13:45:25');
INSERT INTO `perday_data_item` VALUES ('37', '4', '3', '10', '0.0000', '2.0000', '4.0000', '1.0000', '1.0000', '', '2016-09-24', '2016-09-24 13:45:25');
INSERT INTO `perday_data_item` VALUES ('38', '4', '3', '11', '0.0000', '0.0000', '3.0000', '0.0000', '1.0000', '', '2016-09-24', '2016-09-24 13:45:25');
INSERT INTO `perday_data_item` VALUES ('41', '2', '1', '1', '2.0000', '1.0000', '1.0000', '1.0000', '1.0000', 'fgf1', '2016-09-27', '2016-09-26 18:08:15');
INSERT INTO `perday_data_item` VALUES ('42', '2', '1', '3', '2.0000', '2.0000', '2.0000', '2.0000', '2.0000', '二块钱', '2016-09-27', '2016-09-26 11:37:48');
INSERT INTO `perday_data_item` VALUES ('51', '11', '1', '2', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '加备注', '2016-09-27', '2016-09-27 19:27:16');
INSERT INTO `perday_data_item` VALUES ('52', '11', '1', '26', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:50');
INSERT INTO `perday_data_item` VALUES ('53', '11', '2', '5', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:50');
INSERT INTO `perday_data_item` VALUES ('54', '11', '3', '8', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:50');
INSERT INTO `perday_data_item` VALUES ('55', '11', '7', '20', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:50');
INSERT INTO `perday_data_item` VALUES ('56', '11', '7', '21', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:50');
INSERT INTO `perday_data_item` VALUES ('57', '11', '9', '23', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-27', '2016-09-27 19:26:51');
INSERT INTO `perday_data_item` VALUES ('58', '11', '11', '25', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-27', '2016-09-27 19:26:51');
INSERT INTO `perday_data_item` VALUES ('59', '11', '1', '2', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:06');
INSERT INTO `perday_data_item` VALUES ('60', '11', '1', '26', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('61', '11', '2', '5', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('62', '11', '3', '8', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('63', '11', '7', '20', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('64', '11', '7', '21', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('65', '11', '9', '23', '5.0000', '5.0000', '5.0000', '5.0000', '5.0000', '', '2016-09-15', '2016-09-27 20:00:07');
INSERT INTO `perday_data_item` VALUES ('66', '11', '11', '25', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-15', '2016-09-27 20:00:07');

-- ----------------------------
-- Table structure for `project`
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '项目名称',
  `branch_id` int(11) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category` tinyint(4) DEFAULT NULL COMMENT '1：银行；2：期货；3：存货',
  `bank_category` tinyint(4) DEFAULT NULL COMMENT '0中国，1美国',
  `finish` tinyint(4) DEFAULT NULL COMMENT '1代表存在，0代表删除',
  `finish_date` date DEFAULT NULL COMMENT '删除项目日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project
-- ----------------------------
INSERT INTO `project` VALUES ('1', '现货库存结存', '1', '2016-09-14 17:37:54', '3', '0', null, null);
INSERT INTO `project` VALUES ('2', '工行账号（4548）', '1', '2016-09-14 17:38:38', '1', '0', null, null);
INSERT INTO `project` VALUES ('3', '建信期货', '1', '2016-09-14 17:39:05', '2', '0', null, null);
INSERT INTO `project` VALUES ('4', '现货库存结存', '2', '2016-09-14 17:42:06', '3', '0', null, null);
INSERT INTO `project` VALUES ('5', '建设川沙银行（5710）', '2', '2016-09-14 17:42:49', '1', '0', null, null);
INSERT INTO `project` VALUES ('6', '一德子账户02 套期', '2', '2016-09-14 17:43:37', '2', '0', null, null);
INSERT INTO `project` VALUES ('7', '现货库存结存', '3', '2016-09-14 17:44:16', '3', '0', null, null);
INSERT INTO `project` VALUES ('8', '招商银行（0602）', '3', '2016-09-14 17:45:03', '1', '0', null, null);
INSERT INTO `project` VALUES ('9', '一德子账户01 套期', '3', '2016-09-14 17:45:30', '2', '0', null, null);
INSERT INTO `project` VALUES ('10', '五矿期货 套期', '3', '2016-09-14 17:45:58', '2', '0', null, null);
INSERT INTO `project` VALUES ('11', '无锡不锈钢 套期', '3', '2016-09-14 17:46:18', '2', '0', null, null);
INSERT INTO `project` VALUES ('12', '境内 申万', '4', '2016-09-14 20:31:07', '2', '0', null, null);
INSERT INTO `project` VALUES ('13', '境外 FCS', '4', '2016-09-14 20:31:29', '2', '1', null, null);
INSERT INTO `project` VALUES ('14', '境外 ED&F', '4', '2016-09-14 20:31:44', '2', '1', null, null);
INSERT INTO `project` VALUES ('15', '境外 招商220子帐号', '4', '2016-09-16 11:10:15', '2', '1', null, null);
INSERT INTO `project` VALUES ('16', '境内 建信1号子帐号', '5', '2016-09-16 11:12:27', '2', '0', null, null);
INSERT INTO `project` VALUES ('17', '境外 招商221子帐号', '5', '2016-09-16 11:12:51', '2', '1', null, null);
INSERT INTO `project` VALUES ('18', '境内 长江期货', '6', '2016-09-16 11:14:18', '2', '0', null, null);
INSERT INTO `project` VALUES ('19', '现货库存结存', '7', '2016-09-16 11:20:36', '3', '1', null, null);
INSERT INTO `project` VALUES ('20', '申源法巴银行', '7', '2016-09-16 11:15:49', '1', '1', null, null);
INSERT INTO `project` VALUES ('21', 'YLI汇丰银行', '7', '2016-09-16 11:16:18', '1', '1', null, null);
INSERT INTO `project` VALUES ('22', '现货库存结存', '9', '2016-09-16 11:20:28', '3', '0', null, null);
INSERT INTO `project` VALUES ('23', '民生银行（9782）', '9', '2016-09-16 11:19:53', '1', '0', null, null);
INSERT INTO `project` VALUES ('24', '长江期货', '9', '2016-09-16 11:20:39', '2', '0', null, null);
INSERT INTO `project` VALUES ('25', '测试项目', '11', '2016-09-22 18:20:44', '1', '1', null, null);
INSERT INTO `project` VALUES ('26', '其他货币资金（工行承兑汇票）', '1', '2016-09-26 18:32:22', '1', '0', null, null);

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL COMMENT '部门的id',
  `branch_manager` tinyint(4) DEFAULT NULL COMMENT '1表示经理，0表示不是经理',
  `user_name` varchar(100) DEFAULT NULL COMMENT '账户姓名',
  `user_password` varchar(50) DEFAULT NULL COMMENT '账户密码',
  `power` int(11) DEFAULT NULL COMMENT '设想用数字来设置用户权限',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '用户创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '10', '1', '冯郭飞', '123456', '3', '2016-09-24 09:26:08');
INSERT INTO `user` VALUES ('2', '1', '0', '黄炳姜', '123456', '1', '2016-09-24 09:25:54');
INSERT INTO `user` VALUES ('3', '2', '0', '张雪良1', '123456', '1', '2016-09-24 09:25:56');
INSERT INTO `user` VALUES ('4', '3', '0', '张雪良2', '123456', '1', '2016-09-24 09:25:57');
INSERT INTO `user` VALUES ('5', '4', '0', '张雪良3', '123456', '1', '2016-09-24 09:25:59');
INSERT INTO `user` VALUES ('6', '5', '0', '汪旭琪', '123456', '1', '2016-09-24 09:26:00');
INSERT INTO `user` VALUES ('7', '6', '0', '张表强', '123456', '1', '2016-09-24 09:26:01');
INSERT INTO `user` VALUES ('8', '7', '0', '陈明杰1', '123456', '1', '2016-09-24 09:26:02');
INSERT INTO `user` VALUES ('9', '8', '0', '陈明杰2', '123456', '1', '2016-09-24 09:26:04');
INSERT INTO `user` VALUES ('10', '9', '0', '金坛', '123456', '1', '2016-09-24 09:26:06');
INSERT INTO `user` VALUES ('11', null, '0', '银行', '123456', '2', '2016-09-22 18:06:00');
INSERT INTO `user` VALUES ('12', '11', '1', '张三', '123456', '1', '2016-09-24 09:26:16');
