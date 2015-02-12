<?php include 'blocks/headers.php'; ?>
<div class="main-container">
	<?php include "blocks/auth.php";?>
	<div class="banner"></div>
	<?php include "blocks/menu.php";?>
	<?php include $content_tmpl;?>
	<?php include "blocks/debug.php";?>
</div>
<?php include 'blocks/footers.php';?>