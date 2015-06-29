<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

$config = array();
require 'routes.php';

$config['mysql'] = array(
    'host' => 'localhost',
    'database' => 'product_catalog',
    'user' => 'root',
    'password' => '48'
);

$config['memcache'] = array(
    'host' => '127.0.0.1',
    'port' => 11211
);

$config['items_per_page'] = 10;