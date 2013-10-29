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
						$('allProductDiv').insert({'bottom': new Element('div', {'class' : 'titleRow'})
																.insert({'bottom' : new Element('span', {'class' : 'titleColumn'}).update('ID') })
																.insert({'bottom' : new Element('span', {'class' : 'titleColumn'}).update('SUK') })	
																.insert({'bottom' : new Element('span', {'class' : 'titleColumn'}).update('TITLE') })	
																.insert({'bottom' : new Element('span', {'class' : 'titleColumn'}).update('ACTIVE') })	
																.insert({'bottom' : new Element('span', {'class' : 'titleColumn'}).update('Option') })	
						});
						tmp.result.products.each(function(key, value) {
							$('allProductDiv').insert({'bottom': new Element('div', {'class': 'titleRow'})
																	.insert({'bottom': new Element('span', {'class': 'titleColumn'}).update(key.id) })
																	.insert({'bottom': new Element('span', {'class': 'titleColumn'}).update(key.suk) })
																	.insert({'bottom': new Element('span', {'class': 'titleColumn'}).update(key.title) })
																	.insert({'bottom': new Element('span', {'class': 'titleColumn'}).update(key.active) })
																	.insert({'bottom': new Element('span', {'class': 'titleColumn'}).insert({'bottom': new Element('img', {'editId': key.id, 'src': '/themes/default/images/edit.png', 'alt': 'EDIT'})
																																					.observe('click', tmp.me.editProduct)
																																			}) 
																			})
							});
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

	,editProduct : function()
	{
		alert('fsdfd');
	}
});