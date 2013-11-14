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
			tmp.oneRowDiv = new Element('div', {'class' : 'singleRowDiv'});
			tmp.viewDiv = new Element('div', {'class' : 'viewProductDiv'}).store('main_item', item);
			tmp.viewDiv.insert({'bottom':  tmp.me._getItemRow(item.id, item.suk, item.title, item.active,  new Element('img', {'editId': item.id, 'class': 'btn', 'src': '/themes/default/images/edit.png', 'alt': 'EDIT'})
				.observe('click', function() {tmp.me.editItem(this); }) )
			}).insert({'bottom' : tmp.me._getAdditionalProductInfo(item) });
			tmp.oneRowDiv.insert({'bottom' : tmp.viewDiv}).addClassName(tmp.i % 2 === 1 ? 'even' : 'odd');
			tmp.resultDiv.insert({'bottom': tmp.oneRowDiv});
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
	
	,_getAdditionalProductInfo: function (item) {
		var tmp = {};
		tmp.me = this;
		
		tmp.outputArray = new Hash();
		
		console.debug(item);
		tmp.attributeArray = $H(item.attributes);
		tmp.keyArray = tmp.attributeArray.keys();
		tmp.keyArray.each(function(key) {
			tmp.attributeArray.get(key).each(function(item) {
				if(!tmp.outputArray[item.type.code + '~' + item.type.name])
					tmp.outputArray[item.type.code + '~' + item.type.name] = new Array();
				tmp.outputArray[item.type.code + '~' + item.type.name].push(item.attribute);
			})
		});
		
		tmp.div = new Element('div');
		console.debug(tmp.outputArray);
		
		tmp.outputArray.keys().each(function(key) {
			console.debug(key);
			tmp.div.insert({'bottom' : new Element('span').update(key)});
			tmp.outputArray.get(key).each(function(value) {
				tmp.div.insert({'bottom' : new Element('span').update(value)});
			});
			tmp.div.insert({'bottom' : new Element('span').update('--------------------------')});
		});
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
		
		tmp.activeOptions1 = new Element('option', {'value': 'true'}).update('true');
		tmp.activeOptions2 = new Element('option', {'value': 'false'}).update('false');
		
		tmp.editPanel = new Element('div', {'class' : 'editProductDiv'})
							.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(tmp.id) })
							.insert({'bottom' : new Element('span', {'class' : 'eSukLabel'}).update('Suk') })
							.insert({'bottom' : new Element('input', {'type' : 'text', 'class' : 'eSukBox rdcrnr lightBrdr', 'value' : tmp.suk}) })
							.insert({'bottom' : new Element('span', {'class' : 'eTitleLabel'}).update('Title') })
							.insert({'bottom' : new Element('input', {'type' : 'text', 'class' : 'eTitleBox rdcrnr lightBrdr', 'value' : tmp.title}) })
							.insert({'bottom' : new Element('select', {'class' : 'eActiveBox rdcrnr lightBrdr'}) 
									.insert(tmp.activeOptions1)
									.insert(tmp.activeOptions2)
							})
							.insert({'bottom' : new Element('span', {'class' : 'col btns'}) 
								.insert({'bottom' : new Element('img', {'class' : 'editBtn', 'src': '/themes/default/images/save.png', 'alt': 'Save', 'title' : 'Save'})
									.observe('click', function() {tmp.me.saveEditedItem(this); }) })
								.insert({'bottom' : new Element('img', {'class' : 'cancelBtn', 'src': '/themes/default/images/cancel.gif', 'alt': 'Cancel', 'title' : 'Cancel'})
									.observe('click', function() {tmp.me.cancelEdit(this); })
								})
						});
		
		tmp.btnSpan.up('div.singleRowDiv').insert({'bottom' : tmp.editPanel}).down('div.viewProductDiv').hide();
	}
	
	,cancelEdit: function(btn) {
		var tmp = {};
		tmp.me = this;
		
		tmp.me._hideShowAllEditPens(btn, true);
		tmp.rowDiv = $(btn).up('div.singleRowDiv');
		tmp.rowDiv.down('div.viewProductDiv').show();
		tmp.rowDiv.down('div.editProductDiv').remove();
	}
	
	,saveEditedItem: function(btn) {
		var tmp = {};
		tmp.me = this;
		try
		{
			tmp.editDiv = $(btn).up('div.editProductDiv');
			tmp.editProductId = tmp.editDiv.down('span.col.id').innerHTML;
			tmp.eSukValue = tmp.editDiv.down('span.eSukBox').getValue();
			//tmp.eTitleValue = tmp.editDiv.down('span.eTitleBox').value.trim();
			//tmp.eActiveValue = tmp.editDiv.down('span.eActiveBox').value;
			
			//console.debug(tmp.eSukValue);
			
			if(eSukValue === '' || eSukValue === undefined)
				throw 'SUK must be provided';
			if(eTitleValue === '' || eTitleValue === undefined)
				throw 'Title must be provided';
		}
		catch(e)
		{
			alert(e);
		}
		tmp.editDiv.down('span.col.id');
		
	}

});