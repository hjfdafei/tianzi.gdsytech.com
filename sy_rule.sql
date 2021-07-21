/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50725
Source Host           : localhost:3306
Source Database       : tianzi_gdsy

Target Server Type    : MYSQL
Target Server Version : 50725
File Encoding         : 65001

Date: 2021-07-21 17:18:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sy_rule
-- ----------------------------
DROP TABLE IF EXISTS `sy_rule`;
CREATE TABLE `sy_rule` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `rule_module` varchar(50) DEFAULT 'sytechadmin' COMMENT '模块名称',
  `rule_controller` varchar(50) DEFAULT '' COMMENT '控制器名称',
  `rule_action` varchar(50) DEFAULT '' COMMENT '方法名称',
  `parentid` mediumint(8) DEFAULT NULL COMMENT '上级id',
  `rule_title` varchar(50) DEFAULT NULL COMMENT '标题',
  `rule_sort` mediumint(8) DEFAULT '0' COMMENT '排序  值越大排在越前',
  `rule_ismenu` tinyint(1) DEFAULT '2' COMMENT '是否在左侧菜单显示 1是 2否',
  `rule_class` varchar(30) DEFAULT NULL COMMENT 'css样式类',
  `rule_isshow` tinyint(1) DEFAULT '1' COMMENT '是否显示 1是 2否',
  `create_time` varchar(30) DEFAULT NULL COMMENT '创建时间',
  `update_time` varchar(30) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- ----------------------------
-- Records of sy_rule
-- ----------------------------
INSERT INTO `sy_rule` VALUES ('1', 'sytechadmin', '#', '#', '0', '管理员管理', '0', '1', 'fa-address-card', '1', '2021-07-21 11:46:55', null);
INSERT INTO `sy_rule` VALUES ('2', 'sytechadmin', 'Adminuser', 'adminuser_list', '1', '管理员列表', '0', '1', '', '1', '2021-07-21 11:47:47', null);
INSERT INTO `sy_rule` VALUES ('3', 'sytechadmin', 'Adminuser', 'adminuser_add', '1', '添加管理员', '0', '2', '', '1', '2021-07-21 11:50:06', null);
INSERT INTO `sy_rule` VALUES ('4', 'sytechadmin', 'Adminuser', 'adminuser_edit', '1', '编辑管理员', '0', '2', '', '1', '2021-07-21 11:50:30', null);
INSERT INTO `sy_rule` VALUES ('5', 'sytechadmin', 'Adminuser', 'adminuser_open', '1', '启用管理员', '0', '2', '', '1', '2021-07-21 11:58:10', null);
INSERT INTO `sy_rule` VALUES ('6', 'sytechadmin', 'Adminuser', 'adminuser_close', '1', '禁用管理员', '0', '2', '', '1', '2021-07-21 11:58:34', null);
INSERT INTO `sy_rule` VALUES ('7', 'sytechadmin', 'Adminuser', 'adminuser_assign', '1', '分配管理员权限', '0', '2', '', '1', '2021-07-21 11:59:03', null);
INSERT INTO `sy_rule` VALUES ('8', 'sytechadmin', 'Adminuser', 'adminuser_cancelassign', '1', '取消管理员权限', '0', '2', '', '1', '2021-07-21 11:59:34', null);
INSERT INTO `sy_rule` VALUES ('9', 'sytechadmin', 'Adminuser', 'adminuser_del', '1', '删除管理员', '0', '2', '', '1', '2021-07-21 12:00:03', null);
INSERT INTO `sy_rule` VALUES ('10', 'sytechadmin', '#', '#', '0', '角色管理', '0', '1', 'fa-sitemap', '1', '2021-07-21 12:01:29', null);
INSERT INTO `sy_rule` VALUES ('11', 'sytechadmin', 'Adminrole', 'adminrole_list', '10', '角色列表', '0', '1', '', '1', '2021-07-21 12:01:58', null);
INSERT INTO `sy_rule` VALUES ('12', 'sytechadmin', 'Adminrole', 'adminrole_add', '10', '添加角色', '0', '2', '', '1', '2021-07-21 12:02:24', null);
INSERT INTO `sy_rule` VALUES ('13', 'sytechadmin', 'Adminrole', 'adminrole_edit', '10', '编辑角色', '0', '2', '', '1', '2021-07-21 12:02:47', null);
INSERT INTO `sy_rule` VALUES ('14', 'sytechadmin', 'Adminrole', 'adminrole_open', '10', '启用角色', '0', '2', '', '1', '2021-07-21 12:03:08', null);
INSERT INTO `sy_rule` VALUES ('15', 'sytechadmin', 'Adminrole', 'adminrole_close', '10', '禁用角色', '0', '2', '', '1', '2021-07-21 12:03:25', null);
INSERT INTO `sy_rule` VALUES ('16', 'sytechadmin', 'Adminrole', 'adminrole_assign', '10', '分配角色权限', '0', '2', '', '1', '2021-07-21 12:03:46', null);
INSERT INTO `sy_rule` VALUES ('17', 'sytechadmin', 'Adminrole', 'adminrole_cancelassign', '10', '取消角色权限', '0', '2', '', '1', '2021-07-21 12:04:07', null);
INSERT INTO `sy_rule` VALUES ('18', 'sytechadmin', 'Adminrole', 'adminrole_del', '10', '删除角色', '0', '2', '', '1', '2021-07-21 12:04:30', null);
INSERT INTO `sy_rule` VALUES ('19', 'sytechadmin', '#', '#', '0', 'Banner图片管理', '0', '1', 'fa-picture-o', '1', '2021-07-21 12:05:34', null);
INSERT INTO `sy_rule` VALUES ('20', 'sytechadmin', 'Banner', 'banner_list', '19', 'Banner列表', '0', '1', '', '1', '2021-07-21 12:05:59', null);
INSERT INTO `sy_rule` VALUES ('21', 'sytechadmin', 'Banner', 'banner_add', '19', '添加Banner', '0', '2', '', '1', '2021-07-21 12:06:21', null);
INSERT INTO `sy_rule` VALUES ('22', 'sytechadmin', 'Banner', 'banner_edit', '19', '编辑Banner', '0', '2', '', '1', '2021-07-21 12:06:45', null);
INSERT INTO `sy_rule` VALUES ('23', 'sytechadmin', 'Banner', 'banner_show', '19', '启用Banner', '0', '2', '', '1', '2021-07-21 12:07:10', null);
INSERT INTO `sy_rule` VALUES ('24', 'sytechadmin', 'Banner', 'banner_hide', '19', '禁用Banner', '0', '2', '', '1', '2021-07-21 12:07:31', null);
INSERT INTO `sy_rule` VALUES ('25', 'sytechadmin', 'Banner', 'banner_del', '19', '删除Banner', '0', '2', '', '1', '2021-07-21 12:07:49', null);
INSERT INTO `sy_rule` VALUES ('26', 'sytechadmin', '#', '#', '0', '校区管理', '0', '1', 'fa-life-ring', '1', '2021-07-21 12:08:24', null);
INSERT INTO `sy_rule` VALUES ('27', 'sytechadmin', 'School', 'school_list', '26', '校区列表', '0', '1', '', '1', '2021-07-21 12:08:53', null);
INSERT INTO `sy_rule` VALUES ('28', 'sytechadmin', 'School', 'school_add', '26', '添加校区', '0', '2', '', '1', '2021-07-21 12:09:16', null);
INSERT INTO `sy_rule` VALUES ('29', 'sytechadmin', 'School', 'school_edit', '26', '编辑校区', '0', '2', '', '1', '2021-07-21 12:09:33', null);
INSERT INTO `sy_rule` VALUES ('30', 'sytechadmin', 'School', 'school_show', '26', '启用校区', '0', '2', '', '1', '2021-07-21 12:09:53', null);
INSERT INTO `sy_rule` VALUES ('31', 'sytechadmin', 'School', 'school_hide', '26', '禁用校区', '0', '2', '', '1', '2021-07-21 12:10:10', null);
INSERT INTO `sy_rule` VALUES ('32', 'sytechadmin', 'School', 'school_del', '26', '删除校区', '0', '2', '', '1', '2021-07-21 12:10:27', null);
INSERT INTO `sy_rule` VALUES ('33', 'sytechadmin', '#', '#', '0', '用户管理', '0', '1', 'fa-user-circle', '1', '2021-07-21 12:11:02', null);
INSERT INTO `sy_rule` VALUES ('34', 'sytechadmin', 'User', 'user_list', '33', '用户列表', '0', '1', '', '1', '2021-07-21 12:11:20', null);
INSERT INTO `sy_rule` VALUES ('35', 'sytechadmin', 'User', 'user_edit', '33', '编辑用户', '0', '2', '', '1', '2021-07-21 12:11:37', null);
INSERT INTO `sy_rule` VALUES ('36', 'sytechadmin', '#', '#', '0', '宽带套餐管理', '0', '1', 'fa-asterisk', '1', '2021-07-21 12:13:26', null);
INSERT INTO `sy_rule` VALUES ('37', 'sytechadmin', 'Goods', 'goods_list', '36', '宽带套餐列表', '0', '1', '', '1', '2021-07-21 12:13:54', null);
INSERT INTO `sy_rule` VALUES ('38', 'sytechadmin', 'Goods', 'goods_add', '36', '添加宽带套餐', '0', '2', '', '1', '2021-07-21 12:14:18', null);
INSERT INTO `sy_rule` VALUES ('39', 'sytechadmin', 'Goods', 'goods_edit', '36', '编辑宽带套餐', '0', '2', '', '1', '2021-07-21 12:14:37', null);
INSERT INTO `sy_rule` VALUES ('40', 'sytechadmin', 'Goods', 'goods_show', '36', '上架宽带套餐', '0', '2', '', '1', '2021-07-21 12:14:57', null);
INSERT INTO `sy_rule` VALUES ('41', 'sytechadmin', 'Goods', 'goods_hide', '36', '下架宽带套餐', '0', '2', '', '1', '2021-07-21 12:15:22', null);
INSERT INTO `sy_rule` VALUES ('42', 'sytechadmin', 'Goods', 'goods_del', '36', '删除宽带套餐', '0', '2', '', '1', '2021-07-21 12:15:42', null);
INSERT INTO `sy_rule` VALUES ('43', 'sytechadmin', '#', '#', '0', '宽带账号管理', '0', '1', 'fa-futbol-o', '1', '2021-07-21 12:16:11', null);
INSERT INTO `sy_rule` VALUES ('44', 'sytechadmin', 'Broadband', 'broadband_list', '43', '宽带账号列表', '0', '1', '', '1', '2021-07-21 12:16:38', null);
INSERT INTO `sy_rule` VALUES ('45', 'sytechadmin', 'Broadband', 'broadband_add', '43', '添加宽带账号', '0', '2', '', '1', '2021-07-21 12:31:53', null);
INSERT INTO `sy_rule` VALUES ('46', 'sytechadmin', 'Broadband', 'broadband_edit', '43', '编辑宽带账号', '0', '2', '', '1', '2021-07-21 12:32:17', null);
INSERT INTO `sy_rule` VALUES ('47', 'sytechadmin', 'Broadband', 'broadband_import', '43', '导入宽带账号', '0', '2', '', '1', '2021-07-21 12:32:43', null);
INSERT INTO `sy_rule` VALUES ('48', 'sytechadmin', 'Broadband', 'broadband_show', '43', '启用宽带账号', '0', '2', '', '1', '2021-07-21 14:23:03', null);
INSERT INTO `sy_rule` VALUES ('49', 'sytechadmin', 'Broadband', 'broadband_hide', '43', '禁用宽带账号', '0', '2', '', '1', '2021-07-21 14:23:28', null);
INSERT INTO `sy_rule` VALUES ('50', 'sytechadmin', 'Broadband', 'broadband_del', '43', '删除宽带账号', '0', '2', '', '1', '2021-07-21 14:23:56', null);
INSERT INTO `sy_rule` VALUES ('51', 'sytechadmin', '#', '#', '0', '订单管理', '0', '1', 'fa-first-order', '1', '2021-07-21 14:25:00', null);
INSERT INTO `sy_rule` VALUES ('52', 'sytechadmin', 'Orders', 'orders_list', '51', '订单列表', '0', '1', '', '1', '2021-07-21 14:26:01', null);
INSERT INTO `sy_rule` VALUES ('53', 'sytechadmin', 'Orders', 'orders_edit', '51', '修改订单', '0', '2', '', '1', '2021-07-21 14:26:35', null);
INSERT INTO `sy_rule` VALUES ('54', 'sytechadmin', 'Orders', 'orders_detail', '51', '订单详情', '0', '2', '', '1', '2021-07-21 14:27:01', null);
INSERT INTO `sy_rule` VALUES ('55', 'sytechadmin', 'Orders', 'orders_setbroadband', '51', '分配宽带账号', '0', '2', '', '1', '2021-07-21 14:28:50', null);
INSERT INTO `sy_rule` VALUES ('56', 'sytechadmin', 'Orders', 'orders_clearbroadband', '51', '清空宽带账号', '0', '2', '', '1', '2021-07-21 14:29:18', null);
INSERT INTO `sy_rule` VALUES ('57', 'sytechadmin', 'Orders', 'orders_settime', '51', '设置宽带时间', '0', '2', '', '1', '2021-07-21 14:29:41', null);
INSERT INTO `sy_rule` VALUES ('58', 'sytechadmin', 'Orders', 'orders_export', '51', '导出订单', '0', '2', '', '1', '2021-07-21 14:30:04', null);
INSERT INTO `sy_rule` VALUES ('59', 'sytechadmin', 'Orders', 'orders_del', '51', '删除订单', '0', '2', '', '1', '2021-07-21 14:30:31', null);
INSERT INTO `sy_rule` VALUES ('60', 'sytechadmin', 'Index', 'setting', '0', '系统设置', '0', '1', 'fa-cog', '1', '2021-07-21 14:49:37', null);
