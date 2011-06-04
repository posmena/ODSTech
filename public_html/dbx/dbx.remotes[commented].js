// DBX3.0 :: Docking Boxes (dbx) >>> Remote Controls
// *****************************************************
// DOM scripting by brothercake -- http://www.brothercake.com/
// GNU Lesser General Public License -- http://www.gnu.org/licenses/lgpl.html
//******************************************************




//get box index from direct box reference
//used when reference argument to an API method is an object not an ID or dbxid
dbxGroup.prototype._getBoxIndex = function(ref)
{
	//our base collection of boxes is this group
	var collection = [this];

	//now for each group in the collection
	for(i=0; i<collection.length; i++)
	{
		//iterate through the relevant members of the boxes object
		for(var j in collection[i].boxes)
		{
			if(!dbx.unwanted(collection[i].boxes, j))
			{
				//if the reference is this box, return the index
				if(ref == collection[i].boxes[j])
				{
					return j;
				}
			}
		}
	}

	//if we get here then we didn't find a box
	//so return null for failure
	return null;
};


//API toggle method is for opening and closing boxes remotely
dbxGroup.prototype.toggle = function(ref, state)
{
	//return failure if the script is unsupported
	//or we don't have a box reference defined
	if(!dbx.supported || typeof ref == 'undefined') { return false; }

	//if the reference is an object then look for it in the boxes collection
	//and if we don't find it, return false for failure
	if(typeof ref == 'object')
	{
		ref = this._getBoxIndex(ref);
		if(!ref) { return false; }
	}

	//if state is undefined default to invert
	if(typeof state == 'undefined') { state = null; }
	
	//support string value "open", "close" and "toggle"
	switch(state)
	{
		case 'open' : state = true; break;
		case 'close' : state = false; break;
		case 'toggle' : state = null; break;
	}

	//the second parameter specifies whether to open the box
	//(true = open, false = close, null = auto)
	//but toggleBoxState needs a "start at x state then toggle" parameter
	//so false should be true, and visa versa, but null unaffected
	state = state === true ? false : state === false ? true : state;

	//assemble an array of buttons to toggle
	var collection = [];

	//if the ref is '*' then our collection is all buttons in this group
	if(ref == '*')
	{
		//so iterate through all the boxes in this group
		for(var i in this.boxes)
		{
			//..and if it's a member we're interested in
			if(!dbx.unwanted(this.boxes, i))
			{
				//..providing that the box has a corresponding button object
				if(typeof this.buttons[i] != 'undefined')
				{
					//add this button to the collection
					collection.push(this.buttons[i]);
				}
			}
		}
	}

	//otherwise our collection is a single element
	else
	{
		//providing that the referenced element and button object exist
		//store it as the single element in the collection
		if(typeof this.boxes[ref] != 'undefined' && typeof this.buttons[ref] != 'undefined')
		{
			collection.push(this.buttons[ref]);
		}
	}

	//if we have no elements in the array, return false for failure
	if(collection.length == 0) { return false; }

	//otherwise iterate through the elements and perform the action
	for(var i=0; i<collection.length; i++)
	{
		//if the state argument is not null (ie, this is open or close, not toggle)
		//check that it doesn't already have that state,
		if(state !== null && collection[i].className.indexOf('dbx-toggle-' + (state ? 'closed' : 'open')) != -1)
		{
			//and if it does, store false as the return result
			var result = false;
		}

		//otherwise we can proceed
		else
		{
			//pass the button to toggle box state and save
			//pass the argument that says this is a manual interaction, so that API events will fire
			//and pass the state argument to force the specified state, or not
			this.toggleBoxState(collection[i], true, true, state);

			//and finally store true as the return result
			result = true;
		}
	}

	//if we only have one element we can return by success of that action
	if(collection.length == 1) { return result; }

	//otherwise just return true for general success
	else { return true; }
};


//API move method is for remotely moving boxes in linear orientation
dbxGroup.prototype.move = function(ref, direction)
{
	//return failure if the script is unsupported
	//or we don't have a box reference defined
	if(!dbx.supported || typeof ref == 'undefined') { return false; }

	//if the reference is an object convert it to a box index
	//and if we don't find it, return false for failure
	if(typeof ref == 'object')
	{
		ref = this._getBoxIndex(ref);
		if(!ref) { return false; }
	}

	//if positive is undefined, default to true (right/down)
	if(typeof direction == 'undefined') { direction = true; }

	//if direction is a string value, convert to lower case for uniform checking
	if(typeof direction == 'string') { direction = direction.toLowerCase(); }

	//for linear orientation, convert string values
	//direction values "up"/"left" and "down"/"right"
	//compass values "n"/"w" and "s"/"e" or "north"/"west" and "south"/"east"
	//if we have a diagonal value here just discard the second part
	if(this.orientation != 'freeform')
	{
		direction = direction.split('-')[0];
		switch(direction)
		{
			case 'up' : 
			case 'left' : 
			case 'n' : 
			case 'w' : 
			case 'north' :
			case 'west' : 
				direction = false; 
				break;
				
			case 'down' : 
			case 'right' : 
			case 's' : 
			case 'e' : 
			case 'south' : 
			case 'east' :
				direction = true; 
				break;
		}
	}
	
	//for freeform orientation, convert direction values 
	//or longhand compass values to shorthand compass values
	else
	{
		
		switch(direction)
		{
			case 'up' :
			case 'north' :
				direction = 'N';
				break;
				
			case 'up-right' :
			case 'right-up' :
			case 'north-east' :
			case 'ne' :
				direction = 'Ne';
				break;
				
			case 'right' :
			case 'east' :
				direction = 'E';
				break;
				
			case 'right-down' :
			case 'down-right' :
			case 'south-east' :
			case 'se' :
				direction = 'Se';
				break;
				
			case 'down' :
			case 'south' :
				direction = 'S';
				break;
				
			case 'down-left' :
			case 'left-down' :
			case 'south-west' :
			case 'sw' :
				direction = 'Sw';
				break;
				
			case 'left' :
			case 'west' :
				direction = 'W';
				break;
				
			case 'up-left' :
			case 'left-up' :
			case 'north-west' :
			case 'nw' :
				direction = 'Nw';
				break;
		}
	}
	
	//if we have this box reference
	if(typeof this.boxes[ref] != 'undefined')
	{
		//if we have a button reference here, pass the reference as the button
		//if not, all the object will be used for is to find the parent box iteratively
		//which we can acheive by simply passing the original box reference again
		var button = typeof this.buttons[ref] != 'undefined' ? this.buttons[ref] : this.boxes[ref];

		//if the orientation is linear, re-convert the direction of this movement to a compass value
		if(this.orientation != 'freeform')
		{
			direction = this.orientation == 'vertical'
				? direction == true ? 'S' : 'N'
				: direction == true ? 'E' : 'W';
		}
		
		//call shift box position with a null event, this box
		//the compass direction specified, and no confirm mode
		//and the argument that says this is not a manual interaction
		//and return the return value back up to the caller
		return this.moveBoxByKeyboard(null, button, this.boxes[ref], direction, false, false);
	}

	//if we get here we didn't find all the elements we need
	//so return false for failure
	return false;
};


//API swap method is for remotely swapping boxes in freeform orientation
dbxGroup.prototype.swap = function(ref1, ref2)
{
	//return failure if the script is unsupported
	//or we don't have box references defined
	//or if the orientation is not freeform (where swap() is not allowed)
	if(!dbx.supported || typeof ref1 == 'undefined' || typeof ref2 == 'undefined' || this.orientation != 'freeform') { return false; }

	//if either of the references is an object then look for it in the boxes collection
	//and if we don't find it, return false for failure
	if(typeof ref1 == 'object')
	{
		ref1 = this._getBoxIndex(ref1);
		if(!ref1) { return false; }
	}
	if(typeof ref2 == 'object')
	{
		ref2 = this._getBoxIndex(ref2);
		if(!ref2) { return false; }
	}

	//if both the referenced elements exists in the boxes object
	if(typeof this.boxes[ref1] != 'undefined' && typeof this.boxes[ref2] != 'undefined')
	{
		//if we have a button reference here, pass the reference as the button
		//if not, all the object will be used for is to find the parent box iteratively
		//which we can acheive by simply passing the original box reference again
		var button = typeof this.buttons[ref1] != 'undefined' ? this.buttons[ref1] : this.boxes[ref1];

		//if the selected box is not the same as the original box
		if(ref1 != ref2)
		{
			//store values to dbx properties for external methods
			dbx.dbxobject = this;
			dbx.group = this.container;
			dbx.sourcebox = this.boxes[ref1];
			dbx.event = null;

			//if onboxdrag doesn't exist, or returns true
			if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag())
			{
				//physically swap the two boxes
				//passing the original box, selected box, and original box anchor
				//and the argument that says this is not a manual interaction
				this.swapTwoBoxes(this.boxes[ref1], this.boxes[ref2], button, false);

				//return true for success
				return true;
			}
		}

		//return false for failure
		return false;
	}

	//if we get here we didn't find all the elements we need
	//so return false for failure
	return false;
};


//API insert method is for moving any box to any position within the same group
//nb: although this was based on shared groups insert(), it's a completely different method now
//and should not be used as the basis for re-implementing cross group insert
dbxGroup.prototype.insert = function(ref1, option, ref2)
{
	//save the arguments array, because we'll need to write to it
	//and doing so isn't safe in all browsers (eg. older safari)
	//save it iteratively rather than a single assignment
	//to make sure we get a copy, not a reference
	for(var args = [], i=0; i<arguments.length; i++) { args.push(arguments[i]); }
	
	//return null for failure if the script is unsupported
	//or if we don't have all arguments defined
	//or if the orientation is not freeform (where insert() is not allowed)
	if(!dbx.supported 
		|| typeof ref1 == 'undefined' 
		|| this.orientation != 'freeform') { return null; }
		
	//if option is undefined. make it true
	if(typeof option == 'undefined') { var option = true; }

	//create an object to store the two target elements, 
	//and a reference to the group
	//once we've isolated and confirmed ref2 it will be
	//converted to an insertBefore reference for ref1
	var targets = {
		'ref1' : null,
		'ref2' : null,
		'group' : this
		};

	//the option determined whether to insert ref1 before or after ref2
	//we support string values "before" and "after", or boolean values
	//true (before) and false (after)
	switch(option)
	{
		case 'before' : option = true; break;
		case 'after' : option = false; break;
	}
	
	//if option is anything other than true or false now, make it true
	if(option != true && option != false) { option = true; }
	
	//if the second argument is undefined or null, 
	//get a reference at the very beginning or end of the whole group
	if(typeof ref2 == 'undefined' || ref2 == null)
	{
		if(option == true)
		{
			ref2 = dbx.getSiblingBox(this.container.firstChild, 'nextSibling');
		}
		else
		{
			ref2 = dbx.getSiblingBox(this.container.lastChild, 'previousSibling');
		}
		args[2] = this._getBoxIndex(ref2);
	}

	//if either of the references is an object then look for it in the boxes collection
	//and if we don't find it, return null for failure
	if(typeof ref1 == 'object')
	{
		ref1 = this._getBoxIndex(ref1);
		if(!ref1) { return null; }
	}
	if(typeof ref2 == 'object')
	{
		ref2 = this._getBoxIndex(ref2);
		if(!ref2) { return null; }
	}

	//for each ref (arguments 0 and 2)
	for(i=0; i<=2; i+=2)
	{
		//token reference to targets object
		//they're not in sync because I reckon the function is easier to use
		//if it goes insert{a -> before/after -> b}, rather than
		//(as would be more convenient to me) {a -> b -> before/after}
		var ref = i == 0 ? 'ref1' : 'ref2';
		
		//look for this reference as a box in this group
		if(typeof this.boxes[args[i]] != 'undefined')
		{
			//create an insert reference, converting to sibling for insert-after
			//as necessary for the second reference
			var tmp = this.boxes[args[i]];
			targets[ref] = option || ref == 'ref1' ? tmp : dbx.getSiblingBox(tmp, 'nextSibling');
		}
	}

	//if either of the targets are not defined, return null for failure
	if(!targets.ref1 || !targets.ref2) { return null; }

	//since we're inserting, the animation will be on every box from the insertion point
	//to the end of the group (not including the box we're moving, which is taken care of separately),
	//so we're going to have to create that collection as a numerically indexed array
	var add = false, pointer = 0, theboxes = [], visiboxes = [];
	for(i in this.boxes)
	{
		if(dbx.unwanted(this.boxes, i)) { continue; }
		
		theboxes.push(this.boxes[i]);
	}
	for(i=0; i<theboxes.length; i++)
	{
		if(theboxes[i] == targets.ref1) { continue; }
		
		visiboxes.push(theboxes[i]); 
	}


	//if the insertion element is actually the next sibling box of the primary box
	//or indeed, is exactly the same element, then no change will occur, so don't bother
	if(targets.ref2 != dbx.getSiblingBox(targets.ref1, 'nextSibling') && targets.ref2 != targets.ref1)
	{
		//store values to dbx properties for external methods
		//the group and container references are the original group
		dbx.dbxobject = targets.group;
		dbx.group = targets.group.container;
		dbx.sourcebox = targets.ref1;
		dbx.event = null;

		//if onboxdrag doesn't exist, or returns true
		if(typeof dbx.onboxdrag == 'undefined' || dbx.onboxdrag())
		{
			//if we're using the box animation effect in the end group (if the resolution is > 0)
			if(targets.group.resolution > 0)
			{
				//make the priimary box pre invisible ahead of the animator, so you don't ever see
				//a brief snatch of it visible in its new positions, before movement
				targets.ref1.style.visibility = 'hidden';

				//get the before positions of the visibly-moved box[es]
				//if we have a collection of them
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
				
				//get the before position of the box we're moving
				var beforepos = { 'x' : targets.ref1.offsetLeft, 'y' : targets.ref1.offsetTop };
			}


			//perform the insertion
			targets.group.container.insertBefore(targets.ref1, targets.ref2);

			//regenerate the box order array (from which, save back to cookie)
			targets.group.regenerateBoxOrder();

			//copy ref1 to clone reference, just so that
			//we have a consistent reference for dbxAnimator
			var clone = targets.ref1;


			//if we're using the box animation effect in the end group
			if(targets.group.resolution > 0)
			{
				//if we have a colleciton of animation trgets
				//cretae a new animator for each one [in its end group]
				if(typeof visiboxes != 'undefined' && visiboxes.length > 0)
				{
					for(i=0; i<visiboxes.length; i++)
					{
						new dbxAnimator(targets.group, visiboxes[i], visiposes[i], targets.group.resolution, true, null, false);
					}
				}
				
				//create a single box animator for the box we're moving
				new dbxAnimator(targets.group, clone, beforepos, targets.group.resolution, true, null, false);
			}


			//return the end group for success
			return targets.group;
		}

		//if we get here return false for failure
		return false;
	}


	//if we get this far then we didn't find everything we needed
	//so return null for failure
	return null;
};

