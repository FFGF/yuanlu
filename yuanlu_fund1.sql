/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : yuanlu_fund1

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-09-19 20:56:08
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of branch
-- ----------------------------
INSERT INTO `branch` VALUES ('1', '现货1', '2016-09-08 22:49:57');
INSERT INTO `branch` VALUES ('2', '现货2', '2016-09-08 22:50:03');
INSERT INTO `branch` VALUES ('3', '现货3', '2016-09-08 22:50:10');
INSERT INTO `branch` VALUES ('4', '对冲交易1', '2016-09-08 22:50:26');
INSERT INTO `branch` VALUES ('5', '对冲交易2', '2016-09-08 22:50:44');
INSERT INTO `branch` VALUES ('6', '期货投机1', '2016-09-08 22:51:20');
INSERT INTO `branch` VALUES ('7', '海外现货1', '2016-09-08 22:51:28');
INSERT INTO `branch` VALUES ('8', '银行资金1', '2016-09-08 22:51:40');
INSERT INTO `branch` VALUES ('9', '现货4', '2016-09-08 22:51:50');
INSERT INTO `branch` VALUES ('10', '超级管理员', '2016-09-08 23:03:53');

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
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of perday_data_item
-- ----------------------------
INSERT INTO `perday_data_item` VALUES ('1', '4', '3', '7', '0.0000', '41774475.3250', '0.0000', '363900.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('2', '4', '3', '8', '113470.3400', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('3', '4', '3', '9', '-32319914.1600', '9772226.0000', '32636976.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('4', '4', '3', '10', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('5', '4', '3', '11', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('6', '2', '1', '1', '0.0000', '3437332.4400', '0.0000', '892661.4127', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('7', '2', '1', '2', '2500184.6200', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('8', '2', '1', '3', '-168784.9800', '330615.0200', '499400.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('9', '3', '2', '4', '0.0000', '10532233.5000', '0.0000', '363900.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('10', '3', '2', '5', '4158083.3200', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('11', '3', '2', '6', '-163014.6400', '626156.8000', '2584176.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('12', '5', '4', '12', '13135952.3100', '16599240.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('13', '5', '4', '13', '-67267.0700', '977218.0000', '2000000.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('14', '5', '4', '14', '-921493.5400', '1420882.0000', '3000000.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('15', '5', '4', '15', '0.0000', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('16', '6', '5', '16', '2566556.8700', '1771663.7500', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('17', '6', '5', '17', '696844.0400', '543020.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('18', '7', '6', '18', '2465000.0600', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('19', '8', '7', '19', '0.0000', '0.0000', '0.0000', '99000.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('20', '8', '7', '20', '4086886.8200', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('21', '8', '7', '21', '285695.3500', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('22', '10', '9', '22', '0.0000', '21900000.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('23', '10', '9', '23', '141596.2100', '0.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');
INSERT INTO `perday_data_item` VALUES ('24', '10', '9', '24', '2385066.1800', '4870140.0000', '0.0000', '0.0000', '0.0000', '', '2016-09-16 11:54:00');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project
-- ----------------------------
INSERT INTO `project` VALUES ('1', '现货库存结存', '1', '2016-09-14 17:37:54', '3', '0');
INSERT INTO `project` VALUES ('2', '工行账号（4548）', '1', '2016-09-14 17:38:38', '1', '0');
INSERT INTO `project` VALUES ('3', '建信期货', '1', '2016-09-14 17:39:05', '2', '0');
INSERT INTO `project` VALUES ('4', '现货库存结存', '2', '2016-09-14 17:42:06', '3', '0');
INSERT INTO `project` VALUES ('5', '建设川沙银行（5710）', '2', '2016-09-14 17:42:49', '1', '0');
INSERT INTO `project` VALUES ('6', '一德子账户02 套期', '2', '2016-09-14 17:43:37', '2', '0');
INSERT INTO `project` VALUES ('7', '现货库存结存', '3', '2016-09-14 17:44:16', '3', '0');
INSERT INTO `project` VALUES ('8', '招商银行（0602）', '3', '2016-09-14 17:45:03', '1', '0');
INSERT INTO `project` VALUES ('9', '一德子账户01 套期', '3', '2016-09-14 17:45:30', '2', '0');
INSERT INTO `project` VALUES ('10', '五矿期货 套期', '3', '2016-09-14 17:45:58', '2', '0');
INSERT INTO `project` VALUES ('11', '无锡不锈钢 套期', '3', '2016-09-14 17:46:18', '2', '0');
INSERT INTO `project` VALUES ('12', '境内 申万', '4', '2016-09-14 20:31:07', '2', '0');
INSERT INTO `project` VALUES ('13', '境外 FCS', '4', '2016-09-14 20:31:29', '2', '1');
INSERT INTO `project` VALUES ('14', '境外 ED&F', '4', '2016-09-14 20:31:44', '2', '1');
INSERT INTO `project` VALUES ('15', '境外 招商220子帐号', '4', '2016-09-16 11:10:15', '2', '1');
INSERT INTO `project` VALUES ('16', '境内 建信1号子帐号', '5', '2016-09-16 11:12:27', '2', '0');
INSERT INTO `project` VALUES ('17', '境外 招商221子帐号', '5', '2016-09-16 11:12:51', '2', '1');
INSERT INTO `project` VALUES ('18', '境内 长江期货', '6', '2016-09-16 11:14:18', '2', '0');
INSERT INTO `project` VALUES ('19', '现货库存结存', '7', '2016-09-16 11:20:36', '3', '1');
INSERT INTO `project` VALUES ('20', '申源法巴银行', '7', '2016-09-16 11:15:49', '1', '1');
INSERT INTO `project` VALUES ('21', 'YLI汇丰银行', '7', '2016-09-16 11:16:18', '1', '1');
INSERT INTO `project` VALUES ('22', '现货库存结存', '9', '2016-09-16 11:20:28', '3', '0');
INSERT INTO `project` VALUES ('23', '民生银行（9782）', '9', '2016-09-16 11:19:53', '1', '0');
INSERT INTO `project` VALUES ('24', '长江期货', '9', '2016-09-16 11:20:39', '2', '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '10', '1', '冯郭飞', 'c2a5421a20b8d6a261151baf719cdae7', '1', '2016-09-08 22:54:40');
INSERT INTO `user` VALUES ('2', '1', '0', '黄炳姜', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:23');
INSERT INTO `user` VALUES ('3', '2', '0', '张雪良1', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:24');
INSERT INTO `user` VALUES ('4', '3', '0', '张雪良2', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:26');
INSERT INTO `user` VALUES ('5', '4', '0', '张雪良3', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:27');
INSERT INTO `user` VALUES ('6', '5', '0', '汪旭琪', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:29');
INSERT INTO `user` VALUES ('7', '6', '0', '张表强', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:30');
INSERT INTO `user` VALUES ('8', '7', '0', '陈明杰1', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:33');
INSERT INTO `user` VALUES ('9', '8', '0', '陈明杰2', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:34');
INSERT INTO `user` VALUES ('10', '9', '0', '金坛', 'c2a5421a20b8d6a261151baf719cdae7', '3', '2016-09-14 20:03:38');
INSERT INTO `user` VALUES ('11', null, '0', '银行', 'c2a5421a20b8d6a261151baf719cdae7', '2', '2016-09-19 12:12:38');
