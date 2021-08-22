CREATE TABLE IF NOT EXISTS `uk_third` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `platform` varchar(30) NOT NULL DEFAULT '' COMMENT '第三方应用',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方唯一ID',
  `unionid` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方唯一ID',
  `open_username` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方会员昵称',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `refresh_token` varchar(255) NOT NULL DEFAULT 'RefreshToken',
  `expires_in` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `login_time` int(10) unsigned DEFAULT NULL COMMENT '登录时间',
  `expire_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform` (`platform`,`openid`),
  KEY `uid` (`uid`,`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='第三方登录表';