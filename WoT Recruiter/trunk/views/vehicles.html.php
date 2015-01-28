<h4>Список техники:</h4>
<?php if (! $data->isMember() ) : ?> 
<form action="?cont=interview&action=join" method="post">
	<input type="hidden" name="itrv_id" value="<?php echo $data->itrv_id;?>" />
	<div class="vehiclesbox">
		<?php foreach ( $data->a_vehicles as $vehicle ) : ?>
			<div data-type="<?php echo $vehicle->type;?>" data-nation="<?php echo $vehicle->nation;?>" data-level="<?php echo $vehicle->lvl;?>"
				class="vehicle <?php echo $vehicle->type;?>"><label>
				<input type="checkbox" name="vehicles[]" value="<?php echo $vehicle->tank_id;?>" />
				<?php echo $vehicle->name_i18n;?>
			</label></div>
		<?php endforeach;?>
	</div>
	
	<input type="submit" name="join" value="Записаться" />
</form>
<?php else : ?>
<div class="vehiclesbox">
	<?php foreach ( $data->a_vehicles as $vehicle ) : ?>
		<div data-type="<?php echo $vehicle->type;?>" data-nation="<?php echo $vehicle->nation;?>" data-level="<?php echo $vehicle->lvl;?>"
			class="vehicle <?php echo $vehicle->type;?>">
			<label><?php echo $vehicle->name_i18n;?></label>
		</div>
	<?php endforeach;?>
</div>
<?php endif;?>