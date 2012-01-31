var sbCookie={
setRaw:function(n,v,daysToExp,pg){
var ex='';
try{
if(daysToExp!=undefined){
var d=new Date();
d.setTime(d.getTime()+(86400000*parseFloat(daysToExp)));
ex=';expires='+d.toGMTString();}
}catch(e){}
if(pg!=undefined){if(pg!='.')ex+=';path='+pg;}
else {ex+=';path=/';}
return(document.cookie=escape(n)+'='+(v||'')+ex);
},
set:function(n,v,daysToExp,pg){
return this.setRaw(n,escape(v||''),daysToExp,pg);
},
get:function(n){
var c=document.cookie.match(new RegExp('(^|;)\\s*'+escape(n)+'=([^;\\s]*)'));
return(c?unescape(c[2]):null);
},
erase:function(n,pg){
var c=sbCookie.get(n)||true;
sbCookie.set(n,'',-1,pg);
return c;
},
accept:function(){
if(typeof(navigator.cookieEnabled)=='boolean'){return navigator.cookieEnabled;}
sbCookie.set('_t','1');return(sbCookie.erase('_t')==='1');
}
};
