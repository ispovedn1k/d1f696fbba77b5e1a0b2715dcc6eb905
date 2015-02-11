<?php $squads = array('', '', '', '', '', '');?>

<?php
// расписываем кандидатов по отрядам
foreach( $data->_candidates as $candidate) {
	ob_start();
	?>
		<div class="candidate" data-userid="<?php echo $candidate->user_id;?>">
			<span><?php echo $candidate->personName;?></span>
		</div>
	<?php
	$squads[ $candidate->status ] .= ob_get_clean();
}
/**
 * Сделать сортировку вывода калькулятора техники в отряде.
 * 
 * Сделать сортировку вывода статистики игрока.
 * Добавить цветовую градацию
 */

?>

<table>
	<tr>
		<td>
		<?php for ( $i = 1; $i <= $data->squads_num; $i++ ) : ?>
			<h4>squad #<?php echo $i;?></h4>
			<div class="squad-box" data-squad="<?php echo $i;?>">
				<?php echo $squads[$i];?>
			</div>
		<?php endfor;?>
		</td>
		<td>
		<table id="squad_calc">
			<caption>В отряде техники:</caption>
			<thead>
				<tr>
					<th>Единица</th>
					<th>шт.</th>
					<th>треб.</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><span class="{{~item~.tvi.type}}">{{~item~.tvi.short_name_i18n}}</span></td>
					<td>{{~item~.data.insquad}}</td>
					<td>{{~item~.data.required}}</td>
				</tr>
			</tbody>
		</table>
		</td>
	</tr>
</table>


<input type="button" value="update squads" id="squadsupdate" />
<div class="helper">Перетаскивайте мышью бойцов в отряд или из отряда.</div>
<h4>candidates: </h4>
<table>
<tr>
	<td>
		<div class="squad-box" data-squad="0">
			<?php echo $squads[0];?>
		</div>
	</td>
	<td>
		<table id="candidateInfoStat">
			<thead>
				<tr>
					<th>техника</th>
					<th>боев</th>
					<th>побед</th>
					<th>урон</th>
					<th>попаданий</th>
					<th>обнаружено</th>
					<th>выживаемость</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><span class="{{~item~.tvi.type}}">{{~item~.tvi.short_name_i18n}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.battles}}" title="{{~item~.data.required.battles}}">{{~item~.data.userStat.battles}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.winrate}}" title="{{~item~.data.required.winrate}}">{{~item~.data.userStat.winrate}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.avg_damage}}" title="{{~item~.data.required.avg_damage}}">{{~item~.data.userStat.avg_damage}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.hits_percents}}" title="{{~item~.data.required.hits_percents}}">{{~item~.data.userStat.hits_percents}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.avg_spotted}}" title="{{~item~.data.required.avg_spotted}}">{{~item~.data.userStat.avg_spotted}}</span></td>
					<td><span class="vrating-{{~item~.data.ratingColorID.avg_surv}}" title="{{~item~.data.required.avg_surv}}">{{~item~.data.userStat.avg_surv}}</span></td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
</table>


<!--  -->