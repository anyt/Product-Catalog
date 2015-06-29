<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

$config['routes'] = array();
$config['routes'][] = array(
    'path' => '',
    'controller' => 'index.php'
);
$config['routes'][] = array(
    'path' => 'product\/create',
    'controller' => 'create.php'
);
$config['routes'][] = array(
    'path' => 'product\/(\d+)\/edit',
    'controller' => 'edit.php',
    'params' => ['id']
);
$config['routes'][] = array(
    'path' => 'product\/(\d+)\/delete',
    'controller' => 'delete.php',
    'params' => ['id']
);
