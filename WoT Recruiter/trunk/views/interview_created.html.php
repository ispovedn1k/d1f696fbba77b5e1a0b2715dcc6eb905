<h4>data</h4>
<pre><?php print_r( $data );?></pre>
<?php if ($data['last_action'] === "created_successfully") :?>
<a href="<?php echo ROOT_URI;?>?cont=interview&action=show&itrv_id=<?php echo $data['data'];?>">View team</a>
<?php endif;?>