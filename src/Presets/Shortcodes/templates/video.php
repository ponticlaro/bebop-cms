<?php if ($source && ($id || $url)) { ?> 
  
  <figure class="media-wrap is-video">

    <?php 

    if (in_array($source, ['upload', 'remote'])) { ?>

      <video controls src="<?php echo $url; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></video>

    <?php }

    elseif ($source == 'vimeo') { ?>

      <iframe src="https://player.vimeo.com/video/<?php echo $id; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        
    <?php }

    elseif ($source == 'youtube'){ ?>

      <iframe src="https://www.youtube.com/embed/<?php echo $id; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

    <?php } ?>

    <?php if ($caption) { ?>
      <figcaption class="media-caption"><?php echo $caption; ?></figcaption>
    <?php } ?> 

  </figure>

<?php }