var PageJs=new Class.create();PageJs.prototype=Object.extend(new FrontPageJs(),{htmlIDs:{totalCountDiv:"",listingDiv:""},pagination:{pageNo:1,pageSize:30},order:{},setHTMLIDs:function(b,a){this.htmlIDs.totalCountDiv=b;this.htmlIDs.listingDiv=a;return this},_openDetailsPage:function(b){var a={};a.me=this;jQuery.fancybox({width:"95%",height:"95%",autoScale:false,autoDimensions:false,fitToView:false,autoSize:false,type:"iframe",href:"/libadmin/order/"+b.id+".html",beforeClose:function(){if($(a.me.htmlIDs.listingDiv).down(".item-row[item-id="+b.id+"]")){$(a.me.htmlIDs.listingDiv).down(".item-row[item-id="+b.id+"]").replace(a.me._getResultTableRow($$("iframe.fancybox-iframe").first().contentWindow.pageJs._order,false))}}});return a.me},_getPaginationDiv:function(a){var b={};if(a.pageNumber>=a.totalPages){return}b.me=this;return new Element("div",{"class":"pagination_wrapper pull-right"}).insert({bottom:b.me._getPaginationBtn("查看更多 / 查看更多<br />Get more",a.pageNumber+1)})},changePage:function(d,b,a){var c={};this.pagination.pageNo=b;this.pagination.pageSize=a;$(d).update("Getting more ....").writeAttribute("disabled",true);this.getResult(false,function(){$(d).up(".pagination_wrapper").remove()})},_getPaginationBtn:function(a,b){var c={};c.me=this;return new Element("button",{"class":"btn btn-primary",type:"button"}).update(a).observe("click",function(){c.me.changePage(this,b,c.me.pagination.pageSize)})},_getResultTableRow:function(c,a){var b={};b.me=this;b.isTitle=(a||false);b.tag=(b.isTitle===true?"th":"td");b.row=new Element("tr",{"class":"item-row","item-id":c.id}).store("data",c).insert({bottom:new Element(b.tag,{"class":"col-sm-1"}).update(c.orderNo)}).insert({bottom:new Element(b.tag,{"class":"col-sm-1"}).update(c.status)}).insert({bottom:new Element(b.tag,{"class":"col-sm-1"}).update(c.items.size())}).insert({bottom:new Element(b.tag,{"class":"col-sm-2"}).update(c.updated)}).insert({bottom:new Element(b.tag,{"class":"col-sm-2"}).update(c.updatedBy.person.fullname)}).insert({bottom:new Element(b.tag).update(c.comments)}).insert({bottom:new Element(b.tag,{"class":"col-sm-1"}).insert({bottom:new Element("a",{title:"Click to View the Details",href:"javascript: void(0);"}).insert({bottom:new Element("span",{"class":c.status==="OPEN"?"glyphicon glyphicon-pencil":"glyphicon glyphicon-eye-open"})}).observe("click",function(){b.me._openDetailsPage(c)})})});return b.row},getResult:function(b,c){var a={};a.me=this;a.reset=(b||false);a.me.postAjax(a.me.getCallbackId("getItems"),{pagination:a.me.pagination},{onLoading:function(){if(a.reset===true){a.me.pagination.pageNo=1;$(a.me.htmlIDs.listingDiv).update(a.me.getLoadingImg())}},onComplete:function(d,g){try{a.result=a.me.getResp(g,false,true);if(!a.result.items){return}if(a.reset===true){$(a.me.htmlIDs.listingDiv).update("");$(a.me.htmlIDs.totalCountDiv).update(a.result.pagination.totalRows)}a.result.items.each(function(e){$(a.me.htmlIDs.listingDiv).insert({bottom:a.me._getResultTableRow(e,false)})});$(a.me.htmlIDs.listingDiv).insert({bottom:a.me._getPaginationDiv(a.result.pagination)})}catch(f){$(a.me.htmlIDs.listingDiv).update(a.me.getAlertBox("Error: ",f).addClassName("alert-danger"))}if(typeof(c)==="function"){c()}}});return a.me}});