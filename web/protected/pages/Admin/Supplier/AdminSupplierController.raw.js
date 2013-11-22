/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CrudPageJs(), {
	
	types: null //the information types
	
	,_getItemRow: function (item, option) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class' : 'row', 'item_id': item.id}).store('item', item)
			.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(item.id) })
			.insert({'bottom' : new Element('span', {'class' : 'col name'}).update(item.name) })	
			.insert({'bottom' : new Element('span', {'class' : 'col connector'}).update(item.connector) })	
			.insert({'bottom' : new Element('span', {'class' : 'col active'}).update(item.istitle === true ? item.active : new Element('input', {'type': 'checkbox', 'checked': item.active, 'disabled': true})) })	
			.insert({'bottom' : new Element('span', {'class' : 'col btns'}).update(option) })
			.insert({'bottom' : tmp.me._getInfoDiv(item) });
		return tmp.div;
	}

	,_getItemRowEditBtn: function(item) {
		var tmp = {};
		tmp.me = this;
		return new Element('span')
			.insert({'bottom': new Element('span', {'class': 'btn editbtn', 'title': 'EDIT'})
				.observe('click', function() {tmp.me.editItem(this); })
			})
			.insert({'bottom': new Element('span', {'class': 'btn delbtn', 'title': 'DELETE'})
				.observe('click', function() {tmp.me.delItems([item.id]); })
			});
	}
	
	,_hideShowAllEditPens: function () {
		var tmp = {};
		tmp.savePanel = $(this.resultDivId).down('.savePanel');
		if(tmp.savePanel)
			tmp.savePanel.down('.cancelBtn').click();
		return this;
	}

	,showEditPanel: function (btn, isNEW) {
		var tmp = {};
		tmp.me = this;
		if(isNEW === true) {
			$(tmp.me.resultDivId).down('.row.titleRow').insert({'after': tmp.me._getSavePanel({}, 'addDiv') });
		} else {
			tmp.row = $(btn).up('.row');
			tmp.row.replace(this._getSavePanel(tmp.row.retrieve('item'), 'editDiv'));
		}
		return this;
	}
	
	,_getResultDiv: function(items, includetitlerow, itemrowindex) {
		var tmp = {};
		tmp.me = this;
		tmp.includetitlerow = (includetitlerow === false ? false : true);
		
		tmp.resultDiv = new Element('div');
		if(tmp.includetitlerow === true)
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow({'id': 'id', 'name': 'Name', 'connector': 'Connector Script', 'active': 'active', 'istitle': true}, new Element('span', {'class': 'button padding5 rdcrnr'}).update('Create NEW')
					.observe('click', function(){ tmp.me.createItem(this); })
			).addClassName('titleRow') });
		tmp.i = (itemrowindex || 0);
		items.each(function(item) {
			tmp.resultDiv.insert({'bottom':  tmp.me._getItemRow(item, tmp.me._getItemRowEditBtn(item)).addClassName(tmp.i % 2 === 1 ? 'even' : 'odd')
			});
			tmp.i++;
		});
		return tmp.resultDiv;
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
					tmp.attrValeusDiv.insert({'bottom': new Element('div', {'class': 'attr_value'}).update(attr.value) });
						
				});
				tmp.attrDiv.insert({'bottom': tmp.attrValeusDiv });
				tmp.div.insert({'bottom': tmp.attrDiv });
			};
		});
		return tmp.div;
	}
	
	,cancelEdit: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.item = $(btn).up('.savePanel').retrieve('item');
		//if this is for creating then remove this panel
		if(tmp.item.id === undefined || tmp.item.id === null) {
			$(btn).up('.savePanel').remove();
		} else {
			$(btn).up('.savePanel').replace(tmp.me._getItemRow(tmp.item, tmp.me._getItemRowEditBtn(tmp.item)));
		}
		return this;
	}
	
	,_collectSavePanel: function (saveBtn) {
		var tmp = {};
		tmp.me = this;
		tmp.hasError = false;
		tmp.data = {};
		tmp.savePanel = $(saveBtn).up('.savePanel');
		tmp.item = tmp.savePanel.retrieve('item');
		
		tmp.data['id'] = (tmp.item.id === null || tmp.item.id === undefined ? '' : tmp.item.id);
		//clearup all the error messages
		tmp.savePanel.getElementsBySelector('.hasError').each(function(div){ 
			if(div.down('.errmsg'))
				div.down('.errmsg').remove(); 
			div.removeClassName('hasError');
		});
		
		//getting all column for a supplier
		tmp.savePanel.getElementsBySelector('[colname]').each(function(field) {
			tmp.fieldValue = $F(field);
			tmp.field = field.readAttribute('colname');
			if(tmp.fieldValue.blank() && field.readAttribute('noblank')) {
				$(field).up('.fielddiv').addClassName('hasError').down('.value').insert({'after': new Element('span', {'class': 'errmsg smalltxt'}).update(tmp.field + ' is required!') });
				tmp.hasError = true;
			}
			tmp.data[tmp.field] = $F(field);
		});
		
		//getting all information
		tmp.attrs = [];
		tmp.savePanel.getElementsBySelector('[attr_id]').each(function(field) {
			tmp.fieldValue = $F(field);
			tmp.attrId = field.readAttribute('attr_id');
			tmp.attrTypeId = field.readAttribute('attr_type_id');
			if(tmp.fieldValue.blank() && field.readAttribute('noblank')) {
				$(field).up('.fielddiv').addClassName('hasError').down('.value').insert({'after': new Element('span', {'class': 'errmsg smalltxt'}).update('required!') });
				tmp.hasError = true;
			}
			tmp.attrs.push({'id': tmp.attrId, 'typeId': tmp.attrTypeId, 'value': tmp.fieldValue});
		});
		tmp.data['info'] = tmp.attrs;
		return tmp.hasError === true ? null : tmp.data;
	}
	
	//after saving the items
	,_afterSaveItems: function (saveBtn, result) {
		var tmp = {};
		tmp.item = result.items[0];
		$(saveBtn).up('.savePanel').replace(this._getItemRow(tmp.item, this._getItemRowEditBtn(tmp.item)));
		return this;
	}
	
	,_getSavePanel: function (item, cssClass) {
		var tmp = {};
		tmp.me = this;
		tmp.isNew = (item.id === undefined || item.id === null);
		tmp.newDiv = new Element('div', {'class': 'savePanel'}).addClassName(cssClass).store('item', item)
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Name', new Element('input', {'value': (tmp.isNew ? '': item.name), "class": "txt value", 'colname': 'name', 'noblank': true}) ) })
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Connector', new Element('input', {'value': (tmp.isNew ? '': item.connector), "class": "txt value", 'colname': 'connector', 'noblank': true}) ) })
			.insert({'bottom':  tmp.me._getSaveFieldDiv('Act?', new Element('input', {'type': 'checkbox', "class": "value", 'checked': (tmp.isNew === true ? true : item.active), 'disabled': tmp.isNew, 'colname': 'active'}) ) })
			.insert({'bottom':  new Element('span', {'class': 'button padding5 rdcrnr saveBtn'}).update('Save').observe('click', function() { tmp.me.saveEditedItem(this); }) })
			.insert({'bottom':  new Element('span', {'class': 'button padding5 rdcrnr cancelBtn'}).update('Cancel').observe('click', function() { tmp.me.cancelEdit(this); }) })
			.insert({'bottom':  tmp.me._getSaveAttrPanel(item.info) });
		return tmp.newDiv;
	}
	
	,_getNewAttrDiv: function() {
		var tmp = {};
		tmp.me = this;
		tmp.typeSelection = new Element('select').update(new Element('option', {'value': ''}).update('Pls Select:')).observe('change', function() {
			$(this).up('.fielddiv').replace(tmp.me._getSaveFieldDiv($(this).options[$(this).selectedIndex].innerHTML,  new Element('input', {'value': '', 'class': 'txt value', 'attr_id': '', 'attr_type_id':$F(this), 'noblank': true}), true));
		});
		tmp.me.types.each(function(type) {
			tmp.typeSelection.insert({'bottom': new Element('option', {'value': type.id}).update(type.name) });
		});
		return tmp.me._getSaveFieldDiv('Please Select a type: ',  tmp.typeSelection, true);
	}
	
	,_getSaveAttrPanel: function(attrs) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {"class": "attrs_div"}); 
		$H(attrs).each(function(itemArr) {
			if(typeof(itemArr.value) === 'object') {
				itemArr.value.each(function(attr) {
					tmp.div.insert({'bottom': tmp.me._getSaveFieldDiv(attr.type.name, new Element('input', {'value': attr.value, 'class': 'txt value', 'attr_id': attr.id, 'attr_type_id': attr.type.id, 'noblank': true}), true) });
				});
			};
		});
		tmp.div.insert({'bottom': tmp.me._getSaveFieldDiv('', new Element('span', {'class': 'button padding5 rdcrnr'}).update('NEW Info')
					.observe('click', function() {
						$(this).up('.fielddiv').insert({'before': tmp.me._getNewAttrDiv() });
					})
		) });
		return tmp.div;
	}
	
	,_getSaveFieldDiv: function (fieldName, field, showDelBtn) {
		var tmp = {};
		tmp.me = this;
		tmp.showDelBtn = (showDelBtn === true ? true : false);
		tmp.titleDiv = new Element('span', {'class': 'title'}).update(fieldName);
		if(tmp.showDelBtn === true) {
			tmp.titleDiv.insert({'bottom': new Element('span', {'class': 'inlineblock btns'})
				.insert({'bottom': new Element('span', {'class': 'delBtn cursorpntr'}).update('x')
					.observe('click', function(){
						if(!confirm('You are about to delete this attribute.\n Continue?'))
							return false;
						$(this).up('.fielddiv').fade();
						if($(this).up('.fielddiv').down('.value'))
							$(this).up('.fielddiv').down('.value').writeAttribute('deactivated', true);
					})
				})
			});
		}
		return new Element('div', {'class': 'fielddiv inlineblock padding5'})
			.insert({'bottom':  tmp.titleDiv})
			.insert({'bottom':  new Element('span', {'class': 'content'}).update(field) });
	}
});