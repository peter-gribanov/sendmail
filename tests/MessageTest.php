<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Test;

use Sendmail\Message;

/**
 * @package Sendmail\Test
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
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->message = new Message();
    }

    public function testCharset()
    {
        $charset = 'koi8-r';
        $this->assertEquals(Message::DEFAULT_CHARSET, $this->message->getCharset());
        $this->assertEquals($this->message, $this->message->setCharset($charset));
        $this->assertEquals($charset, $this->message->getCharset());
    }

    public function testFromEmpty()
    {
        $this->assertEmpty($this->message->getFrom());
    }

    public function testFrom()
    {
        $from = 'foo@example.com';
        $this->assertEquals($this->message, $this->message->setFrom($from));
        $this->assertEquals($from, $this->message->getFrom());
    }

    public function testFromNameEmpty()
    {
        $this->assertEmpty($this->message->getFromName());
    }

    public function testSetFromName()
    {
        $from_name = 'bar';
        $this->assertEquals($this->message, $this->message->setFromName($from_name));
        $this->assertEquals($from_name, $this->message->getFromName());
    }

    public function testReplyToEmpty()
    {
        $this->assertEmpty($this->message->getReplyTo());
    }

    public function testReplyTo()
    {
        $to = 'foo@example.com';
        $this->assertEquals($this->message, $this->message->setReplyTo($to));
        $this->assertEquals($to, $this->message->getReplyTo());
    }

    public function testReplyToNameEmpty()
    {
        $this->assertEmpty($this->message->getReplyToName());
    }

    public function testSetReplyToName()
    {
        $to_name = 'bar';
        $this->assertEquals($this->message, $this->message->setReplyToName($to_name));
        $this->assertEquals($to_name, $this->message->getReplyToName());
    }

    public function testToEmpty()
    {
        $this->assertEmpty($this->message->getTo());
    }

    public function testTo()
    {
        $to = 'foo@example.com';
        $this->assertEquals($this->message, $this->message->setTo($to));
        $this->assertEquals($to, $this->message->getTo());
    }

    public function testToSubject()
    {
        $this->assertEmpty($this->message->getSubject());
    }

    public function testSubject()
    {
        $subject = 'Example';
        $this->assertEquals($this->message, $this->message->setSubject($subject));
        $this->assertEquals($subject, $this->message->getSubject());
    }

    public function testToText()
    {
        $this->assertEmpty($this->message->getText());
    }

    public function testText()
    {
        $text = 'Example';
        $this->assertEquals($this->message, $this->message->setText($text));
        $this->assertEquals($text, $this->message->getText());
    }

    public function testHTML()
    {
        $this->assertFalse($this->message->isHTML());
        $this->assertEquals($this->message, $this->message->inHTML());
        $this->assertTrue($this->message->isHTML());
    }
}
