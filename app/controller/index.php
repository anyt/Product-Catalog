<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */


// get order
if (isset($_GET['order_by']) && $_GET['order_by'] === 'price') {
    $order_by = 'price';
} else {
    $order_by = 'id';
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = get_config('items_per_page');
$offset = $page * $items_per_page;

$memcache = memcache_connection();

$cache_key = 'product_index__order_by_' . $order_by . '_page_' . $page;

$cached_result = memcache_get($memcache, $cache_key);
if (!$cached_result) {
    $mysql = mysql_connection();
    $res = mysqli_query($mysql, "SELECT * FROM products ORDER BY {$order_by} DESC LIMIT {$offset}, {$items_per_page}");
    if ($res === false) print mysqli_error($mysql);

    $products = array();
    while ($result = mysqli_fetch_assoc($res)) {
        $products[] = $result;

    }
    memcache_set($memcache, $cache_key, $products, 0, 0);
} else {
    $products = $cached_result;
}

if (count($products) === $items_per_page) {
    $query = array('page' => $page + 1);
    if ($order_by === 'price') $query['order_by'] = $order_by;

    $next_page_link = BASE_URL . '?' . http_build_query($query);
} else {
    $next_page_link = false;
}
return render_template('index.php', array(
    'products' => $products,
    'next_page_link' => $next_page_link
));