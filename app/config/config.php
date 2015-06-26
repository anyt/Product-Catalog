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
    'password' => ''
);

$config['items_per_page'] = 10;