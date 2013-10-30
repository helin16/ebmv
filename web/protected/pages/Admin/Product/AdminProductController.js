/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new CrudPageJs(), {
	
	showProducts:  function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.me.postAjax(tmp.me.getCallbackId('showProduct'), 
						{'pageNumber' : tmp.me.getCallbackId('pageNumber'), 
						 'pageSize' : tmp.me.getCallbackId('pageSize'),
						 'productId' : tmp.me.getCallbackId('productId'),
						}, 
			{
			'onLoading': function (sender, param) {
				
			},
			'onComplete': function (sender, param) {
				// TODO -- need to fix the column width in the css file //
				try 
				{
					tmp.result = tmp.me.getResp(param, false, true);
					if(tmp.result.products && tmp.result.products !== undefined && tmp.result.products !== null) 
					{
						$('allProductDiv').insert({'bottom':  tmp.me._getItemRow('id', 'suk', 'title', 'active', 'option').addClassName('titleRow') });
						tmp.i = 0;
						tmp.result.products.each(function(item) {
							$('allProductDiv').insert({'bottom':  tmp.me._getItemRow(item.id, item.suk, item.title, item.active,  new Element('img', {'editId': item.id, 'class': 'btn', 'src': '/themes/default/images/edit.png', 'alt': 'EDIT'})
								.observe('click', function() {tmp.me.editProduct(this); }) ).addClassName(tmp.i % 2 === 0 ? 'even' : 'odd')
							});
							tmp.i++;
						});
					}
					else
						throw 'No Product found/generated'; 
					return;
				}
				catch(e) {
					alert(e);
				}
			}
		});
	}

	,_getItemRow: function (id, suk, title, active, option) {
		var tmp = {};
		tmp.me = this;
		tmp.div = new Element('div', {'class' : 'row'})
			.insert({'bottom' : new Element('span', {'class' : 'col id'}).update(id) })
			.insert({'bottom' : new Element('span', {'class' : 'col suk'}).update(suk) })	
			.insert({'bottom' : new Element('span', {'class' : 'col title'}).update(title) })	
			.insert({'bottom' : new Element('span', {'class' : 'col active'}).update(active) })	
			.insert({'bottom' : new Element('span', {'class' : 'col btns'}).update(option) });
		return tmp.div;
	}

	,editProduct : function()
	{
		alert('fsdfd');
	}
});