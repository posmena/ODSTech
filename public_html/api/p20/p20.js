 
var BrowserDetect = function(){}

BrowserDetect.proptype = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera",
			versionSearch: "Version"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};

 
 var ODST_P20 = function(){}
 
 ODST_P20.prototype = {
    options           : {},
	display : function(me,ssl)
	{
	// for each p20 div call jsonP with params
	var divs = this.getElementsByClassNameX("odst_p20");
	for( i=0; i<divs.length; i++)
		{
		//get parameters from div attributes
		var client = divs[i].getAttribute('data-client');
		var displayType = divs[i].getAttribute('data-type');
		var width = divs[i].getAttribute('data-width');
		var maxproducts = divs[i].getAttribute('data-max');
		var params = divs[i].getAttribute('data-params');
		var colors = divs[i].getAttribute('data-colors');
		var publisher_id = divs[i].getAttribute('data-id');

		
		var style = "";
		style = divs[i].getAttribute('data-style');
		if( params == null ) params = "";
		if( colors == null ) colors = "";
		
		colors = colors.replace(/#/g,"%23");
		
		if( style != null ) 
			{
			if( style != "" )	
				{
				if( style != "default" )
					{
					var fileref=document.createElement("link")
						fileref.setAttribute("rel", "stylesheet")
						fileref.setAttribute("type", "text/css")
						fileref.setAttribute("href", "http://odst.co.uk/api/p20/css/" + style + ".css")
				  
						document.getElementsByTagName('head')[0].appendChild(fileref);
		
					}
				}
			}
			
		// if  need non default styles then add
		
		me.callTheJsonp(i,client,displayType,width,params,colors,maxproducts,style,publisher_id,ssl);
		}
	},
	showContentUnits : function(id,ssl = false)
	{		
		var Me = this;
		this.browserDetect = new BrowserDetect;
		if(document.addEventListener)
			{
			//document.addEventListener('DOMContentLoaded',Me.display(Me),false);
			}
			else if(document.attachEvent)
				{
				//document.attachEvent('onreadystatechange',Me.display(Me));
				}
			if(this.browserDetect.browser == 'Explorer' && window===top)
			(function()
			{
			try
			{document.documentElement.doScroll('left');}
			catch(error){setTimeout(arguments.callee,0);return;}
			this.display(this,ssl);})();

			var oldonload=window.onload;
			window.onload=function(){
			Me.display(Me,ssl);
			if(oldonload)
			if(typeof oldonload=='string'){eval(oldonload);}
			else oldonload();};
			
			var fileref=document.createElement("link")
				  fileref.setAttribute("rel", "stylesheet")
				  fileref.setAttribute("type", "text/css")
				  fileref.setAttribute("href", "http://odst.co.uk/api/p20/css/odst_carousel.css")
				  
			document.getElementsByTagName('head')[0].appendChild(fileref);
 
    },		
    callTheJsonp : function(index,client,displayType,width,params,colors,maxproducts,style,publisher_id,ssl)
            {
                // the url of the script where we send the asynchronous call
				 var url;
				if( ssl == true )
					{
				  url = "https://odst.co.uk/api/p20/p20.php?user=" + publisher_id + "&params[feed_id]=" + client + "&" + params + "&" + colors + "&type=" + displayType + "&width=" + width  + "&max=" + maxproducts + "&style=" + style + "&callback=ODST_P20.parseRequest&index=" + index + "&rand=23ss4322&ssl=1";
				 }
				else
					{
					url = "http://s.odst.co.uk/api/p20/p20.php?user=" + publisher_id + "&params[feed_id]=" + client + "&" + params + "&" + colors + "&type=" + displayType + "&width=" + width  + "&max=" + maxproducts + "&style=" + style + "&callback=ODST_P20.parseRequest&index=" + index + "&rand=23ss4322";		
					}
					
				// create a new script element
                var script = document.createElement('script');
                // set the src attribute to that url
                script.setAttribute('src', url);
                // insert the script in out page
                document.getElementsByTagName('head')[0].appendChild(script);
				
            },
 
            // this function should parse responses.. you can do anything you need..
            // you can make it general so it would parse all the responses the page receives based on a response field
            parseRequest: function(response,index)
            {
			
                try // try to output this to the javascript console
                {
				
				   this.getElementsByClassNameX("odst_p20")[index].innerHTML = response;
                }
                catch(an_exception) // alert for the users that don't have a javascript console
                {
					alert(an_exception);
                    //alert('product id ' + response.item_id + ': quantity = ' + response.quantity + ' & price = ' + response.price);
                }
            },
 
 
  getElementsByClassNameX : function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
}


}


var ODST_P20 = new ODST_P20();