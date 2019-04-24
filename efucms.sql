-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-01-05 01:52:50
-- 服务器版本： 5.6.40-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mh_lingdmh_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `vv_admin`
--

CREATE TABLE IF NOT EXISTS `vv_admin` (
  `id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '账号',
  `password` char(32) NOT NULL COMMENT '密码',
  `nickname` varchar(200) DEFAULT NULL COMMENT '用户名称',
  `wxid` varchar(100) DEFAULT NULL,
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` varchar(100) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '1' COMMENT '用户状态'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='后台用户表';

--
-- 转存表中的数据 `vv_admin`
--

INSERT INTO `vv_admin` (`id`, `username`, `password`, `nickname`, `wxid`, `mobile`, `reg_time`, `reg_ip`, `last_login_time`, `last_login_ip`, `update_time`, `status`) VALUES
(1, 'admin', 'd5dede8aa0f398db3a88350057750233', '管理者', 'kinglong1168', '13417228032', 1483000493, '0', 1543840260, 1270, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `vv_agent`
--

CREATE TABLE IF NOT EXISTS `vv_agent` (
  `id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `sn` varchar(100) NOT NULL,
  `lv` int(5) NOT NULL,
  `money` float(10,2) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `wxid` varchar(50) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `pay_type` int(1) DEFAULT NULL COMMENT '1:微信支付;2:支付宝支付',
  `status` int(1) DEFAULT '1' COMMENT '1:未支付，2：已支付',
  `separate` int(1) DEFAULT '0' COMMENT '1:分成;0:不分成'
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_arclist`
--

CREATE TABLE IF NOT EXISTS `vv_arclist` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `cover` varchar(255) DEFAULT NULL,
  `show_cover` tinyint(3) DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `body` text,
  `link` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服消息';

-- --------------------------------------------------------

--
-- 表的结构 `vv_article`
--

CREATE TABLE IF NOT EXISTS `vv_article` (
  `id` int(11) NOT NULL,
  `autoreply_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(20) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `show_cover` tinyint(3) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `body` text,
  `link` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_assess`
--

CREATE TABLE IF NOT EXISTS `vv_assess` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_autoreply`
--

CREATE TABLE IF NOT EXISTS `vv_autoreply` (
  `id` int(11) NOT NULL,
  `keyword` varchar(50) DEFAULT NULL COMMENT '关键词',
  `type` tinyint(3) DEFAULT NULL COMMENT '回复类型,1文本2图文',
  `content` text COMMENT '文本回复的内容',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态,1启用'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='公众号自动回复';

--
-- 转存表中的数据 `vv_autoreply`
--

INSERT INTO `vv_autoreply` (`id`, `keyword`, `type`, `content`, `status`) VALUES
(1, '关注', 1, '☞欢迎关注~\r\nHello，终于等到您！我们已经恭候多时啦，本站内容全部免费哦！\r\n请您尽情阅读浏览！\r\n我们这里有全网火爆、流行、优质的男生、女生小说和漫画！\r\n并且为大家提供不断更新，谢谢啦！\r\n而且每日签到送100书币、每日分享送100书币哦（分享方式：点击进入任何一部小说、漫画页面，然后点击右上角，分享到朋友圈即可获赠100书币，一位好友点击，您获得100书币，两位好友点击，您获得200书币，以此类推，无上限哦）！\r\n回复：推广赚钱，您可以在阅读的时候赚钱！\r\n回复：代理介绍，您可以成为我们平台代理，享受永久分成哦！\r\n回复：联系客服，您有任何问题都可以咨询我们！', 1),
(2, '联系客服', 1, '客服微信：kinglong1168\r\n客服QQ：914207606\r\nEFUCMS小说漫画系统站点：www.efucms.com', 1),
(3, '推广赚钱', 1, '推广赚钱说明：\r\n1. 分享你的推广二维码，你将获得：通过此二维码进入充值的客户的20%提成佣金。\r\n2. 你的推广二维码获取方式：点击下方菜单“用户中心-个人中心-推广二维码”即可获取推广二维码，然后点击右上角将二维码分享给朋友、群、朋友圈。\r\n3. 有人通过你分享的二维码进入漫画平台的，则该用户为你的推广用户。\r\n4. 你将永久获得：通过此二维码进入充值的客户的20%提成，不限充值次数和时间。\r\n5. 推广二维码分享后，你可以在：“个人中心—我的客户”中查看通过此推广二维码进入的客户和客户充值的金额。然后在“申请提现”中可以申请提现。\r\n6.申请提现三个工作日内到账。', 1),
(4, '代理介绍', 1, 'Hallo！！！\r\n成为代理方式：\r\n点击公众号右下角-用户中心-代理教程，下载教程观看。\r\n很简单的哈！', 1);

-- --------------------------------------------------------

--
-- 表的结构 `vv_book`
--

CREATE TABLE IF NOT EXISTS `vv_book` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `cateids` varchar(255) DEFAULT NULL COMMENT '分类',
  `bookcate` varchar(255) DEFAULT '0' COMMENT '小说分类',
  `send` int(11) DEFAULT '0' COMMENT '打赏书币',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `summary` text NOT NULL COMMENT '作品简介',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图(列表)',
  `detail_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '详情页图片',
  `sort` int(10) NOT NULL DEFAULT '1' COMMENT '排序权值',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态:1连载 2完结',
  `free_type` tinyint(2) NOT NULL DEFAULT '2' COMMENT '属性:1免费 2付费',
  `episodes` int(10) NOT NULL DEFAULT '0' COMMENT '最新多少集(话)',
  `pay_num` int(10) NOT NULL DEFAULT '0' COMMENT '第m话开始需要付费',
  `reader` int(10) NOT NULL DEFAULT '0' COMMENT '人气值',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `collect` int(10) NOT NULL DEFAULT '0' COMMENT '收藏数',
  `is_new` tinyint(2) NOT NULL DEFAULT '0' COMMENT '新书/非新书',
  `is_recomm` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否精选推荐 1是 0否',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳',
  `readnum` int(10) DEFAULT '0',
  `chargenum` int(10) DEFAULT '0',
  `chargemoney` int(10) DEFAULT '0' COMMENT '付费章节金额',
  `share_title` text COMMENT '分享标题',
  `share_pic` varchar(255) DEFAULT NULL COMMENT '分享图标',
  `share_desc` text COMMENT '分享简介'
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 COMMENT='小说列表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_book_episodes`
--

CREATE TABLE IF NOT EXISTS `vv_book_episodes` (
  `id` int(10) unsigned NOT NULL,
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '小说ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分集标题',
  `ji_no` int(10) NOT NULL DEFAULT '0' COMMENT '集数编号',
  `info` longtext COMMENT '每集内容',
  `readnums` int(11) DEFAULT '0' COMMENT '阅读人数',
  `likes` int(10) DEFAULT '0' COMMENT '点赞数量',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '上集编号no',
  `next` int(10) NOT NULL DEFAULT '0' COMMENT '下集编号no',
  `money` float(10,2) DEFAULT '0.00' COMMENT '阅读需要的费用',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=111756 DEFAULT CHARSET=utf8 COMMENT='小说章节内容表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_book_likes`
--

CREATE TABLE IF NOT EXISTS `vv_book_likes` (
  `id` int(10) unsigned NOT NULL COMMENT '小说分集记录ID',
  `bid` int(10) NOT NULL DEFAULT '0' COMMENT '小说ID',
  `ji_no` int(10) NOT NULL DEFAULT '0' COMMENT '小说集数编号',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞用户ID',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小说点赞表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_book_read`
--

CREATE TABLE IF NOT EXISTS `vv_book_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `ji_no` int(11) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小说阅读表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_book_ys_read`
--

CREATE TABLE IF NOT EXISTS `vv_book_ys_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `yid` int(11) NOT NULL,
  `ji_no` int(11) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_cart`
--

CREATE TABLE IF NOT EXISTS `vv_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `pic` varchar(256) NOT NULL,
  `market_price` float(10,2) NOT NULL COMMENT '优惠价格价',
  `price` float(10,2) NOT NULL COMMENT '实际价格',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '加入时间戳',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0:未提交订单，1：已经提交订单的'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='购物车';

-- --------------------------------------------------------

--
-- 表的结构 `vv_chapter`
--

CREATE TABLE IF NOT EXISTS `vv_chapter` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `memid` int(11) DEFAULT '0' COMMENT '代理ID；0表示总管理员',
  `mbid` int(11) NOT NULL DEFAULT '0',
  `eid` int(11) DEFAULT '0',
  `pic` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `etitle` varchar(255) DEFAULT NULL,
  `ji_no` int(11) NOT NULL,
  `url` text,
  `burl` text,
  `isubscribe` tinyint(4) DEFAULT '0' COMMENT '是否强制关注',
  `qrcode` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '1:漫画2：小说',
  `subscribe` int(10) DEFAULT '0' COMMENT '关注量',
  `charge` float(11,2) DEFAULT '0.00' COMMENT '充值量',
  `read` int(11) DEFAULT '0' COMMENT '阅读量'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='推广记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_charge`
--

CREATE TABLE IF NOT EXISTS `vv_charge` (
  `id` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型 1购买VIP 2购买视频',
  `sn` varchar(100) NOT NULL,
  `mid` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `money` float(11,2) NOT NULL,
  `dmoney` float(11,2) DEFAULT '0.00' COMMENT '第三方扣除之后显示金额',
  `smoney` float(11,2) DEFAULT '0.00',
  `isvip` tinyint(4) DEFAULT '0' COMMENT '是否VIP包年订单',
  `create_time` int(11) NOT NULL,
  `paysn` varchar(100) DEFAULT NULL,
  `remark` text,
  `pay_time` varchar(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付状态',
  `chapid` int(11) DEFAULT '0' COMMENT '文案地址',
  `is_status` tinyint(2) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值订单表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_comment`
--

CREATE TABLE IF NOT EXISTS `vv_comment` (
  `id` int(11) NOT NULL,
  `headimg` text NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户评论表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_config`
--

CREATE TABLE IF NOT EXISTS `vv_config` (
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点设置';

--
-- 转存表中的数据 `vv_config`
--

INSERT INTO `vv_config` (`name`, `value`) VALUES
('site', 'a:18:{s:4:"name";s:11:"CC漫画站";s:9:"subscribe";s:6:"关注";s:10:"send_money";s:3:"100";s:3:"uid";s:24:"ed292aa45caf5deb45432c45";s:5:"token";s:32:"891a84bdc3df4ee3d764bcdd510142d3";s:8:"paymodel";s:1:"3";s:4:"kfqq";s:8:"24814545";s:6:"weixin";s:8:"24814545";s:3:"vip";s:3:"365";s:4:"rate";s:3:"100";s:7:"mhmoney";s:2:"20";s:7:"xsmoney";s:2:"20";s:4:"sign";s:3:"100";s:6:"mobile";s:11:"13845615424";s:7:"smsuser";s:7:"chengyc";s:6:"smspsw";s:8:"27658525";s:7:"smssign";s:11:"CC漫画站";s:6:"qrcode";s:53:"./Public/upload/images/1811/24/011758775390003563.jpg";}'),
('yyb', 'a:3:{s:4:"name";s:12:"微信支付";s:5:"payid";s:0:"";s:6:"paykey";s:0:"";}'),
('user', 'a:2:{s:4:"name";s:5:"admin";s:4:"pass";s:32:"355e994fa05e380c362ba9adf0711299";}'),
('dist_normal', 'a:8:{s:11:"parent_name";s:15:"上级分销商";s:5:"model";s:1:"1";s:11:"level1_name";s:15:"一级分销商";s:10:"level1_per";s:2:"20";s:11:"level2_name";s:15:"二级分销商";s:10:"level2_per";s:2:"20";s:11:"level3_name";s:15:"三级分销商";s:10:"level3_per";s:2:"10";}'),
('mp', 'a:5:{s:5:"appid";s:18:"wxc4fbeb5a99b3e842";s:9:"appsecret";s:32:"556d3c22200d4a499699f268a4ed7db4";s:6:"mch_id";s:0:"";s:3:"key";s:0:"";s:4:"cert";s:47:"./Public/cert/e54776fdbc4fc791453ecc95fdc3d684/";}'),
('lv', 'a:3:{i:1;a:7:{s:4:"name";s:12:"白银代理";s:5:"money";s:4:"0.01";s:6:"amount";s:4:"1200";s:6:"coupon";s:2:"95";s:9:"separate1";s:2:"10";s:9:"separate2";s:2:"20";s:9:"separate3";s:2:"30";}i:2;a:7:{s:4:"name";s:12:"黄金代理";s:5:"money";s:4:"0.02";s:6:"amount";s:4:"2000";s:6:"coupon";s:2:"90";s:9:"separate1";s:2:"20";s:9:"separate2";s:2:"30";s:9:"separate3";s:2:"40";}i:3;a:7:{s:4:"name";s:9:"总代理";s:5:"money";s:4:"0.03";s:6:"amount";s:4:"5000";s:6:"coupon";s:2:"85";s:9:"separate1";s:2:"30";s:9:"separate2";s:2:"40";s:9:"separate3";s:2:"50";}}'),
('qrcode', 'a:2:{s:4:"name";s:15:"推广二维码";s:3:"pic";s:57:"http://shipin.ruiqi88.com/Upload/20171025/012816_368.jpeg";}'),
('banner', 'a:1:{s:6:"config";a:3:{i:0;a:2:{s:3:"pic";s:53:"./Public/upload/images/1810/15/002420462901005849.jpg";s:3:"url";s:59:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=bookinfo&mhid=39";}i:1;a:2:{s:3:"pic";s:53:"./Public/upload/images/1810/15/002453386898004520.jpg";s:3:"url";s:58:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=bookinfo&mhid=8";}i:2;a:2:{s:3:"pic";s:53:"./Public/upload/images/1810/15/002536576391002410.jpg";s:3:"url";s:59:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=bookinfo&mhid=24";}}}'),
('charge', 'a:5:{i:1;a:3:{s:5:"money";s:1:"8";s:4:"send";s:3:"800";s:5:"ishot";i:0;}i:2;a:3:{s:5:"money";s:2:"28";s:4:"send";s:4:"3000";s:5:"ishot";i:0;}i:3;a:3:{s:5:"money";s:2:"50";s:4:"send";s:4:"7000";s:5:"ishot";s:1:"1";}i:4;a:3:{s:5:"money";s:3:"100";s:4:"send";s:5:"16000";s:5:"ishot";i:0;}i:5;a:3:{s:5:"money";s:3:"200";s:4:"send";s:5:"38000";s:5:"ishot";s:1:"1";}}'),
('plogo', 'a:1:{s:3:"pic";s:54:"http://xs12.fweixin.top/Upload/20171025/014719_143.jpeg";}'),
('level', 'a:3:{i:1;a:4:{s:4:"name";s:6:"班长";s:9:"separate1";s:2:"20";s:9:"separate2";s:2:"30";s:9:"separate3";s:2:"40";}i:2;a:4:{s:4:"name";s:9:"班主任";s:9:"separate1";s:2:"30";s:9:"separate2";s:2:"40";s:9:"separate3";s:2:"50";}i:3;a:4:{s:4:"name";s:12:"教导主任";s:9:"separate1";i:0;s:9:"separate2";s:2:"30";s:9:"separate3";s:2:"50";}}'),
('vipset', 'a:12:{s:9:"vip99last";s:1:"3";s:9:"vip99cost";s:1:"1";s:9:"vip98last";s:2:"10";s:9:"vip98cost";s:1:"2";s:9:"vip97last";s:2:"15";s:9:"vip97cost";s:1:"3";s:9:"vip96last";s:2:"30";s:9:"vip96cost";s:1:"5";s:9:"vip95last";s:2:"60";s:9:"vip95cost";s:1:"9";s:8:"vip1last";s:1:"1";s:8:"vip1cost";s:2:"98";}'),
('bookcate', 'a:11:{i:1;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/200049038107006927.png";s:3:"url";s:48:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=index";s:4:"name";s:6:"漫画";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:2;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195455908579001143.png";s:3:"url";s:53:"http://e10.eteawell.cn/index.php?m=&c=Book&a=book_hot";s:4:"name";s:6:"排行";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:3;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195459752272006347.png";s:3:"url";s:54:"http://e10.eteawell.cn/index.php?m=&c=Book&a=book_free";s:4:"name";s:6:"免费";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:4;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195503455502007059.png";s:3:"url";s:54:"http://e10.eteawell.cn/index.php?m=&c=Book&a=book_cate";s:4:"name";s:6:"分类";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:5;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020551806640008089.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"热门推荐";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:6;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020555462890001345.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"本周人气";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:7;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020558791015002396.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"都市小说";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:8;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020602166015007642.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"女生小说";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:9;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020605744140009530.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"男生小说";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:10;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020609072265001401.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"美女上司";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:11;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020612681640002315.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"其他小说";s:4:"show";s:1:"2";s:6:"isshow";s:1:"0";}}'),
('mhcate', 'a:12:{i:1;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195359392107007275.png";s:3:"url";s:50:"http://e10.eteawell.cn/index.php?m=&c=Book&a=index";s:4:"name";s:6:"小说";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:2;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195404752307004408.png";s:3:"url";s:51:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=book_hot";s:4:"name";s:6:"排行";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:3;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195408767294004497.png";s:3:"url";s:52:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=book_free";s:4:"name";s:6:"免费";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:4;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195413423720005649.png";s:3:"url";s:52:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=book_cate";s:4:"name";s:6:"分类";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:5;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020512009765007016.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"国漫精选";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:6;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020516619140002925.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"新品上架";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:7;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020627369140001484.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"热门推荐";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:8;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020520150390001237.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"经典必看";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:9;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020523431640007756.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"男生漫画";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:10;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020527337890004998.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"真人漫画";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:11;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020530884765001755.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"女生漫画";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:12;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020534603515009265.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"恐怖悬疑";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}}'),
('dist', 'a:8:{s:11:"parent_name";s:15:"上级分销商";s:5:"model";s:1:"1";s:11:"level1_name";s:15:"一级分销商";s:10:"level1_per";s:2:"30";s:11:"level2_name";s:15:"二级分销商";s:10:"level2_per";s:2:"20";s:11:"level3_name";s:15:"三级分销商";s:10:"level3_per";s:2:"10";}'),
('Asite', 'a:3:{s:4:"name";s:23:"DSCMS小说漫画系统 ";s:6:"mobile";s:11:"13417228032";s:6:"weixin";s:7:"dscmsgf";}'),
('xbanner', 'a:1:{s:6:"config";a:3:{i:0;a:2:{s:3:"pic";s:53:"./Public/upload/images/1806/22/193115211067005999.jpg";s:3:"url";s:60:"http://e10.eteawell.cn/index.php?m=&c=Book&a=bookinfo&bid=42";}i:1;a:2:{s:3:"pic";s:53:"./Public/upload/images/1806/22/193132101770007911.jpg";s:3:"url";s:60:"http://e10.eteawell.cn/index.php?m=&c=Book&a=booklist&cate=6";}i:2;a:2:{s:3:"pic";s:53:"./Public/upload/images/1807/03/100021070415008273.jpg";s:3:"url";s:60:"http://e10.eteawell.cn/index.php?m=&c=Book&a=booklist&cate=9";}}}'),
('tplmsg', 'a:1:{s:15:"OPENTM207008612";a:2:{s:2:"id";s:43:"c9esVrwndalCxBMCpRaB4CXnys_R-cSyKeWoO_ASxIc";s:6:"status";i:1;}}'),
('config', 'a:1:{s:5:"index";N;}'),
('config', 'a:1:{s:5:"index";N;}'),
('config', 'a:1:{s:5:"index";N;}'),
('config', 'a:1:{s:5:"index";N;}'),
('send', 'a:4:{i:1;a:2:{s:3:"pic";s:53:"./Public/upload/images/1804/24/030511906701003443.jpg";s:5:"money";s:3:"100";}i:2;a:2:{s:3:"pic";s:53:"./Public/upload/images/1804/24/030513547258009716.jpg";s:5:"money";s:3:"388";}i:3;a:2:{s:3:"pic";s:53:"./Public/upload/images/1804/24/030515531618007134.jpg";s:5:"money";s:3:"588";}i:4;a:2:{s:3:"pic";s:53:"./Public/upload/images/1804/24/030517500688005051.jpg";s:5:"money";s:3:"888";}}'),
('yook', 'a:8:{i:1;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/200049038107006927.png";s:3:"url";s:48:"http://e10.eteawell.cn/index.php?m=&c=Mh&a=index";s:4:"name";s:6:"漫画";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:2;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195455908579001143.png";s:3:"url";s:53:"http://e10.eteawell.cn/index.php?m=&c=Yook&a=book_hot";s:4:"name";s:6:"排行";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:3;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195459752272006347.png";s:3:"url";s:54:"http://e10.eteawell.cn/index.php?m=&c=Yook&a=book_free";s:4:"name";s:6:"免费";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:4;a:5:{s:3:"pic";s:53:"./Public/upload/images/1804/06/195503455502007059.png";s:3:"url";s:54:"http://e10.eteawell.cn/index.php?m=&c=Yook&a=book_cate";s:4:"name";s:6:"分类";s:4:"show";s:1:"1";s:6:"isshow";s:1:"1";}i:5;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020451884765007403.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"小编推荐";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:6;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020456087890003563.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"新书推荐";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:7;a:5:{s:3:"pic";s:53:"./Public/upload/images/1811/24/020459619140003985.jpg";s:3:"url";s:0:"";s:4:"name";s:12:"收听榜单";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}i:8;a:5:{s:3:"pic";s:0:"";s:3:"url";s:0:"";s:4:"name";s:12:"听书分类";s:4:"show";s:1:"2";s:6:"isshow";s:1:"1";}}'),
('ads', 'a:5:{s:3:"url";s:21:"http://www.efucms.com";s:3:"pic";s:53:"./Public/upload/images/1811/15/230605878937004817.jpg";s:7:"chapter";s:1:"1";s:8:"achapter";s:3:"2,5";s:8:"xchapter";s:2:"10";}'),
('share', 'a:3:{s:5:"title";s:48:"efucms系统e10，小说漫画阅读平台搭建";s:4:"desc";s:48:"efucms系统e10，小说漫画阅读平台搭建";s:3:"pic";s:53:"./Public/upload/images/1807/24/050514617388003349.jpg";}'),
('ybanner', 'a:1:{s:6:"config";a:2:{i:0;a:2:{s:3:"pic";s:53:"./Public/upload/images/1807/09/010714569476002281.png";s:3:"url";s:0:"";}i:1;a:2:{s:3:"pic";s:53:"./Public/upload/images/1807/09/010725991262001145.png";s:3:"url";s:0:"";}}}');

-- --------------------------------------------------------

--
-- 表的结构 `vv_data`
--

CREATE TABLE IF NOT EXISTS `vv_data` (
  `date` int(11) NOT NULL,
  `orders` int(11) DEFAULT '0',
  `total` decimal(10,2) DEFAULT '0.00',
  `subs` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='数据统计';

--
-- 转存表中的数据 `vv_data`
--

INSERT INTO `vv_data` (`date`, `orders`, `total`, `subs`) VALUES
(20181230, 0, '0.00', 0),
(20181231, 0, '0.00', 1),
(20190104, 0, '0.00', 0);

-- --------------------------------------------------------

--
-- 表的结构 `vv_finance_log`
--

CREATE TABLE IF NOT EXISTS `vv_finance_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `money` float(10,2) NOT NULL,
  `action` tinyint(4) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='账户变动记录';

-- --------------------------------------------------------

--
-- 表的结构 `vv_goods`
--

CREATE TABLE IF NOT EXISTS `vv_goods` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(60) DEFAULT NULL COMMENT '标题',
  `file_id` varchar(100) DEFAULT NULL,
  `teacher` int(10) DEFAULT NULL,
  `sorts_id` int(10) DEFAULT NULL COMMENT '分类',
  `price` decimal(60,2) DEFAULT NULL COMMENT '价格',
  `separate_money` decimal(60,2) DEFAULT NULL,
  `pic` varchar(200) DEFAULT NULL COMMENT '封面图片',
  `banner` text COMMENT '详情轮播图片',
  `hours` int(6) DEFAULT NULL COMMENT '有效期（月）',
  `tryhours` int(6) DEFAULT NULL,
  `content` text,
  `sold` int(11) DEFAULT '0' COMMENT '交易数',
  `is_send` tinyint(4) DEFAULT '0' COMMENT '是否推荐',
  `status` int(1) DEFAULT '1',
  `dznums` int(10) DEFAULT '0' COMMENT '点赞数量',
  `grow` int(10) DEFAULT '0' COMMENT '打赏金额 '
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_goods_sorts`
--

CREATE TABLE IF NOT EXISTS `vv_goods_sorts` (
  `id` int(10) unsigned NOT NULL,
  `fname` varchar(60) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_group`
--

CREATE TABLE IF NOT EXISTS `vv_group` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '用户组id,自增主键',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '角色名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '角色状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '角色拥有的规则id，多个规则 , 隔开'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vv_group`
--

INSERT INTO `vv_group` (`id`, `title`, `description`, `status`, `rules`) VALUES
(1, '超级管理员', '超级管理员', 1, '2,3,4,5,6,23,7,9,8,10,22,11,12,13,14,15,16,25,17,18,19,20,21,24,28,29,30,26,27,31,32,33,34,35,54,91,36,37,39,41,42,40,38,92,43,48,49,50,94,93,44,45,46,47,51,52,53,55,56,57,59,58,60,61,62,64,65,63,66,67,68,69,95,70,71,74,75,76,72,73,77,80,81,82,78,83,84,85,89,86,87,88'),
(2, '仓库', '仓库', 1, '2,3,4,5,23,7,9,8,10,22,11,12,13,14,15,16,25,17,18,19,20,21,24,26,27,31,32,33,34,35,54'),
(4, '管理', '管理', 1, '1'),
(5, '1', '1', 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `vv_grow`
--

CREATE TABLE IF NOT EXISTS `vv_grow` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sn` varchar(100) NOT NULL,
  `gid` int(11) NOT NULL,
  `money` float(10,2) NOT NULL,
  `create_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `pay_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_jubao`
--

CREATE TABLE IF NOT EXISTS `vv_jubao` (
  `id` int(11) NOT NULL,
  `rid` int(11) DEFAULT NULL,
  `type` char(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `seqing` int(11) DEFAULT '0',
  `xuexing` int(11) DEFAULT '0',
  `baoli` int(11) DEFAULT '0',
  `weifa` int(11) DEFAULT '0',
  `daoban` int(11) DEFAULT '0',
  `qita` int(11) DEFAULT '0',
  `nums` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='举报记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mch_pay`
--

CREATE TABLE IF NOT EXISTS `vv_mch_pay` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `openid` varchar(256) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `money` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `op` varchar(30) NOT NULL,
  `msg` varchar(100) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_member`
--

CREATE TABLE IF NOT EXISTS `vv_member` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `tpassword` varchar(200) DEFAULT NULL,
  `zfb` varchar(200) DEFAULT NULL,
  `mobile` char(20) DEFAULT NULL,
  `separate` float(10,2) DEFAULT '0.00' COMMENT '分成比例',
  `declv` float(10,2) DEFAULT '0.00' COMMENT '扣除比例',
  `url` text,
  `qrcode` varchar(200) DEFAULT NULL,
  `gqrcode` varchar(255) DEFAULT NULL COMMENT '公众号二维码',
  `salt` varchar(20) DEFAULT NULL,
  `imei` text,
  `money` float(10,2) DEFAULT '0.00',
  `status` tinyint(4) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `deductions_s` int(10) DEFAULT NULL,
  `deductions_e` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理用户表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_member_data`
--

CREATE TABLE IF NOT EXISTS `vv_member_data` (
  `id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `date` varchar(20) DEFAULT '0',
  `money` float(10,2) DEFAULT '0.00'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_member_desc`
--

CREATE TABLE IF NOT EXISTS `vv_member_desc` (
  `id` int(11) NOT NULL,
  `cid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nickname` int(11) DEFAULT NULL,
  `money` float(11,2) DEFAULT NULL,
  `dmoney` float(11,2) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `status` tinyint(11) DEFAULT '0' COMMENT '是否支付',
  `pay_time` int(11) DEFAULT '0' COMMENT '支付时间'
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_member_separate`
--

CREATE TABLE IF NOT EXISTS `vv_member_separate` (
  `id` int(11) NOT NULL,
  `date` varchar(20) DEFAULT NULL,
  `mid` int(11) NOT NULL,
  `sn` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `money` float(11,2) NOT NULL,
  `pay` float(11,2) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `pay_time` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `is_status` tinyint(2) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理佣金分成表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_menu`
--

CREATE TABLE IF NOT EXISTS `vv_menu` (
  `id` int(10) unsigned NOT NULL COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:是否禁止'
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vv_menu`
--

INSERT INTO `vv_menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `status`) VALUES
(92, '查看评论', 38, 10, 'Good/assess', 0, '', 0),
(2, '设置', 0, 20, '', 0, '', 0),
(3, '管理员设置', 2, 10, 'Setting/admin', 0, '', 0),
(4, '新增管理员', 3, 10, '', 0, '', 0),
(5, '修改管理员', 3, 10, 'Setting/editAdmin', 0, '', 0),
(6, '删除管理员', 3, 10, '', 0, '', 0),
(9, '删除角色', 7, 10, '', 0, '', 0),
(7, '角色设置', 2, 10, 'Setting/group', 0, '', 0),
(8, '增加角色', 7, 10, '', 0, '', 0),
(10, '修改角色', 7, 10, '', 0, '', 0),
(11, '权限设置', 2, 10, 'Setting/auth', 0, '', 0),
(12, '添加权限', 11, 10, '', 0, '', 0),
(13, '添加子权限', 11, 0, '', 0, '', 0),
(14, '修改', 11, 0, '', 0, '', 0),
(15, '删除', 11, 0, '', 0, '', 0),
(16, '系统设置', 2, 20, 'Config/site', 0, '', 0),
(17, '自动回复设置', 2, 10, 'Autoreply/index', 0, '', 0),
(18, '添加关键词回复', 17, 10, '', 0, '', 0),
(19, '修改回复', 17, 10, 'Autoreply/edit', 0, '', 0),
(20, '删除自动回复', 17, 10, '', 0, '', 0),
(21, '图文编辑', 17, 10, 'Article/index', 0, '', 0),
(22, '保存角色', 7, 0, '', 0, '', 0),
(23, '保存管理员', 3, 0, '', 0, '', 0),
(24, '保存图文编辑', 17, 10, '', 0, '', 0),
(25, '保存网站编辑', 16, 0, '', 0, '', 0),
(26, '公众号设置', 2, 0, 'Config/mp', 0, '', 0),
(27, '保存公众号设置', 26, 10, '', 0, '', 0),
(28, '添加图文文章', 17, 0, '', 0, '', 0),
(29, '编辑图文文章', 17, 0, '', 0, '', 0),
(30, '删除图文文章', 17, 0, '', 0, '', 0),
(31, '自定义菜单', 2, 0, 'Selfmenu/index', 0, '', 0),
(32, '添加菜单', 31, 0, 'Selfmenu/edit', 0, '', 0),
(33, '保存菜单', 31, 0, '', 0, '', 0),
(34, '修改菜单', 31, 0, 'Selfmenu/edit', 0, '', 0),
(35, '删除菜单', 31, 0, '', 0, '', 0),
(36, '课程管理', 0, 10, '', 0, '', 0),
(37, '课程分类', 36, 10, 'Good/sorts', 0, '', 0),
(38, '课程列表', 36, 10, 'Good/lists', 0, '', 0),
(39, '添加分类', 37, 10, '', 0, '', 0),
(41, '查看分类', 37, 10, '', 0, '', 0),
(42, '删除分类', 37, 10, '', 0, '', 0),
(40, '添加子分类', 37, 10, '', 0, '', 0),
(43, '添加课程', 38, 10, 'Good/add', 0, '', 0),
(44, '课程教师', 36, 10, 'Teacher/index', 0, '', 0),
(45, '添加教师', 44, 10, 'Teacher/edit', 0, '', 0),
(46, '修改教师', 44, 10, 'Teacher/edit', 0, '', 0),
(47, '删除教师', 44, 10, '', 0, '', 0),
(48, '修改课程', 38, 10, 'Good/edit', 0, '', 0),
(49, '删除课程', 38, 0, '', 0, '', 0),
(50, '更改交易数', 38, 10, '', 0, '', 0),
(51, '代理商设置', 0, 10, '', 0, '', 0),
(52, '代理商级别', 51, 10, 'Config/lv', 0, '', 0),
(53, '代理商列表', 51, 10, 'Agent/index', 0, '', 0),
(54, '发布菜单', 31, 10, 'Selfmenu/send', 0, '', 0),
(55, '添加代理商', 53, 10, 'Agent/addAgent', 0, '', 0),
(56, '修改代理商', 53, 10, 'Agent/editAgent', 0, '', 0),
(57, '删除代理商', 53, 10, '', 0, '', 0),
(58, '代理预存款', 51, 10, 'Agent/recharg', 0, '', 0),
(59, '下级团队列表', 53, 10, 'Agent/AgentSonList', 0, '', 0),
(60, '代理商统计', 51, 10, 'Agent/AgentTree', 0, '', 0),
(61, '网站设置', 0, 10, '', 0, '', 0),
(62, '首页轮播设置', 61, 10, 'Config/banner', 0, '', 0),
(63, '米粒信息发布', 61, 10, 'Notice/index', 0, '', 0),
(64, '添加设置', 62, 10, '', 0, '', 0),
(65, '删除设置', 62, 10, '', 0, '', 0),
(66, '添加信息', 63, 10, 'Notice/edit', 0, '', 0),
(67, '修改信息', 63, 10, 'Notice/edit', 0, '', 0),
(68, '删除信息', 63, 10, '', 0, '', 0),
(69, '网站推广码图片', 61, 10, 'Config/qrcode', 0, '', 0),
(70, '合伙人设置', 0, 10, '', 0, '', 0),
(71, '合伙人列表', 70, 10, 'Partner/index', 0, '', 0),
(72, '合伙人设置', 70, 10, 'Config/level', 0, '', 0),
(73, '系统财务', 0, 10, '', 0, '', 0),
(74, '添加合伙人', 71, 10, 'Partner/addPartner', 0, '', 0),
(75, '编辑合伙人', 71, 10, 'Partner/edit', 0, '', 0),
(76, '删除合伙人', 71, 10, '', 0, '', 0),
(77, '提现管理', 73, 10, 'Withdraw/index', 0, '', 0),
(78, '分成记录', 73, 10, 'Separate/index', 0, '', 0),
(80, '拒绝提现', 77, 0, '', 0, '', 0),
(81, '发放金额', 77, 10, '', 0, '', 0),
(82, '删除提现', 77, 10, '', 0, '', 0),
(83, '销量统计', 73, 10, 'Sales/index', 0, '', 0),
(84, '团队业绩', 73, 10, 'Reward/index', 0, '', 0),
(85, '发放奖励', 84, 10, '', 0, '', 0),
(86, '订单管理', 0, 15, '', 0, '', 0),
(87, '订单列表', 86, 10, 'Order/index', 0, '', 0),
(88, '订单详情', 87, 10, 'Order/OrderDetail', 0, '', 0),
(89, '领取详情', 84, 10, '', 0, '', 0),
(91, '用户列表', 2, 0, 'Setting/user', 0, '', 0),
(94, '删除评论', 38, 10, '', 0, '', 0),
(93, '添加评论', 38, 10, '', 0, '', 0),
(95, '网站后台logo', 61, 0, 'Config/plogo', 0, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `vv_message`
--

CREATE TABLE IF NOT EXISTS `vv_message` (
  `id` int(10) NOT NULL,
  `fromuser` int(10) NOT NULL,
  `fromuser_pic` varchar(200) DEFAULT NULL,
  `touser` int(10) NOT NULL,
  `touser_pic` varchar(200) DEFAULT NULL,
  `message` text,
  `hf_message` text,
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_banner`
--

CREATE TABLE IF NOT EXISTS `vv_mh_banner` (
  `id` int(10) unsigned NOT NULL COMMENT '漫画分集记录ID',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '图片路径',
  `mhid` int(10) NOT NULL DEFAULT '0' COMMENT '漫画ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '漫画标题',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='漫画轮播图';

--
-- 转存表中的数据 `vv_mh_banner`
--

INSERT INTO `vv_mh_banner` (`id`, `pic`, `mhid`, `title`, `create_time`, `update_time`) VALUES
(1, '5a3b82354e58e.jpg', 1, '帝豪老公太狂热', 1515775444, 1515775444),
(2, '5a222856b081f.jpg', 2, '妾欲偷香', 1515775444, 1515775444),
(3, '5a41ac14b3eed.jpg', 3, '婚途陌路', 1515775444, 1515775444),
(4, '5a222eb7964e6.jpg', 4, '重生之慕甄', 1515775444, 1515775444),
(5, '5a222e3a172f6.jpg', 5, '王爷你好贱', 1515775444, 1515775444);

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_collect`
--

CREATE TABLE IF NOT EXISTS `vv_mh_collect` (
  `id` int(10) unsigned NOT NULL COMMENT '收藏记录ID',
  `mhid` int(10) NOT NULL DEFAULT '0' COMMENT '小说或漫画ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '小说或漫画标题',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图(列表)',
  `episodes` int(10) NOT NULL DEFAULT '0' COMMENT '最新多少集(话)',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '收藏时间戳',
  `type` char(10) DEFAULT 'mh'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小说漫画收藏表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_episodes`
--

CREATE TABLE IF NOT EXISTS `vv_mh_episodes` (
  `id` int(10) unsigned NOT NULL,
  `mhid` bigint(11) DEFAULT '0' COMMENT '漫画ID',
  `title` varchar(255) NOT NULL COMMENT '章节标题',
  `ji_no` int(10) NOT NULL DEFAULT '0' COMMENT '第几章节',
  `pics` text NOT NULL,
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数量',
  `readnums` int(11) DEFAULT '0' COMMENT '阅读人数',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '上集编号',
  `next` int(10) NOT NULL DEFAULT '0' COMMENT '下集编号',
  `money` float(10,2) DEFAULT '0.00' COMMENT '阅读需要的费用',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳'
) ENGINE=MyISAM AUTO_INCREMENT=5528 DEFAULT CHARSET=utf8 COMMENT='漫画分集表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_feedback`
--

CREATE TABLE IF NOT EXISTS `vv_mh_feedback` (
  `id` int(10) unsigned NOT NULL COMMENT '反馈ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `content` text NOT NULL COMMENT '反馈内容',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '时间戳'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='反馈表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_likes`
--

CREATE TABLE IF NOT EXISTS `vv_mh_likes` (
  `id` int(10) unsigned NOT NULL COMMENT '漫画分集记录ID',
  `mhid` int(10) NOT NULL DEFAULT '0' COMMENT '漫画ID',
  `ji_no` int(10) NOT NULL DEFAULT '0' COMMENT '集数编号',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞用户ID',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='漫画点赞表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_list`
--

CREATE TABLE IF NOT EXISTS `vv_mh_list` (
  `id` int(10) unsigned NOT NULL COMMENT '漫画ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '漫画标题',
  `mhcate` varchar(255) DEFAULT '7',
  `send` int(11) DEFAULT '0' COMMENT '打赏金额',
  `cateids` varchar(255) DEFAULT '' COMMENT '分类逗号拼接 1总裁2穿越3校园',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `summary` text NOT NULL COMMENT '作品简介',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图(列表)',
  `detail_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '详情页图片',
  `sort` int(10) NOT NULL DEFAULT '1' COMMENT '排序字段',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态:1连载 2完结',
  `free_type` tinyint(2) NOT NULL DEFAULT '2' COMMENT '属性:1免费 2付费',
  `episodes` int(10) NOT NULL DEFAULT '0' COMMENT '最新多少集(话)',
  `pay_num` int(10) NOT NULL DEFAULT '0' COMMENT '第m话开始需要付费',
  `reader` int(10) NOT NULL DEFAULT '0' COMMENT '人气值',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `collect` int(10) NOT NULL DEFAULT '0' COMMENT '收藏数',
  `is_new` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否最近更新 1是 0否',
  `is_recomm` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否精选推荐 1是 0否',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳',
  `readnum` int(10) DEFAULT '0',
  `chargenum` int(10) DEFAULT '0',
  `chargemoney` int(10) DEFAULT '0',
  `share_title` text,
  `share_pic` varchar(255) DEFAULT NULL,
  `share_desc` text
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COMMENT='漫画数据主表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mh_read`
--

CREATE TABLE IF NOT EXISTS `vv_mh_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mhid` int(11) NOT NULL,
  `ji_no` int(11) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='漫画阅读表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_mxsend`
--

CREATE TABLE IF NOT EXISTS `vv_mxsend` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `mxid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `nickname` text,
  `headimg` text,
  `create_time` char(20) DEFAULT NULL,
  `type` char(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_m_withdraw`
--

CREATE TABLE IF NOT EXISTS `vv_m_withdraw` (
  `id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `zfb` varchar(200) DEFAULT NULL,
  `money` float(10,2) DEFAULT '0.00',
  `status` tinyint(4) DEFAULT '1',
  `create_time` int(11) DEFAULT NULL,
  `isua` tinyint(4) DEFAULT '0' COMMENT '是否是新的提现记录'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理结算表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_notice`
--

CREATE TABLE IF NOT EXISTS `vv_notice` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='平台公告表';

--
-- 转存表中的数据 `vv_notice`
--

INSERT INTO `vv_notice` (`id`, `title`, `content`, `create_time`) VALUES
(20, '本周好书双手捧上！', '<p style="box-sizing: border-box; margin-top: 0px; margin-bottom: 30px; font-size: 14px; white-space: normal; background-color: rgb(248, 250, 251); line-height: 28px; letter-spacing: 0.5px; text-align: justify; word-break: break-all; color: rgb(51, 51, 51); font-family: Helvetica, " microsoft="" hiragino="" sans="" wenquanyi="" micro=""><span style="box-sizing: border-box; font-family: " arial="" font-weight:="" font-size:="" color:="">女频</span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">1.《总裁的契约情人》<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">热门</span>，豪门、恋爱<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">（文案建议前4-5章）</span></span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">身世坎坷的孤女，为何会得到总裁青睐？冷面总裁有什么不为人知的过去？</span></p><p style="box-sizing: border-box; margin-top: 0px; margin-bottom: 30px; font-size: 14px; white-space: normal; background-color: rgb(248, 250, 251); line-height: 28px; letter-spacing: 0.5px; text-align: justify; word-break: break-all; color: rgb(51, 51, 51); font-family: Helvetica, " microsoft="" hiragino="" sans="" wenquanyi="" micro=""><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">2.《霸道总裁爱上我》恋爱、霸总<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">（文案建议前3章）</span></span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">绝路下她被迫接受天价交易，本以为钱货两清，没想到五年后再相逢。 他嘴上说着厌弃，身体却很诚实的耍流氓。她拼命守护的秘密忽然曝光，他誓要将她困在身边一睡到底！无耻！别以为你长得帅体力好就人见人爱！</span></p><p style="box-sizing: border-box; margin-top: 0px; margin-bottom: 30px; font-size: 14px; white-space: normal; background-color: rgb(248, 250, 251); line-height: 28px; letter-spacing: 0.5px; text-align: justify; word-break: break-all; color: rgb(51, 51, 51); font-family: Helvetica, " microsoft="" hiragino="" sans="" wenquanyi="" micro=""><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">3.《总裁的替嫁新娘》豪门、恋爱<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">（文案建议前2-3章）</span></span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">姐姐在出嫁前一天和别的男人私奔，而她成了代替品要嫁给满腔怒火的他。 新婚夜，帅气高冷的他道 “开始履行妻子的义务” 褪掉单薄的衣衫的她却被他叫着姐姐的名字。 她代替偿还姐姐的债，却不知……</span></p><p style="box-sizing: border-box; margin-top: 0px; margin-bottom: 30px; font-size: 14px; white-space: normal; background-color: rgb(248, 250, 251); line-height: 28px; letter-spacing: 0.5px; text-align: justify; word-break: break-all; color: rgb(51, 51, 51); font-family: Helvetica, " microsoft="" hiragino="" sans="" wenquanyi="" micro=""><span style="box-sizing: border-box; font-family: " arial="" font-weight:="" font-size:="" color:="">男频</span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">1.《谢文东》<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">新书</span>，热血、都市<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">（文案建议前2章）</span></span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">有人就有恩怨，有恩怨，就有江湖。人就是江湖，叫我怎么退出。 这是坏蛋的领域，属于坏蛋的时代。男人与男人之间的诺言，刀与刀之间火焰，让我们一起去领略真男儿洒血、扶剑、高歌的豪情，去欣赏逆天、叛地、逐鹿的气概…… 刀出男儿必见血，人挡杀人，佛挡杀佛~谁敢拦我宏图霸业……</span></p><p style="box-sizing: border-box; margin-top: 0px; margin-bottom: 0px; font-size: 14px; white-space: normal; background-color: rgb(248, 250, 251); line-height: 28px; letter-spacing: 0.5px; text-align: justify; word-break: break-all; color: rgb(51, 51, 51); font-family: Helvetica, " microsoft="" hiragino="" sans="" wenquanyi="" micro=""><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">2.《斗战狂潮》<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">新书</span>，魔幻、热血<span data-mce-style="color: #ff0000;" style="box-sizing: border-box; color: rgb(255, 0, 0);">（文案建议前2-3章）</span></span><br style="box-sizing: border-box;"/><span data-mce-style="font-family: &#39;arial black&#39;, sans-serif; font-size: 10pt;" style="box-sizing: border-box; font-family: " arial="" font-size:="">双月当空，无限可能的英魂世界 孤寂黑暗，神秘古怪的嬉命小丑 百城联邦，三大帝国，异族横行，魂兽霸幽 这是一个英雄辈出的年代，人类卧薪尝胆重掌地球主权，孕育着进军高纬度的野望！ 重点是…二年级的废柴学长王同学，如何使用嬉命轮盘，撬动整个世界，请注意，学长来了！！！</span></p>', 1524592041);

-- --------------------------------------------------------

--
-- 表的结构 `vv_order`
--

CREATE TABLE IF NOT EXISTS `vv_order` (
  `id` int(11) NOT NULL,
  `sn` varchar(20) NOT NULL,
  `goods_id` text,
  `user_id` int(11) DEFAULT NULL,
  `points` float(10,2) DEFAULT NULL,
  `money` float(10,2) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  `pay_time` int(11) DEFAULT '0',
  `refund_time` int(11) DEFAULT '0',
  `separate` tinyint(4) DEFAULT '0',
  `separate_money` decimal(10,2) DEFAULT '0.00' COMMENT '分拥金额',
  `status` tinyint(11) DEFAULT '1' COMMENT '-1已关闭 1待支付 2已支付待发货 3待确认 4已完成',
  `order_type` int(1) DEFAULT NULL COMMENT '支付方式:1:微信支付;2:支付宝支付;3:余额支付'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商城支付记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_pay_log`
--

CREATE TABLE IF NOT EXISTS `vv_pay_log` (
  `id` int(11) NOT NULL,
  `return_code` varchar(10) NOT NULL,
  `result_code` varchar(10) NOT NULL,
  `openid` varchar(100) NOT NULL,
  `bank_type` varchar(20) NOT NULL,
  `total_fee` float(10,2) NOT NULL,
  `cash_fee` float(10,2) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `out_trade_no` varchar(100) NOT NULL,
  `attach` text NOT NULL,
  `time_end` varchar(20) NOT NULL,
  `log_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_read`
--

CREATE TABLE IF NOT EXISTS `vv_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT '0' COMMENT '漫画或者小说ID',
  `title` text,
  `pic` varchar(200) DEFAULT NULL,
  `summary` text,
  `author` text,
  `episodes` int(11) DEFAULT '0' COMMENT '第几话',
  `type` varchar(100) DEFAULT NULL COMMENT '漫画:mh;小说:xs',
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='小说漫画阅读历史';

--
-- 转存表中的数据 `vv_read`
--

INSERT INTO `vv_read` (`id`, `user_id`, `rid`, `title`, `pic`, `summary`, `author`, `episodes`, `type`, `create_time`) VALUES
(1, 1, 32, '幕后社团', 'http://img.027cgb.com/610673/%E6%BC%AB%E7%94%BB%E5%B0%81%E9%9D%A2/%E5%B9%95%E5%90%8E%E7%A4%BE%E5%9B%A2.jpg', '讲述一位妈妈是如何从“傻白甜”转变为“第一会长”的故事，为了金钱和权利，她是如何付出与牺牲的？在她背后又有着怎样的爱恨纠葛？', '种军＆AAMEDIA', 1, 'mh', 1546267679),
(2, 1, 25, '邻居大叔', 'http://img.027cgb.com/610673/%E6%BC%AB%E7%94%BB%E5%B0%81%E9%9D%A2/%E9%82%BB%E5%B1%85%E5%A4%A7%E5%8F%94.png', '七年的时间，小女孩变成了大美女，但心中还惦记着那位邻居大叔！', 'HBZ＆JX', 1, 'mh', 1546267702),
(3, 1, 98, '异能少女重生：帝少夺吻99次', 'http://img.027cgb.com/610673/%E5%B0%8F%E8%AF%B4%E5%B0%81%E9%9D%A2/%E5%BC%82%E8%83%BD%E5%B0%91%E5%A5%B3%E9%87%8D%E7%94%9F%EF%BC%9A%E5%B8%9D%E5%B0%91%E5%A4%BA%E5%90%BB99%E6%AC%A1.jpg', '她助渣男荣华富贵，最终被渣男开车撞死。重生回到十二岁，她发誓不再期盼虚无的爱情！开启异能掌控阴阳，趋吉避凶未卜先知，有仇报仇有恩报恩，各路大佬尊她为“国师”，风云变幻尽在一念中！他是玄界至尊强者，霸道冷酷，嗜血无情，不近美色，唯独对她千依百顺，天天变着法子虐狗……她靠在他身上：“你不喜欢男人也不喜欢女人，那你喜欢什么？”“你。”男人低头，吻住她的唇。', '格格喵', 1, 'xs', 1546267730),
(4, 1, 98, '异能少女重生：帝少夺吻99次', 'http://img.027cgb.com/610673/%E5%B0%8F%E8%AF%B4%E5%B0%81%E9%9D%A2/%E5%BC%82%E8%83%BD%E5%B0%91%E5%A5%B3%E9%87%8D%E7%94%9F%EF%BC%9A%E5%B8%9D%E5%B0%91%E5%A4%BA%E5%90%BB99%E6%AC%A1.jpg', '她助渣男荣华富贵，最终被渣男开车撞死。重生回到十二岁，她发誓不再期盼虚无的爱情！开启异能掌控阴阳，趋吉避凶未卜先知，有仇报仇有恩报恩，各路大佬尊她为“国师”，风云变幻尽在一念中！他是玄界至尊强者，霸道冷酷，嗜血无情，不近美色，唯独对她千依百顺，天天变着法子虐狗……她靠在他身上：“你不喜欢男人也不喜欢女人，那你喜欢什么？”“你。”男人低头，吻住她的唇。', '格格喵', 2, 'xs', 1546267738);

-- --------------------------------------------------------

--
-- 表的结构 `vv_read_charge`
--

CREATE TABLE IF NOT EXISTS `vv_read_charge` (
  `id` bigint(10) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_reward`
--

CREATE TABLE IF NOT EXISTS `vv_reward` (
  `id` int(11) NOT NULL,
  `per` float(10,2) NOT NULL,
  `level` int(4) NOT NULL,
  `nums` int(10) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_reward_record`
--

CREATE TABLE IF NOT EXISTS `vv_reward_record` (
  `id` int(11) NOT NULL,
  `reward_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `money` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_rlog`
--

CREATE TABLE IF NOT EXISTS `vv_rlog` (
  `id` bigint(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `ji_no` int(11) NOT NULL,
  `type` char(20) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vv_rlog`
--

INSERT INTO `vv_rlog` (`id`, `user_id`, `rid`, `ji_no`, `type`) VALUES
(1, 1, 32, 1, 'mh'),
(2, 1, 25, 1, 'mh');

-- --------------------------------------------------------

--
-- 表的结构 `vv_selfmenu`
--

CREATE TABLE IF NOT EXISTS `vv_selfmenu` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(20) NOT NULL,
  `extra` varchar(256) NOT NULL,
  `type` varchar(50) NOT NULL,
  `pid` tinyint(4) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vv_selfmenu`
--

INSERT INTO `vv_selfmenu` (`id`, `name`, `extra`, `type`, `pid`, `sort`) VALUES
(2, '书城首页', 'http://e10.eteawell.cn/index.php?m=&c=Mh&a=index&p_reload=1&reload_time=15', 'view', 0, 4),
(16, '阅读记录', 'http://e10.eteawell.cn/index.php?m=&c=Mh&a=book_recent_read', 'view', 0, 5),
(12, '用户中心', '', 'click', 0, 0),
(13, '个人中心', 'http://e10.eteawell.cn/index.php?m=&c=Mh&a=my&p_reload=1&reload_time=1520659303866', 'view', 12, 5),
(14, '我要充值', 'http://e10.eteawell.cn/index.php?m=&c=Mh&a=pay&p_reload=1&reload_time=1520659342378', 'view', 12, 4),
(15, '加盟代理', 'http://e10.eteawell.cn/index.php?m=&c=Public&a=regist', 'view', 12, 2),
(18, '我的收藏', 'http://e10.eteawell.cn/index.php?m=&c=Mh&a=book_shelf&p_reload=1&reload_time=1531051531359', 'view', 12, 1),
(21, '代理教程', 'http://e10.eteawell.cn', 'view', 12, 3);

-- --------------------------------------------------------

--
-- 表的结构 `vv_send`
--

CREATE TABLE IF NOT EXISTS `vv_send` (
  `id` int(10) unsigned NOT NULL,
  `send_user_id` int(10) NOT NULL DEFAULT '0',
  `get_user_id` int(10) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `create_time` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='打赏记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_separate_log`
--

CREATE TABLE IF NOT EXISTS `vv_separate_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `self_id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `money` float(10,2) NOT NULL,
  `create_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '-1已取消 1未分成 2已分成',
  `type` varchar(10) DEFAULT NULL COMMENT '分拥类型：lv:代理分拥;level:合伙人分拥'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_sign`
--

CREATE TABLE IF NOT EXISTS `vv_sign` (
  `id` int(11) NOT NULL,
  `date` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `money` float(11,2) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_slog`
--

CREATE TABLE IF NOT EXISTS `vv_slog` (
  `id` bigint(11) NOT NULL,
  `date` char(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `self_id` int(11) DEFAULT NULL,
  `money` int(10) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分享获得书币记录';

-- --------------------------------------------------------

--
-- 表的结构 `vv_teacher`
--

CREATE TABLE IF NOT EXISTS `vv_teacher` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `pic` varchar(200) DEFAULT NULL,
  `sex` varchar(2) DEFAULT NULL,
  `wxid` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `sign` varchar(200) DEFAULT NULL,
  `status` int(1) DEFAULT '0' COMMENT '1:启用；0：禁用',
  `create_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_tpl`
--

CREATE TABLE IF NOT EXISTS `vv_tpl` (
  `id` int(11) NOT NULL,
  `title` text,
  `url` text,
  `remark` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_user`
--

CREATE TABLE IF NOT EXISTS `vv_user` (
  `id` int(11) NOT NULL,
  `memid` int(11) DEFAULT '0' COMMENT '第三方代理的id',
  `openid` varchar(100) DEFAULT NULL,
  `money` float(10,2) NOT NULL DEFAULT '0.00',
  `rmb` float(10,2) DEFAULT '0.00' COMMENT '累计佣金',
  `nickname` varchar(50) DEFAULT NULL,
  `sex` tinyint(4) DEFAULT NULL,
  `headimg` varchar(256) DEFAULT NULL,
  `true_name` varchar(20) DEFAULT NULL,
  `cardno` varchar(20) DEFAULT NULL,
  `wxid` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号',
  `birth` varchar(20) NOT NULL,
  `sub_time` int(11) NOT NULL,
  `subscribe` tinyint(4) NOT NULL,
  `default_addr` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '合伙人',
  `level_status` tinyint(1) DEFAULT '1' COMMENT '合伙人启用状态:1启用；0：未启用',
  `lv` tinyint(1) NOT NULL DEFAULT '0' COMMENT '代理商等级；',
  `vip` tinyint(4) DEFAULT '0' COMMENT '是否VIP',
  `vip_s_time` int(11) DEFAULT '0' COMMENT 'vip开始时间',
  `vip_e_time` int(11) DEFAULT '0' COMMENT 'vip到期时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '代理商启用状态',
  `join_level_time` int(11) DEFAULT NULL COMMENT '加入合伙人时间',
  `join_lv_time` int(11) DEFAULT '0',
  `parent1` int(11) NOT NULL,
  `parent2` int(11) NOT NULL,
  `parent3` int(11) NOT NULL,
  `agent1` int(11) NOT NULL,
  `agent2` int(11) NOT NULL,
  `agent3` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `sales` float(10,2) DEFAULT '0.00' COMMENT '销售额',
  `btotal` float(10,2) DEFAULT '0.00' COMMENT '总购买额',
  `withdraw_total` float(10,2) DEFAULT '0.00',
  `username` varchar(255) DEFAULT '' COMMENT '用户名',
  `userpwd` varchar(255) DEFAULT '' COMMENT '密码',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间戳',
  `isend` tinyint(4) DEFAULT '0' COMMENT '是否发送过客服消息',
  `isrec` tinyint(4) DEFAULT '1' COMMENT '是否接受群发客服',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳',
  `wxtel` varchar(30) NOT NULL,
  `wxpassword` varchar(30) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `vv_user`
--

INSERT INTO `vv_user` (`id`, `memid`, `openid`, `money`, `rmb`, `nickname`, `sex`, `headimg`, `true_name`, `cardno`, `wxid`, `mobile`, `birth`, `sub_time`, `subscribe`, `default_addr`, `level`, `level_status`, `lv`, `vip`, `vip_s_time`, `vip_e_time`, `status`, `join_level_time`, `join_lv_time`, `parent1`, `parent2`, `parent3`, `agent1`, `agent2`, `agent3`, `points`, `sales`, `btotal`, `withdraw_total`, `username`, `userpwd`, `create_time`, `isend`, `isrec`, `update_time`, `wxtel`, `wxpassword`) VALUES
(1, 0, NULL, 0.00, 0.00, 'chengyc', NULL, '/Public/home/mhimages/100.jpeg', NULL, NULL, NULL, NULL, '', 1546266197, 0, 0, 0, 1, 0, 0, 0, 0, 1, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 'chengyc', 'qq27658525', 1546266197, 0, 1, 1546266197, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `vv_user_group`
--

CREATE TABLE IF NOT EXISTS `vv_user_group` (
  `uid` int(10) NOT NULL,
  `gid` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_video`
--

CREATE TABLE IF NOT EXISTS `vv_video` (
  `id` int(10) unsigned NOT NULL COMMENT '视频ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '视频标题',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图url',
  `video_url` varchar(255) NOT NULL DEFAULT '' COMMENT '视频URL',
  `summary` text NOT NULL COMMENT '视频简介',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `sort` int(10) NOT NULL DEFAULT '10' COMMENT '排序 越大越前',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `reader` int(10) NOT NULL DEFAULT '0' COMMENT '阅读数',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `trytime` int(10) DEFAULT '0' COMMENT '试看时间秒数',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `sold` int(10) DEFAULT '0' COMMENT '购买数量'
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_video_pay`
--

CREATE TABLE IF NOT EXISTS `vv_video_pay` (
  `id` int(10) unsigned NOT NULL COMMENT '记录ID',
  `sn` varchar(100) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '视频ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '视频标题',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图url',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '购买状态',
  `paysn` varchar(100) NOT NULL DEFAULT '',
  `pay_time` varchar(20) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户购买视频记录表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_withdraw`
--

CREATE TABLE IF NOT EXISTS `vv_withdraw` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `bank` text NOT NULL,
  `bank_id` int(11) NOT NULL,
  `cardno` varchar(100) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `truename` varchar(100) DEFAULT '' COMMENT '开户姓名',
  `create_time` int(11) NOT NULL,
  `audit_time` int(11) NOT NULL,
  `confirm_time` int(11) NOT NULL,
  `err_msg` text,
  `status` tinyint(4) NOT NULL COMMENT '-1已拒绝 1待转帐 2提现成功 3提现失败 4提现提交失败'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vv_yook`
--

CREATE TABLE IF NOT EXISTS `vv_yook` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `cateids` varchar(255) DEFAULT '' COMMENT '分类逗号拼接 1总裁2穿越3校园',
  `bookcate` varchar(255) DEFAULT '0' COMMENT '听书分类',
  `send` int(11) DEFAULT '0' COMMENT '打赏书币',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
  `summary` text NOT NULL COMMENT '作品简介',
  `cover_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图(列表)',
  `detail_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '详情页图片',
  `sort` int(10) NOT NULL DEFAULT '10' COMMENT '排序字段',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态:1连载 2完结',
  `free_type` tinyint(2) NOT NULL DEFAULT '2' COMMENT '属性:1免费 2付费',
  `episodes` int(10) NOT NULL DEFAULT '0' COMMENT '最新多少集(话)',
  `pay_num` int(10) NOT NULL DEFAULT '0' COMMENT '第m话开始需要付费',
  `reader` int(10) NOT NULL DEFAULT '0' COMMENT '人气值',
  `likes` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `collect` int(10) NOT NULL DEFAULT '0' COMMENT '收藏数',
  `is_new` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否最近更新 1是 0否',
  `is_recomm` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否精选推荐 1是 0否',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳',
  `readnum` int(10) DEFAULT '0',
  `chargenum` int(10) DEFAULT '0',
  `chargemoney` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='听书数据主表';

-- --------------------------------------------------------

--
-- 表的结构 `vv_yook_episodes`
--

CREATE TABLE IF NOT EXISTS `vv_yook_episodes` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `yid` int(10) NOT NULL DEFAULT '0' COMMENT '听书ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分集标题',
  `ji_no` int(10) NOT NULL DEFAULT '0' COMMENT '集数编号',
  `info` longtext COMMENT '每集内容',
  `likes` int(10) DEFAULT '0' COMMENT '点赞数',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '上集编号no',
  `next` int(10) NOT NULL DEFAULT '0' COMMENT '下集编号no',
  `money` float(10,2) DEFAULT '0.00' COMMENT '阅读需要的费用',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间戳'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='听书分集表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vv_admin`
--
ALTER TABLE `vv_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`nickname`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `vv_agent`
--
ALTER TABLE `vv_agent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_arclist`
--
ALTER TABLE `vv_arclist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_article`
--
ALTER TABLE `vv_article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_assess`
--
ALTER TABLE `vv_assess`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_autoreply`
--
ALTER TABLE `vv_autoreply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_book`
--
ALTER TABLE `vv_book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`) USING BTREE,
  ADD KEY `btype` (`create_time`) USING BTREE;

--
-- Indexes for table `vv_book_episodes`
--
ALTER TABLE `vv_book_episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anid` (`bid`) USING BTREE,
  ADD KEY `chaps` (`ji_no`) USING BTREE;

--
-- Indexes for table `vv_book_likes`
--
ALTER TABLE `vv_book_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_book_read`
--
ALTER TABLE `vv_book_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_book_ys_read`
--
ALTER TABLE `vv_book_ys_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_cart`
--
ALTER TABLE `vv_cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_chapter`
--
ALTER TABLE `vv_chapter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_charge`
--
ALTER TABLE `vv_charge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_comment`
--
ALTER TABLE `vv_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_data`
--
ALTER TABLE `vv_data`
  ADD PRIMARY KEY (`date`);

--
-- Indexes for table `vv_finance_log`
--
ALTER TABLE `vv_finance_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_goods`
--
ALTER TABLE `vv_goods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_goods_sorts`
--
ALTER TABLE `vv_goods_sorts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_group`
--
ALTER TABLE `vv_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_grow`
--
ALTER TABLE `vv_grow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_jubao`
--
ALTER TABLE `vv_jubao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mch_pay`
--
ALTER TABLE `vv_mch_pay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_member`
--
ALTER TABLE `vv_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_member_data`
--
ALTER TABLE `vv_member_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_member_desc`
--
ALTER TABLE `vv_member_desc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_member_separate`
--
ALTER TABLE `vv_member_separate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_menu`
--
ALTER TABLE `vv_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `vv_message`
--
ALTER TABLE `vv_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mh_banner`
--
ALTER TABLE `vv_mh_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mh_collect`
--
ALTER TABLE `vv_mh_collect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mh_episodes`
--
ALTER TABLE `vv_mh_episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anid` (`mhid`) USING BTREE,
  ADD KEY `chaps` (`ji_no`) USING BTREE;

--
-- Indexes for table `vv_mh_feedback`
--
ALTER TABLE `vv_mh_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mh_likes`
--
ALTER TABLE `vv_mh_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mh_list`
--
ALTER TABLE `vv_mh_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`) USING BTREE,
  ADD KEY `cateid` (`mhcate`) USING BTREE;

--
-- Indexes for table `vv_mh_read`
--
ALTER TABLE `vv_mh_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_mxsend`
--
ALTER TABLE `vv_mxsend`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_m_withdraw`
--
ALTER TABLE `vv_m_withdraw`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_notice`
--
ALTER TABLE `vv_notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_order`
--
ALTER TABLE `vv_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_pay_log`
--
ALTER TABLE `vv_pay_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_read`
--
ALTER TABLE `vv_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_read_charge`
--
ALTER TABLE `vv_read_charge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_reward`
--
ALTER TABLE `vv_reward`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_reward_record`
--
ALTER TABLE `vv_reward_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_rlog`
--
ALTER TABLE `vv_rlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_selfmenu`
--
ALTER TABLE `vv_selfmenu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_send`
--
ALTER TABLE `vv_send`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_separate_log`
--
ALTER TABLE `vv_separate_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_sign`
--
ALTER TABLE `vv_sign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_slog`
--
ALTER TABLE `vv_slog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_teacher`
--
ALTER TABLE `vv_teacher`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_tpl`
--
ALTER TABLE `vv_tpl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_user`
--
ALTER TABLE `vv_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`) USING BTREE;

--
-- Indexes for table `vv_video`
--
ALTER TABLE `vv_video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_video_pay`
--
ALTER TABLE `vv_video_pay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_withdraw`
--
ALTER TABLE `vv_withdraw`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_yook`
--
ALTER TABLE `vv_yook`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vv_yook_episodes`
--
ALTER TABLE `vv_yook_episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`) USING BTREE,
  ADD KEY `ji_no` (`ji_no`) USING BTREE,
  ADD KEY `yid` (`yid`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vv_admin`
--
ALTER TABLE `vv_admin`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `vv_agent`
--
ALTER TABLE `vv_agent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `vv_arclist`
--
ALTER TABLE `vv_arclist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_article`
--
ALTER TABLE `vv_article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_assess`
--
ALTER TABLE `vv_assess`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_autoreply`
--
ALTER TABLE `vv_autoreply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `vv_book`
--
ALTER TABLE `vv_book`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=133;
--
-- AUTO_INCREMENT for table `vv_book_episodes`
--
ALTER TABLE `vv_book_episodes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=111756;
--
-- AUTO_INCREMENT for table `vv_book_likes`
--
ALTER TABLE `vv_book_likes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '小说分集记录ID';
--
-- AUTO_INCREMENT for table `vv_book_read`
--
ALTER TABLE `vv_book_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_book_ys_read`
--
ALTER TABLE `vv_book_ys_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_cart`
--
ALTER TABLE `vv_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_chapter`
--
ALTER TABLE `vv_chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_charge`
--
ALTER TABLE `vv_charge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_comment`
--
ALTER TABLE `vv_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_finance_log`
--
ALTER TABLE `vv_finance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_goods`
--
ALTER TABLE `vv_goods`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_goods_sorts`
--
ALTER TABLE `vv_goods_sorts`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_group`
--
ALTER TABLE `vv_group`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `vv_grow`
--
ALTER TABLE `vv_grow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_jubao`
--
ALTER TABLE `vv_jubao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_mch_pay`
--
ALTER TABLE `vv_mch_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_member`
--
ALTER TABLE `vv_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_member_data`
--
ALTER TABLE `vv_member_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `vv_member_desc`
--
ALTER TABLE `vv_member_desc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `vv_member_separate`
--
ALTER TABLE `vv_member_separate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_menu`
--
ALTER TABLE `vv_menu`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT for table `vv_message`
--
ALTER TABLE `vv_message`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_mh_banner`
--
ALTER TABLE `vv_mh_banner`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '漫画分集记录ID',AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `vv_mh_collect`
--
ALTER TABLE `vv_mh_collect`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏记录ID';
--
-- AUTO_INCREMENT for table `vv_mh_episodes`
--
ALTER TABLE `vv_mh_episodes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5528;
--
-- AUTO_INCREMENT for table `vv_mh_feedback`
--
ALTER TABLE `vv_mh_feedback`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '反馈ID';
--
-- AUTO_INCREMENT for table `vv_mh_likes`
--
ALTER TABLE `vv_mh_likes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '漫画分集记录ID';
--
-- AUTO_INCREMENT for table `vv_mh_list`
--
ALTER TABLE `vv_mh_list`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '漫画ID',AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT for table `vv_mh_read`
--
ALTER TABLE `vv_mh_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_mxsend`
--
ALTER TABLE `vv_mxsend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_m_withdraw`
--
ALTER TABLE `vv_m_withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_notice`
--
ALTER TABLE `vv_notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `vv_order`
--
ALTER TABLE `vv_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_pay_log`
--
ALTER TABLE `vv_pay_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_read`
--
ALTER TABLE `vv_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `vv_read_charge`
--
ALTER TABLE `vv_read_charge`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_reward`
--
ALTER TABLE `vv_reward`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_reward_record`
--
ALTER TABLE `vv_reward_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_rlog`
--
ALTER TABLE `vv_rlog`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `vv_selfmenu`
--
ALTER TABLE `vv_selfmenu`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `vv_send`
--
ALTER TABLE `vv_send`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_separate_log`
--
ALTER TABLE `vv_separate_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_sign`
--
ALTER TABLE `vv_sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_slog`
--
ALTER TABLE `vv_slog`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_teacher`
--
ALTER TABLE `vv_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_tpl`
--
ALTER TABLE `vv_tpl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_user`
--
ALTER TABLE `vv_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `vv_video`
--
ALTER TABLE `vv_video`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '视频ID',AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `vv_video_pay`
--
ALTER TABLE `vv_video_pay`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';
--
-- AUTO_INCREMENT for table `vv_withdraw`
--
ALTER TABLE `vv_withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vv_yook`
--
ALTER TABLE `vv_yook`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- AUTO_INCREMENT for table `vv_yook_episodes`
--
ALTER TABLE `vv_yook_episodes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
