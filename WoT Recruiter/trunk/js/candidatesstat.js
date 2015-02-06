var CandidatesStat = function() {
	var _root = this;
	
	var data = {};
	
	this.sortedByUserID = {};
	
	
	this.Init = function( _data ) {
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
		
		return _root;
	};
	
	
	/**
	 * @desc: Превращает данные в отсортированные по пользовательским ID
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