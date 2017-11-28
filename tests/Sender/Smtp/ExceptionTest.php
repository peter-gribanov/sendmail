<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Tests\Sender\Smtp;

use Sendmail\Sender\Smtp\Dialogue;
use Sendmail\Sender\Smtp\Exception;

/**
 * @package Sendmail\Tests\Sender\Smtp
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Dialogue
     */
    protected $dialogue;

    protected function setUp()
    {
        $this->dialogue = $this
            ->getMockBuilder('\Sendmail\Sender\Smtp\Dialogue')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return array(
            array(
                "200 OK\n",
                'OK',
                200
            ),
            array(
                "404 Not Found\n",
                'Not Found',
                404
            ),
            array(
                "1234 Bad status\n".
                "Some Headers\n",
                '4 Bad status',
                123
            ),
            array(
                "No set status",
                'No set status',
                500
            ),
        );
    }

    /**
     * @dataProvider getLogs
     */
    public function testConstruct($log, $message, $code)
    {
        $this->dialogue
            ->expects($this->once())
            ->method('getLog')
            ->willReturn($log);
        $exception = new Exception($this->dialogue);
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testGetDialogue()
    {
        $exception = new Exception($this->dialogue);
        $this->assertEquals($this->dialogue, $exception->getDialogue());
    }
}
