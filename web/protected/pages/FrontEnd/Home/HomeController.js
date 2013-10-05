/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	
	resultDivId: '', //the result div for the product list
	getProductsBtn: '', //the callbackId for getting the products
	pagination: {'pageNo': 1, 'pageSize': 10},
	searchCriteria: {'searchString': '', 'categories': []},
	getProductItemFunc: '_getProductGridItem',
	
	//constructor
	initialize: function(resultDivId, getProductsBtn) {
		this.resultDivId = resultDivId;
		this.getProductsBtn = getProductsBtn;
	}

	//show the products
	,showProducts: function() {
		var tmp = {};
		tmp.me = this;
		$(this.resultDivId).update(tmp.me._getLoadingDiv());
		pageJs.postAjax(this.getProductsBtn, {'pagination': tmp.me.pagination, 'searchCriteria':  tmp.me.searchCriteria}, {
			'onComplete': function(sender, param) {
				tmp.resultDiv = new Element('div');
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(!tmp.result.pagination || tmp.result.pagination.totalRows === 0)
						throw 'Nothing found!';
					
					tmp.resultDiv.insert({'bottom': tmp.me._getPaginationDiv(tmp.result.pagination) });
					tmp.result.products.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me[tmp.me.getProductItemFunc](item) });
					});
					tmp.resultDiv.insert({'bottom':tmp.me._getPaginationDiv(tmp.result.pagination) });
				} catch (e) {
					tmp.resultDiv.update(new Element('div', {'class': 'errMsg'}).update(e));
				}
				$(tmp.me.resultDivId).update(tmp.resultDiv);
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
		if(pagination.totalPages === 1)
			return;
		
		tmp.me = this;
		tmp.paginationDiv = new Element('div', {'class': 'pagination_wrapper'});
		tmp.windowSize = 5;
		tmp.morePages = true; 
		if(pagination.totalPages < tmp.windowSize) {
			tmp.morePages = false;
			tmp.windowSize = pagination.totalPages
		}
		//if the page we are at is at 
		if(Math.ceil( tmp.windowSize / 2) > pagination.pageNumber) {
			tmp.pageStart = 1;
		} else {
			tmp.pageStart = pagination.pageNumber - (Math.ceil( tmp.windowSize / 2) - 1);
			if(Math.ceil( tmp.windowSize / 2) > (pagination.totalPages - pagination.pageNumber)) {
				tmp.pageStart = pagination.totalPages - tmp.windowSize + 1;
			}
			if(tmp.morePages) {
				tmp.paginationDiv
					.insert({'bottom': tmp.me._getPaginationBtn('<<', 1) })
					.insert({'bottom': tmp.me._getPaginationBtn('<', (pagination.pageNumber - 1) < 0 ? 1 : (pagination.pageNumber - 1)) })
				;
			}
		}
		
		$R(tmp.pageStart, tmp.pageStart + (tmp.windowSize - 1)).each(function(pageNo) {
			tmp.paginationDiv.insert({'bottom': tmp.me._getPaginationBtn(pageNo, pageNo).addClassName(pageNo === (pagination.pageNumber * 1) ? ' selected' : '') });
		});
		
		if(tmp.morePages && Math.ceil( tmp.windowSize / 2) <= (pagination.totalPages - pagination.pageNumber)) {
			tmp.paginationDiv
				.insert({'bottom': tmp.me._getPaginationBtn('>', pagination.pageNumber > pagination.totalPages ? pagination.totalPages : (pagination.pageNumber * 1 + 1)) })
				.insert({'bottom': tmp.me._getPaginationBtn('>>', pagination.totalPages) })
			;
		}
		return tmp.paginationDiv;
	}
	
	,changePage: function (pageNo, pageSize) {
		this.pagination.pageNo = pageNo;
		this.pagination.pageSize = pageSize;
		this.showProducts();
	}
	
	,_getPaginationBtn: function (txt, pageNo) {
		var tmp = {};
		tmp.me = this;
		return new Element('span', {'class': 'pagin_btn cursorpntr'})
			.update(txt)
			.observe('click', function() {
				tmp.me.changePage(pageNo, tmp.me.pagination.pageSize);
			})
		;
	}
	
	//get product list item
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
	
	//get product grid item
	,_getProductGridItem: function(product) {
		var tmp = {};
		tmp.me = this;
		tmp.productDiv = new Element('span', {'class': 'product griditem inlineblock cursorpntr'})
			.insert({'bottom': tmp.me._getProductImgDiv(product.attributes.image_thumb || null) })
			.insert({'bottom': new Element('div', {'class': 'product_details'})
				.insert({'bottom': new Element('div', {'class': 'product_title'}).update(product.title) })
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