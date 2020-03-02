<?php

namespace xeois\sendgrid;

use yii\base\Exception;

class SendgridException extends Exception
{
    public function getName()
    {
        return 'SendGrid Client Exception';
    }
}
