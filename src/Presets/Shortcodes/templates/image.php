<?php if ($url) { ?>

  <figure class="media-wrap is-image">
    
    <img alt="<?php echo $alt; ?>" src="<?php echo $url; ?>">

    <?php if ($caption) { ?>
      <figcaption class="caption"><?php echo $caption; ?></figcaption>
    <?php } ?>

  </figure>

<?php }