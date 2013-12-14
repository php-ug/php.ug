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

//
//// The module name is obtained using directory name or constant
//$moduleName = pathinfo($rootPath, PATHINFO_BASENAME);
//if (defined('MODULE_NAME')) {
//    $moduleName = MODULE_NAME;
//}
//
//// A locator will be set to this class if available
//$moduleTestCaseClassname = '\\'.$moduleName.'Test\\Framework\\TestCase';
//
//require_once substr(str_Replace('\\', '/', $moduleTestCaseClassname . '.php'), 1);
//
//// This module's path plus additionally defined paths are used $modulePaths
//$modulePaths = array(dirname($rootPath));
//if (isset($additionalModulePaths)) {
//    $modulePaths = array_merge($modulePaths, $additionalModulePaths);
//}
//
//// Load this module and defined dependencies
//$modules = array($moduleName);
//if (isset($moduleDependencies)) {
//    $modules = array_merge($modules, $moduleDependencies);
//}
//
//
//$listenerOptions = new Zend\ModuleManager\Listener\ListenerOptions(array('module_paths' => $modulePaths));
//$defaultListeners = new Zend\ModuleManager\Listener\DefaultListenerAggregate($listenerOptions);
//$sharedEvents = new Zend\EventManager\SharedEventManager();
//$moduleManager = new \Zend\ModuleManager\ModuleManager($modules);
//$moduleManager->getEventManager()->setSharedManager($sharedEvents);
//$moduleManager->getEventManager()->attachAggregate($defaultListeners);
//$moduleManager->loadModules();
//
//if (method_exists($moduleTestCaseClassname, 'setLocator')) {
//
//    $config = $defaultListeners->getConfigListener()->getMergedConfig();
//    $di = new Zend\ServiceManager\ServiceManager(new Zend\Mvc\Service\ServiceManagerConfig(array()));
////     $di->instanceManager()->addTypePreference('Zend\Di\LocatorInterface', $di);
//
////     if (isset($config['di'])) {
////         $diConfig = new \Zend\Di\Config($config['di']);
////         $diConfig->configure($di);
////     }
//
////     $routerDiConfig = new \Zend\Di\Config(
////         array(
////             'definition' => array(
////                 'class' => array(
////                     'Zend\Mvc\Router\RouteStackInterface' => array(
////                         'instantiator' => array(
////                             'Zend\Mvc\Router\Http\TreeRouteStack',
////                             'factory'
////                         ),
////                     ),
////                 ),
////             ),
////         )
////     );
////     $routerDiConfig->configure($di);
//
//    call_user_func_array($moduleTestCaseClassname.'::setLocator', array($di));
//}
//
//// When this is in global scope, PHPUnit catches exception:
//// Exception: Zend\Stdlib\PriorityQueue::serialize() must return a string or NULL
//unset($moduleManager, $sharedEvents);
//

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


