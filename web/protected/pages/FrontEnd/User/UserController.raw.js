/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	resultDivId: ''
	,pagination: {'pageNo': 1, 'pageSize': 5}
	,borrowStatusId: ''
	
	//show the products
	,showBookShelf: function(clear, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.clear = (clear === true ? true : false);
		if(tmp.clear === true)
		{
			this.pagination.pageNo = 1;
			$(tmp.me.resultDivId).update(tmp.me._getLoadingDiv());
		}
		
		pageJs.postAjax(tmp.me.getCallbackId("getProducts"), {'pagination': tmp.me.pagination}, {
			'onLoading': function () { }
			,'onComplete': function(sender, param) {
				tmp.resultDiv = $(tmp.me.resultDivId);
				if(tmp.clear === true)
					tmp.resultDiv.update('');
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(!tmp.result.pagination || tmp.result.pagination.totalRows === 0 || tmp.result.items.size() === 0)
						throw 'Nothing on your shelf!';
					
					tmp.result.items.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me._getProductListItem(item) });
					});
					
					if(tmp.result.pagination.pageNumber < tmp.result.pagination.totalPages)
						tmp.resultDiv.insert({'bottom':tmp.me._getPaginationDiv(tmp.result.pagination) });
				} catch (e) {
					if(tmp.clear === true)
						tmp.resultDiv.update(e);
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
		return new Element('div', {'class': 'pagination_wrapper fullwith'}).insert({'bottom': tmp.me._getPaginationBtn('æŸ¥çœ‹æ›´å¤š / æŸ¥çœ‹æ›´å¤š<br />Get more', pagination.pageNumber + 1) });
	}
	
	,removeItem: function(btn, itemId) {
		var tmp = {};
		tmp.me = this;
		if(!confirm('You are removing this BOOK from your book shelf?\n\nContinue?'))
			return;
		
		pageJs.postAjax(tmp.me.getCallbackId("removeProduct"), {'itemId': itemId}, {
			'onLoading': function () {
				$(btn).addClassName('disabled loading');
			}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(tmp.result.item && tmp.result.item.id) {
						tmp.itemRow = $(tmp.me.resultDivId).down('.listitem[item_id=' + tmp.result.item.id + ']');
						if(tmp.itemRow)
							tmp.itemRow.remove();
					}
				} catch (e) {
					alert(e);
					$(btn).removeClassName('disabled loading');
				}
			}
		});
		return this;
	}
	
	,changePage: function (btn, pageNo, pageSize) {
		var tmp = {};
		tmp.me = this;
		tmp.me.pagination.pageNo = pageNo;
		tmp.me.pagination.pageSize = pageSize;
		$(btn).update('Getting more ....').writeAttribute('disabled', true);
		tmp.me.showBookShelf(false, function() {
			$(btn).up('.pagination_wrapper').remove();
		});
	}
	
	,_getPaginationBtn: function (txt, pageNo) {
		var tmp = {};
		tmp.me = this;
		return new Element('span', {'class': 'fullwith button rdcrnr pagin_btn'})
			.update(txt)
			.observe('click', function() {
				tmp.me.changePage(this, pageNo, tmp.me.pagination.pageSize);
			})
		;
	}
	
	,borrowItem: function (btn, shelfItemId) {
		var tmp = {};
		tmp.me = this;
		if(!confirm('You are trying to borrow this BOOK./ æ‚¨æ­£è¯•å›¾å€Ÿè¿™æœ¬ä¹¦./ æ‚¨æ­£è©¦åœ–å€Ÿé€™æœ¬æ›¸ã€‚\n\nContinue / ç»§ç»­ / ç¹¼çºŒ?'))
			return;
		
		pageJs.postAjax(tmp.me.getCallbackId("borrowItem"), {'itemId': shelfItemId}, {
			'onLoading': function () {
				$(btn).addClassName('disabled loading');
			}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = pageJs.getResp(param, false, true);
					if(tmp.result.item && tmp.result.item.id) {
						tmp.itemRow = $(tmp.me.resultDivId).down('.listitem[item_id=' + tmp.result.item.id + ']');
						if(tmp.itemRow)
							tmp.itemRow.replace(tmp.me._getProductListItem(tmp.result.item));
					}
					alert('You have successfully borrowed this book./ æ‚¨å·²æˆ�åŠŸå€Ÿé˜…è¿™æœ¬ä¹¦./ æ‚¨å·²æˆ�åŠŸå€Ÿé–±é€™æœ¬æ›¸.');
				} catch (e) {
					alert(e);
					$(btn).removeClassName('disabled loading');
				}
			}
		});
		return this;
	}
	
	,returnItem: function (btn, shelfItemId) {
		var tmp = {};
		tmp.me = this;
		if(!confirm('You are trying to return this BOOK./ ä½ æ­£åœ¨è¯•å›¾è¿”å›žæœ¬ä¹¦./ ä½ æ­£åœ¨è©¦åœ–è¿”å›žæœ¬æ›¸ã€‚\n\nContinue / ç»§ç»­ / ç¹¼çºŒ?'))
			return;
		
		pageJs.postAjax(tmp.me.getCallbackId("returnItem"), {'itemId': shelfItemId}, {
			'onLoading': function () {
				$(btn).addClassName('disabled loading');
			}
		,'onComplete': function(sender, param) {
			try {
				tmp.result = pageJs.getResp(param, false, true);
				if(tmp.result.item && tmp.result.item.id) {
					tmp.itemRow = $(tmp.me.resultDivId).down('.listitem[item_id=' + tmp.result.item.id + ']');
					if(tmp.itemRow)
						tmp.itemRow.replace(tmp.me._getProductListItem(tmp.result.item));
				}
				alert('You have successfully returned this book./ æ‚¨å·²æˆ�åŠŸè¿˜å›žæœ¬ä¹¦./ æ‚¨å·²æˆ�åŠŸé‚„å›žæœ¬æ›¸.');
			} catch (e) {
				alert(e);
				$(btn).removeClassName('disabled loading');
			}
		}
		});
		return this;
	}
	
	,_getBtnDiv: function(shelfItem) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'row btns'})
			//add remove btn
			.insert({'bottom': new Element('span', {'class': 'imgBtn delBtn', 'title': 'åˆ é™¤ / åˆªé™¤ Remove From My Shelf'})
				.observe('click', function(){
					tmp.me.removeItem(this, shelfItem.id);
				})
			});
//		if(shelfItem.status === tmp.me.borrowStatusId) {
//			//add return book btn
//			tmp.newDiv.insert({'bottom': new Element('span', {'class': 'imgBtn returnBookBtn', 'title': 'è¿˜ä¹¦ / é‚„æ›¸ Return This Book'})
//				.observe('click', function(){
//					tmp.me.returnItem(this, shelfItem.id);
//				})
//			});
//		} else {
//			//add return book btn
//			tmp.newDiv.insert({'bottom': new Element('span', {'class': 'imgBtn borrowBookBtn', 'title': 'å€Ÿä¹¦ / å€Ÿæ›¸ Borrow This Book'})
//				.observe('click', function(){
//					tmp.me.borrowItem(this, shelfItem.id);
//				})
//			});
//		}
		return tmp.newDiv;
	}
	
	//get product list item
	,_getProductListItem: function(shelfItem) {
		var tmp = {};
		tmp.me = this;
		if(!shelfItem.product || !shelfItem.product.id)
			return null;
		tmp.productDiv = new Element('div', {'class': 'product listitem', 'item_id': shelfItem.id}).store('data', shelfItem)
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol left'})
				.insert({'bottom': tmp.me._getProductImgDiv(shelfItem.product.attributes.image_thumb || null)
						.addClassName('cursorpntr')
						.observe('click', function(){ tmp.me.showDetailsPage(shelfItem.product.id); })
				})
			})
			.insert({'bottom': new Element('span', {'class': 'inlineblock listcol right'})
				.insert({'bottom': new Element('div', {'class': 'product_title'}).update(shelfItem.product.title)
					.addClassName('cursorpntr')
					.observe('click', function(){ tmp.me.showDetailsPage(shelfItem.product.id); })
				})
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': new Element('span', {'class': 'author inlineblock'})
						.insert({'bottom': new Element('label').update('Author:')})
						.insert({'bottom': new Element('span').update(shelfItem.product.attributes.author ? tmp.me._getAttrString(shelfItem.product.attributes.author).join(' ') : '')})
					})
					.insert({'bottom': new Element('span', {'class': 'product_isbn inlineblock textright'})
						.insert({'bottom': new Element('label').update('ISBN:')})
						.insert({'bottom': new Element('span').update(shelfItem.product.attributes.isbn ? tmp.me._getAttrString(shelfItem.product.attributes.isbn).join(' ') : '')})
					})
				})
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': new Element('span', {'class': 'product_publisher inlineblock'})
						.insert({'bottom': new Element('label').update('Publisher:')})
						.insert({'bottom': new Element('span').update(shelfItem.product.attributes.publisher ? tmp.me._getAttrString(shelfItem.product.attributes.publisher).join(' ') : '')})
					})
					.insert({'bottom': new Element('span', {'class': 'product_publish_date inlineblock textright'})
						.insert({'bottom': new Element('label').update('Publisher Date:')})
						.insert({'bottom': new Element('span').update(shelfItem.product.attributes.publish_date ? tmp.me._getAttrString(shelfItem.product.attributes.publish_date).join(' ') : '')})
					})
				})
				.insert({'bottom': tmp.me._getBtnDiv(shelfItem) })
				.insert({'bottom': new Element('div', {'class': 'product_description'}).update((shelfItem.product.attributes.description ? tmp.me._getAttrString(shelfItem.product.attributes.description).join(' ') : '')) })
			})
		;
		return tmp.productDiv;
	}
	
	,_getAttrString: function(attArray){
		return attArray.map(function(attr) { return attr.attribute || '';});
	}
});