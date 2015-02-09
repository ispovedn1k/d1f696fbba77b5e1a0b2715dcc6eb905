<h3><?php echo $data->itrv_name;?></h3>
<div class="descr"><?php echo $data->itrv_comment;?></div>

<script type="text/javascript">
var moves = new Object();
var candidatesStat = new CandidatesStat(<?php echo json_encode($data->_statData);?>);

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
	<? endif; ?>
	
	$('.candidate').click(function(){
		var user_id = $(this).data('userid');
		$('#candidateInfoStat tbody').Container( candidatesStat.getFilteredStat( user_id, squads.Candidates[user_id].a_vehicles ) );
	});
	$('#candidateInfoStat tbody').Container( {} );
	
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
/**
 * показывать в статистике выбранного игрока не всю технику, а только ту, что он выбрал для участия.
 * 
 * заблокировать кнопку "записаться", если не выбран ни один танк
 */
?>
<?php include "candidates.html.php";?>
