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
    'path' => 'admin',
    'controller' => 'admin/index.php'
);
$config['routes'][] = array(
    'path' => 'admin\/create',
    'controller' => 'admin/create.php'
);
$config['routes'][] = array(
    'path' => 'admin\/(\d+)\/edit',
    'controller' => 'admin/edit.php',
    'params' => ['id']
);
$config['routes'][] = array(
    'path' => 'admin\/(\d+)\/delete',
    'controller' => 'admin/delete.php',
    'params' => ['id']
);
