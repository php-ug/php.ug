<?php
/**
 * Copyright (c)2013-2013 heiglandreas
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
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category 
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     15.11.13
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Form;

use Phpug\Entity\Usergroup;
use Zend\Form\Element\Email;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator;

class SlackInviteFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected $serviceLocator = null;

    protected $edit = true;

    public function __construct()
    {
        parent::__construct();

        $this->setLabel('slackInvite')
             ->setName('slackInvite');

        $this->add(array(
            'name' => 'email',
            'type' => Email::class,
            'options' => [
                'label' => 'Email',
                'label_attributes' => [
                    'class' => 'control-label',
                ],
                'description' => 'The Email-Address we will send the invite to',
            ]
        ));

        $this->add(array(
            'name'    => 'name',
            'type'    => Text::class,
            'options' => array(
                'label'      => 'Name',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'What\'s your name',
            ),
        ));

    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'properties' => array(
                    'required' => true,
                ),
            ),
            'email' => array(
                'required' => true,
                'validators' => [array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'hostnameValidator' => new Validator\Hostname(),
                    ),
                )],
            ),
        );
    }
}
