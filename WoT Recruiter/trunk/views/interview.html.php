<h3><?php echo $data->itrv_name;?></h3>
<div class="descr"><?php echo $data->itrv_comment;?></div>

<script type="text/javascript">
var moves = new Object();
var candidatesStat = new CandidatesStat().Init(<?php echo json_encode($data->_statData);?>);

var squads = new Squads(
	<?php echo json_encode( $data->_candidates );?>,
	<?php echo json_encode( $data->a_vehicles );?>
);

$(function() {
	<? if ( $data->isEditAllowed() ) : ?>
	$('.squad-box').sortable({
		connectWith: ".squad-box",
		stop: function(event, ui){
			var user_id = $(ui.item[0]).data('userid');
			var squad = $(ui.item[0].parentElement).data('squad');
			moves[ user_id ] = squad;

			squads.moveUserToSquad( user_id, squad);

			$('#squad_calc tbody').Container( squads.calcVehiclesInSquad( 1 ) );
			// console.log( squads.calcVehiclesInSquad( squad ) );
		},
	});
	$('.squad-box').disableSelection();
	$('.candidate').click(function(){
		var user_id = $(this).data('userid');
		$('#candidateInfoStat tbody').Container( candidatesStat['sortedByUserID'][ user_id ]['all'] );
	});
	$('#candidateInfoStat tbody').Container( {} );
	<? endif; ?>
	
	// @warning! Костыль!
	$('#squadsupdate').click(function() {
		$.post('?cont=interview&action=candidatesUpdate',
			{
				candidates: moves,
				itrv_id: "<?php echo $data->itrv_id;?>",
			},
			function( data ) {
				if (data.status === true) {
					alert("updated");
				}
				else {
					alert(data.status);
				}
			},
			"json"
		);
	});

	$('#requiredVehicles tbody').Container( squads.vehicles );
	$('#squad_calc tbody').Container( squads.calcVehiclesInSquad( 1 ) );
});
</script>
<?php
if (! $data->isMember() ) {
	include "vehicles.html.php";
}
?>
<?php include "candidates.html.php";?>
