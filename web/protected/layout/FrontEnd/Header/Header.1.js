var HeaderJs=new Class.create;HeaderJs.prototype={initialize:function(){},load:function(e){var t={};return t.me=this,t.menuList=$$("ul#learn-chinese-menu").first(),t.menuList&&e.each(function(e){t.menuList.insert({bottom:new Element("li",{role:"presentation"}).insert({bottom:new Element("a",{href:"/product/"+e.id}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("div",{"class":"col-xs-12"}).update(e.title)})})})})}),t.me}};