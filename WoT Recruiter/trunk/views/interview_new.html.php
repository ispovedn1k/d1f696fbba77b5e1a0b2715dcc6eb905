<?php global $nations, $vtypes;?>
<form action="?cont=interview&action=create" method="post">
<table class="interview_new">
<caption>Создайте новый отряд</caption>
	<tbody>
	<tr>
		<td colspan="3">
			<label>Имя отряда: <br />
				<input type="text" name="int_name" />
			</label>
			<input type="submit" name="gogo" />
			<select><option>цель создания</option></select>
		</td>
	</tr>
	<tr>
		<td width="20%">
		<label>Нация:<br />
			<select class="filter nation" name="nation">
				<option value="all"><?php echo i18n("allNations")?></option>
				<?php foreach ( $nations as $nation ) :?>
				<option value="<?php echo $nation;?>"><?php echo i18n($nation);?></option>
				<?php endforeach;?>
			</select>
		</label>
		<br />
		<label>Тип:<br />
			<select class="filter type" name="type">
				<option value="all"><?php echo i18n("allTypes");?></option>
				<?php foreach ( $vtypes as $type ) :?>
				<option value="<?php echo $type;?>"><?php echo i18n($type);?></option>
				<?php endforeach;?>
			</select>
		</label>
		<br />
		<label>Уровень:<br />
			<select multiple="multiple" class="filter level" name="level" size="11">
				<option value="all"><?php echo i18n("allLevels");?></option>
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
				<option>6</option>
				<option>7</option>
				<option>8</option>
				<option>9</option>
				<option>10</option>
			</select> 
		</label>
		<br />
		</td>
		<td width="40%">
		<div class="vehiclesbox">
		<?php foreach ( $data as $vehicle ) : ?>
			<div data-type="<?php echo $vehicle->type;?>" data-nation="<?php echo $vehicle->nation;?>" data-level="<?php echo $vehicle->lvl;?>"
				class="vehicle <?php echo $vehicle->type;?>"><label>
				<input type="checkbox" name="vehicles[]" value="<?php echo $vehicle->tank_id;?>" />
				<?php echo $vehicle->name_i18n;?>
			</label></div>
		<?php endforeach;?>
		</div>
		</td>
		<td width="40%">filters
			<label>Кол-во боев:
				<input type="text" />
			</label>
		</td>
	</tr>
	</tbody>
</table>
</form>

<script type="text/javascript">
var x = new VehiclesFilter();
x.init();
</script>