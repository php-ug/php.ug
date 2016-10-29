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

use OrgHeiglHybridAuth\UserToken;
use Phpug\Acl\RoleManager;
use Phpug\Acl\UsersGroupAssertion;
use Phpug\Entity\Groupcontact;
use Phpug\Entity\Usergroup;
use Phpug\Exception\UnauthenticatedException;
use Phpug\Exception\UnauthorizedException;
use Phpug\Form\PromoteUsergroupForm;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Permissions\Acl\Acl;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;


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

    protected $acl;

    protected $usertoken;

    protected $roleManager;

    protected $assertion;

    protected $form;

    public function __construct(EntityManager $em, Acl $acl, UserToken $usertoken, RoleManager $roleManager, UsersGroupAssertion $assertion, Form $form)
    {
        $this->em = $em;
        $this->acl = $acl;
        $this->usertoken = $usertoken;
        $this->roleManager = $roleManager;
        $this->assertion = $assertion;
        $this->form = $form;
    }

    public function promoteAction()
    {
        $this->form->get('userGroupFieldset')->remove('id');

        $usergroup = new Usergroup();
        $contact = new Groupcontact();
        $contact->group = $usergroup;
        $usergroup->addContact($contact);

        $this->form->bind($usergroup);
        $this->form->setAttribute('action', $this->url()->fromRoute('ug/promote'));
        if (! $this->acl->isAllowed((string) $this->roleManager, 'ug', 'validate')) {
            $this->form->get('userGroupFieldset')->remove('state');
        }

        if ($this->usertoken->isAuthenticated()) {
            $collection = $this->form->get('userGroupFieldset')->get('contacts');
            $fieldSets  = $collection->getFieldsets();
            $fieldSets[0]->get('service')->setValue(1);
            $fieldSets[0]->get('name')->setValue($this->usertoken->getName());
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Handle form sending
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                // Handle storage of form data
                try {
                   // var_Dump($form->getData());
                    // Store content
                    $this->em->persist($this->form->getData());
                    $this->em->flush();
                }catch(Exception $e){var_dump($e);}

                $this->flashMessenger()->addSuccessMessage(sprintf(
                    'Thanks for telling us about %1$s. We will revise your entry and inform you as soon as it\'s publicised',
                    $usergroup->getName()
                ));
                $this->getEventManager()->trigger(
                    'notifyadmin', null, [
                        'name' => $usergroup->getName(),
                        'shortname' => $usergroup->getShortname(),
                    ]
                );

                return $this->redirect()->toRoute('home');
            } else {
//                var_Dump($form->getMessages());
            }
        }
        return array('form' => $this->form);

    }

    public function editAction()
    {
        if (! $this->usertoken->isAuthenticated()) {
            throw new UnauthenticatedException(
                'You have to be logged in to edit the informations for this usergroup.',
                0,
                null,
                'You are not logged in'
            );
        }

        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $usergroup = $this->em->getRepository('Phpug\Entity\Usergroup')->findBy(array('shortname' => $id));
        if (! $usergroup) {
            $this->getResponse()->setStatusCode(404);
            return array('error' => array(
                'title' => 'No Usergroup found',
                'message' => 'We could not find the usergroup you requested!</p><p>Perhaps the usergroup has been renamed or there is a typo in its name? Please check back with the contac ts of the usergroup or feel free to contact us via the <a href="/contact">Contact-Form</a>!'
            ));
        }
        $usergroup = $usergroup[0];
        /** @var \Phpug\Entity\Usergroup $usergroup */
        if (! $usergroup->hasContacts()) {
            $contact = new Groupcontact();
            $contact->group = $usergroup;
            $usergroup->addContact($contact);
        }

        $this->assertion->setGroup($usergroup);

        if (! $this->acl->isAllowed((string) $this->roleManager, 'ug', 'edit')) {
            $this->getResponse()->setStatusCode(403);
            throw new UnauthorizedException(
                'Your account has not the necessary rights to edit this usergroup. If you feel like that is an error please contact one of the representatives of the usergroup. If that doesn\'t help (Or you have locked yourself out) feel free to contact us via the <a href="/contact">Contact-Form</a>!',
                0,
                null,
                'You are not authorized to do that'
            );
        }

        $this->form->bind($usergroup);

        $this->form->setAttribute('action', $this->url()->fromRoute('ug/edit', array('id' => $id)));
        $this->form->get('userGroupFieldset')->get('location')->setValue($usergroup->getLocation());
        if (! $this->acl->isAllowed((string) $this->roleManager, 'ug', 'validate')) {
            $this->form->get('userGroupFieldset')->remove('state');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Handle form sending
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                // Handle storage of form data
                try {
                    // Store content
                    $this->em->persist($this->form->getData());
                    $this->em->flush();
                } catch(Exception $e){error_log($e->getMessage());}

                $this->flashMessenger()->addSuccessMessage(sprintf(
                    'Your Entry has been stored and you should already see the changes you did for %1$s.',
                    $usergroup->getName()
                ));
                return $this->redirect()->toRoute('home');
            } else {
            }
        }

        $view = new \Zend\View\Model\ViewModel(array('form' => $this->form));
        $view->setTemplate('phpug/usergroup/promote.phtml');
        return $view;

    }
}
