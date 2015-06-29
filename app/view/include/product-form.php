<?php if ($errors): ?>
    <ul class="form-errors">
        <?php foreach ($errors as $error): ?>
            <li>
                <?php print $error; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<form class="pure-form pure-form-stacked" method="POST" action="<?php print $action; ?>" novalidate>
    <div class="pure-control-group">
        <label for="product_title">Title</label>
        <input class="pure-input-1" id="product_title" name="product[title]" type="text"
               value="<?php print $product['title'] ?>">
    </div>
    <div class="pure-control-group">
        <label for="product_description">Description</label>
                    <textarea class="pure-input-1" name="product[description]" id="product_description" cols="30"
                              rows="10"><?php print $product['description'] ?></textarea>
    </div>
    <div class="pure-control-group">
        <label for="product_price">Price ($)</label>
        <input class="pure-input-1" name="product[price]" id="product_price" type="text"
               value="<?php print $product['price'] ?>" placeholder="0.00">
    </div>
    <div class="pure-control-group">
        <label for="product_picture_url">Picture</label>
        <input class="pure-input-1" name="product[picture_url]" id="product_picture_url" type="url"
               value="<?php print $product['picture_url'] ?>" placeholder="Picture url http://...">
    </div>

    <button type="submit" class="pure-button pure-button-primary">Save</button>
    <a class="pure-button" href="/">Cancel</a>
</form>