document.domain="chinese.cn";
var _currentCourseId = 0;
var _currentLessonsId = 0;
var _courseId =0;
var _lessonsId = 0;

function tabLiClick(liObj,courseId)
{
  hblogtracker();
  _currentCourseId = courseId;
  $("#tabs1 ul li").removeClass("on");
  $("#tabs1 div").hide();
  tabContent(liObj,courseId);
  $(liObj).addClass("on");
  $(liObj).parent().next().show();
}


function hblogtracker()   
{ 	
  try{
	hbTracker(this);
  } catch(e){
  }
}
function tabContent(obj,courseId)
{
  hblogtracker();
  try
  {
    var params={
    "wareCourse.id":courseId,
    "reqType":'json',
    "s":Math.random()
    };
 	
 	$(obj).parent().next().html('<img src="/reservseProxy.php?url=http://res.chinese.cn/images/ajax-loader.gif" alt="loading" />');
	$.ajax(
		{
			type: "POST",
			url: '/reservseProxy.php?directRead=1&url=http://happychinese.chinese.cn/online/courseLessons.htm?wareCourse.id=' + lessonsId + '&reqType=json&s=' + Math.random(),
			cache: false,
			data: params,
			dataType: "json",
			timeout: 30000,
			success: function(datas)
			{
				var lessonsListStr = '';
				for(var i=0;i<datas['lessons'].length;i++)
				{
					lessonsListStr += '<li><a href="javascript:void(0);" onclick="getLessonStyles(' + datas['lessons'][i]['id'] + ',this)">' + datas['lessons'][i]['title'] + '</a></li>';
				}				
				$(obj).parent().next().html(lessonsListStr);
			},			
			error: function()
			{
				$(obj).parent().next().html('');
				alert("Server busy, try later!");				
				return;
			}
		}
	) 
  }
  catch(e)
  {
    $(obj).parent().next().html('');
    alert("Error:" + e.message);
  }
}

function getLessonStyles(lessonsId, obj)
{
hblogtracker();
  try
  {
	if(obj != null)
  		changeCurrent(obj);  
  
  	_currentLessonsId = lessonsId;
    var params={
    "lesson.id":lessonsId,
    "reqType":'json',
    "s":Math.random()
    };

	$("#flashcontent").html('<img src="/reservseProxy.php?url=http://res.chinese.cn/images/ajax-loader.gif" alt="loading" />');  
	$("#styles").html(''); 
	$.ajax(
		{
			type: "POST",
			url: '/reservseProxy.php?directRead=1&url=http://happychinese.chinese.cn/online/lessonStyles.htm?wareCourse.id=' + lessonsId + '&reqType=json&s=' + Math.random(),
			cache: false,
			data: params,
			dataType: "json",
			timeout: 30000,
			success: function(datas)
			{
				if(datas['items'] > 0)
				{
					var stylesListStr = '';
					for(var i=0;i<datas['styles'].length;i++)
					{
						stylesListStr += '<li' + (i==0?' class="current"':'')  + '><a href="javascript:void(0);" onclick="tabStylesClick(\'' + datas['styles'][i]['id'] + '\',\'' + datas['styles'][i]['mediaUrl'] + '\',this);">' + datas['styles'][i]['title'] + '</a></li>';
					}
					tabStylesClick(datas['styles'][0]['id'], datas['styles'][0]['mediaUrl']); //自动播放第一个媒体
					$("#styles").html(stylesListStr);
					
					if(_currentLessonsId  != _lessonsId) {					
						_courseId = _currentCourseId;
						_lessonsId = _currentLessonsId;
						$("#c01").html(datas['intro']);
						$("#c02").html(datas['body']);
					//	getWareCourseComments(lessonsId);					
					}
					
					$("#demoContent").hide();
					$("#mainContent").show();
					$("#intro").show();
					$("#comments").show();
				}
				else
				{					
					$("#flashcontent").html(''); 
					alert("还没有发布媒体！");
				}
			},
			error: function()
			{
				$("#flashcontent").html(''); 
				alert("Server busy, try later!");				
				return;
			}
		}
	) 
  }
  catch(e)
  {
    $("#styles").html('');
    alert("Error:" + e.message);     
  }
}
function   endWith(s1,s2) 
{  
    if(s1.length<s2.length)  
      return   false;  
    if(s1==s2)  
      return   true;  
    if(s1.substring(s1.length-s2.length)==s2)  
        return   true;  
}
function tabStylesClick(lesTypesId, mediaUrl, obj)
{
 hblogtracker();

	if(obj != null){
  		changeCurrent(obj);
		  		
	}

   //if(mediaUrl != '')
   if(endWith(mediaUrl,'.swf'))
   {
	   var so = new SWFObject("/reservseProxy.php?url=http://res.chinese.cn" + mediaUrl, "online", "438", "292", "7", "#FF0000");
	   so.addParam("quality", "height");
	   so.addParam("wmode", "transparent"); 
	   so.addParam("allowFullScreen","true");
	   so.addParam("allowscriptaccess","always");
	   so.write("flashcontent");   
   }
   else  if(endWith(mediaUrl,'.flv'))
   {
	  var url = "/reservseProxy.php?url=http://res.chinese.cn" + mediaUrl;
	  //alert(url);
	  var player = document.createElement("a");
	  //player.href="/reservseProxy.php?url=http://202.205.173.114/E-learning/HappyChinese/unit1/L2/2SituationalVideo/2.flv";
	  player.href=url;
	  //player.style="display:block;width:438px;height:292px";
	  player.id="player";
	  var div = document.getElementById("flashcontent");
	  div.innerHTML="";
	  div.appendChild(player);
	  var fplayer = flowplayer("player", "swf/flowplayer.swf",
				{
				clip:{ 
					//url:"/reservseProxy.php?url=http://202.205.173.114/E-learning/HappyChinese/unit1/L2/2SituationalVideo/2.flv",
	       		    autoPlay: false, 
	        	    autoBuffering: true
	    		},
	    		plugins: 
	    		 {            // load one or more plugins 
	       			controls: 
	       			{            // load the controls plugin 
		            	url: 'flowplayer.controls.swf',    // always: where to find the Flash object 
		            	playlist: false,                // now the custom options of the Flash object 
		            	backgroundColor: '#aedaff', 
		            	tooltips: 
	            		{                // this plugin object exposes a 'tooltips' object 
			                buttons: true, 
			                fullscreen: 'Enter Fullscreen mode' 
	            		} 
	        		} 
	    		}
	   		});
	    fplayer.onLoad(function()  { 
	        this.setVolume(100);
	        this.play();
	    });

   }
}

function changeCurrent(obj)
{
	if(obj != null)
	{
	  	$(obj).parent().addClass("current")
	  	$(obj).parent().siblings().removeClass("current");
  	}
}

function getWareCourseComments(lessonsId)
{
}

function changePage(toPage)
{
}

function getWareCourseCommentsRequest(params)
{
}

function getWareCourseCommentsCallback(datas)
{
}

function sendWareCourseComments() {
}

function reloadValidateImage()
{
	$("#validateCodeImage").attr("src","/validateCode?" + Math.random());
}

function DrawImage(ImgD,w,h)
{
    var flag = false;
    var MyImage = new Image();
    MyImage.src = ImgD.src;
    if(MyImage.width > 0 && MyImage.height > 0){
        flag = true;
        if(MyImage.width / MyImage.height >= w / h){
            if(MyImage.width > w){
                ImgD.width = w;
                ImgD.height = (MyImage.height * w) / MyImage.width;
            }else{
                ImgD.width = MyImage.width;
                ImgD.height = MyImage.height;
            }
            ImgD.alt = "Size：" + MyImage.width + "x" + MyImage.height;
        }else{
            if(MyImage.height > h){
                ImgD.height = h;
                ImgD.width = (MyImage.width * h) / MyImage.height;
            }else{
                ImgD.width = MyImage.width;
                ImgD.height = MyImage.height;
            }
            ImgD.alt = "Size：" + MyImage.width + "x" + MyImage.height;
        }
    }
}
function zoomimg(img)
{
    var zoom=parseInt(img.style.zoom,10) || 100;
    zoom += event.wheelDelta / 24;
    imgW = img.clientWidth*zoom/100;
    if (zoom>10) img.style.zoom = zoom + "%";

    return false;
}

$(document).ready(function()
{
	   var so = new SWFObject("/reservseProxy.php?url=http://res.chinese.cn/flash/guide.swf", "demo", "505", "400", "7", "#ffffff");
	   so.addParam("quality", "height");
	   so.addParam("wmode", "transparent"); 
	   so.addParam("allowFullScreen","true");
	   so.addParam("allowscriptaccess","always");
	   so.write("flashcontent01"); 
	   
		var SDmodel = new scrollDoor();
		SDmodel.sd(["m01","m02"],["c01","c02"],"sd0101","sd0201");	   
});
