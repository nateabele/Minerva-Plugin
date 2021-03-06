<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \lithium\data\Connections;

// Connections::add('default', array(
// 	'type' => 'database',
// 	'adapter' => 'MySql',
// 	'host' => 'localhost',
// 	'login' => 'root',
// 	'password' => '',
// 	'database' => 'app_name'
// ));

Connections::add('default', array(	
	'type' => 'database',
	'adapter' =>  'MongoDb', 
	'database' => 'minerva', 
	'host' => 'localhost'
));

Connections::add('test', array('type' =>  'MongoDb', 'database' => 'test', 'host' => 'localhost'));

?>
