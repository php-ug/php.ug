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

use Phpug\Entity\Usergroup;
use Phpug\Form\PromoteUsergroupForm;
use Zend\Mvc\View\Http\ViewManager;
use Zend\View\Helper\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;


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
class UsergroupController extends AbstractActionController
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

    public function promoteAction()
    {
        $currentUser = $this->getServiceLocator()->get('OrgHeiglHybridAuthToken');
        if (! $currentUser) {
            $this->getResponse()->setStatusCode(401);
            return;
        }

        $acl = $this->getServiceLocator()->get('acl');
        if (! $acl) {
            $this->getResponse()->setSTatusCode(500);
            return true;
        }

        $role = $this->getServiceLocator()->get('roleManager')->setUserToken($currentUser);
        if (! $acl->isAllowed((string) $role, 'ug', 'promote')) {
            $this->getResponse()->setStatusCode(401);
            return true;
        }

        $form = $this->getServiceLocator()->get('PromoteUsergroupForm');
        $form->get('userGroupFieldset')->remove('id');


        $objectManager = $this->getEntityManager();
        $usergroup = new Usergroup();

        $form->bind($usergroup);
        $form->setAttribute('action', $this->url()->fromRoute('ug/promote'));
        if (! $acl->isAllowed((string) $role, 'ug', 'validate')) {
            $form->get('userGroupFieldset')->remove('state');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Handle form sending
            $form->setData($request->getPost());
            if ($form->isValid()) {
                // Handle storage of form data
                try {
                   // var_Dump($form->getData());
                    // Store content
                    $objectManager->persist($form->getData());
                    $objectManager->flush();
                }catch(Exception $e){var_dump($e);}

                $this->flashMessenger()->addSuccessMessage(sprintf(
                    'Thanks for telling us about %1$s. We will revise your entry and inform you as soon as it\'s publicised',
                    $usergroup->getName()
                ));
                $this->sendNotification($usergroup);
                return $this->redirect()->toRoute('home');
            } else {
//                var_Dump($form->getMessages());
            }
        }
        return array('form' => $form);

    }

    public function editAction()
    {
        $currentUser = $this->getServiceLocator()->get('OrgHeiglHybridAuthToken');
        if (! $currentUser) {
            $this->getResponse()->setStatusCode(401);
            return;
        }

        $acl = $this->getServiceLocator()->get('acl');
        if (! $acl) {
            $this->getResponse()->setSTatusCode(500);
            return true;
        }

        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $objectManager = $this->getEntityManager();
        $usergroup = $objectManager->getRepository('Phpug\Entity\Usergroup')->findBy(array('shortname' => $id));
        if (! $usergroup) {
            $this->getResponse()->setStatusCode(404);
            return true;
        }
        $usergroup = $usergroup[0];

        $role = $this->getServiceLocator()->get('roleManager')->setUserToken($currentUser);

        $this->getServiceLocator()
            ->get('usersGroupAssertion')
            ->setUser($currentUser)
            ->setGroup($usergroup);

        if (! $acl->isAllowed((string) $role, 'ug', 'edit')) {
            $this->getResponse()->setStatusCode(401);
            return true;
        }

        $form = $this->getServiceLocator()->get('PromoteUsergroupForm');


        $form->bind($usergroup);

        $form->setAttribute('action', $this->url()->fromRoute('ug/edit', array('id' => $id)));
        $form->get('userGroupFieldset')->get('location')->setValue($usergroup->getLocation());
        if (! $acl->isAllowed((string) $role, 'ug', 'validate')) {
            $form->get('userGroupFieldset')->remove('state');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Handle form sending
            $form->setData($request->getPost());
            if ($form->isValid()) {
                // Handle storage of form data
                try {
                    // var_Dump($form->getData());
                    // Store content
                    $objectManager->persist($form->getData());
                    $objectManager->flush();
                }catch(Exception $e){var_dump($e);}

                $this->flashMessenger()->addSuccessMessage(sprintf(
                    'Your Entry has been stored and you should already see the changes you did for %1$s.',
                    $usergroup->getName()
                ));
                return $this->redirect()->toRoute('home');
            } else {
//                var_Dump($form->getMessages());
            }
        }

        $view = new \Zend\View\Model\ViewModel(array('form' => $form));
        $view->setTemplate('phpug/usergroup/promote.phtml');
        return $view;

    }

    protected function sendNotification(Usergroup $usergroup)
    {
        $message = $this->getServiceLocator()->get('Phpug\Service\UsergroupMessage');
        $message->setBody(sprintf(
            $message->getBody(),
            $usergroup->getName()
        ));
        $message->setSubject(sprintf(
            $message->getSubject(),
            $usergroup->getName()
        ));

        $transport = $this->getServiceLocator()->get('Phpug\Service\Transport');
        $transport->send($message);

        return $this;
    }
}