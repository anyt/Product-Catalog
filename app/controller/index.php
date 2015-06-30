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
$products = product_list($page, $order_by, $sort);

return render_template('index.php', array(
    'products' => $products
));