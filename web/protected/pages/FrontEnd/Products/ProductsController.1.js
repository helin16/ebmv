var PageJs=new Class.create();PageJs.prototype=Object.extend(new FrontPageJs(),{resultDivId:"",getProductsBtn:"",pagination:{pageNo:1,pageSize:10},searchCriteria:{searchString:"",categoryIds:[],searchOpt:"",searchCat:"",language:"",productType:""},getProductItemFunc:"_getProductGridItem",initialize:function(b,a){this.resultDivId=b;this.getProductsBtn=a},showProducts:function(a,c){var b={};b.me=this;b.clear=(a===true?true:false);if(b.clear===true){$(b.me.resultDivId).update(b.me._getLoadingDiv())}pageJs.postAjax(this.getProductsBtn,{pagination:b.me.pagination,searchCriteria:b.me.searchCriteria},{onLoading:function(){},onComplete:function(d,g){if(b.clear===true){$(b.me.resultDivId).update("")}try{b.result=pageJs.getResp(g,false,true);if(!b.result.pagination||b.result.pagination.totalRows===0){throw"Nothing found!"}b.result.products.each(function(e){$(b.me.resultDivId).insert({bottom:b.me[b.me.getProductItemFunc](e)})});$(b.me.resultDivId).insert({bottom:b.me._getPaginationDiv(b.result.pagination)})}catch(f){alert(f)}if(typeof(c)==="function"){c()}}});return this},_getLoadingDiv:function(){return new Element("span",{"class":"loading"}).insert({bottom:new Element("img",{src:"/themes/default/images/loading.gif"})}).insert({bottom:"Loading ..."})},_getPaginationDiv:function(a){var b={};if(a.pageNumber>=a.totalPages){return}b.me=this;return new Element("div",{"class":"pagination_wrapper fullwith"}).insert({bottom:b.me._getPaginationBtn("Get more",a.pageNumber+1)})},changePage:function(d,b,a){var c={};this.pagination.pageNo=b;this.pagination.pageSize=a;$(d).update("Getting more ....").writeAttribute("disabled",true);this.showProducts(false,function(){$(d).up(".pagination_wrapper").remove()})},_getPaginationBtn:function(a,b){var c={};c.me=this;return new Element("span",{"class":"fullwith button rdcrnr"}).update(a).observe("click",function(){c.me.changePage(this,b,c.me.pagination.pageSize)})},_getProductListItem:function(b){var a={};a.me=this;a.productDiv=new Element("div",{"class":"product listitem"}).insert({bottom:new Element("span",{"class":"inlineblock listcol left"}).insert({bottom:a.me._getProductImgDiv(b.attributes.image_thumb||null).addClassName("cursorpntr").observe("click",function(){a.me.showDetailsPage(b.id)})})}).insert({bottom:new Element("span",{"class":"inlineblock listcol right"}).insert({bottom:new Element("div",{"class":"product_title"}).update(b.title).addClassName("cursorpntr").observe("click",function(){a.me.showDetailsPage(b.id)})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("span",{"class":"author inlineblock"}).insert({bottom:new Element("label").update("Author:")}).insert({bottom:new Element("span").update(b.attributes.author?a.me._getAttrString(b.attributes.author).join(" "):"")})}).insert({bottom:new Element("span",{"class":"product_isbn inlineblock textright"}).insert({bottom:new Element("label").update("ISBN:")}).insert({bottom:new Element("span").update(b.attributes.isbn?a.me._getAttrString(b.attributes.isbn).join(" "):"")})})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("span",{"class":"product_publisher inlineblock"}).insert({bottom:new Element("label").update("Publisher:")}).insert({bottom:new Element("span").update(b.attributes.publisher?a.me._getAttrString(b.attributes.publisher).join(" "):"")})}).insert({bottom:new Element("span",{"class":"product_publish_date inlineblock textright"}).insert({bottom:new Element("label").update("Publisher Date:")}).insert({bottom:new Element("span").update(b.attributes.publish_date?a.me._getAttrString(b.attributes.publish_date).join(" "):"")})})}).insert({bottom:new Element("div",{"class":"product_description"}).update((b.attributes.description?a.me._getAttrString(b.attributes.description).join(" "):""))})});return a.productDiv},_getProductGridItem:function(a){return this._getProductThumbnail(a)},_getAttrString:function(a){return a.map(function(b){return b.attribute||""})}});