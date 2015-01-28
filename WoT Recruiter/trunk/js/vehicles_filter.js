VehiclesFilter = function() {
	
	var Type = "all";
	var Nation = "all";
	var Level = "all";
	
	var Filters;
	var VehiclesBox;
	
	var root = this;
	
		
	this.draw = function() {
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
	
	
	this.init = function () {
		VehiclesBox = $('div.vehiclesbox');
		Filters = $('.filter');
		Filters.change( onFilterChange );
	};
	
	
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
		
		root.draw();
	}
	
	return this;
}

VehiclesFilter.prototype = {
		Hello: function() {
			alert("Hello");
		}
}