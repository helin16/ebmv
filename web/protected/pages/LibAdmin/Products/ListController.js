/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	htmlIDs: {'listingDiv': ''}

	,setHTMLIDs: function(listingDiv) {
		this.htmlIDs.listingDiv = listingDiv;
		return this;
	}
	
	,_getResultTableRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td');
		tmp.img = (tmp.isTitle === true ? '' : new Element('a').update(row.img.addClassName('list-thumbnail'))	);
		tmp.row = new Element('tr')
			.insert({'bottom': new Element(tmp.tag).update(tmp.img) })
			.insert({'bottom': new Element(tmp.tag).update(row.title) })
			.insert({'bottom': new Element(tmp.tag).update(row.isbn) })
			.insert({'bottom': new Element(tmp.tag).update(row.qty) })
			.insert({'bottom': new Element(tmp.tag).update('') })
		return tmp.row;
	}
	
	,getResult: function(reset) {
		var tmp = {};
		tmp.me = this;
		tmp.reset = (reset || false);
		tmp.me.postAjax(tmp.me.getCallbackId('getItems'), {}, {
			'onLoading': function () {}
			,'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.items)
						return;
					
					if(tmp.reset === true) {
						$(tmp.me.htmlIDs.listingDiv).update(new Element('table', {'class': 'table table-striped table-hover'})
							.insert({'bottom': new Element('thead').update(tmp.me._getResultTableRow({'title': 'Name', 'isbn': 'ISBN', 'qty': 'Qty'}, true) ) })
							.insert({'bottom': tmp.tbody = new Element('tbody') })
						);
					}
					tmp.result.items.each(function(item) {
						tmp.item = {'title': item.title, 'isbn': item.attributes.isbn[0].attribute, 'img': tmp.me._getProductImgDiv(item.attributes.image_thumb || null)}
						$(tmp.me.htmlIDs.listingDiv).down('tbody').insert({'bottom': tmp.me._getResultTableRow(tmp.item, false) });
					})
				} catch (e) {
					console.error(e);
					$(tmp.me.htmlIDs.listingDiv).update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger') );
				}
			}
		})
		return tmp.me;
	}
});