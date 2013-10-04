/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	
	resultDivId: '', //the result div for the product list
	getProductsBtn: '', //the callbackId for getting the products
	pagination: {'pageNo': 1, 'pageSize': 10},
	
	//constructor
	initialize: function(resultDivId, getProductsBtn) {
		this.resultDivId = resultDivId;
		this.getProductsBtn = getProductsBtn;
	}

	//show the products
	,showProducts: function() {
		var tmp = {};
		tmp.me = this;
		pageJs.postAjax(this.getProductsBtn, {'pagination': tmp.me.pagination}, {
			'onLoading': function () {
				$(this.resultDivId).update(tmp.me._getLoadingDiv());
			}
			,'onComplete': function(sender, param) {
				tmp.resultDiv = new Element('div');
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(tmp.result.size() === 0)
						throw 'Nothing found!';
					
					tmp.result.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me._getProductListItem(item) });
					});
					
				} catch (e) {
					tmp.resultDiv.update(new Element('div', {'class': 'errMsg'}).update(e));
				}
				$(tmp.me.resultDivId).update(tmp.resultDiv);
			}
		});
		return this;
	}
	
	,_getLoadingDiv: function() {
		return new Element('span', {'class': 'loading'})
			.insert({'bottom': new Element('img', {'src': '/themes/default/images/loading.gif'})})
			.insert({'bottom': 'Loading ...'});
	}
	
	//get product html
	,_getProductListItem: function(product) {
		var tmp = {};
		tmp.me = this;
		tmp.productDiv = new Element('div', {'class': 'product listitem'})
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol left'})
				.insert({'bottom': tmp.me._getProductImgDiv(product.attributes.image_thumb || null) })
			})
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol right'})
				.insert({'bottom': new Element('div', {'class': 'product_title'}).update(product.title) })
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': new Element('span', {'class': 'author inlineblock'})
						.insert({'bottom': new Element('label').update('Author:')})
						.insert({'bottom': new Element('span').update(product.attributes.author ? tmp.me._getAttrString(product.attributes.author).join(' ') : '')})
					})
					.insert({'bottom': new Element('span', {'class': 'product_isbn inlineblock textright'})
						.insert({'bottom': new Element('label').update('ISBN:')})
						.insert({'bottom': new Element('span').update(product.attributes.isbn ? tmp.me._getAttrString(product.attributes.isbn).join(' ') : '')})
					})
				})
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': new Element('span', {'class': 'product_publisher inlineblock'})
						.insert({'bottom': new Element('label').update('Publisher:')})
						.insert({'bottom': new Element('span').update(product.attributes.publisher ? tmp.me._getAttrString(product.attributes.publisher).join(' ') : '')})
					})
					.insert({'bottom': new Element('span', {'class': 'product_publish_date inlineblock textright'})
						.insert({'bottom': new Element('label').update('Publisher Date:')})
						.insert({'bottom': new Element('span').update(product.attributes.publish_date ? tmp.me._getAttrString(product.attributes.publish_date).join(' ') : '')})
					})
				})
				.insert({'bottom': new Element('div', {'class': 'product_description'}).update((product.attributes.description ? tmp.me._getAttrString(product.attributes.description).join(' ') : '')) })
			})
		;
		return tmp.productDiv;
	}
	
	,_getAttrString: function(attArray){
		return attArray.map(function(attr) { return attr.attribute || '';});
	}
	
	,_getProductImgDiv: function (images) {
		var tmp = {};
		if(images === undefined || images === null || images.size() === 0)
			return new Element('div', {'class': 'product_image noimage'});
		return new Element('div', {'class': 'product_image', 'src': images[0].attribute});
	}
});