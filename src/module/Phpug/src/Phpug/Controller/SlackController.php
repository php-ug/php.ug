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

use GuzzleHttp\Client;
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
class SlackController extends AbstractActionController
{
    private $apiKey;

    private $teamName;

    /** @var  Client */
    private $client;

    /** @var  Form */
    private $form;

    public function __construct(Client $client, Form $form, $teamName, $apiKey)
    {
        $this->teamName = $teamName;
        $this->apiKey = $apiKey;
        $this->client = $client;
        $this->form = $form;
    }

    public function inviteAction()
    {
        $this->form->setAttribute('action', $this->url()->fromRoute('slackinvite'));

        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ['form' => $this->form];
        }

        $this->form->setData($request->getPost());

        if (! $this->form->isValid()) {
            return ['form' => $this->form];
        }
        // Handle storage of form data
        try {
            $slackinvite = $this->form->getData();
            $params = [
                'email' => $slackinvite['slackInvite']['email'],
                'first_name' => $slackinvite['slackInvite']['name'],
                'token' => $this->apiKey,
                'set_active' => true,
                '_attempts' => '1',
                't' => time(),
            ];

            $response = $this->client->request(
                'POST',
                'https://'.$this->teamName.'.slack.com/api/users.admin.invite',
                [
                 'query' => $params
                ]
            );

            $values = json_decode($response->getBody()->getContents(), true);
            if ($values['ok'] !== true) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    'Slack reported an error: "%1$s". <br/>Sadly there\'s nothing we can do about it currently',
                    $values['error']
                ));
                return $this->redirect()->toRoute('home');
            }
        } catch (Exception $e) {
        }

        $this->flashMessenger()->addSuccessMessage(
            'Thanks for your interest in the world-wide PHP-Usergroup-Slack-Channel'
            . '<br/>'
            . 'You should receive an email with your invitation shortly.'
        );

        return $this->redirect()->toRoute('home');
    }

    public function redirectAction()
    {
        return $this->redirect()->toUrl('https://' . $this->teamName . '.slack.com');
    }
}
