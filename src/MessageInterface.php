<?php

namespace shershennm\sendgrid;

interface MessageInterface extends \yii\mail\MessageInterface
{
    public function getHtmlBody();

    public function getTextBody();

    public function getTemplateId();

    public function getSubstitutions($index = 0);
}
