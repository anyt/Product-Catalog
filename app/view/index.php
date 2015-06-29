<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product catalog</title>

    <link rel="stylesheet" href="/css/pure-min.css">
    <link rel="stylesheet" href="/css/style.css"/>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="content">
    <div class="pure-menu pull-right">
        <ul id="order-menu" class="pure-menu-list pure-menu-horizontal">
            <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
                <a class="pure-button" href="#" id="menuLink1" class="pure-menu-link">Order by</a>
                <ul class="pure-menu-children">
                    <li class="pure-menu-item"><a href="/" class="pure-menu-link">
                            id &#x2191;</a></li>
                    <li class="pure-menu-item"><a href="/?sort=asc" class="pure-menu-link">
                            id &#x2193;</a></li>
                    <li class="pure-menu-item"><a href="/?order_by=price" class="pure-menu-link">
                            price &#x2191;</a></li>
                    <li class="pure-menu-item"><a href="/?order_by=price&sort=asc" class="pure-menu-link">
                            price &#x2193;</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <h1>Products</h1>
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

                    <p>
                        <a class="pure-button button-secondary"
                           href="/product/<?php print $product['id']; ?>/edit">Edit</a>
                        <a class="pure-button button-error"
                           href="/product/<?php print $product['id']; ?>/delete">Delete</a>
                    </p>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <!--pagination-->
    <?php if ($next_page_link): ?>
        <div class="paginator">
            <a class="pure-button" href="<?php print $next_page_link ?>">Next page ></a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>