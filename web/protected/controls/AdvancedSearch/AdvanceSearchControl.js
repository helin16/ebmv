/**
 * The page Js file
 */
var AdvanceSearchJs = new Class.create();
AdvanceSearchJs.prototype = {
	
	searchURL: '/products/search/',
	attributeTypeArray : {},
	categoryArray : {},
	
	//constructor
	initialize: function(searchURL) {
		this.searchURL = (searchURL || this.searchURL);
	}	
	
	,_getAdvanceSearchOptions: function() {
		var tmp = {};
		tmp.me  = this;
		tmp.selectbox = new Element('select');
		tmp.selectbox.insert({'bottom': new Element('option', {value: '0'}).update('All Attributes') });
		$H(tmp.me.attributeTypeArray).each(function(item) {
			tmp.selectbox.insert({'bottom': new Element('option', {value: item.key}).update(item.value) });
		});
		return tmp.selectbox; 
	}
	
	,_getCategoryOptions: function() {
		var tmp = {};
		tmp.me  = this;
		tmp.selectbox = new Element('select');
		tmp.selectbox.insert({'bottom': new Element('option', {value: '0'}).update('All Categories') });
		$H(tmp.me.categoryArray).each(function(item) {
			tmp.selectbox.insert({'bottom': new Element('option', {value: item.key}).update(item.value) });
		});
		return tmp.selectbox; 
	}
	
	,showHideAdvanceSearchPanel: function(asButton) {
		var tmp = {};
		tmp.me = this;
		
		tmp.me._getAdvanceSearchOptions();
		
		if($$('.advSearcDiv').size() > 0) {
			$$('.advSearcDiv').each(function(item){ 
				item.remove(); 
			});
		} else {	
			$(asButton).up('#advanceSearchDiv').insert({'bottom' : new Element('div', {'class' : 'advSearcDiv'})
				.insert({'bottom' : new Element('span', {'width' : '20%'})
					.insert({'bottom' : tmp.me._getCategoryOptions().addClassName('searchCat padding4 rdcrnr lightBrdr') })
				})
				.insert({'bottom' : new Element('span', {'width' : '20%'})
					.insert({'bottom' : tmp.me._getAdvanceSearchOptions().addClassName('searchOpt padding4 rdcrnr lightBrdr') })
				})
				.insert({'bottom' : new Element('span', {'width' : '50%'})
					.insert({'bottom' : new Element('input', {'type' : 'textbox', 'style' : 'width:400px;', 'class' : 'searchTxt rdcrnr lightBrdr padding5'})})
				})
				.insert({'bottom' : new Element('span', {'width' : '10%'})		
					.insert({'bottom' : new Element('input', {'type' : 'button', 'value' : 'Advanced Search', 'class' : 'button rdcrnr'})
										.observe('click', function(){
											tmp.searchPanel = $(this).up('.advSearcDiv');
											
											if($F(tmp.searchPanel.down('.searchTxt')) == '')
											{
												alert('Nothing to Search!!!');
												return;
											}
											
											tmp.searchData = {'searchText': $F(tmp.searchPanel.down('.searchTxt')), 'searchOpt': $F(tmp.searchPanel.down('.searchOpt')), 'searchCat': $F(tmp.searchPanel.down('.searchCat'))};
											window.location = tmp.me.searchURL + Object.toJSON(tmp.searchData);
										}) 
					})
				})		
			});
		}
	}
};