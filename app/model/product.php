<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

define('PRODUCT_CACHE_NAMESPACE', 'product_index');

function product_get_paginated($page, $order_by, $sort)
{
    $items_per_page = get_config('items_per_page');
    $offset = $page * $items_per_page;
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
