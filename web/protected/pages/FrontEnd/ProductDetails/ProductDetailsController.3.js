/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	product: null //the product object
	
	,readOnline: function(readUrl, siteId, uid, pwd) {
		var tmp = {};
		tmp.me = this;
		tmp.readUrl = (readUrl || '');
		if(tmp.readUrl.blank())
		{
			alert('System Error: no where to read it!');
			return;
		}
		
		tmp.params = {'isbn': tmp.me.product.attributes.isbn[0].attribute, 'no': tmp.me.product.attributes.cno[0].attribute, 'siteID': siteId, 'uid': uid, 'pwd': pwd};
		window.open(tmp.readUrl + '?' + $H(tmp.params).toQueryString());
	}

	,download: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.originalValue = $F(btn);
		tmp.me.postAjax(tmp.me.getCallbackId('download'), {}, {
			'onLoading': function () {
				$(btn).disabled = true;
				$(btn).value = "Processing ...";
			}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(tmp.result.url)
						window.open(tmp.result.url);
				} catch(e) {
					alert(e);
				}
				$(btn).disabled = false;
				$(btn).value = tmp.originalValue;
			}, 120000
		});
	}
});