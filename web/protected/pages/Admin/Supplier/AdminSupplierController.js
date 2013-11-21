/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CrudPageJs(), {
	
	_getResultDiv: function(items, includetitlerow, itemrowindex) {
		var tmp = {};
		tmp.me = this;
		tmp.includetitlerow = (includetitlerow === false ? false : true);
		
		tmp.resultDiv = new Element('div');
		if(tmp.includetitlerow === true)
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow({'id': 'id', 'name': 'Name', 'connector': 'Connector Script', 'active': 'active', 'istitle': true}, '').addClassName('titleRow') });
		tmp.i = (itemrowindex || 0);
		items.each(function(item) {
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow(item, new Element('span', {'editId': item.id, 'class': 'btn editbtn', 'title': 'EDIT'})
					.observe('click', function() {tmp.me.editItem(this); }) 
				).addClassName(tmp.i % 2 === 1 ? 'even' : 'odd')
			});
			tmp.i++;
		});
		return tmp.resultDiv;
	}

	,_getItemRow: function (item, option) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class' : 'row'}).store('item', item)
			.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(item.id) })
			.insert({'bottom' : new Element('span', {'class' : 'col name'}).update(item.name) })	
			.insert({'bottom' : new Element('span', {'class' : 'col connector'}).update(item.connector) })	
			.insert({'bottom' : new Element('span', {'class' : 'col active'}).update(item.istitle === true ? item.active : new Element('input', {'type': 'checkbox', 'checked': item.active, 'disabled': true})) })	
			.insert({'bottom' : new Element('span', {'class' : 'col btns'}).update(option) })
			.insert({'bottom' : tmp.me._getInfoDiv(item) });
		return tmp.div;
	}
	
	,_getInfoDiv: function (item) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class': 'attrs_wrapper'});
		tmp.code = '';
		$H(item.info).each(function(itemArr) {
			tmp.attrCode = itemArr.key;
			if(typeof(itemArr.value) === 'object') {
				tmp.attrDiv = new Element('div', {'class': 'attr_wrapper'}); 
				
				//getting the title div
				tmp.attrDiv.insert({'bottom': new Element('span', {'class': 'attr_name inlineblock'}).update(itemArr.value[0].type.name) });
				if(tmp.attrCode !== tmp.code) {
					tmp.code = tmp.attrCode;
				} 
				
				//getting the value div
				tmp.attrValeusDiv = new Element('span', {'class': 'attr_values_wrapper inlineblock'}); 
				itemArr.value.each(function(attr) {
					tmp.attrValeusDiv.insert({'bottom': new Element('div', {'class': 'attr_value' }).update(attr.value) });
						
				});
				tmp.attrDiv.insert({'bottom': tmp.attrValeusDiv });
				tmp.div.insert({'bottom': tmp.attrDiv });
			};
		});
		return tmp.div;
	}
	
	,showEditPanel: function (btn) {
		var tmp = {};
		tmp.me = this;
		tmp.row = $(btn).up('.row');
		tmp.row.replace(tmp.me._getSavePanel(tmp.row.retrieve('item'), 'editDiv'));
		return 
	}
	
	,_getSavePanel: function (item, cssClass) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'savePanel'}).addClassName(cssClass)
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Name', new Element('input', {'value': item.name, "class": "txt"}) ) })
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Connector', new Element('input', {'value': item.connector, "class": "txt"}) ) })
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Act?', new Element('input', {'type': 'checkbox', 'checked': item.active}) ) })
			.insert({'bottom':  tmp.me._getSaveAttrPanel(item.info) });
		return tmp.newDiv;
	}
	
	,_getSaveAttrPanel: function(attrs) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {"class": "attrs_div"}); 
		$H(attrs).each(function(itemArr) {
			if(typeof(itemArr.value) === 'object') {
				itemArr.value.each(function(attr) {
					tmp.div.insert({'bottom': tmp.me._getSaveFieldDiv(attr.type.name, new Element('input', {'value': attr.value}) ) });
				});
			};
		});
		return tmp.div;
	}
	
	,_getSaveFieldDiv: function (fieldName, field) {
		var tmp = {};
		tmp.me = this;
		return new Element('div', {'class': 'fielddiv inlineblock padding5'})
			.insert({'bottom':  new Element('span', {'class': 'title'}).update(fieldName) })
			.insert({'bottom':  new Element('span', {'class': 'content'}).update(field) });
	}
});