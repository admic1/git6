-- phpMyAdmin SQL Dump
-- version 4.0.0
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成日時: 2013 年 6 月 13 日 11:34
-- サーバのバージョン: 5.1.69
-- PHP のバージョン: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- データベース: `twitter`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `louise`
--

DROP TABLE IF EXISTS `louise`;
CREATE TABLE IF NOT EXISTS `louise` (
  `no` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'No',
  `twitter_id` bigint(20) NOT NULL COMMENT 'Twitter ID',
  `token` varchar(256) NOT NULL COMMENT 'アクセストークン',
  `token_sec` varchar(256) NOT NULL COMMENT 'アクセストークンシークレット',
  `ip` varchar(20) NOT NULL COMMENT 'IPアドレス',
  `host` varchar(1024) NOT NULL COMMENT 'リモートホスト',
  `agent` varchar(1024) NOT NULL COMMENT 'ユーザーエージェント',
  `regist_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '登録日時',
  PRIMARY KEY (`no`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='ルイズうわあああん' AUTO_INCREMENT=11 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
