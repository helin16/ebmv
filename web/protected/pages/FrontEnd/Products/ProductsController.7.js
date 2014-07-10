var PageJs=new Class.create();PageJs.prototype=Object.extend(new FrontPageJs(),{resultDivId:"",getProductsBtn:"",pagination:{pageNo:1,pageSize:10},searchCriteria:{searchString:"",categoryIds:[],searchOpt:"",searchCat:"",language:"",productType:""},getProductItemFunc:"_getProductGridItem",searchProductUrl:"/products/search/{searchTxt}",initialize:function(b,a){this.resultDivId=b;this.getProductsBtn=a},showProducts:function(a,c){var b={};b.me=this;b.clear=(a===true?true:false);if(b.clear===true){this.pagination.pageNo=1;$(b.me.resultDivId).update(b.me._getLoadingDiv())}pageJs.postAjax(this.getProductsBtn,{pagination:b.me.pagination,searchCriteria:b.me.searchCriteria},{onLoading:function(){},onComplete:function(d,g){if(b.clear===true){b.list=(b.me.getProductItemFunc==="_getProductGridItem"?new Element("div",{"class":"row"}):new Element("ul",{"class":"media-list"}));b.list.addClassName("plist");$(b.me.resultDivId).update(b.list)}try{b.result=pageJs.getResp(g,false,true);if(!b.result.pagination||b.result.pagination.totalRows===0){throw"Nothing found!"}b.list=$(b.me.resultDivId).down(".plist");b.result.products.each(function(e){b.list.insert({bottom:b.me[b.me.getProductItemFunc](e)})});$(b.me.resultDivId).insert({bottom:b.me._getPaginationDiv(b.result.pagination)})}catch(f){if(b.clear===true){$(b.me.resultDivId).update(f)}else{alert(f)}}if(typeof(c)==="function"){c()}}});return this},_getLoadingDiv:function(){return new Element("span",{"class":"loading"}).insert({bottom:new Element("img",{src:"/images/loading.gif"})}).insert({bottom:"Loading ..."})},_getPaginationDiv:function(a){var b={};if(a.pageNumber>=a.totalPages){return}b.me=this;return new Element("div",{"class":"pagination_wrapper pull-right"}).insert({bottom:b.me._getPaginationBtn("查看更多 / 查看更多<br />Get more",a.pageNumber+1)})},changePage:function(d,b,a){var c={};this.pagination.pageNo=b;this.pagination.pageSize=a;$(d).update("Getting more ....").writeAttribute("disabled",true);this.showProducts(false,function(){$(d).up(".pagination_wrapper").remove()})},_getPaginationBtn:function(a,b){var c={};c.me=this;return new Element("button",{"class":"btn btn-primary",type:"button"}).update(a).observe("click",function(){c.me.changePage(this,b,c.me.pagination.pageSize)})},_getProductListItem:function(b){var a={};a.me=this;a.productDiv=new Element("div",{"class":"row nodefault plistitem"}).insert({bottom:new Element("div",{"class":"col-xs-3"}).insert({bottom:new Element("a",{href:a.me.getProductDetailsUrl(b.id)}).insert({bottom:a.me._getProductImgDiv(b.attributes.image_thumb||null).addClassName("img-thumbnail")})})}).insert({bottom:new Element("div",{"class":"col-xs-9"}).insert({bottom:new Element("a",{"class":"product_title",href:a.me.getProductDetailsUrl(b.id)}).insert({bottom:new Element("h4").update(b.title)})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5"}).insert({bottom:new Element("strong").insert({bottom:"Author:"})})}).insert({bottom:new Element("div",{"class":"col-sm-7"}).update(b.attributes.author?a.me._getAttrString(b.attributes.author).join(" "):"")})})}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5"}).insert({bottom:new Element("strong").insert({bottom:"ISBN:"})})}).insert({bottom:new Element("div",{"class":"col-sm-7"}).update(b.attributes.isbn?a.me._getAttrString(b.attributes.isbn).join(" "):"")})})})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5"}).insert({bottom:new Element("strong").insert({bottom:"Publisher:"})})}).insert({bottom:new Element("div",{"class":"col-sm-7"}).update(b.attributes.publisher?a.me._getAttrString(b.attributes.publisher).join(" "):"")})})}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5"}).insert({bottom:new Element("strong").insert({bottom:"Pub. Date:"})})}).insert({bottom:new Element("div",{"class":"col-sm-7"}).update(b.attributes.publish_date?a.me._getAttrString(b.attributes.publish_date).join(" "):"")})})})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("small").insert({bottom:b.attributes.description?a.me._getAttrString(b.attributes.description).join(" "):""})})})});return a.productDiv},_getProductGridItem:function(a){return new Element("div",{"class":"col-md-3 col-sm-4 col-xs-6"}).update(this._getProductThumbnail(a))},_getAttrString:function(a){return a.map(function(b){return b.attribute||""})},searchProducts:function(b){var a={};a.me=this;if($F(b).blank()){alert("没什么可搜索\n沒什麼可搜索\nNothing to Search.")}else{window.location=a.me.searchProductUrl.replace("{searchTxt}",$F(b))}return a.me}});