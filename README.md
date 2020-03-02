Yii 2 shershennm SendGrid
=========================
Sendgrid Mailer for Yii 2

> forked from [bryglen/yii2-sendgrid](https://github.com/bryglen/yii2-sendgrid.git)

**3.1.0 update** 

Methods which returned `\SendGrid\Mail\EmailAddress` or array with this type, now returns plain email string or `['email' => 'name']` array or array with this values.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xeois/yii2-sendgrid "~4"
```

or add

```
"xeois/yii2-sendgrid": "~4"
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
		'class' => 'xeois\sendgrid\Mailer',
		'apiKey' => 'your api key',
	]
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
	//also you can use sendgrid substitutions
	->setSendGridSubstitution('template id', [
		':var1' => 'var1value',
		':var2' => 'var2value',
	]);
```
