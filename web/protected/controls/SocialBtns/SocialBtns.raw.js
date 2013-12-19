
var SocialBtnsJs = new Class.create();
SocialBtnsJs.prototype = Object.extend(new FrontPageJs(), {
	_holderId: '' //where the btns will be displayed
		
	,load: function (holderId, url, title, description) {
		var tmp = {};
		tmp.me = this;
		tmp.me._holderId = holderId;
		$(tmp.me._holderId).update(tmp.me._getBtnsDiv(url, title, description));
	}
	
	,_getLink: function(url, title, description) {
		var tmp = {};
		tmp.url = (url || '');
		tmp.title = (title || '');
		tmp.description = (description || '');
		
		tmp.a = new Element('a');
		if(!tmp.url.blank())
			tmp.a.writeAttribute('addthis:url', tmp.url);
		if(!tmp.title.blank())
			tmp.a.writeAttribute('addthis:title', tmp.title);
		if(!tmp.description.blank())
			tmp.a.writeAttribute('addthis:description', tmp.description);
		return tmp.a;
	}

	,_getBtnsDiv: function (url, title, description) {
		var tmp = {};
		tmp.me = this;
		return new Element('span', {'class': 'socialBtns_wrapper'})
			.insert({'bottom': new Element('span', {'class': 'addthis_toolbox addthis_default_style addthis_32x32_style'})
				.insert({'bottom': tmp.me._getLink(url, title, description).addClassName('addthis_button_facebook')  })
				.insert({'bottom': tmp.me._getLink(url, title, description).addClassName('addthis_button_twitter')  })
				.insert({'bottom': tmp.me._getLink(url, title, description).addClassName('addthis_button_google_plusone_share')  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_email'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_print'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_button_compact'})  })
				.insert({'bottom': new Element('a', {'class': 'addthis_counter addthis_bubble_style'})  })
			});
	}
});