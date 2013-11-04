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
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow('id', 'suk', 'title', 'active', 'option').addClassName('titleRow') });
		tmp.i = (itemrowindex || 0);
		items.each(function(item) {
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow(item.id, item.suk, item.title, item.active,  new Element('img', {'editId': item.id, 'class': 'btn', 'src': '/themes/default/images/edit.png', 'alt': 'EDIT'})
				.observe('click', function() {tmp.me.editItem(this); }) ).addClassName(tmp.i % 2 === 1 ? 'even' : 'odd')
			});
			tmp.i++;
		});
		return tmp.resultDiv;
	}

	,_getItemRow: function (id, suk, title, active, option) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class' : 'row'})
			.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(id) })
			.insert({'bottom' : new Element('span', {'class' : 'col suk'}).update(suk) })	
			.insert({'bottom' : new Element('span', {'class' : 'col title'}).update(title) })	
			.insert({'bottom' : new Element('span', {'class' : 'col active'}).update(active) })	
			.insert({'bottom' : new Element('span', {'class' : 'col btns'}).update(option) });
		return tmp.div;
	}
	
	,showEditPanel: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.editProductId = $(btn).readAttribute('editId');
		tmp.btnSpan = $(btn).up('span.col.btns');
		tmp.suk = tmp.btnSpan.previous('span.col.suk').innerHTML;
		tmp.title = tmp.btnSpan.previous('span.col.title').innerHTML;
		tmp.active = tmp.btnSpan.previous('span.col.active').innerHTML;
		tmp.id = tmp.btnSpan.previous('span.col.id').innerHTML;
		
		tmp.editPanel = new Element('div', {'class' : 'editProductDiv'})
							.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(tmp.id) })
							.insert({'bottom' : new Element('span', {'class' : 'eSukLabel'}).update('Suk') })
							.insert({'bottom' : new Element('input', {'type' : 'text', 'class' : 'eSukBox rdcrnr lightBrdr', 'value' : tmp.suk}) })
							.insert({'bottom' : new Element('span', {'class' : 'eTitleLabel'}).update('Title') })
							.insert({'bottom' : new Element('input', {'type' : 'text', 'class' : 'eTitleBox rdcrnr lightBrdr', 'value' : tmp.title}) })
							.insert({'bottom' : new Element('select', {'class' : 'eActiveBox rdcrnr lightBrdr'}) })
							.insert({'bottom' : new Element('span', {'class' : 'col btns'}) 
								.insert({'bottom' : new Element('img', {'class' : 'editBtn', 'src': '/themes/default/images/save.png', 'alt': 'Save', 'title' : 'Save'}) })
								.insert({'bottom' : new Element('img', {'class' : 'cancelBtn', 'src': '/themes/default/images/cancel.gif', 'alt': 'Cancel', 'title' : 'Cancel'}) })
								
						});
		
		tmp.btnSpan.up('div.row').update(tmp.editPanel);
		
		
		
	}

});