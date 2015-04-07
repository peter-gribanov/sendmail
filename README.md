# sendmail

Package for send mails.

## Examples

### Example 1 - send mail from mail() function

Отправка одного сообщения через PHP функцию [mail()](http://php.net/manual/en/book.mail.php)

```php
use Sendmail\Sender\Mail;

$message = new Message();
$message
	->setTo('user@example.com')
	->setSubject('Заголовок')
	->setMessage('Текст сообщения');
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
	->setSubject('Заголовок 1')
	->setMessage('Текст сообщения 1')
	->setFrom('sender@example.com', 'Sender');
$message2 = new Message();
$message2
	->setTo('user2@example.com')
	->setSubject('Заголовок 2')
	->setMessage('Текст сообщения 2')
	->setFrom('sender@example.com', 'Sender');

// отправка сообщений в очереди через прямое соединенияе с SMTP сервером
$queue = new Queue(new Smtp('example.com', 25, 'username', 'password'));
$queue->add(message1)->add(message2);

try {
	// отправляем все сообщения в очереди
	var_dump($queue->send());
} catch (Exception $e) {
	// вывод текста диалога с сервером
	echo $e->getDialogue()->getLog();
}

// очищаем очередь
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
	->setMessage('<b>Test message.<b><br />You can remove this message.')
	// устанавливаем адрес отправителя
	->setFrom('sender@example.com')
	// отправлять письмо в формате HTML
	->inHTML();

// инициализируем объект для отправки через PHP функцию mail()
$queue = new Queue(new Mail());
// добавляем в очередь письмо адресованое нескольким получателям
$queue
	->notify(
		array(
			'user1@example.com',
			'user2@example.com',
			'user3@example.com'
		),
		$message
	);

// отправляет все сообщения в очереди
$queue->send();
// очищаем очередь
$queue->clear();
```
