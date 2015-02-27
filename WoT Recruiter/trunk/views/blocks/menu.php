<ul class="menu main-menu">
	<li class="<?php echo ($this->menu_pointer === "home" ? "active" : '');?>">
		<a href="?"><span>Главная</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "create" ? "active" : '');?>">
		<a href="?cont=interview"><span>Создать отряд</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "mysquads" ? "active" : '');?>">
		<a href="?cont=search&action=mine"><span>Мои отряды</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "search" ? "active" : '');?>">
		<a href="?cont=search&action=find"><span>Найти отряд</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "tournaments" ? "active" : '');?>">
		<a href="?#"><span>Турниры</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "fort" ? "active" : '');?>">
		<a href="?cont=slackers&action=show"><span>Укрепрайон</span></a>
	</li>
	<li class="<?php echo ($this->menu_pointer === "about" ? "active" : '');?>">
		<a href="?action=about"><span>О проекте</span></a>
	</li>
</ul>