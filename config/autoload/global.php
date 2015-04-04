<?php

return array(
    'db' => array(
	'driver' => 'Pdo',
	'dsn' => 'mysql:dbname=subscribe;hostname=localhost',
	'username' => 'root',
	'password' => 'root',
	'driver_options' => array(
	    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
	),
    ),
    'service_manager' => array(
	'factories' => array(
	    'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
	),
    ),
    'module_layouts' => array(
	'Application' => 'layout/layout'
    ),
    'session' => array(
	'config' => array(
	    'class' => 'Zend\Session\Config\SessionConfig',
	    'options' => array(
	    ),
	),
	'storage' => 'Zend\Session\Storage\SessionArrayStorage',
	'validators' => array(
	    'Zend\Session\Validator\RemoteAddr',
	    'Zend\Session\Validator\HttpUserAgent',
	),
    ),
);
