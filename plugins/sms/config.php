<?php

return array (
  '基础配置' => 
  array (
    'enable' => 
    array (
      'title' => '是否启用',
      'type' => 'radio',
      'value' => 'N',
      'options' => 
      array (
        'N' => '不启用',
        'ali' => '阿里云短信',
        'tencent' => '腾讯云短信',
      ),
      'tips' => 'AccessKey ID',
    ),
  ),
  '阿里云短信' => 
  array (
    'AliAccessKeyId' => 
    array (
      'title' => 'AccessKeyID',
      'type' => 'text',
      'value' => 'SXUmpbWGwice3AD9',
      'options' => 
      array (
      ),
      'tips' => 'AccessKey ID',
    ),
    'AliAccessKeySecret' => 
    array (
      'title' => 'AccessKeySecret',
      'type' => 'text',
      'value' => '8geAVYsLFPKmYtzLkDK2GiCnMvNOZ8',
      'options' => 
      array (
      ),
      'tips' => 'AccessKey Secret',
    ),
    'AliSignName' => 
    array (
      'title' => '短信签名',
      'type' => 'text',
      'value' => '安辰博客',
      'options' => 
      array (
      ),
      'tips' => '请在控制台国内消息或国际/港澳台消息页面中的签名管理页签下签名名称一列查看。',
    ),
    'AliTemplateCode' => 
    array (
      'title' => '短信模板',
      'type' => 'text',
      'options' => 
      array (
      ),
      'value' => 'SMS_181851468',
      'tips' => '请在控制台国内消息或国际/港澳台消息页面中的模板管理页签下模板CODE一列查看',
    ),
    'AliEndpoint' => 
    array (
      'title' => '域名节点',
      'type' => 'text',
      'value' => 'dysmsapi.aliyuncs.com',
      'options' => 
      array (
      ),
      'tips' => '域名节点',
    ),
  ),
  '腾讯云短信' => 
  array (
    'tencentSecretId' => 
    array (
      'title' => 'SecretId',
      'type' => 'text',
      'value' => '',
      'options' => 
      array (
      ),
      'tips' => 'SecretId',
    ),
    'tencentAccessKeyId' => 
    array (
      'title' => 'AccessKeyID',
      'type' => 'text',
      'value' => '',
      'options' => 
      array (
      ),
      'tips' => 'AccessKey ID',
    ),
    'tencentAccessKeySecret' => 
    array (
      'title' => 'AccessKeySecret',
      'type' => 'text',
      'value' => '',
      'options' => 
      array (
      ),
      'tips' => 'AccessKey Secret',
    ),
    'tencentSignName' => 
    array (
      'title' => '短信签名',
      'type' => 'text',
      'value' => '',
      'options' => 
      array (
      ),
      'tips' => '请在控制台国内消息或国际/港澳台消息页面中的签名管理页签下签名名称一列查看。',
    ),
    'tencentTemplateCode' => 
    array (
      'title' => '短信模板',
      'type' => 'text',
      'options' => 
      array (
      ),
      'value' => '',
      'tips' => '请在控制台国内消息或国际/港澳台消息页面中的模板管理页签下模板CODE一列查看',
    ),
  ),
);
