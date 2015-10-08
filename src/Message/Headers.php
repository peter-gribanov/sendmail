<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Message;

/**
 * Message headers
 *
 * @package Sendmail/Message
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Headers
{
    /**
     * Default charset
     *
     * @var string
     */
    const DEFAULT_CHARSET = 'utf-8';

    /**
     * Headers end of line
     *
     * @var string
     */
    const EOL = "\r\n";

    /**
     * Message charset
     *
     * @var string
     */
    protected $charset = self::DEFAULT_CHARSET;

    /**
     * List headers
     *
     * @var string
     */
    protected $list = array();

    /**
     * Set charset
     *
     * @param string $charset
     *
     * @return \Sendmail\Message\Headers
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set
     *
     * @param string $key
     * @param string $value
     * @param boolean $encode
     *
     * @return \Sendmail\Message\Headers
     */
    public function set($key, $value, $encode = false)
    {
        $this->list[$key] = $encode ? $this->encode($value) : $value;
        return $this;
    }

    /**
     * Get
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        return isset($this->list[$key]) ? $this->list[$key] : null;
    }

    /**
     * Encode string
     *
     * @param string $string
     *
     * @return string
     */
    public function encode($string)
    {
        return '=?'.$this->charset.'?B?'.base64_encode($string).'?=';
    }

    /**
     * Foramat name
     *
     * @param string $email
     * @param string $name
     *
     * @return string
     */
    public function foramatName($email, $name = '')
    {
        return $name ? $this->encode($name).' <'.$email.'>' : $email;
    }

    /**
     * To string
     *
     * @return string
     */
    public function toString()
    {
        $headers = '';
        foreach ($this->list as $key => $value) {
            $headers .= $key.': '.$value.self::EOL;
        }
        return $headers;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}