<?php if ( UserAuth::AUTH_SUCCESS === Engine::getInstance()->user->getStatus() ) : ?>
	<a href="#" class="authblock">Logout</a>
	<a href="#" class="authblock"><?php echo Engine::getInstance()->user->personName;?></a>
<?php else :?>
	<a href="?cont=auth" title="Auth" class="authblock">Войти</a>
<?php endif;?>