<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */
define('BASE_URL', 'http://' . $_SERVER["SERVER_NAME"] . '/');


function handle_request()
{
    $uri = current_uri();
    $route = get_route_by_uri($uri);
    render_controller_by_route($route);
}

function current_uri()
{
    static $uri = null;
    if (is_null($uri)) {
        $uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );
        $uri = trim($uri, "/");
    }
    return $uri;
}


function get_route_by_uri($uri)
{
    $routes = get_config('routes');
    foreach ($routes as $route) {

        if (!$match = preg_match("/^" . $route['path'] . "$/", $uri, $matches)) {
            continue;
        }
        array_shift($matches);
        if (isset($route['params'])) {
            $route['params'] = array_combine($route['params'], $matches);
        }
        return $route;
    }
    return false;
}

function render_controller_by_route($route)
{
    if (isset($route['params'])) {
        extract($route['params']);
    }
    print require_once(APPLICATION_ROOT . '/app/controller/' . $route['controller']);

}

function render_template($template, $params = array())
{
    if ($params) extract($params);

    ob_start();
    require_once(APPLICATION_ROOT . '/app/view/' . $template);
    return ob_get_clean();
}

function get_config($key)
{
    static $config = array();
    if (empty($config)) {
        require_once(APPLICATION_ROOT . '/app/config/config.php');
    }
    return isset($config[$key]) ? $config[$key] : null;
}

function mysql_connection()
{
    static $connection = null;
    if (is_null($connection)) {
        $config = get_config('mysql');
        $connection = mysqli_connect(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']) or die ("Connection error.");
    }
    return $connection;
}

function memcache_connection()
{
    static $connection = null;
    if (is_null($connection)) {
        $config = get_config('memcache');
        $connection = memcache_connect($config['host'], $config['port']);
    }
    return $connection;
}


function format_price($price)
{
    return number_format($price / 10, 2);
}