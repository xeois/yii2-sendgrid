<?php
// ensure we get report on all possible php errors

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

//define('SENDGRID_FROM', '<EMAIL_FROM>');
//define('SENDGRID_TOKEN', '<YOUR_TOKEN>');
//define('SENDGRID_TO', '<EMAIL_TO>');
//define('SENDGRID_TEMPLATE', '<TEMPLATE_ID>');
//define('SENDGRID_DYNAMIC_TEMPLATE', '<TEMPLATE_ID>');

define('SENDGRID_FROM', 'test@example.com');
define('SENDGRID_TOKEN', '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');
define('SENDGRID_TO', 'admiral.smith34@gmail.com');
define('SENDGRID_TEMPLATE', '');
define('SENDGRID_TEST_SEND', false);


//Yii::setAlias('@tests/unit', __DIR__ . '/unit');
Yii::setAlias('@sweelix/sendgrid', dirname(__DIR__) .'/src');
