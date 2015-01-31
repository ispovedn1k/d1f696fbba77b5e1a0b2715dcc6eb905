(function() {
	/**
	 * Заполняет контейнер с шаблоном сведениями о всех танках
	 * по заданному шаблону.
	 */
	$.fn.BuildVehiclesList = function() {
		$(this).each(function(index){
			// все что внутри, считается шаблоном и должно быть повторено
			var $this = $(this);
			var html_template = $this.html();
			
			// шаблон сохранен, очищаем контейнер
			$this.empty();
			
			// теперь надо копировать шаблон, заполняя его данными.
			var v = sortedVehicles();
			for (var i = 0; i < v.length; i++) {
				$this.append( applyVehicle( v[i]['tank_id'], html_template ) );
			}
		});
		
		return $(this);
	}
	
	
	/**
	 * Заполняет шаблонные поля ноды данными
	 */
	$.fn.Tankany = function() {
		$(this).each(function(index){
			var $this = $(this);
			var html_template = $this.outerHTML;
			var tank_id = $this.data('tank_id');
			
			if ( tank_id ) {
				$this.outerHTML = applyVehicle( tank_id, html_temlplate );
			}
		});
		return $(this);
	}
	
	
	/**
	 * Вставляет данные в нужные поля в шаблоне
	 */
	function applyVehicle(tank_id, template) {
		var rx = /{{\s*(\w+)\s*}}/gm;
		template = template.replace(rx, function(str, p, offset, a){
			return vehicles[ tank_id ] [ p ];
		});
		return template;
	}
	
	
	/**
	 * Создает сортированный список техники
	 */
	function sortedVehicles(v) {
		var presort = new Object();
		var level = 0;
		var type = '';
		var nation = '';
		
		var types = ['lightTank', 'mediumTank', 'heavyTank', 'SPG', 'AT-SPG'];
		var nations = ['ussr', 'germany', 'usa', 'france', 'china', 'uk', 'japan'];
		
		var sorted = [];
		
		if (undefined === v) {
			v = vehicles;
		}
		
		for ( tank_id in v ) {
			level = v[ tank_id ]['level'];
			type = v[ tank_id ]['type'];
			nation = v[ tank_id ]['nation'];
			
			if (undefined === presort[ level ]) {
				presort[ level ] = new Object();
			}
			
			if (undefined === presort[ level ][ type ]) {
				presort[ level ][ type ] = new Object();
			}
			
			if (undefined === presort[ level ][ type ][ nation ]) {
				presort[ level ][ type ][ nation ] = new Object();
			}
			
			presort[ level ][ type ][ nation ][ tank_id ] = v[ tank_id ];
		}
		
		
		for (var L = 1; L <= 10; L++ ) {
			if (undefined !== presort[L]) {
				for (var t = 0; t < 5; t++) {
					T = types[t];
					if (undefined !== presort[L][T]) {
						for (var n = 0; n < 7; n++) {
							N = nations[n];
							if( undefined !== presort[L][T][N]) {
								for( tank_id in presort[L][T][N]) {
									sorted.push(v[tank_id]);
								}
							}
						}
					}
				}
			}
		}
		
		return sorted;
	}
})();