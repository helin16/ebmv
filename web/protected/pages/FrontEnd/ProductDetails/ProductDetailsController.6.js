var PageJs=new Class.create();PageJs.prototype=Object.extend(new FrontPageJs(),{product:null,readOnline:function(c,f,e,b,d){var a={};a.me=this;a.readUrl=(f||"");if(a.readUrl.blank()){alert("System Error: no where to read it!");return}$(c).writeAttribute("originvalue",$F(c));a.me.getUser(c,function(){a.params={isbn:a.me.product.attributes.isbn[0].attribute,no:a.me.product.attributes.cno[0].attribute,siteID:e,uid:b,pwd:d};window.open(a.readUrl+"?"+$H(a.params).toQueryString())},function(){$(c).disabled=true;$(c).value="Processing ..."})},download:function(b){var a={};a.me=this;$(b).writeAttribute("originvalue",$F(b));a.me.getUser(b,function(){a.me._getDownLoadLink(b)},function(){$(b).disabled=true;$(b).value="Processing ..."})},_getDownLoadLink:function(b){var a={};a.me=this;a.me.postAjax(a.me.getCallbackId("download"),{},{onLoading:function(){},onComplete:function(c,f){try{a.result=a.me.getResp(f,false,true);if(a.result.url){window.open(a.result.url)}if(a.result.redirecturl){window.location=a.result.redirecturl}}catch(d){alert(d)}$(b).disabled=false;$(b).value=$(b).readAttribute("originvalue")}},120000)}});