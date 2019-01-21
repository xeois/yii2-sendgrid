<?php
// ensure we get report on all possible php errors

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

define('SENDGRID_FROM', '<EMAIL_FROM>');
define('SENDGRID_TOKEN', '<YOUR_TOKEN>');
define('SENDGRID_TO', '<EMAIL_TO>');
define('SENDGRID_TEMPLATE', '<TEMPLATE_ID>');

//Yii::setAlias('@tests/unit', __DIR__ . '/unit');
Yii::setAlias('@sweelix/sendgrid', dirname(__DIR__) .'/src');
