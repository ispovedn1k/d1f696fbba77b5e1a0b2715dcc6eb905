<table id="slackers">
	<thead>
		<tr>
			<th id="slackers-nick" title="Изменить сортировку" data-stt-key="playerName">Игрок</th>
			<th id="slackers-period" title="Изменить сортировку <?php echo $data['strict'] ? '' : "Интервал не соблюден";?>" data-stt-key="diff">За месяц<br />
				<?php echo $data['start']->format("Y/m/d") ." - ".  $data['end']->format("Y/m/d");?>
			</th>
			<th id="slackers-total" title="Изменить сортировку" data-stt-key="resources">Всего</th>			
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>[ {{~item~.clanTag}} ] {{~item~.playerName}}</td>
			<td>{{~item~.diff}}</td>
			<td>{{~item~.resources}}</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	$('#slackers').SortableTableTemplate(
		<?php echo json_encode( $data['data'] );?>,
		{
			init_parse_function: function( elm ) {
				elm.resources = parseInt( elm.resources );
				return elm;
			}
		}
	);
</script>