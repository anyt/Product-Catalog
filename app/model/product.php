<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */
require_once(APPLICATION_ROOT . '/app/model/image.php');

/**
 * Namespace for invalidation the cache of product list
 */
define('PRODUCT_CACHE_NAMESPACE', 'product_index');

/**
 * List of fields in product entity
 *
 * @return array
 */
function product_fields_list()
{
    return array(
        'title',
        'description',
        'price',
        'picture_url',
        'picture_filename',
    );
}

/**
 * Load the product entity from the database
 *
 * @param $id
 * @return array|null
 */
function product_load($id)
{
    static $products = array();
    // if image checked before, get cached info from array
    if (in_array($id, array_keys($products))) {
        return $products[$id];
    }

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

/**
 * Save new product to the database
 *
 * @param $product
 * @return int
 */
function product_create($product)
{
    // save image file
    $product['picture_filename'] = image_save($product['picture_url']);

    $mysql = mysql_connection();
    foreach ($product as $key => $value) {
        $product[$key] = '"' . mysqli_real_escape_string($mysql, $value) . '"';
    }
    $product = implode(', ', array_values($product));

    // @todo prevent sort order change in $products
    $res = mysqli_query($mysql, "INSERT INTO products (title, description, price, picture_url, picture_filename) VALUES ({$product})");
    if ($res === false) print mysqli_error($mysql);
    $id = mysqli_insert_id($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);

    return $id;
}

/**
 * Persist the updated product entity information to the database
 *
 * @param $product
 */
function product_update($product)
{
    $old_product = product_load($product['id']);
    // if image is new, save file to disk
    if ($old_product['picture_url'] !== $product['picture_url']) {
        $product['picture_filename'] = image_save($product['picture_url']);
    }
    $mysql = mysql_connection();
    foreach ($product as $key => $value) {
        $product[$key] = '"' . mysqli_real_escape_string($mysql, $value) . '"';
    }
    $res = mysqli_query($mysql, "
            UPDATE products SET
              title={$product['title']},
              description={$product['description']},
              price={$product['price']},
              picture_url={$product['picture_url']},
              picture_filename={$product['picture_filename']}
            WHERE id={$product['id']}");
    if ($res === false) print mysqli_error($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);
}

/**
 * Delete the product form the database
 *
 * @param $id
 */
function product_delete($id)
{
    $id = intval($id);
    $mysql = mysql_connection();
    $res = mysqli_query($mysql, "DELETE FROM products WHERE id={$id}");
    if ($res === false) print mysqli_error($mysql);

    update_cache_namespace(PRODUCT_CACHE_NAMESPACE);
}

/**
 * Prepare product for saving to the database
 *
 * @param $product
 */
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

/**
 * Prepare product data fetched from database to rendering
 *
 * @param $product
 */
function product_denormalize(&$product)
{
    if ($product['price'] === 0) {
        $product['price'] = '';
    } else {
        $product['price'] = format_price($product['price']);
    }
}

/**
 * Validate the product and return an array with errors if so.
 *
 * @param $product
 * @return array
 */
function product_validate($product)
{
    $errors = array();

    // title required
    if (empty($product['title'])) $errors[] = 'Title is required.';
    // description required
    if (empty($product['description'])) $errors[] = 'Description is required.';
    // price required
    if (empty($product['price'])) $errors[] = 'Price is required.';
    // picture_url required
    if (empty($product['picture_url'])) $errors[] = 'Picture url is required.';
    // picture_url valid URL
    if (false === filter_var($product['picture_url'], FILTER_VALIDATE_URL)) $errors[] = 'Picture url is not a valid URL.';
    // picture_url valid image
    $file_info = image_get_info_by_url($product['picture_url']);
    if ($file_info === false) {
        $errors[] = 'Picture url is not a valid file.';
    } elseif (false === image_is_allowed_mime_type($file_info['mime'])) {
        $errors[] = 'Picture url has not supported file type (jpg, png, gif).';
    }

    return $errors;
}


/**
 * Get paginated products list
 *
 * @param int $page
 * @param string $order_by
 * @param string $sort
 * @return array
 */
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
