<?php

namespace shershennm\sendgrid;

use yii\mail\BaseMessage;
use yii\helpers\BaseArrayHelper;

class Message extends BaseMessage
{
	/**
	 * @var \SendGrid\Email
	 */
	private $_sendGridMessage;

	public function getSendGridMessage()
	{
		if ($this->_sendGridMessage == null) {
			$this->_sendGridMessage = new \SendGrid\Email();
		}
		return $this->_sendGridMessage;
	}

	/**
	 * @param string $templateId sendGrid template id
	 * @param array [key => value] array for sendGrid substition
	 */
	public function setSendGridSubstitution($templateId, array $templateSubstitution = [])
	{

		$this->sendGridMessage->addFilter('templates', 'enabled', 1)
			->addFilter('templates', 'template_id', $templateId)
			->setSubstitutions($this->normalizeSubstitution($templateSubstitution));

		return $this;
	}

	/**
	 * @param  array $templateSubstitution [key => value]
	 * @return array [key => [value]] for substition
	 */
	private function normalizeSubstitution($templateSubstitution)
	{
		foreach ($templateSubstitution as $key => $value) {
			if (!is_array($value))
			{
				$templateSubstitution[$key] = [$value];
			}
		}

		return $templateSubstitution;
	}

	/**
	 * @inheritdoc
	 */
	public function getCharset()
	{
		// not available on sendgrid
	}

	/**
	 * @inheritdoc
	 */
	public function setCharset($charset)
	{
		// not available on sendgrid
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
		if (is_array($from) && BaseArrayHelper::isAssociative($from)) {
			$this->sendGridMessage->setFrom(key($from), current($from));
		}
		else
		{
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
		return $this->sendGridMessage->to;
	}

	/**
	 * @inheritdoc
	 */
	public function setTo($to)
	{
		$this->addEmailParam($to, 'to');

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getCc()
	{
		return $this->sendGridMessage->getCcs();
	}

	/**
	 * @inheritdoc
	 */
	public function setCc($cc)
	{
		$this->addEmailParam($cc, 'cc');

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getBcc()
	{
		return $this->sendGridMessage->getBccs();
	}

	/**
	 * @inheritdoc
	 */
	public function setBcc($bcc)
	{
		$this->addEmailParam($bcc, 'bcc');

		return $this;
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
		$this->sendGridMessage->setText($text);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setHtmlBody($html)
	{
		$this->sendGridMessage->setHtml($html);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function attach($fileName, array $options = [])
	{
		$this->sendGridMessage->addAttachment($fileName);

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function attachContent($content, array $options = [])
	{
		// no available method for sendgrid
	}

	/**
	 * @inheritdoc
	 */
	public function embed($fileName, array $options = [])
	{
		// no available method for sendgrid
	}

	/**
	 * @inheritdoc
	 */
	public function embedContent($content, array $options = [])
	{
		// no available method for sendgrid
	}

	/**
	 * @inheritdoc
	 */
	public function toString()
	{
		$string = '';
		foreach ($this->sendGridMessage->toWebFormat() as $key => $value) {
			$string .= sprintf("%s:%s\n", $key, $this->processWebFormatElement($value));
		}
		return $string;
	}

	/**
	 * @param  array|string $elValue
	 * @return string
	 */
	private function processWebFormatElement($elValue)
	{
		return is_array($elValue) ? implode(', ', $elValue) : $elValue;
	}

	/**
	 * Adding to sendgrid params which coontains email new items
	 * @param string|array $paramValue ['email' => 'name'] or ['email', ['email' => 'name'], 'email'] or 'email'
	 * @inheritdoc yii\mail\MessageInterface for more info
	 * @param string $paramType sendGrid var name like cc, bcc, to, from
	 * @return $this
	 */
	private function addEmailParam($paramValue, $paramType)
	{
		$paramTypeName = $paramType . 'Name';

		$this->sendGridMessage->$paramType = [];
		$this->sendGridMessage->$paramTypeName = [];

		if (!is_array($paramValue) || BaseArrayHelper::isAssociative($paramValue)) {
			$this->addSingleParam($paramValue, $paramType);
		} else {
			foreach ($paramValue as $value) {
				$this->addSingleParam($paramValue, $paramType);
			}
		}

		return $this;
	}
	
	/**
	 * @param string|array $paramValue ['email' => 'name'] or 'email'
	 * @param string $paramType sendGrid var name like cc, bcc, to
	 */
	private function addSingleParam($paramValue, $paramType)
	{
		$addFunction = 'add' . ucfirst($paramType);

		if (is_array($paramValue) && BaseArrayHelper::isAssociative($paramValue)) {
			$this->sendGridMessage->$addFunction(key($paramValue), current($paramValue));
		}
		else
		{
			$this->sendGridMessage->$addFunction($paramValue);
		}
	}
} 
