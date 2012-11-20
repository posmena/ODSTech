function ubud_getParameterByName( name )
{
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function ubud_getAlias()
{
if( document.location.hostname == 'www.bootybingo.com' )
	{
	return ubud_getBBAlias();
	}

	if( document.location.hostname == 'www.vampirebingo.com' )
	{
	return ubud_getVBAlias();
	}

}

function ubud_getSite()
{
if( document.location.hostname == 'www.bootybingo.com' )
	{
	return 'Booty';
	}

	if( document.location.hostname == 'www.vampirebingo.com' )
	{
	return 'Vampire';
	}

}


function ubud_getBBAlias()
{
var val = '';
$("li.hello span").each( function(i,x) {  
  val =  $(x).html();
});
return val;
}

function ubud_getVBAlias()
{
var val = '';

$("li.hello").each( function(i,x) {  
var str = $(x).html();
var y = str.split('<br>');
  val =  $.trim(y[1]);
});
return val;
}

function ubud_getBalance()
{
var val;

$("li.cb span").each( function(i,x) {  
  val = $(x).html();
});

return val;
}

function ubud_createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function ubud_readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function ubud_RecBal()
{
ubud_createCookie('bal',ubud_getBalance(),365);
}

function ubud_RecDep()
{
var newbal = ubud_getBalance();
var oldbal = ubud_readCookie('bal');

var img = new Image();
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'odst.co.uk/api/wdw/dep.php?';

src = src + 'site=' + ubud_getSite();
src = src + '&alias=' + ubud_getAlias();
src = src + '&utm_source=' + ubud_readCookie('utm_source');
src = src + '&utm_medium=' + ubud_readCookie('utm_medium');
src = src + '&utm_term=' + ubud_readCookie('utm_term');
src = src + '&utm_content=' + ubud_readCookie('utm_content');
src = src + '&utm_id=' + ubud_readCookie('utm_id');
src = src + '&oldbal=' + oldbal;
src = src + '&newbal=' + newbal;

img.src = src;
}

function ubud_RecReg()
{
var img = new Image();
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'odst.co.uk/api/wdw/reg.php?';

src = src + 'site=' + ubud_getSite();
src = src + '&alias=' + ubud_getAlias();
src = src + '&utm_source=' + ubud_readCookie('utm_source');
src = src + '&utm_medium=' + ubud_readCookie('utm_medium');
src = src + '&utm_term=' + ubud_readCookie('utm_term');
src = src + '&utm_content=' + ubud_readCookie('utm_content');
src = src + '&utm_id=' + ubud_readCookie('utm_id');

img.src = src;
}

function ubud_RecUTM()
{
if( ubud_getParameterByName('utm_source') != '' )
	{
	UTM2Cookie('utm_source');
	UTM2Cookie('utm_medium');
	UTM2Cookie('utm_term');
	UTM2Cookie('utm_content');
	UTM2Cookie('utm_campaign');
	UTM2Cookie('utm_id');	
	}
}

function UTM2Cookie(name)
{
ubud_createCookie(name,ubud_getParameterByName(name),365);
}