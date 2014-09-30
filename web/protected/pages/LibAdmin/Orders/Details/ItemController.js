/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	htmlIDs: {'orderDetailsDiv': ''}
	,_order: {} //the order object
	/**
	 * setting the HTML IDs
	 */
	,setHTMLIds: function(orderDetailsDiv) {
		this.htmlIDs.orderDetailsDiv = orderDetailsDiv;
		return this;
	}
	/**
	 * setting the order object
	 */
	,setOrder: function(order) {
		this._order = order;
		return this;
	}
	,_deleteItem: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.row = btn.up('.item-row');
		tmp.item = tmp.row.retrieve('data');
		tmp.me.postAjax(tmp.me.getCallbackId('delItem'), tmp.item, {
			'onLoading': function() {}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.item)
						return
					tmp.row.remove();
				} catch (e) {
					tmp.me.showModalbox('ERROR', e, true);
				}
			}
		})
		return tmp.me;
	}
	,_getItemsRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle === true ? true : false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td');
		tmp.newDiv = new Element('tr', {'class': 'item-row'}).store('data', row)
			.insert({'bottom': new Element(tmp.tag).update(tmp.isTitle === true ? 'Title' : row.product.title) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2'}).update(tmp.isTitle === true ? 'ISBN' : (!row.product.attributes.isbn ? '' : row.product.attributes.isbn[0].attribute)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2'}).update(tmp.isTitle === true ? 'Author' : (!row.product.attributes.author ? '' : row.product.attributes.author[0].attribute)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2'}).update(tmp.isTitle === true ? 'Publisher' : (!row.product.attributes.publisher ? '' : row.product.attributes.publisher[0].attribute)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-2'}).update(tmp.isTitle === true ? 'PublishDate' : (!row.product.attributes.publish_date ? '' : row.product.attributes.publish_date[0].attribute)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-sm-1'}).update(tmp.isTitle === true ? 'Qty' : new Element('div', {'class': 'input-group input-group-sm'})
				.insert({'bottom': new Element('input', {'class': 'form-control order-qty', 'type': 'text', 'value': row.qty, 'style': 'padding: 4px;'}) })
				.insert({'bottom': new Element('span', {'class': 'input-group-btn'}) 
					.insert({'bottom': new Element('span', {'class': 'btn btn-danger'})
						.update(new Element('span', {'class': 'glyphicon glyphicon-trash'})) 
						.observe('click', function(){
							if(!confirm('Do you want to delete this item from this order?'))
								return false;
							tmp.me._deleteItem(this);
						})
					}) 
				})
			) })
		;
		return tmp.newDiv;
	}
	/**
	 * displaying the order
	 */
	,displayOrder: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-sm-3'})
					.insert({'bottom': new Element('dl')
						.insert({'bottom': new Element('dt').update('Order No.:') })
						.insert({'bottom': new Element('dd').update(tmp.me._order.orderNo) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-3'})
					.insert({'bottom': new Element('dl')
						.insert({'bottom': new Element('dt').update('Order status.:') })
						.insert({'bottom': new Element('dd').update(tmp.me._order.status) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-3'})
					.insert({'bottom': new Element('dl')
						.insert({'bottom': new Element('dt').update('Order Date.:') })
						.insert({'bottom': new Element('dd').update(tmp.me._order.created) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-3'})
					.insert({'bottom': new Element('dl')
						.insert({'bottom': new Element('dt').update('Order By.:') })
						.insert({'bottom': new Element('dd').update(tmp.me._order.createdBy.person.fullname) })
					})
				})
			})
			.insert({'bottom': new Element('table', {'class': 'table table-striped table-hover'})
				.insert({'bottom': new Element('thead').update(tmp.me._getItemsRow({}, true) ) })
				.insert({'bottom': tmp.tbody = new Element('tbody') })
			});
		tmp.me._order.items.each(function(item){
			tmp.tbody.insert({'bottom': tmp.me._getItemsRow(item, false)  });
		})
		$(tmp.me.htmlIDs.orderDetailsDiv).update(tmp.newDiv);
		return tmp.me;
	}
});