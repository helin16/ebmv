var PageJs=new Class.create();PageJs.prototype=Object.extend(new FrontPageJs(),{product:null,resultDivId:"",ownTypeIds:{},_joinAtts:function(a,b){var c={};c.attrs=[];if(a){a.each(function(d){c.attrs.push(d[b])})}return c.attrs},_getAtts:function(e,d,c,a){var b={};b.me=this;if(!b.me.product.attributes[e]&&!a){return[]}b.overRideContent=(a||"");return new Element("div",{"class":"col-xs-6 attr-wrapper"}).addClassName(c).insert({bottom:new Element("div",{"class":"title"}).update(d)}).insert({bottom:new Element("div",{"class":"attribute"}).update((!b.overRideContent?b.me._joinAtts(b.me.product.attributes[e],"attribute").join(", "):b.overRideContent))})},_getLoadingImg:function(a){return new Element("img",{"class":"loadingImg",id:a,src:"/themes/images/loading.gif",width:"50px",height:"50px"})},displayProduct:function(){var a={};a.me=this;a.newDiv=new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5 left"}).insert({bottom:a.me._getProductImgDiv((a.me.product.attributes.image_thumb||null),{"class":"img-thumbnail"}).addClassName("img-thumbnail")})}).insert({bottom:new Element("div",{"class":"col-sm-7 right"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("h3").insert({bottom:a.me.product.title})})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:a.me._getAtts("author","<strong>作者/作者/Author:</strong>","author")}).insert({bottom:a.me._getAtts("isbn","<strong>ISBN:</strong>","product_isbn")}).insert({bottom:a.me._getAtts("publisher","<strong>出版社/出版社/Publisher:</strong>","product_publisher")}).insert({bottom:a.me._getAtts("publish_date","<strong>出版日期/出版日期/Publish Date:</strong>","product_publish_date")}).insert({bottom:a.me._getAtts("languages","<strong>语言/語言/Languages:</strong>","product_languages",a.me._joinAtts(a.me.product.languages,"name").join(", "))}).insert({bottom:a.me._getAtts("no_of_words","<strong>Length:</strong>","product_no_of_words")})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:a.me._getLoadingImg("copies_display")})}).insert({bottom:new Element("div",{"class":"clearfix attr-wrapper"})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div").insert({bottom:"<strong>内容简介/內容簡介/Description:</strong>"})}).insert({bottom:new Element("em").insert({bottom:a.me._joinAtts(a.me.product.attributes.description,"attribute").join(" ")})})}).insert({bottom:new Element("div",{"class":"clearfix attr-wrapper"})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:a.me._getLoadingImg("view_btn")})}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:a.me._getLoadingImg("downloadBtn")})})})});$(a.me.resultDivId).update(a.newDiv);a.me._getCopies("copies_display","view_btn","downloadBtn");return this},_getCopies:function(a,c,d){var b={};b.me=this;b.copiesHolder=$(a).up(".row");b.btnsHolder=$(c).up(".row");b.me.postAjax(b.me.getCallbackId("getCopies"),{},{onLoading:function(){},onComplete:function(f,h){try{b.result=b.me.getResp(h,false,true);b.readCopies=b.downloadCopies="N/A";b.readBtn=new Element("span",{"class":"btn btn-success iconbtn disabled popoverbtn visible-lg visible-md visible-sm visible-xs",id:"preadonlinebtn","data-loading-text":"处理中/處理中/Processing ..."}).insert({bottom:new Element("div",{"class":"btnname"}).insert({bottom:"在线阅读 / 在線閱讀"}).insert({bottom:new Element("small").update("Read Online")})});b.downloadBtn=new Element("span",{"class":"btn btn-success iconbtn disabled popoverbtn visible-lg visible-md visible-sm visible-xs",id:"pdownloadbtn","data-loading-text":"处理中/處理中/Processing ..."}).insert({bottom:new Element("div",{"class":"btnname"}).insert({bottom:"下载阅读 / 下載閱讀"}).insert({bottom:new Element("small").update("Download")})});if(b.result.urls.viewUrl&&b.result.copies[b.me.ownTypeIds.OnlineRead].avail*1>0){b.readCopies=b.result.copies[b.me.ownTypeIds.OnlineRead].avail+" out of "+b.result.copies[b.me.ownTypeIds.OnlineRead].total;b.readBtn.removeClassName("disabled").observe("click",function(){return b.me._getLink(this,"read")})}if(b.result.urls.downloadUrl&&b.result.copies[b.me.ownTypeIds.Download].avail*1>0){b.downloadCopies=b.result.copies[b.me.ownTypeIds.Download].avail+" out of "+b.result.copies[b.me.ownTypeIds.Download].total;b.downloadBtn.removeClassName("disabled").observe("click",function(){return b.me._getLink(this,"download")})}b.copiesHolder.update("").insert({bottom:b.me._getAtts("","<strong>Online Read Copies:</strong>","online_read_copies",b.readCopies)}).insert({bottom:b.me._getAtts("","<strong>Download Copies:</strong>","download_copies",b.downloadCopies)});if(b.result.warningMsg){b.btnsHolder.insert({top:b.me.getAlertBox("<h4>Error:</h4>",new Element("small").update(b.result.warningMsg.zh_CN+" / "+b.result.warningMsg.zh_TW+"<br />"+b.result.warningMsg.en)).addClassName("alert-danger")})}$(c).replace(b.readBtn);$(d).replace(b.downloadBtn)}catch(g){b.btnsHolder.insert({top:b.me.getAlertBox("ERROR:",g).addClassName("alert-danger")})}}},120000)},_openNewUrl:function(a){var b={};b.me=this;if(a.url){window.open(a.url)}if(a.redirecturl){window.location=a.redirecturl}return this},_getLink:function(b,c){var a={};a.me=this;a.me.getUser(b,function(){a.me.postAjax(a.me.getCallbackId("geturl"),{type:c},{onLoading:function(){},onSuccess:function(d,g){try{a.result=a.me.getResp(g,false,true);a.me._openNewUrl(a.result)}catch(f){$(b).insert({before:a.me.getAlertBox("Error: ",f).addClassName("alert-danger")})}},onComplete:function(d,e){jQuery("#"+b.id).button("reset")}},120000)},function(){jQuery("#"+b.id).button("loading")},function(){jQuery("#"+b.id).button("reset")});return false}});