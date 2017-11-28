sendmail
========

[![Latest Stable Version](https://img.shields.io/packagist/v/gribanov/sendmail.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gribanov/sendmail)
[![Total Downloads](https://img.shields.io/packagist/dt/gribanov/sendmail.svg?maxAge=3600)](https://packagist.org/packages/gribanov/sendmail)
[![Build Status](https://img.shields.io/travis/peter-gribanov/sendmail.svg?maxAge=3600)](https://travis-ci.org/peter-gribanov/sendmail)
[![Coverage Status](https://img.shields.io/coveralls/peter-gribanov/sendmail.svg?maxAge=3600)](https://coveralls.io/github/peter-gribanov/sendmail?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/peter-gribanov/sendmail.svg?maxAge=3600)](https://scrutinizer-ci.com/g/peter-gribanov/sendmail/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/0393f547-c429-47ef-8255-4607d6e40231.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/0393f547-c429-47ef-8255-4607d6e40231)
[![StyleCI](https://styleci.io/repos/33310622/shield?branch=master)](https://styleci.io/repos/33310622)
[![License](https://img.shields.io/packagist/l/gribanov/sendmail.svg?maxAge=3600)](https://github.com/peter-gribanov/sendmail)

Package for send mails.

Examples
--------

### Send mail from mail() function

Send one message by the PHP function [mail()](http://php.net/manual/en/book.mail.php)

```php
use Sendmail\Message;
use Sendmail\Sender\Mail;

$message = new Message();
$message
    ->setTo('user@example.com')
    ->setSubject('Example subject')
    ->setText('Example message');
$sender = new Mail();
$sender->send($message);
```

### Send mail from SMTP

Connect to SMTP server and push mails into him

```php
use Sendmail\Queue;
use Sendmail\Message;
use Sendmail\Sender\Smtp;
use Sendmail\Sender\Smtp\Exception;

$message1 = new Message();
$message1
    ->setTo('user1@example.com')
    ->setSubject('Example subject 1')
    ->setText('Example message 1')
    // email of the sender
    ->setFrom('sender@example.com', 'Sender');

$message2 = clone $message1;
$message2
    ->setTo('user2@example.com')
    ->setSubject('Example subject 2')
    ->setText('Example message 2');

// sending messages to the queue via a direct connection to the SMTP server
$queue = new Queue(new Smtp('example.com', 25, 'username', 'password'));
$queue
    ->add($message1)
    ->add($message2);

try {
    // send all messages
    var_dump($queue->send());
} catch (Exception $e) {
    // SMTP dialogue
    echo $e->getDialogue()->getLog();
}

$queue->clear();
```


### Creation mailing list

```php
use Sendmail\Queue;
use Sendmail\Message;
use Sendmail\Sender\Mail;

$message = new Message();
$message
    ->setSubject('Example subject')
    ->setText('<h1>Example message.<h1><p>You can remove this message.</p>')
    // email of the sender
    ->setFrom('sender@example.com')
    // send email in HTML format
    ->inHTML();

$queue = new Queue(new Mail());
// add to queue a letter addressed to multiple recipients
$queue->notify(
    array(
        'user1@example.com',
        'user2@example.com',
        'user3@example.com'
    ),
    $message
);

$queue->send();
$queue->clear();
```
