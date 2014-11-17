<div class="container">
<?
$id = $post->ID;
?>
<dl class="dl-horizontal">
  <dt>Date:</dt>
  <dd><?=get_field("date",$id)?></dd>
</dl>
<dl class="dl-horizontal">
  <dt>Venue:</dt>
  <dd><?=get_field("venue",$id)?></dd>
</dl>
<dl class="dl-horizontal">
  <dt>Organizer:</dt>
  <dd><?=get_field("organizer",$id)?></dd>
</dl>
<?php the_content(); ?>
</div>