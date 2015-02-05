<h4>Список техники:</h4>
<form action="<?php echo Route::LocalUrl("?cont=interview&action=join&itrv_id=". $data->itrv_id);?>" method="post">
<table id="requiredVehicles">
	<caption>Ваши характиристики / требуемый минимум:</caption>
	<thead>
		<tr>
			<th></th>
			<th>техника</th>
			<th>количество</th>
			<th>боев</th>
			<th>побед</th>
			<th>урон</th>
			<th>попаданий</th>
			<th>обнаружено</th>
			<th>выживаемость</th>
		</tr>
	</thead>
	<tbody>
		<tr data-tank_id="{{~key~}}">
			<td>
				<?php if (! $data->isMember() ) : ?>
				<input type="checkbox" name="vehicles[]" value="{{~key~}}"/>
				<?php endif;?>
			</td>
			<td><span class="vehicle {{tankopedia.vehicles.~key~.type}}">{{tankopedia.vehicles.~key~.name_i18n}}</span></td>
			<td>/ {{~item~.num_required}}</td>
			<td>/ {{~item~.battles}}</td>
			<td>/ {{~item~.winrate}}</td>
			<td>/ {{~item~.avg_damage}}</td>
			<td>/ {{~item~.avg_hits}}</td>
			<td>/ {{~item~.avg_spoted}}</td>
			<td>/ {{~item~.avg_surv}}</td>
		</tr>
	</tbody>
</table>
<?php if (! $data->isMember() ) : ?>
<input type="submit" name="join" value="Записаться" />
<?php endif;?>
</form>

<script type="text/javascript">
	$('#requiredVehicles tbody').Container(<?php echo json_encode( $data->a_vehicles );?>);
</script>