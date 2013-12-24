/**
 * The control Js file
 */
var ProductImportViewJs = new Class.create();
ProductImportViewJs.prototype = {
	_pageJs: null, //the pageJs object
	_callbackIds: {}, //the callback ids
	
	//constructor
	initialize: function(pageJs, getSupLibInfoBtn, isImportingBtn, importBtn) {
		this._pageJs = pageJs;
		this._callbackIds.suplibinfobtn = getSupLibInfoBtn;
		this._callbackIds.isImportingBtn = isImportingBtn;
		this._callbackIds.importBtn = importBtn;
	}

	,_getFieldDiv: function(title, field) {
		return new Element('div', {'class': 'fieldDiv'})
			.insert({'bottom': new Element('span', {'class': 'title'}).update(title) })
			.insert({'bottom': new Element('span', {'class': 'field'}).update(field) });
	}

	,_getImportDiv: function(supplierSelBox, libSelBox, maxQty) {
		var tmp = {};
		tmp.me = this;
		tmp.maxQty = (maxQty || 'all');
		tmp.newDiv = new Element('div', {'class': 'productImportWrapper'})
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('span', {'class': 'inlineblock'}).update( tmp.me._getFieldDiv('Supplier:', supplierSelBox.writeAttribute('importinfo', 'supplierIds')))	})
				.insert({'bottom': new Element('span', {'class': 'inlineblock'}).update( tmp.me._getFieldDiv('Library:', libSelBox.writeAttribute('importinfo', 'libraryIds')))	})
				.insert({'bottom': new Element('span', {'class': 'inlineblock'}).update( tmp.me._getFieldDiv('Max Qty:', new Element('input', {'type': 'textbox', 'importinfo': 'maxQty', 'class': 'maxQty', 'value': 'all'}) ))	})
				.insert({'bottom': new Element('span', {'class': 'inlineblock'})
					.insert({'bottom': new Element('span', {'class': 'button submitbtn import'}).update('Import NOW') })
					.insert({'bottom': new Element('span', {'class': 'button submitbtn cancel'}).update('Cancel') })
				})
			})
		;
		return tmp.newDiv;
	}
	
	,_getSelBox: function (options) {
		var tmp = {};
		tmp.me = this;
		tmp.selBox = new Element('select');
		options.each(function(opt) {
			tmp.selBox.insert({'bottom': new Element('option', {'value': opt.id}).update(opt.name) });
		});
		return tmp.selBox;
	}
	
	,_showModalbox: function(supplierSelBox, libSelBox) {
		var tmp = {};
		tmp.me = this;
		tmp.div = tmp.me._getImportDiv(supplierSelBox, libSelBox);
		Modalbox.show(tmp.div, {
			'title': 'Do you want to import from:', 
			'width': 800,
			'afterLoad': function() {
				Modalbox.MBcontent.down('.submitbtn.cancel').observe('click', function() { Modalbox.hide(); });
			}
		});
		return this;
	}
	
	,_getImportPanel: function(supplier, lib) {
		var tmp = {};
		tmp.me = this;
		
		//trying to form the selection box
		tmp.supplierSelBox = tmp.libSelBox = null;
		if(supplier && supplier.id && supplier.name)
			tmp.supplierSelBox = tmp.me._getSelBox([{'id': supplier.id, 'name': supplier.name}]);
		if(lib && lib.id && lib.name)
			tmp.libSelBox = tmp.me._getSelBox([{'id': lib.id, 'name': lib.name}]);
		
		if(tmp.supplierSelBox === null || tmp.libSelBox === null) {
			tmp.me._pageJs.postAjax(tmp.me._callbackIds.suplibinfobtn, {'suppliers': tmp.supplierSelBox === null, 'libraries': tmp.libSelBox === null}, {
				'onLoading': function (sender, param) {},
				'onComplete': function (sender, param) {
					try {
						tmp.result = tmp.me._pageJs.getResp(param, false, true);
						if(!tmp.result.suppliers || !tmp.result.libraries)
							throw 'System Error: No information found/generated, contact BMV directly now!';
						tmp.defaultOps = [{'id': 'all', 'name': 'All'}];
						if(tmp.supplierSelBox === null)
							tmp.supplierSelBox = tmp.me._getSelBox(tmp.defaultOps.concat(tmp.result.suppliers));
						if(tmp.libSelBox === null)
							tmp.libSelBox = tmp.me._getSelBox(tmp.defaultOps.concat(tmp.result.libraries));
						tmp.me._showModalbox(tmp.supplierSelBox, tmp.libSelBox);
					} catch(e) {
						alert(e);
					}
				}
			});
		} else {
			tmp.me._showModalbox(tmp.supplierSelBox, tmp.libSelBox);
		}
	}
	
	,load: function(supplier, lib) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs.postAjax(tmp.me._callbackIds.isImportingBtn, {}, {
			'onLoading': function (sender, param) {},
			'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me._pageJs.getResp(param, false, true);
					if(!tmp.result.isImporting)
						tmp.me._getImportPanel(supplier, lib);
					else
						tmp.me._getImportPanel(supplier, lib);
				} catch(e) {
					alert(e);
				}
			}
		});
	}
};