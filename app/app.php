<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */


/**
 * Project URL
 */
define('BASE_URL', 'http://' . $_SERVER["SERVER_NAME"] . '/');

/**
 * Memcache prefix, used for cache invalidation
 */
define('MEMCACHE_NAMESPACE_PREFIX', 'namespace:');


/**
 * Match the controller by route and render it's content
 */
function handle_request()
{
    $uri = current_uri();
    $route = get_route_by_uri($uri);
    if (!$route) {
        page_not_found();
    }
    render_controller_by_route($route);
}

/**
 * Current page URI
 *
 * @return string
 */
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

/**
 * Safely get $_GET parameter
 *
 * @param string $name
 * @param string $default
 * @param array $allowedValues
 * @return null|string
 */
function get_uri_param($name, $default = null, $allowedValues = array())
{
    $value = isset($_GET[$name]) ? $_GET[$name] : null;
    if (!is_null($value) &&
        (
            empty($allowedValues) || !empty($allowedValues) && in_array($value, $allowedValues)
        )
    ) {
        return $value;
    }
    return $default;
}

/**
 * Match configured route with URI
 *
 * @param string $uri
 * @return false|array
 */
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

/**
 * Render controller with parameters by route
 *
 * @param $route
 */
function render_controller_by_route($route)
{
    if (isset($route['params'])) {
        extract($route['params']);
    }
    print require_once(APPLICATION_ROOT . '/app/controller/' . $route['controller']);

}

/**
 * Redirect to URL
 *
 * @param $url
 */
function redirect($url)
{
    header('Location: ' . BASE_URL . $url);
    die();
}

/**
 * Render template from app/view folder with parameters
 *
 * @param string $template
 * @param array $params
 * @return string
 */
function render_template($template, $params = array())
{
    if ($params) extract($params);

    ob_start();
    require_once(APPLICATION_ROOT . '/app/view/' . $template);
    return ob_get_clean();
}

/**
 * Get config section by key
 *
 * @param string $key
 * @return null|array
 */
function get_config($key)
{
    static $config = array();
    if (empty($config)) {
        require_once(APPLICATION_ROOT . '/app/config/config.php');
    }
    return isset($config[$key]) ? $config[$key] : null;
}

/**
 * Get mysqli connection
 *
 * @return mysqli
 */
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

/**
 * Get memcached connection
 */
function memcache_connection()
{
    static $connection = null;
    if (is_null($connection)) {
        $config = get_config('memcache');
        $connection = memcache_connect($config['host'], $config['port']);
    }
    return $connection;
}

/**
 *  Get cache namespace by index
 *
 * @param string $index
 * @return string
 */
function get_cache_namespace($index)
{
    $namespace = MEMCACHE_NAMESPACE_PREFIX . $index;
    $memcache = memcache_connection();
    $counter = memcache_get($memcache, $namespace);
    $counter = $counter ? $counter : 1;
    return $index . ':' . $counter;
}

/**
 * Increment cache namespace value for cache invalidation
 *
 * @param $index
 */
function update_cache_namespace($index)
{
    if ($memcache = memcache_connection()) {
        $namespace = MEMCACHE_NAMESPACE_PREFIX . $index;
        if (!$counter = memcache_get($memcache, $namespace)) {
            memcache_set($memcache, $namespace, 1);
        }
        memcache_increment($memcache, $namespace);
    }
}

/**
 * Convert price from cents to dollars
 *
 * @param $price
 * @return string
 */
function format_price($price)
{
    return number_format($price / 100, 2);
}

/**
 * Display 404 page
 */
function page_not_found()
{
    http_response_code(404);
    print render_template('404.php');
    die;
}

/**
 * Render next and previous page links based on items count
 *
 * @param array $items
 * @return string
 */
function paginator($items)
{
    $page = intval(get_uri_param('page', 1));

    $uri = current_uri();
    $items_per_page = get_config('items_per_page');
    $query = $_GET;
    // next page link
    if (count($items) === $items_per_page) {
        $query['page'] = $page + 1;
        $next_page_link = $uri . '?' . http_build_query($query);
    } else {
        $next_page_link = false;
    }

    // previous page link
    if ($page > 1) {
        $query['page'] = $page - 1;
        $previous_page_link = $uri . '?' . http_build_query($query);
    } else {
        $previous_page_link = false;
    }
    if (false === $next_page_link && false === $previous_page_link) {
        return '';
    }
    return render_template('include/paginator.php', array(
        'next_link' => $next_page_link,
        'previous_link' => $previous_page_link
    ));
}