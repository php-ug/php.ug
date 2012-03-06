<?php
/**
 * $Id$
 *
 * @__LICENSE__@
 *
 * This is the main Bootstrap-File for PHPUnit
 *
 * @category   Tests
 * @package    Org_Heigl_Hyphenator
 * @author     Andreas Heigl<a.heigl@wdv.de>
 * @copyright  2011-2011 Andreas Heigl
 * @license    @__LICENSEURL__@ @__LICENSENAME__@
 * @version    2.0.1
 * @since      05.09.2011
 */
// TODO: check include path
ini_set ( 'date.timezone', 'Europe/Berlin' );
ini_set ( 'memory_limit', '512M');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../src'),
    get_include_path(),
)));

mb_internal_encoding('UTF-8');

class UnitTestHelper
{
    /**
     * Access protected or private methods
     *
     * use the following code to access any protected or private class method
     * $obj = new MyClass();
     * $method = UnitTestHelper::getMethod($obj, 'nameOfMethod');
     * $result = $method->invoke('your',method,array('arguments'));
     *
     * @param Object|string $obj
     * @param string $name
     *
     * @return method
     */
    public static function getMethod($obj, $name) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
