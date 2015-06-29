<?php
/**
 * @author Andrey Yatsenco <yatsenco@gmail.com>
 */

require_once(APPLICATION_ROOT . '/app/model/product.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $product = array(
            'title' => '',
            'description' => '',
            'price' => 0,
            'picture_url' => ''
        );
        break;
    case 'POST':
        // validate and submit the form
        $product = $_POST['product'];
        product_normalize($product);
        $errors = product_validate($product);
        if (count($errors) === 0) {
            $id = product_create($product);
            redirect('');
        }
        break;
}
// prepare data for rendering
product_denormalize($product);

return render_template('create.php', array(
    'product' => $product,
    'action' => '/product/create',
    'errors' => isset($errors) ? $errors : false
));