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


use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Phpug\Entity\Groupcontact;
use Phpug\Entity\Usergroup;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator;

class UsergroupFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected $serviceLocator = null;

    protected $edit = true;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        parent::__construct();

        $this->serviceLocator = $serviceLocator;

        $em = $serviceLocator->get('doctrine.entitymanager.orm_default');

        $this->setHydrator(new DoctrineObject($em))
             ->setObject(new Usergroup());

        $this->setLabel('Usergroup-Information')
             ->setName('userGroupFieldset');

        $this->add(array(
            'name' => 'id',
            'type' => '\Zend\Form\Element\Hidden',
        ));
        $this->add(array(
            'name'    => 'name',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'      => 'Name',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'What does your usergroup call itself?',
            ),
        ));

        $this->add(array(
            'name'    => 'shortname',
            'type'    => 'Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Short Name',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'Give us a short name that you can then use to refer to your group via http://php.ug/<short name>',
            ),
        ));

        $this->add(array(
            'name'    => 'url',
            'type'    => 'Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Web-Presence',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'Give us a URL where any interested person can find freely available Informations about your Usergroup. This is the URL we will send everyone looking for your usergroup to. So the main informations as next meeting and a contact person should be freely available without any kind of registration',
            ),
        ));

        $this->add(array(
            'name'    => 'location',
            'type' => '\OrgHeiglGeolocation\Form\Element\Geolocation',
            'options' => array(
               'label'       => 'Location',
               'description' => 'Tell us where you are located. Currently you have to provide the information like "50.1234,8.0815" for somewhere near Frankfurt/Main in Germany',
               'required'    => true,
            ),
        ));

        $this->add(array(
            'name'    => 'icalendar_url',
            'type'    => '\Zend\Form\Element\Text',
            'options' => array(
                'label'       => 'Calendar-URL',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'Do you have an Event-Calendar? Tell us where we can point our calendar-app to get our hands onto your events.',
                'required'    => true,
                'validators'  => array(
                    new Validator\Uri(array('allowRelative' => false)),
                ),
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'ugtype',
            'options' => array(
                'label' => 'Type of UG',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'What describes your Usergroup in the best way',
                'object_manager' => $em,
                'target_class' => 'Phpug\Entity\Grouptype',
                'property' => 'name',
                'is_method' => true,
                'find_method' => array(
                    'name' => 'findAll',
                    'params' => array(),
                )
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'state',
            'options' => array(
                'label' => 'Group-State',
                'label_attributes' => array(
                    'class' => 'control-label',
                ),
                'description' => 'In what state is this Usergroup-entry?',
                'value_options' => array(
                    Usergroup::PROMOTED => 'Promoted',
                    Usergroup::ACTIVE   => 'Active',
                    Usergroup::OBSOLETE => 'Obsolete',
                ),
            ),
        ));

        $groupcontactFieldset = new GroupcontactFieldset($this->serviceLocator);
        $this->add(array(
            'type'    => 'Zend\Form\Element\Collection',
            'name'    => 'contacts',
            'options' => array(
            'label'   => 'Contacts',
                'count' => 1,
                'allow_add' => true,
                'allow_remove' => true,
                'target_element' => $groupcontactFieldset,
                'should_create_template' => true,
            )
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
        $em = $this->serviceLocator->get('doctrine.entitymanager.orm_default');

        $shortnameValidator = 'DoctrineModule\Validator\NoObjectExists';
        $errorMessageTemplate = 'objectFound';
        if (true === $this->edit) {
            $shortnameValidator = 'DoctrineModule\Validator\UniqueObject';
            $errorMessageTemplate = 'objectNotUnique';
        }
        return array(
            'name' => array(
                'required' => true,
                'properties' => array(
                    'required' => true,
                ),
            ),
            'shortname' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 10,
                        ),
                    ),
                    array(
                        'name' => $shortnameValidator,
                        'options' => array(
                            'object_repository' => $em->getRepository('Phpug\Entity\Usergroup'),
                            'object_manager' => $em,
                            'fields' => array('shortname'),
                            'messages' => array(
                                $errorMessageTemplate => 'This shortname is already in use.',
                            ),
                        ),
                    ),
                )),
            'location' => array(
                'required' => true,
                'validators' => array(
                    array('name' => 'OrgHeiglGeolocation\Validator\IsGeolocation'),
                )
            ),
            'url' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Uri',
                        'options' => array(
                            'uriHandler' => 'Zend\Uri\Http',
                            'allowRelative' => false,
                        ),
                    ),
                    array(
                        'name' => 'Phpug\Validator\WebsiteExists',
                    ),
                ),
            ),
            'icalendar_url' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Uri',
                        'options' => array(
                            'uriHandler' => 'Zend\Uri\Http',
                            'allowRelative' => false,
                        )
                    ),
                    array('name' => 'Phpug\Validator\IsCalendarUrl'),
                ),
            ),
            'ugtype' => array(
                'required' => true,
            ),
//            'state' => array(
//                'required' => true,
//            ),
        );
    }

    public function remove($elementOrFieldset)
    {
        if ('id' === $elementOrFieldset) {
            $this->edit = false;
        }
        return parent::remove($elementOrFieldset);
    }
}