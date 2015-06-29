<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

require_once(APPLICATION_ROOT . '/app/model/product.php');

$product = product_load($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validate and submit the form
    $product = array_merge($product, $_POST['product']);
    product_normalize($product);
    $errors = product_validate($product);
    if (count($errors) === 0) {
        product_update($product);
        redirect('');
    }
}
// prepare data for rendering
product_denormalize($product);

return render_template('edit.php', array(
    'product' => $product,
    'action' => '/product/' . $product['id'] . '/edit',
    'errors' => isset($errors) ? $errors : false
));