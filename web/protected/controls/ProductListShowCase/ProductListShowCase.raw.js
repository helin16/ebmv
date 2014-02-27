/**
 * The ProductListShowCaseJs file
 */
var ProductListShowCaseJs = new Class.create();
ProductListShowCaseJs.prototype = Object.extend(new FrontPageJs(), {
	pagination: {'pageNo': 1, 'pageSize': 10}
	,_langId: null//the languageId
	,_callbackId: '' //the callbackId
	,_wrapperId: '' //the wrapper id

	,fetch: function(callbackId, wrapperId) {
		var tmp = {};
		tmp.me = this;
		tmp.me._callbackId = callbackId; 
		tmp.me._wrapperId = wrapperId; 
		
		tmp.resultDiv = $(wrapperId).down('.list');
		tmp.me.postAjax(callbackId, {'pagnation': tmp.me.pagination, 'languageId': tmp.me._langId}, {
			'onLoading': function () {
				$(tmp.resultDiv).update(tmp.me._getLoadingDiv());
			}
			,'onComplete': function(sender, param) {
				tmp.listDiv = new Element('ul',{'class': 'jcarousel jcarousel-skin-tango'});
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(tmp.result.products.size() === 0) 
						throw 'No product found!';
					tmp.result.products.each(function(item){
						tmp.listDiv.insert({'bottom': tmp.me._getProductThumbnail(item).wrap(new Element('li')) });
					});
					$(tmp.resultDiv).update(tmp.listDiv);
					jQuery('#' + wrapperId + ' .list .jcarousel').jcarousel();
					
				} catch (e) {
					$(tmp.resultDiv).update(e);
				}
			}
		});
		return this;
	}
	
	//getting the loading div
	,_getLoadingDiv: function() {
		return new Element('span', {'class': 'loading'})
			.insert({'bottom': new Element('img', {'src': '/themes/default/images/loading.gif'})})
			.insert({'bottom': 'Loading ...'});
	}
	
	,changeLanguage: function(btn) {
		var tmp = {};
		tmp.me = this;
		$(btn).up('.langlist').getElementsBySelector('li.langitem').each(function(item){
			item.removeClassName('active');
		})
		tmp.me._langId = $(btn).addClassName('active').readAttribute('langid').strip();
		this.fetch(tmp.me._callbackId, tmp.me._wrapperId);
		return this;
	}
});
