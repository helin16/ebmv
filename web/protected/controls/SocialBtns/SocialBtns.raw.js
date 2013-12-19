
var SocialBtnsJs = new Class.create();
SocialBtnsJs.prototype = Object.extend(new FrontPageJs(), {
	_holderId: '' //where the btns will be displayed
		
	,load: function (holderId) {
		var tmp = {};
		tmp.me = this;
		tmp.me._holderId = holderId;
		$(tmp.me._holderId).update(tmp.me._getBtnsDiv());
	}
	,_getBtnsDiv: function () {
		return new Element('span', {'class': 'socialBtns_wrapper'})
			.insert({'bottom': new Element('span', {'class': 'addthis_toolbox addthis_default_style addthis_32x32_style'})
				.insert({'bottom': new Element('a', {'class': 'addthis_button_preferred_1'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_preferred_2'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_preferred_3'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_preferred_4'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_compact'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_counter addthis_bubble_style'})  })
			});
	}

});