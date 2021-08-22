SET NAMES utf8mb4;
-- ----------------------------
-- Table structure for uk_action
-- ----------------------------
DROP TABLE IF EXISTS `uk_action`;
CREATE TABLE `uk_action` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
     `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
     `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
     `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
     `action_rule` text COMMENT '行为规则',
     `log_rule` text COMMENT '日志规则',
     `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
     `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
     `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
     UNIQUE KEY `name` (`name`) USING BTREE,
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户行为表';

-- ----------------------------
-- Records of uk_action
-- ----------------------------
BEGIN;
INSERT INTO `uk_action` VALUES (1, 'user_login', '用户登录', '用户登录系统', '', '[user|get_link_username] 在 [time|formatTime] 登录了系统', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (2, 'publish_question', '发起提问', '发起提问', '', '[user|get_link_username] 在 [time|formatTime] 发起了提问', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (3, 'publish_article', '发表文章', '发表文章', '', '[user|get_link_username] 在 [time|formatTime] 发表了文章', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (4, 'publish_answer', '发表回答', '发表回答', '', '[user|get_link_username] 在 [time|formatTime] 发表了回答', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (5, 'publish_article_comment', '发表文章评论', '发表文章评论', '', '[user|get_link_username] 在 [time|formatTime] 发表了文章评论', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (6, 'agree_question', '点赞问题', '点赞问题', '', '[user|get_link_username] 在 [time|formatTime] 点赞了问题', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (7, 'agree_article', '点赞文章', '点赞文章', '', '[user|get_link_username] 在 [time|formatTime] 点赞了文章', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (8, 'agree_answer', '点赞回答', '点赞回答', '', '[user|get_link_username] 在 [time|formatTime] 点赞了回答', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (9, 'focus_user', '关注用户', '关注用户', '', '[user|get_link_username] 在 [time|formatTime] 关注了用户', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (10, 'focus_question', '关注问题', '关注问题', '', '[user|get_link_username] 在 [time|formatTime] 关注了问题', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (11, 'modify_answer', '修改回答', '修改回答', '', '[user|get_link_username] 在 [time|formatTime] 修改了回答', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (12, 'modify_question', '修改提问', '修改提问', '', '[user|get_link_username] 在 [time|formatTime] 修改了提问', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (13, 'modify_article', '修改文章', '修改文章', '', '[user|get_link_username] 在 [time|formatTime] 修改了文章', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (14, 'chance_focus_user', '取消关注用户', '取消关注用户', '', '[user|get_link_username] 在 [time|formatTime] 取消关注了用户', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (15, 'chance_focus_question', '取消关注问题', '取消关注问题', '', '[user|get_link_username] 在 [time|formatTime] 取消关注了问题', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (16, 'chance_focus_column', '取消关注专栏', '取消关注专栏', '', '[user|get_link_username] 在 [time|formatTime] 取消关注了专栏', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (17, 'chance_focus_topic', '取消关注话题', '取消关注话题', '', '[user|get_link_username] 在 [time|formatTime] 取消关注了话题', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (18, 'chance_focus_favorite', '取消关注收藏夹', '取消关注收藏夹', '', '[user|get_link_username] 在 [time|formatTime] 取消关注了收藏夹', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (19, 'focus_column', '关注专栏', '关注专栏', '', '[user|get_link_username] 在 [time|formatTime] 关注了专栏', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (20, 'focus_topic', '关注话题', '关注话题', '', '[user|get_link_username] 在 [time|formatTime] 关注了话题', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (21, 'focus_favorite', '关注收藏夹', '关注收藏夹', '', '[user|get_link_username] 在 [time|formatTime] 关注了收藏夹', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (22, 'agree_article_comment', '点赞文章评论', '点赞文章评论', '', '[user|get_link_username] 在 [time|formatTime] 点赞了文章评论', 1, 1387181220, 0);
INSERT INTO `uk_action` VALUES (23, 'agree_answer_comment', '点赞回答评论', '点赞回答评论', '', '[user|get_link_username] 在 [time|formatTime] 点赞了回答评论', 1, 1387181220, 0);
COMMIT;

-- ----------------------------
-- Table structure for uk_action_log
-- ----------------------------
DROP TABLE IF EXISTS `uk_action_log`;
CREATE TABLE `uk_action_log` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
     `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
     `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
     `action_ip` varchar(20) NOT NULL COMMENT '执行行为者ip',
     `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
     `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
     `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
     `anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
     `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
     `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `action_ip` (`action_ip`) USING BTREE,
     KEY `action_id` (`action_id`) USING BTREE,
     KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='行为日志表';

-- ----------------------------
-- Table structure for uk_answer
-- ----------------------------
DROP TABLE IF EXISTS `uk_answer`;
CREATE TABLE `uk_answer` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '回答id',
     `question_id` int(11) unsigned NOT NULL COMMENT '问题id',
     `content` text COMMENT '回答内容',
     `against_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '反对人数',
     `agree_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支持人数',
     `uid` int(11) unsigned DEFAULT '0' COMMENT '发布问题用户ID',
     `comment_count` int(11) unsigned DEFAULT '0' COMMENT '评论总数',
     `uninterested_count` int(11) unsigned DEFAULT '0' COMMENT '不感兴趣',
     `thanks_count` int(11) unsigned DEFAULT '0' COMMENT '感谢数量',
     `answer_user_ip` varchar(20) DEFAULT NULL COMMENT '回答用户的来源IP',
     `is_anonymous` tinyint(1) unsigned DEFAULT '0' COMMENT '是否匿名回答',
     `publish_source` varchar(16) CHARACTER SET utf8 DEFAULT NULL COMMENT '回答内容来源',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '内容状态',
     `is_best` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为最佳回复1是',
     `best_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定人id',
     `best_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定时间',
     `comment` tinyint DEFAULT NULL COMMENT '评论权限：0禁止评论1允许2由我筛选3我关注的人',
     `reprint` tinyint unsigned DEFAULT '1' COMMENT '转载方式:0禁止转载1允许转载2付费转载',
     `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
     `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
     `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
     `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `question_id` (`question_id`) USING BTREE,
     KEY `agree_count` (`agree_count`) USING BTREE,
     KEY `against_count` (`against_count`) USING BTREE,
     KEY `create_time` (`create_time`) USING BTREE,
     KEY `uid` (`uid`) USING BTREE,
     KEY `uninterested_count` (`uninterested_count`) USING BTREE,
     KEY `is_anonymous` (`is_anonymous`) USING BTREE,
     KEY `publish_source` (`publish_source`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='问题回答表';

-- ----------------------------
-- Records of uk_answer
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_answer_comment
-- ----------------------------
DROP TABLE IF EXISTS `uk_answer_comment`;
CREATE TABLE `uk_answer_comment` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `answer_id` int(11) DEFAULT '0' COMMENT '问题id',
     `uid` int(11) DEFAULT '0' COMMENT '评论人',
     `message` text COMMENT '评论内容',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
     `at_info` varchar(100) DEFAULT NULL COMMENT '用户信息',
     `create_time` int(10) DEFAULT '0' COMMENT '评论时间',
     `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `answer_id` (`answer_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='回答评论表';

-- ----------------------------
-- Records of uk_answer_comment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_approval
-- ----------------------------
DROP TABLE IF EXISTS `uk_approval`;
CREATE TABLE `uk_approval` (
   `id` int(10) NOT NULL AUTO_INCREMENT,
   `type` varchar(16) DEFAULT NULL COMMENT '审核类型',
   `data` mediumtext NOT NULL COMMENT '审核数据',
   `uid` int(11) NOT NULL DEFAULT '0',
   `create_time` int(10) NOT NULL DEFAULT '0',
   `reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
   `status` int(11) NOT NULL DEFAULT '0' COMMENT '0待审核,已审核,2已拒绝',
   PRIMARY KEY (`id`) USING BTREE,
   KEY `type` (`type`) USING BTREE,
   KEY `uid` (`uid`) USING BTREE,
   KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='审核表';

-- ----------------------------
-- Records of uk_approval
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_article
-- ----------------------------
DROP TABLE IF EXISTS `uk_article`;
CREATE TABLE `uk_article` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `uid` int(10) NOT NULL,
          `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
          `message` text CHARACTER SET utf8mb4,
          `comment_count` int(10) DEFAULT '0',
          `view_count` int(10) DEFAULT '0',
          `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞同数总和',
          `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对数总和',
          `category_id` int(10) DEFAULT '0',
          `is_recommend` tinyint(1) DEFAULT '0',
          `sort` tinyint(2) unsigned NOT NULL DEFAULT '0',
          `column_id` int(11) DEFAULT NULL COMMENT '所属专栏id',
          `cover` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '文章封面',
          `status` tinyint(1) unsigned DEFAULT '0' COMMENT '文章状态1正常0删除2待审核3待发布',
          `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
          `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
          `wait_time` int(10) DEFAULT '0' COMMENT '定时时间',
          `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
          `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
          `seo_title` varchar(100) DEFAULT NULL COMMENT 'SEO标题',
          `seo_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键词',
          `seo_description` varchar(255) DEFAULT NULL COMMENT 'SEO描述',
          `create_time` int(10) DEFAULT '0' COMMENT '发布时间',
          `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
          PRIMARY KEY (`id`) USING BTREE,
          KEY `uid` (`uid`) USING BTREE,
          KEY `comment_count` (`comment_count`) USING BTREE,
          KEY `view_count` (`view_count`) USING BTREE,
          KEY `category_id` (`category_id`) USING BTREE,
          KEY `is_recommend` (`is_recommend`) USING BTREE,
          KEY `sort` (`sort`) USING BTREE,
          FULLTEXT KEY title_message_fulltext(title,message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for uk_article_comment
-- ----------------------------
DROP TABLE IF EXISTS `uk_article_comment`;
CREATE TABLE `uk_article_comment` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `uid` int(10) NOT NULL,
      `article_id` int(10) NOT NULL,
      `message` text NOT NULL,
      `create_time` int(10) NOT NULL,
      `at_uid` varchar(50) DEFAULT NULL,
      `pid` int UNSIGNED NULL DEFAULT 0 COMMENT '父级评论',
      `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞同数总和',
      `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对数总和',
      `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '内容状态',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE,
      KEY `article_id` (`article_id`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文章评论表';

BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_article_vote
-- ----------------------------
DROP TABLE IF EXISTS `uk_article_vote`;
CREATE TABLE `uk_article_vote` (
       `id` int(10) NOT NULL AUTO_INCREMENT,
       `uid` int(10) NOT NULL COMMENT '投票用户',
       `item_type` varchar(16) DEFAULT NULL COMMENT '内容类型',
       `item_id` int(10) NOT NULL COMMENT '内容ID',
       `vote_value` tinyint(1) DEFAULT '0' COMMENT '1赞同,-1反对',
       `create_time` int(10) NOT NULL COMMENT '操作时间',
       `weigh_factor` int(10) DEFAULT '0' COMMENT '赞同反对系数',
       `item_uid` int(10) DEFAULT '0' COMMENT '被投票用户',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `uid` (`uid`) USING BTREE,
       KEY `item_type` (`item_type`) USING BTREE,
       KEY `item_id` (`item_id`) USING BTREE,
       KEY `create_time` (`create_time`) USING BTREE,
       KEY `item_uid` (`item_uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文章赞踩表';

-- ----------------------------
-- Records of uk_article_vote
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_attach
-- ----------------------------
DROP TABLE IF EXISTS `uk_attach`;
CREATE TABLE `uk_attach` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户表id',
         `name` varchar(255) DEFAULT NULL COMMENT '文件名',
         `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
         `path` varchar(255) DEFAULT NULL COMMENT '路径',
         `url` varchar(255) DEFAULT NULL COMMENT '完整地址',
         `ext` varchar(5) DEFAULT NULL COMMENT '后缀',
         `size` int(11) DEFAULT '0' COMMENT '大小',
         `width` varchar(30) DEFAULT '0' COMMENT '宽度',
         `height` varchar(30) DEFAULT '0' COMMENT '高度',
         `md5` char(32) DEFAULT NULL,
         `sha1` varchar(64) DEFAULT NULL,
         `mime` varchar(80) DEFAULT NULL,
         `driver` varchar(20) DEFAULT 'local',
         `create_time` int(11) DEFAULT NULL,
         `update_time` int(11) DEFAULT NULL,
         `status` tinyint(1) NOT NULL DEFAULT '1',
         `sort` int(5) NOT NULL DEFAULT '50',
         PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件表';

-- ----------------------------
-- Records of uk_attach
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `uk_auth_group`;
CREATE TABLE `uk_auth_group` (
     `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
     `title` char(100) NOT NULL DEFAULT '' COMMENT '组名称',
     `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
     `rules` text NOT NULL  COMMENT '后台用户组拥有的规则id， 多个规则","隔开',
     `permission` text COMMENT '前台控制权限',
     `create_time` int(10) unsigned DEFAULT '0' COMMENT '修改时间',
     `update_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_auth_group
-- ----------------------------
BEGIN;
INSERT INTO `uk_auth_group` (`id`, `title`, `status`, `rules`, `permission`, `create_time`, `update_time`) VALUES (1, '超级管理员组', 1, '*', '{\"visit_website\":\"1\",\"publish_question_enable\":\"1\",\"publish_question_approval\":\"1\",\"publish_article_enable\":\"1\",\"publish_article_approval\":\"1\",\"publish_answer_enable\":\"1\",\"publish_answer_approval\":\"1\",\"modify_answer_approval\":\"1\",\"modify_article_approval\":\"1\",\"modify_question_approval\":\"1\",\"create_topic_enable\":1}', 0, 1613807404);
INSERT INTO `uk_auth_group` (`id`, `title`, `status`, `rules`, `permission`, `create_time`, `update_time`) VALUES (2, '前台管理员', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49', '{\"visit_website\":\"1\",\"publish_question_enable\":\"1\",\"publish_question_approval\":\"1\",\"publish_article_enable\":\"1\",\"publish_article_approval\":\"1\",\"publish_answer_enable\":\"1\",\"publish_answer_approval\":\"1\",\"modify_answer_approval\":\"1\",\"modify_article_approval\":\"1\",\"modify_question_approval\":\"1\",\"create_topic_enable\":1}', 1603886884, 1613815597);
INSERT INTO `uk_auth_group` (`id`, `title`, `status`, `rules`, `permission`, `create_time`, `update_time`) VALUES (3, '普通会员组', 1, '', '{\"visit_website\":\"1\",\"publish_question_enable\":\"1\",\"publish_question_approval\":\"1\",\"publish_article_enable\":\"1\",\"publish_article_approval\":\"1\",\"publish_answer_enable\":\"1\",\"publish_answer_approval\":\"1\",\"modify_answer_approval\":\"1\",\"modify_article_approval\":\"1\",\"modify_question_approval\":\"1\",\"create_topic_enable\":0}', 1603886900, 1613801433);
INSERT INTO `uk_auth_group` (`id`, `title`, `status`, `rules`, `permission`, `create_time`, `update_time`) VALUES (4, '待验证会员组', 1, '', '{\"visit_website\":\"1\",\"publish_question_enable\":\"1\",\"publish_question_approval\":\"1\",\"publish_article_enable\":\"1\",\"publish_article_approval\":\"1\",\"publish_answer_enable\":\"0\",\"publish_answer_approval\":\"1\",\"modify_answer_approval\":\"1\",\"modify_article_approval\":\"1\",\"modify_question_approval\":\"1\",\"create_topic_enable\":0}', 1603886900, 1613801433);
INSERT INTO `uk_auth_group` (`id`, `title`, `status`, `rules`, `permission`, `create_time`, `update_time`) VALUES (5, '游客组', 1, '', '{\"visit_website\":\"1\",\"publish_question_enable\":\"0\",\"publish_question_approval\":\"0\",\"publish_article_enable\":\"0\",\"publish_article_approval\":\"0\",\"publish_answer_enable\":\"0\",\"publish_answer_approval\":\"0\",\"modify_answer_approval\":\"0\",\"modify_article_approval\":\"0\",\"modify_question_approval\":\"0\",\"create_topic_enable\":0}', 1619249099, 1619249099);
COMMIT;

-- ----------------------------
-- Table structure for uk_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `uk_auth_group_access`;
CREATE TABLE `uk_auth_group_access` (
    `uid` mediumint(8) unsigned NOT NULL COMMENT '用户id',
    `group_id` mediumint(8) unsigned NOT NULL COMMENT '权限组id',
    `score_group_id` int(10) unsigned DEFAULT '0' COMMENT '积分组id',
    `power_group_id` int(10) unsigned DEFAULT '0' COMMENT '声望组id',
    `create_time` int(10) unsigned DEFAULT '0' COMMENT '最后修改时间',
    `update_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
    UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for uk_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `uk_auth_rule`;
CREATE TABLE `uk_auth_rule` (
        `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
        `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
        `name` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器/方法',
        `title` char(20) NOT NULL DEFAULT '',
        `type` tinyint(1) NOT NULL DEFAULT '1',
        `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '菜单状态',
        `condition` char(100) NOT NULL DEFAULT '',
        `sort` mediumint(8) NOT NULL DEFAULT '0' COMMENT '排序',
        `auth_open` tinyint(2) DEFAULT '1',
        `icon` char(50) DEFAULT '' COMMENT '菜单图标',
        `create_time` int(11) DEFAULT '0' COMMENT '添加时间',
        `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
        `param` varchar(50) NOT NULL DEFAULT '' COMMENT '参数',
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_auth_rule
-- ----------------------------
BEGIN;
INSERT INTO `uk_auth_rule` VALUES (1, 0, 'index', '后台首页', 1, 1, '', 0, 1, 'icon-home', 1612069842, 1613466659, '');
INSERT INTO `uk_auth_rule` VALUES (2, 0, 'system', '系统管理', 1, 1, '', 1, 1, 'icon-cog', 1612069842, 1612056628, '');
INSERT INTO `uk_auth_rule` VALUES (3, 0, 'ask', '问答管理', 1, 1, '', 50, 1, 'fas fa-book', 1612005363, 1612056664, '');
INSERT INTO `uk_auth_rule` VALUES (4, 0, 'extend', '拓展功能', 1, 1, '', 50, 1, 'icon-sound-mix', 1612005409, 1612005409, '');
INSERT INTO `uk_auth_rule` VALUES (5, 0, 'member', '会员模块', 1, 1, '', 50, 1, 'fas fa-user-friends', 1612005442, 1612056684, '');
INSERT INTO `uk_auth_rule` VALUES (6, 0, 'application', '应用管理', 1, 1, '', 50, 1, 'fas fa-wrench', 1612253092, 1612253092, '');
INSERT INTO `uk_auth_rule` VALUES (7, 2, 'admin/system.Config/index', '配置管理', 1, 1, '', 1, 1, 'icon-cog', 1612056113, 1613736579, '');
INSERT INTO `uk_auth_rule` VALUES (8, 2, 'admin/system.Config/config', '系统配置', 1, 1, '', 0, 1, 'fa fa-edit', 1612063862, 1613466835, '');
INSERT INTO `uk_auth_rule` VALUES (9, 7, 'admin/system.Config/add', '操作-添加', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (10, 7, 'admin/system.Config/edit', '操作-编辑', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (11, 7, 'admin/system.Config/state', '操作-状态', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (12, 7, 'admin/system.Config/delete', '操作-删除', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (13, 7, 'admin/system.Config/export', '操作-导出', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (14, 7, 'admin/system.Config/sort', '操作-排序', 1, 0, '', 1, 1, 'fa fa-plus', 1612069842, 1612069842, '');
INSERT INTO `uk_auth_rule` VALUES (15, 1, 'admin/Index/clear', '清除缓存', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 1612056380, '');
INSERT INTO `uk_auth_rule` VALUES (29, 2, 'admin/system.AuthRule/index', '菜单管理', 1, 1, '', 3, 1, 'fas fa-cogs', 0, 1612056380, '');
INSERT INTO `uk_auth_rule` VALUES (30, 29, 'admin/system.AuthRule/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (31, 29, 'admin/system.AuthRule/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (32, 29, 'admin/system.AuthRule/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (33, 29, 'admin/system.AuthRule/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (34, 29, 'admin/system.AuthRule/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (35, 29, 'system.AuthRule/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (36, 5, 'member/Users/index', '用户管理', 1, 1, '', 0, 1, 'icon-users', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (37, 36, 'member/Users/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (38, 36, 'member/Users/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (39, 36, 'member/Users/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (40, 36, 'member/Users/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (41, 36, 'member/Users/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (42, 36, 'member/Users/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (43, 5, 'admin/system.AuthGroup/index', '系统组', 1, 1, '', 2, 1, 'icon-vpn_key', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (44, 43, 'admin/system.AuthGroup/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (45, 43, 'admin/system.AuthGroup/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (46, 43, 'admin/system.AuthGroup/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (47, 43, 'admin/system.AuthGroup/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (48, 43, 'admin/system.AuthGroup/permission', '用户权限', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (49, 43, 'admin/system.AuthGroup/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (50, 4, 'admin/module.Database/database', '数据库备份', 1, 1, '', 31, 1, 'fa fa-server', 1580881507, 1580881507, '');
INSERT INTO `uk_auth_rule` VALUES (51, 50, 'admin/module.Database/backup', '操作-备份', 1, 0, '', 1, 1, '', 1580881536, 1580881536, '');
INSERT INTO `uk_auth_rule` VALUES (52, 50, 'admin/module.Database/repair', '操作-修复', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (53, 50, 'admin/module.Database/optimize', '操作-优化', 1, 0, '', 3, 1, '', 1580881596, 1580881596, '');
INSERT INTO `uk_auth_rule` VALUES (54, 4, 'admin/module.Database/restore', '数据库还原', 1, 1, '', 32, 1, 'fa fa-recycle', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (55, 54, 'admin/module.Database/import', '操作-还原', 1, 0, '', 1, 1, '', 1580881791, 1580881791, '');
INSERT INTO `uk_auth_rule` VALUES (56, 54, 'admin/module.Database/downFile', '操作-下载', 1, 0, '', 2, 1, '', 1580881823, 1580881823, '');
INSERT INTO `uk_auth_rule` VALUES (57, 54, 'admin/module.Database/delete', '操作-删除', 1, 0, '', 3, 1, '', 1580881861, 1580881861, '');
INSERT INTO `uk_auth_rule` VALUES (58, 3, 'ask/Question/index', '问题管理', 1, 1, '', 2, 1, 'icon-help-with-circle', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (59, 22, 'admin/system.Field/changeType', '字段类型', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (60, 58, 'ask/Question/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (61, 58, 'ask/Question/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (62, 58, 'ask/Question/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (63, 58, 'ask/Question/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (64, 58, 'ask/Question/seo', '操作-seo设置', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (65, 3, 'ask/Article/index', '文章管理', 1, 1, '', 2, 1, 'icon-assignment', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (66, 65, 'ask/Article/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (67, 65, 'ask/Article/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (68, 65, 'ask/Article/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (69, 65, 'ask/Article/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (70, 65, 'ask/Article/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (71, 65, 'ask/Article/seo', '操作-SEO设置', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (72, 6, 'admin/plugin.Plugins/index', '插件管理', 1, 1, '', 0, 1, 'fas fa-cloud-upload-alt', 1612253228, 1612253228, '');
INSERT INTO `uk_auth_rule` VALUES (73, 72, 'admin/plugin.Plugins/config', '插件配置', 1, 0, '', 50, 1, '', 1612253323, 1612253386, '');
INSERT INTO `uk_auth_rule` VALUES (74, 72, 'admin/plugin.Plugins/install', '安装插件', 1, 0, '', 50, 1, '', 1612253346, 1612253346, '');
INSERT INTO `uk_auth_rule` VALUES (75, 72, 'admin/plugin.Plugins/uninstall', '卸载插件', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (76, 72, 'admin/plugin.Plugins/design', '设计插件', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (77, 72, 'admin/plugin.Plugins/state', '操作-状态', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (78, 72, 'admin/plugin.Plugins/import', '操作-导入', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (79, 72, 'admin/plugin.Plugins/delete', '操作-删除', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (80, 72, 'admin/plugin.Plugins/upgrade', '操作-升级', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (81, 3, 'ask/Approval/index', '审核管理', 1, 1, '', 2, 1, 'icon-error', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (82, 81, 'ask/Approval/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (83, 81, 'ask/Approval/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (84, 81, 'ask/Approval/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (85, 81, 'ask/Approval/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (86, 81, 'ask/Approval/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (87, 81, 'ask/Approval/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (88, 5, 'member/UsersScoreGroup/index', '积分组', 1, 1, '', 1, 1, 'fas fa-users', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (89, 88, 'member/UsersScoreGroup/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (90, 88, 'member/UsersScoreGroup/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (91, 88, 'member/UsersScoreGroup/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (92, 88, 'member/UsersScoreGroup/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (93, 88, 'member/UsersScoreGroup/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (94, 88, 'member/UsersScoreGroup/permission', '用户权限', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (95, 5, 'member/Permission/index', '权限管理', 1, 1, '', 2, 1, 'icon-user-check', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (96, 95, 'member/Permission/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (97, 95, 'member/Permission/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (98, 95, 'member/Permission/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (99, 95, 'member/Permission/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (100, 95, 'member/Permission/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (101, 95, 'member/Permission/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (102, 229, 'admin/module.Theme/index', '模板管理', 1, 0, '', 0, 1, 'fas fa-cloud-upload-alt', 1612253228, 1612253228, 'type=ask');
INSERT INTO `uk_auth_rule` VALUES (103, 102, 'admin/module.Theme/config', '模板配置', 1, 0, '', 50, 1, '', 1612253323, 1612253386, '');
INSERT INTO `uk_auth_rule` VALUES (104, 72, 'admin/module.Theme/install', '安装模板', 1, 0, '', 50, 1, '', 1612253346, 1612253346, '');
INSERT INTO `uk_auth_rule` VALUES (105, 72, 'admin/module.Theme/uninstall', '卸载模板', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (106, 72, 'admin/module.Theme/state', '操作-状态', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule` VALUES (107, 2, 'admin/system.Nav/index', '导航管理', 1, 1, '', 2, 1, 'fas fa-users', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (108, 107, 'admin/system.Nav/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (109, 107, 'admin/system.Nav/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (110, 107, 'admin/system.Nav/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (111, 107, 'admin/system.Nav/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (112, 107, 'admin/system.Nav/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (113, 107, 'admin/system.Nav/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (114, 4, 'admin/module.Links/index', '友情链接', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (115, 114, 'admin/module.Links/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (116, 114, 'admin/module.Links/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (117, 114, 'admin/module.Links/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (118, 114, 'admin/module.Links/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (119, 114, 'admin/module.Links/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (120, 114, 'admin/module.Links/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (121, 5, 'member/Score/index', '积分规则', 1, 1, '', 61, 1, 'icon-database', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (122, 121, 'member/Score/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (123, 121, 'member/Score/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (124, 121, 'member/Score/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (125, 121, 'member/Score/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (126, 121, 'member/Score/deleteLog', '操作-删除记录', 1, 0, '', 8, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (127, 121, 'member/Score/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (128, 121, 'member/Score/detail', '记录详情', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (129, 5, 'member/Score/log', '积分记录', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (130, 2, 'admin/system.Action/index', '日志规则', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (131, 130, 'admin/system.Action/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (132, 130, 'admin/system.Action/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (133, 130, 'admin/system.Action/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (134, 130, 'admin/system.Action/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (135, 130, 'admin/system.Action/deleteLog', '操作-删除记录', 1, 0, '', 8, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (136, 130, 'admin/system.Action/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (137, 130, 'admin/system.Action/detail', '记录详情', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (138, 2, 'admin/system.Action/log', '行为记录', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (139, 3, 'ask/Category/index', '分类管理', 1, 1, '', 2, 1, 'icon-folder1', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (140, 139, 'ask/Category/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (141, 139, 'ask/Category/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (142, 139, 'ask/Category/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (143, 139, 'ask/Category/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (144, 139, 'ask/Category/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (145, 139, 'ask/Category/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (146, 3, 'ask/Topic/index', '话题管理', 1, 1, '', 2, 1, 'icon-hash', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (147, 146, 'ask/Topic/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (148, 146, 'ask/Topic/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (149, 146, 'ask/Topic/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (150, 146, 'ask/Topic/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (151, 146, 'ask/Topic/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (152, 146, 'ask/Topic/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (153, 3, 'ask/Column/index', '专栏管理', 1, 1, '', 2, 1, 'icon-layers2', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (154, 146, 'ask/Column/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (155, 146, 'ask/Column/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (156, 146, 'ask/Column/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (157, 146, 'ask/Column/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (158, 146, 'ask/Column/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (159, 146, 'ask/Column/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (160, 1, 'admin/Index/icons', '选择图标', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (162, 3, 'ask/Page/index', '页面管理', 1, 1, '', 2, 1, 'icon-file-text', 0, 1613736618, '');
INSERT INTO `uk_auth_rule` VALUES (163, 162, 'ask/Page/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (164, 162, 'ask/Page/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (165, 162, 'ask/Page/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (166, 162, 'ask/Page/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (167, 5, 'member/Verify/index', '认证管理', 1, 1, '', 9, 1, 'icon-verified_user', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (168, 5, 'member/Verify/field', '认证字段', 1, 1, '', 10, 1, 'icon-check-square', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (169, 167, 'member/Verify/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (170, 167, 'member/Verify/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (171, 167, 'member/Verify/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (172, 167, 'member/Verify/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (173, 167, 'member/Verify/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (174, 167, 'member/Verify/preview', '操作-预览', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (175, 167, 'member/Verify/manager', '操作-管理', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (176, 5, 'member/UsersPowerGroup/index', '声望组', 1, 1, '', 1, 1, 'icon-command', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (177, 176, 'member/UsersPowerGroup/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (178, 176, 'member/UsersPowerGroup/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (179, 176, 'member/UsersPowerGroup/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (180, 176, 'member/UsersPowerGroup/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (181, 176, 'member/UsersPowerGroup/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (182, 176, 'member/UsersPowerGroup/permission', '用户权限', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (183, 4, 'admin/module.Queue/index', '任务管理', 1, 1, '', 31, 1, 'fa fa-server', 1580881507, 1580881507, '');
INSERT INTO `uk_auth_rule` VALUES (184, 183, 'admin/module.Queue/add', '操作-添加', 1, 0, '', 1, 1, '', 1580881536, 1580881536, '');
INSERT INTO `uk_auth_rule` VALUES (185, 183, 'admin/module.Queue/start', '操作-开始', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (186, 183, 'admin/module.Queue/stop', '操作-停止', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (187, 183, 'admin/module.Queue/delete', '操作-删除', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (188, 183, 'admin/module.Queue/test', '操作-测试', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (189, 1, 'admin/Index/index', '系统面板', 1, 1, '', 0, 1, 'icon-home', 1612069842, 1613466659, '');
INSERT INTO `uk_auth_rule` VALUES (190, 1, 'admin/Tools/index', '系统工具', 1, 1, '', 0, 1, 'fa fa-cogs', 1612069842, 1613466659, '');
INSERT INTO `uk_auth_rule` VALUES (191, 2, 'admin/system.Email/index', '邮件记录', 1, 1, '', 1, 1, 'icon-email', 1612056113, 1613736579, '');
INSERT INTO `uk_auth_rule` VALUES (192, 191, 'admin/system.Email/preview', '浏览记录', 1, 1, '', 0, 0, 'icon-email', 1612056113, 1613736579, '');
INSERT INTO `uk_auth_rule` VALUES (193, 2, 'admin/system.Route/index', '路由管理', 1, 1, '', 50, 1, 'icon-flag1', 1621859497, 1621859497, '');
INSERT INTO `uk_auth_rule` VALUES (194, 193, 'admin/system.Route/add', '添加', 1, 0, '', 50, 1, '', 1621860683, 1621860683, '');
INSERT INTO `uk_auth_rule` VALUES (195, 193, 'admin/system.Route/edit', '编辑', 1, 0, '', 0, 1, '', 1621860683, 1621860683, '');
INSERT INTO `uk_auth_rule` VALUES (196, 4, 'admin/module.Order/index', '交易流水', 1, 1, '', 31, 1, 'icon-shopping-cart', 1580881507, 1580881507, '');
INSERT INTO `uk_auth_rule` VALUES (197, 196, 'admin/module.Order/detail', '操作-详情', 1, 0, '', 1, 1, '', 1580881536, 1580881536, '');
INSERT INTO `uk_auth_rule` VALUES (198, 196, 'admin/module.Order/delete', '操作-删除', 1, 0, '', 2, 1, '', 1580881567, 1580881567, '');
INSERT INTO `uk_auth_rule` VALUES (199, 4, 'admin/module.Log/index', '资金记录', 1, 1, '', 3, 1, 'icon-slack', 1580881596, 1580881596, '');
INSERT INTO `uk_auth_rule` VALUES (200, 199, 'admin/module.Log/detail', '操作-详情', 1, 0, '', 32, 1, '', 1580881718, 1580881729, '');
INSERT INTO `uk_auth_rule` VALUES (224, 36, 'member/Users/forbidden', '操作-封禁', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (225, 36, 'member/Users/un_forbidden', '操作-解除封禁', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (226, 36, 'member/Users/approval', '操作-通过审核', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (227, 36, 'member/Users/decline', '操作-拒绝审核', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule` VALUES (228, 36, 'member/Users/manager', '操作-通用管理', 1, 0, '', 0, 1, '', 0, 0, '');
INSERT INTO `uk_auth_rule`  VALUES (229, 6, 'admin/module.Module/index', '模块管理', 1, 1, '', 0, 1, 'fas fa-cloud-upload-alt', 1612253228, 1612253228, '');
INSERT INTO `uk_auth_rule`  VALUES (230, 229, 'admin/module.Module/config', '模块配置', 1, 0, '', 50, 1, '', 1612253323, 1612253386, '');
INSERT INTO `uk_auth_rule`  VALUES (231, 229, 'admin/module.Module/install', '安装模块', 1, 0, '', 50, 1, '', 1612253346, 1612253346, '');
INSERT INTO `uk_auth_rule`  VALUES (232, 229, 'admin/module.Module/uninstall', '卸载模块', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (233, 229, 'admin/module.Module/design', '设计模块', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (234, 229, 'admin/module.Module/status', '操作-状态', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (235, 229, 'admin/module.Module/import', '操作-导入', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (236, 229, 'admin/module.Module/delete', '操作-删除', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (237, 229, 'admin/module.Module/upgrade', '操作-升级', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');
INSERT INTO `uk_auth_rule`  VALUES (238, 229, 'admin/module.Module/state', '操作-设置默认', 1, 0, '', 50, 1, '', 1612253361, 1612253361, '');

COMMIT;
-- ----------------------------
-- Table structure for uk_category
-- ----------------------------
DROP TABLE IF EXISTS `uk_category`;
CREATE TABLE `uk_category` (
       `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
       `title` varchar(128) DEFAULT NULL COMMENT '分类名称',
       `type` varchar(16) DEFAULT NULL COMMENT '分类类型',
       `icon` varchar(255) DEFAULT NULL COMMENT '分类图标',
       `pid` int(11) DEFAULT '0' COMMENT '分类父级',
       `sort` smallint(6) DEFAULT '0' COMMENT '分类排序',
       `url_token` varchar(32) DEFAULT NULL COMMENT '分类别名',
       `status` tinyint(1) unsigned DEFAULT '0' COMMENT '分类状态',
       `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
       `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `pid` (`pid`) USING BTREE,
       KEY `url_token` (`url_token`) USING BTREE,
       KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='分类表';

-- ----------------------------
-- Records of uk_category
-- ----------------------------
BEGIN;
INSERT INTO `uk_category` VALUES (1, '默认分类', 'all', NULL, 0, 0, NULL, 1, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for uk_column
-- ----------------------------
DROP TABLE IF EXISTS `uk_column`;
CREATE TABLE `uk_column` (
     `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '专栏id',
     `name` varchar(64) DEFAULT NULL COMMENT '专栏标题',
     `verify` tinyint(1) DEFAULT '0' COMMENT '是否审核通过 （1通过0审核中-1通过）',
     `focus_count` int(11) DEFAULT '0' COMMENT '关注计数',
     `description` text COMMENT '专栏描述',
     `cover` varchar(255) DEFAULT NULL COMMENT '专栏图片',
     `uid` int(11) DEFAULT NULL COMMENT '用户UID',
     `sort` int(10) DEFAULT '0' COMMENT '排序',
     `reason` varchar(100) DEFAULT NULL COMMENT '拒绝原因',
     `recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '是否推荐',
     `auth` text COMMENT '专栏权限管理',
     `view_count` int(10) unsigned DEFAULT '0' COMMENT '专栏浏览数',
     `post_count` int(10) unsigned DEFAULT '0' COMMENT '专栏文章数',
     `join_count` int(10) unsigned DEFAULT '0' COMMENT '专栏签约用户数',
     `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
     `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
     `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
     `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='专栏表';

-- ----------------------------
-- Records of uk_column
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_column_focus
-- ----------------------------
DROP TABLE IF EXISTS `uk_column_focus`;
CREATE TABLE `uk_column_focus` (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
   `column_id` int(11) DEFAULT NULL COMMENT '问题ID',
   `uid` int(11) DEFAULT NULL COMMENT '用户UID',
   `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
   PRIMARY KEY (`id`) USING BTREE,
   KEY `uid` (`uid`) USING BTREE,
   KEY `column_id` (`column_id`) USING BTREE,
   KEY `column_uid` (`column_id`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专栏关注表';

-- ----------------------------
-- Records of uk_column_focus
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_config
-- ----------------------------
DROP TABLE IF EXISTS `uk_config`;
CREATE TABLE `uk_config` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量名',
     `group` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分组',
     `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量标题',
     `tips` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量描述',
     `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
     `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
     `option` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量字典数据',
     `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值,数字越小越靠前',
     `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
     `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
     PRIMARY KEY (`id`) USING BTREE,
     UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='系统配置';

-- ----------------------------
-- Records of uk_config
-- ----------------------------
BEGIN;
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (1, 'site_name', 'base', '站点名称', '请填写站点名称', 'text', 'UKnowing', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (2, 'site_logo', 'base', '网站logo', '请上传网站LOGO', 'image', '/static/common/image/logo.png', '\"\"', 2, 1621578838, 1621578955);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (3, 'site_close', 'base', '关闭站点', '开启或关闭站点', 'radio', '1', '[\"关闭\",\"开启\"]', 2, 1621578838, 1621578962);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (4, 'icp', 'base', '备案号', '输入网站备案号', 'text', '', 'null', 3, 1621578838, 1621578969);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (5, 'show_name', 'member', '显示用户字段', '显示用户字段', 'radio', 'user_name', '{\"user_name\":\"用户名\",\"nick_name\":\"昵称\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (6, 'show_url', 'member', '用户链接', '用户链接', 'radio', 'uid', '{\"uid\":\"用户ID\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (7, 'site_description', 'base', '站点描述', '站点描述', 'textarea', '', '\"\"', 1, 1621578838, 1621578977);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (8, 'mobile_enable', 'base', '开启手机端', '开启手机端', 'radio', '0', '[\"关闭\",\"开启\"]', 5, 1621578838, 1621578985);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (9, 'online_check', 'member', '在线检测', '在线检测用户在线状态', 'radio', '0', '[\"关闭\",\"开启\"]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (10, 'online_check_time', 'member', '检测间隔', '在线检测用户在线状态时间间隔，单位（分钟）', 'text', '5', 'null', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (11, 'report_category', 'config', '举报分类', '举报类型配置', 'array', '0', '[\"语言辱骂\",\"广告推广\"]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (12, 'config_group', 'config', '配置分组', '配置分组', 'array', '0', '{\"base\":\"基础配置\",\"system\":\"系统配置\",\"upload\":\"上传设置\",\"member\":\"用户配置\",\"config\":\"配置字典\",\"power\":\"声望配置\",\"email\":\"邮箱配置\",\"site\":\"功能配置\",\"open\":\"开放平台\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (13, 'yes_no', 'config', '是否配置', '是否配置', 'array', '0', '[\"否\",\"是\"]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (18, 'upload_file_size', 'upload', '文件限制', '单位：KB，0表示不限制上传大小 ', 'text', '0', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (19, 'upload_file_ext', 'upload', '文件格式', '多个格式请用英文逗号（,）隔开 ', 'text', 'rar,zip,avi,rmvb,3gp,flv,mp3,mp4,txt,doc,xls,ppt,pdf,xls,docx,xlsx,doc', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (20, 'upload_image_size', 'upload', '图片限制', '单位：KB，0表示不限制上传大小', 'text', '0', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (21, 'upload_image_ext', 'upload', '图片格式', '多个格式请用英文逗号（,）隔开 ', 'text', 'jpg,png,gif,jpeg,ico', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (23, 'module_field_type', 'config', '字段类型', '字段类型', 'array', '0', '{\"text\":\"单行文本\",\"icon\":\"字体图标\",\"textarea\":\"多行文本\",\"radio\":\"单选按钮\",\"checkbox\":\"多选按钮\",\"checkbox2\":\"树形多选\",\"select\":\"普通下拉菜单\",\"select2\":\"高级下拉菜单\",\"date\":\"日期\",\"time\":\"时间\",\"datetime\":\"日期时间\",\"daterange\":\"日期范围\",\"number\":\"数字\",\"image\":\"单张图片\",\"images\":\"多张图片\",\"file\":\"单文件上传\",\"files\":\"多文件上传\",\"hidden\":\"隐藏域\",\"password\":\"密码\",\"color\":\"取色器\",\"tag\":\"标签\",\"editor\":\"编辑器\",\"bool\":\"布尔型\",\"array\":\"配置字典\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (24, 'register_valid_type', 'member', '验证类型', '新用户注册验证类型', 'checkbox', 'mobile', '{\"email\":\"邮箱验证\",\"mobile\":\"手机验证\",\"admin\":\"后台审核\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (25, 'register_type', 'member', '注册类型', '注册类型', 'radio', 'open', '{\"open\":\"开放注册\",\"invite\":\"邀请注册\",\"close\":\"关闭注册\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (26, 'password_min_length', 'member', '密码最小长度', '密码最小长度', 'number', '6', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (27, 'password_max_length', 'member', '密码最大长度', '密码最大长度', 'number', '18', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (28, 'password_type', 'member', '密码类型', '密码类型，密码必须是选择的类型才可使用，不选则不限制', 'checkbox', 'number,special,letter', '{\"number\":\"数字\",\"special\":\"特殊字符\",\"letter\":\"字母\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (29, 'username_min_length', 'member', '用户名最小长度', '用户名最小长度', 'number', '4', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (30, 'username_max_length', 'member', '用户名最大长度', '用户名最大长度', 'number', '10', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (31, 'user_verify_type', 'config', '用户认证类型', '', 'array', '0', '{\"people\":\"个人认证\",\"company\":\"公司认证\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (32, 'user_group_factor', 'member', '用户组类型', '', 'radio', 'score', '{\"score\":\"积分值\",\"power\":\"声望值\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (33, 'score_unit', 'member', '积分单位', '前台积分单位', 'text', '积分', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (34, 'power_unit', 'member', '声望单位', '前台声望单位', 'text', '声望', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (35, 'power_agree_factor', 'power', '赞同系数', '', 'number', '3', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (36, 'power_against_factor', 'power', '反对系数', '', 'number', '2', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (37, 'power_best_answer_factor', 'power', '最佳回复系数', '', 'number', '5', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (38, 'user_register_welcome', 'member', '新用户注册欢迎内容', '新用户注册欢迎内容, 以下变量可作为内容替换:\r\n{username}: 用户名\r\n{time}: 发送时间\r\n{sitename}: 网站名称', 'textarea', '尊敬的{username}，您已经注册成为{sitename}的会员，请您在发表言论时，遵守当地法律法规。如果您有什么疑问可以联系管理员。', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (39, 'popular_gravity', 'system', '热门-刷新速度', '内容变得不再热门的速度，热门刷新速度越大，一个内容刷新的就越快', 'number', '1.5', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (40, 'popular_agree_ratio', 'system', '热门-赞同比例', '计算热门时点赞数所占比例', 'number', '2', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (41, 'popular_against_ratio', 'system', '热门-反对比例', '计算热门时反对数所占比例', 'number', '1', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (42, 'popular_view_ratio', 'system', '热门-浏览比例', '计算热门时浏览数所占比例', 'number', '2', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (43, 'popular_comment_ratio', 'system', '热门-评论比例', '计算热门时评论/回复数所占比例', 'number', '2', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (44, 'popular_quality_init_value', 'system', '热门-初始质量', '内容初始质量', 'number', '1', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (45, 'errors_exceeds_limit_password', 'member', '密码最大重试次数', '密码最大重试次数', 'number', '3', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (46, 'password_error_limit_time', 'member', '密码错误限制时长', '密码错误限制时长', 'number', '10', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (47, 'email_enable', 'email', '邮箱功能', '', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (48, 'email_host', 'email', 'SMTP地址', '设置SMTP服务地址', 'text', 'smtp.example.co', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (49, 'email_username', 'email', '邮箱用户名', '邮箱用户名', 'text', 'user@example.com', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (50, 'email_password', 'email', '邮箱密码', '邮箱密码', 'password', '', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (51, 'email_secure', 'email', '安全链接(SSL)', '验证模式', 'radio', 'tls', '{\"tls\":\"否\",\"ssl\":\"是\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (52, 'email_port', 'email', '邮箱端口', '留空时默认服务器端口为 25，使用 SSL 协议默认端口为 465，详细参数请询问邮箱服务商', 'text', '25', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (53, 'email_from', 'email', '显示来源', '请保持和邮箱用户名同一主域下', 'text', 'user@example.com', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (54, 'email_show_name', 'email', '来源名称', '显示来源名称', 'text', 'UKnowing官方团队', '[]', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (55, 'enable_category', 'site', '启用分类', '', 'radio', '1', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (56, 'site_brand', 'base', '副标题', '请填写站点副标题', 'text', '一款基于TP6开发的社交化知识付费问答系统，打造私有社交', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (57, 'site_keywords', 'base', '关键词', '请填写关键词', 'text', 'UKnowing,知识问答,私有问答,社交问答,知识库', '\"\"', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (58, 'logo_type', 'base', 'logo模式', '选择前台logo显示方式', 'radio', 0, '[\"logo\",\"网站名称文字\"]', 1, 1621578838, 1621578962);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (59, 'uninterested_power_factor', 'power', '不感兴趣声望系数', '', 'number', '3', '[]', 0, 1622120863, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (60, 'thanks_power_factor', 'power', '感谢声望系数', '', 'number', '5', '[]', 0, 1622120962, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (61, 'verify_user_power_factor', 'power', '认证会员赞踩系数', '', 'number', '3', '[]', 0, 1622120863, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (62, 'publish_user_power_factor', 'power', '提问者赞踩系数', '', 'number', '2', '[]', 0, 1622120962, 0);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (63, 'power_log_factor', 'power', '声望对底系数', '', 'number', '2', '[]', 0, 1622120962, 0);
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
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (78, 'download_image_to_local', 'site', '图片本地化', '将复制的内容图片远程下载到本地', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (79, 'water_author_text_enable', 'site', '作者水印', '发表内容添加作者水印,禁用则显示上传设置中配置的水印文字', 'radio', '0', '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1621578838, 1621578838);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (80, 'cut_image_prop', 'upload', '图片压缩比例', '', 'text', '0.5', '[]', 0, 1624452381, 1624452381);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (81, 'water_text', 'upload', '水印文字', '', 'text', '', '[]', 0, 1624452381, 1624452381);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (82, 'water_enable', 'site', '水印开关', '是否开启水印功能', 'radio', 0, '{\"1\":\"启用\",\"0\":\"禁用\"}', 0, 1624452381, 1624452381);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (83, 'cdn_url', 'upload', '资源URL', '默认为空，代表当前服务器，其他服务器填写域名或IP,不带/', 'text', '', '', 0, 1624452381, 1624452381);
INSERT INTO `uk_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (84, 'upload_dir', 'upload', '上传路径', '默认为/uploads,代表当前服务器public目录下uploads文件夹，不带/', 'text', '/uploads', '', 0, 1624452381, 1624452381);
COMMIT;

-- ----------------------------
-- Table structure for uk_draft
-- ----------------------------
DROP TABLE IF EXISTS `uk_draft`;
CREATE TABLE `uk_draft` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) DEFAULT '0',
    `item_type` varchar(16) DEFAULT NULL,
    `item_id` char(16) NOT NULL,
    `data` longtext,
    `create_time` int(10) DEFAULT '0',
    `update_time` int(10) unsigned DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_draft
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_email_log
-- ----------------------------
DROP TABLE IF EXISTS `uk_email_log`;
CREATE TABLE `uk_email_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `send_to` varchar(255) NOT NULL COMMENT '发送给',
    `subject` varchar(255) NOT NULL COMMENT '邮件主题',
    `message` text NOT NULL COMMENT '邮件内容',
    `error_message` varchar(255) DEFAULT NULL COMMENT '错误信息',
    `create_time` int(10) DEFAULT '0',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0发送失败，1发送成功',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `send_to` (`send_to`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_email_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_favorite
-- ----------------------------
DROP TABLE IF EXISTS `uk_favorite`;
CREATE TABLE `uk_favorite` (
       `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
       `uid` int(11) DEFAULT '0',
       `item_id` int(11) unsigned NOT NULL DEFAULT '0',
       `item_type` varchar(16) NOT NULL DEFAULT '',
       `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
       `create_time` int(10) unsigned DEFAULT '0',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `uid` (`uid`) USING BTREE,
       KEY `create_time` (`create_time`) USING BTREE,
       KEY `item_id` (`item_id`) USING BTREE,
       KEY `item_type` (`item_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_favorite
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_favorite_tag
-- ----------------------------
DROP TABLE IF EXISTS `uk_favorite_tag`;
CREATE TABLE `uk_favorite_tag` (
       `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
       `uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户',
       `title` varchar(128) DEFAULT NULL COMMENT '收藏标签',
       `post_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
       `focus_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
       `comment_count` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
       `is_public` tinyint(1) unsigned DEFAULT '0' COMMENT '1公开，0私密',
       `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
       `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `uid` (`uid`) USING BTREE,
       KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_favorite_tag
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_inbox
-- ----------------------------
DROP TABLE IF EXISTS `uk_inbox`;
CREATE TABLE `uk_inbox` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) DEFAULT NULL COMMENT '发送者 ID',
        `dialog_id` int(11) DEFAULT NULL COMMENT '对话id',
        `message` text COMMENT '私信内容',
        `sender_remove` tinyint(1) DEFAULT '0' COMMENT '发送者删除消息',
        `recipient_remove` tinyint(1) DEFAULT '0' COMMENT '接受者删除消息',
        `send_time` int(10) DEFAULT '0' COMMENT '发送时间',
        `read_time` int(10) DEFAULT NULL COMMENT '读取时间',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `dialog_id` (`dialog_id`) USING BTREE,
        KEY `uid` (`uid`) USING BTREE,
        KEY `sender_remove` (`sender_remove`) USING BTREE,
        KEY `recipient_remove` (`recipient_remove`) USING BTREE,
        KEY `send_time` (`send_time`) USING BTREE,
        KEY `read_time` (`read_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_inbox
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_inbox_dialog
-- ----------------------------
DROP TABLE IF EXISTS `uk_inbox_dialog`;
CREATE TABLE `uk_inbox_dialog` (
       `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '对话ID',
       `sender_uid` int(11) DEFAULT NULL COMMENT '发送者UID',
       `sender_unread` int(11) DEFAULT NULL COMMENT '发送者未读',
       `recipient_uid` int(11) DEFAULT NULL COMMENT '接收者UID',
       `recipient_unread` int(11) DEFAULT NULL COMMENT '接收者未读',
       `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
       `update_time` int(11) DEFAULT NULL COMMENT '最后更新时间',
       `sender_count` int(11) DEFAULT NULL COMMENT '发送者显示对话条数',
       `recipient_count` int(11) DEFAULT NULL COMMENT '接收者显示对话条数',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `recipient_uid` (`recipient_uid`) USING BTREE,
       KEY `sender_uid` (`sender_uid`) USING BTREE,
       KEY `update_time` (`update_time`) USING BTREE,
       KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_inbox_dialog
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_nav
-- ----------------------------
DROP TABLE IF EXISTS `uk_nav`;
CREATE TABLE `uk_nav` (
      `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
      `pid` int unsigned NOT NULL DEFAULT '0' COMMENT '上级导航ID',
      `title` char(30) NOT NULL COMMENT '导航标题',
      `module` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'ask' COMMENT '模块名',
      `controller` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '控制器名',
      `action` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '方法名',
      `url` char(100) NOT NULL COMMENT '外部导航连接',
      `type` int NOT NULL DEFAULT '1' COMMENT '导航类型：1顶部导航,2底部导航',
      `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
      `icon` varchar(20) DEFAULT NULL COMMENT '图标',
      `create_time` int unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
      `update_time` int unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
      `status` tinyint NOT NULL DEFAULT '0' COMMENT '状态',
      `target` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
      `need_login` tinyint unsigned DEFAULT '0' COMMENT '是否需要登陆可见',
      `is_home` tinyint unsigned DEFAULT '0' COMMENT '是否是默认首页',
      `seo_title` varchar(100) DEFAULT NULL COMMENT 'SEO标题',
      `unique_name` varchar(255) DEFAULT NULL COMMENT '唯一标识',
      `seo_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键词',
      `seo_description` varchar(255) DEFAULT NULL COMMENT 'SEO描述',
      UNIQUE KEY `module_url` (`module`,`controller`,`action`) USING BTREE,
      UNIQUE KEY `unique_name` (`unique_name`) USING BTREE,
      PRIMARY KEY (`id`) USING BTREE,
      KEY `pid` (`pid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='导航菜单表';
-- ----------------------------
-- Records of uk_nav
-- ----------------------------
BEGIN;
INSERT INTO `uk_nav` (`id`, `pid`, `title`, `module`, `controller`, `action`, `url`, `type`, `sort`, `icon`, `create_time`, `update_time`, `status`, `target`, `need_login`, `is_home`, `seo_title`, `seo_keywords`, `seo_description`, `unique_name`) VALUES (1, 0, '发现', 'ask', 'index', 'index', '', 1, 1, 'icon-explore', 1603690015, 1621585480, 1, 0, 0, 0, '发现', '', '','explore');
INSERT INTO `uk_nav` (`id`, `pid`, `title`, `module`, `controller`, `action`, `url`, `type`, `sort`, `icon`, `create_time`, `update_time`, `status`, `target`, `need_login`, `is_home`, `seo_title`, `seo_keywords`, `seo_description`, `unique_name`) VALUES (2, 0, '问答', 'ask', 'question', 'index', '', 1, 1, 'icon-help_outline', 1603690015, 1621585480, 1, 0, 0, 0, '问题库', '', '','question');
INSERT INTO `uk_nav` (`id`, `pid`, `title`, `module`, `controller`, `action`, `url`, `type`, `sort`, `icon`, `create_time`, `update_time`, `status`, `target`, `need_login`, `is_home`, `seo_title`, `seo_keywords`, `seo_description`, `unique_name`) VALUES (3, 0, '文章', 'ask', 'article', 'index', '', 1, 1, 'icon-map', 1608174950, 1621585557, 1, 0, 0, 0, '文章', '', '','article');
INSERT INTO `uk_nav` (`id`, `pid`, `title`, `module`, `controller`, `action`, `url`, `type`, `sort`, `icon`, `create_time`, `update_time`, `status`, `target`, `need_login`, `is_home`, `seo_title`, `seo_keywords`, `seo_description`, `unique_name`) VALUES (4, 0, '专栏', 'ask', 'column', 'index', '', 1, 1, '', 1608174986, 1621585584, 1, 0, 0, 0, '专栏', '', '','column');
INSERT INTO `uk_nav` (`id`, `pid`, `title`, `module`, `controller`, `action`, `url`, `type`, `sort`, `icon`, `create_time`, `update_time`, `status`, `target`, `need_login`, `is_home`, `seo_title`, `seo_keywords`, `seo_description`, `unique_name`) VALUES (5, 0, '话题', 'ask', 'topic', 'index', '', 1, 1, '', 1608174986, 1621585584, 1, 0, 0, 0, '话题', '', '','topic');
COMMIT;

-- ----------------------------
-- Table structure for uk_notify
-- ----------------------------
DROP TABLE IF EXISTS `uk_notify`;
CREATE TABLE `uk_notify` (
     `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
     `sender_uid` int(11) DEFAULT NULL COMMENT '发送者ID',
     `recipient_uid` int(11) DEFAULT '0' COMMENT '接收者ID',
     `action_type` varchar(100) DEFAULT NULL COMMENT '操作类型',
     `item_id` varchar(16) NOT NULL DEFAULT '0' COMMENT '关联ID',
     `subject` varchar(75) NOT NULL DEFAULT '' COMMENT '通知标题',
     `content` text NOT NULL COMMENT '通知内容',
     `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
     `read_flag` tinyint(1) unsigned DEFAULT '0' COMMENT '阅读状态',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `recipient_read_flag` (`recipient_uid`,`read_flag`) USING BTREE,
     KEY `sender_uid` (`sender_uid`) USING BTREE,
     KEY `item_id` (`item_id`) USING BTREE,
     KEY `action_type` (`action_type`) USING BTREE,
     KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统通知';

-- ----------------------------
-- Records of uk_notify
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_post_relation
-- ----------------------------
DROP TABLE IF EXISTS `uk_post_relation`;
CREATE TABLE `uk_post_relation` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `item_id` int(10) NOT NULL COMMENT '类型ID',
        `item_type` varchar(16) NOT NULL DEFAULT '' COMMENT '内容类型',
        `category_id` int(10) DEFAULT '0' COMMENT '分类ID',
        `is_recommend` tinyint(1) DEFAULT '0' COMMENT '是否推荐',
        `view_count` int(10) DEFAULT '0' COMMENT '浏览量',
        `is_anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
        `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
        `uid` int(10) NOT NULL COMMENT '内容uid',
        `agree_count` int(10) DEFAULT '0' COMMENT '赞同数',
        `answer_count` int(10) DEFAULT '0' COMMENT '回答数/评论数',
        `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除1正常0删除',
        `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
        `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
        `create_time` int(10) NOT NULL COMMENT '创建时间',
        `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `item_id` (`item_id`) USING BTREE,
        KEY `item_type` (`item_type`) USING BTREE,
        KEY `create_time` (`create_time`) USING BTREE,
        KEY `update_time` (`update_time`) USING BTREE,
        KEY `category_id` (`category_id`) USING BTREE,
        KEY `recommend` (`is_recommend`) USING BTREE,
        KEY `anonymous` (`is_anonymous`) USING BTREE,
        KEY `popular_value` (`popular_value`) USING BTREE,
        KEY `uid` (`uid`) USING BTREE,
        KEY `agree_count` (`agree_count`) USING BTREE,
        KEY `answer_count` (`answer_count`) USING BTREE,
        KEY `view_count` (`view_count`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='内容聚合表';

-- ----------------------------
-- Table structure for uk_question
-- ----------------------------
DROP TABLE IF EXISTS `uk_question`;
CREATE TABLE `uk_question` (
       `id` int(11) NOT NULL AUTO_INCREMENT,
       `title` varchar(255) NOT NULL DEFAULT '' COMMENT '问题内容',
       `detail` text COMMENT '问题说明',
       `uid` int(11) DEFAULT NULL COMMENT '发布用户UID',
       `answer_count` int(11) NOT NULL DEFAULT '0' COMMENT '回答计数',
       `answer_users` int(11) NOT NULL DEFAULT '0' COMMENT '回答人数',
       `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
       `focus_count` int(11) NOT NULL DEFAULT '0' COMMENT '关注数',
       `comment_count` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
       `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类 ID',
       `user_ip` varchar(20) DEFAULT NULL COMMENT '用户的来源IP',
       `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复赞同数总和',
       `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复反对数总和',
       `best_answer` int(11) NOT NULL DEFAULT '0' COMMENT '最佳回复ID',
       `modify_count` int(10) NOT NULL DEFAULT '0' COMMENT '修改次数',
       `last_answer` int(11) NOT NULL DEFAULT '0' COMMENT '最后回答 ID',
       `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
       `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
       `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
       `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名提问',
       `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐问题',
       `sort` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
       `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '问题状态1正常0删除2待审核',
       `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
       `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
       `question_type` varchar(50) NOT NULL DEFAULT 'normal' COMMENT 'normal普通问题',
       `best_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定人id',
       `best_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定时间',
       `reward_money` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '赏金',
       `reward_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '悬赏截止总时间戳',
       `look_enable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启围观0开放回答,1付费围观',
       `seo_title` varchar(100) DEFAULT NULL COMMENT 'SEO标题',
       `seo_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键词',
       `seo_description` varchar(255) DEFAULT NULL COMMENT 'SEO描述',
       `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
       `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `category_id` (`category_id`) USING BTREE,
       KEY `update_time` (`update_time`) USING BTREE,
       KEY `uid` (`uid`) USING BTREE,
       KEY `answer_count` (`answer_count`) USING BTREE,
       KEY `agree_count` (`agree_count`) USING BTREE,
       KEY `title` (`title`) USING BTREE,
       KEY `is_lock` (`is_lock`) USING BTREE,
       KEY `is_anonymous` (`is_anonymous`) USING BTREE,
       KEY `popular_value` (`popular_value`) USING BTREE,
       KEY `best_answer` (`best_answer`) USING BTREE,
       KEY `popular_value_update` (`popular_value_update`) USING BTREE,
       KEY `against_count` (`against_count`) USING BTREE,
       KEY `is_recommend` (`is_recommend`) USING BTREE,
       KEY `modify_count` (`modify_count`) USING BTREE,
       KEY `sort` (`sort`) USING BTREE,
       FULLTEXT KEY title_detail_fulltext(title,detail)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='问题表';

-- ----------------------------
-- Table structure for uk_question_comment
-- ----------------------------
DROP TABLE IF EXISTS `uk_question_comment`;
CREATE TABLE `uk_question_comment` (
       `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
       `question_id` int(11) DEFAULT '0' COMMENT '问题id',
       `uid` int(11) DEFAULT '0' COMMENT '评论人',
       `message` text COMMENT '评论内容',
       `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
       `at_info` varchar(100) DEFAULT NULL COMMENT '用户信息',
       `create_time` int(10) DEFAULT '0' COMMENT '评论时间',
       `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `question_id` (`question_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='问题评论表';

-- ----------------------------
-- Records of uk_question_comment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_question_focus
-- ----------------------------
DROP TABLE IF EXISTS `uk_question_focus`;
CREATE TABLE `uk_question_focus` (
         `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
         `question_id` int(11) DEFAULT NULL COMMENT '问题ID',
         `uid` int(11) DEFAULT NULL COMMENT '用户UID',
         `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
         PRIMARY KEY (`id`) USING BTREE,
         KEY `uid` (`uid`) USING BTREE,
         KEY `question_id` (`question_id`) USING BTREE,
         KEY `question_uid` (`question_id`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='问题关注表';

-- ----------------------------
-- Records of uk_question_focus
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_question_invite
-- ----------------------------
DROP TABLE IF EXISTS `uk_question_invite`;
CREATE TABLE `uk_question_invite` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `question_id` int(11) NOT NULL COMMENT '问题ID',
      `sender_uid` int(11) NOT NULL,
      `recipient_uid` int(11) DEFAULT NULL,
      `create_time` int(10) DEFAULT '0' COMMENT '添加时间',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `question_id` (`question_id`) USING BTREE,
      KEY `sender_uid` (`sender_uid`) USING BTREE,
      KEY `recipient_uid` (`recipient_uid`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='邀请问答';

-- ----------------------------
-- Records of uk_question_invite
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_question_vote
-- ----------------------------
DROP TABLE IF EXISTS `uk_question_vote`;
CREATE TABLE `uk_question_vote` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `uid` int(10) NOT NULL COMMENT '投票用户',
        `item_type` varchar(16) DEFAULT NULL COMMENT '内容类型',
        `item_id` int(10) NOT NULL COMMENT '内容ID',
        `vote_value` tinyint(1) DEFAULT '0' COMMENT '1赞同,-1反对',
        `create_time` int(10) NOT NULL COMMENT '操作时间',
        `weigh_factor` int(10) DEFAULT '0' COMMENT '赞同反对系数',
        `item_uid` int(10) DEFAULT '0' COMMENT '被投票用户',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `uid` (`uid`) USING BTREE,
        KEY `item_type` (`item_type`) USING BTREE,
        KEY `item_id` (`item_id`) USING BTREE,
        KEY `create_time` (`create_time`) USING BTREE,
        KEY `item_uid` (`item_uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='赞踩表';

-- ----------------------------
-- Records of uk_question_vote
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_queue
-- ----------------------------
DROP TABLE IF EXISTS `uk_queue`;
CREATE TABLE `uk_queue` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `code` varchar(20) NOT NULL DEFAULT '' COMMENT '任务编号',
        `title` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
        `command` varchar(500) DEFAULT '' COMMENT '执行指令',
        `exec_pid` bigint(20) DEFAULT '0' COMMENT '执行进程',
        `exec_data` longtext COMMENT '执行参数',
        `exec_time` bigint(20) DEFAULT '0' COMMENT '执行时间',
        `exec_desc` varchar(500) DEFAULT '' COMMENT '执行描述',
        `enter_time` decimal(20,4) DEFAULT '0.0000' COMMENT '开始时间',
        `outer_time` decimal(20,4) DEFAULT '0.0000' COMMENT '结束时间',
        `loops_time` bigint(20) DEFAULT '0' COMMENT '循环时间',
        `attempts` bigint(20) DEFAULT '0' COMMENT '执行次数',
        `rscript` tinyint(1) DEFAULT '1' COMMENT '任务类型(0单例,1多例)',
        `status` tinyint(1) DEFAULT '1' COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
        `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `queue_code` (`code`) USING BTREE,
        KEY `queue_title` (`title`) USING BTREE,
        KEY `queue_status` (`status`) USING BTREE,
        KEY `queue_rscript` (`rscript`) USING BTREE,
        KEY `queue_create_at` (`create_at`) USING BTREE,
        KEY `queue_exec_time` (`exec_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务队列';

-- ----------------------------
-- Records of uk_queue
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_report
-- ----------------------------
DROP TABLE IF EXISTS `uk_report`;
CREATE TABLE `uk_report` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `uid` int(11) DEFAULT '0' COMMENT '举报用户id',
     `item_type` varchar(50) DEFAULT NULL COMMENT '类别内容类型',
     `item_id` int(11) DEFAULT '0' COMMENT '举报内容id',
     `report_type` varchar(255) DEFAULT NULL COMMENT '举报类型',
     `reason` varchar(255) DEFAULT NULL COMMENT '举报理由',
     `url` varchar(255) DEFAULT NULL COMMENT '举报内容也main',
     `create_time` int(11) DEFAULT '0' COMMENT '举报时间',
     `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否处理',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `create_time` (`create_time`) USING BTREE,
     KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of uk_report
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_score_log
-- ----------------------------
DROP TABLE IF EXISTS `uk_score_log`;
CREATE TABLE `uk_score_log` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) DEFAULT '0' COMMENT '触发行为用户id',
    `record_id` char(16) DEFAULT NULL COMMENT '触发行为的数据id',
    `action_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '触发行为的类型',
    `score` int(11) DEFAULT NULL COMMENT '操作积分',
    `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '积分说明',
    `balance` int DEFAULT '0' COMMENT '积分余额',
    `create_time` int(11) DEFAULT '0',
    `record_db` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '记录数据表',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `action_type` (`action_type`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `score` (`score`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='积分记录表';

-- ----------------------------
-- Records of uk_score_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_score_rule
-- ----------------------------
DROP TABLE IF EXISTS `uk_score_rule`;
CREATE TABLE `uk_score_rule` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
     `name` char(30) NOT NULL DEFAULT '' COMMENT '唯一标识',
     `title` varchar(255) NOT NULL DEFAULT '' COMMENT '规则说明',
     `cycle` int(11) NOT NULL DEFAULT '0' COMMENT '执行次数',
     `cycle_type` char(10) NOT NULL DEFAULT '' COMMENT '执行单位;month月,week周,day天,hour小时,minute分钟,second秒数',
     `max` int(10)  DEFAULT '0' COMMENT '最大执行次数',
     `score` int(11)  DEFAULT '0' COMMENT '操作积分',
     `log` text COMMENT '日志规则',
     `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
     `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
     `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
     UNIQUE KEY `name` (`name`) USING BTREE,
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='积分规则表';

-- ----------------------------
-- Records of uk_score_rule
-- ----------------------------
BEGIN;
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (1, 'user_register', '用户注册赠送积分', 1, 'day', 0, 200, '用户注册奖励积分', 1, 1616743608, 1616743608);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (2, 'user_login', '每日登录', 1, 'day', 1, 10, '登录系统,获得积分奖励', 1, 0, 0);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (3, 'publish_question', '发起提问', 1, 'day', 0, -5, '发表提问,扣除积分', 1, 1616738150, 1616738150);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (4, 'publish_article', '发表文章', 1, 'day', 3, 10, '发表文章,奖励积分奖励', 1, 1616740189, 1616740189);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (5, 'publish_question_answer', '回答问题', 0, 'day', 0, 5, '回答问题,获得积分奖励', 1, 1616740545, 1616740545);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (6, 'set_best_answer', '被设为最佳回答', 1, 'day', 0, 20, '回答被设为最佳,获得积分奖励', 1, 1616743608, 1616743608);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (7, 'invite_user_answer_question', '邀请用户回答问题', 1, 'day', 0, -10, '邀请用户回答问题,扣除积分', 1, 1616743608, 1616743608);
INSERT INTO `uk_score_rule` (`id`, `name`, `title`, `cycle`, `cycle_type`, `max`, `score`, `log`, `status`, `update_time`, `create_time`) VALUES (8, 'answer_question_by_invite', '被邀请用户回答问题', 1, 'day', 0, 10, '被邀请回答问题并回答,获得积分奖励', 1, 1616743608, 1616743608);
COMMIT;

-- ----------------------------
-- Table structure for uk_third
-- ----------------------------
DROP TABLE IF EXISTS `uk_third`;
CREATE TABLE `uk_third` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
    `platform` varchar(30) NOT NULL DEFAULT '' COMMENT '第三方应用',
    `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方唯一ID',
    `open_username` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方会员昵称',
    `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'AccessToken',
    `refresh_token` varchar(255) NOT NULL DEFAULT 'RefreshToken',
    `expires_in` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
    `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
    `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
    `login_time` int(10) unsigned DEFAULT NULL COMMENT '登录时间',
    `expire_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `platform` (`platform`,`openid`) USING BTREE,
    KEY `uid` (`uid`,`platform`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='第三方登录表';

-- ----------------------------
-- Records of uk_third
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for uk_topic
-- ----------------------------
DROP TABLE IF EXISTS `uk_topic`;
CREATE TABLE `uk_topic` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题id',
        `pid` int(10) DEFAULT '0',
        `title` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '话题标题',
        `discuss` int(11) DEFAULT '0' COMMENT '讨论计数',
        `description` text CHARACTER SET utf8mb4 COMMENT '话题描述',
        `pic` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '话题图片',
        `lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '话题是否锁定 1 锁定 0 未锁定',
        `top` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐到首页',
        `focus` int(11) DEFAULT '0' COMMENT '关注计数',
        `related` tinyint(1) DEFAULT '0' COMMENT '是否被用户关联',
        `url_token` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
        `seo_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
        `seo_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `seo_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `discuss_week` int(10) DEFAULT '0',
        `discuss_month` int(10) DEFAULT '0',
        `discuss_update` int(10) DEFAULT '0',
        `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
        `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '审核状态',
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE KEY `title` (`title`) USING BTREE,
        KEY `url_token` (`url_token`) USING BTREE,
        KEY `discuss` (`discuss`) USING BTREE,
        KEY `create_time` (`create_time`) USING BTREE,
        KEY `related` (`related`) USING BTREE,
        KEY `focus` (`focus`) USING BTREE,
        KEY `lock` (`lock`) USING BTREE,
        KEY `pid` (`pid`) USING BTREE,
        KEY `discuss_week` (`discuss_week`) USING BTREE,
        KEY `discuss_month` (`discuss_month`) USING BTREE,
        KEY `discuss_update` (`discuss_update`) USING BTREE,
        FULLTEXT KEY title_description_fulltext(title,description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='话题';

-- ----------------------------
-- Table structure for uk_topic_focus
-- ----------------------------
DROP TABLE IF EXISTS `uk_topic_focus`;
CREATE TABLE `uk_topic_focus` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
      `topic_id` int(11) DEFAULT NULL COMMENT '话题ID',
      `uid` int(11) DEFAULT NULL COMMENT '用户UID',
      `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE,
      KEY `topic_id` (`topic_id`) USING BTREE,
      KEY `topic_uid` (`topic_id`,`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='话题关注表';

-- ----------------------------
-- Table structure for uk_topic_relation
-- ----------------------------
DROP TABLE IF EXISTS `uk_topic_relation`;
CREATE TABLE `uk_topic_relation` (
     `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
     `topic_id` int(11) DEFAULT '0' COMMENT '话题id',
     `uid` int(11) DEFAULT '0' COMMENT '用户ID',
     `item_id` int(11) DEFAULT '0',
     `item_type` varchar(16) DEFAULT NULL,
     `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除',
     `create_time` int(10) DEFAULT '0' COMMENT '添加时间',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `topic_id` (`topic_id`) USING BTREE,
     KEY `uid` (`uid`) USING BTREE,
     KEY `item_type` (`item_type`) USING BTREE,
     KEY `item_id` (`item_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for uk_users
-- ----------------------------
DROP TABLE IF EXISTS `uk_users`;
CREATE TABLE `uk_users` (
    `uid` int(11) NOT NULL AUTO_INCREMENT,
    `nick_name` varchar(20) NOT NULL COMMENT '昵称',
    `user_name` varchar(20) NOT NULL COMMENT '用户名',
    `password` varchar(64) NOT NULL COMMENT '用户密码',
    `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
    `mobile` char(11) DEFAULT NULL COMMENT '手机号',
    `sex` tinyint(1) unsigned DEFAULT '0' COMMENT '0保密1男2女',
    `is_first_login` tinyint(1) unsigned DEFAULT '0' COMMENT '是否首次登陆',
    `inbox_unread` int(10) unsigned DEFAULT '0' COMMENT '未读私信',
    `notify_unread` int(10) unsigned DEFAULT '0' COMMENT '未读通知',
    `agree_count` int(10) unsigned DEFAULT '0' COMMENT '赞同数量',
    `question_count` int(255) unsigned DEFAULT '0' COMMENT '问题数量',
    `answer_count` int(255) unsigned DEFAULT '0' COMMENT '回答数量',
    `article_count` int(10) unsigned DEFAULT '0' COMMENT '发布文章数量',
    `fans_count` int(10) unsigned DEFAULT '0' COMMENT '粉丝数量',
    `friend_count` int(10) unsigned DEFAULT '0' COMMENT '关注数量',
    `topic_focus_count` int(10) unsigned DEFAULT '0' COMMENT '话题关注数量',
    `available_invite_count` int UNSIGNED NULL DEFAULT 0 COMMENT '可用邀请数量',
    `is_valid_email` tinyint(1) unsigned DEFAULT '0' COMMENT '是否验证邮箱',
    `is_valid_mobile` tinyint(1) unsigned DEFAULT '0' COMMENT '是否验证手机号',
    `score` int(10) unsigned DEFAULT '0' COMMENT '积分数量',
    `avatar` varchar(255) DEFAULT NULL COMMENT '用户头衔',
    `signature` varchar(255) DEFAULT NULL COMMENT '签名',
    `verified` varchar(255) DEFAULT NULL COMMENT '认证类型',
    `power` int unsigned DEFAULT '0' COMMENT '声望值',
    `last_login_time` int(10) unsigned DEFAULT '0' COMMENT '最后登录时间',
    `last_login_ip` varchar(20) DEFAULT '' COMMENT '最后登录IP',
    `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '用户余额',
    `frozen_money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '冻结金额',
    `deal_password` varchar(64) NULL COMMENT '交易密码',
    `birthday` int unsigned DEFAULT '0' COMMENT '生日',
    `url_token` varchar(255) DEFAULT NULL COMMENT '自定义URL',
    `views_count` int(255) unsigned DEFAULT '0' COMMENT '个人主页浏览量',
    `extend` varchar(255) DEFAULT NULL,
    `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '用户状态0已删除1正常2待审核3已封禁',
    `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`uid`) USING BTREE,
    UNIQUE KEY `user_name` (`user_name`) USING BTREE,
    UNIQUE KEY `url_token` (`url_token`) USING BTREE,
    FULLTEXT KEY user_nick_name_fulltext(nick_name,user_name)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for uk_users_follow
-- ----------------------------
DROP TABLE IF EXISTS `uk_users_follow`;
CREATE TABLE `uk_users_follow` (
       `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
       `fans_uid` int(11) DEFAULT NULL COMMENT '关注人的UID',
       `friend_uid` int(11) DEFAULT NULL COMMENT '被关注人的uid',
       `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
       `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
       PRIMARY KEY (`id`) USING BTREE,
       KEY `fans_uid` (`fans_uid`) USING BTREE,
       KEY `friend_uid` (`friend_uid`) USING BTREE,
       KEY `user_follow` (`fans_uid`,`friend_uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户关注表';
-- ----------------------------
-- Records of uk_users_follow
-- ----------------------------
BEGIN;
COMMIT;

DROP TABLE IF EXISTS `uk_users_score_group`;
CREATE TABLE `uk_users_score_group` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
      `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
      `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
      `permission` text COMMENT '前台控制权限',
      `group_icon` varchar(255) DEFAULT NULL COMMENT '用户组图标',
      `min_score` int unsigned DEFAULT '0' COMMENT '最小条件',
      `max_score` int unsigned DEFAULT '0' COMMENT '最大条件',
      `create_time` int(10) unsigned DEFAULT '0' COMMENT '最后修改时间',
      `update_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
      PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='前台积分组';

-- ----------------------------
-- Table structure for uk_users_group
-- ----------------------------
DROP TABLE IF EXISTS `uk_users_power_group`;
CREATE TABLE `uk_users_power_group` (
        `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
        `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
        `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
        `permission` text COMMENT '前台控制权限',
        `group_icon` varchar(255) DEFAULT NULL COMMENT '用户组图标',
        `min_power` int unsigned DEFAULT '0' COMMENT '最小条件',
        `max_power` int unsigned DEFAULT '0' COMMENT '最大条件',
        `power_factor` int unsigned DEFAULT '0' COMMENT '声望系数',
        `create_time` int(10) unsigned DEFAULT '0' COMMENT '最后修改时间',
        `update_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
        PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='前台声望组';

-- ----------------------------
-- Records of uk_users_group
-- ----------------------------
BEGIN;
INSERT INTO `uk_users_power_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_power`, `max_power`, `power_factor`) VALUES (1, '青铜会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":1}', '/static/common/image/group/1.png', 1604053796, 1619099168, 0, 100, 1);
INSERT INTO `uk_users_power_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_power`, `max_power`, `power_factor`) VALUES (2, '白银会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":1}', '/static/common/image/group/2.png', 1604053796, 1619099206, 100, 200, 2);
INSERT INTO `uk_users_power_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_power`, `max_power`, `power_factor`) VALUES (3, '黄金会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/3.png', 1604053796, 1619099220, 200, 500, 3);
INSERT INTO `uk_users_power_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_power`, `max_power`, `power_factor`) VALUES (4, '铂金会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/4.png', 1604053796, 1619099231, 500, 1000, 4);
INSERT INTO `uk_users_power_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_power`, `max_power`, `power_factor`) VALUES (5, '钻石会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/5.png', 1604053796, 1619099242, 1000, 9999, 5);
COMMIT;

-- ----------------------------
-- Records of uk_users_group
-- ----------------------------
BEGIN;
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (1, '青铜会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/1.png', 1604053796, 1619099168, 0, 1000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (2, '白银会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/2.png', 1604053796, 1619099206, 1000, 3000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (3, '黄金会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/3.png', 1604053796, 1619099220, 3000, 5000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (4, '铂金会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/4.png', 1604053796, 1619099231, 5000, 7000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (5, '钻石会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/5.png', 1604053796, 1619099242, 7000, 9000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (6, '星耀会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/6.png', 1604053796, 1619099254, 9000, 12000);
INSERT INTO `uk_users_score_group` (`id`, `title`, `status`, `permission`, `group_icon`, `create_time`, `update_time`, `min_score`, `max_score`) VALUES (7, '王者会员', 1, '{\"publish_question_enable\":1,\"publish_question_approval\":1,\"publish_article_enable\":1,\"publish_article_approval\":1,\"create_topic_enable\":0}', '/static/common/image/group/7.png', 1604053796, 1619099267, 12000, 20000);
COMMIT;

-- ----------------------------
-- Table structure for uk_users_online
-- ----------------------------
DROP TABLE IF EXISTS `uk_users_online`;
CREATE TABLE `uk_users_online` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `uid` int(11) NOT NULL COMMENT '用户 ID',
   `last_login_time` int(11) DEFAULT '0' COMMENT '上次活动时间',
   `last_login_ip` varchar(20) DEFAULT '' COMMENT '客户端ip',
   `last_url` varchar(255) DEFAULT NULL COMMENT '停留页面',
   `user_agent` text DEFAULT NULL COMMENT '用户客户端信息',
   PRIMARY KEY (`id`) USING BTREE,
   KEY `uid` (`uid`) USING BTREE,
   KEY `last_login_time` (`last_login_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='在线用户列表';

-- ----------------------------
-- Table structure for uk_users_permission
-- ----------------------------
DROP TABLE IF EXISTS `uk_users_permission`;
CREATE TABLE `uk_users_permission` (
       `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
       `group_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分组类型,system系统组,score积分组,power声望组',
       `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量名',
       `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量标题',
       `tips` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量描述',
       `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
       `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
       `option` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量字典数据',
       `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
       PRIMARY KEY (`id`) USING BTREE,
       UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户权限配置';

-- ----------------------------
-- Records of uk_users_permission
-- ----------------------------
BEGIN;
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (1, 'system', 'visit_website', '允许浏览网站', '', 'radio', '1', '[\"否\",\"是\"]', 0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (2, 'system', 'publish_question_enable', '发布问题', '是否允许发起问题', 'radio', '1', '[\"否\",\"是\"]', 0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (3, 'system', 'publish_question_approval', '提问审核', '发起问题时是否需要审核', 'radio', '1', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (4, 'system', 'publish_article_enable', '发起文章', '是否允许发起文章', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (5, 'system', 'publish_article_approval', '发文审核', '发起文章时是否需要审核', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (6, 'system', 'publish_answer_enable', '允许回答问题', '是否允许发起回答', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (7, 'system', 'publish_answer_approval', '回答审核', '发起回答时是否需要审核', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (8, 'system', 'modify_answer_approval', '修改回答审核', '修改回答时是否需要审核', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (9, 'system', 'modify_article_approval', '修改文章审核', '修改文章时是否需要审核', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (10, 'system', 'modify_question_approval', '修改提问审核', '修改提问时是否需要审核', 'radio', '0', '[\"否\",\"是\"]',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (11, 'power', 'available_invite_count', '可邀请用户数量', '可邀请用户数量', 'number', '0', '',  0);
INSERT INTO `uk_users_permission` (`id`, `group_type`, `name`, `title`, `tips`, `type`, `value`, `option`, `sort`) VALUES (12, 'system', 'create_topic_enable', '创建话题', '', 'radio', '0', '[\"否\",\"是\"]', 0);

COMMIT;


DROP TABLE IF EXISTS `uk_links`;
CREATE TABLE `uk_links` (
   `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
   `url` varchar(255) NOT NULL DEFAULT '' COMMENT '网站地址',
   `logo` varchar(80) NOT NULL DEFAULT '' COMMENT '网站logo',
   `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
   `sort` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
   `status` tinyint(10) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
   `create_time` int(11) NOT NULL,
   `update_time` int(11) NOT NULL,
   PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='友情链接';

DROP TABLE IF EXISTS `uk_admin_log`;
CREATE TABLE `uk_admin_log` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `uid` text NOT NULL COMMENT '管理员',
    `url` varchar(255) NOT NULL DEFAULT '' COMMENT '操作页面	',
    `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
    `content` text NOT NULL COMMENT '日志内容',
    `ip` varchar(20) NOT NULL DEFAULT '' COMMENT '操作IP',
    `user_agent` text NOT NULL COMMENT 'User-Agent',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='管理员日志';

DROP TABLE IF EXISTS `uk_topic_logs`;
CREATE TABLE `uk_topic_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL COMMENT '日志记录内容',
  `create_time` int(11) DEFAULT NULL COMMENT '操作时间',
  `user_info` varchar(255) DEFAULT NULL COMMENT '操作人信息',
  `item_info` varchar(255) DEFAULT NULL COMMENT '内容信息',
  `type` tinyint(1) DEFAULT NULL COMMENT '1添加2修改3文章4问题',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='话题操作日志表';

DROP TABLE IF EXISTS `uk_favorite_focus`;
CREATE TABLE `uk_favorite_focus` (
     `id` int NOT NULL AUTO_INCREMENT COMMENT '自增ID',
     `tag_id` int DEFAULT NULL COMMENT '收藏夹ID',
     `uid` int DEFAULT NULL COMMENT '用户UID',
     `create_time` int DEFAULT NULL COMMENT '添加时间',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `uid` (`uid`) USING BTREE,
     KEY `tag_id` (`tag_id`) USING BTREE,
     KEY `tag_uid` (`tag_id`,`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='收藏关注表';

DROP TABLE IF EXISTS `uk_page`;
CREATE TABLE `uk_page` (
       `id` int NOT NULL AUTO_INCREMENT,
       `title` varchar(255) DEFAULT NULL,
       `keywords` varchar(255) DEFAULT NULL,
       `description` varchar(255) DEFAULT NULL,
       `contents` text,
       `url_name` varchar(32) NOT NULL,
       `status` tinyint(1) NOT NULL DEFAULT '1',
       `create_time` int DEFAULT '0' COMMENT '发布时间',
       `update_time` int DEFAULT '0' COMMENT '更新时间',
       PRIMARY KEY (`id`),
       UNIQUE KEY `url_name` (`url_name`),
       KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='单页表';

DROP TABLE IF EXISTS `uk_users_setting`;
CREATE TABLE `uk_users_setting` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `uid` int unsigned DEFAULT '0' COMMENT '用户id',
    `email_setting` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮件设置',
    `notify_setting` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '通知设置',
    `inbox_setting` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '私信设置',
    `create_time` int unsigned DEFAULT '0' COMMENT '添加时间',
    `update_time` int unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='用户设置表';

DROP TABLE IF EXISTS `uk_users_verify`;
CREATE TABLE `uk_users_verify` (
   `id` int NOT NULL AUTO_INCREMENT,
   `uid` int NOT NULL COMMENT '用户ID',
   `data` text COMMENT '审核数据',
   `status` tinyint(1) DEFAULT '0' COMMENT '审核状态0待审核1已审核2拒绝审核',
   `type` varchar(32) DEFAULT '' COMMENT '审核类型',
   `reason` varchar(255) NOT NULL COMMENT '审核理由',
   `create_time` int NOT NULL,
   PRIMARY KEY (`id`),
   KEY `uid` (`uid`),
   KEY `type` (`type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='用户认证资料表';

DROP TABLE IF EXISTS `uk_verify_field`;
CREATE TABLE `uk_verify_field` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量名',
     `verify_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '认证类型',
     `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量标题',
     `tips` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量描述',
     `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
     `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
     `option` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量字典数据',
     `sort` int COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'  COMMENT '排序字段',
     `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
     `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
     PRIMARY KEY (`id`) USING BTREE,
     UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='认证字段管理';

INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (1, 'real_name', 'people', '真实姓名', '填写您的真实姓名', 'text', '', '[]', 0, 1621141649, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (2, 'card', 'people', '身份证', '填写身份证号码', 'text', '', '[]', 0, 1621141718, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (3, 'mobile', 'people', '联系方式', '', 'text', '', '[]', 0, 1621141759, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (4, 'remark', 'people', '认证说明', '', 'textarea', '', '[]', 0, 1621141784, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (5, 'company_name', 'company', '公司名称', '', 'text', '', '[]', 0, 1621143505, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (6, 'company_code', 'company', '组织代码', '', 'text', '', '[]', 0, 1621143524, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (7, 'company_mobile', 'company', '联系电话', '', 'text', '', '[]', 0, 1621143553, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (8, 'company_code_image', 'company', '组织代码附件', '', 'image', '', '[]', 0, 1621143596, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (9, 'company_image', 'company', '营业执照', '', 'image', '', '[]', 0, 1621143630, 0);
INSERT INTO `uk_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`) VALUES (10, 'company_remark', 'company', '认证说明', '', 'textarea', '', '[]', 0, 1621143655, 0);

DROP TABLE IF EXISTS `uk_users_active`;
CREATE TABLE `uk_users_active` (
       `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
       `uid` int(11) DEFAULT '0',
       `expire_time` int(10) DEFAULT NULL,
       `active_code` varchar(32) DEFAULT NULL,
       `active_type` varchar(50) DEFAULT NULL,
       `create_time` int(10) DEFAULT NULL,
       `create_valid_ip` varchar(20) DEFAULT NULL,
       `active_time` int(10) DEFAULT NULL,
       `active_ip` varchar(20) DEFAULT NULL,
       PRIMARY KEY (`id`),
       KEY `active_code` (`active_code`),
       KEY `active_type` (`active_type`),
       KEY `uid` (`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='用户激活码';

DROP TABLE IF EXISTS `uk_invite`;
CREATE TABLE `uk_invite` (
     `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '激活ID',
     `uid` int DEFAULT '0' COMMENT '用户ID',
     `invite_code` varchar(32) DEFAULT NULL COMMENT '激活码',
     `invite_email` varchar(255) DEFAULT NULL COMMENT '激活email',
     `create_time` int DEFAULT NULL COMMENT '添加时间',
     `create_ip` varchar(20) DEFAULT NULL COMMENT '添加IP',
     `active_expire` tinyint(1) DEFAULT '0' COMMENT '激活过期',
     `active_time` int DEFAULT NULL COMMENT '激活时间',
     `active_ip` varchar(20) DEFAULT NULL COMMENT '激活IP',
     `active_status` tinyint DEFAULT '0' COMMENT '1已使用0未使用-1已删除',
     `active_uid` int DEFAULT NULL,
     PRIMARY KEY (`id`),
     KEY `uid` (`uid`),
     KEY `invite_code` (`invite_code`),
     KEY `invite_email` (`invite_email`),
     KEY `active_time` (`active_time`),
     KEY `active_ip` (`active_ip`),
     KEY `active_status` (`active_status`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='邀请注册码管理';

DROP TABLE IF EXISTS `uk_route_rule`;
CREATE TABLE `uk_route_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `rule` varchar(255) DEFAULT NULL COMMENT '规则',
  `module` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='路由配置';

INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (1, 'index/index', '首页', '/', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (2, 'question/index', '问题列表', 'questions', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (3, 'article/index', '文章列表', 'articles', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (4, 'question/detail', '问题详情带回答', 'q/[:id]-[:answer]', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (5, 'question/detail', '问题详情', 'q/[:id]', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (6, 'article/detail', '文章详情', 'news/[:id]', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (7, 'account/login', '登录', 'login', 'member');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (8, 'account/register', '注册', 'register', 'member');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (9, 'topic/index', '话题广场', 'topic/', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (10, 'topic/detail', '话题详情', 'tag/[:id]', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (11, 'question/publish', '发起提问', 'publish_question/[:id]', 'ask');
INSERT INTO `uk_route_rule` (`id`, `url`, `title`, `rule`, `module`) VALUES (12, 'article/publish', '发起提问', 'publish_article/[:id]', 'ask');


DROP TABLE IF EXISTS `uk_answer_thanks`;
CREATE TABLE `uk_answer_thanks` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `uid` int DEFAULT '0' COMMENT '用户ID',
    `answer_id` int DEFAULT '0' COMMENT '回答ID',
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

DROP TABLE IF EXISTS `uk_users_forbidden`;
CREATE TABLE `uk_users_forbidden` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `uid` int unsigned NOT NULL DEFAULT '0',
      `forbidden_time` int unsigned DEFAULT '0' COMMENT '封禁时长',
      `forbidden_reason` varchar(255) DEFAULT NULL COMMENT '封禁原因',
      `status` tinyint unsigned DEFAULT '0' COMMENT '是否删除',
      `create_time` int unsigned DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户封禁记录表';


DROP TABLE IF EXISTS `uk_module`;
CREATE TABLE `uk_module` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统模块',
     `name` varchar(50) NOT NULL COMMENT '模块名(英文)',
     `identifier` varchar(100) NOT NULL COMMENT '模块标识(模块名(字母).开发者标识.module)',
     `title` varchar(50) NOT NULL COMMENT '模块标题',
     `intro` varchar(255) NOT NULL COMMENT '模块简介',
     `author` varchar(100) NOT NULL COMMENT '作者',
     `icon` varchar(80) NOT NULL DEFAULT '' COMMENT '图标',
     `version` varchar(20) NOT NULL COMMENT '版本号',
     `url` varchar(255) NOT NULL COMMENT '链接',
     `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未安装，2未启用，1已启用',
     `default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认模块(只能有一个)',
     `config` text NOT NULL COMMENT '配置',
     `app_id` varchar(30) NOT NULL DEFAULT '0' COMMENT '应用市场ID(0本地)',
     `app_keys` varchar(200) DEFAULT '' COMMENT '应用秘钥',
     `theme` varchar(50) NOT NULL DEFAULT 'default' COMMENT '主题模板',
     `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
     `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
     PRIMARY KEY (`id`),
     UNIQUE KEY `name` (`name`),
     UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='模块管理';

INSERT INTO `uk_module` (`id`, `system`, `name`, `identifier`, `title`, `intro`, `author`, `icon`, `version`, `url`, `sort`, `status`, `default`, `config`, `app_id`, `app_keys`, `theme`, `create_time`, `update_time`)
VALUES
(1, 1, 'admin', 'system.uk.module', '后台模块', '系统核心模块', 'UKnowing官方', '', '1.0.0', 'https://www.uknowing.com', 0, 1, 0, '', '0', '', 'default', 0, 0),
(2, 0, 'ask', 'ask.uk.module', '问答模块', '系统核心模块。', 'UKnowing官方', '', '1.0.0', 'https://www.uknowing.com', 0, 1, 1, '', '0', '', 'default', 0, 0),
(3, 1, 'member', 'member.uk.module', '用户模块', '系统核心模块', 'UKnowing官方', '', '1.0.0', 'https://www.uknowing.com', 0, 1, 0, '', '0', '', 'default', 0, 0),
(4, 1, 'api', 'api.uk.module', '接口模块', '系统核心模块', 'UKnowing官方', '', '1.0.0', 'https://www.uknowing.com', 0, 1, 0, '', '0', '', 'default', 0, 0);
