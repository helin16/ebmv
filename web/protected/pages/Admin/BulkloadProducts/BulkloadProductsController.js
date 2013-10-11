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
});