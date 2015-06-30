<ul class="paginator">
    <?php if ($next_link) : ?>
        <li><a class="pure-button paginator-next" href="<?php print $next_link; ?>">Next page &#x2192;</a></li>
    <?php endif; ?>
    <?php if ($previous_link) : ?>
        <li><a class="pure-button paginator-previous" href="<?php print $previous_link; ?>">&#x2190; Previous page</a></li>
    <?php endif; ?>
</ul>