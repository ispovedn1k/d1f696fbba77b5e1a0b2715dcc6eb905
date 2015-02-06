var Squads = function( initCandidates, vehicles ) {
	var Candidates = {};
	var _root = this;
	
	// this.sortedBySquads = {};
	
	this.vehicles = {};
	
	
	/**
	 * constructor
	 */
	(function(){
		Candidates = initCandidates;
		
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
		
		for ( p in Candidates ) {
			for (i in Candidates[ p ]['a_vehicles'] ) {
				if ( Candidates[ p ]['status'] == squad_id ) {
					ret[ Candidates[ p ]['a_vehicles'][i] ]++;
				}
			}
		}
		
		return ret;
	};
	
	
	/**
	 * 
	 */
	this.moveUserToSquad = function( user_id, squad_id ) {
		Candidates[ user_id ]['status'] = squad_id;
	};
	
	
	/**
	 * 
	 */
	function sortBySquads() {
		var sorted = new Object();
		
		for ( p in Candidates ) {
			var status = Candidates[ p ]['status'];
			if (undefined === sorted[ status ]) {
				sorted[ status ] = new Array();
			}
			sorted[ status ].push( Candidates [ p ] );
		}
		
		return sorted;
	};
}