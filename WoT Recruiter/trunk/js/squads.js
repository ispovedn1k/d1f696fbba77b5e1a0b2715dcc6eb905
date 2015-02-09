var Squads = function( initCandidates, vehicles ) {
	var _root = this;
	
	// this.sortedBySquads = {};
	
	this.vehicles = {};
	this.Candidates = {};
	
	
	/**
	 * constructor
	 */
	(function(){
		_root.Candidates = initCandidates;
		
		_root.vehicles = vehicles;
		
		//_root.sortedBySquads = sortBySquads();
	})();
	
	
	/**
	 * 
	 */
	this.calcVehiclesInSquad = function( squad_id ) {
		var ret = new Object();
		
		if (undefined === squad_id) {
			squad_id = 1;
		}
		
		for ( tank_id in _root.vehicles ) {
			ret[ tank_id ] = 0;
		}
		
		for ( p in _root.Candidates ) {
			for (i in _root.Candidates[ p ]['a_vehicles'] ) {
				if ( _root.Candidates[ p ]['status'] == squad_id ) {
					ret[ _root.Candidates[ p ]['a_vehicles'][i] ]++;
				}
			}
		}
		
		return ret;
	};
	
	
	/**
	 * 
	 */
	this.moveUserToSquad = function( user_id, squad_id ) {
		_root.Candidates[ user_id ]['status'] = squad_id;
	};
	
	
	/**
	 * 
	 */
	function sortBySquads() {
		var sorted = new Object();
		
		for ( p in _root.Candidates ) {
			var status = _root.Candidates[ p ]['status'];
			if (undefined === sorted[ status ]) {
				sorted[ status ] = new Array();
			}
			sorted[ status ].push( _root.Candidates [ p ] );
		}
		
		return sorted;
	};
}