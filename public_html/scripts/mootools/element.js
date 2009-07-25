Element.implement({tidy:function(){this.set("value",this.get("value").tidy())},getTextInRange:function(b,a){return this.get("value").substring(b,a)},getSelectedText:function(){if(this.setSelectionRange){return this.getTextInRange(this.getSelectionStart(),this.getSelectionEnd())}return document.selection.createRange().text},getSelectedRange:function(){if($defined(this.selectionStart)){return{start:this.selectionStart,end:this.selectionEnd}}var e={start:0,end:0};var a=this.getDocument().selection.createRange();if(!a||a.parentElement()!=this){return e}var c=a.duplicate();if(this.type=="text"){e.start=0-c.moveStart("character",-100000);e.end=e.start+a.text.length}else{var b=this.get("value");var d=b.length-b.match(/[\n\r]*$/)[0].length;c.moveToElementText(this);c.setEndPoint("StartToEnd",a);e.end=d-c.text.length;c.setEndPoint("StartToStart",a);e.start=d-c.text.length}return e},getSelectionStart:function(){return this.getSelectedRange().start},getSelectionEnd:function(){return this.getSelectedRange().end},setCaretPosition:function(a){if(a=="end"){a=this.get("value").length}this.selectRange(a,a);return this},getCaretPosition:function(){return this.getSelectedRange().start},selectRange:function(e,a){if(this.setSelectionRange){this.focus();this.setSelectionRange(e,a)}else{var c=this.get("value");var d=c.substr(e,a-e).replace(/\r/g,"").length;e=c.substr(0,e).replace(/\r/g,"").length;var b=this.createTextRange();b.collapse(true);b.moveEnd("character",e+d);b.moveStart("character",e);b.select()}return this},insertAtCursor:function(b,a){var d=this.getSelectedRange();var c=this.get("value");this.set("value",c.substring(0,d.start)+b+c.substring(d.end,c.length));if($pick(a,true)){this.selectRange(d.start,d.start+b.length)}else{this.setCaretPosition(d.start+b.length)}return this},insertAroundCursor:function(b,a){b=$extend({before:"",defaultMiddle:"",after:""},b);var c=this.getSelectedText()||b.defaultMiddle;var g=this.getSelectedRange();var f=this.get("value");if(g.start==g.end){this.set("value",f.substring(0,g.start)+b.before+c+b.after+f.substring(g.end,f.length));this.selectRange(g.start+b.before.length,g.end+b.before.length+c.length)}else{var d=f.substring(g.start,g.end);this.set("value",f.substring(0,g.start)+b.before+d+b.after+f.substring(g.end,f.length));var e=g.start+b.before.length;if($pick(a,true)){this.selectRange(e,e+d.length)}else{this.setCaretPosition(e+f.length)}}return this}});Element.implement({measure:function(e){var g=function(h){return !!(!h||h.offsetHeight||h.offsetWidth)};if(g(this)){return e.apply(this)}var d=this.getParent(),b=[],f=[];while(!g(d)&&d!=document.body){b.push(d.expose());d=d.getParent()}var c=this.expose();var a=e.apply(this);c();b.each(function(h){h()});return a},expose:function(){if(this.getStyle("display")!="none"){return $empty}var a=this.style.cssText;this.setStyles({display:"block",position:"absolute",visibility:"hidden"});return function(){this.style.cssText=a}.bind(this)},getDimensions:function(a){a=$merge({computeSize:false},a);var d={};var c=function(f,e){return(e.computeSize)?f.getComputedSize(e):f.getSize()};if(this.getStyle("display")=="none"){d=this.measure(function(){return c(this,a)})}else{try{d=c(this,a)}catch(b){}}return $chk(d.x)?$extend(d,{width:d.x,height:d.y}):$extend(d,{x:d.width,y:d.height})},getComputedSize:function(a){a=$merge({styles:["padding","border"],plains:{height:["top","bottom"],width:["left","right"]},mode:"both"},a);var c={width:0,height:0};switch(a.mode){case"vertical":delete c.width;delete a.plains.width;break;case"horizontal":delete c.height;delete a.plains.height;break}var b=[];$each(a.plains,function(g,f){g.each(function(h){a.styles.each(function(i){b.push((i=="border")?i+"-"+h+"-width":i+"-"+h)})})});var e={};b.each(function(f){e[f]=this.getComputedStyle(f)},this);var d=[];$each(a.plains,function(g,f){var h=f.capitalize();c["total"+h]=0;c["computed"+h]=0;g.each(function(i){c["computed"+i.capitalize()]=0;b.each(function(k,j){if(k.test(i)){e[k]=e[k].toInt()||0;c["total"+h]=c["total"+h]+e[k];c["computed"+i.capitalize()]=c["computed"+i.capitalize()]+e[k]}if(k.test(i)&&f!=k&&(k.test("border")||k.test("padding"))&&!d.contains(k)){d.push(k);c["computed"+h]=c["computed"+h]-e[k]}})})});["Width","Height"].each(function(g){var f=g.toLowerCase();if(!$chk(c[f])){return}c[f]=c[f]+this["offset"+g]+c["computed"+g];c["total"+g]=c[f]+c["total"+g];delete c["computed"+g]},this);return $extend(e,c)}});(function(){var a=false;window.addEvent("domready",function(){var b=new Element("div").setStyles({position:"fixed",top:0,right:0}).inject(document.body);a=(b.offsetTop===0);b.dispose()});Element.implement({pin:function(c){if(this.getStyle("display")=="none"){return null}var d;if(c!==false){d=this.getPosition();if(!this.retrieve("pinned")){var f={top:d.y-window.getScroll().y,left:d.x-window.getScroll().x};if(a){this.setStyle("position","fixed").setStyles(f)}else{this.store("pinnedByJS",true);this.setStyles({position:"absolute",top:d.y,left:d.x});this.store("scrollFixer",(function(){if(this.retrieve("pinned")){this.setStyles({top:f.top.toInt()+window.getScroll().y,left:f.left.toInt()+window.getScroll().x})}}).bind(this));window.addEvent("scroll",this.retrieve("scrollFixer"))}this.store("pinned",true)}}else{var e;if(!Browser.Engine.trident){if(this.getParent().getComputedStyle("position")!="static"){e=this.getParent()}else{e=this.getParent().getOffsetParent()}}d=this.getPosition(e);this.store("pinned",false);var b;if(a&&!this.retrieve("pinnedByJS")){b={top:d.y+window.getScroll().y,left:d.x+window.getScroll().x}}else{this.store("pinnedByJS",false);window.removeEvent("scroll",this.retrieve("scrollFixer"));b={top:d.y,left:d.x}}this.setStyles($merge(b,{position:"absolute"}))}return this.addClass("isPinned")},unpin:function(){return this.pin(false).removeClass("isPinned")},togglepin:function(){this.pin(!this.retrieve("pinned"))}})})();(function(){var a=Element.prototype.position;Element.implement({position:function(r){if(r&&($defined(r.x)||$defined(r.y))){return a?a.apply(this,arguments):this}$each(r||{},function(t,s){if(!$defined(t)){delete r[s]}});r=$merge({relativeTo:document.body,position:{x:"center",y:"center"},edge:false,offset:{x:0,y:0},returnPos:false,relFixedPosition:false,ignoreMargins:false,allowNegative:false},r);var b={x:0,y:0};var h=false;var c=this.measure(function(){return document.id(this.getOffsetParent())});if(c&&c!=this.getDocument().body){b=c.measure(function(){return this.getPosition()});h=true;r.offset.x=r.offset.x-b.x;r.offset.y=r.offset.y-b.y}var q=function(s){if($type(s)!="string"){return s}s=s.toLowerCase();var t={};if(s.test("left")){t.x="left"}else{if(s.test("right")){t.x="right"}else{t.x="center"}}if(s.test("upper")||s.test("top")){t.y="top"}else{if(s.test("bottom")){t.y="bottom"}else{t.y="center"}}return t};r.edge=q(r.edge);r.position=q(r.position);if(!r.edge){if(r.position.x=="center"&&r.position.y=="center"){r.edge={x:"center",y:"center"}}else{r.edge={x:"left",y:"top"}}}this.setStyle("position","absolute");var p=document.id(r.relativeTo)||document.body;var i=p==document.body?window.getScroll():p.getPosition();var o=i.y;var g=i.x;if(Browser.Engine.trident){var l=p.getScrolls();o+=l.y;g+=l.x}var j=this.getDimensions({computeSize:true,styles:["padding","border","margin"]});if(r.ignoreMargins){r.offset.x=r.offset.x-j["margin-left"];r.offset.y=r.offset.y-j["margin-top"]}var n={};var d=r.offset.y;var e=r.offset.x;var k=window.getSize();switch(r.position.x){case"left":n.x=g+e;break;case"right":n.x=g+e+p.offsetWidth;break;default:n.x=g+((p==document.body?k.x:p.offsetWidth)/2)+e;break}switch(r.position.y){case"top":n.y=o+d;break;case"bottom":n.y=o+d+p.offsetHeight;break;default:n.y=o+((p==document.body?k.y:p.offsetHeight)/2)+d;break}if(r.edge){var m={};switch(r.edge.x){case"left":m.x=0;break;case"right":m.x=-j.x-j.computedRight-j.computedLeft;break;default:m.x=-(j.x/2);break}switch(r.edge.y){case"top":m.y=0;break;case"bottom":m.y=-j.y-j.computedTop-j.computedBottom;break;default:m.y=-(j.y/2);break}n.x=n.x+m.x;n.y=n.y+m.y}n={left:((n.x>=0||h||r.allowNegative)?n.x:0).toInt(),top:((n.y>=0||h||r.allowNegative)?n.y:0).toInt()};if(p.getStyle("position")=="fixed"||r.relFixedPosition){var f=window.getScroll();n.top=n.top.toInt()+f.y;n.left=n.left.toInt()+f.x}if(r.returnPos){return n}else{this.setStyles(n)}return this}})})();Element.implement({isDisplayed:function(){return this.getStyle("display")!="none"},toggle:function(){return this[this.isDisplayed()?"hide":"show"]()},hide:function(){var b;try{if("none"!=this.getStyle("display")){b=this.getStyle("display")}}catch(a){}return this.store("originalDisplay",b||"block").setStyle("display","none")},show:function(a){return this.setStyle("display",a||this.retrieve("originalDisplay")||"block")},swapClass:function(a,b){return this.removeClass(a).addClass(b)}});