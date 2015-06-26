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

$res = mysqli_query(mysql_connection(), "SELECT * FROM products ORDER BY {$order_by} DESC LIMIT {$offset}, {$items_per_page}");
if ($res === false) print mysqli_error(mysql_connection());

$products = array();
while ($result = mysqli_fetch_assoc($res)) {
    $products[] = $result;

}

$query = array('page' => $page + 1);
if ($order_by === 'price') $query['order_by'] = $order_by;

$next_page_link = BASE_URL . '?' . http_build_query($query);
return render_template('index.php', array(
    'products' => $products,
    'next_page_link' => $next_page_link
));