CREATE TABLE `check_stock` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `stock_code` varchar(50) NOT NULL COMMENT '股票代码',
  `stock_price` float(8,3) NOT NULL,
  `check_time` varchar(20) NOT NULL COMMENT '检测时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE `my_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `stock_code` varchar(50) NOT NULL DEFAULT '' COMMENT '股票代码',
  `stock_name` varchar(50) NOT NULL DEFAULT '' COMMENT '股票名称',
  `price` float(8,3) NOT NULL DEFAULT '0.000' COMMENT '自己的持仓成本',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `select_stock` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(8) NOT NULL COMMENT '用户ID',
  `stock_code` varchar(50) NOT NULL,
  `stock_price` float(8,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '关注价格',
  `check_type` int(8) NOT NULL DEFAULT '0' COMMENT '检测上限1下限0',
  `stock_name` varchar(50) NOT NULL COMMENT '股票名称',
  `alter_time` varchar(50) NOT NULL DEFAULT '',
  `stock_status` tinyint(2) unsigned NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `uid_index` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

