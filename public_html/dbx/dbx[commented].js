// DBX3.0 :: Docking Boxes (dbx)
// *****************************************************
// DOM scripting by brothercake -- http://www.brothercake.com/
// GNU Lesser General Public License -- http://www.gnu.org/licenses/lgpl.html
//******************************************************


//global dbx manager reference
var dbx;

//docking boxes manager
function dbxManager(sid, useid, hide, buttontype)
{
	//global reference to this
	dbx = this;

	//throw exception and stop if the session ID isn't valid
	if(!/^[-_a-z0-9]+$/i.test(sid)) { throw('Error from dbxManager:\n"' + sid + '" is an invalid session ID'); return; }

	//identify some browsers for a few little things 
	this.kde = navigator.vendor == 'KDE';
	this.safari = navigator.vendor == 'Apple Computer, Inc.';
	this.chrome = navigator.vendor == 'Google Inc.';
	this.opera = typeof window.opera != 'undefined';
	this.msie = typeof document.uniqueID != 'undefined';
	
	//whether a browser is supported
	//we'll do tests here to set the flag and then re-use it in the dbxGroup constructor
	//so this browser isn't supported if the '*' collection isn't supported,
	//or if this is konqueror earlier than 3.2, or opera earlier than 8
	//(we need the '*' collection so the dbx-box element can be anything)
	//this affects win/ie5, and safari builds earlier than 1.2
	//but we needed to cut out older safari builds anyway, because
	//they don't support the absolute-in-relative contextual positioning
	//that we need to make the clone positioning work)
	this.supported = (!(
		typeof document.getElementsByTagName == 'undefined'
		|| document.getElementsByTagName('*').length == 0
		|| (this.kde && typeof window.sidebar == 'undefined')
		|| (this.opera && parseFloat(navigator.userAgent.toLowerCase().split(/opera[\/ ]/)[1].split(' ')[0], 10) < 8)
		));

	//don't continue if unsupported
	if(!this.supported) { return; }



	//identify supported method of adding encapsulated event listeners
	this.etype = typeof document.addEventListener != 'undefined' 
		? 'addEventListener' 
		: typeof document.attachEvent != 'undefined' 
			? 'attachEvent' 
			: 'none';

	//if encapsulated event listening is not supported,
	//set flag to unsupported and don't continue
	//in practise this will filter-out mac/ie5
	if(this.etype == 'none') { this.supported = false; return; }

	//set event name prefix
	this.eprefix = (this.etype == 'attachEvent' ? 'on' : '');


	//the session id is used as part of the cookie name
	//so that one cookie can store multiple groups
	//but single or multiple groups on different pages are discreet
	//so that they don't wipe out each other's cookies
	this.sid = sid;

	//so using that, define the core cookie name
	this.cookiename = 'dbx-' + this.sid + '=';

	//if the useid value is undefined use the default value of false, otherwise save the value
	//this controls whether box-ID based dynamic groups are enabled
	//which is whether to use the box element's ID as its dbxid, or whether to generate one dynamically
	//there needs to be a switch because if it's true it validates the ID and throws failure
	//so this way it's backward compatible, because v2 ignored IDs completely
	this.useid = typeof useid != 'undefined' && useid == 'yes' ? true : false;

	//if the hide source box while dragging value is undefined,
	//use the default value of true, otherwise save the value
	this.hide = typeof hide != 'undefined' && hide == 'no' ? false : true;

	//if the toggle button element type is undefined,
	//use the default value of 'link', otherwise save the value
	//the default of 'link' is for backward compatibility
	//even though 'button' is the optimum value for best overall accessibility
	this.buttontype = typeof buttontype != 'undefined' && buttontype == 'button' ? 'button' : 'link';


	//count the number of simultaneous running timers, so that we can limit it
	//the limit will be set the same as the number of boxes in a given group
	//so that it is still possible for all of them to be animated simultaneously
	//but not for multiple sets to be going at the same time
	//the bottom-line reason for this is to improve efficiency, or more specifically,
	//to reduce the possibility of very severe inefficiency leading to device slowdown
	this.running = 0;


	//for each named group we also want to assign it a numeric id
	//which will become part of the cookie value
	this.gnumbers = {};


	//define the maximum number of boxes in a group
	//which is 0 by default, meaning no limit
	//any other number will be that maximum
	this.max = 0;


	//and for the same purpose we need to save the root cookiestate right now
	//before we've done any changes or modification that would re-save it
	//this is so we can avoid removing boxes that are incidental to your management
	//ie, the box is there but not present in group cookie, and would otherwise be removed
	//so we check that it's present somewhere in this root cookie value, otherwise ignore it
	this.rootcookie = '';
	if(document.cookie && document.cookie.indexOf(this.cookiename) != -1)
	{
		//extract only the part that relates to this dbx session
		//to avoid getting false matches on values in other cookies
		//that just happen to have the same string pattern!
		this.rootcookie = document.cookie.split(this.cookiename)[1].split(';')[0];

		//convert to internal format
		this.rootcookie = this.rootcookie.replace(/\|/g, ',').replace(/:/g, '=');

		//restore plus symbols only if they're not already there in the value
		//so that we retain backward compatibility with before they were removed
		if(this.rootcookie.indexOf('+') == -1)
		{
			this.rootcookie = this.rootcookie.replace(/(,|$|&)/ig, '+$1').replace(/(\-\+)/g, '-');
		}
	}


	//create an object for storing save data
	//(order and open-state of boxes, to save to cookie)
	this.savedata = {};

	//look for existing cookie state data
	this.cookiestate = this.getCookieState();
};




//set state to cookie and output to receiver method
dbxManager.prototype.setCookieState = function()
{
	//format expiry date
	var now = new Date();
	now.setTime(now.getTime() + (365*24*60*60*1000));

	//compile the save object data into state string for onstatechange event
	this.compileStateString();

	//convert the format to netscape compatible cookie string,
	//(http://wp.netscape.com/newsref/std/cookie_spec.html
	// which says that the data can't contain commas; even though the RFC allows it,
	// we may as well make it fully compatible to avoid issues like this:
	// http://trac.wordpress.org/ticket/2660)
	//at the same time I've changed = for : to avoid ambiguity there as well
	//and removed plus symbols to save space
	//but we're only doing this at the last minute, and not overwriting this.state
	//so that the internal format isn't affected
	//and so that it remains backwardly compatible with earlier versions
	this.cookiestring = this.state.replace(/,/g, '|').replace(/=/g, ':').replace(/\+/g, '');

	//call the onstatechange method
	//continue to save the cookie, if
	//	the method doesn't exist; or
	//	the method exists and returns true
	if(typeof this.onstatechange == 'undefined' || this.onstatechange())
	{
		//create the cookie
		document.cookie = this.cookiename
			+ this.cookiestring
			+ '; expires=' + now.toGMTString()
			+ '; path=/';
	}
};


//get state from cookie
dbxManager.prototype.getCookieState = function()
{
	//set null reference so we always have something to return
	this.cookiestate = null;

	//if we have a cookie
	if(document.cookie)
	{
		//if it's our cookie
		if(document.cookie.indexOf(this.cookiename) != -1)
		{
			//extract order data
			this.cookie = document.cookie.split(this.cookiename)[1].split(';')[0].split('&');

			//iterate through resulting data
			for(var i in this.cookie)
			{
				//ignore unwanted properties
				if(this.unwanted(this.cookie, i)) { continue; }

				//convert the format back to internal format
				this.cookie[i] = this.cookie[i].replace(/\|/g, ',');
				this.cookie[i] = this.cookie[i].replace(/:/g, '=');

				//restore plus symbols only if they're not already there in the value
				//so that we retain backward compatibility with before they were removed
				if(this.cookie[i].indexOf('+') == -1)
				{
					this.cookie[i] = this.cookie[i].replace(/(,|$|&)/ig, '+$1').replace(/(\-\+)/g, '-');
				}

				//split into key and value
				this.cookie[i] = this.cookie[i].split('=');

				//split value (which is comma-delimited) into an array
				this.cookie[i][1] = this.cookie[i][1].split(',');
			}

			//copy the cookie data into a state object
			//so that when a dbx group is instantiated
			//we can test whether there's existing cookie data for it
			//using typeof this.cookiestate['container-id']
			this.cookiestate = {};
			for(i in this.cookie)
			{
				//ignore unwanted properties
				if(this.unwanted(this.cookie, i)) { continue; }

				//create member
				this.cookiestate[this.cookie[i][0]] = this.cookie[i][1];
			}
		}
	}

	return this.cookiestate;
};


//compile the state string for for onstatechange event
dbxManager.prototype.compileStateString = function()
{
	//compile the save object data into a string
	var str = '';
	for(var j in this.savedata)
	{
		//ignore unwanted properties
		if(this.unwanted(this.savedata, j)) { continue; }

		//add to string
		str += j + '=' + this.savedata[j] + '&'
	}

	//trim off the last stray ampersand
	//and save the string to this.state
	//so that the data is available from onstatechange
	this.state = str.replace(/^(.+)&$/, '$1');
};


//create an HTML element
dbxManager.prototype.createElement = function(tag)
{
	//create element using supported method and return it
	return typeof document.createElementNS != 'undefined' 
		? document.createElementNS('http://www.w3.org/1999/xhtml', tag) 
		: document.createElement(tag);
};


//get an element identified by classname
//by upwards iteration from event target
dbxManager.prototype.getTarget = function(e, pattern, node)
{
	//if we have an explicit node reference, use that
	if(typeof node != 'undefined')
	{
		var target = node;
	}
	//otherwise store a reference to the target node
	else
	{
		target = typeof e.target != 'undefined' ? e.target : e.srcElement;
	}

	//[if we don't already have the element we want]
	//iterate upwards from the target
	//until we find a match with the specified element
	while(!this.hasClass(target, pattern))
	{
		target = target.parentNode;
		
		//if we reach a group container and it's still not the right target 
		//we'll have to abandon the search and return null
		if(this.hasClass(target, 'dbx\-group') && !this.hasClass(target, pattern))
		{
			return null;
		}
	}

	//return the element
	return target;
};


//extract the dbxid from an element classname
dbxManager.prototype.getID = function(element)
{
	//if the element is null or doesn't have a classname, return null for failure
	if(!element || !element.className) { return null; }

	//or if it's a dummy return "dummy"
	else if(this.hasClass(element, 'dbx\-dummy')) { return 'dummy'; }

	//get the element classname and split it by dbxid
	var cname = element.className.split('dbxid-');

	//if we only have a single member then this element doesn't have a dbxid
	//so in that case return null for failure
	if(cname.length == 1) { return null; }

	//otherwise return the second member
	//parsed of any additional classname values
	return cname[1].replace(/^([a-zA-Z0-9_]+).*$/, '$1');
};


//find a sibling box from a given box
//with a fallback to reselect the original box
//in case we run out of nodes in that direction
dbxManager.prototype.getSiblingBox = function(root, sibling)
{
	var node = root[sibling];
	while(node && !this.hasClass(node, 'dbx\-box'))
	{
		node = node[sibling];
	}
	if(!node) { node = root; }

	return node;
};


//get the position of an object with respect to the canvas
//this is needed for moving objects between groups
//but it's here in the core script just in case it should be needed in future
dbxManager.prototype.getPosition = function(obj, center)
{
	var position = { 'left' : obj.offsetLeft, 'top' : obj.offsetTop };
	var tmp = obj.offsetParent;
	while(tmp)
	{
		position.left += tmp.offsetLeft;
		position.top += tmp.offsetTop;
		tmp = tmp.offsetParent;
	}

	//if we're returning a center point
	//add half the object's width and height
	if(center)
	{
		position.left += obj.offsetWidth / 2;
		position.top += obj.offsetHeight / 2;
	}

	return position;
};


//get the viewport width using the various models in use by different browsers and rendering modes
//this is used to keep script-generated tooltips inside the window
//we're only repositioning in one direction here, to save code space
//because horizontal positioning is critical since pages typically don't scroll that way
//and therefore there's no easy way to get to an obscured portion
//but vertical positioning less so, since they do and you can therefore scroll to see it
dbxManager.prototype.getViewportWidth = function()
{
	return typeof window.innerWidth != 'undefined'
		? window.innerWidth
		: (typeof document.documentElement != 'undefined'
			&& typeof document.documentElement.clientWidth != 'undefined'
			&& document.documentElement.clientWidth != 0)
			? document.documentElement.clientWidth
			: this.get('body')[0].clientWidth;
};


//compile the data for, and dispatch, any onbeforestatechange function that exists
dbxManager.prototype.compileAndDispatchOnBeforeStateChange = function()
{
	//create an actions object
	var actions = {};

	//for each of the argument arrays
	for(var i=0; i<arguments.length; i++)
	{
		//create a shortcut reference to this data array
		var data = arguments[i];

		//store the values to dbx properties
		this.dbxobject = data[1];
		this.group = data[2];
		this.gid = data[3];
		this.sourcebox = data[4];
		this.target = data[5];
		this.action = data[6];

		//call the function and store the return value to actions object
		actions[data[0]] = this.onbeforestatechange();
	}

	//return the actions object
	return actions;
};


//compile the data for and fire onanimate event
dbxManager.prototype.compileAndDispatchOnAnimate = function(box, clone, caller, count, res)
{
	//store the necessary properties to the manager object
	//then fire the function
	dbx.sourcebox = box;
	dbx.clonebox = clone;
	dbx.dbxobject = caller;
	dbx.group = caller.container;
	dbx.anicount = count - 1;
	dbx.anilength = res - 1;
	dbx.onanimate();
};


//compile the data for and fire onafteranimate event
dbxManager.prototype.compileAndDispatchOnAfterAnimate = function(box, caller)
{
	//store the necessary properties to the manager object
	//then fire the function
	dbx.sourcebox = box;
	dbx.dbxobject = caller;
	dbx.group = caller.container;
	dbx.onafteranimate();
};


//check whether an enumerable property of an object is unwanted during iteration
//this is uses in for..in iterators to weed out external prototypes
//and any other members deemed generally unwanted while iterating through collections
dbxManager.prototype.unwanted = function(obj, i)
{
	//we don't want external members, function members, 
	//undefined members, or members called "length"
	return (!obj.hasOwnProperty(i) || typeof obj[i] == 'undefined' 
		|| typeof obj[i] == 'function' || i == 'length');
};


//add an event listener
dbxManager.prototype.addEvent = function(node, type, handler)
{
	node[this.etype](this.eprefix + type, handler, false);
};


//trim leading and trailing whitespace from a string
dbxManager.prototype.trim = function(str)
{
	return str.replace(/^\s+|\s+$/g,"");
};


//similar to PHP's empty() function, but more strongly typeed
dbxManager.prototype.empty = function(data)
{
	if(typeof data == 'string' && this.trim(data) === '') { return true; }
	else if(typeof data == 'object')
	{
		if(data instanceof Array && data.length == 0) { return true; }
		else
		{
			var n = 0;
			for(var i in data)
			{
				if(!data.hasOwnProperty(i)) { continue; }
				n++;
			}
			if(n == 0) { return true; }
		}
	}
	return false;
};


//does an element have a particular class name (as a boundaried substring)
dbxManager.prototype.hasClass = function(element, pattern)
{
	return (element.className && new RegExp(pattern + '($|[ ])').test(element.className));
};

//remove a value pattern from an element's class name
dbxManager.prototype.removeClass = function(element, pattern)
{
	if(typeof flags == 'undefined')
	{
		flags = '';
	}
	element.className = element.className.replace(new RegExp(pattern, 'g'), '');
	if(!/\S/.test(element.className))
	{
		element.className = '';
	}
	return element;
};

//it's a simple dollar function
dbxManager.prototype.get = function(find, context)
{
	var nodes = [];
	
	//context.[sibling or child]
	if(/(previous|next|first|last)(Sibling|Child)/.test(find))
	{
		context = context[find];
		switch(find)
		{
			case 'nextSibling' :
			case 'previousSibling' :
			
				while(context && context.nodeType != 1)
				{
					context = context[find];
				}
				break;
		}
		return context;
	}
	
	//document.getElementById
	else if(find.indexOf('#') != -1)
	{
		return document.getElementById(find.split('#')[1]);
	}
	
	//[document | context].getElementsByTagName
	else 
	{
		if(typeof context == 'undefined') { context = document; }
		return context.getElementsByTagName(find);
	}
};









//create new docking boxes group
function dbxGroup()
{
	//don't continue if the script is unsupported
	if(!dbx.supported) { return; }

	//store the arguments collection (we have to do this because
	//Safari doesn't allow writing back to arguments collection)
	var args = arguments;

	//throw exception and stop if the container ID isn't valid
	if(!/^[-_a-z0-9]+$/i.test(args[0]) || args[0] == 'deleted') { throw('Error from dbxGroup:\n"' + args[0] + '" is an invalid container ID'); return; }

	//group container
	this.container = dbx.get('#' + args[0]);
	
	//warn (throw and stop) if the container doesn't have the dbx-group class name
	if(!dbx.hasClass(this.container, 'dbx\-group')) { throw('Error from dbxGroup:\nGroup container (the element with id="' + args[0] + '") must contain the class name "dbx-group"'); return; }

	//don't continue if the container doesn't exist
	//silently fail because this could happen in the wild even if the script is properly configured
	//(eg. when rendering or loading issues create an incomplete DOM)
	//and we don't want end users to see error alerts
	if(!this.container) { return; }

	//group id
	this.gid = args[0];

	//create transient elements for key dynamic classes
	//so that if those classes contain any image backgrounds
	//those images will be preloaded
	this.cacheDynamicClasses();


	//if an onbeforestatechange function is defined
	if(typeof dbx.onbeforestatechange != 'undefined')
	{
		//compile the data for, and dispatch, any onbeforestatechange function that exists
		//and store the result to actions object
		var actions = dbx.compileAndDispatchOnBeforeStateChange(
			['proceed', this, this.container, this.gid, null, null, 'load']);

		//if we're not okay to proceed, don't continue
		//but first set the container reference back to null
		//so that this group shows as non-instantiated
		if(!actions.proceed)
		{
			this.container = null;
			return;
		}
	}


	//container orientation, defaults to freeform
	this.orientation = /^(freeform|confirm|horizontal|vertical|insert(\-swap|\-insert)?)/.test(args[1]) ? args[1] : 'freeform';

	//if the value is just "insert" that means "freeform-insert"
	if(this.orientation == 'insert') { this.orientation = 'freeform-insert'; }

	//exchange mode is swap by default, meaning that the box you're holding and the insertion 
	//box are swapped over; the alternative mode is "insert", where everything from 
	//the insert block to the end is shifted along by one
	this.exchange = 'swap';
	
	//if the orientation contains insert, then exchange mode is insert
	//and orientation is just the first value (see next expression)
	if(/(freeform|confirm)\-insert/.test(this.orientation))
	{
		this.exchange = 'insert';
	}
	
	//if it contains any additional value, remove it
	//this finishes the task above, and is also for general safety
	this.orientation = this.orientation.split('-')[0];
	
	//confirm orientation is freeform orientation,
	//but with a confirmation dialog before swapping
	//so if that's the value, set confirm mode and reset orientation
	this.confirm = false;
	if(this.orientation == 'confirm')
	{
		this.confirm = true;
		this.orientation = 'freeform';
	}
	
	
	//drag threshold - how far the cursor must move before the drag registers
	this.threshold = parseInt(args[2], 10);
	if(isNaN(this.threshold)) { this.threshold = 0; }

	//restrict drag movement to container axis
	//store this value the same as the container orientation
	//(if it's "yes", because we need to know the restriction axis)
	//otherwise set it to an empty string (if it's "no" or invalid)
	this.restrict = args[3] == 'yes' ? this.orientation : '';
	

	//if the resolution is zero, set it to 1
	//this avoids having to have a no-animation condition
	//and visually appears the same to anyone's eye
	this.resolution = parseInt(args[4], 10);
	if(isNaN(this.resolution)) { this.resolution = 0; }
	if(this.resolution == 0) { this.resolution = 1; }

	//include open/close toggles
	this.toggles = args[5] == 'yes';

	//is the box open by default
	this.defopen = args[6] != 'closed';

	//vocabulary
	this.vocab = {
		'open' : args[7],
		'close' : args[8],
		'move' : args[9],
		'toggle' : args[10],
		'kmove' : args[11],
		'ktoggle' : args[12],
		'syntax' : args[13],
		//we need to check the last two against undefined
		//because they didn't exist in version 2
		'kyes' : (typeof args[14] != 'undefined' ? args[14] : ''),
		'kno' : (typeof args[15] != 'undefined' ? args[15] : '')
		};


	//reference to this
	var self = this;

	//drag ok flag
	this.dragok = false;

	//initially null reference to target box
	this.box = null;

	//initially null reference to dialog element
	//which is a specially styled clone for tracking and highlighting movement selections
	this.dialog = null;

	//buffering timer for dialog animation
	this.buffer = null;

	//define an object for storing the object that's just moved and its direction
	//so that we can track it to prevent multiple movements of the same object as necessary (in freeform orientation)
	//or for working out the position comparisons (in linear orientations)
	this.last = {
		'box' : null,
		'direction' : null
		};
		
	//store the box elements which are first-child and last-child at any given point
	//this is used to implement dynamic first-child and last-child classes
	//[CSS last-child won't work because the dummy is actually the last child]
	this.child = {
		'first' : null, 
		'last' : null
		};

	//properties for tracking multiple keypress
	//that indicate diagonal movement
	this.keytimer = null;
	this.currentdir = null;

	//the rules object stores global rules, and any subset rules (indexed by class name)
	//that specifies how all or those objects are allowed to move
	//each of those objects stores a pointer for tracking patterns, and an array of moves
	//a single move is just a directional limit, while multiple moves track patterns
	this.rules = { 'global' : { 'pointer' : 0, 'rule' : [], 'actual' : [] } };

	//we'll also need to remember when we test a rule,
	//what its key (eg 'global') is, and the last direction to save to moves array
	this.rulekey = '';
	this.ruledir = '';


	//re-inforce relative positioning and block display on group container
	//the container must have positioning,
	//because many other calculations depend on it
	//I originally supported static positioning as well
	//but this solves some browser clone positioning quirks
	//which I otherwise couldn't fix cleanly
	this.container.style.position = 'relative';
	this.container.style.display = 'block';


	//initialise the boxes in this group
	//and recover any saved state and sparetokens
	this.initBoxes(true, true);


	//the keydown flag is used to tell key-initiated focus events from mouse-initiated focus events
	this.keydown = false;

	//the mousedown flag is an inverse of that, used as a backup 
	//to compensate for a crack in konqueror's key event support
	//(namely, that the document keydown event which sets the keydown flag doesn't fire at all)
	//I suppose we could universally replace the keydown flag with this
	//but I don't want to risk it at this stage of the game
	//when it's so much safer and easier just to manage a handful of kde exceptions
	this.mouseisdown = false;



	//add a document mouseout handler
	dbx.addEvent(document, 'mouseout', function(e)
	{
		//store event related target
		if(typeof e.target == 'undefined') { e.relatedTarget = e.toElement; }

		//if the related target is null, fire the mouseup handler
		//this catches mouse movement outside the window while holding a clone
		//which could cause "sticky mouse", as onmouseup doesn't fire outside the window
		if(e.relatedTarget == null)
		{
			//pass event to mouseup handler
			self.mouseup(e);
		}
		
	});


	//add document mousemove handler for when we're moving the clone
	//which we can also use to implement the dynamic box hover class
	dbx.addEvent(document, 'mousemove', function(e)
	{
		//pass event to hover method
		self.hover(e);
		
		//pass event to mousemove handler
		self.mousemove(e);

		//IE needs this so that the drag action works properly for links
		//otherwise it "mis-fires", as it were, and generates that black "no action" symbol
		//leading to a series of bugs that results in the clone being sticky to the mouse
		//and resetting at whatever arbitrary position you let go of it
		//drag 'n' drop scripting always requires this return false on the mousemove
		//so I feel pretty silly for not having noticed it before, even though
		//I've never really understood why it's necessary in the first place
		//--- Thanks to Thomas Karl for reporting this bug ---//
		//however if it returns false all the time then
		//text-range selection is broken completely in IE
		//so we have to return by the inverse of the dragok flag, for whether
		//a drag action is occuring (so return false), or this is unrelated mousemovement (so return true)
		return !self.dragok;

	});


	//add document mousedown handler purely to set the mousedown flag
	dbx.addEvent(document, 'mousedown', function(e)
	{
		//clear the mousedown flag
		self.mouseisdown = true;

	});
	

	//add document mouseup handler for when we let go of the clone
	dbx.addEvent(document, 'mouseup', function(e)
	{
		//clear the mouseisdown flag
		self.mouseisdown = false;
		
		//pass event to mouseup handler
		self.mouseup(e);
	});


	//add document keydown handler to set the keydown flag 
	//and to implement the dynamic active class
	dbx.addEvent(document, 'keydown', function(e)
	{
		//set the keydown flag
		self.keydown = true;

		//if we have an existing dialog and this is not an arrow key, enter or meta key
		//(shift is what we're interested in, but checking others allows for future expansion)
		//we want to remove it because it will be residual from an abandoned action
		if(self.dialog && !/^((3[7-9])|40|13|(1[6-8]))$/.test(e.keyCode))
		{
			self.clearDialog();
		}

	});

	//add a keyup handler to clear the keydown flag and active classes
	dbx.addEvent(document, 'keyup', function()
	{
		//clear the keydown flag
		self.keydown = false;

		//clear the keyboard direction property
		self.currentdir = null;

		//remove any active class (but not target class);
		self.removeActiveClasses('dbx\-box\-active');
	});
};


//initialize the boxes in this group
dbxGroup.prototype.initBoxes = function(recover, getspare)
{
	//dictionary of box objects, including a manual length property
	//because we won't be able to derive one otherwise
	this.boxes = { 'length' : 0 };
	
	//array of handles, for referring to in the hover class routine
	this.handles = [];

	//dictionary of buttons, so we can save them on creation
	//and then have a reference to initialise them from cookie data
	this.buttons = {};

	//box order array, needs to be an array specifically
	//because we use array methods on it
	this.order = [];


	//copy a reference to this
	var self = this;
	
	//get all elements within this container
	this.eles = dbx.get('*', this.container);

	//for each element - iterating by length because it will be changing
	for(var i=0; i<this.eles.length; i++)
	{
		//the dbxid token is based on the boxes length counter
		var dbxid = this.boxes.length;

		//if it's a docking box and not a dummy
		//(or a clone which might be temporarily inside the box
		// but musn't be counted as a permanent member)
		if(dbx.hasClass(this.eles[i], 'dbx\-box') && !dbx.hasClass(this.eles[i], 'dbx\-(dummy|clone)'))
		{
			//if box-ID based dynamic groups are enabled
			if(dbx.useid)
			{
				//if it has a valid ID, use that for dbxid
				if(/^[a-z][a-z0-9]*$/i.test(this.eles[i].id) && !/^(length|dummy)$/.test(this.eles[i].id))
				{
					dbxid = this.eles[i].id;
				}

				//if it has an ID but it's invalid, throw an exception and stop
				else if(this.eles[i].id != '')
				{
					throw('Error from dbxGroup:\n"' + this.eles[i].id + '" is an invalid box ID');
					return;
				}
			}

			//add to dictionary of docking boxes and increase the length counter
			this.boxes[dbxid] = this.eles[i];
			this.boxes.length++;
			
			//add its order in the array to the order array
			//plus its open state ("+" or "-" for open or close), which is open by default
			this.order.push(dbxid + '+');
			
			//if this element doesn't already have handlers
			if(typeof this.eles[i].hashandlers == 'undefined')
			{
				//bind mousedown handler
				dbx.addEvent(this.eles[i], 'mousedown', function(e)
				{
					//convert event argument
					if(!e) { e = window.event; }

					//get box element from target
					//then pass event reference and box to mousedown handler
					self.mousedown(e, dbx.getTarget(e, 'dbx\-box'));

				});
				
				//set the hashandlers flag
				this.eles[i].hashandlers = true;
			}

			//if we've already processed this box since it's been here this session
			//we can continue straight on to the next iteration
			//indeed, we must, to avoid the class name etc. being written multiple times to the same box
			if(typeof this.eles[i].processed != 'undefined') { continue; }

			//re-inforce relative positioning and block display
			this.eles[i].style.position = 'relative';
			this.eles[i].style.display = 'block';
			
			//add its default open state as an additional classname
			this.eles[i].className += ' dbx-box-open';

			//then add the dbxid to the classname, so we can identify it whatever its position
			//we're not using the actual id attribute because then it wouldn't be encapsulated or unique
			this.eles[i].className += ' dbxid-' + dbxid;
			
			//set the processed flag
			this.eles[i].processed = true;
		}

		//if it's a handle
		if(dbx.hasClass(this.eles[i], 'dbx\-handle'))
		{
			//add it to the handles collection
			this.handles.push(this.eles[i]);
			
			//save a reference to the parent box
			var parentbox = dbx.getTarget(null, 'dbx\-box', this.eles[i]);
			
			//if we're adding toggle buttons
			if(this.toggles)
			{
				//get dbxid from parent box
				dbxid = dbx.getID(parentbox);

				//add toggle behavior, returning the button object for the cookie function to use
				//(the function itself will either create a new button, or re-initialize existing
				// depending on whether there's a button there already)
				this.buttons[dbxid] = this.addToggleBehavior(this.eles[i]);
			}

			//else if we're not adding toggle buttons
			//and this element doesn't already have handlers,
			//attempt to bind a keyboard handlers to the handle itself
			//which will work if the handle is a focussable element
			else if(typeof this.eles[i].hashandlers == 'undefined')
			{
				//save a reference to the handle
				var handle = this.eles[i];
			
				//create a hasfocus flag to determine if the handle is focussed
				//[see addToggleBehavior for a full explanation of this]
				handle.hasfocus = dbx.opera || dbx.safari ? null : false;
			
				//if the parent is not an ungrabbable box
				if(!dbx.hasClass(parentbox, 'dbx\-nograb'))
				{
					//we need a key handler for the button
					//we also want to be able to suppress page scrolling when appropriate
					//but browsers have different ideas about which event that comes from, and what will work
					//in ie, safari and chrome we need onkeydown, in moz we need onkeypress,
					//in opera default action suppression may not work either way
					//(later versions should be okay, but not earlier versions)
					dbx.addEvent(handle, 'key' + (dbx.msie || dbx.safari || dbx.chrome ? 'down' : 'press'), function(e)
					{
						//convert event argument
						if(!e) { e = window.event; }
						
						//pass event and handle to keypress handler
						//return value determines whether native action happens
						return self.keypress(e, dbx.getTarget(e, 'dbx\-handle'));
	
					});
	
					//bind click handler 
					dbx.addEvent(handle, 'click', function(e)
					{
						//if we have a dialog
						if(self.dialog)
						{
							//pass handle to click handler
							self.click(e, dbx.getTarget(e, 'dbx\-handle'));
				
							//prevent default action if that's supported
							//otherwise return false (resulting in the same effect in ie)
							if(typeof e.preventDefault != 'undefined') { e.preventDefault(); }
							else { return false; }
						}
						
					});
					
				}
		
				//bind focus handler
				dbx.addEvent(handle, 'focus', function(e)
				{
					//convert event argument
					if(!e) { e = window.event; }
					
					//save a reference to the parent box
					var parentbox = dbx.getTarget(e, 'dbx\-box');

					//if the keydown flag is set, 
					//or this is konqueror and the mousedown flag is not set
					if(self.keydown || (dbx.kde && !self.mouseisdown))
					{
						//add focus class name to parent box
						parentbox.className += ' dbx-box-focus';
					}

					//save a reference to the handle
					var handle = dbx.getTarget(e, 'dbx\-handle');
		
					//get the tooltiptext from lang
					var tooltiptext = self.vocab.kmove;
		
					//if this is an ungrabbable box, we should only use the original text
					//not add any instructions, because the box can't be moved
					if(dbx.hasClass(parentbox, 'dbx\-nograb'))
					{
						tooltiptext = handle.getAttribute('oldtitle');
					}
		
					//if the handle has existing title text, combine the two as per config
					//using the saved original value, because the actual value will have been modified
					else if(!dbx.empty(handle.getAttribute('oldtitle')))
					{
						tooltiptext = self.vocab.syntax
										.replace(/%mytitle[%]?/, handle.getAttribute('oldtitle'))
										.replace(/%dbxtitle[%]?/, tooltiptext)
		
					}
					
					//if the text isn't empty, pass it and handle object to create tooltip
					//plus the keydown/mouseisdown flag to determine if it's okay
					if(!dbx.empty(tooltiptext))
					{
						self.createTooltip(
							tooltiptext,
							handle,
							(self.keydown || (dbx.kde && !self.mouseisdown))
							);
					}
					
					//set the has focus flag if it's not strictly null
					if(handle.hasfocus !== null) { handle.hasfocus = true; }
		
				});

				//bind blur handler
				dbx.addEvent(handle, 'blur', function(e)
				{
					//convert event argument
					if(!e) { e = window.event; }

					//remove any focus class name from parent box
					dbx.removeClass(dbx.getTarget(e, 'dbx\-box'), 'dbx\-box\-focus');
					
					//remove any tooltip that's there
					self.removeTooltip();

					//save a reference to the handle
					//and clear the has focus flag if it's not strictly null
					var handle = dbx.getTarget(e, 'dbx\-handle');
					if(handle.hasfocus !== null) { handle.hasfocus = false; }
		
				});
				
				//set the hashandlers flag
				this.eles[i].hashandlers = true;
			}

			//if we've already processed this handle 
			//we can continue straight on to the next iteration
			//indeed, we must, to avoid the class etc. being added multiple times to the same handle
			if(typeof this.eles[i].processed != 'undefined') { continue; }

			//save its original title (ie. what it has now, before processing)
			//to another attribute, so we can refer to it for keyboard tooltips
			var oldtitle = this.eles[i].getAttribute('title');
			if(oldtitle) { this.eles[i].setAttribute('oldtitle', oldtitle); }

			//re-inforce relative positioning and block display
			this.eles[i].style.position = 'relative';
			this.eles[i].style.display = 'block';

			//if the parent is not an ungrabbable box
			if(!dbx.hasClass(parentbox, 'dbx\-nograb'))
			{
				//add cursor classname
				this.eles[i].className += ' dbx-handle-cursor';
	
				//if the handle doesn't already have a title, create one
				//if it does, append the title using pattern match syntax
				this.eles[i].setAttribute('title', 
					dbx.empty(this.eles[i].getAttribute('title'))
						? this.vocab.move 
						: this.vocab.syntax.replace(/%mytitle[%]?/, this.eles[i].title)
							.replace(/%dbxtitle[%]?/, this.vocab.move)
						);
			}
			
			//set the processed flag
			this.eles[i].processed = true;
		}

		//if it's a content area or box, and this is IE, 
		//add css to force it to "have layout" to fix any rendering bugs
		//the content area is for a known issue with it being initially invisible
		//the box itself is just in case (i think i saw it happen though)	
		//writing to runtimeStyle instead of style means that changes to the style object odn't affect it
		//which makes it more stable and reliable in practise
		if(dbx.msie && dbx.hasClass(this.eles[i], 'dbx\-(content|box)'))
		{
			this.eles[i].runtimeStyle.zoom = '1.0';
		}

	}
	
	
	
	//update (or rather, initially add) the child classes
	this.updateChildClasses();


	//save this group in the dbx manager save data object
	//with a string representation of its order
	dbx.savedata[this.gid] = this.order.join(',');

	//add a dummy docking box to the end of the container
	//which we'll need as an insertBefore reference for boxes moved to the end
	var dummy = this.container.appendChild(dbx.createElement('span'));
	dummy.className = 'dbx-box dbx-dummy';

	//re-inforce important styles
	dummy.style.display = 'block';
	dummy.style.width = '0';
	dummy.style.height = '0';
	dummy.style.overflow = 'hidden';

	//apply offleft positioning
	dummy.className += ' dbx-offdummy';

	//create the dummy dbxid token
	dbxid = this.boxes.length;
	if(typeof dbx.gnumbers[this.gid] != 'undefined')
	{
		dbxid += '_' + dbx.gnumbers[this.gid];
	}

	//add to dictionary of docking boxes and increase the length counter
	this.boxes[dbxid] = dummy;
	this.boxes.length++;


	//don't continue unless we want to recover a saved state
	if(!recover) { return; }


	//if there's cookie state data that relates to this group
	if(dbx.cookiestate && typeof dbx.cookiestate[this.gid] != 'undefined')
	{
		//number of values
		var num = dbx.cookiestate[this.gid].length;

		//iterate through values
		for(i=0; i<num; i++)
		{
			//the index of this box, stripped of its open/closed token
			var index = dbx.cookiestate[this.gid][i].replace(/[\-\+]/g, '');

			//if this box exists and its not the dummy
			if(typeof this.boxes[index] != 'undefined' && this.boxes[index] != dummy)
			{
				//move this box before the last one
				this.container.insertBefore(this.boxes[index], dummy);

				//if we're using toggle buttons, and
				//if the box (in its corresponding place in boxes array) should be closed
				//--- Thanks to Ward Vandewege for the patch that allows for more than 10 boxes ---//
				if(this.toggles && /\-$/.test(dbx.cookiestate[this.gid][i]))
				{
					//if the box button exists, send to toggle box state method, but don't save
					//this if condition is purely to prevent an error due to bad config
					//that is, when using dynamic groups without the box-ID being enabled
					//which can happen if you add, modify, reload then try to remove a box!
					//pass the flag that says this is not a manual interaction
					//and the override flag that says to set this to closed (argument = isopen)
					if(typeof this.buttons[index] != 'undefined')
					{
						this.toggleBoxState(this.buttons[index], false, false, true);
					}
				}
			}
		}

		//regenerate the box order array (from which, save back to cookie)
		this.regenerateBoxOrder();
	}

	//else if there is no cookie,
	//and the box is set to be not-open by default
	//and we're using toggle buttons
	else if(!this.defopen && this.toggles)
	{
		//iterate through box buttons
		for(i in this.buttons)
		{
			//ignore unwanted properties 
			if(dbx.unwanted(this.buttons, i)) { continue; }
			
			//send the box button to toggle box state method and save
			//pass the flag that says this is not a manual interaction
			//and an empty final argument that means toggle, don't force a state
			this.toggleBoxState(this.buttons[i], true, false, null);
		}
	}
};




//create transient elements for key dynamic classes
//so that if those classes contain any image backgrounds
//those images will be preloaded
dbxGroup.prototype.cacheDynamicClasses = function()
{
	//the lists of classes we're concerned with
	//and an array to save the elements we create
	var eles = [], classes = ['dbx-tooltip', 'dbx-dragclone', 'dbx-dialog'];
	for(var i=0; i<classes.length; i++)
	{
		//create an element with the given class
		//plus "dbx-clone" so that it's hidden
		//appended inside the group container so it inherits correctly
		eles[i] = dbx.createElement('div');
		eles[i].className = 'dbx-clone ' + classes[i];
		//don't append until classes are defined
		//so it doesn't have a brief displacement effect
		this.container.appendChild(eles[i]);
	}

	//now set a momentary timer, and remove all the elements
	//100ms is more than plenty for any image requests to have started
	//and they'll then continue even after the element is gone
	setTimeout(function()
	{
		for(var i=0; i<eles.length; i++)
		{
			if(eles[i].parentNode)
			{
				eles[i].parentNode.removeChild(eles[i]);
			}
		}
	}, 100);
};


//add docking box toggle (open and close) behavior
dbxGroup.prototype.addToggleBehavior = function()
{
	//copy a reference to this
	var self = this;

	//look for an existing button in this handle
	var existing = dbx.get((dbx.buttontype == 'link' ? 'a' : 'button'), arguments[0]);
	for(var i=0; i<existing.length; i++)
	{
		//if we find one, save the reference and break
		if(dbx.hasClass(existing[i], 'dbx\-toggle'))
		{
			var button = existing[i];
			break;
		}
	}

	//if we don't have a button reference we need to create a new one
	if(typeof button == 'undefined')
	{
		//if the button type is link
		if(dbx.buttontype == 'link')
		{
			//insert new toggle button as link
			button = arguments[0].appendChild(dbx.createElement('a'));

			//we need to add something inside the link, or it may not be key accessible to all
			//so I'm going to write in a non-breaking space
			//using a unicode reference, because we can't use an entity within createTextNode
			//(there is a createEntityReference method, but no browser effectively implements it)
			button.appendChild(document.createTextNode('\u00a0'));

			//give it an href so it can accept the focus
			button.href = 'javascript:void(null)';
		}

		//otherwise it must be button
		else
		{
			//insert new toggle button as button
			button = arguments[0].appendChild(dbx.createElement('button'));
		}

		//set classname and title to be initially open
		button.className = 'dbx-toggle dbx-toggle-open';
		button.setAttribute('title', this.vocab.toggle.replace(/%toggle[%]?/, this.vocab.close));
	}

	//set pointer cursor, else it will inherit move cursor from parent
	button.style.cursor = 'pointer';


	//create a hasfocus flag to determine if the button is focussed
	//which we'll use to differentiate click events on the button
	//and prevent them from working if the button isn't focussed
	//this will prevent browser-based screenreaders from being able to undisplay the contents
	//but that will fail in opera, safari and chrome, so we need to exclude them specifically
	//fortunately there aren't any readers based on those browsers
	//(opera 8+ has voice, but that's something else)
	//we're using a tri-state flag here,
	//so to avoid conflict with browsers doing automatic type conversion
	//tests against this value are going to use strict [in]equality
	button.hasfocus = dbx.opera || dbx.safari || dbx.chrome ? null : false;


	//keyboard navigation tooltip object
	this.tooltip = null;


	//if this element doesn't already have handlers
	if(typeof button.hashandlers == 'undefined')
	{
		//bind click handler to button
		button.onclick = function(e)
		{
			//pass button to click handler
			self.click(e, this);

			//return false so we don't follow the link, even though it isn't one
			//it's a dummy href value, but nonetheless, may cause the browser to respond
			//at least partially as though following a link, eg, in IE it would make that "click" sound
			return false;
		};
		
		//we need a key handler for the button
		//but we also want to be able to suppress page scrolling when appropriate
		//but browsers have different ideas about which event that comes from, and what will work
		//in ie, safari and chrome we need onkeydown, in moz we need onkeypress,
		//in opera default action suppression doesn't occur either way
		button['onkey' + (dbx.msie || dbx.safari || dbx.chrome ? 'down' : 'press')] = function(e)
		{
			//convert event argument
			if(!e) { e = window.event; }

			//pass event and button object to keypress handler
			//return value determines whether native action happens
			return self.keypress(e, this);
		};

		//bind focus handler to button
		button.onfocus = function()
		{
			//iterate through all buttons and remove existing hilite classname
			for(var i in self.buttons)
			{
				if(dbx.unwanted(self.buttons, i)) { continue; }
				
				self.buttons[i] = dbx.removeClass(self.buttons[i], '(dbx\-toggle\-hilite\-)(open|closed)');
			}

			//get open state from button classname
			var isopen = dbx.hasClass(this, 'dbx\-toggle\-open');

			//add the hilite classname
			this.className += ' dbx-toggle-hilite-' + (isopen ? 'open' : 'closed');

			//if the keydown flag is set,
			//or this is konqueror and the mousedown flag is not set
			if(self.keydown || (dbx.kde && !self.mouseisdown))
			{
				//add focus class name to parent box
				dbx.getTarget(null, 'dbx\-box', this).className += ' dbx-box-focus';
			}

			//compile the tooltiptext according to open state
			var tooltiptext = (!dbx.hasClass(dbx.getTarget(null, 'dbx\-box', this), 'dbx\-nograb')
				? self.vocab.kmove : '')
				+ self.vocab.ktoggle.replace(/%toggle[%]?/, (isopen ? self.vocab.close : self.vocab.open));

			//if the handle has existing title text, combine the two as per config
			//using the saved original value, because the actual value will have been modified
			var handle = dbx.getTarget(null, 'dbx\-handle', this);
			if(!dbx.empty(handle.getAttribute('oldtitle')))
			{
				tooltiptext = self.vocab.syntax
								.replace(/%mytitle[%]?/, handle.getAttribute('oldtitle'))
								.replace(/%dbxtitle[%]?/, tooltiptext)

			}

			//pass tooltiptext and button object to create tooltip
			//plus the keydown (or kde not-mousedown) flag to determine if it's okay to move,
			//passing the toggle instructions only if not
			self.createTooltip(
				tooltiptext,
				this,
				(self.keydown || (dbx.kde && !self.mouseisdown))
				);

			//set the isactive flag for focus setter in animation
			//we need the flag to prevent setting focus on
			//the animated elements that we only cloned and didn't move
			//and to prevent setting highlight on pressed buttons from cookie initialisation
			this.isactive = true;

			//set the has focus flag if it's not strictly null
			if(this.hasfocus !== null) { this.hasfocus = true; }
		};

		//bind blur handler to button
		button.onblur = function()
		{
			//remove the hilite classname
			button = dbx.removeClass(button, '(dbx\-toggle\-hilite\-)(open|closed)');

			//remove any focus class name from parent box
			dbx.removeClass(dbx.getTarget(null, 'dbx\-box', this), 'dbx\-box\-focus');

			//remove any tooltip that's there
			self.removeTooltip();

			//clear the has focus flag if it's not strictly null
			if(this.hasfocus !== null) { this.hasfocus = false; }
		};

		//set the hashandlers flag
		button.hashandlers = true;
	}

	//return the button object
	return button;
};


//toggle the state of box
dbxGroup.prototype.toggleBoxState = function(button, regen, manual, forcestate)
{
	//get open state from button classname
	var isopen = dbx.hasClass(button, 'dbx\-toggle\-open');

	//or use force state if defined
	if(forcestate !== null) { isopen = forcestate; }

	//iteratively find a reference to the button's parent box
	var parent = dbx.getTarget(null, 'dbx\-box', button);

	//store values to dbx properties for external methods
	dbx.sourcebox = parent;
	dbx.toggle = button;
	dbx.dbxobject = this;
	//but the container might be undefined
	//if this is called from the cookie function
	if(typeof dbx.container == 'undefined')
	{
		//so in that case, retrieve it iteratively
		dbx.group = dbx.getTarget(null, 'dbx\-group', parent);
	}
	//otherwise just copy it from container
	else { dbx.group = dbx.container; }


	//if an onbeforestatechange function is defined
	if(typeof dbx.onbeforestatechange != 'undefined')
	{
		//compile the data for, and dispatch, any onbeforestatechange function that exists
		//and store the result to actions object
		var actions = dbx.compileAndDispatchOnBeforeStateChange(
			['proceed', this, this.container, this.gid, parent, button, (isopen ? 'close' : 'open')]);

		//if we're not okay to proceed, don't continue
		if(!actions.proceed) { return; }
	}


	//if the manual flag is false (so that onboxopen and onboxclose don't fire from 
	//                             programmatic toggle commands that restore a cookie state); or
	//if the box is currently closed, and onopen doesn't exist or returns true; or
	//if the box is currently open, and onclose doesn't exist or returns true
	if
	(
		manual == false
		||
		(!isopen && (typeof dbx.onboxopen == 'undefined' || dbx.onboxopen()))
		||
		(isopen && (typeof dbx.onboxclose == 'undefined' || dbx.onboxclose()))
	)
	{
		//change the classname and title
		button.className = 'dbx-toggle dbx-toggle-' + (isopen ? 'closed' : 'open');
		button.title = this.vocab.toggle.replace(/%toggle[%]?/, isopen ? this.vocab.open : this.vocab.close);

		//if this is a manual interaction and the hilite classname is necessary, add it
		if(manual && typeof button.isactive != 'undefined')
		{
			button.className += ' dbx-toggle-hilite-' + (isopen ? 'closed' : 'open')
		}

		//change the parent box open state classname
		//which is both a stored value for us to read its state
		//and used in a descendent selector to hide the inner content
		parent.className = parent.className.replace(/[ ](dbx-box-)(open|closed)/, ' $1' + (isopen ? 'closed' : 'open'));

		//if the regenerate flag is set,
		//regenerate the box order array and save to cookie
		if(regen) { this.regenerateBoxOrder(); }
	}
};







//move a box (by conditions) using the keyboard
dbxGroup.prototype.moveBoxByKeyboard = function(e, anchor, parent, direction, confirm, manual)
{
	//store values to dbx properties for external methods
	dbx.dbxobject = this;
	dbx.group = this.container;
	dbx.gid = this.gid;
	dbx.sourcebox = parent;
	dbx.clonebox = null;
	dbx.event = e;

	//define an index variable for storing a target box
	//default to '-' which means no selection
	var index = '-';

	//the movement is positive if we're moving down or right
	//unless we're moving down-left
	//save it to a global property because we'll need it outside this method - 
	this.positive = /[se]/i.test(direction);
	if(/^(Sw)$/.test(direction)) { this.positive = false; }

	//remove any target or active class names from the boxes in this group
	this.removeActiveClasses('dbx\-box\-(target|active)');


	//get the origin point of the selected box
	var clonepoint = {
		'x' : parent.offsetLeft,
		'y' : parent.offsetTop
		};

	//create an array of differences with each comparison box
	var differences = [];

	//for most conditions the boxes collection we ant
	//is all the boxes in this group
	var boxes = this.boxes;

	//for each box in the collection
	for(var i in boxes)
	{
		//don't include members we don't care about, or the dummy
		if(dbx.unwanted(boxes, i) || dbx.hasClass(boxes[i], 'dbx\-dummy')) { continue; }
		
		//get the origin point of this box
		var boxpoint = {
			'x' : boxes[i].offsetLeft,
			'y' : boxes[i].offsetTop
			};

		//work out the differences for this box
		//which will be positive for right/down or negative for left/up
		//and add it to the array of differences including the i index
		//because we'll need that at the other end when it's sorted
		differences.push([i, boxpoint.x - clonepoint.x, boxpoint.y - clonepoint.y]);

		//if this is the box we're moving, store its index
		//(but don't stop - we need to store all the differences)
		if(parent == boxes[i]) { index = i; }
	}


	//create arrays for storing members by whether they have positive or negative difference
	var splitdiffs = {
		'positive' : [],
		'negative' : []
		};

	//the applicable axis for direction (x for right/left, y for up/down)
	var n = /[ew]/i.test(direction) ? 1 : 2;

	//for each of the differences, add them to the correct array (as per axis)
	for(i=0; i<differences.length; i++)
	{
		//don't add the parent box itself
		if(differences[i][0] == index) { continue; }

		//add to the relevant array
		if(differences[i][n] >= 0)
		{
			splitdiffs.positive.push(differences[i]);
		}
		else
		{
			splitdiffs.negative.push(differences[i]);
		}
	}


	//now if we're moving in a positive direction we only care about the positives array
	//or if we're moving in a negative direction we only care about the negatives
	var ary = this.positive ? splitdiffs.positive : splitdiffs.negative;

	//sort the array by the same axis and using absolute numbers
	//so that we find out which objects are closest by the movement direction
	ary.sort(function(a, b){ return Math.abs(a[n]) - Math.abs(b[n]); });

	//now check the axis for values in the positive arrays which are zero
	//for example, movement right where the y axis is 0,
	//hence it's an object on the same vertical line, and we need to remove it
	for(i=0; i<ary.length; i++)
	{
		if(ary[i][n] == 0)
		{
			ary.splice(i--, 1);
		}
	}

	//if this is diagonal movement,
	if(direction.length > 1)
	{
		//remove members which are on a parallel
		//and remove members which are in the wrong direction on the other axis
		//this thinks in terms of diagonals being principly left or right, with an additional consideration
		for(i=0; i<ary.length; i++)
		{
			if(
				(/[ew]/i.test(direction) && ary[i][2] == 0)
				||
				(/[ns]/i.test(direction) && ary[i][1] == 0)
				||
				(/(N[ew])/.test(direction) && ary[i][2] > 0)
				||
				(/(S[ew])/.test(direction) && ary[i][2] < 0)
				)
			{
				ary.splice(i--, 1);
			}
		}
	}
	//if it's not diagonal movement we don't want to remove diagonal paths
	//because we may need to fallback on them when moving around irregularly sized shapes
	//and because this reduces the possibility of accessibility problems due to
	//a user not being able to press two keys at once, because if the interface requires it,
	//it's still possible to move diagonally with single keys
	//(nb. the rules engine ratifies this with the behavior of confirm mode
	// which allows a user to navigate any path to a valid square through intervening invalid squares
	// in case they can't press the keystrokes that would describe the most direct path (like a diagonal))

	//now iterate and remove all which aren't the same as the lowest value
	//so that we end up with arrays of just the lowest
	for(i=0; i<ary.length; i++)
	{
		//if we're interested in positive we need absolute numbers
		if(this.positive)
		{
			if(i > 0 && Math.abs(ary[i][n]) != Math.abs(ary[0][n]))
			{
				ary.splice(i--, 1);
			}
		}
		//but for negatives we need to preserve their negativity
		//can't remember why, but I'm sure there was a reason!
		else
		{
			if(i > 0 && ary[i][n] != ary[0][n])
			{
				ary.splice(i--, 1);
			}
		}
	}

	//now we want to sort the arrays by the opposite axis
	//to find which one is closest in that direction
	n = n == 1 ? 2 : 1;
	ary.sort(function(a, b){ return Math.abs(a[n]) - Math.abs(b[n]); });

	//now if the array has no members then there's no object in this direction
	//so reset the index to '-' for no selection
	if(ary.length == 0)
	{
		index = '-';
	}

	//otherwise the index we want is at ary[0][0]
	//plus one if this is positive movement
	//(so that we effectively get an insert-after reference)
	else
	{
		index = ary[0][0];
	}

	//get a reference to the box that this anchor relates
	var box = dbx.getTarget(null, 'dbx\-box', anchor);


	//if the index is '-' for no selection
	if(index == '-')
	{
		//in any case don't continue here
		//(return false so we pass a value back to the api move() method)
		return false;
	}


	//the box we want then is boxes[index]
	var targetbox = boxes[index];


	//but if the exchange mode is insert and confirm mode is false and this is positive movement
	//we want its next sibling [effectively, next sibling box or dummy]
	//because we're going to want to insert after, not before,
	//which of course translates to insert before the next one
	//if we don't do this then moving to the right becomes impossible
	//and moving down is counter-intuitive
	//however if we run out of next siblings without finding a box
	//we'll just have to maintain the previous target
	//(note, we will need to do this modification for confirm mode as well
	// but not in the display of the dialogs, only in the actual confirmed movement)
	if(this.exchange == 'insert' && this.confirm == false && this.positive == true)
	{
		targetbox = dbx.get('nextSibling', targetbox);
		if(!targetbox) { targetbox = boxes[index]; }
	}

	//if onboxdrag doesn't exist, or returns true
	if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag())
	{
		//now we we know specifically which block we're trying to swap with
		//which means we can work out the direction and blocks values for testing with
		//but we only need to do that if the two boxes are not the same
		//because you're always allow to move the caret back to the place you started from
		//also don't do this if unless we're using boxes from this group
		//because cross-group transfer is not affected by rules
		if(box != targetbox && boxes == this.boxes)
		{
			//so first get the center point of the original box
			var origpoint = {
				'x' : box.offsetLeft + (box.offsetWidth / 2),
				'y' : box.offsetTop + (box.offsetHeight / 2)
				};

			//then get the center point of the selected box
			var boxpoint = {
				'x' : targetbox.offsetLeft + (targetbox.offsetWidth / 2),
				'y' : targetbox.offsetTop + (targetbox.offsetHeight / 2)
				};

			//get the blocks difference
			var testblocks = this.getBlocksDifference(origpoint, boxpoint, box);

			//get the compass direction
			var testcompass = this.getCompassDirection(origpoint, boxpoint);

			//evaluate the rules for this direction, blocks, box and no rulekey override
			//and if we fail ...
			if(this.functionExists('_testRules') && !this._testRules(testcompass, testblocks, box, null))
			{
				//if confirm mode is enabled or we already have a tracking dialog element
				if(confirm || this.dialog)
				{
					//pass the box to update dialog method
					//passing the additional state class, and no position override or group ref
					//and the keyboard source flag
					this.updateDialog(targetbox, ' dbx-dialog-no', null, null, 'keyboard');

					//if we have the applicable action instructions defined
					if(this.vocab.kno != '')
					{
						//pass action instructions and original box to create tooltip
						//and true for the okay flag, so it definitely happens
						//(keydown flag may not be true, even though this is key initiated
						// because you press and release it to trigger the action)
						this.createTooltip(
							this.vocab.kno,
							box,
							true
							);
					}
				}

				//if this is a manual interaction, send focus back to the anchor
				//and add back the focus class to the box
				if(manual) { this.refocus(anchor); }

				//don't continue if we don't get permission
				//(return false so we pass a value back to the api move() method)
				return false;
			}
		}


		//if the boxes are not the same, and the target is not the dummy or a dialog
		//apply the target class name to the target box
		if(box != targetbox && !dbx.hasClass(targetbox, 'dbx\-(dialog|dummy)'))
		{
			targetbox.className += ' dbx-box-target';
		}


		//if confirm mode is enabled or we already have a tracking dialog element
		if(confirm || this.dialog)
		{
			//for most situations we have no position and group override for update dialog
			var diffs = null, group = null;

			//but if we're using boxes from a different group, we do
			if(boxes != this.boxes)
			{
				//store the group reference
				group = this.dialog.group;

				//we need to calculate the position difference between
				//the calling group and the new group
				//and then pass those values as position overrides to update dialog method
				var groupcontainer = dbx.getPosition(group.container, false);
				var callcontainer = dbx.getPosition(this.container, false);
				var diffs = {
					'x' : groupcontainer.left - callcontainer.left,
					'y' : groupcontainer.top - callcontainer.top
					};
			}

			//pass the selected box and additional state class to update dialog method
			//along with the defined position and group overrides (or null)
			//passing the additional state class even if the boxes are the same,
			//because i reckon that makes for better usability - it's confusing when it disappears
			//and makes you wonder whether it's still in the same mode
			//I tried it with a third state which indicates "home square in confirm mode"
			//but that just seemed more confusing than leaving it as normal confirmation
			//also pass the source flag so we know how this dialog is generated
			//so that we can prevent mouse movement from clearing keyboard generated dialogs
			this.updateDialog(targetbox, ' dbx-dialog-yes', diffs, group, 'keyboard');

			//if we have the applicable action instructions defined
			if(this.vocab.kyes != '')
			{
				//pass action instructions and original box to create tooltip
				//and true for the okay flag, so it definitely happens
				//(keydown flag may not be true, even though this is key initiated
				// because you press and release it to trigger the action)
				this.createTooltip(
					this.vocab.kyes,
					box,
					true
					);
			}

			//if this is a manual interaction, send focus back to the anchor
			//and add the focus class back to the parent box
			//this manual test isn't really necessary, since currently only
			//manual interactions can generate a dialog, but i like to be thorough!
			if(manual) { this.refocus(anchor); }

			//don't continue with the animation
			//(return false so we pass a value back to the api move() method
			//although this condition should never be true under those circumstances
			//this is so it returns failure if something screws up!)
			return false;
		}


		//if the exchange mode is swap, physically swap the two boxes
		//passing the original box, selected box, and original box anchor
		//and the argument that says whether or not is a manual interaction
		//and the argument for whether this is positive or negative
		//(to use for evaluating onbeforestatechange properties)
		//return the return value of that back up to the api
		if(this.exchange == 'swap')
		{
			return this.swapTwoBoxes(parent, targetbox, anchor, manual, this.positive);
		}
		
		//otherwise insert the original box before or after the selected box (depending on direction)
		//and return the return value of that back up to the api
		else
		{
			return this.insertTwoBoxes(parent, targetbox, anchor, manual, direction);
		}
	}

	//if we get here then something screwed up
	//so return false so we pass a value back to the api move() method
	return false;
};



//insert one box before or after another, in response to keyboard action
dbxGroup.prototype.insertTwoBoxes = function(original, selected, anchor, manual, positive)
{
	//if a beforestatechange function is defined
	if(typeof dbx.onbeforestatechange != 'undefined')
	{
		//compile the data for, and dispatch, any onbeforestatechange function that exists
		//then store the resulting action flag to an actions object
		var actions = dbx.compileAndDispatchOnBeforeStateChange(
			['proceed', this, this.container, this.gid, original, selected, 'insert']
			);

		//if the function returned false
		//return false here in case we need to pass that back up to api
		if(!actions.proceed) { return false; }
	}


	//update the rule pointer as necessary
	if(this.functionExists('_updateRulePointer')) { this._updateRulePointer(); }


	//the box animation will be on every box from the insertion point
	//to the end of the group (not including the box we're moving),
	//so we're going to have to create that collection as a numerically indexed array
	var add = false, pointer = 0, theboxes = [], visiboxes = [];
	for(var i in this.boxes)
	{
		if(dbx.unwanted(this.boxes, i)) { continue; }
		
		theboxes.push(this.boxes[i]);
	}
	for(i=0; i<theboxes.length; i++)
	{
		if(theboxes[i] == original) { continue; }

		visiboxes.push(theboxes[i]); 
	}
	
	//get the positions of those boxes
	var visiposes = [];
	for(i=0; i<visiboxes.length; i++)
	{
		visiposes.push({
			'x' : visiboxes[i].offsetLeft,
			'y' : visiboxes[i].offsetTop
			});
	}
	
	//we'll also separately animate the box you're moving
	var originalpos = { 'x' : original.offsetLeft, 'y' : original.offsetTop };
	original.style.visibility = 'hidden';

	//remove any target class name from the target box
	selected = dbx.removeClass(selected, 'dbx\-box\-target');

	//insert the original box before the selected box
	selected.parentNode.insertBefore(original, selected);


	//if we have a collection of boxes, animate each one
	if(typeof visiboxes != 'undefined' && visiboxes.length > 0)
	{
		for(i=0; i<visiboxes.length; i++)
		{
			new dbxAnimator(this, visiboxes[i], visiposes[i], this.resolution, false, null, true);
		}
	}
	
	//and animate the original box last, so it finishes on top (fnarr)
	new dbxAnimator(this, original, originalpos, this.resolution, true, anchor, manual);


	//regenerate the box order array and save to cookie
	this.regenerateBoxOrder();

	//return false for success, in case we need a value for the api
	return true;
};


//physically swap two boxes over, in response to keyboard action
dbxGroup.prototype.swapTwoBoxes = function(original, selected, anchor, manual, positive)
{
	//if a beforestatechange function is defined
	if(typeof dbx.onbeforestatechange != 'undefined')
	{
		//compile the data for, and dispatch, any onbeforestatechange function that exists
		//then store the r	esulting action flag to an actions object
		var actions = dbx.compileAndDispatchOnBeforeStateChange(
			['proceed', this, this.container, this.gid, original,
				//since this is a swap action,
				//then if this is positive movement and linear orientation
				//we need to convert this target to next sibling box,
				(this.orientation != 'freeform' && positive ? dbx.getSiblingBox(selected, 'nextSibling') : selected),
				(this.orientation == 'freeform' ? 'swap' : 'move')
				]
			);

		//if the function returned false
		//return false here in case we need to pass that back up to api
		if(!actions.proceed) { return false; }
	}


	//update the rule pointer as necessary
	if(this.functionExists('_updateRulePointer')) { this._updateRulePointer(); }


	//the first animation is on the box that we've selected
	//the second animation is on the box we've moving (parent)

	//get both before positions of the boxes
	var selectedpos = { 'x' : selected.offsetLeft, 'y' : selected.offsetTop };
	var originalpos = { 'x' : original.offsetLeft, 'y' : original.offsetTop };

	//make them pre invisible ahead of the animator, so you don't ever see
	//a brief snatch of them visible in their new positions, before movement
	original.style.visibility = 'hidden';
	selected.style.visibility = 'hidden';

	//remove any target class name from the target box
	selected = dbx.removeClass(selected, 'dbx\-box\-target');

	//swap the boxes over
	//--- Thanks to jkd for this swapNode re-creation ---//
	var next = selected.nextSibling;
	if(next == original)
	{
		selected.parentNode.insertBefore(original, selected);
	}
	else
	{
		original.parentNode.insertBefore(selected, original);
		next.parentNode.insertBefore(original, next);
	}


	//create new box animators for the two boxes
	//do the original box second, because we want that to be on top
	//and pass false as the manual flag for the first, so that it doesn't capture focus
	new dbxAnimator(this, selected, selectedpos, this.resolution, true, null, false);
	new dbxAnimator(this, original, originalpos, this.resolution, true, anchor, manual);


	//regenerate the box order array and save to cookie
	this.regenerateBoxOrder();


	//return true for success in case we need
	//a value to pass back up to the api
	return true;
};




//create a tooltip for keyboard navigation instructions
dbxGroup.prototype.createTooltip = function(text, anchor, okay, cname)
{
	//if the action is okay
	if(okay)
	{
		//create the tooltip inside the group container
		//it's here so that it comes out above all the boxes
		this.tooltip = this.container.appendChild(dbx.createElement('span'));
		this.tooltip.style.visibility = 'hidden';
		this.tooltip.className = 'dbx-tooltip';

		//create and append the tooltip text
		this.tooltip.appendChild(document.createTextNode(text));

		//iteratively find a reference to the anchor's parent box
		var parent = dbx.getTarget(null, 'dbx\-box', anchor);

		//set tooltip position to box origin
		//so developers can move it from there, eg, with margin
		//need to int the value, in case it comes out as a float (which it might)
		this.tooltip.style.left = parseInt(parent.offsetLeft, 10) + 'px';
		this.tooltip.style.top = parseInt(parent.offsetTop, 10) + 'px';


		//get the true position of the tooltip with respect to the page
		var position = dbx.getPosition(this.tooltip);

		//get the widths of the viewport and tooltip
		var viewsize = dbx.getViewportWidth();
		var tipsize = this.tooltip.offsetWidth;

		//if the left position plus tooltip width is greater than the viewport width
		//reposition the tooltip by the necessary amount
		if(position.left + tipsize > viewsize)
		{
			this.tooltip.style.left = parseInt(parent.offsetLeft - (position.left + tipsize - viewsize), 10) + 'px';
		}

		//show the tooltip on a timer so it's not in your face
		//we could do this by conditionalising the whole process
		//and only creating tooltips after event-discriminated timeouts
		//but this is a great deal simpler, and nobody will notice the difference :)
		var tooltip = this.tooltip;
		window.setTimeout(function()
		{
			//check it's still here, in case it's been removed in the meantime
			if(tooltip != null) { tooltip.style.visibility = 'visible'; }

		}, 400);
	}
};

//remove such a tooltip, if it's there
dbxGroup.prototype.removeTooltip = function()
{
	//if there's a tooltip
	if(this.tooltip)
	{
		//remove it and nullify the reference
		this.tooltip.parentNode.removeChild(this.tooltip);
		this.tooltip = null;
	}
};



//hover method sets and clears box hover class from a single mousemove handle
//which is rather slick, though I do say so myself :D
dbxGroup.prototype.hover = function(e)
{
	//if the keydown flag is not set (so this is not triggered by spatial navigation in opera)
	//or this is konqueror and the mousedown flag is set
	if(!this.keydown || (dbx.kde && !this.mouseisdown))
	{
		//implement the box hover class whenever the mouse moves over a handle
		//this matches the behavior of the focus class by only appearing
		//when the mouse is over a target that can move the box
		var found = false, target = typeof e.target != 'undefined' ? e.target : e.srcElement;
		for(var i=0; i<this.handles.length; i++)
		{
			if(this.contains(this.handles[i], target))
			{
				found = true;
				var parentbox = dbx.getTarget(null, 'dbx\-box', this.handles[i]);
				if(!dbx.hasClass(parentbox, 'dbx\-box\-hover'))
				{
					if(typeof this.hoverbox != 'undefined')
					{
						this.hoverbox = dbx.removeClass(this.hoverbox, 'dbx\-box\-hover');
					}
					this.hoverbox = parentbox;
					parentbox.className += ' dbx-box-hover';
				}
				break;
			}
		}
		if(!found)
		{
			if(typeof this.hoverbox != 'undefined')
			{
				this.hoverbox = dbx.removeClass(this.hoverbox, 'dbx\-box\-hover');
				delete this.hoverbox;
			}
		}
	}
};


//refresh method re-initializes a group after load time
//this is primarily for the API,
//but it's also vital to moving boxes between groups
//(which is why it's in this script, not dbx.remotes)
dbxGroup.prototype.refresh = function(recover)
{
	//don't continue if the script is unsupported
	if(!dbx.supported) { return; }

	//recover flag is whether to recover a state from cookie
	//if this is being used internally or mid-view then there's no point
	//it would be wasteful, and internally would conflict
	//with the code that recovers dynamic groups onload
	//however it might be needed as a user argument to recover
	//the state of a group that was built dynamically external to the program
	//(like in the addremove demo)
	//if the argument is undefined, default to false
	if(typeof recover == 'undefined') { recover = false; }

	//get all elements within this container
	this.eles = dbx.get('*', this.container);

	//look for and remove any existing dummy elements
	//because it will be added again when we re-initialize with initBoxes
	for(var i=0; i<this.eles.length; i++)
	{
		if(dbx.hasClass(this.eles[i], 'dbx\-dummy'))
		{
			this.container.removeChild(this.eles[i]);
		}
	}

	//now re-initialise the boxes in this group as per recover flag
	//and with true for the flag that says to look for sparetokens
	this.initBoxes(recover, true);

	//then regenerate the box order array (from which, save back to cookie)
	this.regenerateBoxOrder();
};


//check whether an optional function exists
dbxGroup.prototype.functionExists = function(cname)
{
	return typeof this[cname] == 'function';
};




//docking box mousedown handler
dbxGroup.prototype.mousedown = function(e, box, handle, override)
{
	//note: the handle and override arguments are always undefined
	//unless this was called from the manager-level mousemove handler
	//ie, it's a triggering even after having moved a box between groups
	//and we need to contrive some of the values to make it all go smoothly

	//store the target node, converting event argument as we go
	//or use the handle override value if that's defined
	var node = typeof handle != 'undefined' ? handle : typeof e.target != 'undefined' ? e.target : e.srcElement;

	//if it's a text node, convert refence to its parent
	//this is for safari, in which events can come from text nodes
	if(node.nodeName == '#text') { node = node.parentNode; }

	//if the target is not a toggle, box or group
	if(!dbx.hasClass(node, 'dbx\-(toggle|box|group)'))
	{
		//while target doesn't contain docking box handle classname
		//set reference upwards until we find it
		//this is so that the handle can contain inner elements
		//but stop if we get to a box or group
		//to filter out any remaining events that started from higher than the handle
		while(!dbx.hasClass(node, 'dbx\-(handle|box|group)'))
		{
			node = node.parentNode;
		}
	}
	
	//if target is a handle or a toggle, 
	if(dbx.hasClass(node, 'dbx\-(toggle|handle)'))
	{
		box.className += ' dbx-box-active';
	}

	//if the box is not ungrabbale and the target is a handle
	if(!dbx.hasClass(box, 'dbx\-nograb') && dbx.hasClass(node, 'dbx\-handle'))
	{
		//clear any residual dialog
		this.clearDialog();

		//remove any focus class name from parent box
		box = dbx.removeClass(box, 'dbx\-box\-focus');

		//remove any tooltip that's there
		this.removeTooltip();


		//set the "released" flag, initially to false
		//which is used to detect whether a box has already moved once
		//or this is the first time it's been released
		//we'll need this as part of sticky box / drag threshold evaluations
		this.released = false;

		//store initial mouse coords
		this.initial = { 'x' : e.clientX, 'y' : e.clientY };


		//if override values are defined
		if(typeof override != 'undefined')
		{
			//adjust the initial mouse coords by an inverse of the override values
			//so that the clone and sticky position is updated accordingly
			this.initial.x += (0 - override.x);
			this.initial.y += (0 - override.y);
		}

		//reset the current mouse coords object
		this.current = { 'x' : 0, 'y' : 0 };


		//create a moveable clone of this box, also passing the source value
		this.createCloneBox(box, 'mouse');

		//prevent default action to try to stop text range selection while dragging
		if(typeof e.preventDefault != 'undefined' ) { e.preventDefault(); }

		//prevent textrange selection in IE
		//by temporarily suppressing it on the whole document
		if(typeof document.onselectstart != 'undefined')
		{
			document.onselectstart = function() { return false; }
		}
	}
};


//group-level mousemove handler
dbxGroup.prototype.mousemove = function(e)
{
	//if dragging is not okay and we have a residual keyboard-generated dialog, remove it
	//this cleans up after a situation where a dialog can be created
	//if you just shift-click on a header then don't move it
	//but because it checks the source flag, it ensures that a 
	//key-generated dialog can't be removed by mouse movement
	if(!this.dragok && (this.dialog && this.dialog.source != 'keyboard'))
	{
		//clear the dialog
		this.clearDialog();

		//remove any tooltip that's there
		this.removeTooltip();

		//remove any target or active class names from the boxes in this group
		this.removeActiveClasses('dbx\-box\-(target|active)');
	}

	//if dragging is okay and we have a box reference
	if(this.dragok && this.box)
	{
		//get the current direction of movement
		this.direction = e.clientY == this.current.y
			? (e.clientX > this.current.x ? 'right' : 'left')
			: (e.clientY > this.current.y ? 'down' : 'up');

		//store the current mouse coords
		this.current = { 'x' : e.clientX, 'y' : e.clientY };

		//store the total difference from the initial coordinates
		var overall = { 'x' : this.current.x - this.initial.x, 'y' : this.current.y - this.initial.y };

		//if the differences are both less than or equal to the drag threshold
		//even out to zero, which creates a "stickiness" around the origin
		if
		(
			((overall.x >= 0 && overall.x <= this.threshold) || (overall.x <= 0 && overall.x >= 0 - this.threshold))
			&&
			((overall.y >= 0 && overall.y <= this.threshold) || (overall.y <= 0 && overall.y >= 0 - this.threshold))
		)
		{
			this.current.x -= overall.x;
			this.current.y -= overall.y;
		}
		
		//if this box has already been released, or one of the differences has changed past the drag threshold
		//(having a drag threshold is so that handles can also be links or other actuators without conflict
		// because no-one holds the mouse perfectly still when they click a link)
		if(this.released || overall.x > this.threshold || overall.x < (0 - this.threshold) || overall.y > this.threshold || overall.y < (0 - this.threshold))
		{
			//store values to dbx properties for external methods
			dbx.dbxobject = this;
			dbx.group = this.container;
			dbx.sourcebox = this.box;
			dbx.clonebox = this.boxclone;
			dbx.event = e;

			//if onboxdrag doesn't exist or returns true
			if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag())
			{
				//set the released flag, to say this can always happen from now on
				//otherwise, after moving the box away once,
				//the subsequent tests would make it sticky
				//to the threshold points instead of the origin
				this.released = true;

				//move the clone to mouse coords minus mouse/position difference
				//if we're restricting the axis of movement in a linear direction,
				//only change the applicable position value
				//need to int the value, in case it comes out as a float (which it might)
				if(this.restrict != 'vertical' || this.orientation == 'horizontal')
				{
					this.boxclone.style.left = parseInt(this.current.x - this.difference.x, 10) + 'px';
				}
				if(this.restrict != 'horizontal' || this.orientation == 'vertical')
				{
					this.boxclone.style.top = parseInt(this.current.y - this.difference.y, 10) + 'px';
				}

				//if axis restriction is freeform we want to
				//reset any actions that take the box centerpoint outside the container
				if(this.restrict == 'freeform')
				{
					//get the center point of the clone relative to the container
					var clonepoint = {
						'x' : this.boxclone.offsetLeft + (this.boxclone.offsetWidth / 2),
						'y' : this.boxclone.offsetTop + (this.boxclone.offsetHeight / 2)
						};

					//define a proportion of the box dimensions to allow for
					//the same sensitvity threshold as the swapping action
					//that defines when a box is "well inside".
					//we need this discrimination to avoid a strip of insensitivity
					//that could amount to a way of cheating in gaming applications
					//and in fact we're going to use a higher proportion here: 0.2 instead of 0.1
					//to overlap the regions and remove all possibility of manipulating it this way
					var proportion = 0.2;
					var hypotonuse = Math.round(Math.sqrt(Math.pow(this.boxclone.offsetWidth, 2) + Math.pow(this.boxclone.offsetHeight, 2)));

					//if the point goes outside the container
					if(clonepoint.x < 0 || clonepoint.x > (this.container.offsetWidth - proportion * hypotonuse)
						|| clonepoint.y < 0 || clonepoint.y > (this.container.offsetHeight - proportion * hypotonuse))
					{
						//pass event to mouseup handler
						this.mouseup(e);

						//then return true to complete this action
						//we need to do this to avoid any errors due to bad event sychronisation
						//(eg, further mousemove occuring during the mouseup process
						// at a time when the box reference no longer exists)
						return true;
					}
				}

				//move the original box to new position, as per confirm mode
				this.moveBoxByMouse(this.current.x, this.current.y, this.confirm);

				//prevent default action to try to stop text range selection while dragging
				if(typeof e.preventDefault != 'undefined' ) { e.preventDefault(); }
			}
		}
	}

	return true;
};


//document mouseup handler
dbxGroup.prototype.mouseup = function(e)
{
	//remove any target or active class names from the boxes in this group
	this.removeActiveClasses('dbx\-box\-(target|active)');

	//if we have a box reference
	if(this.box)
	{
		//if we have an dialog element we need to move the original box to position
		//and explicitly with false for the confirm parameter, so it physically happens
		if(this.dialog)
		{
			//if this dialog has a group reference property
			//and this dialog was not cloned from a box in this group
			//then we're dealing with confirm-based cross group transfer
			//ie, our original box needs to insert before/swap with the target referred to by the clone
			if(typeof this.dialog.group != 'undefined' && typeof this.boxes[dbx.getID(this.dialog)] == 'undefined')
			{
				//store the group and ibox references to pass the dbx.mousemove
				//that will tell it what objects to use for transfer
				var xgroup = this.dialog.group;
				var xinsert = xgroup.boxes[dbx.getID(this.dialog)];
			}

			//remove the dialog element and nullify the reference
			this.clearDialog();

			//if we had a group reference (now stored as xgroup)
			if(typeof xgroup != 'undefined')
			{
				//pass event and this to the manager level mousemove handler
				//along with explicit group and insert reference
				dbx.mousemove(e, this, xgroup, xinsert);

				//don't go any further here
				//because the mousemove will handle the rest
				return;
			}

			//then move the box, with confirm mode false so a movement definitely happens
			this.moveBoxByMouse(e.clientX, e.clientY, false);
		}

		//remove the clone box
		this.removeCloneBox();

		//regenerate the box order array and save to cookie
		this.regenerateBoxOrder();

		//release textrange selection in IE
		if(typeof document.onselectstart != 'undefined')
		{
			document.onselectstart = function() { return true; }
		}
	}

	//clear any residual dialog
	this.clearDialog();

	//reset drag ok flag
	this.dragok = false;
};




//toggle click handlers
dbxGroup.prototype.click = function(e, anchor)
{
	//if the has focus flag is strictly true or null
	if(anchor.hasfocus === true || anchor.hasfocus === null)
	{
		//if we have an dialog element we need to move the box to position (if confirmed)
		if(this.dialog)
		{
			//get a reference to the original box, derived from anchor
			var box = dbx.getTarget(null, 'dbx\-box', anchor);

			//get the target box id from the dialog
			var dbxid = dbx.getID(this.dialog);

			//otherwise store the selected box reference as the box that
			//the dialog relates to, from its own group as normal
			var targetbox = this.boxes[dbxid];

			//if the exchange mode is insert and confirm mode is true and this is positive movement
			//we want its next sibling [effectively, next sibling box or dummy]
			//because we're going to want to insert after, not before,
			//which of course translates to insert before the next one
			//if we don't do this then moving to the right becomes impossible
			//and moving down is counter-intuitive
			//however if we run out of next siblings without finding a box
			//we'll just have to maintain the previous target
			if(this.exchange == 'insert' && this.confirm == true && this.positive == true)
			{
				targetbox = dbx.get('nextSibling', targetbox);
				if(!targetbox) { targetbox = this.boxes[dbxid]; }
			}
			
			//the action was confirmed if the dialog has the yes classname
			var confirmed = dbx.hasClass(this.dialog, 'dbx\-dialog\-yes');

			//remove the dialog element and nullify the reference
			this.clearDialog();

			//remove any tooltip that's there
			this.removeTooltip();

			//if a target box is defined, and not the original box, and the action is confirmed
			if(typeof targetbox != 'undefined' && targetbox != box && confirmed == true)
			{
				//if the exchange mode is swap, physically swap the two boxes passing the original box,
				//target box, and original box anchor
				//and the argument that says anchor is a manual interaction
				//and the argument for whether this is positive or negative
				//which can be false because only linear orientation requires it
				//(to use for evaluating onbeforestatechange properties)
				if(this.exchange == 'swap')
				{
					this.swapTwoBoxes(box, targetbox, anchor, true, false);
				}
	
				//otherwise insert the original box before or after the selected box 
				//and return the return value of that back up to the api
				else
				{
					return this.insertTwoBoxes(box, targetbox, anchor, true, false);
				}
			}
		
			//return so we don't activate the toggle
			return false;
		}

		//remove any tooltip that's there
		this.removeTooltip();

		//toggle box state and save
		//with the anchor object, and argument that says this is a manual interaction
		//and a null final argument that means toggle, don't force a state
		this.toggleBoxState(anchor, true, true, null);
	}

	return false;
};



//toggle or handle keypress handlers
dbxGroup.prototype.keypress = function(e, anchor)
{
	//get a reference to the anchor's parent box
	var parentbox = dbx.getTarget(null, 'dbx\-box', anchor);
	
	//if the keyCode is one of the arrow keys
	if(/^(3[7-9])|(40)$/.test(e.keyCode.toString()))
	{
		//if this is opera and the shift key is pressed
		//return true to allow natural mouse behaviors, not box movement
		//so that we don't conflict with spatial navigation
		if(dbx.opera && e.shiftKey) { return true; }

		//if the parent box isn't an ungrabbable object
		if(!dbx.hasClass(dbx.getTarget(null, 'dbx\-box', anchor), 'dbx\-nograb'))
		{
			//add the active class to the parent box
			parentbox.className += ' dbx-box-active';
			
			//remove any tooltip that's there
			this.removeTooltip();
	
			//store compass direction from keycode
			var direction = '';
			switch(e.keyCode)
			{
				case 37 :
					direction = 'W';
					break;
				case 38 :
					direction = 'N';
					break;
				case 39 :
					direction = 'E';
					break;
				case 40 :
					direction = 'S';
					break;
			}
	
			//wait a short interval before proceeding
			//so we have time to get two key direction presses
			//that indicate diagonal movement
			//it can't be too short, however, or it will be
			//drowned out by the keydown repeat rate (hence, too difficult to activate)
			var wait = 75;
	
			//if a current key direction is already defined
			//and not the same as the current direction
			if(this.currentdir && this.currentdir != direction)
			{
				//add it to the direction value in lower case
				direction += this.currentdir.toLowerCase();
	
				//convert inverted values
				switch(direction)
				{
					case 'En' : direction = 'Ne'; break;
					case 'Es' : direction = 'Se'; break;
					case 'Wn' : direction = 'Nw'; break;
					case 'Ws' : direction = 'Sw'; break;
				}
	
				//clear the timer
				clearTimeout(this.keytimer);
	
				//and set the movement to happen immediately
				wait = 0;
			}
	
			//otherwise define it as the current direction
			else
			{
				this.currentdir = direction;
			}
	
	
			//store a reference to this
			var self = this;
	
			//start the timer
			this.keytimer = setTimeout(function()
			{
				//ignore conflicting direction values, like "Ns"
				//which can happen if you've pressed several keys at once
				//or are generally sloppy with keypress accuracy (ie, normal!)
				if(!/^(Ns|Sn|Ew|Ww)$/.test(direction))
				{
					//if we already have a dialog
					if(self.dialog)
					{
						//get the box id from the clone
						var dbxid = dbx.getID(self.dialog);
	
						//set the box reference to the box that
						//the dialog relates to, from its own group as normal
						//this is what allows the dialog to move further than 1 block away before swapping
						var box = self.boxes[dbxid];
					}
	
					//otherwise get a reference to the box this anchor is inside
					else
					{
						box = dbx.getTarget(null, 'dbx\-box', anchor);
					}
					
					//then pass event, anchor, box, direction and confirm mode to shift box position method
					//along with the flag that says this move hasn't been tested against rules yet
					//we have to defer the testing in this situation
					//because we don't know the box target until we've tried to move
					//but we can't move until we've tested the target...
					//pas the argument that says this is a manual interaction
					self.moveBoxByKeyboard(e, anchor, box, direction, self.confirm, true);
				}
	
			}, wait);
	
	
			//prevent default action if that's supported
			//otherwise return false (resulting in the same effect in ie)
			if(typeof e.preventDefault != 'undefined') { e.preventDefault(); }
			else { return false; }
	
			//stop event bubbling, because in safari events can come from text nodes
			//and without this bubble control each keyup would call the function twice
			//but since we're doing this, we should do it for everyone for the sake of consistency
			typeof e.stopPropagation != 'undefined' ? e.stopPropagation() : e.cancelBubble = true;
	
			//and since we're doing that, we also need to clear the keydown flag manually
			//because the event won't reach the document keyup handler which normally does that
			this.keydown = false;
		}
	}
	
	//or if this is konqueror, and an actuation key (enter or space)
	//and the target is a button/anchor element
	//pass the anchor to click handler and prevent default action
	//we're doing this because the enter key isn't firing button.onclick in konqueror
	else if(dbx.kde && e.target == anchor && (e.keyCode == 13 || e.keyCode == 32))
	{
		this.click(e, anchor);
		e.preventDefault();
	}
	
	//in any other case remove any target or active class names from the boxes in this group
	//this should catch tabbing or otherwise navigating away from an anchor or box
	//we can't use blur events for this reset because they happen programatically as well
	else
	{
		this.removeActiveClasses('dbx\-box\-(target|active)');
	}
	
	//then if this is any browser and an actuation key
	//add the active class back to this box
	if(e.keyCode == 13 || e.keyCode == 32)
	{
		parentbox.className += ' dbx-box-active';
	}
	

	return true;
};







//regenerate box order array, save to cookie and output to receiver method
dbxGroup.prototype.regenerateBoxOrder = function()
{
	//rebuild the order array
	this.order = [];

	//re-iterate through the elements in this column
	var len = this.eles.length;
	for(var j=0; j<len; j++)
	{
		//if it's a docking box, and not a clone or a dummy
		if(dbx.hasClass(this.eles[j], 'dbx\-box') && !dbx.hasClass(this.eles[j], 'dbx\-(clone|dummy)'))
		{
			//add its index (extracted from dbxid classname)
			//plus its open state (extracted from dbx-box-(open|closed) classname )
			this.order.push(dbx.getID(this.eles[j]) + (dbx.hasClass(this.eles[j], 'dbx\-box\-open') ? '+' : '-'));
		}
	}
	
	//save the order to this member of the dbx manager's save data object
	dbx.savedata[this.gid] = this.order.join(',');

	//save some references to dbx properties
	//for the onstatechange receiver method
	dbx.dbxobject = this;
	dbx.group = this.container;
	dbx.gid = this.gid;

	//update child classes
	this.updateChildClasses();

	//set a cookie and output to receiver method
	dbx.setCookieState();
};


//apply first-child and last-child class to the correct elements
dbxGroup.prototype.updateChildClasses = function()
{
	//create an array of box ids in their current DOM order
	var boxids = [], eles = dbx.get('*', this.container);
	for(var i=0; i<eles.length; i++)
	{
		if(dbx.hasClass(eles[i], 'dbx\-box') && !dbx.hasClass(eles[i], 'dbx\-(dummy|clone)'))
		{
			boxids.push(dbx.getID(eles[i]));
		}
	}

	//the children can then be referenced from that array
	var children = {
		'first' : boxids[0], 
		'last' : boxids[boxids.length - 1]
		};
		
	//iterate through the children
	for(var i in children)
	{
		if(dbx.unwanted(children, i)) { continue; }
		
		//get a box reference from the dbxid
		var box = this.boxes[children[i]];
		
		//remove any existing child class from wherever it is now
		if(this.child[i] != null)
		{
			//remove the child class
			this.child[i] = dbx.removeClass(this.child[i], i + '\-child');

			//if there's a drag clone and it matches this box, remove its child class now
			if(this.boxclone && dbx.getID(this.boxclone) == dbx.getID(this.child[i]))
			{
				this.boxclone = dbx.removeClass(this.boxclone, i + '\-child');
			}

			//nullify the reference
			this.child[i] = null;
		}
		
		//apply the class to this box and store it
		box.className += ' ' + i + '-child';
		this.child[i] = box;
		
		//if there's a drag clone and it now amtches this box, add the child class
		if(this.boxclone && dbx.getID(this.boxclone) == dbx.getID(this.child[i]))
		{
			this.boxclone.className += ' ' + i + '-child';
		}
	}
};


//create a clone
dbxGroup.prototype.createClone = function(box, zorder, position, cname, children, source)
{
	//create a clone and append it to the group container
	//(a dialog clone has false for the create with children argument)
	//it has to be appended to group container, not body
	//so that it inherits CSS just the same as the original box
	var clone = this.container.appendChild(box.cloneNode(children));
	
	//receord the source property for later reference
	//this will be used to prevent mouse movement
	//from unduly clearing keyboard generated dialogs
	clone.source = source;

	//add clone classname
	clone.className += ' dbx-clone';

	//is a class name is specified, add that as well
	if(cname != '')
	{
		clone.className += ' ' + cname;
	}

	//remove any focus class name
	//it's not needed, and would look ugly
	clone = dbx.removeClass(clone, 'dbx\-box\-focus');

	//re-inforce important styles
	clone.style.position = 'absolute';
	clone.style.visibility = 'hidden';

	//set z-index
	clone.style.zIndex = zorder;

	//move clone to superimpose original
	clone.style.left = parseInt(position.x, 10) + 'px';
	clone.style.top = parseInt(position.y, 10) + 'px';

	//set width and height same as original
	clone.style.width = box.offsetWidth + 'px';
	clone.style.height = box.offsetHeight + 'px';

	return clone;
};


//create a moveable clone of the original box
dbxGroup.prototype.createCloneBox = function(box, source)
{
	//original box object
	this.box = box;

	//get original box position
	this.position = { 'x' : this.box.offsetLeft, 'y' : this.box.offsetTop };

	//calculate mouse/position difference
	this.difference = { 'x' : (this.initial.x - this.position.x), 'y' : (this.initial.y - this.position.y) };

	//create a clone of the original box, including the dragclone class name
	//and passing the source property; set the index at the top of the stack
	this.boxclone = this.createClone(this.box, 30000, this.position, 'dbx-dragclone', true, source);

	//set move cursor
	this.boxclone.style.cursor = 'move';

	//dont hide the original / show the clone just yet
	//wait until it's confirmed to be moving
	//so that links will still work before the drag threshold

	//set drag ok flag
	this.dragok = true;
};


//remove a clone box
dbxGroup.prototype.removeCloneBox = function()
{
	//remove the clone
	this.container.removeChild(this.boxclone);

	//show the original
	this.box.style.visibility = 'visible';

	//nullify the reference
	this.box = null;
};


//remove all target and active class names from the boxes in a group
dbxGroup.prototype.removeActiveClasses = function(pattern)
{
	for(var i in this.boxes)
	{
		if(dbx.unwanted(this.boxes, i)) { continue; }

		this.boxes[i] = dbx.removeClass(this.boxes[i], pattern);
	}
};






//move a box (by conditions) with the mouse
dbxGroup.prototype.moveBoxByMouse = function(clientX, clientY, confirm)
{
	//if we're using freeform orientation
	if(this.orientation == 'freeform')
	{
		//get the center point of the clone
		var clonepoint = {
			'x' : clientX - this.difference.x + (this.boxclone.offsetWidth / 2),
			'y' : clientY - this.difference.y + (this.boxclone.offsetHeight / 2)
			};

		//create an array of differences with each comparison box
		var differences = [];
	}

	//else we're using linear orientation
	else
	{
		//get position and dimensions of the clone
		//xy is y for a vertical column and x for a horizontal row
		//wh is h for a vertical column and w for a horizontal row
		var cloneprops = {
			'xy' : this.orientation == 'vertical' ? clientY - this.difference.y : clientX - this.difference.x,
			'wh' : this.orientation == 'vertical' ? this.boxclone.offsetHeight : this.boxclone.offsetWidth
			};
	}


	//if hide source while dragging is enabled, hide the original
	if(dbx.hide) { this.box.style.visibility = 'hidden'; }

	//remove any target or active class names from the boxes in this group
	this.removeActiveClasses('dbx\-box\-(target|active)');

	//show the clone
	this.boxclone.style.visibility = 'visible';


	//for each box in the array
	for(var i in this.boxes)
	{
		//don't include members we don't care about
		if(dbx.unwanted(this.boxes, i)) { continue; }

		//if the orientation is freeform
		if(this.orientation == 'freeform')
		{
			//if the exchange mode is "swap", and the box is the dummy, then continue to
			//the next iteration, because we don't ever want to swap with that
			if(dbx.hasClass(this.boxes[i], 'dbx\-dummy') && this.exhange == 'swap') { continue; }

			//get the center point of the box
			var boxpoint = {
				'x' : this.boxes[i].offsetLeft + (this.boxes[i].offsetWidth / 2),
				'y' : this.boxes[i].offsetTop + (this.boxes[i].offsetHeight / 2)
				};

			//now we need to find the box with which to swap our original
			//to preserve the best intuitive sense of physical co-incidence
			//we're going to find that box by comparing the difference betweeen
			//the center point of our clone, and that of each box
			//the closest match (shortest hypotonuse) will be the one we want

			//work out the difference for this box
			//and add it to the array of differences including the i index
			//because we'll need that at the other end when it's sorted
			differences.push([i, Math.round(Math.sqrt(Math.pow(Math.abs(clonepoint.x - boxpoint.x), 2) + Math.pow(Math.abs(clonepoint.y - boxpoint.y), 2)))]);

			//if the boxes we're comparing are the same,
			if(this.boxes[i] == this.box)
			{
				//set the difference to a ridiculously high value,
				//so it gets sorted to the end and hence never selected
				differences[differences.length - 1][1] = Math.pow(10, 20);

				//if confirm is enabled or there's an dialog element
				if(confirm || this.dialog)
				{
					//pass the box to update dialog method
					//with no additional state class token, and no position override or group ref
					//and the mouse source flag
					this.updateDialog(this.box, '', null, null, 'mouse');
				}
			}
		}

		//else if the orientation is linear (vertical or horizontal)
		//we're re-arranging them by inserting new before old
		else
		{
			//get position and dimensions of the original box
			var boxprops = {
				'xy' : this.orientation == 'vertical' ? this.boxes[i].offsetTop : this.boxes[i].offsetLeft,
				'wh' : this.orientation == 'vertical' ? this.boxes[i].offsetHeight : this.boxes[i].offsetWidth
				};

			//the direction of movement is positive if it's moving down or right
			this.positive = this.direction == 'down' || this.direction == 'right';

			//if - the direction of movement is positive; and
			//	clone left/top plus clone width/height is greater than box left/top; and
			//	clone left/top is less than box left/top
			//or - the direction of movement is negative; and
			//	clone left/top is less than box left/top; and
			//	clone left/top plus clone width/height is greater than box left/top
			if
			(
				(this.positive && cloneprops.xy + cloneprops.wh > boxprops.xy && cloneprops.xy < boxprops.xy)
				||
				(!this.positive && cloneprops.xy < boxprops.xy && cloneprops.xy + cloneprops.wh > boxprops.xy)
			)
			{
				//we've found the box before which to insert our original
				//but if the boxes we're comparing are the same don't continue
				//this is to prevent redundent movement
				if(this.boxes[i] == this.box) { return; }

				//look for a next sibling of this box
				//and don't continue if there isn't one,
				//or it's the same as the box we're inserting before
				//so that we're not doing an action that would result in no change
				//this filtering improves efficiency generally,
				//and is necessary specifically to stabilize the animation
				//otherwise the multiple unecessary calls would overload the animation timers
				//and the result would be snap movement with no apparent transition
				var sibling = dbx.getSiblingBox(this.box, 'nextSibling');
				if(this.box == sibling || this.boxes[i] == sibling) { return; }

				//store the value of i as the box index we want, and stop
				var index = i;
				break;
			}
		}
	}


	//if the orientation is freeform
	if(this.orientation == 'freeform')
	{
		//sort the differences array numerically by its second member (difference)
		differences.sort(function(a, b) { return a[1] - b[1]; });

		//so the box index we want is now differences[0][0]
		index = differences[0][0];

		//the box we want then is boxes[index]
		var targetbox = this.boxes[index];
	
		//but if the exchange mode is insert and this is positive movement
		//we want its next sibling [effectively, next sibling box or dummy]
		//because we're going to want to insert after, not before,
		//which of course translates to insert before the next one
		//if we don't do this then moving to the right becomes impossible
		//and moving down is counter-intuitive
		//but in either case we have to save the original targetbox reference as well
		//because when we do hypotonuse comparision to see if we've moved enough for insert to happen
		//we need that to be on the nearer box, treating it as an insert-after reference
		var originaltargetbox = targetbox;
		if(this.exchange == 'insert' && (this.direction == 'down' || this.direction == 'right'))
		{
			targetbox = dbx.get('nextSibling', targetbox);
		}

		//if the boxes we're comparing are the same don't continue
		//this is to prevent redundent movement, and also
		//errors when the group contains only one visible box
		//to prevent it trying to swap with itself
		if(targetbox == this.box) { return; }

		//get 2d position and extent of the original target box
		//expressed as the position of each edge
		boxprops = {
			'left' : originaltargetbox.offsetLeft,
			'top' : originaltargetbox.offsetTop
			};
		boxprops.right = boxprops.left + targetbox.offsetWidth;
		boxprops.bottom = boxprops.top + targetbox.offsetHeight;

		//define a proportion where the center of the clone is "well inside" the original box
		//that proportion being a fraction of the box hypotonuse away from the center point
		//bringing this value closer to 1 will reduce the sensitivity of the swap detection
		//so a value of 1 would mean that no swaps ever occur
		//while a value of 0 would mean that there's no threshold limit and all registered swaps occur
		//doing this avoids excessive swapping and keeps the behaviors more intuitive
		//and makes it easier to do diagonal swapping rather than multiple horizontal and vertical shifts
		//we're going to have 0.1 for regular movement, or 0 for dialog tracking movement
		//(where it's counter-productive because it makes the caret reset too much, and it's harder to lock onto targets)
		var proportion = confirm || this.dialog ? 0 : 0.1;
		var hypotonuse = Math.round(Math.sqrt(Math.pow(originaltargetbox.offsetWidth, 2) + Math.pow(originaltargetbox.offsetHeight, 2)));

		//now check that the center point of the clone is well inside the original box (by that proportion)
		if(!(clonepoint.x > boxprops.left + (hypotonuse * proportion) && clonepoint.x < boxprops.right - (hypotonuse * proportion)
			&& clonepoint.y > boxprops.top + (hypotonuse * proportion) && clonepoint.y < boxprops.bottom - (hypotonuse * proportion)))
		{
			return;
		}

		//don't continue if this is the same as the lastmoved object and in the same direction
		//this is for the situation where you're moving a small box over a large one which completely encloses it
		//and so it could register the move, do it, then the center point could still be inside
		//and so it would register immediately again and re-perform the same animation
		//and potentially this could happens several times on the trot
		//so, by differeentiating like this, it means that we get nice discreet movement
		//that only happens multiple times if you initiate it by a change in drag direction
		if(this.last.box == targetbox && this.last.direction == this.direction)
		{
			return;
		}


		//now we need to compute a compass direction for this swap
		//that we can pass it to the rules engine to evaluate whether it's allowed
		//and also a total distance for the two objects in this swap
		//so we can use that to evaluate how many blocks away

		//so first get the center point of the original box (not the clone)
		var origpoint = {
			'x' : this.box.offsetLeft + (this.box.offsetWidth / 2),
			'y' : this.box.offsetTop + (this.box.offsetHeight / 2)
			};

		//then get the center point of the selected box
		boxpoint = {
			'x' : originaltargetbox.offsetLeft + (originaltargetbox.offsetWidth / 2),
			'y' : originaltargetbox.offsetTop + (originaltargetbox.offsetHeight / 2)
			};

		//get the blocks difference
		var blocks = this.getBlocksDifference(origpoint, boxpoint, this.box);

		//get the compass direction
		var compass = this.getCompassDirection(origpoint, boxpoint);
		
		//evaluate the rules for this direction, blocks, box and no rulekey override
		//and if we don't get permission
		if(this.functionExists('_testRules') && !this._testRules(compass, blocks, this.box, null))
		{
			//if confirm mode is enabled or we already have a dialog
			if(confirm || this.dialog)
			{
				//pass the box to update dialog method
				//passing the additional state class, and no position override or group ref
				//and the mouse source flag
				this.updateDialog(originaltargetbox, ' dbx-dialog-no', null, null, 'mouse');
			}

			//don't go any further
			return;
		}
		//otherwise we've got permission

		//if confirm mode is enabled or we already have a tracking dialog element
		if(confirm || this.dialog)
		{
			//if this box is not a dummy or a dialog,
			//apply the target class name to the box
			if(!dbx.hasClass(targetbox, 'dbx\-(dialog|dummy)'))
			{
				originaltargetbox.className += ' dbx-box-target';
			}

			//pass the box to update dialog method
			//passing the additional state class, and no position override or group ref
			//and the mouse source flag
			this.updateDialog(originaltargetbox, ' dbx-dialog-yes', null, null, 'mouse');

			//return so that we don't perform the movement
			return;
		}



		//if we get this far we're definitely going to perform a movement
		//so update the rule pointer as necessary
		if(this.functionExists('_updateRulePointer')) { this._updateRulePointer(); }


		//store this original box as the lastmoved object and direction
		this.last = {
			'box' : originaltargetbox,
			'direction' : this.direction
			};
	}

	//else if this is linear orientation	
	//save targetbox and originaltargetbox references
	else
	{
		var targetbox = this.boxes[index];
		var originaltargetbox = targetbox;
	}

	//if we don't have an index defined, don't continue
	if(typeof index == 'undefined') { return; }


	//if the original target is not a dummy or a dialog
	//apply the target class name to the box
	if(!dbx.hasClass(originaltargetbox, 'dbx\-(dialog|dummy)'))
	{
		originaltargetbox.className += ' dbx-box-target';
	}


	//if a beforestatechange function is defined
	if(typeof dbx.onbeforestatechange != 'undefined')
	{
		//compile the data for, and dispatch, any onbeforestatechange function that exists
		//then store the resulting action flag to an actions object
		var actions = dbx.compileAndDispatchOnBeforeStateChange(
			['proceed', this, this.container, this.gid, this.box, originaltargetbox,
				(this.orientation == 'freeform' ? this.exchange : 'move')
				]
			);

		//if the function returned false, don't continue
		if(!actions.proceed) { return; }
	}


	//if we're using freeform orientation
	if(this.orientation == 'freeform' )
	{
		//if the exchange mode is swap, the animation is on 
		//the visible object that will get shifted as a result of the shift
		//because that's the only one that visibly appears to have moved
		if(this.exchange == 'swap')
		{
			var visibox = originaltargetbox;
		}
		
		//otherwise it's on every box from the insertion point
		//to the end of the group (not including the box we're moving),
		//so we're going to have to create that collection as a numerically indexed array
		else
		{
			//then iterate through them [in order] to create to create the array
			var add = false, pointer = 0, theboxes = [], visiboxes = [];
			for(var i in this.boxes)
			{
				if(dbx.unwanted(this.boxes, i)) { continue; }
				
				theboxes.push(this.boxes[i]);
			}
			for(i=0; i<theboxes.length; i++)
			{
				if(theboxes[i] == this.box) { continue; }

				visiboxes.push(theboxes[i]); 
			}
		}
	}

	//else if we're using linear orientation
	else
	{
		//if the direction of movement is positive (ie, the original box is before the box we inserted before)
		//then the visibly-moved box is the previous sibling of the box we're inserting it before
		//but getSiblingBox has a catch test in case there isn't a previous sibling
		//which can happen if we're inserting it before the first box,
		//but the mouse movement is in a positive direction
		if(this.positive)
		{
			var visibox = dbx.getSiblingBox(targetbox, 'previousSibling');
		}

		//otherwise the visibly-moved box IS the box we inserted it before
		else
		{
			visibox = targetbox;
		}
	}

	//get the before positions of the visibly-moved box[es]
	if(typeof visiboxes != 'undefined')
	{
		var visiposes = [];
		for(i=0; i<visiboxes.length; i++)
		{
			visiposes.push({
				'x' : visiboxes[i].offsetLeft,
				'y' : visiboxes[i].offsetTop
				});
		}
	}
	else
	{
		var visipos = { 'x' : visibox.offsetLeft, 'y' : visibox.offsetTop };
	}


	//remove any target class name from the original target box
	originaltargetbox = dbx.removeClass(originaltargetbox, 'dbx\-box\-target');

	//get the pre-position of the original box
	var prepos = { 'x' : this.box.offsetLeft, 'y' : this.box.offsetTop };

	//if we're using freeform orientation, and
	//the exchange mode is "swap", swap the two boxes over
	if(this.orientation == 'freeform' && this.exchange == 'swap')
	{
		//--- Thanks to jkd for this swapNode re-creation ---//
		var next = targetbox.nextSibling;
		if(next == this.box)
		{
			targetbox.parentNode.insertBefore(this.box, targetbox);
		}
		else
		{
			this.box.parentNode.insertBefore(targetbox, this.box);
			next.parentNode.insertBefore(this.box, next);
		}
	}

	//otherwise [if the orientation is not freeform, 
	//or the exchange mode is insrt] perform insertion
	//which naturally moves all the subsequent boxes along one
	else
	{
		//move the box we're move before the target box
		this.container.insertBefore(this.box, targetbox);
	}


	//update child classes
	this.updateChildClasses();


	//update initial mouse co-ordinates values with the
	//difference between these positions and the pre-positions
	//so that the sticky region follows the static box
	this.initial.x += (this.box.offsetLeft - prepos.x);
	this.initial.y += (this.box.offsetTop - prepos.y);


	//if we have a collection of boxes, animate each one
	if(typeof visiboxes != 'undefined' && visiboxes.length > 0)
	{
		for(i=0; i<visiboxes.length; i++)
		{
			new dbxAnimator(this, visiboxes[i], visiposes[i], this.resolution, false, null, true);
		}
	}
	
	//or if we have one box and it's not the same as the original box we moved, animate that
	else if(visibox != this.box)
	{
		new dbxAnimator(this, visibox, visipos, this.resolution, false, null, true);
	}

	//if we're not in confirm mode, and not hiding the source box while dragging, 
	//animate the source box (the box we grabbed and moved) as well
	if(!this.confirm && !dbx.hide)
	{
		new dbxAnimator(this, this.box, prepos, this.resolution, false, null, true);
	}

};


//get a compass direction from point a to point b
dbxGroup.prototype.getCompassDirection = function(a, b)
{
	//begin wit N or S, since all diagonals also begin with that
	var compass = a.y < b.y ? 'S' : 'N';

	//but if the y points are the same then this is E or W
	if(a.y == b.y)
	{
		compass = a.x < b.x ? 'E' : 'W';
	}

	//otherwise if the original x point is less then this is Ne or Se
	else if(a.x < b.x)
	{
		compass += 'e';
	}

	//or of the original x point is greater then this is Nw or Sw
	else if(a.x > b.x)
	{
		compass += 'w';
	}
	//hence if they're the same, it remains N or S

	//return the direction
	return compass;
};


//get the blocks difference between two points for a given box size
dbxGroup.prototype.getBlocksDifference = function(a, b, box)
{
	//store the block differences using absolute values
	//here the big assumption is that all the blocks are the same size
	//hence this won't work correctly if that's significantly not the case
	var blocks = [
		parseInt(Math.abs(a.x - b.x) / box.offsetWidth, 10),
		parseInt(Math.abs(a.y - b.y) / box.offsetHeight, 10)
		];

	//sort the array numerically so the value order is predictable when we test it
	blocks.sort(function(a, b) { return a - b; });

	//return the values
	return blocks;
};


//update the dialog element for confirm-based movement
dbxGroup.prototype.updateDialog = function(box, state, position, group, source)
{
	//wait for a very brief, managed (single-thread) timeout, so that
	//we get a buffer to avoid visual flickering and erraticness
	if(this.buffer)
	{
		clearTimeout(this.buffer);
		this.buffer = null;
	}
	var self = this;
	this.buffer = setTimeout(function()
	{
		//get the selected box position
		var boxpos = { 'x' : box.offsetLeft, 'y' : box.offsetTop };

		//if we have a position override
		if(position)
		{
			//increase the positions by the override values
			boxpos.x += position.x;
			boxpos.y += position.y;
		}

		//if we already have an dialog element, remove it first and nullify the reference
		//we need to do this rather than just repositiong an existing one
		//so that it has the same meta-information as the box it relates to
		self.clearDialog();

		//create a dialog clone from the box, including the state class
		//set the z-index just below the original box clone
		//and set false for the clone-children argument, so that
		//our dialog is just an outline of the original box
		self.dialog = self.createClone(box, 29999, boxpos, 'dbx-dialog' + state, false, source);

		//don't preserve transient state box classes 
		self.dialog = dbx.removeClass(self.dialog, 'dbx\-box\-(target|active|hover|focus)');

		//show the element
		self.dialog.style.visibility = 'visible';

		//clear and nullify the buffer timer
		clearTimeout(self.buffer);
		self.buffer = null;

	}, 20);

};


//clear any residual dialog element
dbxGroup.prototype.clearDialog = function()
{
	if(this.dialog)
	{
		this.container.removeChild(this.dialog);
		this.dialog = null;
	}
};


//contains method evaluates whether one node contains another
dbxGroup.prototype.contains = function(outer, inner)
{
	if(inner == outer) { return true; }
	if(inner == null) { return false; }
	else { return this.contains(outer, inner.parentNode); }
};


//[re]focus a box anchor and associated stuff
dbxGroup.prototype.refocus = function(target)
{
	//[re]set focus on the target - usually an anchor/button, but sometimes a handle
	//use a silent try catch because it may fail if user CSS or JS makes the target unfocussable
	try { target.focus(); } catch(err){}
	
	//add the focus class back to the box, if necessary
	var box = dbx.getTarget(null, 'dbx\-box', target);
	if(!dbx.hasClass(box, 'dbx-box-focus'))
	{
		box.className += ' dbx-box-focus';
	}
};
















//animation object
function dbxAnimator(caller, box, pos, res, kbd, anchor, manual)
{
	//calling object
	this.caller = caller;
	
	//check the animation speed for safety in case it was change in API scripting
	if(this.caller.resolution == 0) { this.caller.resolution = 1; }

	//the box we're going to animate
	this.box = box;

	//timer object, initially null
	//so we can test its non-existence against null
	this.timer = null;

	//its position before moving
	var before = {
		'x' : pos.x,
		'y' : pos.y
		};

	//its new position
	var after = {
		'x' : this.box.offsetLeft,
		'y' : this.box.offsetTop
		};

	//if the values are not the same
	if(!(before.x == after.x && before.y == after.y))
	{
		//don't continue if the number of running timers
		//is greater than the number of boxes in this group
		//(minus one, so as not to count the dummy)
		if(dbx.running > this.caller.boxes.length - 1) { return; }

		//create a clone of the box,
		//set the z-index just below the original box clone
		var clone = this.caller.createClone(this.box, 29999, arguments[2], 'dbx-aniclone', true, 'animator');

		//make the clone visible
		clone.style.visibility = 'visible';

		//make the box invisible
		this.box.style.visibility = 'hidden';

		//calculate the change between the before and after positions
		//so it comes out as a negative number for movement upwards/leftwards
		var change = {
			'x' : after.x > before.x ? after.x - before.x : 0 - (before.x - after.x),
			'y' : after.y > before.y ? after.y - before.y : 0 - (before.y - after.y)
			};

		//then animate the clone from its zero position
		//for the amount specified by the direction specified at the set resolution
		this.animateClone(
			clone,
			before,
			change,
			res,
			kbd,
			anchor,
			manual
			);
	}
};



//animate a clone
dbxAnimator.prototype.animateClone = function(clone, current, change, res, kbd, anchor, manual)
{
	//reference to this
	var self = this;

	//timer counter so we know when it's finished
	var count = 0;

	//add to the number of running timers
	dbx.running ++;

	//start a perpetual timer
	this.timer = window.setInterval(function()
	{
		//increase counter
		count ++;

		//change current position by change divided by resolution
		current.x += change.x / res;
		current.y += change.y / res;

		//re-apply the clone position
		clone.style.left = parseInt(current.x, 10) + 'px';
		clone.style.top = parseInt(current.y, 10) + 'px';

		//if the counter has reached resolution
		if(count == res)
		{
			//abandon this timer and nullify the reference
			window.clearInterval(self.timer);
			self.timer = null;

			//deduct from the number of running timers
			dbx.running --;

			//remove the clone
			self.caller.container.removeChild(clone);

			//reshow and re-display the original box
			self.box.style.visibility = 'visible';
			//don't use hasClass here because it tests boundary as well, 
			//but here "dbxid" is only a fragment at the start of a subtring
			if(self.box.className.indexOf('dbxid') != -1)
			{
				self.box = dbx.removeClass(self.box, 'dbx\-dummy');
			}

			//copy its reference back to the boxes object
			self.caller.boxes[dbx.getID(self.box)] = self.box;

			//if this animation was keyboard initiated, and a manual interaction
			if(kbd && manual)
			{
				//send focus to the anchor, if it's not null and if its parent isn't hidden
				//and add the focus class vack to the box
				//we need to test the latter to prevent sending focus to a hidden anchor
				//which can happen if multiple animations overlap quickly,
				//such as from keydown events repeating
				if(anchor && anchor.parentNode.style.visibility != 'hidden')
				{
					//but do it on a instant timer because
					//the latency helps browsers to stably retain the focus
					//this was originally for opera 8's benefit
					//but may as well leave it here anyway
					setTimeout(function() { self.caller.refocus(anchor); }, 0);
				}

				//else if it is null but we're using toggles
				//(which can happen during programmatic movement)
				else if(self.caller.toggles)
				{
					//get a reference to the button
					var button = self.caller.buttons[dbx.getID(self.box)];

					//if there is one and it has an "isactive" flag
					if(button && typeof button.isactive != 'undefined')
					{
						//[if this is a manual interaction, which it is]
						//send focus back to the button
						//and add the focus class back to the box
						//otherwise the focus may get transferred to the clone
						//and then lost when the clone is destroyed
						self.caller.refocus(button);
					}
				}

				//or if it's null and we're not using toggles (likewise)
				else
				{
					//just attempt to focus the box itself
					//and add the focus class back to it
					if(typeof self.box.focus == 'function')
					{
						setTimeout(function() { self.caller.refocus(self.box); }, 0);
					}
				}
			}

			//if the onafteranimate function exists
			if(typeof dbx.onafteranimate == 'function')
			{
				//compile the data for and fire onafteranimate event
				//do it after a momentary timer to allow for any intense process
				//that's tied into the onafteranimate event
				setTimeout(function() { dbx.compileAndDispatchOnAfterAnimate(self.box, self.caller) }, 0);
			}
		}

		//if the onafteranimate function exists and the resolution is greater than one
		if(typeof dbx.onanimate == 'function' && self.caller.resolution > 1)
		{
			//compile the data for and fire onafteranimate event
			dbx.compileAndDispatchOnAnimate(self.box, clone, self.caller, count, res)
		}

	}, 20);
};









//DOM cleaner for IE
if(typeof window.attachEvent != 'undefined')
{
	//window unload listener
	window.attachEvent('onunload', function()
	{
		//relevant events
		var ev = ['mousedown', 'mousemove', 'mouseup', 'mouseout', 'click', 'keydown', 'keyup', 'focus', 'blur', 'selectstart', 'statechange', 'boxdrag', 'boxopen', 'boxclose', 'ruletest', 'afteranimate', 'beforestatechange', 'animate'];
		var el = ev.length;

		//for each item in the document.all collection
		var dl = document.all.length;
		for(var i=0; i<dl; i++)
		{
			//for each relevant event
			for(var j=0; j<el; j++)
			{
				//set it to null so it's garbage collected
				document.all[i]['on' + ev[j]] = null;
			}
		}
	});
}
