Tankopedia = function(){

	var _root = this;
	var cache_sorted = null;
	/**
	 * Создает сортированный список техники из объекта, где ключом служит tank_id.
	 * В результате получается массив, сортированных объектов.
	 * @param v : объект вида tank_id => params_data
	 * @return array : сортированный массив объектов tank_id => params_data
	 */
	this.sortedVehicles = function(v, enableCheckTankId) {
		var presort = new Object();
		var level = 0;
		var type = '';
		var nation = '';
		
		var types = tankopedia.types;
		var nations = tankopedia.nations;
		
		var sorted = [];
		var saveToCache = false;
		
		if (undefined === v) {
			if (cache_sorted !== null) {
				return cache_sorted;
			}
			v = tankopedia.vehicles;
			saveToCache = true;
		}
		
		for ( tank_id in v ) {
			level = tankopedia.vehicles[ tank_id ]['level'];
			type = tankopedia.vehicles[ tank_id ]['type'];
			nation = tankopedia.vehicles[ tank_id ]['nation'];
			
			if (undefined === presort[ level ]) {
				presort[ level ] = new Object();
			}
			
			if (undefined === presort[ level ][ type ]) {
				presort[ level ][ type ] = new Object();
			}
			
			if (undefined === presort[ level ][ type ][ nation ]) {
				presort[ level ][ type ][ nation ] = new Object();
			}
			
			// на всякий случай
			if ( enableCheckTankId ) {
				v[ tank_id ]['tank_id'] = tank_id;
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
		
		if ( saveToCache ) {
			cache_sorted = sorted;
		}
		
		return sorted;
	}
}