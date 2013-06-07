function ubud_generateGuid()
{
var result, i, j;
result = '';
for(j=0; j<32; j++)
{
if( j == 8 || j == 12|| j == 16|| j == 20)
result = result + '-';
i = Math.floor(Math.random()*16).toString(16).toUpperCase();
result = result + i;
}
return result
} 

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
jQuery("li.hello span").each( function(i,x) {  
  val =  jQuery(x).html();
});
return val;
}

function ubud_getVBAlias()
{
var val = '';

jQuery("li.hello").each( function(i,x) {  
var str = jQuery(x).html();
var y = str.split('<br>');
  val =  jQuery.trim(y[1]);
});
return val;
}

function ubud_getBalance()
{
var val;

jQuery("li.cb span").each( function(i,x) {  
  val = jQuery(x).html();
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
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.odst.co.uk/api/wdw/dep.php?';

src = src + 'site=' + encodeURIComponent(ubud_getSite());
src = src + '&alias=' + encodeURIComponent(ubud_getAlias());
src = src + '&utm_source=' + encodeURIComponent(ubud_readCookie('utm_source'));
src = src + '&guid=' + encodeURIComponent(ubud_readCookie('guid'));
src = src + '&utm_medium=' + encodeURIComponent(ubud_readCookie('utm_medium'));
src = src + '&utm_term=' + encodeURIComponent(ubud_readCookie('utm_term'));
src = src + '&utm_content=' + encodeURIComponent(ubud_readCookie('utm_content'));
src = src + '&utm_id=' + encodeURIComponent(ubud_readCookie('utm_id'));
src = src + '&oldbal=' + encodeURIComponent(oldbal);
src = src + '&newbal=' + encodeURIComponent(newbal);
src = src + '&r=' + Math.floor(Math.random()*99999);
img.src = src;
}

function ubud_RecDupReg()
{
var err = false;

if( ubud_getSite() == 'Booty')
	{
	jQuery("div.myerror_msg").each( function(i,x) {  
	  if(jQuery(x).html() == "The entered email address already exists !")
		{
		err = true;
		}
	});
	}
else
	{
		jQuery("table.error").each( function(i,x) {  
	  if( jQuery(x).html().indexOf('Duplicate') > 0 )
		{
		err = true;
		}
	});
	}

if( err == true )
	{
	var img = new Image();
	var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.odst.co.uk/api/wdw/dup.php?';
	
	src = src + 'site=' + encodeURIComponent(ubud_getSite());
	src = src + '&alias=' + encodeURIComponent(jQuery('#alias').val());
	src = src + '&email=' + encodeURIComponent(jQuery('#email').val());
	src = src + '&guid=' + encodeURIComponent(ubud_readCookie('guid'));
	src = src + '&utm_source=' + encodeURIComponent(ubud_readCookie('utm_source'));
	src = src + '&utm_medium=' + encodeURIComponent(ubud_readCookie('utm_medium'));
	src = src + '&utm_term=' + encodeURIComponent(ubud_readCookie('utm_term'));
	src = src + '&utm_content=' + encodeURIComponent(ubud_readCookie('utm_content'));
	src = src + '&utm_id=' + encodeURIComponent(ubud_readCookie('utm_id'));
	src = src + '&r=' + Math.floor(Math.random()*99999);

	img.src = src;
	}

}

function ubud_RecReg()
{
if( ubud_getSite() == 'Vampire' )
{
return ubud_RecReg_V2();
}

var img = new Image();
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.odst.co.uk/api/wdw/reg.php?';

src = src + 'site=' + encodeURIComponent(ubud_getSite());
src = src + '&alias=' + encodeURIComponent(jQuery('#alias').val());
src = src + '&email=' + encodeURIComponent(jQuery('#email').val());
src = src + '&fname=' + encodeURIComponent(jQuery('#firstname').val());
src = src + '&lname=' + encodeURIComponent(jQuery('#lastname').val());
src = src + '&utm_source=' + encodeURIComponent(ubud_readCookie('utm_source'));
src = src + '&guid=' + encodeURIComponent(ubud_readCookie('guid'));
src = src + '&utm_medium=' + encodeURIComponent(ubud_readCookie('utm_medium'));
src = src + '&utm_term=' + encodeURIComponent(ubud_readCookie('utm_term'));
src = src + '&utm_content=' + encodeURIComponent(ubud_readCookie('utm_content'));
src = src + '&utm_id=' + encodeURIComponent(ubud_readCookie('utm_id'));
src = src + '&r=' + Math.floor(Math.random()*99999);

img.src = src;
document.body.appendChild( img );
}

function ubud_RecReg_V2()
{
var img = new Image();
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.odst.co.uk/api/wdw/reg.php?';

src = src + 'site=' + encodeURIComponent(ubud_getSite());
src = src + '&email=' + encodeURIComponent(jQuery('#edit-mail').val());
src = src + '&fname=' + encodeURIComponent(jQuery('#edit-first-name').val());
src = src + '&lname=' + encodeURIComponent(jQuery('#edit-last-name').val());
src = src + '&utm_source=' + encodeURIComponent(ubud_readCookie('utm_source'));
src = src + '&guid=' + encodeURIComponent(ubud_readCookie('guid'));
src = src + '&utm_medium=' + encodeURIComponent(ubud_readCookie('utm_medium'));
src = src + '&utm_term=' + encodeURIComponent(ubud_readCookie('utm_term'));
src = src + '&utm_content=' + encodeURIComponent(ubud_readCookie('utm_content'));
src = src + '&utm_id=' + encodeURIComponent(ubud_readCookie('utm_id'));
src = src + '&r=' + Math.floor(Math.random()*99999);

img.src = src;
document.body.appendChild( img );
}


function ubud_Track(ref)
{
ubud_RecUTM();

var img = new Image();
var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.odst.co.uk/api/wdw/track.php?';

src = src + 'site=' + encodeURIComponent(ubud_getSite());
src = src + '&alias=' + encodeURIComponent(ubud_getAlias());
src = src + '&email=' + encodeURIComponent(jQuery('#email').val());
src = src + '&fname=' + encodeURIComponent(jQuery('#firstname').val());
src = src + '&lname=' + encodeURIComponent(jQuery('#lastname').val());
src = src + '&utm_source=' + encodeURIComponent(ubud_readCookie('utm_source'));
src = src + '&guid=' + encodeURIComponent(ubud_readCookie('guid'));
src = src + '&utm_medium=' + encodeURIComponent(ubud_readCookie('utm_medium'));
src = src + '&utm_term=' + encodeURIComponent(ubud_readCookie('utm_term'));
src = src + '&utm_content=' + encodeURIComponent(ubud_readCookie('utm_content'));
src = src + '&utm_id=' + encodeURIComponent(ubud_readCookie('utm_id'));
src = src + '&page=' + encodeURIComponent(window.location.href) + '_ref=' + ref;
src = src + '&r=' + Math.floor(Math.random()*99999);

img.src = src;
document.body.appendChild( img );
}

function ubud_RecUTM()
{

	UTM2Cookie('utm_source');
	UTM2Cookie('utm_medium');
	UTM2Cookie('utm_term');
	UTM2Cookie('utm_content');
	UTM2Cookie('utm_campaign');
	UTM2Cookie('utm_id');	
	if( ubud_readCookie('guid') == null )
		{
		ubud_createCookie('guid',ubud_generateGuid());
		}

}

function UTM2Cookie(name)
{
if( ubud_readCookie(name) == null ||  ubud_readCookie(name) == "")
	{
	ubud_createCookie(name,ubud_getParameterByName(name),365);
	}
}


ubud_Track(0);