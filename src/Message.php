<?php

namespace shershennm\sendgrid;

use SendGrid\Mail\Mail;
use SendGrid\Mail\MimeType;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\mail\BaseMessage;
use yii\mail\MessageInterface;

/**
 * Class Message
 * @package shershennm\sendgrid
 * @property Mail $sendGridMessage
 */
class Message extends BaseMessage implements MessageInterface
{
	/**
	 * @var Mail
	 */
	private $_sendGridMessage;

    /**
     * @return Mail
     */
	public function getSendGridMessage()
	{
		if ($this->_sendGridMessage == null) {
			$this->_sendGridMessage = new Mail();
		}
		return $this->_sendGridMessage;
	}

    /**
     * @param string $templateId sendGrid template id
     * @param array [key => value] array for sendGrid substitution
     * @return Message
     */
	public function setSendGridSubstitution($templateId, array $templateSubstitution = [])
	{

		$this->sendGridMessage->setTemplateId($templateId);
		$this->sendGridMessage->addSubstitutions($templateSubstitution);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getFrom()
	{
		return $this->sendGridMessage->getFrom();
	}

	/**
	 * @inheritdoc
	 */
	public function setFrom($from)
	{
		if (is_array($from)) {
			$this->sendGridMessage->setFrom(key($from), current($from));
		} else {
			$this->sendGridMessage->setFrom($from);
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getReplyTo()
	{
		return $this->sendGridMessage->getReplyTo();
	}

	/**
	 * @inheritdoc
	 */
	public function setReplyTo($replyTo)
	{
		$this->sendGridMessage->setReplyTo($replyTo);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getTo()
	{
		return $this->getPersonalizationParams('to');
	}

	/**
	 * @inheritdoc
	 */
	public function setTo($to)
	{
        return $this->setPersonalizationParams('to', $to);
	}

	/**
	 * @inheritdoc
	 */
	public function getCc()
	{
        return $this->getPersonalizationParams('cc');
	}

	/**
	 * @inheritdoc
	 */
	public function setCc($cc)
	{
        return $this->setPersonalizationParams('cc', $cc);
	}

	/**
	 * @inheritdoc
	 */
	public function getBcc()
	{
        return $this->getPersonalizationParams('bcc');
	}

	/**
	 * @inheritdoc
	 */
	public function setBcc($bcc)
	{
        return $this->setPersonalizationParams('bcc', $bcc);
	}

	/**
	 * @inheritdoc
	 */
	public function getSubject()
	{
		return $this->sendGridMessage->getSubject();
	}

	/**
	 * @inheritdoc
	 */
	public function setSubject($subject)
	{
		$this->sendGridMessage->setSubject($subject);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setTextBody($text)
	{
		$this->sendGridMessage->addContent(MimeType::TEXT, $text);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setHtmlBody($html)
	{
        $this->sendGridMessage->addContent(MimeType::HTML, $html);

		return $this;
	}

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
	public function attach($fileName, array $options = [])
	{
		$this->sendGridMessage->addAttachment(
		    base64_encode(file_get_contents($fileName)),
            isset($options['contentType']) ? $options['contentType'] : FileHelper::getMimeType($fileName),
            isset($options['fileName']) ? $options['fileName'] : basename($fileName)
        );

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function attachContent($content, array $options = [])
	{
        $this->sendGridMessage->addAttachment(
            $content,
            isset($options['contentType']) ? $options['contentType'] : null,
            isset($options['fileName']) ? $options['fileName'] : null
        );

        return $this;
	}

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
	public function embed($fileName, array $options = [])
	{
		throw new NotSupportedException('No available method for sendgrid!');
	}

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
	public function embedContent($content, array $options = [])
	{
        throw new NotSupportedException('No available method for sendgrid!');
	}

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public function getCharset()
    {
        throw new NotSupportedException('Content and subject must be in UTF charset!');
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public function setCharset($charset)
    {
        throw new NotSupportedException('Content and subject must be in UTF charset!');
    }

    /**
	 * @inheritdoc
	 */
	public function toString()
	{
		return Json::encode($this->sendGridMessage->jsonSerialize());
	}

    /**
     * @param $personalization string
     * @return array
     */
	protected function getPersonalizationParams($personalization)
    {
        return array_merge(... ArrayHelper::getColumn($this->sendGridMessage->getPersonalizations(), $personalization . 's'));
    }

    /**
     * @param $personalization string
     * @param $emails array|string
     * @return $this
     */
	protected function setPersonalizationParams($personalization, $emails)
    {
        $addMethod = 'add' . ucfirst($personalization);

        if (is_array($emails)) {
            foreach ($emails as $email => $name) {
                if (is_string($email)) {
                    $this->sendGridMessage->{$addMethod}($email, $name);
                } else {
                    $this->sendGridMessage->{$addMethod}($name);
                }
            }
        } else {
            $this->sendGridMessage->{$addMethod}($emails);
        }

        return $this;
    }
}
