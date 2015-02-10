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
				<label>
					<input type="radio" name="visability" value="clan"
					<?php echo (0 != Engine::getInstance()->user->clan_id ? 
							'' :
							'disabled="disabled"');?>
					/>
					только бойцам клана
				</label><br />
				<label><input type="radio" name="visability" value="invite" />только приглашенным</label><br />
			</div>
		</td>
		<td>Запрошенная Вами техника:
			<div id="selectedVehicles">
				<span class="vehicle vehicle-preset {{tankopedia.vehicles.~key~.type}}" data-tank_id="{{~key~}}">
					{{tankopedia.vehicles.~key~.short_name_i18n}}
				</span>
			</div>
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
		<div class="vehiclesbox" data-collection="">
			<div class="vehicle {{~item~.type}}" id="vehicle_{{~item~.tank_id}}" data-tank_id="{{~item~.tank_id}}"
				data-type="{{~item~.type}}" data-nation="{{~item~.nation}}" data-level="{{~item~.level}}">
				<div class="contourbox"><img src="{{~item~.contour_image}}" alt="x" /></div>
				{{~item~.name_i18n}}
			</div>
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
					<input type="text" id="avg_spotted" name="avg_spotted" />
				</label>
				<br />
				<label>выживаемость:
					<input type="text" id="avg_surv" name="avg_surv" />
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
$('.vehiclesbox').Container( tankopedia.sortedVehicles() );
var x = new VehiclesInterview();
x.init();


</script>