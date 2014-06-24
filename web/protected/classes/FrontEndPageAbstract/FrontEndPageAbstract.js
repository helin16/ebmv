/**
 * The FrontEndPageAbstract Js file
 */
var FrontPageJs = new Class.create();
FrontPageJs.prototype = {
	productDetailsUrl: '/product/{id}' 
		
	,_currentLib: null //the id of current library
	
	//the callback ids
	,callbackIds: {}

	//constructor
	,initialize: function () {}
	
	,setCallbackId: function(key, callbackid) {
		this.callbackIds[key] = callbackid;
		return this;
	}
	
	,getCallbackId: function(key) {
		if(this.callbackIds[key] === undefined || this.callbackIds[key] === null)
			throw 'Callback ID is not set for:' + key;
		return this.callbackIds[key];
	}
	
	//posting an ajax request
	,postAjax: function(callbackId, data, requestProperty, timeout) {
		var tmp = {};
		tmp.request = new Prado.CallbackRequest(callbackId, requestProperty);
		tmp.request.setCallbackParameter(data);
		tmp.timeout = (timeout || 30000);
		if(tmp.timeout < 30000) {
			tmp.timeout = 30000;
		}
		tmp.request.setRequestTimeOut(tmp.timeout);
		tmp.request.dispatch();
		return tmp.request;
	}
	//parsing an ajax response
	,getResp: function (response, expectNonJSONResult, noAlert) {
		var tmp = {};
		tmp.expectNonJSONResult = (expectNonJSONResult !== true ? false : true);
		tmp.result = response;
		if(tmp.expectNonJSONResult === true)
			return tmp.result;
		if(!tmp.result.isJSON()) {
			tmp.error = 'Invalid JSON string: ' + tmp.result;
			if (noAlert === true)
				throw tmp.error;
			else 
				return alert(tmp.error);
		}
		tmp.result = tmp.result.evalJSON();
		if(tmp.result.errors.size() !== 0) {
			tmp.error = 'Error: \n\n' + tmp.result.errors.join('\n');
			if (noAlert === true)
				throw tmp.error;
			else 
				return alert(tmp.error);
		}
		return tmp.result.resultData;
	}
	//format the currency
	,getCurrency: function(number, dollar, decimal, decimalPoint, thousandPoint) {
		var tmp = {};
		tmp.decimal = (isNaN(decimal = Math.abs(decimal)) ? 2 : decimal);
		tmp.dollar = (dollar == undefined ? "$" : dollar);
		tmp.decimalPoint = (decimalPoint == undefined ? "." : decimalPoint);
		tmp.thousandPoint = (thousandPoint == undefined ? "," : thousandPoint);
		tmp.sign = (number < 0 ? "-" : "");
		tmp.Int = parseInt(number = Math.abs(+number || 0).toFixed(tmp.decimal)) + "";
		tmp.j = (tmp.j = tmp.Int.length) > 3 ? tmp.j % 3 : 0;
		return tmp.dollar + tmp.sign + (tmp.j ? tmp.Int.substr(0, tmp.j) + tmp.thousandPoint : "") + tmp.Int.substr(tmp.j).replace(/(\d{3})(?=\d)/g, "$1" + tmp.thousandPoint) + (tmp.decimal ? tmp.decimalPoint + Math.abs(number - tmp.Int).toFixed(tmp.decimal).slice(2) : "");
	}
	//do key enter
	,keydown: function (event, enterFunc, nFunc) {
		//if it's not a enter key, then return true;
		if(!((event.which && event.which == 13) || (event.keyCode && event.keyCode == 13))) {
			if(typeof(nFunc) === 'function') {
				nFunc();
			}
			return true;
		}
		
		if(typeof(enterFunc) === 'function') {
			enterFunc();
		}
		return false;
	}
	//getting te product thumbnail div
	,_getProductThumbnail: function(product) {
		var tmp = {};
		tmp.me = this;
		tmp.productDiv = new Element('div', {'class': 'thumbnail nodefault', 'title': product.title})
			.insert({'bottom': new Element('a', {'href': tmp.me.getProductDetailsUrl(product.id) })
				.update(tmp.me._getProductImgDiv(product.attributes.image_thumb || null)) 
			})
			.insert({'bottom': new Element('div', {'class': 'caption'})
				.insert({'bottom': product.title })
			})
		;
		return tmp.productDiv;
	}
	//redirect the product to detailspage
	,showDetailsPage: function(productId) {
		window.location = this.getProductDetailsUrl(productId);
	}
	
	//getting the product details page's url
	,getProductDetailsUrl: function (productId) {
		return this.productDetailsUrl.replace('{id}', productId);
	}
	
	//getting the product image div
	,_getProductImgDiv: function (images) {
		if(images === undefined || images === null || images.size() === 0)
			return new Element('img', {'src': '/themes/images/no_image_found.jpg', 'data-src': "holder.js/100%x180"});
		return new Element('img', {'src': '/asset/get?id=' + images[0].attribute, 'data-src': "holder.js/100%x180"});
	}
	//getting the current user
	,getUser: function(btn, afterFunc, loadingFunc, cancelLoginFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('getUser'), {}, {
			'onLoading': function () {
				if(typeof(loadingFunc) === 'function')
					loadingFunc();
			}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(typeof(afterFunc) === 'function')
						afterFunc();
				} catch(e) {
					jQuery('#' + btn.id).popover({
						'html': true,
						'placement': 'auto',
						'title': '登陆/登陸/Sign In',
						'content': tmp.me._getLoginPanel(btn, cancelLoginFunc),
						'container': 'body'
					})
					.popover('show');
//					jQuery('.popoverbtn').not(jQuery('#' + btn.id)).popover('hide').button('reset');
//					tmp.me.showLoginPanel(btn, cancelLoginFunc);
				}
			}
		});
	}
	//showing login panel
	,_getLoginPanel: function(btn, cancelLoginFunc) {
		var tmp = {};
		tmp.me = this;
		return new Element('div', {'class': 'login-form loginpanel', 'role': 'form'})
			.insert({'bottom': new Element('div', {'class': 'row msgpanel'}) })
			.insert({'bottom': new Element('div', {'class': 'form-group'})
				.insert({'bottom': new Element('label', {'for': 'username'}).update('图书馆卡号/圖書館卡號/Library Card No.') })
				.insert({'bottom': new Element('div', {'class': 'input-group'})
					.insert({'bottom': new Element('span', {'class': 'input-group-addon'}) 
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-user'}) })
					})
					.insert({'bottom': new Element('input', {'id': 'username', 'type': 'text', 'class': 'form-control username', 'placeholder': 'Username', 'required': true, 'autofocus': true}) 
						.observe('keydown', function(event) {
							pageJs.keydown(event, function(){$(Event.element(event)).up('.loginpanel').down('.loginbtn').click();});
						})
					})
				})
			})
			.insert({'bottom': new Element('div', {'class': 'form-group'})
				.insert({'bottom': new Element('label', {'for': 'password'}).update('密码/密碼/PIN') })
				.insert({'bottom': new Element('div', {'class': 'input-group'})
					.insert({'bottom': new Element('span', {'class': 'input-group-addon'}) 
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-lock'}) })
					})
					.insert({'bottom': new Element('input', {'id': 'password', 'type': 'password', 'class': 'form-control password', 'placeholder': 'Password', 'required': true}) 
						.observe('keydown', function(event) {
							pageJs.keydown(event, function(){$(Event.element(event)).up('.loginpanel').down('.loginbtn').click();});
						})
					})
				})
			})
			.insert({'bottom': new Element('div', {'class': 'form-group btns'})
				.insert({'bottom': new Element('span', {'id': 'pop_login_btn', 'class': 'loginbtn btn btn-sm btn-primary btn-block iconbtn', 'data-loading-text': '登陆中/登陸中/Processing...'})
					.insert({'bottom': new Element('div', {'class': 'btnname'})
						.insert({'bottom': '登陆/登陸' })
						.insert({'bottom': new Element('small').update('Sign in') })
					})
					.observe('click', function() {
						tmp.me._login(this, null, function() {
							window.location = document.URL;
						});
					})	
				})
				.insert({'bottom': new Element('span', {'class': 'btn btn-sm btn-default btn-block iconbtn'})
					.insert({'bottom': new Element('div', {'class': 'btnname'})
						.insert({'bottom': '取消/撤消' })
						.insert({'bottom': new Element('small').update('Cancel') })
					})
					.observe('click', function() {
						jQuery(btn).popover('hide');
						if(typeof(cancelLoginFunc) === 'function')
							cancelLoginFunc();
					})
				})
			});
	}
	
	,_getErrMsg: function (msg) {
		return new Element('span', {'class': 'errmsg smalltxt'}).update(msg);
	}

	,_login: function (btn, loadingFunc, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.panel = $(btn).up('.loginpanel');
		tmp.usernamebox = tmp.panel.down('.username');
		tmp.passwordbox = tmp.panel.down('.password');
		if(tmp.me._preLogin(tmp.usernamebox, tmp.passwordbox) === false) {
			return;
		}
		
		tmp.loadingMsg = new Element('div', {'class': 'loadingMsg'}).update('log into system ...');
		tmp.me.postAjax(tmp.me.getCallbackId('loginUser'), {'username': $F(tmp.usernamebox), 'password': $F(tmp.passwordbox)}, {
			'onLoading': function () {
				$(btn).up('.row').hide().insert({'after': tmp.loadingMsg });
				tmp.panel.down('.msgpanel').update('');
				if(typeof(loadingFunc) === 'function')
					loadingFunc();
			}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(typeof(afterFunc) === 'function')
						afterFunc();
				}
				catch(e)
				{
					$(tmp.usernamebox).select();
					tmp.panel.down('.msgpanel').update(tmp.me._getErrMsg(e));
				}
				tmp.loadingMsg.remove();
				$(btn).up('.row').show();
			}
		});
	}
	/**
	 * pre checking for login
	 */
	,_preLogin: function (usernamebox, passwordbox) {
		var tmp = {};
		tmp.me = this;
		tmp.loginPanel = $(usernamebox).up('.loginpanel');
		//cleanup error msg
		tmp.loginPanel.getElementsBySelector('.has-error').each(function(item) {
			item.removeClassName('has-error');
		});
		tmp.loginPanel.down('.msgpanel').update('');
		
		tmp.me.errorMsg = '';
		if($F(usernamebox).blank()) {
			$(usernamebox).up('.form-group').addClassName('has-error');
			tmp.me.errorMsg += '<span class="label label-danger">Username is required</span> ';
		}
		
		if($F(passwordbox).blank()) {
			$(passwordbox).up('.form-group').addClassName('has-error');
			tmp.me.errorMsg += '<span class="label label-danger">Password is required</span> ';
		}
		
		if(!tmp.me.errorMsg.blank()) {
			tmp.loginPanel.down('.msgpanel').update(tmp.me.errorMsg);
			return false;
		}
		return true;
	}
};
