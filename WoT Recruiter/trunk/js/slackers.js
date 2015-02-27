var Slackers = function( _init ) {

	var storage = [];

	var sNameUp = [];
	var sNameDown = [];
	var sDiffUp = [];
	var sDiffDown = [];
	var sTotalUp = [];
	var sTotalDown = [];

	var dName = "down";
	var dDiff = "down";
	var dTotal = "down";

	var _root = this;

	(function(){
		for (p in _init) {
			storage.push( _init[ p ] );
		}

		$('#slackers-nick').click(function() {
			$('#slackers tbody').Container( getSortedByNames());
		});

		$('#slackers-period').click(function() {
			$('#slackers tbody').Container( getSortedByDiff());
		});

		$('#slackers-total').click(function() {
			$('#slackers tbody').Container( getSortedByTotal());
		});

		$('#slackers tbody').Container( getSortedByNames());
	})()

	
	/**
	 * сортировка по никам
	 */
	function getSortedByNames(){
		dName = dName === "up" ? "down" : "up";

		if (dName === "up" && sNameUp.length > 0) {
			return sNameUp;
		}

		if (dName === "down" && sNameDown.length > 0) {
			return sNameDown;
		}

		if (dName === "up") {
			sNameUp = storage.slice(0).sort(function(a, b) {
				return a['playerName'] > b['playerName'] ? 1 : -1;
			});

			return sNameUp;
		}

		if (dName === "down") {
			sNameDown = storage.slice(0).sort(function(a, b) {
				return a['playerName'] > b['playerName'] ? -1 : 1;
			});

			return sNameDown;
		}
	};


	/**
	 * сортировка по периоду
	 */
	function getSortedByDiff(){
		dDiff = dDiff === "up" ? "down" : "up";

		if (dDiff === "up" && sDiffUp.length > 0) {
			return sDiffUp;
		}

		if (dDiff === "down" && sDiffDown.length > 0) {
			return sDiffDown;
		}

		if (dDiff === "up") {
			sDiffUp = storage.slice(0).sort(function(a, b) {
				return parseInt(a['diff']) -  parseInt(b['diff']);
			});
			
			return sDiffUp;
		}

		if (dDiff === "down") {
			sDiffDown = storage.slice(0).sort(function(a, b) {
				return parseInt(b['diff']) -  parseInt(a['diff']);
			});

			return sDiffDown;
		}
	};


	/**
	 * сортировка по "всего"
	 */
	function getSortedByTotal(){
		dTotal = dTotal === "up" ? "down" : "up";

		if (dTotal === "up" && sTotalUp.length > 0) {
			return sTotalUp;
		}

		if (dTotal === "down" && sTotalDown.length > 0) {
			return sTotalDown;
		}

		if (dTotal === "up") {
			sTotalUp = storage.slice(0).sort(function(a, b) {
				return parseInt(a['resources']) -  parseInt(b['resources']);
			});

			return sTotalUp;
		}

		if (dTotal === "down") {
			sTotalDown = storage.slice(0).sort(function(a, b) {
				return parseInt(b['resources']) -  parseInt(a['resources']);
			});

			return sTotalDown;
		}
	};
}
