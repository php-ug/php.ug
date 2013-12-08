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

class IsCalendarUrl extends AbstractValidator
{
    const DOES_NOT_EXIST = 'doesNotExist';

    const NOT_CALENDAR = 'notCalendar';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::DOES_NOT_EXIST => "This URL does not provide a File",
        self::NOT_CALENDAR   => "This URL does not provide an iCalendar-File",
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
        $headers = $this->getHeaders($value);
        if (200 != $headers['http-return-code']) {
            $this->error(self::DOES_NOT_EXIST);
            return false;
        }
        if (false === strpos($headers['content-type'], 'text/calendar')) {
            $this->error(self::NOT_CALENDAR);
            return false;
        }
        return true;
    }

    protected function getHeaders($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "php.ug-website-checker");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_exec($ch);

        return array(
            'http-return-code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'content-type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
        );
    }
}
