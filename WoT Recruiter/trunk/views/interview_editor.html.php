<?php global $nations, $vtypes;?>
<form action="?cont=interview&action=create" method="post">
<table class="interview_new">
<caption>Создайте новый отряд</caption>
	<tbody>
	<tr>
		<td>
			<label>Позывной отряда: <br />
				<input type="text" name="itrv_name" id="itrv_name" />
				<input type="hidden" name="itrv_id" id="itrv_id" />
			</label>
			<br />
			<select><option>цель создания</option></select>
			<div class="radiogroup"><label>Видимость команды:</label><br />
				<label><input type="radio" name="visability" value="all" checked="checked"/>видна всем</label><br />
				<label><input type="radio" name="visability" value="clan" />только бойцам клана</label><br />
				<label><input type="radio" name="visability" value="invite" />только приглашенным</label><br />
			</div>
		</td>
		<td>Запрошенная Вами техника:
			<div id="selectedVehicles"></div>
		</td>
		<td>
			<div class="helper">Добавьте описание</div>
			<textarea id="itrv_comment" name="itrv_comment" class="commentbox"></textarea>
			<input type="button" value="create" id="submit"/>
		</td>
	</tr>
	<tr>
		<td width="20%">
		<div class="helper">Воспользуйтесь фильтрами, чтобы быстрее найти технику.</div>
		<label>Нация:<br />
			<select class="filter nation" name="nation">
				<option value="all"><?php echo i18n("allNations");?></option>
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
		<div class="helper">Щелкните по танку, и нажмите кнопку "Добавить", чтобы затребовать боевую единицу в команду.</div>
		<div class="vehiclesbox">
		<?php foreach ( $data as $vehicle ) : ?>
			<div
				data-type="<?php echo $vehicle->type;?>"
				data-nation="<?php echo $vehicle->nation;?>"
				data-level="<?php echo $vehicle->lvl;?>"
				data-tank_id="<?php echo $vehicle->tank_id;?>"
				data-type_i18n="<?php echo $vehicle->type_i18n;?>"
				data-name_i18n="<?php echo $vehicle->name_i18n;?>"
				data-short="<?php echo $vehicle->short_name_i18n;?>"
				data-img="<?php echo $vehicle->image;?>"
				class="vehicle <?php echo $vehicle->type;?>"
				id="vehicle_<?php echo $vehicle->tank_id;?>">
				<div class="contourbox"><img src="<?php echo $vehicle->contour_image;?>" alt="x" /></div>
				<?php echo $vehicle->name_i18n;?>
			</div>
		<?php endforeach;?>
		</div>
		<?php 
		/** @todo
		 * Клик по танку создает JS объект, внутри которого определяется статистика по машине.
		 * Эти объекты объединены в массив, ключами которого являются ID машин.
		 * Клик по танку переводит курсор на нужный объект в массиве.
		 * Изменения в полях статистики сразу приводят к изменениям свойств объектов.
		 * Отправка данных происходит через ajax-post.
		 * Ответом приходит статус-результат выполнения действия и ссылка-редирект на команду,
		 * после чего вызов location.href обновляет контент. 
		 */
		?>
		</td>
		<td width="40%">
			<div id="vehicleStatBox" class="groupbox">
				<table>
				<tr>
					<td rowspan="2"><img src="" alt="" class="vimg"/></td>
					<td class="vtype"></td>
				</tr>
				<tr><td class="vname"></td></tr>
				</table>
				<div class="helper">При желании можете указать дополнительную информацию тут.</div>
				<label>Кол-во боев:
					<input type="text" id="battles" name="battles" />
				</label>
				<br />
				<label>% побед:
					<input type="text" id="winrate" name="winrate" />
				</label>
				<br />
				<label>% попаданий:
					<input type="text" id="avg_hits" name="avg_hits"/>
				</label>
				<br />
				<label>средний урон:
					<input type="text" id="avg_damage" name="avg_damage" />
				</label>
				<br />
				<label>обнаружено в среднем:
					<input type="text" id="avg_spoted" name="avg_spoted" />
				</label>
				<br />
				<label>Требуется единиц:
					<input type="text" id="num_required" name="num_required" />
				</label>
				<br />
				<label>
					<input type="button" value="request" id="addvehilcebutton" />
					<input type="button" value="decline" id="removevehiclebutton" />
				</label>
			</div>
		</td>
	</tr>
	</tbody>
</table>
</form>

<script type="text/javascript">
var x = new VehiclesFilter();
x.init();


</script>