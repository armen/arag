//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

var Log=new Class({log:function(){Log.logger.call(this,arguments);}});Log.logged=[];Log.logger=function(){if(window.console&&console.log){console.log.apply(console,arguments);
}else{Log.logged.push(arguments);}};