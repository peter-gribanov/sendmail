<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Tests;

use Sendmail\Message;
use Sendmail\Message\Headers;

/**
 * @package Sendmail\Tests
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Message
     *
     * @var \Sendmail\Message
     */
    protected $message;

    /**
     * Headers
     *
     * @var \Sendmail\Message\Headers
     */
    protected $headers;

    /**
     * Example email
     *
     * @var string
     */
    protected $email = 'foo@example.com';

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->message = new Message();
        $this->headers = new Headers();
        $this->headers
            ->set('Content-type', 'text/plain; charset="'.Headers::DEFAULT_CHARSET.'"')
            ->set('Subject', '', true)
            ->set('MIME-Version', '1.0');
    }

    public function testConstruct()
    {
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetCharset()
    {
        $charset = 'koi8-r';
        $this->assertEquals($this->message, $this->message->setCharset($charset));
        $this->headers->set('Content-type', 'text/plain; charset="'.$charset.'"');
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetFrom()
    {
        $this->assertEquals($this->message, $this->message->setFrom($this->email));
        $this->headers->set('From', $this->email);
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetFromName()
    {
        $this->assertEquals($this->message, $this->message->setFrom($this->email, 'foo'));
        $this->headers->set('From', $this->headers->foramatName($this->email, 'foo'));
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetReplyTo()
    {
        $this->assertEquals($this->message, $this->message->setReplyTo($this->email));
        $this->headers->set('Reply-To', $this->email);
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetReplyToName()
    {
        $this->assertEquals($this->message, $this->message->setReplyTo($this->email, 'foo'));
        $this->headers->set('Reply-To', $this->headers->foramatName($this->email, 'foo'));
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetTo()
    {
        $this->assertEquals($this->message, $this->message->setTo($this->email));
        $this->headers->set('To', $this->email);
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testSetSubject()
    {
        $subject = 'Example message';
        $this->assertEquals($this->message, $this->message->setSubject($subject));
        $this->headers->set('Subject', $subject, true);
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testText()
    {
        $text = 'Example message';
        $this->assertEquals($this->message, $this->message->setText($text));
        $this->assertEquals($text, $this->message->getText());
    }

    public function testInHtml()
    {
        $this->assertEquals($this->message, $this->message->inHTML());
        $this->headers->set('Content-type', 'text/html; charset="'.Headers::DEFAULT_CHARSET.'"');
        $this->assertEquals($this->headers->toString(), $this->message->getHeaders());
    }

    public function testClone()
    {
        $this->message->setFrom($this->email);
        $new_message = clone $this->message;
        $new_message->setFrom('bar@example.com');
        $this->assertNotEquals($this->message->getHeaders(), $new_message->getHeaders());
    }
}
