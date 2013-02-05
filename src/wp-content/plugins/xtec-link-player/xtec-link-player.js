/*
xlp Media Player 2.0
Makes any mp3, Flash flv, Quicktime mov, mp4, m4v, m4a, m4b and 3gp as well as wmv, avi and asf links playable directly on your webpage while optionally hiding the download link. Flash movies, including YouTube etc, use SWFObject javascript embeds - usage examples at http://blog.deconcept.com/swfobject/#examples
xlp.js is based on various hacks of excellent scripts - Del.icio.us mp3 Playtagger javascript (http://del.icio.us/help/playtagger) as used in Taragana's Del.icio.us mp3 Player Plugin (http://blog.taragana.com/index.php/archive/taraganas-delicious-mp3-player-wordpress-plugin/) - Jeroen Wijering's Flv Player (http://www.jeroenwijering.com/?item=Flash_Video_Player) with Tradebit modifications (http://www.tradebit.com) - EMFF inspired WP Audio Player mp3 player (http://www.1pixelout.net/code/audio-player-wordpress-plugin). Flash embeds via Geoff Stearns' excellent standards compliant Flash detection and embedding JavaScript (http://blog.deconcept.com/swfobject/).
Distributed under GNU General Public License.
*/

// Configure plugin options below

var xlp_url = xtec_link_player_url // http address for the xlp-media plugin folder (no trailing slash).
var viddownloadLink = 'none'    // Download link for flv and wmv links: One of 'none' (to turn downloading off) or 'inline' to display the link. ***Use $qtkiosk for qt***.

// MP3 Flash player options
var playerloop = 'no'        // Loop the music ... yes or no?
var mp3downloadLink = 'none'    // Download for mp3 links: One of 'none' (to turn downloading off) or 'inline' to display the link.

// Hex colours for the MP3 Flash Player (minus the #)
var playerbg ='DDDDDD'                // Background colour
var playerleftbg = 'BBBBBB'        // Left background colour
var playerrightbg = 'BBBBBB'        // Right background colour
var playerrightbghover = '666666'    // Right background colour (hover)
var playerlefticon = '000000'        // Left icon colour
var playerrighticon = '000000'        // Right icon colour
var playerrighticonhover = 'FFFFFF'    // Right icon colour (hover)
var playertext = '333333'            // Text colour
var playerslider = '666666'        // Slider colour
var playertrack = '999999'            // Loader bar colour
var playerloader = '666666'        // Progress track colour
var playerborder = '333333'        // Progress track border colour

// Flash video player options
var flvwidth = '400'     // Width of the flv player
var flvheight = '320'    // Height of the flv player (allow 20px for controller)
var flvfullscreen = 'true' // Show fullscreen button, true or false (no auto return on Safari, double click in IE6)

//Quicktime player options
var qtloop = 'false'        // Loop Quicktime movies: true or false.
var qtwidth = '400'        // Width of your Quicktime player
var qtheight = '316'    // Height of your Quicktime player (allow 16px for controller)
var qtkiosk = 'true'        // Allow downloads, false = yes, true = no.
var qtversion = '6';

//WMV player options
var wmvwidth = '400'    // Width of your WMV player
var wmvheight = '372'    // Height of your WMV player (allow 45px for WMV controller or 16px if QT player - ignored by WinIE)
	
// CSS styles
var mp3playerstyle = 'vertical-align:bottom; margin:10px 0 5px 2px;'    // Flash mp3 player css style
var mp3imgmargin = '0.5em 0.5em -4px 5px'        // Mp3 button image css margins
var vidimgmargin = '0'        // Video image placeholder css margins
/* ------------------ End configuration options --------------------- */

/* --------------------- Flash MP3 audio player ----------------------- */
if(typeof(xlp) == 'undefined') xlp = {}
xlp.Mp3 = {
    playimg: null,
    player: null,
    go: function() {
        var all = document.getElementsByTagName('a')
        for (var i = 0, o; o = all[i]; i++) {
            if(o.href.match(/\.mp3$/i) && o.className!="amplink") {
                o.style.display = mp3downloadLink
                var img = document.createElement('img')
                img.src = xlp_url+'/images/audio_mp3_play.gif'; img.title = 'Click to listen'
                img.style.margin = mp3imgmargin
                img.style.border = 'none'
                img.style.cursor = 'pointer'
                img.onclick = xlp.Mp3.makeToggle(img, o.href)
                o.parentNode.insertBefore(img, o)
    }}},
    toggle: function(img, url) {
        if (xlp.Mp3.playimg == img) xlp.Mp3.destroy()
        else {
            if (xlp.Mp3.playimg) xlp.Mp3.destroy()
            img.src = xlp_url+'/images/audio_mp3_stop.gif'; xlp.Mp3.playimg = img;
            xlp.Mp3.player = document.createElement('span')
            xlp.Mp3.player.innerHTML = '<br /><object style="'+mp3playerstyle+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
            'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"' +
            'width="290" height="24" id="player" align="middle">' +
            '<param name="wmode" value="transparent" />' +
            '<param name="allowScriptAccess" value="sameDomain" />' +
            '<param name="flashVars" value="bg=0x'+playerbg+'&amp;leftbg=0x'+playerleftbg+'&amp;rightbg=0x'+playerrightbg+'&amp;rightbghover=0x'+playerrightbghover+'&amp;lefticon=0x'+playerlefticon+'&amp;righticon=0x'+playerrighticon+'&amp;righticonhover=0x'+playerrighticonhover+'&amp;text=0x'+playertext+'&amp;slider=0x'+playerslider+'&amp;track=0x'+playertrack+'&amp;loader=0x'+playerloader+'&amp;border=0x'+playerborder+'&amp;autostart=yes&amp;loop='+playerloop+'&amp;soundFile='+url+'" />' +
            '<param name="movie" value="'+xlp_url+'/player.swf" /><param name="quality" value="high" />' +
            '<embed style="'+mp3playerstyle+'" src="'+xlp_url+'/player.swf" flashVars="bg=0x'+playerbg+'&amp;leftbg=0x'+playerleftbg+'&amp;rightbg=0x'+playerrightbg+'&amp;rightbghover=0x'+playerrightbghover+'&amp;lefticon=0x'+playerlefticon+'&amp;righticon=0x'+playerrighticon+'&amp;righticonhover=0x'+playerrighticonhover+'&amp;text=0x'+playertext+'&amp;slider=0x'+playerslider+'&amp;track=0x'+playertrack+'&amp;loader=0x'+playerloader+'&amp;border=0x'+playerborder+'&amp;autostart=yes&amp;loop='+playerloop+'&amp;soundFile='+url+'" '+
            'quality="high" wmode="transparent" width="290" height="24" name="player"' +
            'align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
            ' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object><br />'
            img.parentNode.insertBefore(xlp.Mp3.player, img.nextSibling)
    }},
    destroy: function() {
        xlp.Mp3.playimg.src = xlp_url+'/images/audio_mp3_play.gif'; xlp.Mp3.playimg = null
        xlp.Mp3.player.removeChild(xlp.Mp3.player.firstChild); xlp.Mp3.player.parentNode.removeChild(xlp.Mp3.player); xlp.Mp3.player = null
    },
    makeToggle: function(img, url) { return function(){ xlp.Mp3.toggle(img, url) }}
}

/* ----------------- Flash flv video player ----------------------- */

if(typeof(xlp) == 'undefined') xlp = {}
xlp.FLV = {
    go: function() {
        var all = document.getElementsByTagName('a')
        for (var i = 0, o; o = all[i]; i++) {
            if(o.href.match(/\.flv$/i) && o.className!="amplink") {
	            o.style.display = viddownloadLink
	            url = o.href
	            var flvplayer = document.createElement('span')
	            flvplayer.innerHTML = '<object type="application/x-shockwave-flash" wmode="transparent" data="'+xlp_url+'/flvplayer.swf?click='+xlp_url+'/images/flvplaybutton.jpg&file='+url+'&showfsbutton='+flvfullscreen+'" height="'+flvheight+'" width="'+flvwidth+'">' +
	            '<param name="movie" value="'+xlp_url+'/flvplayer.swf?click='+xlp_url+'/images/flvplaybutton.jpg&file='+url+'&showfsbutton='+flvfullscreen+'"> <param name="wmode" value="transparent">' +
	            '<embed src="'+xlp_url+'/flvplayer.swf?file='+url+'&click='+xlp_url+'/images/flvplaybutton.jpg&&showfsbutton='+flvfullscreen+'" ' + 
	            'width="'+flvwidth+'" height="'+flvheight+'" name="flvplayer" align="middle" ' + 
	            'play="true" loop="false" quality="high" allowScriptAccess="sameDomain" ' +
	            'type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">' + 
	            '</embed></object><br />'
	            o.parentNode.insertBefore(flvplayer, o)
            }
        }
    }
}

/* ----------------------- QUICKTIME DETECT --------------------------- 
// Bits of code by Chris Nott (chris[at]dithered[dot]com) and
// Geoff Stearns (geoff@deconcept.com, http://www.deconcept.com/)
--------------------------------------------------------------------- */

function getQuicktimeVersion() {
var n=navigator;
var nua=n.userAgent;
var saf=(nua.indexOf('Safari')!=-1);
var quicktimeVersion = 0;

if (saf) {
quicktimeVersion='9.0';
}
else {    
var agent = navigator.userAgent.toLowerCase(); 
    
    // NS3+, Opera3+, IE5+ Mac (support plugin array):  check for Quicktime plugin in plugin array
    if (navigator.plugins != null && navigator.plugins.length > 0) {
      for (i=0; i < navigator.plugins.length; i++ ) {
         var plugin =navigator.plugins[i];
         if (plugin.name.indexOf("QuickTime") > -1) {
            quicktimeVersion = parseFloat(plugin.name.substring(18));
         }
      }
    }
       else if (window.ActiveXObject) {
        execScript('on error resume next: qtObj = IsObject(CreateObject("QuickTime.QuickTime.4"))','VBScript');
            if (qtObj == true) {
                quicktimeVersion = 100;
                }
            else {
                quicktimeVersion = 0;
            }
        }
    }
    return quicktimeVersion;
}

/* ----------------------- Quicktime player ------------------------ */

if(typeof(xlp) == 'undefined') xlp = {}
xlp.MOV = {
    playimg: null,
    player: null,
    go: function() {
        var all = document.getElementsByTagName('a')
        xlp.MOV.preview_images = { }
        for (var i = 0, o; o = all[i]; i++) {
            if(o.href.match(/\.mov$|\.mp4$|\.m4v$|\.m4b$|\.3gp$/i) && o.className!="amplink") {
                o.style.display = 'none'
                var img = document.createElement('img')
                xlp.MOV.preview_images[i] = document.createElement('img') ;
                xlp.MOV.preview_images[i].src = o.href + '.jpg' ;
                xlp.MOV.preview_images[i].defaultImg = img ;
                xlp.MOV.preview_images[i].replaceDefault = function() {
                  this.defaultImg.src = this.src ; 
                }
                xlp.MOV.preview_images[i].onload = xlp.MOV.preview_images[i].replaceDefault ;
                img.src = xlp_url+'/images/vid_play.gif'
                img.title = 'Click to play video'
                img.style.margin = vidimgmargin
                img.style.padding = '0px'
                img.style.display = 'block'
                img.style.border = 'none'
                img.style.cursor = 'pointer'
                img.height = qtheight
                img.width = qtwidth
                img.onclick = xlp.MOV.makeToggle(img, o.href)
                o.parentNode.insertBefore(img, o)
            }
        }
    },
    toggle: function(img, url) {
        if (xlp.MOV.playimg == img) xlp.MOV.destroy()
        else {
            if (xlp.MOV.playimg) xlp.MOV.destroy()
            img.src = xlp_url+'/images/vid_play.gif'
            img.style.display = 'none'; 
            xlp.MOV.playimg = img;
            xlp.MOV.player = document.createElement('p')
            var quicktimeVersion = getQuicktimeVersion()
            if (quicktimeVersion >= qtversion) {
            xlp.MOV.player.innerHTML = '<embed src="'+url+'" width="'+qtwidth+'" height="'+qtheight+'" loop="'+qtloop+'" autoplay="true" controller="true" border="0" type="video/quicktime" kioskmode="'+qtkiosk+'" scale="tofit"></embed><br />'
          img.parentNode.insertBefore(xlp.MOV.player, img.nextSibling)
          }
        else
            xlp.MOV.player.innerHTML = '<a href="http://www.apple.com/quicktime/download/" target="_blank"><img src="'+xlp_url+'/images/getqt.jpg"></a>'
          img.parentNode.insertBefore(xlp.MOV.player, img.nextSibling)
        }
    },
    destroy: function() {
    },
    makeToggle: function(img, url) { return function(){ xlp.MOV.toggle(img, url) }}
}

/* --------------------- MPEG 4 Audio Quicktime player ---------------------- */

if(typeof(xlp) == 'undefined') xlp = {}
xlp.M4a = {
    playimg: null,
    player: null,
    go: function() {
        var all = document.getElementsByTagName('a')
        for (var i = 0, o; o = all[i]; i++) {
            if(o.href.match(/\.m4a$/i) && o.className!="amplink") {
                o.style.display = 'none'
                var img = document.createElement('img')
                img.src = xlp_url+'/images/audio_mp4_play.gif'; img.title = 'Click to listen'
                img.style.margin = mp3imgmargin
                img.style.border = 'none'
                img.style.cursor = 'pointer'
                img.onclick = xlp.M4a.makeToggle(img, o.href)
                o.parentNode.insertBefore(img, o)
        	}
        }
    },
    toggle: function(img, url) {
        if (xlp.M4a.playimg == img) xlp.M4a.destroy()
        else {
            if (xlp.M4a.playimg) xlp.M4a.destroy()
            img.src = xlp_url+'/images/audio_mp4_stop.gif'; xlp.M4a.playimg = img;
            xlp.M4a.player = document.createElement('p')
            var quicktimeVersion = getQuicktimeVersion()
            if (quicktimeVersion >= qtversion) {
            xlp.M4a.player.innerHTML = '<embed src="'+url+'" width="160" height="16" loop="'+qtloop+'" autoplay="true" controller="true" border="0" type="video/quicktime" kioskmode="'+qtkiosk+'" ></embed><br />'
          img.parentNode.insertBefore(xlp.M4a.player, img.nextSibling)
          }
        else
            xlp.M4a.player.innerHTML = '<a href="http://www.apple.com/quicktime/download/" target="_blank"><img src="'+xlp_url+'/images/getqt.jpg"></a>'
          img.parentNode.insertBefore(xlp.M4a.player, img.nextSibling)
    }},
    destroy: function() {
        xlp.M4a.playimg.src = xlp_url+'/images/audio_mp4_play.gif'; xlp.M4a.playimg = null
        xlp.M4a.player.removeChild(xlp.M4a.player.firstChild); xlp.M4a.player.parentNode.removeChild(xlp.M4a.player); xlp.M4a.player = null
    },
    makeToggle: function(img, url) { return function(){ xlp.M4a.toggle(img, url) }}
}

/* ----------------------- WMV player -------------------------- */

if(typeof(xlp) == 'undefined') xlp = {}
xlp.WMV = {
    playimg: null,
    player: null,
    go: function() {
        var all = document.getElementsByTagName('a')
        for (var i = 0, o; o = all[i]; i++) {
            if(o.href.match(/\.asf$|\.avi$|\.wmv$/i) && o.className!="amplink") {
                o.style.display = viddownloadLink
                var img = document.createElement('img')
                img.src = xlp_url+'/images/vid_play.gif'; img.title = 'Click to play video'
                img.style.margin = '0px'
                img.style.padding = '0px'
                img.style.display = 'block'
                img.style.border = 'none'
                img.style.cursor = 'pointer'
                img.height = qtheight
                img.width = qtwidth
                img.onclick = xlp.WMV.makeToggle(img, o.href)
                o.parentNode.insertBefore(img, o)
    }}},
    toggle: function(img, url) {
        if (xlp.WMV.playimg == img) xlp.WMV.destroy()
        else {
              if (xlp.WMV.playimg) xlp.WMV.destroy()
              img.src = xlp_url+'/images/vid_play.gif'
              img.style.display = 'none'; 
              xlp.WMV.playimg = img;
              xlp.WMV.player = document.createElement('span')
              if(navigator.userAgent.indexOf('Mac') != -1) {
              xlp.WMV.player.innerHTML = '<embed src="'+url+'" width="'+qtwidth+'" height="'+qtheight+'" loop="'+qtloop+'" autoplay="true" controller="true" border="0" type="video/quicktime" kioskmode="'+qtkiosk+'" scale="tofit" pluginspage="http://www.apple.com/quicktime/download/"></embed><br />'
              img.parentNode.insertBefore(xlp.WMV.player, img.nextSibling)
              } else {
              if (navigator.plugins && navigator.plugins.length) {
              xlp.WMV.player.innerHTML = '<embed type="application/x-mplayer2" src="'+url+'" ' +
              'showcontrols="1" ShowStatusBar="1" autostart="1" displaySize="4"' +
              'pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/"' +
              'width="'+wmvwidth+'" height="'+wmvheight+'">' +
              '</embed><br />'
              img.parentNode.insertBefore(xlp.WMV.player, img.nextSibling)
              } else {
                xlp.WMV.player.innerHTML = '<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'+wmvwidth+'" height="'+wmvheight+'" id="player"> ' +
              '<param name="url" value="'+url+'" /> ' +
              '<param name="autoStart" value="True" /> ' +
              '<param name="stretchToFit" value="True" /> ' +
              '<param name="showControls" value="True" /> ' +
              '<param name="ShowStatusBar" value="True" /> ' +
              '<embed type="application/x-mplayer2" src="'+url+'" ' +
              'showcontrols="1" ShowStatusBar="1" autostart="1" displaySize="4"' +
              'pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/"' +
              'width="'+wmvwidth+'" height="'+wmvheight+'">' +
              '</embed>' +
              '</object><br />'
              img.parentNode.insertBefore(xlp.WMV.player, img.nextSibling)
              }}
    }},
    destroy: function() {
        xlp.WMV.playimg.src = xlp_url+'/images/vid_play.gif'
        xlp.WMV.playimg.style.display = 'inline'; xlp.WMV.playimg = null
        xlp.WMV.player.removeChild(xlp.WMV.player.firstChild); 
        xlp.WMV.player.parentNode.removeChild(xlp.WMV.player); 
        xlp.WMV.player = null
    },
    makeToggle: function(img, url) { return function(){ xlp.WMV.toggle(img, url) }}
}

/* ----------------- Trigger players onload ----------------------- */

xlp.addLoadEvent = function(f) { var old = window.onload
    if (typeof old != 'function') window.onload = f
    else { window.onload = function() { old(); f() }}
}

xlp.addLoadEvent(xlp.Mp3.go)
xlp.addLoadEvent(xlp.FLV.go)
xlp.addLoadEvent(xlp.MOV.go)
xlp.addLoadEvent(xlp.M4a.go)
xlp.addLoadEvent(xlp.WMV.go)

/**
 * SWFObject v1.5: Flash Player detection and embed - http://blog.deconcept.com/swfobject/
 *
 * SWFObject is (c) 2006 Geoff Stearns and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
if(typeof deconcept=="undefined"){var deconcept=new Object();}if(typeof deconcept.util=="undefined"){deconcept.util=new Object();}if(typeof deconcept.SWFObjectUtil=="undefined"){deconcept.SWFObjectUtil=new Object();}deconcept.SWFObject=function(_1,id,w,h,_5,c,_7,_8,_9,_a){if(!document.getElementById){return;}this.DETECT_KEY=_a?_a:"detectflash";this.skipDetect=deconcept.util.getRequestParameter(this.DETECT_KEY);this.params=new Object();this.variables=new Object();this.attributes=new Array();if(_1){this.setAttribute("swf",_1);}if(id){this.setAttribute("id",id);}if(w){this.setAttribute("width",w);}if(h){this.setAttribute("height",h);}if(_5){this.setAttribute("version",new deconcept.PlayerVersion(_5.toString().split(".")));}this.installedVer=deconcept.SWFObjectUtil.getPlayerVersion();if(!window.opera&&document.all&&this.installedVer.major>7){deconcept.SWFObject.doPrepUnload=true;}if(c){this.addParam("bgcolor",c);}var q=_7?_7:"high";this.addParam("quality",q);this.setAttribute("useExpressInstall",false);this.setAttribute("doExpressInstall",false);var _c=(_8)?_8:window.location;this.setAttribute("xiRedirectUrl",_c);this.setAttribute("redirectUrl","");if(_9){this.setAttribute("redirectUrl",_9);}};deconcept.SWFObject.prototype={useExpressInstall:function(_d){this.xiSWFPath=!_d?"expressinstall.swf":_d;this.setAttribute("useExpressInstall",true);},setAttribute:function(_e,_f){this.attributes[_e]=_f;},getAttribute:function(_10){return this.attributes[_10];},addParam:function(_11,_12){this.params[_11]=_12;},getParams:function(){return this.params;},addVariable:function(_13,_14){this.variables[_13]=_14;},getVariable:function(_15){return this.variables[_15];},getVariables:function(){return this.variables;},getVariablePairs:function(){var _16=new Array();var key;var _18=this.getVariables();for(key in _18){_16.push(key+"="+_18[key]);}return _16;},getSWFHTML:function(){var _19="";if(navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","PlugIn");this.setAttribute("swf",this.xiSWFPath);}_19="<embed type=\"application/x-shockwave-flash\" src=\""+this.getAttribute("swf")+"\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\"";_19+=" id=\""+this.getAttribute("id")+"\" name=\""+this.getAttribute("id")+"\" ";var _1a=this.getParams();for(var key in _1a){_19+=[key]+"=\""+_1a[key]+"\" ";}var _1c=this.getVariablePairs().join("&");if(_1c.length>0){_19+="flashvars=\""+_1c+"\"";}_19+="/>";}else{if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","ActiveX");this.setAttribute("swf",this.xiSWFPath);}_19="<object id=\""+this.getAttribute("id")+"\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\">";_19+="<param name=\"movie\" value=\""+this.getAttribute("swf")+"\" />";var _1d=this.getParams();for(var key in _1d){_19+="<param name=\""+key+"\" value=\""+_1d[key]+"\" />";}var _1f=this.getVariablePairs().join("&");if(_1f.length>0){_19+="<param name=\"flashvars\" value=\""+_1f+"\" />";}_19+="</object>";}return _19;},write:function(_20){if(this.getAttribute("useExpressInstall")){var _21=new deconcept.PlayerVersion([6,0,65]);if(this.installedVer.versionIsValid(_21)&&!this.installedVer.versionIsValid(this.getAttribute("version"))){this.setAttribute("doExpressInstall",true);this.addVariable("MMredirectURL",escape(this.getAttribute("xiRedirectUrl")));document.title=document.title.slice(0,47)+" - Flash Player Installation";this.addVariable("MMdoctitle",document.title);}}if(this.skipDetect||this.getAttribute("doExpressInstall")||this.installedVer.versionIsValid(this.getAttribute("version"))){var n=(typeof _20=="string")?document.getElementById(_20):_20;n.innerHTML=this.getSWFHTML();return true;}else{if(this.getAttribute("redirectUrl")!=""){document.location.replace(this.getAttribute("redirectUrl"));}}return false;}};deconcept.SWFObjectUtil.getPlayerVersion=function(){var _23=new deconcept.PlayerVersion([0,0,0]);if(navigator.plugins&&navigator.mimeTypes.length){var x=navigator.plugins["Shockwave Flash"];if(x&&x.description){_23=new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").split("."));}}else{if(navigator.userAgent&&navigator.userAgent.indexOf("Windows CE")>=0){var axo=1;var _26=3;while(axo){try{_26++;axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+_26);_23=new deconcept.PlayerVersion([_26,0,0]);}catch(e){axo=null;}}}else{try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");}catch(e){try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");_23=new deconcept.PlayerVersion([6,0,21]);axo.AllowScriptAccess="always";}catch(e){if(_23.major==6){return _23;}}try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");}catch(e){}}if(axo!=null){_23=new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));}}}return _23;};deconcept.PlayerVersion=function(_29){this.major=_29[0]!=null?parseInt(_29[0]):0;this.minor=_29[1]!=null?parseInt(_29[1]):0;this.rev=_29[2]!=null?parseInt(_29[2]):0;};deconcept.PlayerVersion.prototype.versionIsValid=function(fv){if(this.major<fv.major){return false;}if(this.major>fv.major){return true;}if(this.minor<fv.minor){return false;}if(this.minor>fv.minor){return true;}if(this.rev<fv.rev){return false;}return true;};deconcept.util={getRequestParameter:function(_2b){var q=document.location.search||document.location.hash;if(_2b==null){return q;}if(q){var _2d=q.substring(1).split("&");for(var i=0;i<_2d.length;i++){if(_2d[i].substring(0,_2d[i].indexOf("="))==_2b){return _2d[i].substring((_2d[i].indexOf("=")+1));}}}return "";}};deconcept.SWFObjectUtil.cleanupSWFs=function(){var _2f=document.getElementsByTagName("OBJECT");for(var i=_2f.length-1;i>=0;i--){_2f[i].style.display="none";for(var x in _2f[i]){if(typeof _2f[i][x]=="function"){_2f[i][x]=function(){};}}}};if(deconcept.SWFObject.doPrepUnload){deconcept.SWFObjectUtil.prepUnload=function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){};window.attachEvent("onunload",deconcept.SWFObjectUtil.cleanupSWFs);};window.attachEvent("onbeforeunload",deconcept.SWFObjectUtil.prepUnload);}if(Array.prototype.push==null){Array.prototype.push=function(_32){this[this.length]=_32;return this.length;};}if(!document.getElementById&&document.all){document.getElementById=function(id){return document.all["id"];};}var getQueryParamValue=deconcept.util.getRequestParameter;var FlashObject=deconcept.SWFObject;var SWFObject=deconcept.SWFObject;