//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

MooTools.More={version:"1.2.3.1"};(function(){var a={language:"en-US",languages:{"en-US":{}},cascades:["en-US"]};var b;MooTools.lang=new Events();$extend(MooTools.lang,{setLanguage:function(c){if(!a.languages[c]){return this;
}a.language=c;this.load();this.fireEvent("langChange",c);return this;},load:function(){var c=this.cascade(this.getCurrentLanguage());b={};$each(c,function(e,d){b[d]=this.lambda(e);
},this);},getCurrentLanguage:function(){return a.language;},addLanguage:function(c){a.languages[c]=a.languages[c]||{};return this;},cascade:function(e){var c=(a.languages[e]||{}).cascades||[];
c.combine(a.cascades);c.erase(e).push(e);var d=c.map(function(f){return a.languages[f];},this);return $merge.apply(this,d);},lambda:function(c){(c||{}).get=function(e,d){return $lambda(c[e]).apply(this,$splat(d));
};return c;},get:function(e,d,c){if(b&&b[e]){return(d?b[e].get(d,c):b[e]);}},set:function(d,e,c){this.addLanguage(d);langData=a.languages[d];if(!langData[e]){langData[e]={};
}$extend(langData[e],c);if(d==this.getCurrentLanguage()){this.load();this.fireEvent("langChange",d);}return this;},list:function(){return Hash.getKeys(a.languages);
}});})();