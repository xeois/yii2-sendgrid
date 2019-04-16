<?php

use shershennm\sendgrid\Mailer;

class MailerTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        new \yii\console\Application(['id' => 'app', 'basePath' => __DIR__]);
    }

    public function testApiKeyRequired()
    {
        $this->expectException('\yii\base\InvalidConfigException');
        $mailer = new Mailer();
        $message = $mailer->compose();
        $mailer->sendMessage($message);
    }

    public function testCreateMailer()
    {
        $mailer = new Mailer(['apiKey' => SENDGRID_TOKEN, 'useFileTransport' => YII_DEBUG]);
        $this->assertInstanceOf(Mailer::class, $mailer);
    }
}
