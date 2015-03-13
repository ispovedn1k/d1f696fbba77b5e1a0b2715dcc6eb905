// @todo: сделать нормальный универсальный полномасштабный шаблонизатор

(function() {
	
	/**
	 * Заполняет указанные контейнеры данными по имени переменной указанной в data-collection.
	 * Если это поле не заполнено, или переменная не доступна, данные на парсер можно
	 * передать через параметр функции data.
	 */
	$.fn.Container = function( data, single ) {
		/**
		 * порядок приориета данных:
		 * локальной считается запись data-collection с именем
		 * поля, которое и будет разобрано в этом контейнере
		 * данные, переденные через переменную data в вызове
		 * будет разобраны в случае, если не удалось полуить данные
		 * по локальной переменной. 
		 */
		if ( undefined === single ) {
			sigle = false;
		}
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
			
			if ( single ) {
				nodehtml = makeNode(template, undefined, collection);
				// @optimisation: не вынести ли добавление в контейнер из цикла? Будет работать быстрее?
				$this.append( nodehtml );
				return this;
			}
			
			for (key in collection) {
				nodehtml = makeNode(template, key, collection);
				// @optimisation: не вынести ли добавление в контейнер из цикла? Будет работать быстрее?
				$this.append( nodehtml );
			}
			
			return this;
		});
		
		return $(this);
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
					collection = collection[ key ];
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