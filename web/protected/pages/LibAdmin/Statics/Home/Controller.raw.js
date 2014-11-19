/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	pagination: {pageNo: 1, pageSize: 30} //this is the pagination for the crud page
	,_htmlIds: {'resultListId': '', 'totalCountDivId': ''}
	/**
	 * Setting the HTML IDs
	 */
	,setHTMLIDs: function(resultListId, totalCountDivId) {
		this._htmlIds.resultListId = resultListId;
		this._htmlIds.totalCountDivId = totalCountDivId;
		return this;
	}
	//getting the pagination buttons
	,_getPaginBtns: function(pagination) {
		if(pagination.pageNumber >= pagination.totalPages)
			return;
		var tmp = {};
		tmp.me = this;
		tmp.paginDiv = new Element('tfoot', {'class': 'paginDiv'})
			.insert({'bottom': new Element('tr')
				.insert({'bottom': new Element('td', {'colspan': 6})
					.insert({'bottom': new Element('span', {'class': 'btn btn-primary', 'data-loading-text': 'Getting more ...'}).update('Get more')
						.observe('click', function() {
							tmp.btn = this;
							tmp.me._signRandID(tmp.btn);
							jQuery('#' + tmp.btn.id).button('loading');
							tmp.me.load(tmp.me.pagination.pageNo + 1, tmp.me.pagination.pageSize, false, function(){
								$(tmp.btn).up('.paginDiv').remove();
							});
						})
					})
				})
			});
		return tmp.paginDiv;
	}
	,_getResultRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td');
		tmp.newRow = new Element('tr')
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Views' : row.statics[1] && row.statics[1].value && !row.statics[1].value.blank() ? row.statics[1].value : '0') })
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Borrows' : row.statics[2] && row.statics[2].value && !row.statics[2].value.blank() ? row.statics[2].value : '0') })
			.insert({'bottom': new Element(tmp.tag).update(row.title) })
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'ISBN' : row.attributes.isbn ? row.attributes.isbn[0].attribute : '') })
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Author' : row.attributes.author ? row.attributes.author[0].attribute : '') })
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Publisher' : row.attributes.publisher ? row.attributes.publisher[0].attribute : '') })
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Publish Date' : row.attributes.publish_date ? row.attributes.publish_date[0].attribute : '') })
			;
		return tmp.newRow;
	}
	/**
	 * load of list
	 */
	,load: function(pageNo, pageSize, resetResult, loadedFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.me.pagination.pageNo = (pageNo || tmp.me.pagination.pageNo);
		tmp.me.pagination.pageSize = (pageSize || tmp.me.pagination.pageSize);
		tmp.resetResult = (resetResult === false ? false : true);
		tmp.me.postAjax(tmp.me.getCallbackId('getStats'), {'pagination': tmp.me.pagination}, {
			'onLoading': function (sender, param) {},
			'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.items || tmp.result.items === undefined || tmp.result.items === null)
						throw 'No item found/generated'; 
					if(tmp.resetResult === true) {
						$(tmp.me._htmlIds.resultListId).update('')
							.insert({'bottom': tmp.me._getResultRow({'title': 'TITLE'}, true).wrap(new Element('thead')) });
						if($(tmp.me._htmlIds.totalCountDivId))
							$(tmp.me._htmlIds.totalCountDivId).update(tmp.result.pagination.totalRows);
					}
					tmp.tbody = $(tmp.me._htmlIds.resultListId).down('tbody');
					if(!tmp.tbody) {
						$(tmp.me._htmlIds.resultListId).insert({'bottom': tmp.tbody = new Element('tbody') });
					}
					tmp.result.items.each(function(item) {
						tmp.tbody.insert({'bottom': tmp.me._getResultRow(item) });
					});
					if(typeof(loadedFunc) === 'function') {
						loadedFunc();
					}
					
					$(tmp.me._htmlIds.resultListId).insert({'bottom': tmp.me._getPaginBtns(tmp.result.pagination)});
				} catch(e) {
					$(tmp.me._htmlIds.resultListId).update(tmp.me.getAlertBox('ERROR', e).addClassName('alert-danger'));
				}
			}
		});
		return tmp.me;
	}
	,_submitExport: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.fromDate = $F($$('.date-picker[export-crieria=date-from]').first());
		tmp.toDate = $F($$('.date-picker[export-crieria=date-to]').first());
		tmp.me.postAjax(tmp.me.getCallbackId('exportSats'), {'fromDate': tmp.fromDate, 'toDate': tmp.toDate}, {
			'onLoading': function() {
				tmp.me._signRandID(btn);
				jQuery('#' + btn.id).button('loading');
			}
			,'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.items || tmp.result.items === undefined || tmp.result.items === null)
						throw 'No item found/generated';
					tmp.i=0;
					tmp.resultArray = [];
					tmp.result.items.each(function(item){
						tmp.title = [];
						tmp.row = [];
						$H(item).each(function(value){
							tmp.title.push('' + value.key + '');
							tmp.row.push('' + value.value + '');
						});
						if(tmp.i === 0)
							tmp.resultArray.push( tmp.title.join(', '));
						tmp.resultArray.push(tmp.row.join(', '));
						tmp.i = (tmp.i * 1 +1);
					});
					tmp.me.hideModalBox();
					tmp.csvContent = "data:text/csv;charset=utf-8," + tmp.resultArray.join('\n');
					$$('body')[0].insert({'bottom': tmp.newBtn = new Element('a', {"href": encodeURI(tmp.csvContent), "download": "my_data.csv"})  });
					tmp.newBtn.click();
					tmp.newBtn.remove();
				} catch(e) {
					tmp.me.showModalBox('ERROR', '<h4 class="text-danger">' + e + '</h4>');
				}
			}
			,'onComplete': function() {
				jQuery('#' + btn.id).button('reset');
			}
		});
		return tmp.me;
	}
	/**
	 * exporting data
	 */
	,exportAll: function(btn, tableId) {
		var tmp = {};
		tmp.me = this;
		tmp.usingIE = (window.navigator.userAgent.indexOf("MSIE ") > 0 || window.navigator.userAgent.indexOf("Trident/") > 0);
		tmp.now = new Date();
		tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('div', {'class': 'form-horizontal'})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'col-sm-2 control-label'}).update('From:') })
					.insert({'bottom': new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'type': 'text', 'class': 'form-control date-picker', 'placeholder': 'From Date', 'export-crieria': 'date-from', 'value': tmp.now.getFullYear() + '-' + ("00" + (tmp.now.getMonth() * 1 + 1)).slice(2) + '-01'}) }) 
					})
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'col-sm-2 control-label'}).update('To:') })
						.insert({'bottom': new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'type': 'text', 'class': 'form-control date-picker', 'placeholder': 'To Date', 'export-crieria': 'date-to', 'value': tmp.now.getFullYear() + '-' + ("00" + (tmp.now.getMonth() * 1 + 1)).slice(2) + '-' + ("00" + (tmp.now.getDate() * 1 + 1)).slice(2)}) }) 
					})
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'col-sm-offset-2 col-sm-10'})
						.insert({'bottom': tmp.exportBtn = new Element('a', {'class': 'btn ' + (tmp.usingIE === true ? 'btn-warning' : 'btn-primary'), 'href': "javascript: void(0);", 'data-loading-text': 'Exporting ... Please do NOT close this, while processing!'}).update((tmp.usingIE === true ? 'You can ONLY export this in NON IE browser, sorry!' : 'Export Now'))
							.observe('click', function() {
								tmp.me._submitExport(this);
							})
						}) 
					})
				})
			});
		if(tmp.usingIE === true)
			tmp.exportBtn.writeAttribute('disabled', true);
		pageJs.showModalBox('Please provide a date rage to export:', tmp.newDiv, false);
		$$('.date-picker[export-crieria]').each(function(item){
			tmp.me._signRandID(item);
			item.store('date-picker',new Prado.WebUI.TDatePicker({'ID': item.id, 'InputMode':"TextBox", 'PositionMode':"Bottom", 'Format':"yyyy-MM-dd"}) );
		});
	}
});