<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Tests\Message;

use Sendmail\Message\Headers;

/**
 * @package Sendmail\Tests\Message
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HeadersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Headers
     *
     * @var Headers
     */
    protected $headers;

    protected function setUp()
    {
        $this->headers = new Headers();
    }

    public function testToStringEmpty()
    {
        $this->assertEmpty($this->headers->toString());
    }

    public function testGetSet()
    {
        $email = 'foo@example.com';
        $this->assertNull($this->headers->get('To'));
        $this->assertEquals($this->headers, $this->headers->set('To', $email));
        $this->assertEquals($email, $this->headers->get('To'));
    }

    public function testEncode()
    {
        $string = 'foo';
        $expected = '=?'.Headers::DEFAULT_CHARSET.'?B?'.base64_encode($string).'?=';
        $this->assertEquals($expected, $this->headers->encode($string));
    }

    public function testEncodeNewCharset()
    {
        $charset = 'koi8-r';
        $string = 'foo';
        $expected = '=?'.$charset.'?B?'.base64_encode($string).'?=';
        $this->assertEquals($this->headers, $this->headers->setCharset($charset));
        $this->assertEquals($expected, $this->headers->encode($string));
    }

    public function testSetEncode()
    {
        $string = 'foo';
        $this->assertEquals(
            $this->headers,
            $this->headers->set('X-Data', $string, true)
        );
        $this->assertEquals(
            $this->headers->encode($string),
            $this->headers->get('X-Data')
        );
    }

    public function testForamatNameEmpty()
    {
        $email = 'foo@example.com';
        $this->assertEquals($email, $this->headers->foramatName($email));
    }

    public function testForamatName()
    {
        $email = 'foo@example.com';
        $name = 'foo';
        $this->assertEquals(
            $this->headers->encode($name).' <'.$email.'>',
            $this->headers->foramatName($email, $name)
        );
    }

    public function getHeaders()
    {
        return array(
            array(
                array(
                    'To' => 'foo@example.com'
                ),
                'To: foo@example.com'.Headers::EOL
            ),
            array(
                array(
                    'From' => 'foo@example.com',
                    'To' => 'bar@example.com'
                ),
                'From: foo@example.com'.Headers::EOL.
                'To: bar@example.com'.Headers::EOL
            ),
            array(
                array(
                    'Content-type' => 'text/plain; charset="utf-8"',
                    'Subject' => 'baz',
                    'MIME-Version' => '1.0',
                    'From' => 'foo@example.com',
                    'To' => 'bar@example.com',
                    'X-PHP-Version' => PHP_VERSION
                ),
                'Content-type: text/plain; charset="utf-8"'.Headers::EOL.
                'Subject: baz'.Headers::EOL.
                'MIME-Version: 1.0'.Headers::EOL.
                'From: foo@example.com'.Headers::EOL.
                'To: bar@example.com'.Headers::EOL.
                'X-PHP-Version: '.PHP_VERSION.Headers::EOL
            )
        );
    }

    /**
     * @dataProvider getHeaders
     */
    public function testToString($headers, $expected)
    {
        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }
        $this->assertEquals($expected, $this->headers->toString());
        $this->assertEquals($expected, (string)$this->headers);
    }
}
