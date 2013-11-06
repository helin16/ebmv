/**
 * The FrontEndPageAbstract Js file
 */
var FrontPageJs = new Class.create();
FrontPageJs.prototype = {
	productDetailsUrl: '/product/{id}' 
	
	//the callback ids
	,callbackIds: {}

	//constructor
	,initialize: function () {}
	
	,setCallbackId: function(key, callbackid) {
		this.callbackIds[key] = callbackid;
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
		tmp.productDiv = new Element('span', {'class': 'product griditem inlineblock cursorpntr'})
			.insert({'bottom': tmp.me._getProductImgDiv(product.attributes.image_thumb || null) })
			.insert({'bottom': new Element('div', {'class': 'product_details'})
				.insert({'bottom': new Element('div', {'class': 'product_title'}).update(product.title) })
			})
			.observe('click', function(){ tmp.me.showDetailsPage(product.id); })
		;
		return tmp.productDiv;
	}
	//redirect the product to detailspage
	,showDetailsPage: function(productId) {
		window.location = this.productDetailsUrl.replace('{id}', productId);
	}
	//getting the product image div
	,_getProductImgDiv: function (images) {
		if(images === undefined || images === null || images.size() === 0)
			return new Element('div', {'class': 'product_image noimage'});
		return new Element('img', {'class': 'product_image', 'src': '/asset/get?id=' + images[0].attribute});
	}
	//getting the current user
	,getUser: function(btn, afterFunc, loadingFunc) {
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
					tmp.me.showLoginPanel(btn);
				}
			}
		});
	}
	//showing login panel
	,showLoginPanel: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'floatingpanel'})
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('span', {'class': 'inlineblock title'}).update('Username:') })
				.insert({'bottom': new Element('span', {'class': 'inlineblock content'})
					.insert({'bottom': new Element('input', {'type': 'textbox', 'class': 'username', 'placeholder': 'Username'}) })
				})
			})
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('span', {'class': 'inlineblock title'}).update('Password:') })
					.insert({'bottom': new Element('span', {'class': 'inlineblock content'})
					.insert({'bottom': new Element('input', {'type': 'password', 'class': 'password', 'placeholder': 'Password'}) })
				})
			})
			.insert({'bottom': new Element('div', {'class': 'row btns'})
				.insert({'bottom': new Element('input', {'class': 'loginbtn button rdcrnr', 'value': 'Login', 'type': 'button'})
						.observe('click', function() {
							tmp.me._login($F($(this).up('.floatingpanel').down('.username')), $F($(this).up('.floatingpanel').down('.password')), null, function() {
								window.location = document.URL;
							});
					})
				})
				.insert({'bottom': new Element('input', {'class': 'loginbtn button rdcrnr', 'value': 'Cancel', 'type': 'button'})
					.observe('click', function() {
						$(this).up('.floatpanelwrapper').remove();
						$(btn).disabled = false;
						$(btn).value = $(btn).readAttribute('originvalue');
					})
				})
			});
		$(btn).insert({'after': tmp.newDiv.wrap(new Element('div', {'class': 'loginpanel floatpanelwrapper'}))});
	}

	,_login: function (username, password, loadingFunc, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('loginUser'), {'username': username, 'password': password}, {
			'onLoading': function () {
				if(typeof(loadingFunc) === 'function')
					loadingFunc();
			}
			,'onComplete': function(sender, param) {
				if(typeof(afterFunc) === 'function')
					afterFunc();
			}
		});
	}
};
