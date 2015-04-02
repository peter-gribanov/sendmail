<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail;

use Sendmail\Sender\SenderInterface;
use Sendmail\Message;

/**
 * Очередь E-Mail сообщений
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Collection implements \Iterator, \Countable
{
    /**
     * Кодировка по умолчанию
     *
     * @var integer
     */
    const DEFAULT_CHARSET = 'utf-8';

    /**
     * Объект еще не создан
     *
     * @var integer
     */
    const STATUS_NOT_INIT = 0;

    /**
     * Создан, готов к работе
     *
     * @var integer
     */
    const STATUS_READY = 1;

    /**
     * Отправляет сообщение
     *
     * @var integer
     */
    const STATUS_SENDS = 2;

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
     *
     * @var integer
     */
    private $status = self::STATUS_NOT_INIT;

    /**
     * Объект для отправки сообщений
     *
     * @var \Sendmail\Sender\SenderInterface
     */
    private $sender;

    /**
     * Кодировка отправляемых писем
     *
     * @var string
     */
    private $charset = self::DEFAULT_CHARSET;

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
     * @param \Sendmail\Sender\SenderInterface $sender
     */
    protected function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
        $this->status = self::STATUS_READY;
    }

    /**
     * Создает объект коллекции
     * 
     * @param \Sendmail\Sender\SenderInterface $sender
     *
     * @return \Sendmail\Collection
     */
    public static function create(SenderInterface $sender)
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
     * @return boolean
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
     * @return \Sendmail\Collection
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
     * @param \Sendmail\Message $message
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
     * @param array $recipients
     * @param \Sendmail\Message $message
     *
     * @return \Sendmail\Collection
     */
    public function notification(array $recipients, Message $message)
    {
        $message
            ->setCharset($this->charset)
            ->setFrom($this->from, $this->from_name);

        // дублирование сообщения с разными получателями
        foreach ($recipients as $recipient) {
            $this->messages[] = $message->setTo($recipient);
        }
        return $this;
    }

    /**
     * Устанавливает отправителя
     *
     * @param string $from
     * @param string $name
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
     * @param string $charset
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
     * @return \Sendmail\Sender\SenderInterface
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Отправляет письмо текущее в коллекции
     * 
     * @return boolean
     */
    public function send()
    {
        // нет сообщения
        if (!$this->valid()) {
            return false;
        }

        // ждем освобождения очереди
        while ($this->status != self::STATUS_READY) {}

        // устанавливаем метку, что объект начал отправлять сообщение
        $this->status = self::STATUS_SENDS;

        // получаем письмо и отправляем его
        $result = $this->sender->send($this->current());

        // объект освободился и снова готов к работе
        $this->status = self::STATUS_READY;

        return $result;
    }

    /**
     * Отправляет все письма в коллекции
     *
     * @return boolean
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
