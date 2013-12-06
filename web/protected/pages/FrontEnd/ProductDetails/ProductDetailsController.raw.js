/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	product: null //the product object
	
	,readOnline: function(btn) {
		var tmp = {};
		tmp.me = this;
		$(btn).writeAttribute('originvalue', $F(btn));
		tmp.me.getUser(btn, function(){
				tmp.me._getLink(btn, 'read');
			}, function () {
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
				tmp.me._getLink(btn, 'download');
			}, function () {
				$(btn).disabled = true;
				$(btn).value = "Processing ...";
			}
		);
	}
	,_getLink: function(btn, type) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('geturl'), {'type': type}, {
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