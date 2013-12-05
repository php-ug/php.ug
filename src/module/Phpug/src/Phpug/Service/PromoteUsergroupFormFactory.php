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
 * @since     01.08.13
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Service;

use Zend\Form\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Validator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Phpug\Entity\Usergroup;


class PromoteUsergroupFormFactory implements  FactoryInterface
{

    public function createService(ServiceLocatorInterface $createService)
    {
        $factory = new Factory();

        $form = $factory->createForm(array(
            'name' => 'promoteUserGroup',
            'elements' => array(
//                array('spec' => array(
//                    'name'    => 'twitternick',
//                    'type'    => '\Zend\Form\Element\Text',
//                        'label'       => 'Twitter-Nick',
//                    'options' => array(
//                        'label'       => 'Twitter-Nick',
//                        'label_attributes' => array(
//                            'class' => 'control-label',
//                        ),
//                        'description' => 'Is there a twitter-nick for your Usergroup? Tell us whom we should follow to get the newest informations!',
//                        'required'    => false,
//                        'validators'  => array(
//                            // TODO get this from a service?
//                            new \Phpug\Validator\SocialMediaAccountExists('twitter'),
//                        ),
//                    ),
//                )),
//                array('spec' => array(
//                    'name'    => 'city',
//                    'type'    => '\Zend\Form\Element\Text',
//                    'options' => array(
//                        'label'       => 'Irrelephpant',
//                        'label_attributes' => array(
//                            'class' => 'control-label',
//                        ),
//                        'description' => 'Leave this field as it is',
//                        'required'    => true,
//                        'validators'  => array(
//                            new Validator\Identical(''),
//                        ),
//                    ),
//                )),
                array(
                    'spec' => array(
                        'name' => 'csrf',
                        'type' => '\Zend\Form\Element\Csrf',
                    ),
                ),
                array(
                    'spec' => array(
                        'name' => 'send',
                        'type' => '\Zend\Form\Element\Button',
                        'options' => array(
                            'type' => 'submit',
                            'label' => 'Tell us!',
                        ),
                        'attributes' => array(
                            'type' => 'submit',
                            'label' => 'Tell us!',
                        ),
                    ),
                ),
            ),

        ));

        $form->setHydrator(new ClassMethods());
//             ->setInputFilter(new InputFilter());

        // Add the user fieldset, and set it as the base fieldset
        $usergroupFieldset = $createService->get('UsergroupFieldset');
        $usergroupFieldset->setUseAsBaseFieldset(true);
        $form->add($usergroupFieldset);

        return $form;
    }
 }