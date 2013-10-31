/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new AdminPageJs(), {
	
	generateTemplate: function(button, loadLabel) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('downloadtemplate'), {}, {
			'onLoading': function (sender, param) {
				$(button).hide();
				$(loadLabel).show()
			},
			'onComplete': function (sender, param) {
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(tmp.result.fileName !== undefined && tmp.result.fileName !== null) {
						window.open(tmp.result.fileName);
					}
				} catch(e) {
					alert(e);
				}
				$(button).show();
				$(loadLabel).hide();
			}
		});
	}

	,downloadUrl: function(downloadbtn, outputDiv, url) {
		var tmp = {};
		tmp.me = this;
		$(downloadbtn).hide().insert({after: new Element('span', {'class':'loading'}).update('downloading ...')});
		tmp.me.postAjax(tmp.me.getCallbackId('downloadFile'), {'url': url}, {
			'onLoading': function (sender, param) {
			},
			'onComplete': function (sender, param) {
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(!tmp.result.totalCount || !tmp.result.filePath)
						throw 'System Error!';
					$(outputDiv).update(tmp.result.totalCount == 0 ? new Element('h1', {'class': 'errmsg'}).update('No record found!') : tmp.me._getStartImportDiv(downloadbtn, tmp.result.totalCount, outputDiv, tmp.result.filePath));
				} catch(e) {
					alert(e);
					$(downloadbtn).show();
				}
				$$('.loading').each(function(item) {item.remove();});
			}
		}, 240000);
	}
	
	,_getStartImportDiv: function(downloadbtn, totalCount, outputDiv, filePath) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'bulkload_wrapper'})
			.insert({'bottom': new Element('div', {'class':'title_div'})
				.insert({'bottom': new Element('span', {'class':'title'}).update('We found (' + totalCount + ') records!') })
				.insert({'bottom': new Element('span', {'class':'btns'})
					.insert({'bottom': new Element('input', {'class':'button rdcrnr', 'type':"button", 'value': 'start'})
						.observe('click', function(){
							tmp.me._importProduct(downloadbtn, this, outputDiv, 0, totalCount, filePath);
						})
					})
					.insert({'bottom': new Element('input', {'class':'button rdcrnr', 'type':"button", 'value': 'cancel'})
						.observe('click', function(){
							$(this).up('.bulkload_wrapper').remove();
							$(downloadbtn).show();
						})
					})
				})
			});
		return tmp.newDiv;
	}
	
	,_importProduct: function(downloadbtn, btn, outputDiv, index, totalCount, filePath) {
		var tmp = {};
		tmp.me = this;
		if(index >= totalCount) {
			tmp.me._delTmpFile(filePath);
			$(downloadbtn).show();
			return this;
		}
		
		tmp.rowResult = new Element('span', {'class': 'resultContent'}).update(' ... '); //'Error. skipped');
		$(btn).up('.btns').hide();
		$(outputDiv).insert({'bottom': new Element('div', {'class': 'resultRow'})
			.insert({'bottom': new Element('span', {'class': 'rowNo'}).update('Processing Record: ' + index) })
			.insert({'bottom': tmp.rowResult})
		});
		tmp.me.postAjax(tmp.me.getCallbackId('importProduct'), {'index': index, 'filePath': filePath}, {
			'onLoading': function (sender, param) {
			},
			'onComplete': function (sender, param) {
				try {
					tmp.result = pageJs.getResp(param, false, true);
					tmp.rowResult.update(tmp.result.product ? tmp.me._getProductDiv(tmp.result.product) : 'Error. skipped');
					tmp.me._importProduct(downloadbtn, btn, outputDiv, index + 1, totalCount, filePath);
				} catch(e) {
					alert(e);
					$(btn).up('.btns').show();
				}
			}
		});
	}
	
	,_delTmpFile: function(filePath) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('deleteTmpFile'), { 'filePath': filePath}, {});
	}
	
	,_getProductDiv: function(product) {
		return 'Loaded!';
	}
});