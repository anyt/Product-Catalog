<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

require_once(APPLICATION_ROOT . '/app/model/product.php');


// fetch $_GET params
$page = intval(get_uri_param('page', 1));
$order_by = get_uri_param('order_by', 'id', array('id', 'price'));
$sort = get_uri_param('sort', 'desc', array('desc', 'asc'));

// get products list
$products = product_get_paginated($page, $order_by, $sort);

//generate next page link
$items_per_page = get_config('items_per_page');
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