<?php include 'blocks/headers.php'; ?>
<div class="top-menu-container"><?php include "blocks/auth.php";?></div>
<div class="main-container">
	<?php include "blocks/menu.php";?>
	<div class="content-wrapper">
		<?php include $content_tmpl;?>
		<?php include "blocks/debug.php";?>
	</div>
	
	<div class="banner"></div>
</div>
<?php include 'blocks/footers.php';?>