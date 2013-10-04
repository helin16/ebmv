/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	
	resultDivId: '', //the result div for the product list
	
	initialize: function(resultDivId) {
		this.resultDivId = resultDivId;
	}

	,showProducts: function() {
		var tmp = {};
		tmp.me = this;
		tmp.resultDiv = new Element('div', {'class': 'productlist'}).update('test');
		
		$(this.resultDivId).update(tmp.resultDiv);
		return this;
	}
	,_getProduct: function(product) {
		var tmp = {};
		tmp.me = this;
		tmp.productDiv = new Element('div', {'class': 'product'})
			.insert({'bottom': new Element('div', {'class': 'title'}).update(product.title) })
			.insert({'bottom': new Element('div', {'class': 'title'}).update(product.name) })
		;
		return tmp.productDiv;
	}
});