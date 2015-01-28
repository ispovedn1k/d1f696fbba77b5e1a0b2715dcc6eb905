<?php if ("3916664" === Engine::getInstance()->user->id):?>
<div class="spoiler">
	<div class="spoiler-head"><h4>data</h4></div>
	<div class="spoiler-body"><pre><?php print_r( $data );?></pre></div>
</div>

<div class="spoiler">
	<div class="spoiler-head"><h4>$_GET</h4></div>
	<div class="spoiler-body"><pre><?php print_r($_GET);?></pre></div>
</div>

<div class="spoiler">
	<div class="spoiler-head"><h4>$_POST</h4></div>
	<div class="spoiler-body"><pre><?php print_r($_POST);?></pre></div>
</div>

<div class="spoiler">
	<div class="spoiler-head"><h4>Engine</h4></div>
	<div class="spoiler-body"><pre><?php print_r( Engine::getInstance() );?></pre></div>
</div>

<div class="spoiler">
	<div class="spoiler-head"><h4>Cookies</h4></div>
	<div class="spoiler-body"><pre><?php print_r($_COOKIE);?></pre></div>
</div>
<?php endif;?>