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
ini_set('date.timezone', 'Europe/Berlin');
ini_set('memory_limit', '512M');

mb_internal_encoding('UTF-8');

$additionalNamespaces = $additionalModulePaths = $moduleDependencies = null;

$rootPath = realpath(dirname(__DIR__));
$testsPath = "$rootPath/tests";

if (is_readable($testsPath . '/TestConfiguration.php')) {
    require_once $testsPath . '/TestConfiguration.php';
} else {
    require_once $testsPath . '/TestConfiguration.php.dist';
}

$path = array(
    ZF2_PATH,
    realpath(__DIR__ . '/../src'),
    realpath(__DIR__ . '/../tests'),
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

require_once __DIR__ . '/../../../../vendor/autoload.php';
