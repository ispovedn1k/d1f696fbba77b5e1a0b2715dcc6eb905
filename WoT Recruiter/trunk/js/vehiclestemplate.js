// @todo: сделать нормальный универсальный полномасштабный шаблонизатор

(function() {
	
	/**
	 * Заполняет указанные контейнеры данными по имени переменной указанной в data-collection.
	 * Если это поле не заполнено, или переменная не доступна, данные на парсер можно
	 * передать через параметр функции data.
	 */
	$.fn.Container = function( data ) {
		/**
		 * порядок приориета данных:
		 * локальной считается запись data-collection с именем
		 * поля, которое и будет разобрано в этом контейнере
		 * данные, переденные через переменную data в вызове
		 * будет разобраны в случае, если не удалось полуить данные
		 * по локальной переменной. 
		 */
		$(this).each(function(index){
			var $this = $(this);
			var template = $this.data('ctemplate');
			
			if (! template ) {
				// если шаблон не определен, определяем его и сохраняем
				template = $this.html();
				$this.data('ctemplate', template);
			}
			
			var collection = $this.data('collection'); // определяется ключевым словом ~collection~
			if ( collection ) {
				collection = getData( collection.split('.') );
			}
			
			if (! collection ) {
				collection = data;
			}
			
			if ("object" !== typeof collection) {
				return this;
			}
			
			// после чего очищаем контейнер
			$this.empty(); // сейчас будем заполнять его по новой
			
			var nodehtml = '';
			
			for (key in collection) {
				nodehtml = makeNode(template, key, collection);
				// @optimisation: не вынести ли добавлени в контейнер из цикла? Будет работать быстрее?
				$this.append( nodehtml );
			}

		});
	}
	
	
	/**
	 * Заполняет контейнер с шаблоном сведениями о танках
	 * по заданному шаблону.
	 */
	$.fn.BuildVehiclesList = function( vehicles ) {
		// если список танков передан не был, плюемся всеми танками сразу
		if (undefined === vehicles) {
			vehicles = tankopedia.vehicles;
		}
		$(this).each(function(index){
			// все что внутри, считается шаблоном и должно быть повторено
			var $this = $(this);
			var html_template = $this.html();
			
			// шаблон сохранен, очищаем контейнер
			$this.empty();
			
			// теперь надо копировать шаблон, заполняя его данными.
			var v = tankopedia.sortedVehicles();
			for (var i = 0; i < v.length; i++) {
				$this.append( applyVehicle( v[i]['tank_id'], html_template, v[i] ) );
			}
		});
		
		return $(this);
	}
	
	
	/**
	 * Заполняет шаблонные поля данными из параметра
	 */
	$.fn.Tankany = function( data ) {
		$(this).each(function(index){
			var $this = $(this);
			var template = $this.data('ctemplate');
			
			if (! template ) {
				// если шаблон не определен, определяем его и сохраняем
				template = $this.html();
				$this.data('ctemplate', template);
			}
			
			var html = makeNode(template, '', data);
			$this.html(html);
		});
		return $(this);
	}
	
	
	/**
	 * Вставляет данные из танкопедии в нужные поля в шаблоне
	 */
	function applyVehicle(tank_id, template) {
		var rx = /{{\s*(\w+)\s*}}/gm;
		template = template.replace(rx, function(str, p, offset, a){
			return tankopedia.vehicles[ tank_id ] [ p ];
		});
		return template;
	}
	

	
	function makeNode( template, key, collection ) {
		var rx = /{{\s*([~\w\.]+)\s*}}/gm;
		template = template.replace(rx, function(str, varname, offset, a){			
			var varpath = varname.split('.');
			var fixed = getData(varpath, key, collection);
			if (undefined === fixed) {
				fixed = '--';
			}
			return fixed;
		});
		return template;
	}
	
	
	function getData(path, key, collection, level) {
		if ( undefined === path ) {
			return undefined;
		}
		
		if ( undefined === level ) {
			level = 0;
		}
		
		if (path.length === level) {
			return collection;
		}
		
		if (0 === level) {
			switch(path[0]) {
				case '~key~':
					collection = key;
					break;
				case '~item~':
					if (undefined === collection ) return undefined;
					collection =  collection[ key ];
					break;
				case '~collection~':
					break;
				default:
					collection = window[ path[0] ];
			}
		} else {
			if ( undefined === collection ) {
				return undefined;
			}
			
			switch ( path[ level ] ) {
				case '~key~':
					collection = collection[ key ];
					break
				default:
					collection = collection[ path[ level ] ];
			}
		}
		
		return getData(path, key, collection, ++level);
	}
})();