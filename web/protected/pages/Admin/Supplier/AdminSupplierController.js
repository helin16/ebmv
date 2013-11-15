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
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow({'id': 'id', 'name': 'name', 'supplierLocation': 'Supplier URL', 'active': 'active', 'istitle': true}, 'option').addClassName('titleRow') });
		tmp.i = (itemrowindex || 0);
		items.each(function(item) {
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow(item, new Element('img', {'editId': item.id, 'class': 'btn', 'src': '/themes/default/images/edit.png', 'alt': 'EDIT'})
				.observe('click', function() {tmp.me.editItem(this); }) ).addClassName(tmp.i % 2 === 1 ? 'even' : 'odd')
			});
			tmp.i++;
		});
		return tmp.resultDiv;
	}

	,_getItemRow: function (item, option) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class' : 'row'})
			.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(item.id) })
			.insert({'bottom' : new Element('span', {'class' : 'col name'}).update(item.name) })	
			.insert({'bottom' : new Element('span', {'class' : 'col supplierLocation'}).update(item.supplierLocation) })	
			.insert({'bottom' : new Element('span', {'class' : 'col active'}).update(item.active) })	
			.insert({'bottom' : new Element('span', {'class' : 'col btns'}).update(option) });
		return tmp.div;
	}
});