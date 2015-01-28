<h4>teams</h4>
<ul>
<?php foreach ($data as $team) :?>
	<li>
		<a href="?cont=interview&action=show&itrv_id=<?php echo $team['itrv_id'];?>">
			<?php echo $team['itrv_name'];?>
		</a>
	</li>
<?php endforeach;?>
</ul>