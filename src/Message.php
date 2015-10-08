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

use Sendmail\Message\Headers;
/**
 * Message
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Message
{
    /**
     * Message charset
     *
     * @var string
     */
    protected $charset = Headers::DEFAULT_CHARSET;

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
     * Headers
     *
     * @var \Sendmail\Message\Headers
     */
    protected $headers;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->headers = new Headers();
        $this->setCharset($this->charset)->setSubject('');
        $this->headers->set('MIME-Version', '1.0');
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
        $this->headers->setCharset($charset);
        return $this->setContentType();
    }

    /**
     * Set content type
     *
     * @return \Sendmail\Message
     */
    protected function setContentType()
    {
        $this->headers->set(
            'Content-type',
            'text/'.($this->in_html ? 'html' : 'plain').'; charset="'.$this->charset.'"'
        );
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
    public function setFrom($from, $name)
    {
        $this->headers->set('From', $this->headers->foramatName($from, $name));
        return $this;
    }

    /**
     * Set reply to
     *
     * @param string $to
     * @param string $name
     *
     * @return \Sendmail\Message
     */
    public function setReplyTo($to, $name)
    {
        $this->headers->set('Reply-To', $this->headers->foramatName($to, $name));
        return $this;
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
        $this->headers->set('To', $to);
        return $this;
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
        $this->headers->set('Subject', $subject, true);
        return $this;
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
        return $this->setContentType();
    }

    /**
     * Get E-mail headers
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers->toString();
    }

    /**
     * Clone
     */
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }
}
