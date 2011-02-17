<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;

Libraries::add('blog', array('path' => MINERVA_LIBRARY_PATH));

//Libraries::add('example'); <-- example of a completely unrelated library

Libraries::add('li3_access');

Libraries::add('al13_helpers', array('path' => MINERVA_LIBRARY_PATH));
Libraries::add('util', array('path' => MINERVA_LIBRARY_PATH));
Libraries::add('li3_flash_message', array('path' => MINERVA_LIBRARY_PATH));
/*
Libraries::add('li3_assets', array(
    'config' => array(
       'js' => array(
                     'compression' => 'jsmin', // possible values: 'jsmin', 'packer', false (true uses jsmin)
                     'output_directory' => 'optimized', // directory is from webroot/css if full path is not defined
                     'packer_encoding' => 'Normal', // level of encoding (only used for packer), possible values: 0,10,62,95 or 'None', 'Numeric', 'Normal', 'High ASCII'
                     'packer_special_chars' => true
                    ),
       'css' => array(
                      'compression' => 'tidy', // possible values: true, 'tidy', false
                      'tidy_template' => 'highest_compression',
                      'less_debug' => false, // debugs lessphp writing messages to a log file, possible values: true, false
                      'output_directory' => 'optimized' // directory is from webroot/css if full path is not defined
                   ),
       'image' => array(
                        'compression' => true, // uses base64/data uri, possible values: true, false
                        'allowed_formats' => array('jpeg', 'jpg', 'jpe', 'png', 'gif') // which images to base64 encode
                       )
       )
   )
);
*/


?>