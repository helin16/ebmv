/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	product: null //the product object
	,resultDivId: '' //where we are displaying product details
	,ownTypeIds: {} //the libraryowntypes
		
	,_joinAtts: function(attributes, name) {
		var tmp = {};
		tmp.attrs = [];
		if(attributes)
		{
			attributes.each(function(item) {
				tmp.attrs.push(item[name]);
			});
		}
		return tmp.attrs;
	}
		
	,_getAtts: function(attrcode, title, className, overRideContent) {
		var tmp = {};
		tmp.me = this;
		if(!tmp.me.product.attributes[attrcode] && !overRideContent)
			return [];
		
		tmp.overRideContent = (overRideContent || '');
		return new Element('span', {'class': className}).addClassName(className)
			.insert({'bottom': new Element('label').update(title) })
			.insert({'bottom': new Element('span').update((!tmp.overRideContent ? tmp.me._joinAtts(tmp.me.product.attributes[attrcode], 'attribute').join(', ') : tmp.overRideContent)) });
	}
	
	,_getLoadingImg: function (id) {
		return new Element('span', {'class': 'loadingImg', 'id': id});
	}
	
	,displayProduct: function() {
		var tmp = {};
		tmp.me = this;
		
		//getting the thumbnail image
		tmp.thumbImg = new Element('div', {'class': 'product_image'});
		if(!tmp.me.product.attributes['image_thumb'] || tmp.me.product.attributes['image_thumb'].size() === 0)
			tmp.thumbImg.addClassName('noimage');
		else
			tmp.thumbImg.insert({'bottom': new Element('img', {'class': 'product_image', 'src': '/asset/get?id=' + tmp.me.product.attributes['image_thumb'][0]['attribute']})});
		
		tmp.newDiv = new Element('div', {'class': 'wrapper'})
			.insert({'bottom': new Element('div', {'class': 'product listitem'})
				.insert({'bottom': new Element('span', {'class': 'inlineblock listcol left'}).update(tmp.thumbImg) })
				.insert({'bottom': new Element('span', {'class': 'inlineblock listcol right'})
					.insert({'bottom': new Element('div', {'id': 'socialBtns', 'class': 'socialBtns'}) })
					.insert({'bottom': new Element('div', {'class': 'product_title'}).update(tmp.me.product.title) })
					.insert({'bottom': new Element('div', {'class': 'row'})
						.insert({'bottom': tmp.me._getAtts('author', 'Author', 'inlineblock author') })
						.insert({'bottom': tmp.me._getAtts('isbn', 'ISBN', 'inlineblock product_isbn') })
					})
					.insert({'bottom': new Element('div', {'class': 'row'})
						.insert({'bottom': tmp.me._getAtts('publisher', 'Publisher', 'inlineblock product_publisher') })
						.insert({'bottom': tmp.me._getAtts('publish_date', 'Publisher Date', 'inlineblock product_publish_date') })
					})
					.insert({'bottom': new Element('div', {'class': 'row'})
						.insert({'bottom': tmp.me._getAtts('no_of_words', 'Length', 'inlineblock product_no_of_words') })
						.insert({'bottom': tmp.me._getAtts('languages', 'Languages', 'inlineblock product_languages', tmp.me._joinAtts(tmp.me.product.languages, 'name').join(', ')) })
					})
					.insert({'bottom': new Element('div', {'class': 'row'}).update(tmp.me._getLoadingImg('copies_display')) })
					.insert({'bottom': new Element('div', {'class': 'row product_description'}).update(tmp.me._getAtts('description', '', ''))	})
					.insert({'bottom': new Element('div', {'class': 'row btns'})
						.insert({'bottom':  tmp.me._getLoadingImg('view_btn') })
						.insert({'bottom': tmp.me._getLoadingImg('downloadBtn') })
					})
				})
			});
		$(tmp.me.resultDivId).update(tmp.newDiv);
		socialBtnJs.load('socialBtns', document.URL, 'Check this out:' + tmp.me.product.title, tmp.newDiv.down('.product_description').innerHTML);
		tmp.me._getCopies('copies_display', 'view_btn', 'downloadBtn');
		return this;
	}
	
	,_getCopies: function (readCopiesDisplayHolderId, readOnlineBtnId, downloadBtnId) {
		var tmp = {};
		tmp.me = this;
		
		tmp.me.postAjax(tmp.me.getCallbackId('getCopies'), {}, {
			'onLoading': function () {}
			,'onComplete': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					tmp.readCopies = tmp.downloadCopies = 'N/A';
					tmp.readBtn = new Element('span', {'class': 'button rdcrnr disabled'}).update('在线阅读/在線閱讀<br />Read Online');
					tmp.downloadBtn = new Element('span', {'class': 'button rdcrnr disabled'}).update('下载阅读/下載閱讀<br />Download This Book');
					
					//getting the readonline url
					if(tmp.result.urls.viewUrl && tmp.result.copies[tmp.me.ownTypeIds.OnlineRead].avail * 1 > 0) {
						tmp.readCopies = tmp.result.copies[tmp.me.ownTypeIds.OnlineRead].avail + ' out of ' + tmp.result.copies[tmp.me.ownTypeIds.OnlineRead].total;
						tmp.readBtn.removeClassName('disabled')
							.observe('click', function(){
								tmp.me._getLink(this, 'read');
							});
					}
					
					//getting the download url
					if(tmp.result.urls.downloadUrl && tmp.result.copies[tmp.me.ownTypeIds.Download].avail * 1 > 0) {
						tmp.downloadCopies = tmp.result.copies[tmp.me.ownTypeIds.Download].avail + ' out of ' + tmp.result.copies[tmp.me.ownTypeIds.Download].total;
						tmp.downloadBtn.removeClassName('disabled')
							.observe('click', function(){
								tmp.me._getLink(this, 'download');
							});
					} 
					$(readCopiesDisplayHolderId).up('.row').update('')
						.insert({'bottom': tmp.me._getAtts('', 'Online Read Copies', 'inlineblock online_read_copies', tmp.readCopies) })
						.insert({'bottom': tmp.me._getAtts('', 'Download Copies', 'inlineblock download_copies', tmp.downloadCopies) });
					$(readOnlineBtnId).replace(tmp.readBtn);
					$(downloadBtnId).replace(tmp.downloadBtn);
				} catch(e) {
					alert(e);
				}
			}
		}, 120000);
	}
	
	,_getLink: function(btn, type) {
		var tmp = {};
		tmp.me = this;
		
		$(btn).writeAttribute('originvalue', $(btn).innerHTML);
		tmp.me.getUser(btn, function(){
				tmp.me.postAjax(tmp.me.getCallbackId('geturl'), {'type': type}, {
					'onLoading': function () {}
					,'onComplete': function(sender, param) {
						try {
							tmp.result = tmp.me.getResp(param, false, true);
							if(tmp.result.url)
								window.open(tmp.result.url);
							if(tmp.result.redirecturl)
								window.location = tmp.result.redirecturl;
						} catch(e) {
							alert(e);
						}
						$(btn).update($(btn).readAttribute('originvalue')).writeAttribute('disabled', false);
					}
				}, 120000);
			}, function () {
				$(btn).update( "Processing ...").writeAttribute('disabled', true);
			}
			, function () {
				$(btn).update($(btn).readAttribute('originvalue')).writeAttribute('disabled', false);
			}
		);
	}
});