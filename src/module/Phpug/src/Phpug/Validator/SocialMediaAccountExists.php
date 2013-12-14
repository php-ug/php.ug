<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Phpug\Validator;

use Zend\Validator\AbstractValidator;

class SocialMediaAccountExists extends AbstractValidator
{
    const THAT = 'that';
    /**
     * Digits filter used for validation
     *
     * @var \Zend\Filter\Digits
     */
    protected static $filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::THAT   => "that error message",
    );

    /**
     * Returns true if and only if $value is a valid social media account
     *
     * TODO: implement this one
     *
     * @param  string $value
     * @param array $context The context of this validator
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        return true;
    }
}
