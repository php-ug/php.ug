<?php

namespace OrgHeiglContact;

return array(
	'OrgHeiglContact' => array(
		'mail_transport' => array(
            'class'   => 'Zend\Mail\Transport\Smtp',
            'options' => array(
                'host'             => 'localhost',
                //'port'             => 587,
                //'connectionClass'  => 'login',
                //'connectionConfig' => array(
                //    'ssl'      => 'tls',
                //    'username' => 'contact@your.tld',
                //    'password' => 'password',
				   //),
				),
// 			'class'  => 'Zend\Mail\Transport\File',
// 			'options' => array(
// 				'path' => sys_get_temp_dir(),
//  			),
		),
		'message' => array(
				
			 // These can be either a string, or an array of email => name pairs
			'to'     => 'you@example.org',
			'from'   => 'you@example.org',
			// This should be an array with minimally an "address" element, and
			// can also contain a "name" element
// 			'sender' => array(
// 					'address' => 'contact@your.tld'
// 			),
		),
	),
);