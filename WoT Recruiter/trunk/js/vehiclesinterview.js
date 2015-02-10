VehiclesInterview = function() {
	
	var Type = "all";
	var Nation = "all";
	var Level = "all";
	
	var Filters;
	var VehiclesBox;
	var VehicleStatBox;
	
	var pointer = '0';
	var SelectedVehicles = new Object();
	
	var btnAdd;
	var btnRemove;
	
	var root = this;
	
	
	this.init = function () {
		VehiclesBox = $('div.vehiclesbox');
		VehiclesBox.children('div.vehicle').click( onVehicleClick );
		
		Filters = $('.filter');
		Filters.change( onFilterChange );
		
		VehicleStatBox = $('#vehicleStatBox');
		btnAdd = $('#addvehilcebutton');
		btnAdd.click( onAddVehicleButtonClick );
		btnRemove = $('#removevehiclebutton');
		btnRemove.click( onRemoveVehicleButtonClick );
		
		$('#submit').click( onSubmitInterviewClick );
		$('#selectedVehicles').Container( SelectedVehicles );
	};
	
/*****************************************************************/
/******************** Display functions **************************/
/*****************************************************************/
	
	/**
	 * 
	 */
	function DrawFilteredVehicles() {
		VehiclesBox.children('div.vehicle').each(function() {
			var $this = $(this);
			var display = true;
			
			display &= 
				"all" === Type ? 
					true : 
					$this.data('type') == Type;
			
			display &=
				"all" === Nation ?
					true :
					$this.data('nation') == Nation;
			
			display &=
				Level.indexOf("all") > -1?
					true :
					Level.indexOf(""+ $this.data('level')) > -1;
			
			if (display) {
				$this.show();
			} else {
				$this.hide();
			}
		});
	};
	
	
	/**
	 * 
	 */
	function DrawSelectedVehicles() {
		$('#selectedVehicles').Container( SelectedVehicles );
		$('#selectedVehicles').children('.vehicle-preset').click( onVehicleClick );
	}
	
	
/*****************************************************************/
/******************** Events listeners ***************************/
/*****************************************************************/
	
	/**
	 * 
	 */
	function onFilterChange() {
		var $this = $(this);
		var f = $this.attr('name');
		
		if ("nation" === f) {
			Nation = $this.val();
		}
		else if ("type" === f) {
			Type = $this.val();
		}
		else if ("level" === f) {
			Level = $this.val();
		}
		
		DrawFilteredVehicles();
	}
	
	
	/**
	 * Меняет указатель выбранной техники.
	 * Загружает из JS-кэша установленные для техники требования.
	 */
	function onVehicleClick() {
		var $this = $(this);
		$('.vehicle').removeClass('pointed-vehicle');
		
		pointer = $this.data('tank_id');
		VehicleStatBox.find('.vimg').attr('src', tankopedia['vehicles'][ pointer ]['image']);
		VehicleStatBox.find('.vtype').html( tankopedia['vehicles'][ pointer ]['type_i18n'] );
		VehicleStatBox.find('.vname').html( tankopedia['vehicles'][ pointer ]['name_i18n'] );
		
		$this.addClass('pointed-vehicle');
		// если выбранная машина уже в списке
		if ( typeof SelectedVehicles[ pointer ] !== "undefined" ) {
			// зальем всю стату в поля
			VehicleStatBox.find('input:text').each(function(){
				$(this).val( SelectedVehicles[ pointer ][ $(this).attr('name') ] );
			});
			btnRemove.attr('disabled', false);
		}
		else {
			btnRemove.attr('disabled', true);
		}
	}
	
	
	/**
	 * Добавляет по установленному указателю требования к технике в JS-кэш.
	 */
	function onAddVehicleButtonClick() {
		var vehStat = new Object();
		var SelectedVehicle = VehiclesBox.children('#vehicle_' + pointer);
		
		VehicleStatBox.find('input:text').each(function(){
			vehStat[ $(this).attr('name') ] = $(this).val();
		});
		
		SelectedVehicles[ pointer ] = vehStat;
		
		SelectedVehicle.addClass('selected-vehicle');
		btnRemove.attr('disabled', false);
		
		DrawSelectedVehicles();
	}
	
	
	/**
	 * Удаляет по указателю из кэша информацию о технике.
	 */
	function onRemoveVehicleButtonClick() {
		delete SelectedVehicles[ pointer ];
		VehiclesBox.children('#vehicle_' + pointer).removeClass('selected-vehicle');
		btnRemove.attr('disabled', true);
		
		DrawSelectedVehicles();
	}
	
	
	/**
	 * 
	 */
	function onSubmitInterviewClick() {
		var data = new Object();
		data['itrv_name'] = $('#itrv_name').val();
		data['itrv_id'] = $('#itrv_id').val();
		data['itrv_comment'] = $('#itrv_comment').val();
		data['visability'] = $('input[name=visability]:checked').val();
		data['vehicles'] = SelectedVehicles;
		
		$.post(
			"?cont=interview&action=create",
			data,
			function( response ){
				if ("object" != typeof response) {
					alert("Epic fail! WTF was that?\n" + response);
					return;
				}
				
				if ("ok" === response.status ) {
					location.href = response.link;
				} else {
					alert( response.msg );
				}
			},
			"json")
	}
}