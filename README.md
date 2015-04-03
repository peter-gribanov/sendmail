# sendmail

Package for send mails.

## Examples

### Example 1 - send mail from mail() function

Отправка одного сообщения через PHP функцию [mail()](http://php.net/manual/en/book.mail.php)

```php
use Sendmail\Facade;

$facade = new Facade();

$facade->createSender('mail')
	->send($facade->createMessage('user@domain.ru', 'Заголовок', 'Текст сообщения'));
```

### Example 2 - send mail from SMTP

```php
use Sendmail\Facade;

$facade = new Facade();

// инициализируем объект для отправки через SMTP протокол
$sm = $facade->createCollection('smtp://username:password@server:port')
	// устанавливаем кодировку
	->setCharset('koi8-r')
	// устанавливаем E-mail адрес отправителя и его имя 
	->setFrom('sender@domain.ru', 'Sender')
	// добавляем в очередь письма
	->add($facade->createMessage('user1@domain.ru', 'Заголовок 1', 'Текст сообщения 1'))
	->add($facade->createMessage('user2@domain.ru', 'Заголовок 2', 'Текст сообщения 2'));


// проходим по очереди сообщений
while ($sm->valid()){
	// отправляем текущее сообщение
	$result = $sm->send();
	// выводим результат отправки
	var_dump($result);

	// произошла ошибка при отправке
	if (!$result){
		// выводим текст ошибки
		echo $sm->getSender()->errstr;
		break;
	}
	// выводим текст диалога с сервером
	echo '<pre>'.$sm->getSender()->getLog().'</pre>';
	// переход к следующему элименту
	$sm->next();
}

// очищаем очередь
$sm->clear();
```


### Example 2 - send mail from SMTP

```php
use Sendmail\Facade;
use Sendmail\Message;

$facade = new Facade();
$message = new Message();

// инициализируем объект для отправки через PHP функцию mail()
$sm = $facade->createCollection('mail')
	// добавляем в очередь письмо адресованое нескольким получателям
	->notification(array(
		'user1@domain.ru',
		'user2@domain.ru',
		'user3@domain.ru'
	), $message
		->setSubject('Example subject')
		->setMessage('<b>Test message.<b><br />You can remove this message.')
		// устанавливаем адрес отправителя 
		->setFrom('sender@domain.ru')
		// отправлять письмо в формате HTML
		->inHTML());

// отправляет все сообщения в очереди
$sm->sendAll();
// очищаем очередь
$sm->clear();
```
