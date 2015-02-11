var Squads = function( initCandidates, vehicles, statInfo) {
	/**
	 * Содержит сведения об отряде, требуемой отряду технике и требуемой статистике на технике,
	 * сведения о кандидатах, предоставляемой ими технике, и принадлежности кандидатов к отрядам.
	 * 
	 * Умеет считать технику в отряде,
	 * фиксировать перемещения по отрядам,
	 * выдавать список кандидатов по принадлежности к отрядам.
	 */
	var _root = this;
	
	// this.sortedBySquads = {};
	
	this.vehicles = {};
	this.Candidates = {};
	this.candidatesStat = {};
	
	
	/**
	 * constructor
	 */
	(function(){
		_root.Candidates = initCandidates;
		
		_root.vehicles = vehicles;
		
		_root.candidatesStat = new CandidatesStat( statInfo );
		
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
			ret[ tank_id ] = {insquad: 0, required: _root.vehicles[ tank_id ].num_required};
		}
		
		for ( p in _root.Candidates ) {
			for (i in _root.Candidates[ p ]['a_vehicles'] ) {
				if ( _root.Candidates[ p ]['status'] == squad_id ) {
					ret[ _root.Candidates[ p ]['a_vehicles'][i] ].insquad ++;
				}
			}
		}
		
		return sortVehiclesData2LTN( ret );
	};
	
	
	/**
	 * 
	 */
	this.moveUserToSquad = function( user_id, squad_id ) {
		_root.Candidates[ user_id ]['status'] = squad_id;
	};
	
	
	/**
	 * @descr: Выполняет анализ статистики игрока по технике и выдает объект для вывода в таблице выбора техники
	 */
	this.getRequredVehiclesInfo = function( user_id, candidateVehiclesOnly ) {
		var u_vehicles = {};
		if ( true === candidateVehiclesOnly ) {
			u_vehicles = _root.Candidates[ user_id ]['a_vehicles'];
		}
		
		var ret = new Object();
		
		for( tank_id in _root.vehicles ) {
			if ((! in_array( tank_id, u_vehicles )) && (true === candidateVehiclesOnly )){
				continue;
			}
			var ratingPerc = new Object();
			var ratingColorID = new Object();
			var userStat = _root.candidatesStat['sortedByUserID'][ user_id ] ? 
					( _root.candidatesStat['sortedByUserID'][ user_id ]['all'][ tank_id ] ?
					_root.candidatesStat['sortedByUserID'][ user_id ]['all'][ tank_id ]	: {} ) : {};
						
			for ( p in _root.vehicles[ tank_id ] ) {
				var uS =  parseFloat( userStat[ p ] ? userStat[ p ] : 0 );
				var rS = parseFloat( _root.vehicles[ tank_id ][ p ] );
				if( _root.vehicles[ tank_id ] ) {
					ratingPerc[ p ] = 100 * ( uS - rS ) / rS; 	
				}
				else {
					retingPerc[ p ] = 100;
				}
				
				if ( ratingPerc[ p ] < -10 ) {
					ratingColorID[ p ] = 0;
				} else if ( ratingPerc[ p ] < -5 && ratingPerc[ p ] > -10 ) {
					ratingColorID[ p ] = 1;
				}else if ( ratingPerc[ p ] < 0 && ratingPerc[ p ] > -5 ) {
					ratingColorID[ p ] = 2;
				} else if ( ratingPerc[ p ] < 5 && ratingPerc[ p ] > 0 ) {
					ratingColorID[ p ] = 3;
				} else if ( ratingPerc[ p ] < 20 && ratingPerc[ p ] > 5 ) {
					ratingColorID[ p ] = 4;
				} else if ( ratingPerc[ p ] > 20 ) {
					ratingColorID[ p ] = 5;
				}
			}
						
			ret[ tank_id ] = {
				required: _root.vehicles[ tank_id ],
				userStat: userStat,
				ratingPerc: ratingPerc,
				ratingColorID: ratingColorID
			};
		}
		
		return sortVehiclesData2LTN( ret );
	}
	
	
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
	
	
	/**
	 * original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @descr: Checks if a value exists in an array
	 */
	function in_array(needle, haystack, strict) {	
		var found = false, key, strict = !!strict;

		for (key in haystack) {
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
				found = true;
				break;
			}
		}

		return found;
	}

}