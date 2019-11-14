-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014-08-29 22:11:13
-- 服务器版本: 5.6.16-log
-- PHP 版本: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `product_tutu`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(120) DEFAULT NULL COMMENT '登录用户名',
  `passwd` varchar(32) DEFAULT NULL COMMENT '登录密码，md5+salt加密',
  `nickname` varchar(120) DEFAULT NULL COMMENT '昵称',
  `time_insert` int(10) DEFAULT NULL COMMENT '注册时间',
  `time_update` int(10) DEFAULT NULL COMMENT '上次登录时间',
  `login_ip` varchar(100) DEFAULT NULL COMMENT '上次登录IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统管理员' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册ID',
  `user_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT '相册名',
  `name_en` varchar(100) DEFAULT NULL COMMENT '相册英文名',
  `description` longtext COMMENT '描述',
  `avatar` varchar(200) DEFAULT NULL COMMENT '封面',
  `status` tinyint(1) DEFAULT NULL COMMENT '相册状态，0-私密，1-朋友可见，2-公开',
  `time_insert` int(10) DEFAULT NULL COMMENT '插入时间',
  `time_update` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='相册' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `meta` varchar(100) NOT NULL COMMENT '配置键名',
  `value` longtext COMMENT '配置值',
  `description` varchar(300) DEFAULT NULL COMMENT '描述',
  `autoload` tinyint(1) DEFAULT '0' COMMENT '是否自动加载',
  PRIMARY KEY (`meta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统配置';

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`meta`, `value`, `description`, `autoload`) VALUES
('app_name', '图图世界', '应用的中文名', 1),
('app_version', '0.3.001', '管理端版本', 1),
('app_name_en', 'tutu', '应用的英文名', 1);

-- --------------------------------------------------------

--
-- 表的结构 `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `album_id` int(11) unsigned DEFAULT NULL COMMENT '相册ID',
  `name` varchar(200) DEFAULT NULL COMMENT '名称',
  `description` longtext COMMENT '描述',
  `avatar` varchar(200) DEFAULT NULL COMMENT '所在路径（相对）',
  `longitude` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL COMMENT '纬度',
  `position` varchar(200) DEFAULT NULL COMMENT '根据经纬度定位得到',
  `tag_ids` varchar(200) DEFAULT NULL COMMENT '标签列表，逗号分开',
  `time_insert` int(10) DEFAULT NULL COMMENT '插入时间',
  `time_update` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`album_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='图片' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='标签' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsupermap2012signed NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(120) DEFAULT NULL COMMENT '登录用户名',
  `passwd` varchar(32) DEFAULT NULL COMMENT '登录密码，md5',
  `nickname` varchar(120) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(200) DEFAULT NULL COMMENT '用户图像',
  `time_insert` int(10) DEFAULT NULL COMMENT '注册时间',
  `time_update` int(10) DEFAULT NULL COMMENT '上次登录时间',
  `login_ip` varchar(100) DEFAULT NULL COMMENT '上次登录IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统管理员' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_friend`
--

CREATE TABLE IF NOT EXISTS `user_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_from` int(11) NOT NULL,
  `user_id_to` int(11) NOT NULL,
  `type` varchar(200) DEFAULT NULL COMMENT '类型',
  `content` text COMMENT '内容',
  `read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `time_insert` decimal(13,3) DEFAULT NULL COMMENT '插入时间',
  PRIMARY KEY (`id`),
  KEY `dt_insert` (`time_insert`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `user_id_from_2` (`user_id_from`) USING BTREE,
  KEY `user_id_to` (`user_id_to`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户朋友相关' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
