(function($){
	$.fn.SortableTableTemplate = function( _init_data, _init_settigs ) {	
		
		var settings = {
			init_parse_function: undefined,
			sort_functions: {},
			columns_alias: undefined,
			columns_sortable_class: "sortable",
			columns_sorted_asc: "sorted-asc",
			columns_sorted_desc: "sorted-desc",
			buttons_selector: "thead tr th",
			body_selector: "tbody",
		}
		
		var storage_unsorted = [];
		var cache_sorted = {};
		var buttons = [];
		var tbody = {};
		
		var _root = this;
		
		
		/**
		 * constructor
		 */
		(function(){
			loadSTT();
			settings = $.extend(true, settings, _init_settigs );
			
			
			if ("function" === typeof settings.init_parse_function) {
				for( p in _init_data ) {
					storage_unsorted.push( settings.init_parse_function( _init_data[ p ] ) );
				}
			}
			else {
				for ( p in _init_data ) {
					storage_unsorted.push( _init_data[ p ] );
				}
			}
			$(_root).find( settings.buttons_selector ).each(function(){
				var key = $(this).data('stt-key');
				$(this).data('stt-key', key);
				if (undefined !== key ) {
					if (undefined === cache_sorted[ key ] ) {
						cache_sorted[ key ] = {};
					}
				}
			});
			buttons = $(_root).find( settings.buttons_selector + ":data(stt-key)" );
			buttons.click( onSortButtonClick );
			buttons.addClass( settings.columns_sortable_class );
			
			tbody = $(_root).find( settings.body_selector );
			tbody.Container( storage_unsorted );
		})()
		
		
		/**
		 *
		 */
		function loadSTT() {
			var saved_settings = $(_root).data('stt-settings');
			settings = $.extend(true, settings, saved_settings );
			
			cache_sorted = $(_root).data('stt-cache');
			if (undefined === cache_sorted) {
				cache_sorted = {};
			}
			storage_unsorted = $(_root).data('stt-storage');
			if (undefined === storage_unsorted) {
				storage_unsorted = [];
			}
		}
		
		
		/**
		 *
		 */
		function onSortButtonClick(){
			var $this = $(this);
			var key = $this.data('stt-key');
			var order = $this.data('stt-order');
			buttons.removeClass( settings.columns_sorted_asc + " " + settings.columns_sorted_desc);
			$this.addClass( settings['columns_sorted_' + order] );
			tbody.Container( getSorted(key, order) );
			order = "asc" === order ? "desc" : "asc";
			$this.data('stt-order', order);
		}
		
		
		/**
		 * @desc: Возвпащает отсортированную версию данных. Кэширует сортированные данные.
		 */
		function getSorted(key, order) {
			if ( cache_sorted[ key ] ) {
				if ( cache_sorted[ key ][ order ] ) {
					return cache_sorted[ key ][ order ];
				}
			}
			
			var cln = storage_unsorted.slice(0);
			if ("asc" === order) {
				if ("function" === typeof settings.sort_functions[ key ]) {
					cache_sorted[ key ][ order ] = cln.sort( settings.sort_functions[ key ] );
					return cache_sorted[ key ][ order ];
				}
				else {
					cache_sorted[ key ][ order ] = cln.sort(function(a, b){return a[ key ] > b[ key ] ? 1 : ( a[ key ] < b[ key ] ? -1 : 0) });
					return cache_sorted[ key ][ order ];
				}
			}
			else {
				if ("function" === typeof settings.sort_functions[ key ]) {
					cache_sorted[ key ][ order ] = cln.sort( settings.sort_functions[ key ] ).reverse();
					return cache_sorted[ key ][ order ];
				}
				else {
					cache_sorted[ key ][ order ] = cln.sort(function(a, b){return b[ key ] > a[ key ] ? 1 : ( b[ key ] < a[ key ] ? -1 : 0) });
					return cache_sorted[ key ][ order ];
				}
			}
		}
		
		
		
		/**
		 * @desc: Выполняет очистку кэша сортированных массивов
		 */
		_root.clearCache = function() {
			delete cache_sorted;
			cache_sorted = {};
		}
		
		
		return this;
	}
})(jQuery)