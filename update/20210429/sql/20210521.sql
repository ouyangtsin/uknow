ALTER TABLE `uk_nav`
    ADD COLUMN `seo_title` varchar(100) NULL COMMENT 'SEO标题',
ADD COLUMN `seo_keywords` varchar(255) NULL COMMENT 'SEO关键词',
ADD COLUMN `seo_description` varchar(255) NULL COMMENT 'SEO描述';

ALTER TABLE `uk_question`
    ADD COLUMN `seo_title` varchar(100) NULL COMMENT 'SEO标题',
ADD COLUMN `seo_keywords` varchar(255) NULL COMMENT 'SEO关键词',
ADD COLUMN `seo_description` varchar(255) NULL COMMENT 'SEO描述';


ALTER TABLE `uk_article`
    ADD COLUMN `seo_title` varchar(100) NULL COMMENT 'SEO标题',
ADD COLUMN `seo_keywords` varchar(255) NULL COMMENT 'SEO关键词',
ADD COLUMN `seo_description` varchar(255) NULL COMMENT 'SEO描述';

ALTER TABLE `uk_email_log`
    ADD COLUMN `create_time` int(10) DEFAULT '0';

CREATE TABLE `uk_invite` (
     `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '激活ID',
     `uid` int DEFAULT '0' COMMENT '用户ID',
     `invite_code` varchar(32) DEFAULT NULL COMMENT '激活码',
     `invite_email` varchar(255) DEFAULT NULL COMMENT '激活email',
     `create_time` int DEFAULT NULL COMMENT '添加时间',
     `create_ip` bigint DEFAULT NULL COMMENT '添加IP',
     `active_expire` tinyint(1) DEFAULT '0' COMMENT '激活过期',
     `active_time` int DEFAULT NULL COMMENT '激活时间',
     `active_ip` bigint DEFAULT NULL COMMENT '激活IP',
     `active_status` tinyint DEFAULT '0' COMMENT '1已使用0未使用-1已删除',
     `active_uid` int DEFAULT NULL,
     PRIMARY KEY (`id`),
     KEY `uid` (`uid`),
     KEY `invite_code` (`invite_code`),
     KEY `invite_email` (`invite_email`),
     KEY `active_time` (`active_time`),
     KEY `active_ip` (`active_ip`),
     KEY `active_status` (`active_status`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='邀请码管理';

ALTER TABLE `uk_users`
    ADD COLUMN `available_invite_count` int UNSIGNED NULL DEFAULT 0 COMMENT '可用邀请数量';

INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (11, 'power', 'available_invite_count', '可邀请用户数量', '可邀请用户数量', 'number', '0', '',  0);

ALTER TABLE `uk_article_comment`
    ADD COLUMN `pid` int UNSIGNED NULL DEFAULT 0 COMMENT '父级评论';

DROP TABLE IF EXISTS `uk_answer_thanks`;
CREATE TABLE `uk_answer_thanks` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `uid` int DEFAULT '0',
        `answer_id` int DEFAULT '0',
        `create_time` int DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `answer_id` (`answer_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='回答感谢表';

DROP TABLE IF EXISTS `uk_uninterested`;
CREATE TABLE `uk_uninterested` (
   `id` int unsigned NOT NULL AUTO_INCREMENT,
   `uid` int DEFAULT '0' COMMENT '用户ID',
   `item_id` int DEFAULT '0' COMMENT '内容ID',
   `item_type` varchar(100) DEFAULT '' COMMENT '内容类型',
   `create_time` int DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `item_id` (`item_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='内容不感兴趣表';

INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (59, 'uninterested_power_factor', 'power', '不感兴趣声望系数', '', 'number', '3', '[]', 0, 1622120863, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (60, 'thanks_power_factor', 'power', '感谢声望系数', '', 'number', '5', '[]', 0, 1622120962, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (61, 'verify_user_power_factor', 'power', '认证会员赞踩系数', '', 'number', '3', '[]', 0, 1622120863, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (62, 'publish_user_power_factor', 'power', '提问者赞踩系数', '', 'number', '2', '[]', 0, 1622120962, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (63, 'power_log_factor', 'power', '声望对底系数', '', 'number', '2', '[]', 0, 1622120962, 0);

DROP TABLE IF EXISTS `uk_route_rule`;
CREATE TABLE `uk_route_rule` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `url` varchar(255) DEFAULT NULL COMMENT 'url',
     `param` varchar(255) DEFAULT NULL COMMENT '参数',
     `title` varchar(255) DEFAULT NULL COMMENT '标题',
     `separator` varchar(50) DEFAULT '/' COMMENT '分隔符',
     `rule` varchar(255) DEFAULT NULL COMMENT '规则',
     `module` varchar(255) DEFAULT NULL,
     PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='路由配置';

INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (64, 'register_agreement', 'member', '注册协议', '', 'textarea', '当您申请用户时，表示您已经同意遵守本规章。\r\n欢迎您加入本站点参与交流和讨论，本站点为社区，为维护网上公共秩序和社会稳定，请您自觉遵守以下条款：\r\n\r\n一、不得利用本站危害国家安全、泄露国家秘密，不得侵犯国家社会集体的和公民的合法权益，不得利用本站制作、复制和传播下列信息：\r\n　（一）煽动抗拒、破坏宪法和法律、行政法规实施的；\r\n　（二）煽动颠覆国家政权，推翻社会主义制度的；\r\n　（三）煽动分裂国家、破坏国家统一的；\r\n　（四）煽动民族仇恨、民族歧视，破坏民族团结的；\r\n　（五）捏造或者歪曲事实，散布谣言，扰乱社会秩序的；\r\n　（六）宣扬封建迷信、淫秽、色情、赌博、暴力、凶杀、恐怖、教唆犯罪的；\r\n　（七）公然侮辱他人或者捏造事实诽谤他人的，或者进行其他恶意攻击的；\r\n　（八）损害国家机关信誉的；\r\n　（九）其他违反宪法和法律行政法规的；\r\n　（十）进行商业广告行为的。\r\n\r\n二、互相尊重，对自己的言论和行为负责。\r\n三、禁止在申请用户时使用相关本站的词汇，或是带有侮辱、毁谤、造谣类的或是有其含义的各种语言进行注册用户，否则我们会将其删除。\r\n四、禁止以任何方式对本站进行各种破坏行为。\r\n五、如果您有违反国家相关法律法规的行为，本站概不负责，您的登录信息均被记录无疑，必要时，我们会向相关的国家管理部门提供此类信息。', '[]', 0, 1622299395, 1622299395);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (65, 'reward_enable', 'site', '启用打赏功能', '启用打赏功能', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (66, 'reward_question_enable', 'site', '启用悬赏功能', '', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (67, 'reward_default_money', 'site', '悬赏金额默认配置;多个用,隔开', '', 'textarea', '1,2,3,5,10,20,30,50', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (68, 'pay_enable', 'open', '启用支付功能', '启用支付功能', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (69, 'pay_type', 'open', '支付方式', '选择支付方式', 'checkbox', 'balance', '{\"wechat\":\"微信支付\",\"alipay\":\"支付宝支付\",\"balance\":\"余额支付\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (70, 'wechat_app_id', 'open', '微信appId', '微信支付appId', 'text', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (71, 'wechat_mch_id', 'open', '微信mchId', '微信支付商户号', 'text', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (72, 'wechat_mch_key', 'open', '微信mchKey', '微信支付密钥', 'text', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (73, 'ssl_cer', 'open', '微信ssl_cer', '微信证书cert', 'textarea', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (74, 'ssl_key', 'open', '微信ssl_key', '微信证书key', 'textarea', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (75, 'alipay_app_id', 'open', '支付宝应用ID', '支付宝应用app_id', 'text', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (76, 'public_key', 'open', '支付宝公钥', '支付宝公钥(1行填写)', 'textarea', '', '', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (77, 'private_key', 'open', '支付宝私钥', '支付宝私钥(1行填写)', 'textarea', '', '', 0, 1621578838, 1621578838);

ALTER TABLE `uk_question`
    ADD COLUMN `reward_money` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '赏金',
    ADD COLUMN `reward_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '悬赏截止总时间戳',
    ADD COLUMN `look_enable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启围观0开放回答,1付费围观';

CREATE TABLE IF NOT EXISTS `uk_pay_order` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
    `title` varchar(50) DEFAULT NULL COMMENT '标题',
    `uid` int(11) DEFAULT NULL COMMENT '用户id',
    `trade_no` varchar(20) DEFAULT NULL COMMENT '系统订单号',
    `out_trade_no` varchar(255) DEFAULT NULL COMMENT '三方订单号',
    `order_type` varchar(50) DEFAULT NULL COMMENT '交易类型',
    `pay_type` varchar(50) DEFAULT NULL COMMENT '付款方式',
    `relation_type` varchar(50) DEFAULT NULL COMMENT '关联类型',
    `relation_id` int(11) DEFAULT NULL COMMENT '关联id',
    `amount` decimal(11,2) DEFAULT '0.00' COMMENT '交易金额',
    `status` tinyint(1) DEFAULT NULL COMMENT '交易状态（0：未完成，1：已完成，2：失败）',
    `remark` varchar(255) DEFAULT NULL COMMENT '备注',
    `create_time` int(10) DEFAULT NULL COMMENT '交易时间',
    `update_time` int(10) DEFAULT NULL COMMENT '交易完成时间',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='交易流水';
CREATE TABLE IF NOT EXISTS `uk_pay_log` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL COMMENT '用户id',
    `order_id` int(10) NOT NULL COMMENT '交易流水id',
    `item_id` int(11) NOT NULL COMMENT '关联id',
    `item_type` tinyint(1) NOT NULL COMMENT '关联类型',
    `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
    `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
    `pay_type` varchar(50) DEFAULT NULL COMMENT '付款方式',
    `status` tinyint(1) NOT NULL COMMENT '状态：0删除，1成功，2失败',
    `remark` varchar(255) NOT NULL COMMENT '备注',
    `create_time` int(10) NOT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `order_id` (`order_id`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='财务日志';

INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (78, 'download_image_to_local', 'site', '图片本地化', '将复制的内容图片远程下载到本地', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (79, 'water_author_text_enable', 'site', '作者水印', '发表内容添加作者水印', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);


ALTER TABLE `uk_users`
    ADD COLUMN `deal_password` varchar(64) NULL COMMENT '交易密码' AFTER `available_invite_count`;

INSERT INTO `uk_auth_rule` VALUES (196, 4, 'module.Order/index', '交易流水', 1, 1, '', 31, 1, 'icon-shopping-cart', 1580881507, 1580881507, '');
INSERT INTO `uk_auth_rule` VALUES (197, 196, 'module.Order/detail', '操作-详情', 1, 0, '', 1, 1, '', 1580881536, 1580881536, '');
INSERT INTO `uk_auth_rule` VALUES (198, 196, 'module.Order/delete', '操作-删除', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (199, 4, 'module.Log/index', '资金记录', 1, 1, '', 3, 1, 'icon-slack', 1580881596, 1580881596, '');
INSERT INTO `uk_auth_rule` VALUES (200, 199, 'module.Log/detail', '操作-详情', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');

INSERT INTO `uk_auth_rule` VALUES (201, 4, 'wechat', '微信管理', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (202, 201, 'wechat.Account/index', '微信账号', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (203, 202, 'wechat.Account/add', '操作-添加', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (204, 202, 'wechat.Account/edit', '操作-编辑', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (205, 202, 'wechat.Account/delete', '操作-删除', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (206, 201, 'wechat.Fans/index', '微信粉丝', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (207, 206, 'wechat.Fans/synchro', '操作-同步', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (208, 206, 'wechat.Fans/change_tag_group', '操作-更改', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (209, 201, 'wechat.Message/index', '微信消息', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (210, 209, 'wechat.Message/reply', '操作-回复', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (211, 201, 'wechat.Qrcode/index', '微信二维码', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (212, 211, 'wechat.Qrcode/add', '操作-添加', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (213, 211, 'wechat.Qrcode/delete', '操作-删除', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (214, 201, 'wechat.Reply/index', '微信回复', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (215, 214, 'wechat.Reply/add', '操作-添加', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (216, 214, 'wechat.Reply/edit', '操作-编辑', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (217, 214, 'wechat.Reply/delete', '操作-删除', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (218, 201, 'wechat.Material/index', '微信素材', 1, 1, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (219, 218, 'wechat.Material/add', '操作-添加', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (220, 218, 'wechat.Material/edit', '操作-编辑', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (221, 218, 'wechat.Material/delete', '操作-删除', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (222, 218, 'wechat.Material/synchro', '操作-同步', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (223, 218, 'wechat.Material/preview', '操作-预览', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');