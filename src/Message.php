<?php
/**
 * Sendmail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail;

use Sendmail\Message\Headers;

/**
 * Message.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Message
{
    /**
     * Message charset.
     *
     * @var string
     */
    protected $charset = Headers::DEFAULT_CHARSET;

    /**
     * In HTML format.
     *
     * @var bool
     */
    protected $in_html = false;

    /**
     * Headers.
     *
     * @var Headers
     */
    protected $headers;

    /**
     * From.
     *
     * @var string
     */
    protected $from = '';

    /**
     * To.
     *
     * @var string
     */
    protected $to = '';

    /**
     * Subject.
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Message text.
     *
     * @var string
     */
    protected $text = '';

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->headers = new Headers();
        $this->setCharset($this->charset)->setSubject('');
        $this->headers->set('MIME-Version', '1.0');
    }

    /**
     * Set message charset.
     *
     * @param string $charset
     *
     * @return self
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        $this->headers->setCharset($charset);

        return $this->setContentType();
    }

    /**
     * Get message charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Send from E-mail.
     *
     * @param string $from
     * @param string $name
     *
     * @return self
     */
    public function setFrom($from, $name = '')
    {
        $this->from = $from;
        $this->headers->set('From', $this->headers->foramatName($from, $name));

        return $this;
    }

    /**
     * Get from E-mail.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set reply to E-mail.
     *
     * @param string $to
     * @param string $name
     *
     * @return self
     */
    public function setReplyTo($to, $name = '')
    {
        $this->headers->set('Reply-To', $this->headers->foramatName($to, $name));

        return $this;
    }

    /**
     * Send to E-mail.
     *
     * @param string $to
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        $this->headers->set('To', $to);

        return $this;
    }

    /**
     * Get to E-mail.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set message subject.
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        $this->headers->set('Subject', $subject, true);

        return $this;
    }

    /**
     * Get message subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message text.
     *
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get message text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Send E-mail in HTML format.
     *
     * @return self
     */
    public function inHTML()
    {
        $this->in_html = true;

        return $this->setContentType();
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers->toString();
    }

    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    /**
     * @return self
     */
    protected function setContentType()
    {
        $this->headers->set(
            'Content-type',
            'text/'.($this->in_html ? 'html' : 'plain').'; charset="'.$this->charset.'"'
        );

        return $this;
    }
}
