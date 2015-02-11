<h4>Список техники:</h4>
<div class="helper">Чтобы записаться, выберите технику, на которой будете участвовать.</div>
<form action="<?php echo Route::LocalUrl("?cont=interview&action=join&itrv_id=". $data->itrv_id);?>" method="post" id="joinForm">
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
		<tr data-tank_id="{{~item~.tank_id}}">
			<td>
				<?php if (! $data->isMember() ) : ?>
				<input type="checkbox" name="vehicles[]" value="{{~item~.tank_id}}" />		
				<?php endif;?>
			</td>
			<td><span class="vehicle {{~item~.tvi.type}}">{{~item~.tvi.name_i18n}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.in_garage}}">{{~item~.data.userStat.in_garage}} / {{~item~.data.required.num_required}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.battles}}">{{~item~.data.userStat.battles}} / {{~item~.data.required.battles}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.winrate}}">{{~item~.data.userStat.winrate}} / {{~item~.data.required.winrate}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.avg_damage}}">{{~item~.data.userStat.avg_damage}} / {{~item~.data.required.avg_damage}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.hits_percents}}">{{~item~.data.userStat.hits_percents}} / {{~item~.data.required.hits_percents}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.avg_spotted}}">{{~item~.data.userStat.avg_spotted}} / {{~item~.data.required.avg_spotted}}</span></td>
			<td><span class="vrating-{{~item~.data.ratingColorID.avg_surv}}">{{~item~.data.userStat.avg_surv}} / {{~item~.data.required.avg_surv}}</span></td>
		</tr>
	</tbody>
</table>
<?php if (! $data->isMember() ) : ?>
<input type="submit" name="join" value="Записаться" disabled="disabled" />
<?php endif;?>
</form>

<script type="text/javascript">
	$(function(){
		$('#requiredVehicles input[type=checkbox]').click(function(){
			if (0 === $('#requiredVehicles input[type=checkbox]:checked').length ) {
				$('#joinForm input[type=submit]').attr('disabled', true);
			}
			else {
				$('#joinForm input[type=submit]').attr('disabled', false);
			}
		});
	});
</script>
		
