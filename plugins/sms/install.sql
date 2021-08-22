CREATE TABLE `uk_sms_log` (
     `id` int NOT NULL AUTO_INCREMENT COMMENT 'id',
     `mobile` char(11) NOT NULL COMMENT '手机号',
     `send_type` varchar(32) NOT NULL COMMENT '短信商',
     `template_code` varchar(32) NOT NULL COMMENT '模板id',
     `content` text COMMENT '短信内容',
     `ip` varchar(32) NOT NULL COMMENT 'ip',
     `create_time` int DEFAULT '0' COMMENT '添加时间',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信记录表';