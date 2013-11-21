var CrudPageJs=new Class.create();CrudPageJs.prototype=Object.extend(new AdminPageJs(),{
	pagination: {pageNo: 1, pageSize: 30} //this is the pagination for the crud page
	,resultDivId: null //this is the result div id
	
	//show all the items
	,showItems:  function(pageNo, pageSize, itemId, resetResult) {
		var tmp = {};
		tmp.me = this;
		tmp.me.pagination.pageNo = (pageNo || tmp.me.pagination.pageNo);
		tmp.me.pagination.pageSize = (pageSize || tmp.me.pagination.pageSize);
		tmp.itemId = (itemId || null);
		tmp.resetResult = (resetResult === false ? false : true);
		tmp.me.postAjax(tmp.me.getCallbackId('getItems'), {'pagination': tmp.me.pagination, 'itemId': tmp.itemId}, {
			'onLoading': function (sender, param) {},
			'onComplete': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result.items || tmp.result.items === undefined || tmp.result.items === null)
						throw 'No item found/generated'; 
					if(tmp.resetResult === true) {
						$(tmp.me.resultDivId).update('');
					}
					tmp.index = (tmp.me.pagination.pageNo - 1) * tmp.me.pagination.pageSize;
					$(tmp.me.resultDivId).insert({'bottom': tmp.me._getResultDiv(tmp.result.items, tmp.resetResult, tmp.index) })
						.insert({'bottom': tmp.me._getPaginBtns(tmp.result.pagination)});
				} catch(e) {
					$(tmp.me.resultDivId).update(e);
				}
			}
		});
	}
	
	//getting the pagination buttons
	,_getPaginBtns: function(pagination) {
		if(pagination.pageNumber >= pagination.totalPages)
			return;
		var tmp = {};
		tmp.me = this;
		tmp.paginDiv = new Element('div', {'class': 'paginDiv'})
			.insert({'bottom': new Element('span', {'class': 'btn'}).update('Get more')
				.observe('click', function() {
					$(this).up('.paginDiv').remove();
					tmp.me.showItems(tmp.me.pagination.pageNo + 1, tmp.me.pagination.pageSize, null, false);
				})
			});
		return tmp.paginDiv;
	}
	
	//getting the result div
	,_getResultDiv: function(items, notitlerow, itemrowindex) {
		return null;
	}
	
	//editing an item
	,editItem: function (btn) {
		this._hideShowAllEditPens(btn, false); 
		this.showEditPanel(btn);
		 return;
	}
	
	,_hideShowAllEditPens: function(btn, show) {
		var tmp = {};
		tmp.me = this;
		tmp.btnsDiv = $(btn).up('.row').getElementsBySelector('.btns').first();
		if (show === true) {
			tmp.btnsDiv.show();
		} else {
			tmp.btnsDiv.hide();
		}
		return this;
	}
	
	//deleting an item
	,delItem: function (btn) {
		alert('deleting: ');
		return;
	}
	
	//create an item
	,createItem: function (btn) {
		alert('creating: ');
		return;
	}
	
	// create function for deafult behaviour of edit panel
	,showEditPanel: function (btn) {
		throw 'function showEditPanel needs to be overrided!';
	}
	
	,cancelEdit: function(btn) {
		throw 'function cancelEdit needs to be overrided!';
	}
	
	,saveEditedItem: function(btn) {
		throw 'function saveEditedItem needs to be overrided!';
	}
	
});