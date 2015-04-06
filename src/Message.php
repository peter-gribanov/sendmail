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

/**
 * Класс описывающий сообщение
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Message
{
    /**
     * Кодировка по умолчанию
     *
     * @var string
     */
    const DEFAULT_CHARSET = 'utf-8';

    /**
     * Кодировка отправляемых писем
     *
     * @var string
     */
    protected $charset = self::DEFAULT_CHARSET;

    /**
     * E-Mail отправителя
     *
     * @var string
     */
    protected $from = '';

    /**
     * Имя отправителя
     *
     * @var string
     */
    protected $from_name = '';

    /**
     * E-mail получателя
     *
     * @var string
     */
    protected $to = '';

    /**
     * Заголовок
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Текст сообщение
     *
     * @var string
     */
    protected $message = '';

    /**
     * Собщение в формате HTML
     *
     * @var boolean
     */
    protected $in_html = false;


    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->from = '<admin@'.$_SERVER['HTTP_HOST'].'>';
        $this->from_name = 'Administration';
    }

    /**
     * Устанавливает кодировку отправляемых писем
     *
     * @param string $charset
     *
     * @return \Sendmail\Message
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Устанавливает E-mail и имя отправителя
     * 
     * @param string $from
     * @param string $from_name
     *
     * @return \Sendmail\Message
     */
    public function setFrom($from, $from_name = '')
    {
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
     * @param string $to
     *
     * @return \Sendmail\Message
     */
    public function setTo($to)
    {
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
     * @param string $subject
     *
     * @return \Sendmail\Message
     */
    public function setSubject($subject)
    {
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
     * @param string $message
     *
     * @return \Sendmail\Message
     */
    public function setMessage($message)
    {
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
        $from_name = '';
        if ($this->from_name) {
            $from_name = '=?'.$this->charset.'?B?'.base64_encode($this->from_name).'?=';
        }
        $subject = '=?'.$this->charset.'?B?'.base64_encode($this->subject).'?=';
        $type = 'text/'.($this->in_html ? 'html' : 'plain');

        return 'Content-type: '.$type.'; charset="'.$this->charset."\"\r\n".
            'Subject: '.$subject."\r\n".
            'MIME-Version: 1.0'."\r\n".
            'Content-type: '.$type.'; charset="'.$this->charset."\"\r\n".
            'To: '.$this->to."\r\n".
            'From: '.$from_name.' <'.$this->from.'>'."\r\n".
            'Reply-To: '.$from_name.' <'.$this->from.'>'."\r\n";
    }
}
