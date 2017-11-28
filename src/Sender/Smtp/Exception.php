<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender\Smtp;

/**
 * Dialogue exception
 *
 * @package Sendmail\Sender\Smtp
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Exception extends \Exception
{
    /**
     * @var Dialogue
     */
    protected $dialogue;

    /**
     * @param Dialogue $dialogue
     */
    public function __construct(Dialogue $dialogue)
    {
        $this->dialogue = $dialogue;

        $response = explode("\n", $dialogue->getLog());
        $response = array_shift($response);
        if (preg_match('/^(\d{3})(.+)/', $response, $match)) {
            parent::__construct(trim($match[2]), (int)$match[1]);
        } else {
            parent::__construct(trim($response), 500);
        }
    }

    /**
     * @return Dialogue
     */
    public function getDialogue()
    {
        return $this->dialogue;
    }
}
