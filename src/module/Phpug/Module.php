<?php
/**
 * Copyright (c) 2011-2012 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace Phpug;

use Phpug\View\Strategy\UnauthorizedStrategy;
use Zend\Module\Manager;
use Zend\Module\Consumer\AutoloaderProvider;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;
use Phpug\View\Strategy\JsonExceptionStrategy;


/**
 * The Module-Provider
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/heiglandreas/php.ug
 */
class Module
{
    
    public function onBootstrap($e)
    {
    	$eventManager        = $e->getApplication()->getEventManager();
    	$moduleRouteListener = new ModuleRouteListener();
    	$moduleRouteListener->attach($eventManager);

        // Could also be put into a separate Module
        // Config json enabled exceptionStrategy
        $exceptionStrategy = new JsonExceptionStrategy();

        $displayExceptions = false;

        if (isset($config['view_manager']['display_exceptions'])) {
            $displayExceptions = $config['view_manager']['display_exceptions'];
        }

        $exceptionStrategy->setDisplayExceptions($displayExceptions);
        $exceptionStrategy->attach($e->getTarget()->getEventManager());

        $authStrategy = new UnauthorizedStrategy('error/unauthorized.phtml');
        $authStrategy->attach($e->getTarget()->getEventManager());
    }
    
    public function getConfig()
    {
    	return include __DIR__ . '/config/module.config.php';
    }
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                // This will overwrite the native navigation helper
                'navigation' => function(HelperPluginManager $pm) {
                        // Setup ACL:
                        $acl = $pm->getServiceLocator()->get('acl');
                        $role = $pm->getServiceLocator()->get('roleManager');
                        $role->setUserToken($pm->getServiceLocator()->get('OrgHeiglHybridAuthToken'));

                        // Get an instance of the proxy helper
                        $navigation = $pm->get('Zend\View\Helper\Navigation');

                        // Store ACL and role in the proxy helper:
                        $navigation->setAcl($acl)
                            ->setRole((string) $role);

                        // Return the new navigation helper instance
                        return $navigation;
                    }
            )
        );
    }
    public function getAutoloaderConfig()
    {
    	return array(
    			'Zend\Loader\StandardAutoloader' => array(
    					'namespaces' => array(
    							__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
    					        __NAMESPACE__ . 'Test' => __DIR__ . '/tests/' . __NAMESPACE__,
    					),
    			),
    	);
    }
}
