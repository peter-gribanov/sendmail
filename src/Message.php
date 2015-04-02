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

/**
 * Класс описывающий сообщение
 *
 * @package SendMail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Message
{
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
     * E-mail получателя
     *
     * @var string
     */
    private $to = '';

    /**
     * Заголовок
     *
     * @var string
     */
    private $subject = '';

    /**
     * Текст сообщение
     *
     * @var string
     */
    private $message = '';

    /**
     * Собщение в формате HTML
     *
     * @var boolen
     */
    private $in_html = false;


    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->from = '<admin@'.$_SERVER['HTTP_HOST'].'>';
        $this->from_name = 'Administration';
    }

    /**
     * Создает экземпляр класса
     *
     * @return \Sendmail\Message
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Устанавливает кодировку отправляемых писем
     *
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function setCharset($charset)
    {
        if (!is_string($charset) || !trim($charset)) {
            throw new \InvalidArgumentException('Incorrect message charset.');
        }
        $this->charset = $charset;
        return $this;
    }

    /**
     * Устанавливает E-mail и имя отправителя
     * 
     * @param string
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function setFrom($from, $from_name = '')
    {
        if (!is_string($from) || !trim($from) || !is_string($from_name)) {
            throw new \InvalidArgumentException('Sender E-Mail must not be empty.');
        }
        $this->from = $from;
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * Возвращает E-mail отправителя
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Устанавливает E-mail получателя
     *
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function setTo($to)
    {
        if (!is_string($to) || !trim($to)) {
            throw new \InvalidArgumentException('Recipient E-Mail must not be empty.');
        }
        $this->to = $to;
        return $this;
    }

    /**
     * Возвращает E-mail получателя
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Устанавливает заголовок сообщения
     *
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function setSubject($subject)
    {
        if (!is_string($subject) || !trim($subject)) {
            throw new \InvalidArgumentException('Mail subject must not be empty.');
        }
        $this->subject = $subject;
        return $this;
    }

    /**
     * Возвращает заголовок сообщения
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Устанавливает тело сообщения
     *
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function setMessage($message)
    {
        if (!is_string($message) || !trim($message)) {
            throw new \InvalidArgumentException('Mail message must not be empty.');
        }
        $this->message = $message;
        return $this;
    }

    /**
     * Возвращает тело сообщения
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Устанавливает что сообщение надо отправлять в формате HTML
     *
     * @return \Sendmail\Message
     */
    public function inHTML()
    {
        $this->in_html = true;
        return $this;
    }

    /**
     * Составляет заголовки и возвращает их
     *
     * @return string
     */
    public function getHeaders()
    {
        $conttype = 'Content-type: text/'.($this->in_html ? 'html' : 'plain')
            .'; charset="'.$this->charset."\"\r\n";

        // составление заголовков
        return $conttype.'Subject: '.$this->subject."\r\n"
            . 'MIME-Version: 1.0'."\r\n"
            . $conttype
            . 'To: '.$this->to."\r\n"
            . 'From: '.$this->from_name.' <'.$this->from.'>'."\r\n"
            . 'X-Sender: '.$_SERVER['HTTP_HOST']."\r\n"
            . 'X-Mailer: PHP/'.PHP_VERSION."\r\n";
    }
}
