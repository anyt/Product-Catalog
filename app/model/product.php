<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

define('PRODUCT_CACHE_NAMESPACE', 'product_index');

function product_fields_list()
{
    return array(
        'title',
        'description',
        'price',
        'picture_url'
    );
}

function product_load($id)
{
    $id = intval($id);
    $mysql = mysql_connection();
    $res = mysqli_query($mysql, "SELECT * FROM products WHERE id = {$id}");
    if ($res === false) print mysqli_error($mysql);

    $product = mysqli_fetch_assoc($res);
    if (null === $product) {
        page_not_found();
    }
    return $product;
}

function product_create($product)
{
    $mysql = mysql_connection();
    foreach ($product as $key => $value) {
        $product[$key] = '"' . mysqli_real_escape_string($mysql, $value) . '"';
    }
    $product = implode(', ', array_values($product));

    $res = mysqli_query($mysql, "INSERT INTO products (title, description, price, picture_url) VALUES ({$product})");
    if ($res === false) print mysqli_error($mysql);
    $id = mysqli_insert_id($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);

    return $id;
}

function product_update($product)
{
    $mysql = mysql_connection();
    foreach ($product as $key => $value) {
        $product[$key] = '"' . mysqli_real_escape_string($mysql, $value) . '"';
    }
    $res = mysqli_query($mysql, "
            UPDATE products SET
              title={$product['title']},
              description={$product['description']},
              price={$product['price']},
              picture_url={$product['picture_url']}
            WHERE id={$product['id']}");
    if ($res === false) print mysqli_error($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);
}

function product_delete($id)
{
    $id = intval($id);
    $mysql = mysql_connection();
    $res = mysqli_query($mysql, "DELETE FROM products WHERE id={$id}");
    if ($res === false) print mysqli_error($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);
}

function product_normalize(&$product)
{
    $fields = product_fields_list();
    foreach ($product as $key => $value) {
        if (!in_array($key, $fields)) { // remove all extra fields
            unset($product[$key]);
        }
        $product[$key] = trim($value);
    }
    $product['price'] = intval(str_replace(',', '', $product['price']) * 100);
}

function product_denormalize(&$product)
{
    if ($product['price'] === 0) {
        $product['price'] = '';
    } else {
        $product['price'] = format_price($product['price']);
    }
}

function product_validate($product)
{
    $errors = array();

    // title
    if (empty($product['title'])) $errors[] = 'Title is required.';
    // description
    if (empty($product['description'])) $errors[] = 'Description is required.';
    // price
    if (empty($product['price'])) $errors[] = 'Price is required.';
    // picture_url
    if (empty($product['picture_url'])) $errors[] = 'Picture url is required.';
    if (false === filter_var($product['picture_url'], FILTER_VALIDATE_URL)) $errors[] = 'Picture url is not a valid URL.';

    return $errors;
}


function product_list($page, $order_by, $sort)
{
    $items_per_page = get_config('items_per_page');
    $offset = ($page - 1) * $items_per_page;
    $sort = strtoupper($sort);

    // get cached result if exists
    if ($memcache = memcache_connection()) {
        $cache_namespace = get_cache_namespace(PRODUCT_CACHE_NAMESPACE);
        $cache_key = $cache_namespace . ':' . md5(serialize(func_get_args()));

        if ($cached_result = memcache_get($memcache, $cache_key)) {
            return $cached_result;
        }
    }
    $mysql = mysql_connection();
    $res = mysqli_query($mysql, "SELECT * FROM products ORDER BY {$order_by} {$sort} LIMIT {$offset}, {$items_per_page}");
    if ($res === false) print mysqli_error($mysql);

    $products = array();
    while ($result = mysqli_fetch_assoc($res)) {
        $products[] = $result;
    }

    // cache result
    if ($memcache = memcache_connection()) {
        memcache_set($memcache, $cache_key, $products, 0, 0);
    }

    return $products;
}
