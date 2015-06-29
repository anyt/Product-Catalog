<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

require_once(APPLICATION_ROOT . '/app/model/product.php');

$product = product_load($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    product_delete($product['id']);
    redirect('');
}

return render_template('delete.php', array(
    'product' => $product,
));