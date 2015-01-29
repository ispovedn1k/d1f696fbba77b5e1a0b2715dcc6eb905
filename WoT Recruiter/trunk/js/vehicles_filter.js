VehiclesFilter = function() {
	
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
		var html_selectedVehicles = '';
		var sorted = new Object();
		// Создадим сортированный список техники, чтобы не было радуги в выводе.
		for (tank_id in SelectedVehicles) {
			if ("undefined" === typeof sorted[ SelectedVehicles[ tank_id ]['type'] ]) {
				sorted[ SelectedVehicles[ tank_id ]['type'] ] = '';
			}
			// Воспользуемся тем, что ширина поля вывода ограничена, и с помощью стилей
			// организуем вывод в определенные позиции, чтобы было похоже на колонки.
			sorted[ SelectedVehicles[ tank_id ]['type'] ] +=
					"<span class='vehicle-preset " + SelectedVehicles[ tank_id ]['type'] + "'>" +
							SelectedVehicles[ tank_id ]['name'] +
					"</span>";
		}
		for (type in sorted) {
			html_selectedVehicles += "<div>" + sorted[ type ] + "</div>";
		}
		
		$('#selectedVehicles').html( html_selectedVehicles );
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
		VehiclesBox.children('#vehicle_' + pointer).removeClass('pointed-vehicle');
		
		pointer = $this.data('tank_id');
		VehicleStatBox.find('.vimg').attr('src', $this.data('img'));
		VehicleStatBox.find('.vtype').html($this.data('type_i18n'));
		VehicleStatBox.find('.vname').html($this.data('name_i18n'));
		
		$this.addClass('pointed-vehicle');

		if ( typeof SelectedVehicles[ pointer ] !== "undefined" ) {
			VehicleStatBox.find('input:text').each(function(){
				$(this).val( SelectedVehicles[ pointer ]['stat'][ $(this).attr('name') ] );
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
		
		SelectedVehicles[ pointer ] = {
			name: SelectedVehicle.data('short'),
			type: SelectedVehicle.data('type'),
			stat: vehStat
		}
		
		SelectedVehicle.addClass('selected-vehicle');
		btnRemove.attr('disabled', false);
		
		DrawSelectedVehicles();
	}
	
	
	/**
	 * Удаляет по указателю из кэша информацию о технике.
	 */
	function onRemoveVehicleButtonClick() {
		SelectedVehicles[ pointer ] = undefined;
		VehiclesBox.children('#vehicle_' + pointer).removeClass('selected-vehicle');
		btnRemove.attr('disabled', true);
		
		DrawSelectedVehicles();
	}
	
}