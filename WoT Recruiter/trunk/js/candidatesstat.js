var CandidatesStat = function( _data ) {
	var _root = this;
	
	var data = {};
	
	this.sortedByUserID = {};
	
	
	/**
	 * constructor
	 */
	(function() {
		data = _data;
		
		// необходимо провести кое-какие расчеты
		for( p in data ) {
			data[ p ]['avg_damage'] = ( parseInt( data[ p ]['damage_dealt'] ) / parseInt( data[ p ]['battles'] ) ).toFixed(0);
			data[ p ]['winrate'] = ( 100 * parseInt( data[ p ]['wins'] ) / parseInt( data[ p ]['battles'] ) ).toFixed(2);
			data[ p ]['avg_surv'] = ( 100 * parseInt( data[ p ]['survived_battles'] ) / parseInt( data[ p ]['battles'] ) ).toFixed(2);
			data[ p ]['avg_spotted'] = ( parseInt( data[ p ]['spotted'] ) / parseInt( data[ p ]['battles'] ) ).toFixed(2);
		}
		
		// запускаем сортировку
		_root.sortedByUserID = sortByUserID();
	})();
	
	
	/**
	 * @descr: Выдает данные для #candidateInfoStat таблицы
	 */
	this.getFilteredStat = function( user_id, presentedVehicles, battles_type ) {
		if (undefined === battles_type) {
			battles_type = "all";
		}
		
		var ret = new Object();
		
		for (p in presentedVehicles) {
			var tank_id = presentedVehicles[ p ];
			
			ret[ tank_id ] = _root['sortedByUserID'][ user_id ]['all'][ tank_id ];
		}
		
		return sortVehiclesData2LTN( ret );
	}
	
	
	/**
	 * @descr: Превращает данные в отсортированные по пользовательским ID
	 */
	function sortByUserID ( ) {
		var ret = new Object();
		
		for (p in data) {
			var user_id = data[ p ]['account_id'];
			var battle_type = data[ p ]['battle_type'];
			var tank_id = data[ p ]['tank_id'];
			
			if ( undefined === ret[ user_id ]) {
				ret[ user_id ] = new Object();
			}
			if ( undefined === ret[ user_id ][ battle_type ] ) {
				ret[ user_id ][ battle_type ] = new Object
			}
			ret[ user_id ][ battle_type ][ tank_id ] = data[ p ];
		}
		
		return ret;
	};

}