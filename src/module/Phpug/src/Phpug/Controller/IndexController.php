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

namespace Phpug\Controller;

use Zend\View\Helper\ViewModel;

use Zend\Mvc\Controller\AbstractActionController,
    Doctrine\ORM\EntityManager
;

/**
 * The Controller for de default actions
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
class IndexController extends AbstractActionController
{

    protected $config = null;

    /**
     * Store the EntityManager
     *
     * @var EntityManager $em
     */
    protected $em = null;

    /**
     * Get the EntityManager for this Controller
     * 
     * @return EntityManager
     */
    public function getEntityManager()
	{
	    if (null === $this->em) {
	        $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	    }
   		return $this->em;
    }

    public function indexAction()
    {
        return array(
            'flash' => $this->flashMessenger()->getSuccessMessages(),
            'acl' => $this->getServiceLocator()->get('acl'),
        );
    }

    public function imprintAction()
    {
        return array(
            'user' => 'Andreas Heigl',
            'mail' => 'andreas@heigl.org',
            'address' => 'Forsthausstraße 7<br/>61279 Grävenwiesbach<br/>Germany',
        );
    }

    public function aboutAction()
    {
        return array();
    }

    public function teamAction()
    {
        return array(
            'team' => array(
                'andreas@heigl.org'=> array(
                    'name' => 'Andreas Heigl',
                    'twitter' => 'heiglandreas',
                    'google+' => '104738361153508561515',
                ),
                'cn@dmr-solutions.com' => array(
                    'name' => 'Christian Nielebock',
                    'twitter' => 'ravetracer',
                    'google+' => 'ravetracer',
                ),
            ),
        );
    }
    
    public function legalAction()
    {
    	return array();
    }

    /**
     * Redirect a user to the Usergroups homepage
     *
     * @return void
     */
    public function redirectAction()
    {
        $id   = $this->getEvent()->getRouteMatch()->getParam('ugid');
        $base = $this->getEvent()->getRouteMatch()->getParam('base');
        
        $result = $this->getEntityManager()->getRepository('Phpug\Entity\Usergroup')->findBy(array('shortname'=>$id));
        if ( ! $result ) {
            if ( ! $base ) {
                $this->redirect()->toRoute('home');
                return false;
            }
            $this->redirect()->toUrl('http://' . $base);
            return false;
        }
        $this->redirect()->toUrl(current($result)->url);
        return false;
    }

    /**
     * Show a page containing tips and tricks for running a usergroup
     *
     * @return array
     */
    public function tipsAction()
    {
        return array();
    }
}
