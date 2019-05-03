<?php

namespace shershennm\sendgrid;

use SendGrid\Mail\From;
use SendGrid\Mail\Mail;
use SendGrid\Mail\MimeType;
use SendGrid\Mail\Substitution;
use yii\base\NotSupportedException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\mail\BaseMessage;

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
     * @param $templateId
     * @param array $templateSubstitution
     * @return $this
     */
    public function setSendGridSubstitution($templateId, array $templateSubstitution = [])
    {
        $this->setTemplateId($templateId);
        $this->sendGridMessage->addSubstitutions($this->normalizeSubstitutions($templateSubstitution));

        return $this;
    }

    /**
     * @param array $templateSubstitution
     * @return array
     */
    private function normalizeSubstitutions(array $templateSubstitution = [])
    {
        $substitutions = [];

        foreach ($templateSubstitution as $key => $value) {
            $substitutions[] = new Substitution($key, $value);
        }

        return $substitutions;
    }

    /**
     * @return array|string|null
     */
    public function getFrom()
    {
        /** @var From $from */
        return $this->extractEmail($this->sendGridMessage->getFrom());
    }

    /**
     * @param array|string $from
     * @return $this|MessageInterface|BaseMessage
     * @throws \SendGrid\Mail\TypeException
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
     * @return array|string|null
     */
    public function getReplyTo()
    {
        return $this->extractEmail($this->sendGridMessage->getReplyTo());
    }

    /**
     * @param array|string $replyTo
     * @return $this|MessageInterface|BaseMessage
     */
    public function setReplyTo($replyTo)
    {
        $this->sendGridMessage->setReplyTo($replyTo);

        return $this;
    }

    /**
     * @return \SendGrid\Mail\To[]|[]
     */
    public function getTo()
    {
        return $this->getPersonalizationParams('to');
    }

    /**
     * @param array|string $to
     * @return Message|MessageInterface|BaseMessage
     */
    public function setTo($to)
    {
        return $this->setPersonalizationParams('to', $to);
    }

    /**
     * @return \SendGrid\Mail\Cc[]|[]
     */
    public function getCc()
    {
        return $this->getPersonalizationParams('cc');
    }

    /**
     * @param array|string $cc
     * @return Message|MessageInterface|BaseMessage
     */
    public function setCc($cc)
    {
        return $this->setPersonalizationParams('cc', $cc);
    }

    /**
     * @return \SendGrid\Mail\Bcc[]|[]
     */
    public function getBcc()
    {
        return $this->getPersonalizationParams('bcc');
    }

    /**
     * @param array|string $bcc
     * @return Message|MessageInterface|BaseMessage
     */
    public function setBcc($bcc)
    {
        return $this->setPersonalizationParams('bcc', $bcc);
    }

    /**
     * @return string|null
     */
    public function getSubject()
    {
        $subject = $this->sendGridMessage->getGlobalSubject();

        return $subject ? $subject->getSubject() : null;
    }

    /**
     * @param string $subject
     * @return $this|MessageInterface|BaseMessage
     */
    public function setSubject($subject)
    {
        $this->sendGridMessage->setSubject($subject);

        return $this;
    }

    /**
     * @param string $text
     * @return $this|MessageInterface|BaseMessage
     */
    public function setTextBody($text)
    {
        $this->sendGridMessage->addContent(MimeType::TEXT, $text);

        return $this;
    }

    /**
     * @param string $html
     * @return $this|MessageInterface|BaseMessage
     */
    public function setHtmlBody($html)
    {
        $this->sendGridMessage->addContent(MimeType::HTML, $html);

        return $this;
    }

    /**
     * @param string $fileName
     * @param array $options
     * @return $this|MessageInterface|BaseMessage
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
     * @param string $content
     * @param array $options
     * @return $this|MessageInterface|BaseMessage
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
     * @param string $fileName
     * @param array $options
     * @return string|void
     * @throws NotSupportedException
     */
    public function embed($fileName, array $options = [])
    {
        throw new NotSupportedException('No available method for sendgrid!');
    }

    /**
     * @param string $content
     * @param array $options
     * @return string|void
     * @throws NotSupportedException
     */
    public function embedContent($content, array $options = [])
    {
        throw new NotSupportedException('No available method for sendgrid!');
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }

    /**
     * @param string $charset
     * @return $this|MessageInterface|BaseMessage
     * @throws NotSupportedException
     */
    public function setCharset($charset)
    {
        if (strtoupper($charset) !== 'UTF-8') {
            throw new NotSupportedException('Content and subject must be in UTF-8 charset!');
        }

        return $this;
    }

    /**
     * @return string
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
        $params = [];

        foreach ($this->sendGridMessage->getPersonalizations() as $sendGridPersonalization) {
            $value = $sendGridPersonalization->{sprintf('get%ss', ucfirst($personalization))}();

            if ($value) {
                $params = array_merge($params, is_array($value) ? $value : [$value]);
            }
        }

        return $params;
    }

    /**
     * @param $personalization
     * @param $emails
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

    /**
     * @param null $type string
     * @return \SendGrid\Mail\Content[]|string|null
     */
    protected function getContents($type = null)
    {
        $contents = $this->sendGridMessage->getContents();

        if (!$type) {
            return $contents;
        }

        if ($contents && count($contents)) {
            foreach ($contents as $content) {
                /** @var \SendGrid\Mail\Content $content */
                if ($content->getType() === $type) {
                    return $content->getValue();
                }
            }
        }

        return null;
    }

    /**
     * @param $templateId string
     * @return $this
     */
    public function setTemplateId($templateId)
    {
       $this->sendGridMessage->setTemplateId($templateId);

       return $this;
    }

    /**
     * @return string|null
     */
    public function getTextBody()
    {
        return $this->getContents(MimeType::TEXT);
    }

    /**
     * @return string|null
     */
    public function getHtmlBody()
    {
        return $this->getContents(MimeType::HTML);
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->sendGridMessage->getTemplateId()->getTemplateId();
    }

    /**
     * @param int $index
     * @return Substitution[]
     */
    public function getSubstitutions($index = 0)
    {
        return $this->sendGridMessage->getSubstitutions($index);
    }

    /**
     * @param \SendGrid\Mail\EmailAddress $emailAddress
     * @return array|string|null
     */
    protected function extractEmail($emailAddress)
    {
        if (!$emailAddress) {
            return null;
        }

        if ($emailAddress->getName()) {
            return [$emailAddress->getEmail() => $emailAddress->getName()];
        }

        return $emailAddress->getEmail();
    }
}
