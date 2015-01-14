/**
 * The header Js file
 */
var HeaderJs = new Class.create();
HeaderJs.prototype = {
	//constructor
	initialize: function () {}

	,load: function(courseFlag) {
		var tmp = {};
		tmp.me = this;
		// remove course from manu if such Library doesnt have this ProductType
		if(!courseFlag) {
			$$('.learn-chinese-menu').each(function(item){
				if(tmp.menuEl = item.up('li.dropdown'))
					tmp.menuEl.remove();
			});
		}
		return tmp.me;
	}
}
