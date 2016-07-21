<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     12.07.2016
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace UgHealth\Controller;

use Doctrine\ORM\EntityManager;
use UgHealth\WebsiteStatus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class WebsiteHealthController extends AbstractActionController
{
    protected $entityManager;

    protected $website;

    public function __construct(EntityManager $entityManager, WebsiteStatus $website)
    {
        $this->entityManager = $entityManager;
        $this->website = $website;
    }

    public function indexAction()
    {
        return new ViewModel(); // display standard index page
    }

    public function websiteAction()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Get user email from console and check if the user used --verbose or -v flag
        $usergroup = $request->getParam('usergroup');

        $em = $this->entityManager->getRepository('Phpug\Entity\Usergroup')->findBy(array('shortname'=>$usergroup));
        if (count($em) < 1) {
            throw new \UnexpectedValueException(sprintf(
                'No Usergroup with shortname "%s" found',
                $usergroup
            ));
        }
        if (count($em) > 1) {
            throw new \UnexpectedValueException(sprintf(
                'More than one Usergroup with shortname "%s" found',
                $usergroup
            ));
        }

        $checkResult = $this->website->check($em[0]);
        echo $checkResult;
        return true;
    }
}
