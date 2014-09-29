/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	htmlIDs: {'totalCountDiv': '', 'listingDiv': ''}
	,pagination: {'pageNo': 1, 'pageSize': 30}
	,order: {} //the order object
	/**
	 * Getting the HTML IDs
	 */
	,setHTMLIDs: function(totalCountDiv, listingDiv, orderSummaryDiv) {
		this.htmlIDs.totalCountDiv = totalCountDiv;
		this.htmlIDs.listingDiv = listingDiv;
		this.htmlIDs.orderSummaryDiv = orderSummaryDiv;
		return this;
	}
	,_orderProduct: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.btn = btn;
		tmp.product = tmp.btn.up('.prodcut-row').retrieve('data');
		tmp.qty = $F(tmp.btn.up('.prodcut-row').down('.order-qty'));
		tmp.me.postAjax(tmp.me.getCallbackId('orderProduct'), {'orderId': tmp.me.order.id, 'productId': tmp.product.id, 'qty': tmp.qty}, {
			'onLoading': function () {}
			,'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.item)
						return;
					tmp.me.order.items.push(tmp.result.item);
					tmp.me._displayOrderSummary(tmp.me.order);
				} catch (e) {
					tmp.me.showModalBox('ERROR', e, true);
				}
			}
		})
		return tmp.me;
	}
	/**
	 * Getting a row for displaying the result
	 */
	,_getResultTableRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td');
		tmp.img = (tmp.isTitle === true ? '' : new Element('a').update(row.img.addClassName('list-thumbnail'))	);
		tmp.orderBtns = new Element('div', {'class': 'input-group input-group-sm'})
			.insert({'bottom': new Element('input', {'class': 'form-control order-qty', 'type': 'text', 'value': '1', 'style': 'padding: 4px;'}) })
			.insert({'bottom': new Element('span', {'class': 'input-group-btn'}) 
				.insert({'bottom': new Element('span', {'class': 'btn btn-success'})
					.update(new Element('span', {'class': 'glyphicon glyphicon-plus'})) 
					.observe('click', function(){
						tmp.me._orderProduct(this);
					})
				}) 
			});
		tmp.Qty = tmp.Qty;
		tmp.row = new Element('tr', {'class': 'prodcut-row'}).store('data', row)
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(tmp.img) })
			.insert({'bottom': new Element(tmp.tag).update(row.title) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2'}).update(row.isbn) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(row.author) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(row.publishDate) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(tmp.isTitle === true ? row.qty : tmp.Qty) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(tmp.isTitle === true ? '' : tmp.orderBtns) })
		return tmp.row;
	}
	//getting the loading div
	,_getLoadingDiv: function() {
		return new Element('span', {'class': 'loading'})
			.insert({'bottom': new Element('img', {'src': '/images/loading.gif'})})
			.insert({'bottom': 'Loading ...'});
	}
	//get pagination div
	,_getPaginationDiv: function(pagination) {
		var tmp = {};
		if(pagination.pageNumber >= pagination.totalPages)
			return;
		
		tmp.me = this;
		return new Element('div', {'class': 'pagination_wrapper pull-right'}).insert({'bottom': tmp.me._getPaginationBtn('查看更多 / 查看更多<br />Get more', pagination.pageNumber + 1) });
	}
	/**
	 * Getting the next page
	 */
	,changePage: function (btn, pageNo, pageSize) {
		var tmp = {};
		this.pagination.pageNo = pageNo;
		this.pagination.pageSize = pageSize;
		$(btn).update('Getting more ....').writeAttribute('disabled', true);
		this.getResult(false, function() {
			$(btn).up('.pagination_wrapper').remove();
		});
	}
	/**
	 * getting the pagination button
	 */
	,_getPaginationBtn: function (txt, pageNo) {
		var tmp = {};
		tmp.me = this;
		return new Element('button', {'class': 'btn btn-primary', 'type': 'button'})
			.update(txt)
			.observe('click', function() {
				tmp.me.changePage(this, pageNo, tmp.me.pagination.pageSize);
			})
		;
	}
	/**
	 * Getting the list of the products
	 */
	,getResult: function(reset, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.reset = (reset || false);
		tmp.me.postAjax(tmp.me.getCallbackId('getItems'), {'pagination': tmp.me.pagination}, {
			'onLoading': function () {
				if(tmp.reset === true)
				{
					tmp.me.pagination.pageNo = 1;
					$(tmp.me.htmlIDs.listingDiv).update(tmp.me.getLoadingImg());
				}
			}
			,'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.items)
						return;
					
					if(tmp.reset === true) {
						$(tmp.me.htmlIDs.listingDiv).update(new Element('table', {'class': 'table table-striped table-hover'})
							.insert({'bottom': new Element('thead').update(tmp.me._getResultTableRow({'title': 'Name', 'isbn': 'ISBN', 'qty': 'Qty', 'author': 'Author', 'publishDate': 'Publish Date'}, true) ) })
							.insert({'bottom': tmp.tbody = new Element('tbody') })
						);
						$(tmp.me.htmlIDs.totalCountDiv).update(tmp.result.pagination.totalRows);
					}
					tmp.result.items.each(function(item) {
						tmp.item = {
								'id': item.id,
								'title': item.title, 
								'isbn': item.attributes.isbn ? item.attributes.isbn[0].attribute : '', 
								'img': tmp.me._getProductImgDiv(item.attributes.image_thumb || null, {'style': 'height: 50px; width:auto;'}),
								'author': item.attributes.author ? item.attributes.author[0].attribute : '',
								'publishDate': item.attributes.publish_date ? item.attributes.publish_date[0].attribute : '',
								'qty': item.orderedQty
						};
						$(tmp.me.htmlIDs.listingDiv).down('tbody').insert({'bottom': tmp.me._getResultTableRow(tmp.item, false) });
					});
					$(tmp.me.htmlIDs.listingDiv).insert({'bottom': tmp.me._getPaginationDiv(tmp.result.pagination) });
				} catch (e) {
					$(tmp.me.htmlIDs.listingDiv).update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger') );
				}
				
				if(typeof(afterFunc) === 'function')
					afterFunc();
			}
		})
		return tmp.me;
	}
	/**
	 * display the order summary
	 */
	,_displayOrderSummary: function(order) {
		var tmp = {}
		tmp.me = this;
		tmp.totalCount = 0;
		order.items.each(function(item){
			tmp.totalCount = (tmp.totalCount * 1) + (item.qty * 1)
		});
		$(tmp.me.htmlIDs.orderSummaryDiv).update(tmp.totalCount);
		return tmp.me;
	}
	/**
	 * Getting the order object
	 */
	,getOrderSummary: function() {
		var tmp = {}
		tmp.me = this;
		tmp.me.postAjax(tmp.me.getCallbackId('getOrderSummary'), {}, {
			'onLoading': function () {}
			,'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.order)
						return;
					
					tmp.me.order = tmp.result.order;
					tmp.me._displayOrderSummary(tmp.me.order);
				} catch (e) {
					tmp.me.showModalBox('ERROR', e, true);
				}
			}
		})
		return tmp.me;
	}
});