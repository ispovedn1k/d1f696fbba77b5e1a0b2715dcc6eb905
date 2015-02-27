<div class="menu top-menu">
<?php if ( UserAuth::AUTH_SUCCESS === Engine::getInstance()->user->getStatus() ) : ?>
	<a href="?cont=auth&action=logout" class="authblock">Logout</a>
	<a href="#" class="authblock <?php echo (Engine::getInstance()->user->isStatUp2Date() ? "up2date" : "obsolete");?>">
		<?php echo Engine::getInstance()->user->personName;?>
		<span class="popupHelp">
		<?php echo (Engine::getInstance()->user->isStatUp2Date() ? "Ваша статистика актуальна" : "Ваша статистика устарела. Обновляется.");?>
		</span>
	</a>
<?php else :?>
	<a href="?cont=auth" title="Auth" class="authblock">Войти</a>
<?php endif;?>
<?php include 'queue.php';?>
</div>