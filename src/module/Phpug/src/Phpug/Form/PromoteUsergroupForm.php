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

namespace Phpug\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Validator;
use Zend\Stdlib\Hydrator\ArraySerializable;

class PromoteUsergroupForm extends Form
{
    /**
     * Initialize the form
     *
     * @return PromoteUsergroupForm
     */
    public function init()
    {
        $this->setName('promoteUserGroup');

        $this->setHydrator(new ArraySerializable());

        $this->add(array(
            'name'    => 'ug_name',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'      => 'Name',
                'description' => 'What does your usergroup call itself?',
                'required'   => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'EmailAddress',
//                        'options' => array(
//                            'allow'  => HostnameValidator::ALLOW_DNS,
//                            'domain' => true,
//                        ),
//                    ),
//                ),
            )
        ));

        $this->add(array(
            'name'    => 'acronym',
            'type'    => 'Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Short Name',
                'description' => 'Give us a short name that you can then use to refer to your group via http://php.ug/<short name>',
                'required'    => true,
//                'validators'  => array(),
            ),
        ));

        $this->add(array(
            'name'    => 'location',
            'type'    => '\OrgHeiglGeolocation\Form\Element\Geolocation',
            'options' => array(
                'label'       => 'Location',
                'description' => 'Tell us where you are located. Currently you have to provide the information like "50.1234,8.0815" for somewhere near Frankfurt/Main in Germany',
                'required'    => true,
            ),
        ));

        $this->add(array(
            'name'    => 'calendar',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Calendar-URL',
                'description' => 'Do you have an Event-Calendar? Tell us where we can point our calendar-app to get our hands onto your events.',
                'required'    => false,
                'validators'  => array(
                    new Validator\Uri(array('allowRelative' => false)),
                ),
            ),
        ));

        $this->add(array(
            'name'    => 'twitternick',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Twitter-Nick',
                'description' => 'Is there a twitter-nick for your Usergroup? Tell us whom we should follow to get the newest informations!',
                'required'    => false,
                'validators'  => array(
                    // TODO get this from a service?
                    new \Phpug\Validator\SocialMediaAccountExists('twitter'),
                )
            ),
        ));

        $this->add(array(
            'name' => 'csrf',
            'type' => '\Zend\Form\Element\Csrf',
        ));

        $this->add(array(
            'name'    => 'city',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Irrelephpant',
                'description' => 'Leave this field as it is',
                'required'    => true,
                'validators'  => array(
                    new Validator\Identical(''),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'Propose Usergroup',
            'type' => '\Zend\Form\Element\Submit',
        ));

        $this->add(array(
            'name' => 'Reset',
            'type' => '\Zend\Form\Element\Reset',
        ));

    }
}