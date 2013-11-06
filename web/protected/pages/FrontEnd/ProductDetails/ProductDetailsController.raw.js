/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	product: null //the product object
	
	,readOnline: function(btn, readUrl, siteId, uid, pwd) {
		var tmp = {};
		tmp.me = this;
		tmp.readUrl = (readUrl || '');
		if(tmp.readUrl.blank())
		{
			alert('System Error: no where to read it!');
			return;
		}
		$(btn).writeAttribute('originvalue', $F(btn));
		tmp.me.getUser(btn, function(){
				tmp.params = {'isbn': tmp.me.product.attributes.isbn[0].attribute, 'no': tmp.me.product.attributes.cno[0].attribute, 'siteID': siteId, 'uid': uid, 'pwd': pwd};
				window.open(tmp.readUrl + '?' + $H(tmp.params).toQueryString());
				$(btn).setValue($(btn).readAttribute('originvalue')).disabled = false;
			}
			,function () {
				$(btn).disabled = true;
				$(btn).value = "Processing ...";
			}
		);
	}

	,download: function(btn) {
		var tmp = {};
		tmp.me = this;
		$(btn).writeAttribute('originvalue', $F(btn));
		tmp.me.getUser(btn, function(){
				tmp.me._getDownLoadLink(btn);
			}, function () {
				$(btn).disabled = true;
				$(btn).value = "Processing ...";
			}
		);
	}
	,_getDownLoadLink: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('download'), {}, {
			'onLoading': function () {}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(tmp.result.url)
						window.open(tmp.result.url);
					if(tmp.result.redirecturl)
						window.location = tmp.result.redirecturl;
				} catch(e) {
					alert(e);
				}
				$(btn).disabled = false;
				$(btn).value = $(btn).readAttribute('originvalue');
			}
		}, 120000);
	}
});