# sendmail

Package for send mails.

## Examples

### Example 1 - send mail from mail() function

Send one message by the PHP function [mail()](http://php.net/manual/en/book.mail.php)

```php
use Sendmail\Message;
use Sendmail\Sender\Mail;

$message = new Message();
$message
	->setTo('user@example.com')
	->setSubject('Example subject')
	->setMessage('Example message');
$sender = new Mail();
$sender->send($message);
```

### Example 2 - send mail from SMTP

```php
use Sendmail\Queue;
use Sendmail\Message;
use Sendmail\Sender\Smtp;
use Sendmail\Sender\Smtp\Exception;

$message1 = new Message();
$message1
	->setTo('user1@example.com')
	->setSubject('Example subject 1')
	->setMessage('Example message 1')
	->setFrom('sender@example.com', 'Sender');
$message2 = new Message();
$message2
	->setTo('user2@example.com')
	->setSubject('Example subject 2')
	->setMessage('Example message 2')
	->setFrom('sender@example.com', 'Sender');

// sending messages to the queue via a direct connection to the SMTP server
$queue = new Queue(new Smtp('example.com', 25, 'username', 'password'));
$queue->add(message1)->add(message2);

try {
	// send all messages
	var_dump($queue->send());
} catch (Exception $e) {
	// SMTP dialogue
	echo $e->getDialogue()->getLog();
}

$queue->clear();
```


### Example 2 - send mail from SMTP

```php
use Sendmail\Queue;
use Sendmail\Message;
use Sendmail\Sender\Mail;

$message = new Message();
$message
	->setSubject('Example subject')
	->setMessage('<b>Example message.<b><br />You can remove this message.')
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
