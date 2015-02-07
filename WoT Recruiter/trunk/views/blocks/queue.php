<?php 
	if (! Engine::getInstance()->user->isBlessed() ) {
		return;
	}
?>
<div class="queue-interface-box">
	<table>
	<tbody class="queue-stat-table-{{~collection~.status}}">
		<tr>
			<th>status:</th>
			<td><span class="queue-st-{{~collection~.data.status}}">{{~collection~.data.status}}</span></td>
		</tr>
		<tr>
			<th>start ID:</th>
			<td>{{~collection~.data.start_id}}</td>
		</tr>
		<tr>
			<th>start time:</th>
			<td>{{~collection~.data.start_time}}</td>
		</tr>
		<tr>
			<th>done now:</th>
			<td>{{~collection~.data.done_now}}</td>
		</tr>
		<tr>
			<th>done total:</th>
			<td>{{~collection~.data.done_total}}</td>
		</tr>
		<tr>
			<th>to do:</th>
			<td>{{~collection~.data.to_do}}</td>
		</tr>
		<tr>
			<th>nearest ID:</th>
			<td>{{~collection~.data.nearest_id}}</td>
		</tr>	
		<tr>
			<th>endpoint ID:</th>
			<td>{{~collection~.data.endpoint_id}}</td>
		</tr>
	</tbody>
	<div class="buttonsbox">
		<input type="button" id="queueStart" value="start" />
		<input type="button" id="queueStop" value="stop" />
		<input type="button" id="queueTerminate" value="terminate" />
	</div>
	</table>
</div>
<script type="text/javascript">

(function(){
	function updateQueueStat() {
		$.get(
				"<?php echo Route::LocalUrl("?cont=queue");?>&action=status",
				function( response ) {
					if ("object" !== typeof response) {
						return;
					}
					$('.queue-interface-box table').Tankany( response );
				},
				"json"
		);
	}


	function startDaemon() {
		$.get(
				"<?php echo Route::LocalUrl("?cont=queue");?>&action=start",
				function( response ) {
					if ("object" !== typeof response) {
						return;
					}
					
					if ("ok" === response.status) {
						alert("success started");
					}
					else {
						alert("stat failed!\n". response.msg);
					}

					updateQueueStat();
				},
				"json"
		);
	}


	function stopDaemon() {
		$.get(
				"<?php echo Route::LocalUrl("?cont=queue");?>&action=stop",
				function( response ) {
					if ("object" !== typeof response) {
						return;
					}
					
					if ("ok" === response.status) {
						alert("stop mark added");
					}
					else {
						alert("stop failed!\n". response.msg);
					}
				},
				"json"
		);
	}


	function terminateDaemon() {
		$.get(
				"<?php echo Route::LocalUrl("?cont=queue");?>&action=terminate",
				function( response ) {
					if ("object" !== typeof response) {
						return;
					}
					
					if ("ok" === response.status) {
						alert("terminate mark added");
					}
					else {
						alert("terminate failed!\n". response.msg);
					}

					updateQueueStat();
				},
				"json"
		);
	}

	
	$('#queueStart').click( startDaemon );
	$('#queueStop').click( stopDaemon );
	$('#queueTerminate').click( terminateDaemon );
	updateQueueStat();
	setInterval( updateQueueStat, 15000);
})();
	
	
</script>