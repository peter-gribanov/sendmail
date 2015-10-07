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
     * Reply to
     *
     * @var string
     */
    protected $reply_to = '';

    /**
     * Reply to name
     *
     * @var string
     */
    protected $reply_to_name = '';

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
     * @param string $name
     *
     * @return \Sendmail\Message
     */
    public function setFrom($from, $name = '')
    {
        $this->from = $from;
        $this->from_name = $name ?: $this->from_name;
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
     * Set E-mail from name
     *
     * @param string $name
     *
     * @return \Sendmail\Message
     */
    public function setFromName($name)
    {
        $this->from_name = $name;
        return $this;
    }

    /**
     * Get E-mail from name
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * Set reply to
     *
     * @param string $to
     *
     * @return \Sendmail\Message
     */
    public function setReplyTo($to, $name = '')
    {
        $this->reply_to = $to;
        $this->reply_to_name = $name ?: $this->reply_to_name;
        return $this;
    }

    /**
     * Get reply to
     *
     * @return string
     */
    public function getReplyTo()
    {
        return $this->reply_to;
    }

    /**
     * Set reply to name
     *
     * @param string $name
     *
     * @return \Sendmail\Message
     */
    public function setReplyToName($name)
    {
        $this->reply_to_name = $name;
        return $this;
    }

    /**
     * Get reply to name
     *
     * @return string
     */
    public function getReplyToName()
    {
        return $this->reply_to_name;
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
     * Is send E-mail in HTML format
     *
     * @return boolean
     */
    public function isHTML()
    {
        return $this->in_html;
    }

    /**
     * Get E-mail headers
     *
     * @return string
     */
    public function getHeaders()
    {
        $type = 'text/'.($this->in_html ? 'html' : 'plain');
        if ($this->subject) {
            $headers = 'Content-type: '.$type.'; charset="'.$this->charset."\"\r\n";
            $subject = '=?'.$this->charset.'?B?'.base64_encode($this->subject).'?=';
            $headers .= 'Subject: '.$subject."\r\n";
        }
        $headers .= 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-type: '.$type.'; charset="'.$this->charset."\"\r\n";
        $headers .= 'To: '.$this->to."\r\n";

        $from_name = '';
        if ($this->from_name) {
            $from_name = '=?'.$this->charset.'?B?'.base64_encode($this->from_name).'?= ';
        }
        $headers .= 'From: '.$from_name.'<'.$this->from.'>'."\r\n";

        if ($this->reply_to) {
            $reply_to_name = $from_name;
            if ($this->reply_to_name) {
                $reply_to_name = '=?'.$this->charset.'?B?'.base64_encode($this->reply_to_name).'?= ';
            }
            $headers .= 'Reply-To: '.$reply_to_name.'<'.$this->reply_to.'>'."\r\n";
        }

        return $headers;
    }
}
