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

use Sendmail\Queue;
use Sendmail\Message;

/**
 * @package Sendmail\Test
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    protected $sender;
    protected $queue;

    protected function setUp()
    {
        $this->sender = $this->getMock('\Sendmail\Sender\SenderInterface');
        $this->queue = new Queue($this->sender);
    }

    public function getMessages()
    {
        return array(
            array(
                array()
            ),
            array(
                array(
                    $this->getMock('\Sendmail\Message')
                )
            ),
            array(
                array(
                    $this->getMock('\Sendmail\Message'),
                    $this->getMock('\Sendmail\Message')
                )
            ),
            array(
                array(
                    $this->getMock('\Sendmail\Message'),
                    $this->getMock('\Sendmail\Message'),
                    $this->getMock('\Sendmail\Message'),
                    $this->getMock('\Sendmail\Message')
                )
            )
        );
    }

    /**
     * @dataProvider getMessages
     */
    public function testQueue(array $messages)
    {
        // empty queue
        $this->assertEquals(0, count($this->queue));
        $this->assertInstanceOf('ArrayIterator', $this->queue->getIterator());
        // add messages
        foreach ($messages as $message) {
            $this->assertEquals($this->queue, $this->queue->add($message));
        }
        // test get
        $this->assertEquals(count($messages), count($this->queue));
        foreach ($this->queue as $key => $message) {
            $this->assertEquals($messages[$key], $message);
        }
        // clear queue
        $this->assertEquals($this->queue, $this->queue->clear());
        $this->assertEquals(0, count($this->queue));
        
    }

    public function testGetSender()
    {
        $this->assertEquals($this->sender, $this->queue->getSender());
    }

    public function getRecipients()
    {
        return array(
            array(
                array(),
                array()
            ),
            array(
                array(),
                array(
                    'foo@example.com'
                )
            ),
            array(
                array(
                    'foo@example.com'
                ),
                array(
                    'foo@example.com'
                )
            ),
            array(
                array(),
                array(
                    'foo@example.com',
                    'bar@example.com'
                )
            ),
            array(
                array(
                    'foo@example.com',
                    'bar@example.com'
                ),
                array(
                    'foo@example.com',
                    'bar@example.com'
                )
            ),
            array(
                array(),
                array(
                    'foo@example.com',
                    'bar@example.com',
                    'baz@example.com'
                )
            ),
            array(
                array(
                    'foo@example.com',
                    'bar@example.com',
                    'baz@example.com'
                ),
                array(
                    'foo@example.com',
                    'bar@example.com',
                    'baz@example.com'
                )
            )
        );
    }

    /**
     * @dataProvider getRecipients
     */
    public function testNotify(array $base, array $recipients)
    {
        $message = new Message();
        foreach ($base as $to) {
            $this->queue->add($message->setTo($to));
        }
        $this->assertEquals($this->queue, $this->queue->notify($recipients, $message));
        // check list messages
        $this->assertEquals(count($recipients)+count($base), $this->queue->count());
        $expected = array_merge($base, $recipients);
        foreach ($this->queue as $key => $message) {
            /* @var $message \Sendmail\Message */
            $this->assertEquals($expected[$key], $message->getTo());
        }
    }

    /**
     * @dataProvider getMessages
     */
    public function testSend(array $messages)
    {
        // fill queue
        foreach ($messages as $key => $message) {
            $this->queue->add($message);
            $this->sender
                ->expects($this->at($key))
                ->method('send')
                ->with($this->equalTo($message))
                ->willReturn(true);
        }
        $this->assertTrue($this->queue->send());
    }

    public function testSendFail()
    {
        $message = $this->getMock('\Sendmail\Message');
        $this->queue->add($message);
        $this->sender
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo($message))
            ->willReturn(false);
        $this->assertFalse($this->queue->send());
    }
}
