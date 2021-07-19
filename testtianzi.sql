/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50725
Source Host           : localhost:3306
Source Database       : testtianzi

Target Server Type    : MYSQL
Target Server Version : 50725
File Encoding         : 65001

Date: 2021-07-19 19:38:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sy_adminrole
-- ----------------------------
DROP TABLE IF EXISTS `sy_adminrole`;
CREATE TABLE `sy_adminrole` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `role_title` varchar(50) DEFAULT NULL COMMENT '角色名称',
  `role_type` tinyint(1) DEFAULT '0' COMMENT '类型 0:平台 1:揽货点 2:转运公司 3:报关行 4:口岸',
  `role_typeid` int(11) DEFAULT '0' COMMENT '类型id 0:平台 1:揽货点 2:转运公司 3:报关行 4口岸 相对应表的id',
  `role_status` tinyint(1) DEFAULT '1' COMMENT '角色状态 1启用 2禁用 禁用后该角色的管理员不能登录',
  `role_ruleid` text COMMENT '角色的权限id',
  `adminid` int(11) DEFAULT NULL,
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
-- Records of sy_adminrole
-- ----------------------------

-- ----------------------------
-- Table structure for sy_adminuser
-- ----------------------------
DROP TABLE IF EXISTS `sy_adminuser`;
CREATE TABLE `sy_adminuser` (
  `id` bigint(15) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '登录账号',
  `password` varchar(40) DEFAULT NULL COMMENT '登录密码',
  `loginip` varchar(255) DEFAULT NULL COMMENT '登录ip',
  `logintime` varchar(30) DEFAULT NULL COMMENT '商户登录时间',
  `create_time` varchar(30) DEFAULT NULL COMMENT '创建时间',
  `update_time` varchar(30) DEFAULT NULL COMMENT '更新时间',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号码',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1正常 2禁用',
  `roleid` int(11) DEFAULT '0' COMMENT '角色id',
  `ruleid` text COMMENT '权限id 角色的权限和该字段的和为该管理员的实际权限',
  `areaid` int(11) DEFAULT '0' COMMENT '受理点id',
  `meetingsiteid` smallint(5) DEFAULT '0' COMMENT '预约站点id meetingsite表id  0代表平台',
  `preadminids` text COMMENT '上级所有id的集合',
  `parentid` int(11) DEFAULT '0' COMMENT '上一级管理员id',
  `adminid` int(11) DEFAULT '0' COMMENT '操作的管理员',
  `issuperadmin` tinyint(1) DEFAULT '0' COMMENT '是否超级管理员 如果为1则不检验权限；0检验权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='后台管理员账号';

-- ----------------------------
-- Records of sy_adminuser
-- ----------------------------
INSERT INTO `sy_adminuser` VALUES ('1', 'tianzi', '550e1bafe077ff0b0b67f4e32f29d751', '127.0.0.1', '2021-07-15 11:12:30', null, '2021-07-15 11:12:30', null, '1', '0', null, '0', '0', null, '0', '0', '1');

-- ----------------------------
-- Table structure for sy_admin_action_log
-- ----------------------------
DROP TABLE IF EXISTS `sy_admin_action_log`;
CREATE TABLE `sy_admin_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(15) DEFAULT '0' COMMENT '管理员id 表adminuser的id',
  `method` varchar(50) DEFAULT NULL COMMENT '请求方法',
  `url` varchar(1000) DEFAULT NULL COMMENT '请求地址',
  `ip` varchar(30) DEFAULT NULL COMMENT '请求IP',
  `params` longtext COMMENT '请求参数序列化',
  `create_time` varchar(30) DEFAULT NULL COMMENT '新增时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='后台管理员操作记录';

-- ----------------------------
-- Records of sy_admin_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for sy_admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `sy_admin_login_log`;
CREATE TABLE `sy_admin_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(15) DEFAULT '0' COMMENT '管理员id adminuser表id',
  `ip` varchar(30) DEFAULT NULL COMMENT '执行操作所在ip',
  `create_time` varchar(30) DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='后台管理员登录记录表';

-- ----------------------------
-- Records of sy_admin_login_log
-- ----------------------------

-- ----------------------------
-- Table structure for sy_banner
-- ----------------------------
DROP TABLE IF EXISTS `sy_banner`;
CREATE TABLE `sy_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '' COMMENT 'banner标题',
  `img` varchar(255) DEFAULT '' COMMENT 'banner图片地址',
  `linkurl` varchar(255) DEFAULT '' COMMENT '跳转链接',
  `type` tinyint(1) DEFAULT '1' COMMENT '跳转类型 1无跳转 2跳小程序链接 3跳http链接',
  `isshow` tinyint(1) DEFAULT '1' COMMENT '是否显示 1是 2否',
  `position` tinyint(1) DEFAULT '1' COMMENT '幻灯片位置 1首页',
  `sortby` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` varchar(30) DEFAULT NULL COMMENT '创建时间',
  `update_time` varchar(30) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='banner表';

-- ----------------------------
-- Records of sy_banner
-- ----------------------------

-- ----------------------------
-- Table structure for sy_broadband
-- ----------------------------
DROP TABLE IF EXISTS `sy_broadband`;
CREATE TABLE `sy_broadband` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT '1' COMMENT '类型 1移动 2联通 3电信',
  `keyaccount` varchar(255) DEFAULT NULL,
  `keypassword` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1启用 2禁用 只有启用状态的账号才会分配',
  `isuse` tinyint(1) DEFAULT '2' COMMENT '是否已使用 1已使用 2待使用',
  `use_time` varchar(30) DEFAULT NULL COMMENT '使用时间',
  `enable_time` varchar(30) DEFAULT NULL,
  `unable_time` varchar(30) DEFAULT NULL,
  `start_time` varchar(30) DEFAULT NULL COMMENT '开始时间',
  `end_time` varchar(30) DEFAULT NULL COMMENT '结束时间',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='宽带账号';

-- ----------------------------
-- Records of sy_broadband
-- ----------------------------

-- ----------------------------
-- Table structure for sy_goods
-- ----------------------------
DROP TABLE IF EXISTS `sy_goods`;
CREATE TABLE `sy_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_title` varchar(255) DEFAULT NULL COMMENT '标题',
  `goods_img` varchar(255) DEFAULT NULL COMMENT '封面图',
  `goods_price` int(10) DEFAULT '0' COMMENT '售价',
  `goods_content` text COMMENT '详情',
  `goods_status` tinyint(1) DEFAULT '1' COMMENT '状态 1上架 2下架',
  `goods_sortby` int(11) DEFAULT '0',
  `goods_onshelf_time` varchar(30) DEFAULT NULL COMMENT '上架时间',
  `goods_offshelf_time` varchar(30) DEFAULT NULL COMMENT '下架时间',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品套餐表';

-- ----------------------------
-- Records of sy_goods
-- ----------------------------

-- ----------------------------
-- Table structure for sy_orders
-- ----------------------------
DROP TABLE IF EXISTS `sy_orders`;
CREATE TABLE `sy_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `school_id` smallint(5) DEFAULT '0' COMMENT '学校(院区)id',
  `goods_id` int(11) DEFAULT '0',
  `broadband_id` int(11) DEFAULT '0' COMMENT '宽带账号id',
  `orderno` varchar(60) DEFAULT '' COMMENT '订单号',
  `payno` varchar(60) DEFAULT '' COMMENT '自定义支付单号',
  `realname` varchar(50) DEFAULT '' COMMENT '姓名',
  `mobile` varchar(30) DEFAULT '' COMMENT '联系电话',
  `idcardnum` varchar(30) DEFAULT NULL COMMENT '身份证号码',
  `department` varchar(100) DEFAULT NULL COMMENT '院系',
  `studentnumber` varchar(50) DEFAULT NULL COMMENT '学号',
  `address` varchar(255) DEFAULT NULL COMMENT '宿舍地址',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1待支付 2已支付 3已发放 4已取消 5取消中',
  `finish_time` varchar(30) DEFAULT NULL COMMENT '宽带账号发放时间',
  `money` int(11) DEFAULT '0' COMMENT '应付金额 单位分',
  `discount_money` int(11) DEFAULT '0' COMMENT '优惠金额',
  `pay_money` int(11) DEFAULT '0' COMMENT '支付金额',
  `ispay` tinyint(1) DEFAULT '2' COMMENT '是否支付 1是 2否 3支付失败',
  `pay_time` varchar(30) DEFAULT NULL COMMENT '支付时间',
  `pay_way` tinyint(1) DEFAULT '1' COMMENT '支付方式 1微信 2支付宝',
  `pay_tradeno` varchar(100) DEFAULT NULL COMMENT '支付流水号(微信或者支付宝)',
  `isrefund` tinyint(1) DEFAULT '2' COMMENT '是否有退款 1是 2否',
  `refund_money` int(11) DEFAULT '0' COMMENT '退款金额',
  `refund_time` varchar(30) DEFAULT NULL COMMENT '退款时间',
  `refund_item` text COMMENT '退款明细',
  `isfirst` tinyint(1) DEFAULT '2' COMMENT '是否首次预约 1是 2否',
  `isdel` tinyint(1) DEFAULT '2' COMMENT '删除标志 1是 2否',
  `del_time` varchar(30) DEFAULT NULL,
  `close_time` varchar(30) DEFAULT NULL COMMENT '关闭时间',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='预约订单表';

-- ----------------------------
-- Records of sy_orders
-- ----------------------------

-- ----------------------------
-- Table structure for sy_orders_paynotice
-- ----------------------------
DROP TABLE IF EXISTS `sy_orders_paynotice`;
CREATE TABLE `sy_orders_paynotice` (
  `user_id` int(11) DEFAULT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `orderno` varchar(40) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `content` text,
  `type` tinyint(1) DEFAULT '1' COMMENT '回执类型 1支付回执 2退款回执',
  `pay_path` tinyint(1) DEFAULT NULL COMMENT '支付方式 1微信 2支付宝',
  `create_time` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付回调信息表';

-- ----------------------------
-- Records of sy_orders_paynotice
-- ----------------------------

-- ----------------------------
-- Table structure for sy_orders_refund
-- ----------------------------
DROP TABLE IF EXISTS `sy_orders_refund`;
CREATE TABLE `sy_orders_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `openid` varchar(100) DEFAULT '' COMMENT '用户openid',
  `orders_id` int(11) DEFAULT '0',
  `orderno` varchar(100) DEFAULT NULL COMMENT '原订单号 订单表的订单号',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT '支付流水号',
  `orders_money` int(11) DEFAULT '0' COMMENT '订单已支付金额',
  `refund_orderno` varchar(100) DEFAULT NULL COMMENT '退款订单号',
  `refund_money` int(11) DEFAULT '0' COMMENT '实际退款金额',
  `refund_applymoney` int(11) DEFAULT '0' COMMENT '申请退款金额',
  `apply_time` varchar(30) DEFAULT NULL COMMENT '申请时间',
  `deal_time` varchar(30) DEFAULT NULL COMMENT '处理时间',
  `refund_status` tinyint(1) DEFAULT '1' COMMENT '退款状态 1待处理 2拒绝 3通过 4处理中',
  `remark` varchar(255) DEFAULT NULL COMMENT '拒绝说明',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款记录表';

-- ----------------------------
-- Records of sy_orders_refund
-- ----------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- ----------------------------
-- Records of sy_rule
-- ----------------------------

-- ----------------------------
-- Table structure for sy_school
-- ----------------------------
DROP TABLE IF EXISTS `sy_school`;
CREATE TABLE `sy_school` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `sortby` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1启用 2禁用',
  `enable_time` varchar(30) DEFAULT NULL COMMENT '启用时间',
  `unable_time` varchar(30) DEFAULT NULL COMMENT '禁用时间',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='校区表';

-- ----------------------------
-- Records of sy_school
-- ----------------------------
INSERT INTO `sy_school` VALUES ('1', '佛山科学技术学院仙溪校区', '广东省佛山市南海区狮山镇广云路33号', '0', '1', '2021-07-19 17:17:11', '2021-07-16 16:06:45', '2021-07-15 15:32:35', '2021-07-19 17:17:11');
INSERT INTO `sy_school` VALUES ('2', '佛山科学技术学院江湾校区', '广东省佛山市禅城区江湾一路18号   2', '0', '1', '2021-07-15 15:41:13', null, '2021-07-15 15:41:13', '2021-07-16 15:38:04');

-- ----------------------------
-- Table structure for sy_user
-- ----------------------------
DROP TABLE IF EXISTS `sy_user`;
CREATE TABLE `sy_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT '' COMMENT '登录凭证',
  `openid` varchar(100) DEFAULT NULL COMMENT '小程序openid',
  `nickname` varchar(255) DEFAULT '' COMMENT '小程序昵称',
  `avatar` varchar(255) DEFAULT '' COMMENT '小程序头像',
  `sex` varchar(10) DEFAULT '',
  `country` varchar(50) DEFAULT '',
  `province` varchar(50) DEFAULT '',
  `city` varchar(50) DEFAULT NULL,
  `realname` varchar(50) DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(30) DEFAULT '' COMMENT '联系电话',
  `idcardnum` varchar(30) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL COMMENT '院系',
  `studentnumber` varchar(50) DEFAULT NULL COMMENT '学号',
  `address` varchar(255) DEFAULT NULL COMMENT '宿舍地址',
  `create_time` varchar(30) DEFAULT NULL,
  `update_time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of sy_user
-- ----------------------------

-- ----------------------------
-- Table structure for sy_verifycode
-- ----------------------------
DROP TABLE IF EXISTS `sy_verifycode`;
CREATE TABLE `sy_verifycode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(50) DEFAULT NULL COMMENT '手机号',
  `code` varchar(20) DEFAULT NULL COMMENT '验证码',
  `type` tinyint(1) DEFAULT '1' COMMENT '验证码类型 1预约名医',
  `isread` tinyint(1) DEFAULT '2' COMMENT '是否已使用 1是 2否',
  `send_time` varchar(30) DEFAULT NULL COMMENT '发送时间',
  `use_time` varchar(30) DEFAULT NULL COMMENT '使用时间',
  `create_time` varchar(30) DEFAULT NULL COMMENT '创建时间',
  `update_time` varchar(30) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='验证码表';

-- ----------------------------
-- Records of sy_verifycode
-- ----------------------------
