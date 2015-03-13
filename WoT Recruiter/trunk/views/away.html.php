<table id="away">
	<thead>
		<tr>
			<th id="away-nick" data-stt-key="account_name">Игрок</th>
			<th id="away-role">Должность</th>
			<th id="away-lastbtl" data-stt-key="last_battle_time">Последний раз был в бою</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{{~item~.account_name}}</td>
			<td>{{~item~.role_i18n}}</td>
			<td>{{~item~.last_battle_time}}</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
$('#away').SortableTableTemplate(
	<?php echo json_encode($data);?>,
	{
		init_parse_function: function( elm ) {
			var d = new Date(elm.last_battle_time * 1000);
			//d.setSeconds( elm.last_battle_time );
			var month = d.getMonth()+1;
			if (month < 10) {
				month = "0" + month;
			}
			var date = d.getDate();
			if (date < 10) {
				date = "0" + date;
			}
			elm.last_battle_time = d.getFullYear() + "/" + month + "/" + date;
			return elm;
		}
	}
);
</script>