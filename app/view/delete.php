<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete product</title>

    <link rel="stylesheet" href="/css/pure-min.css">
    <link rel="stylesheet" href="/css/style.css"/>
</head>

<body>
<?php include('include/header.php'); ?>

<div class="content">
    <h1>Delete product #<?php print $product['id']; ?></h1>
</div>
<hr/>
<div class="content">
    <form action="/product/<?php print $product['id']; ?>/delete" class="text-center" method="POST">
        <h2>Are you sure you want to delete this product?</h2>
        <button class="pure-button button-error">Delete</button>
        <a class="pure-button" href="/">Cancel</a>
    </form>
</div>
</body>
</html>