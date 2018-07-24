<?php

use shershennm\sendgrid\Message;

class MessageTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        new \yii\console\Application([
            'id' => 'app',
            'basePath' => __DIR__,
            'components' => [
                'mailer' => new \djagya\sparkpost\Mailer(['apiKey' => 'string', 'useDefaultEmail' => false]),
            ],
        ]);
    }

    public function testCreateMessage()
    {
        $this->assertInstanceOf(Message::class, Yii::$app->mailer->compose());
    }

    public function testEmbedContent()
    {

    }

    public function testSetTo()
    {

    }

    public function testGetSendGridMessage()
    {

    }

    public function testSetCharset()
    {

    }

    public function testSetReplyTo()
    {

    }

    public function testSetSendGridSubstitution()
    {

    }

    public function testAttach()
    {

    }

    public function testGetReplyTo()
    {

    }

    public function testSetCc()
    {

    }

    public function testToString()
    {

    }

    public function testGetCharset()
    {

    }

    public function testGetTo()
    {

    }

    public function testGetFrom()
    {

    }

    public function testSetSubject()
    {

    }

    public function testAttachContent()
    {

    }

    public function testSetHtmlBody()
    {

    }

    public function testSetFrom()
    {

    }

    public function testSetBcc()
    {

    }

    public function testEmbed()
    {

    }

    public function testGetBcc()
    {

    }

    public function testGetSubject()
    {

    }

    public function testGetCc()
    {

    }

    public function testSetTextBody()
    {

    }
}
