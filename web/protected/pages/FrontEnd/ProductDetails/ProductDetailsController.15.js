var PageJs=new Class.create;PageJs.prototype=Object.extend(new FrontPageJs,{product:null,resultDivId:"",ownTypeIds:{},_joinAtts:function(e,t){var n={};return n.attrs=[],e&&e.each(function(e){n.attrs.push(e[t])}),n.attrs},_getAtts:function(e,t,n,o){var s={};return s.me=this,s.me.product.attributes[e]||o?(s.overRideContent=o||"",new Element("div",{"class":"col-xs-6 attr-wrapper"}).addClassName(n).insert({bottom:new Element("div",{"class":"title"}).update(t)}).insert({bottom:new Element("div",{"class":"attribute"}).update(s.overRideContent?s.overRideContent:s.me._joinAtts(s.me.product.attributes[e],"attribute").join(", "))})):[]},_getLoadingImg:function(e){return new Element("img",{"class":"loadingImg",id:e,src:"/themes/images/loading.gif",width:"50px",height:"50px"})},displayProduct:function(){var e={};return e.me=this,e.newDiv=new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-sm-5 left"}).insert({bottom:e.me._getProductImgDiv(e.me.product.attributes.image_thumb||null,{"class":"img-thumbnail"}).addClassName("img-thumbnail")})}).insert({bottom:new Element("div",{"class":"col-sm-7 right"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("h3").insert({bottom:e.me.product.title})})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:e.me._getAtts("author","<strong>作者/作者/Author:</strong>","author")}).insert({bottom:e.me._getAtts("isbn","<strong>ISBN:</strong>","product_isbn")}).insert({bottom:e.me._getAtts("publisher","<strong>出版社/出版社/Publisher:</strong>","product_publisher")}).insert({bottom:e.me._getAtts("publish_date","<strong>出版日期/出版日期/Publish Date:</strong>","product_publish_date")}).insert({bottom:e.me._getAtts("languages","<strong>语言/語言/Languages:</strong>","product_languages",e.me._joinAtts(e.me.product.languages,"name").join(", "))}).insert({bottom:e.me._getAtts("no_of_words","<strong>Length:</strong>","product_no_of_words")})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:e.me._getLoadingImg("copies_display")})}).insert({bottom:new Element("div",{"class":"clearfix attr-wrapper"})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div").insert({bottom:"<strong>内容简介/內容簡介/Description:</strong>"})}).insert({bottom:new Element("em").insert({bottom:e.me._joinAtts(e.me.product.attributes.description,"attribute").join(" ")})})}).insert({bottom:new Element("div",{"class":"clearfix attr-wrapper"})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:e.me._getLoadingImg("view_btn")})}).insert({bottom:new Element("div",{"class":"col-xs-6"}).insert({bottom:e.me._getLoadingImg("downloadBtn")})})})}),$(e.me.resultDivId).update(e.newDiv),e.me._getCopies("copies_display","view_btn","downloadBtn"),this},_getCopies:function(e,t,n){var o={};o.me=this,o.copiesHolder=$(e).up(".row"),o.btnsHolder=$(t).up(".row"),o.me.postAjax(o.me.getCallbackId("getCopies"),{},{onLoading:function(){},onComplete:function(e,s){try{o.result=o.me.getResp(s,!1,!0),console.debug(o.result),o.readCopies=o.downloadCopies="N/A",o.readBtn=new Element("span",{"class":"btn btn-success iconbtn disabled popoverbtn visible-lg visible-md visible-sm visible-xs",id:"preadonlinebtn","data-loading-text":"处理中/處理中/Processing ..."}).insert({bottom:new Element("div",{"class":"btnname"}).insert({bottom:"在线阅读 / 在線閱讀"}).insert({bottom:new Element("small").update("Read Online")})}),o.downloadBtn=new Element("span",{"class":"btn btn-success iconbtn disabled popoverbtn visible-lg visible-md visible-sm visible-xs",id:"pdownloadbtn","data-loading-text":"处理中/處理中/Processing ..."}).insert({bottom:new Element("div",{"class":"btnname"}).insert({bottom:"下载阅读 / 下載閱讀"}).insert({bottom:new Element("small").update("Download")})}),o.result.urls.viewUrl&&1*o.result.copies[o.me.ownTypeIds.OnlineRead].avail>0&&(o.readCopies=o.result.copies[o.me.ownTypeIds.OnlineRead].avail+" out of "+o.result.copies[o.me.ownTypeIds.OnlineRead].total,o.readBtn.removeClassName("disabled").observe("click",function(){return o.me._getLink(this,"read")})),o.result.urls.downloadUrl&&1*o.result.copies[o.me.ownTypeIds.Download].avail>0&&(o.downloadCopies=o.result.copies[o.me.ownTypeIds.Download].avail+" out of "+o.result.copies[o.me.ownTypeIds.Download].total,o.downloadBtn.removeClassName("disabled").observe("click",function(){return o.me._getLink(this,"download")})),o.copiesHolder.update("").insert({bottom:o.me._getAtts("","<strong>Online Read Copies:</strong>","online_read_copies",o.readCopies)}).insert({bottom:o.me._getAtts("","<strong>Download Copies:</strong>","download_copies",o.downloadCopies)}),o.result.warningMsg&&o.btnsHolder.insert({top:o.me.getAlertBox("<h4>Warning:</h4>",new Element("small").update(o.result.warningMsg.zh_CN+" / "+o.result.warningMsg.zh_TW+"<br />"+o.result.warningMsg.en)).addClassName("alert-warning")}),o.result.stopMsg&&o.btnsHolder.insert({top:o.me.getAlertBox("<h4>Error:</h4>",new Element("small").update(o.result.stopMsg.zh_CN+" / "+o.result.stopMsg.zh_TW+"<br />"+o.result.stopMsg.en)).addClassName("alert-danger")}),$(t).replace(o.readBtn),$(n).replace(o.downloadBtn)}catch(r){o.btnsHolder.insert({top:o.me.getAlertBox("ERROR:",r).addClassName("alert-danger")})}}},12e4)},_openNewUrl:function(e){var t={};return t.me=this,e.url&&window.open(e.url),e.redirecturl&&(window.location=e.redirecturl),this},_getLink:function(e,t){var n={};return n.me=this,n.me.getUser(e,function(){n.me.postAjax(n.me.getCallbackId("geturl"),{type:t},{onLoading:function(){},onSuccess:function(t,o){try{n.result=n.me.getResp(o,!1,!0),n.me._openNewUrl(n.result)}catch(s){$(e).insert({before:n.me.getAlertBox("Error: ",s).addClassName("alert-danger")})}},onComplete:function(){jQuery("#"+e.id).button("reset")}},12e4)},function(){jQuery("#"+e.id).button("loading")},function(){jQuery("#"+e.id).button("reset")}),!1}});