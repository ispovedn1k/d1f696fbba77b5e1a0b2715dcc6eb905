<table id="slackers">
	<thead>
		<tr>
			<th id="slackers-nick" title="Изменить сортировку">Игрок</th>
			<th id="slackers-period" title="Изменить сортировку">За месяц<br />
				<?php echo $data['start']->format("Y/m/d") ." - ".  $data['end']->format("Y/m/d");?>
			</th>
			<th id="slackers-total" title="Изменить сортировку">Всего</th>			
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
<script type="text/javascript" src="js/slackers.js"></script>
<script type="text/javascript">
new Slackers(<?php echo json_encode( $data['data'] );?>);
</script>