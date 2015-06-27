<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product catalog</title>

    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="/css/style.css"/>
</head>

<body>
<div class="content">
    <h1>Products</h1>
    <!--change products order-->
    <?php if (isset($_GET['order_by']) && $_GET['order_by'] === 'price'): ?>
        <a href="<?php print current_uri() . '/' ?>">order by id</a>
    <?php else: ?>
        <a href="<?php print current_uri() . '/?order_by=price' ?>">order by price</a>
    <?php endif; ?>
</div>
<hr/>
<div class="content">
    <div class="pure-g">
        <?php foreach ($products as $product): ?>
            <section class="product">
                <div class="pure-u-1-3">
                    <div class="product-image">
                        <?php if ($product['picture_url']): ?>
                            <img class="pure-img" src="<?php print $product['picture_url']; ?>">
                        <?php else: ?>
                            <img class="pure-img" src="/images/placeholder.png">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pure-u-2-3">
                    <h2><?php print $product['title'] ?></h2>

                    <h3>Price: $<?php print format_price($product['price']) ?></h3>

                    <p>
                        <?php print $product['description'] ?>
                    </p>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <!--pagination-->
    <?php if ($next_page_link): ?>
        <div class="paginator">
            <a href="<?php print $next_page_link ?>">Next page</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>