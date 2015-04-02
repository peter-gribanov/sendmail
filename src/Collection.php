<?php
/**
 * SendMail package
 *
 * @package   SendMail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail;

use Sendmail\Sender;
use Sendmail\Message;

/**
 * Очередь E-Mail сообщений
 *
 * @package SendMail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Collection implements \Iterator, \Countable
{
    /**
     * Внутренний указатель
     *
     * @var integer
     */
    private $position = 0;

    /**
     * Список сообщений
     *
     * @var array
     */
    private $messages = array();

    /**
     * Текущий статус объекта
     * 0 - объект еще не создан
     * 1 - создан, готов к работе
     * 2 - отправляет сообщение
     *
     * @var integer
     */
    private $status = 0;

    /**
     * Объект для отправки сообщений
     *
     * @var \Sendmail\Sender
     */
    private $sender;

    /**
     * Кодировка отправляемых писем
     * По умолчанию windows-1251
     *
     * @var string
     */
    private $charset = 'windows-1251';

    /**
     * E-Mail отправителя
     *
     * @var string
     */
    private $from = '';

    /**
     * Имя отправителя
     *
     * @var string
     */
    private $from_name = '';


    /**
     * Конструктор
     *
     * @param \Sendmail\Sender
     */
    protected function __construct(Sender $sender)
    {
        $this->sender = $sender;
        $this->status = 1;
    }

    /**
     * Создает объект коллекции
     * 
     * @param \Sendmail\Sender
     *
     * @return \Sendmail\Collection
     */
    public static function create(Sender $sender)
    {
        return new self($sender);
    }
    
    /**
     * Возвращает текущее сообщение
     *
     * @return \Sendmail\Message
     */
    public function current()
    {
        return $this->messages[$this->position];
    }

    /**
     * Передвигает внутренний указатель вперёд
     *
     * @return \Sendmail\Collection
     */
    public function next()
    {
        ++$this->position;
        return $this;
    }

    /**
     * Возвращает внутренний указатель
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Проверяет, существует ли сообщение c данным указателем
     *
     * @return boolen
     */
    public function valid()
    {
        return isset($this->messages[$this->position]);
    }

    /**
     * Устанавливает внутренний указатель на первый элемент
     *
     * @return \Sendmail\Collection
     */
    public function rewind()
    {
        $this->position = 0;
        return $this;
    }

    /**
     * Возвращает количество сообщений
     *
     * @return integer
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Очищает список сообщений
     *
     * @return Collection
     */
    public function clear()
    {
        unset($this->messages);
        $this->messages = array();
        return $this;
    }

    /**
     * Добовляет сообщение в очередь
     *
     * @param \Sendmail\Message
     *
     * @return \Sendmail\Collection
     */
    public function add(Message $message)
    {
        $this->messages[] = $message
            ->setCharset($this->charset)
            ->setFrom($this->from, $this->from_name);
        return $this;
    }

    /**
     * Добовляет в очередь сообщение адресованное списку пользователей
     *
     * @param array
     * @param \Sendmail\Message
     *
     * @return \Sendmail\Collection
     */
    public function notification($recipients, Message $message)
    {
        $message
            ->setCharset($this->charset)
            ->setFrom($this->from, $this->from_name);

        if (is_array($recipients) && $recipients) {
            // дублирование сообщения с разными получателями
            foreach ($recipients as $recipient) {
                $this->messages[] = $message->setTo($recipient);
            }
        }
        return $this;
    }

    /**
     * Устанавливает отправителя
     *
     * @param string
     * @param string
     *
     * @return \Sendmail\Collection
     */
    public function setFrom($from, $name = '')
    {
        $this->from = $from;
        $this->from_name = $name;
        return $this;
    }

    /**
     * Устанавливает кодировку отправляемых писем
     *
     * @param string
     *
     * @return \Sendmail\Collection
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Возвращает объект отправителя
     *
     * @return \Sendmail\Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Отправляет письмо текущее в коллекции
     * 
     * @return boolen
     */
    public function send()
    {
        // нет сообщения
        if (!$this->valid()) {
            return false;
        }

        // ждем освобождения очереди
        while ($this->status != 1) {
            sleep(1);
        }

        // устанавливаем метку, что объект начал отправлять сообщение
        $this->status = 2;

        // получаем письмо и отправляем его
        $result = $this->sender->send($this->current());

        // объект освободился и снова готов к работе
        $this->status = 1;

        return $result;
    }

    /**
     * Отправляет все письма в коллекции
     *
     * @return boolen
     */
    public function sendAll()
    {
        while ($this->valid()) {
            if (!$this->send()) {
                return false;
            }
            $this->next();
        }
        return true;
    }
}
