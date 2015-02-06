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
			<?php /* @warning:
				Мне не нравится, что JS-переменная candidatesStat определена в другом файле.
				*/
				$user_id = Engine::getInstance()->user->id;
			?>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.in_garage}}  / {{~item~.num_required}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.battles}} / {{~item~.battles}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.winrate}} / {{~item~.winrate}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.avg_damage}} / {{~item~.avg_damage}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.hits_percents}} / {{~item~.avg_hits}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.avg_spotted}} / {{~item~.avg_spotted}}</td>
			<td>{{candidatesStat.sortedByUserID.<?php echo $user_id;?>.all.~key~.avg_surv}} / {{~item~.avg_surv}}</td>
		</tr>
	</tbody>
</table>
<?php if (! $data->isMember() ) : ?>
<input type="submit" name="join" value="Записаться" />
<?php endif;?>
</form>