/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	
	resultDivId: '', //the result div for the product list
	getProductsBtn: '', //the callbackId for getting the products
	pagination: {'pageNo': 1, 'pageSize': 10},
	searchCriteria: {'searchString': '', 'categoryIds': [], 'searchOpt': '', 'searchCat' : '', 'language' : '', 'productType' : ''},
	getProductItemFunc: '_getProductGridItem',
	
	//constructor
	initialize: function(resultDivId, getProductsBtn) {
		this.resultDivId = resultDivId;
		this.getProductsBtn = getProductsBtn;
	}

	//show the products
	,showProducts: function(clear, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.clear = (clear === true ? true : false);
		if(tmp.clear === true)
		{
			this.pagination.pageNo = 1;
			$(tmp.me.resultDivId).update(tmp.me._getLoadingDiv());
		}
		pageJs.postAjax(this.getProductsBtn, {'pagination': tmp.me.pagination, 'searchCriteria':  tmp.me.searchCriteria}, {
			'onLoading': function () {
			}
			,'onComplete': function(sender, param) {
				if(tmp.clear === true)
					$(tmp.me.resultDivId).update('');
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(!tmp.result.pagination || tmp.result.pagination.totalRows === 0)
						throw 'Nothing found!';
					
					tmp.result.products.each(function(item){
						$(tmp.me.resultDivId).insert({'bottom': tmp.me[tmp.me.getProductItemFunc](item) });
					});
					$(tmp.me.resultDivId).insert({'bottom':tmp.me._getPaginationDiv(tmp.result.pagination) });
				} catch (e) {
					if(tmp.clear === true)
						$(tmp.me.resultDivId).update(e);
					else
						alert(e);
				}
				if(typeof(afterFunc) === 'function')
					afterFunc();
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
	
	//get pagination div
	,_getPaginationDiv: function(pagination) {
		var tmp = {};
		if(pagination.pageNumber >= pagination.totalPages)
			return;
		
		tmp.me = this;
		return new Element('div', {'class': 'pagination_wrapper fullwith'}).insert({'bottom': tmp.me._getPaginationBtn('Get more', pagination.pageNumber + 1) });
	}
	
	,changePage: function (btn, pageNo, pageSize) {
		var tmp = {};
		this.pagination.pageNo = pageNo;
		this.pagination.pageSize = pageSize;
		$(btn).update('Getting more ....').writeAttribute('disabled', true);
		this.showProducts(false, function() {
			$(btn).up('.pagination_wrapper').remove();
		});
	}
	
	,_getPaginationBtn: function (txt, pageNo) {
		var tmp = {};
		tmp.me = this;
		return new Element('span', {'class': 'fullwith button rdcrnr'})
			.update(txt)
			.observe('click', function() {
				tmp.me.changePage(this, pageNo, tmp.me.pagination.pageSize);
			})
		;
	}
	
	//get product list item
	,_getProductListItem: function(product) {
		var tmp = {};
		tmp.me = this;
		tmp.productDiv = new Element('div', {'class': 'product listitem'})
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol left'})
				.insert({'bottom': tmp.me._getProductImgDiv(product.attributes.image_thumb || null)
							.addClassName('cursorpntr')
							.observe('click', function(){ tmp.me.showDetailsPage(product.id); })
				})
			})
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol right'})
				.insert({'bottom': new Element('div', {'class': 'product_title'}).update(product.title)
					.addClassName('cursorpntr')
					.observe('click', function(){ tmp.me.showDetailsPage(product.id); })
				})
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
	
	//get product grid item
	,_getProductGridItem: function(product) {
		return this._getProductThumbnail(product);
	}
	
	,_getAttrString: function(attArray){
		return attArray.map(function(attr) { return attr.attribute || '';});
	}
});