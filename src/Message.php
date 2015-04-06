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
 * Message
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Message
{
    /**
     * Default charset
     *
     * @var string
     */
    const DEFAULT_CHARSET = 'utf-8';

    /**
     * Message charset
     *
     * @var string
     */
    protected $charset = self::DEFAULT_CHARSET;

    /**
     * E-Mail from
     *
     * @var string
     */
    protected $from = '';

    /**
     * E-Mail from name
     *
     * @var string
     */
    protected $from_name = '';

    /**
     * E-mail to
     *
     * @var string
     */
    protected $to = '';

    /**
     * Subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Message text
     *
     * @var string
     */
    protected $text = '';

    /**
     * In HTML format
     *
     * @var boolean
     */
    protected $in_html = false;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->from = '<admin@'.$_SERVER['HTTP_HOST'].'>';
        $this->from_name = 'Administration';
    }

    /**
     * Set message charset
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
     * Set E-mail from
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
     * Get E-mail from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set E-mail to
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
     * Get E-mail to
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set message subject
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
     * Get message subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message text
     *
     * @param string $text
     *
     * @return \Sendmail\Message
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Send E-mail in HTML format
     *
     * @return \Sendmail\Message
     */
    public function inHTML()
    {
        $this->in_html = true;
        return $this;
    }

    /**
     * Get E-mail headers
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
