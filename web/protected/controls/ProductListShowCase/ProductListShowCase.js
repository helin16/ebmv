/**
 * The ProductListShowCaseJs file
 */
var ProductListShowCaseJs = new Class.create();
ProductListShowCaseJs.prototype = Object.extend(new FrontPageJs(), {
	pagination: {'pageNo': 1, 'pageSize': 10}

	,fetch: function(callbackId, wrapperId) {
		var tmp = {};
		tmp.me = this;
		
		tmp.resultDiv = $(wrapperId).down('.list');
		tmp.me.postAjax(callbackId, {'pagnation': tmp.me.pagination}, {
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
	}
	
	//getting the loading div
	,_getLoadingDiv: function() {
		return new Element('span', {'class': 'loading'})
			.insert({'bottom': new Element('img', {'src': '/themes/default/images/loading.gif'})})
			.insert({'bottom': 'Loading ...'});
	}
});
