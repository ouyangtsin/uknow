ALTER TABLE `uk_answer`
    ADD COLUMN `popular_value` double NULL DEFAULT 0 COMMENT '热度值',
ADD COLUMN `popular_value_update` int UNSIGNED NULL DEFAULT 0 COMMENT '热度值更新时间' ;

ALTER TABLE `uk_article`
    ADD COLUMN `popular_value` double NULL DEFAULT 0 COMMENT '热度值',
ADD COLUMN `popular_value_update` int UNSIGNED NULL DEFAULT 0 COMMENT '热度值更新时间' ;

ALTER TABLE `uk_column`
    ADD COLUMN `popular_value` double NULL DEFAULT 0 COMMENT '热度值',
    ADD COLUMN `popular_value_update` int UNSIGNED NULL DEFAULT 0 COMMENT '热度值更新时间' ;

ALTER TABLE `uk_users`
    ADD COLUMN `is_valid_email` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否验证邮箱',
ADD COLUMN `is_valid_mobile` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否验证手机号';

DROP TABLE IF EXISTS `uk_users_active`;
CREATE TABLE `uk_users_active` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `uid` int(11) DEFAULT '0',
   `expire_time` int(10) DEFAULT NULL,
   `active_code` varchar(32) DEFAULT NULL,
   `active_type` varchar(50) DEFAULT NULL,
   `create_time` int(10) DEFAULT NULL,
   `create_valid_ip` bigint(12) DEFAULT NULL,
   `active_time` int(10) DEFAULT NULL,
   `active_ip` bigint(12) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `active_code` (`active_code`),
   KEY `active_type` (`active_type`),
   KEY `uid` (`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='用户激活码';