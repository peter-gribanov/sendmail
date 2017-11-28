<?php
/**
 * Sendmail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Tests\Sender;

use Sendmail\Tests\RunkitTestCase;
use Sendmail\Sender\Mail;
use Sendmail\Message;

/**
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class MailTest extends RunkitTestCase
{
    public function testSend()
    {
        $message = new Message();
        $message
            ->setText('Example text')
            ->setTo('foo@example.com')
            ->setFrom('bar@example.com')
            ->setSubject('Example subject');

        // override mail()
        $mock = $this->getRunkitMock(array('mail'));
        $mock
            ->expects($this->once())
            ->method('mail')
            ->with(
                '',
                '',
                $message->getText(),
                $message->getHeaders()
            )
            ->willReturn(true);

        // test
        $mail = new Mail();
        $mail->send($message);
    }
}
