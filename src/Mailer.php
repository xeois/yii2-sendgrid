<?php

namespace shershennm\sendgrid;

use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;
use yii\mail\MailerInterface;

/**
 * Mailer implements a mailer based on SendGrid.
 *
 * To use Mailer, you should configure it in the application configuration. See README for more information.
 *
 * @see http://sendgrid.com/
 * @package shershennm\sendgrid
 * @property \SendGrid $sendGridMailer
 */
class Mailer extends BaseMailer implements MailerInterface
{
    /**
     * @var string message default class name.
     */
    public $messageClass = 'shershennm\sendgrid\Message';

    /**
     * @var string key for the sendgrid api
     */
    public $apiKey;

    /**
     * @var array a list of options for the sendgrid api
     */
    public $options = [];

    /**
     * @var string Send grid mailer instance
     */
    private $_sendGridMailer;

    /**
     * @return \SendGrid Send grid mailer instance
     * @throws InvalidConfigException
     */
    public function getSendGridMailer()
    {
        if (!is_object($this->_sendGridMailer)) {
            $this->_sendGridMailer = $this->createSendGridMailer();
        }

        return $this->_sendGridMailer;
    }

    /**
     * Create send grid mail instance with stored params
     * @return \SendGrid
     * @throws \yii\base\InvalidConfigException
     */
    public function createSendGridMailer()
    {
        if ($this->apiKey) {
            return new \SendGrid($this->apiKey, $this->options);
        } else {
            throw new InvalidConfigException("You must configure mailer.");
        }
    }

    /**
     * @param Message $message
     * @return bool
     * @throws \Exception
     */
    public function sendMessage($message)
    {
        $response = $this->sendGridMailer->send($message->sendGridMessage);

        if ($response->statusCode() >= 400) {
            throw new SendgridException(sprintf(
                'Sendgrid returned %d with "%s" error',
                $response->statusCode(),
                $response->body()
            ));
        }

        return true;
    }
}
