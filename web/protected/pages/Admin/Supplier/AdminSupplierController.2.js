var PageJs=new Class.create();PageJs.prototype=Object.extend(new CrudPageJs(),{types:null,_geDl:function(b,a){return new Element("dl").insert({bottom:new Element("dt").update(b)}).insert({bottom:new Element("dd").update(a)})},_getItemRow:function(c,b){var a={};a.me=this;a.div=new Element("div",{"class":"panel panel-success item",item_id:c.id}).store("item",c).insert({bottom:new Element("div",{"class":"panel-heading"}).insert({bottom:new Element("div",{"class":"panel-title"}).insert({bottom:c.name}).insert({bottom:b.addClassName("pull-right")})})}).insert({bottom:new Element("div",{"class":"panel-body"}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-1"}).update(a.me._geDl("id:",c.id))}).insert({bottom:new Element("div",{"class":"col-xs-6"}).update(a.me._geDl("Name:",c.name))}).insert({bottom:new Element("div",{"class":"col-xs-4"}).update(a.me._geDl("Connector:",c.connector))}).insert({bottom:new Element("div",{"class":"col-xs-1"}).update(a.me._geDl("Active?",c.istitle===true?c.active:new Element("input",{type:"checkbox",checked:c.active,disabled:true})))})})}).insert({bottom:a.me._getInfoDiv(c)});return a.div},_getItemRowEditBtn:function(b){var a={};a.me=this;return new Element("span",{"class":"btn-group btn-group-xs"}).insert({bottom:new Element("span",{id:"edit_btn"+b.id,"class":"btn btn-default",title:"EDIT","data-loading-text":"Processing..."}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-pencil"})}).insert({bottom:" Edit"}).observe("click",function(){a.me.editItem(this)})}).insert({bottom:new Element("span",{id:"del_btn"+b.id,"class":"btn btn-danger",title:"DELETE","data-loading-text":"Processing..."}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-remove"})}).insert({bottom:" Delete"}).observe("click",function(){a.me.delItems([b.id])})})},_hideShowAllEditPens:function(){var a={};a.savePanel=$(this.resultDivId).down(".savePanel");if(a.savePanel){a.savePanel.down(".cancelBtn").click()}return this},showEditPanel:function(c,a){var b={};b.me=this;if(a===true){$(b.me.resultDivId).down(".item.titleRow").insert({after:b.me._getSavePanel({},"addDiv")})}else{b.row=$(c).up(".item");b.row.replace(this._getSavePanel(b.row.retrieve("item"),"editDiv"))}return this},_getResultDiv:function(a,b,d){var c={};c.me=this;c.includetitlerow=(b===false?false:true);c.resultDiv=new Element("div");if(c.includetitlerow===true){c.resultDiv.insert({bottom:new Element("p",{"class":"item titleRow"}).insert({bottom:new Element("span",{"class":"btn btn-success"}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-plus-sign"})}).insert({bottom:" Create NEW"}).observe("click",function(){c.me.createItem(this)})})})}c.i=(d||0);a.each(function(e){c.resultDiv.insert({bottom:c.me._getItemRow(e,c.me._getItemRowEditBtn(e))});c.i++});return c.resultDiv},_getInfoDiv:function(b){var a={};a.me=this;a.div=new Element("div",{"class":"list-group"});a.code="";$H(b.info).each(function(c){a.attrCode=c.key;if(typeof(c.value)==="object"){a.attrDiv=new Element("dl",{"class":"list-group-item dl-horizontal"});a.attrDiv.insert({bottom:new Element("dt").update(c.value[0].type.name)});if(a.attrCode!==a.code){a.code=a.attrCode}a.attrValeusDiv=new Element("dd");c.value.each(function(d){a.attrValeusDiv.insert({bottom:new Element("div",{"class":"attr_value"}).update(d.value)})});a.attrDiv.insert({bottom:a.attrValeusDiv});a.div.insert({bottom:a.attrDiv})}});return a.div},cancelEdit:function(b){var a={};a.me=this;a.item=$(b).up(".savePanel").retrieve("item");if(a.item.id===undefined||a.item.id===null){$(b).up(".savePanel").remove()}else{$(b).up(".savePanel").replace(a.me._getItemRow(a.item,a.me._getItemRowEditBtn(a.item)))}return this},_collectSavePanel:function(b){var a={};a.me=this;a.hasError=false;a.data={};a.savePanel=$(b).up(".savePanel");a.item=a.savePanel.retrieve("item");a.data.id=(a.item.id===null||a.item.id===undefined?"":a.item.id);a.savePanel.down(".msgRow").update("");a.savePanel.getElementsBySelector(".has-error").each(function(c){c.removeClassName("has-error")});a.errMsg="";a.savePanel.getElementsBySelector("[colname]").each(function(c){a.fieldValue=$F(c);a.field=c.readAttribute("colname");if(a.fieldValue.blank()&&c.readAttribute("noblank")){$(c).up(".form-group").addClassName("has-error");a.errMsg+="<li>"+a.field+" is required</li>"}a.data[a.field]=$F(c)});a.attrs=[];a.savePanel.getElementsBySelector("[attr_id]").each(function(c){a.fieldValue=$F(c);a.attrId=c.readAttribute("attr_id");a.attrTypeId=c.readAttribute("attr_type_id");if(a.fieldValue.blank()&&c.readAttribute("noblank")){$(c).up(".form-group").addClassName("has-error");a.errMsg+="<li>"+$(c).up(".form-group").down(".control-label").innerHTML+" is required</li>"}a.attrs.push({id:a.attrId,typeId:a.attrTypeId,value:a.fieldValue,active:(c.hasAttribute("deactivated")?0:1)})});a.data.info=a.attrs;if(!a.errMsg.blank()){a.savePanel.down(".msgRow").update(new Element("p",{"class":"alert alert-danger"}).update(a.errMsg));return null}return a.data},_afterSaveItems:function(c,a){var b={};b.item=a.items[0];$(c).up(".savePanel").replace(this._getItemRow(b.item,this._getItemRowEditBtn(b.item)));return this},_getSavePanel:function(c,a){var b={};b.me=this;b.isNew=(c.id===undefined||c.id===null);b.newDiv=new Element("div",{"class":"panel panel-default savePanel"}).addClassName(a).store("item",c).insert({bottom:new Element("div",{"class":"panel-heading"}).insert({bottom:new Element("div",{"class":"panel-title"}).insert({bottom:"Creating a new Supplier:"}).insert({bottom:new Element("span",{"class":"btn-group btn-group-xs pull-right"}).insert({bottom:new Element("span",{"class":"btn btn-primary saveBtn"}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-ok-circle"})}).insert({bottom:" Save"}).observe("click",function(){b.me.saveEditedItem(this)})}).insert({bottom:new Element("span",{"class":"btn btn-default cancelBtn"}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-remove-circle"})}).insert({bottom:" Cancel"}).observe("click",function(){b.me.cancelEdit(this)})})})})}).insert({bottom:new Element("div",{"class":"panel-body"}).insert({bottom:new Element("div",{"class":"msgRow"})}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:b.me._getSaveFieldDiv("Name",new Element("input",{value:(b.isNew?"":c.name),"class":"txt value",colname:"name",noblank:true,placeholder:"The name of the supplier"})).addClassName("col-xs-7")}).insert({bottom:b.me._getSaveFieldDiv("Connector",new Element("input",{value:(b.isNew?"":c.connector),"class":"txt value",colname:"connector",noblank:true,placeholder:"The connector script of the supplier"})).addClassName("col-xs-4")}).insert({bottom:b.me._getSaveFieldDiv("Act?",new Element("input",{type:"checkbox","class":"value",checked:(b.isNew===true?true:c.active),disabled:b.isNew,colname:"active"})).addClassName("col-xs-1")})})}).insert({bottom:b.me._getSaveAttrPanel(c.info)});return b.newDiv},_getNewAttrDiv:function(){var a={};a.me=this;a.typeSelection=new Element("select").update(new Element("option",{value:""}).update("Pls Select:")).observe("change",function(){$(this).up(".list-group-item").replace(a.me._getSaveFieldDiv($(this).options[$(this).selectedIndex].innerHTML,new Element("input",{value:"","class":"txt value",attr_id:"",attr_type_id:$F(this),noblank:true}),true).addClassName("list-group-item dl-horizontal"))});a.me.types.each(function(b){a.typeSelection.insert({bottom:new Element("option",{value:b.id}).update(b.name)})});return a.me._getSaveFieldDiv("Please Select a type: ",a.typeSelection,true)},_getSaveAttrPanel:function(a){var b={};b.me=this;b.div=new Element("div",{"class":"list-group"});$H(a).each(function(c){if(typeof(c.value)==="object"){c.value.each(function(d){b.div.insert({bottom:b.me._getSaveFieldDiv(d.type.name,new Element("input",{value:d.value,"class":"txt value",attr_id:d.id,attr_type_id:d.type.id,noblank:true}),true).addClassName("list-group-item dl-horizontal")})})}});b.div.insert({bottom:new Element("span",{"class":"btn btn-default"}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-plus-sign"})}).insert({bottom:" NEW Info"}).observe("click",function(){$(this).insert({before:b.me._getNewAttrDiv().addClassName("list-group-item dl-horizontal")})})});return b.div},_getSaveFieldDiv:function(d,c,b){var a={};a.me=this;a.showDelBtn=(b===true?true:false);a.ddDiv=new Element("dd").insert({bottom:c.addClassName("form-control")});if(a.showDelBtn===true){a.ddDiv.addClassName("input-group input-group-sm").insert({bottom:new Element("span",{"class":"btn btn-default input-group-addon"}).insert({bottom:new Element("span",{"class":"glyphicon glyphicon-remove"})}).observe("click",function(){if(!confirm("You are about to delete this attribute.\n Continue?")){return false}if($(this).up(".fielddiv").down(".value")){$(this).up(".fielddiv").down(".value").writeAttribute("deactivated",true)}$(this).up(".fielddiv").fade()})})}return new Element("dl",{"class":"form-group fielddiv"}).insert({bottom:new Element("dt",{"class":"control-label"}).update(d)}).insert({bottom:a.ddDiv})}});