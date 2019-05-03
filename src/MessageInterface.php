<?php

namespace shershennm\sendgrid;

interface MessageInterface extends \yii\mail\MessageInterface
{
    /**
     * @return string|null
     */
    public function getHtmlBody();

    /**
     * @return string|null
     */
    public function getTextBody();

    /**
     * @inheritdoc
     */
    public function getTemplateId();

    /**
     * @inheritdoc
     */
    public function getSubstitutions();
}
