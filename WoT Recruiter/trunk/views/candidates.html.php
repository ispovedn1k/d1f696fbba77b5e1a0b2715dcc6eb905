<?php $squads = array('', '', '', '', '', '');?>

<script type="text/javascript">
var moves = new Object();
$(function() {
	<? if ( $data->isEditAllowed() ) : ?>
	$('.squad-box').sortable({
		connectWith: ".squad-box",
		stop: function(event, ui){
			var user_id = $(ui.item[0]).data('userid');
			var squad = $(ui.item[0].parentElement).data('squad');
			moves[ user_id ] = squad;
		},
	});
	$('.squad-box').disableSelection();
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
});
</script>



<?php
// расписываем кандидатов по отрядам
foreach( $data->_candidates as $candidate) {
	ob_start();
	?>
		<div class="candidate" data-userid="<?php echo $candidate->user_id;?>">
			<span><?php echo $candidate->personName;?></span>
			<div class="candidate-vehicles">
				<?php foreach ($candidate->a_vehicles as $cvehicle) :?>
					<div class="candidate-vehicle">
						<?php echo $cvehicle->name_i18n;?>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	<?php
	$squads[ $candidate->status ] .= ob_get_clean();
}?>

<?php for ( $i = 1; $i <= $data->squads_num; $i++ ) : ?>
<h4>squad #<?php echo $i;?></h4>
<div class="squad-box" data-squad="<?php echo $i;?>">
	<?php echo $squads[$i];?>
</div>
<?php endfor;?>

<input type="button" value="update squads" id="squadsupdate" />

<h4>candidates: </h4>
<div class="squad-box" data-squad="0">
	<?php echo $squads[0];?>
</div>