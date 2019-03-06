<?php

use shershennm\sendgrid\Mailer;
use shershennm\sendgrid\Message;

class MessageTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        new yii\console\Application([
            'id' => 'app',
            'basePath' => __DIR__,
            'components' => [
                'mailer' => new Mailer(['apiKey' => SENDGRID_TOKEN, 'useFileTransport' => false]),
            ],
        ]);
    }

    public function testCreateMessage()
    {
        $this->assertInstanceOf(Message::class, Yii::$app->mailer->compose());
    }

    public function testEmbedContent()
    {
        $this->expectException('\yii\base\NotSupportedException');
        (new Message())->embedContent(null);
    }

    public function testGetSendGridMessage()
    {
        $this->assertInstanceOf(
            \SendGrid\Mail\Mail::class,
            (new Message())->getSendGridMessage()
        );
    }

    public function testSetCharset()
    {
        $this->expectException('\yii\base\NotSupportedException');
        (new Message())->setCharset(null);
    }

    public function testGetCharset()
    {
        $this->assertEquals('UTF-8', (new Message())->getCharset());
    }

    public function testSetSendGridSubstitution()
    {
        $this->assertInstanceOf(
            Message::class,
            (new Message())->setSendGridSubstitution(SENDGRID_TEMPLATE)
        );
    }

    public function testAttach()
    {
        $message = (new Message())->attach(__FILE__);
        $attachments = $message->sendGridMessage->getAttachments();
        $this->assertEquals('MessageTest.php', $attachments[0]->getFilename());
        $this->assertEquals(
            base64_encode(file_get_contents(__FILE__)),
            $attachments[0]->getContent()
        );
    }

    public function testSetGetReplyTo()
    {
        $message = (new Message())->setReplyTo(SENDGRID_TO);
        $this->assertEquals(SENDGRID_TO, ($message->getReplyTo())->getEmailAddress());
    }

    public function testSetGetCc()
    {
        $message = (new Message())->setCc(SENDGRID_TO);
        $this->assertEquals(SENDGRID_TO, $message->getCc()[0]->getEmail());
    }

    public function testToString()
    {
        $message = (new Message())
            ->setFrom(SENDGRID_FROM)
            ->setSubject('Test')
            ->setReplyTo(SENDGRID_TO);

        $jsonString = $message->toString();

        $decodedMessage = json_decode($jsonString);
        $this->assertEquals(SENDGRID_FROM, $decodedMessage->from->email);
        $this->assertEquals('Test', $decodedMessage->subject);
        $this->assertEquals(SENDGRID_TO, $decodedMessage->reply_to->email);
    }

    public function testSetGetTo()
    {
        $message = (new Message())->setTo(SENDGRID_TO);
        $this->assertEquals(SENDGRID_TO, $message->getTo()[0]->getEmail());
    }

    public function testSetGetFrom()
    {
        $message = (new Message())->setFrom(SENDGRID_FROM);
        $this->assertEquals(SENDGRID_FROM, $message->getFrom());
    }

    public function testSetGetSubject()
    {
        $message = (new Message())->setSubject('Test');
        $this->assertEquals('Test', $message->sendGridMessage->getGlobalSubject()->getSubject());
    }

    public function testAttachContent()
    {
        $message = (new Message())->attachContent(
            'test',
            [
                'fileName' => 'file.php',
                'contentType' => 'text/plain'
            ]
        );
        $attachments = $message->sendGridMessage->getAttachments();
        $this->assertEquals('file.php', $attachments[0]->getFilename());
        $this->assertEquals(
            'test',
            $attachments[0]->getContent()
        );
    }

    public function testSetGetHtmlBody()
    {
        $message = (new Message())->setHtmlBody('Test');
        $this->assertEquals('Test', $message->sendGridMessage->getContents()[0]->getValue());
    }

    public function testSetGetBcc()
    {
        $message = (new Message())->setBcc(SENDGRID_TO);
        $this->assertEquals(SENDGRID_TO, $message->getBcc()[0]->getEmail());
    }

    public function testEmbed()
    {
        $this->expectException('\yii\base\NotSupportedException');
        (new Message())->embed(null);
    }

    public function testSetGetTextBody()
    {
        $message = (new Message())->setTextBody('Test');
        $this->assertEquals('Test', $message->sendGridMessage->getContents()[0]->getValue());
    }

    public function testSendMessage()
    {
        $message = (new Message())
            ->setFrom(SENDGRID_FROM)
            ->setTo(SENDGRID_TO)
            ->setSubject('Test')
            ->setTextBody('Test text body');
        $this->assertTrue(Yii::$app->mailer->sendMessage($message));
    }

    public function testSendTemplate()
    {
        $message = (new Message())
            ->setSendGridSubstitution(SENDGRID_TEMPLATE, [
                ':testUserName' => 'John Smith',
                ':testMessage' => 'This test message'
            ])
            ->setSubject('Test')
            ->setFrom(SENDGRID_FROM)
            ->setTo(SENDGRID_TO);
        $this->assertTrue(Yii::$app->mailer->sendMessage($message));
    }

    public function testSendDynamicTemplate()
    {
        $message = (new Message())
            ->setSendGridSubstitution(SENDGRID_DYNAMIC_TEMPLATE, [
                'name' => 'John Smith',
                'messages' => [
                    [
                        'text'=> 'This test message #1',
                    ],
                    [
                        'text'=> 'This test message #2',
                    ],
                ]
            ])
            ->setSubject('Test')
            ->setFrom(SENDGRID_FROM)
            ->setTo(SENDGRID_TO);
        $this->assertTrue(Yii::$app->mailer->sendMessage($message));
    }
}
