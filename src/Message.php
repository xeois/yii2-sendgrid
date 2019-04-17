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
class Message extends BaseMessage
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
     * @inheritdoc
     */
    public function getFrom()
    {
        /** @var From $from */
        $from = $this->sendGridMessage->getFrom();

        if (!$from) {
            return null;
        }

        if ($from->getName()) {
            return [$from->getEmail() => $from->getName()];
        }

        return $from->getEmail();
    }

    /**
     * @inheritdoc
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
     */
    public function getCharset()
    {
        return 'UTF-8';
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public function setCharset($charset)
    {
        if (strtoupper($charset) !== 'UTF-8') {
            throw new NotSupportedException('Content and subject must be in UTF-8 charset!');
        }
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
        $params = [];

        foreach ($this->sendGridMessage->getPersonalizations() as $sendGridPersonalization) {
            $value = $sendGridPersonalization->{'get' . ucfirst($personalization . 's')}();

            if ($value) {
                $params = array_merge($params, is_array($value) ? $value : [$value]);
            }
        }

        return $params;
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

    /**
     * @inheritdoc
     */
    public function getContents($type = null)
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
     * @inheritdoc
     */
    public function setTemplateId($templateId)
    {
        return $this->sendGridMessage->setTemplateId($templateId);
    }

    /**
     * @inheritdoc
     */
    public function getTextBody()
    {
        return $this->getContents(MimeType::TEXT);
    }

    /**
     * @inheritdoc
     */
    public function getHtmlBody()
    {
        return $this->getContents(MimeType::HTML);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId()
    {
        return $this->sendGridMessage->getTemplateId();
    }

    /**
     * @param int $index
     * @return array
     */
    public function getSubstitutionByIndex($index = 0)
    {
        return $this->sendGridMessage->getSubstitutions($index);
    }
}
