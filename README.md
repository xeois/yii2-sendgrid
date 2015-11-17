Yii 2 shershennm SendGrid **forked from bryglen/yii2-sendgrid**
==============
Sendgrid Mailer for Yii 2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist shershennm/yii2-sendgrid "*"
```

or add

```
"shershennm/yii2-sendgrid": "1.1.2"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

To use Mailer, you should configure it in the application configuration like the following,

```php
'components' => [
	...
	'sendGrid' => [
		'class' => 'shershennm\sendgrid\Mailer',
		'username' => 'your_user_name',
		'password' => 'your password here',
		//'viewPath' => '@app/views/mail', // your view path here
	],
	...
],
```

To send an email, you may use the following code:

```php
$sendGrid = Yii::$app->sendGrid;
$message = $sendGrid->compose('contact/html', ['contactForm' => $form])
$message->setFrom('from@domain.com')
	->setTo($form->email)
	->setSubject($form->subject)
	->send($sendGrid);
```