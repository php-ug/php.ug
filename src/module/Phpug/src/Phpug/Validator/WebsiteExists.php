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

class WebsiteExists extends AbstractValidator
{
    const DOES_NOT_EXIST = 'doesNotExist';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::DOES_NOT_EXIST => "This website does not seem to exist",
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
        if(200 != $this->getHttpResponseCode($value)) {
            $this->error(self::DOES_NOT_EXIST);
            return false;
        }
        return true;
    }

    protected function getHttpResponseCode($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "php.ug-website-checker");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_exec($ch);

        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
}
