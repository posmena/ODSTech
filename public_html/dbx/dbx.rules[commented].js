// DBX3.0 :: Docking Boxes (dbx) >>> Rules Engine
// *****************************************************
// DOM scripting by brothercake -- http://www.brothercake.com/
// GNU Lesser General Public License -- http://www.gnu.org/licenses/lgpl.html
//******************************************************


//define a rule for this group,
//or a subset of elements in this group defined by class name
dbxGroup.prototype.setRule = function(rule, cname)
{
	//all values are case sensitive, all whitespace is ignored
	//each rule expresses the allowed movement in a swap or sequence of swaps

	//the basic tokens are points on a compass:
	// N = North (or E, S or W)
	// Se = South-East (or Sw, Nw or Ne)
	// * (star) means "any direction"
	// , (comma) separates individual swaps in a sequence
	//eg, "NS, EW, NS" means "north or south, then east or west, then north or south"
	// {n} (braced number) after a swap indicates the maximum number of blocks the move can span
	//eg, "NS{3}" means "north or south up to three blocks" or "*{1}" means "up to one block in any direction"
	// $ (dollar) in a sequence means "the same direction as you took on the the previous step"
	//eg, "NE, $" means "north then north again, or east then east again", but not "north or east, then north or east"
	//you can also use numbers with $ to do things like "NE{1}, ${2}"
	//which means "north or east up to one block, then the same direction again up to two blocks"
	// | (vertical bar) separates multiple OR choices in a swap
	//eg, "NS{1} | E{2}" means "north or south for up to one block OR east for up to two blocks"

	//the extended tokens allow you to define shapes that the move should describe
	//the triangle syntax goes "T:1/1" where "T:" declares a triangle definition
	//and the two numbers refer to the block sizes of the a,b sides of a right-angled triangle
	//eg, "T:2/3" means "to a point that's two blocks in one direction and three in another"
	//the rules engine is not strict about the orientation of the triangle
	//so "T:2/3" and "T:3/2" are treated the same
	//plain numbers are treated as precise: 2 means "exactly two" not "up to two"
	//however you can specify imprecise numbers by surrounding them with braces
	//eg, "T:1/{2}" means "to a point that's one block in one direction and up to two blocks in another"
	//nb. you can describe a straight line by using 0 for one of the values
	//nb. you can describe a fan pattern by using imprecise numbers for both values
	//the triangle syntax can also go "T:2" or "T:{2}", which follow the same rules
	//except they describe a triangle where both sides must be the same length

	//if the orientation is not freeform, throw an exception and stop
	if(this.orientation != 'freeform') { throw('Error from setRule() method:\nThe rules engine cannot be used with "' + this.orientation + '" orientation'); return false; }

	//return failure if the script is unsupported
	//or if this group did not instantiate
	if(!dbx.supported || !this.container) { return false; }

	//remove any whitespace from the rule
	rule = rule.replace(/\s/g, '');

	//if the rule is invalid, throw an exception and stop
	if(!/^[NESWneswT0-9,\$\/\{\}\*\:\|\s]+$/.test(rule)) { throw('Error from setRule() method:\nThe rule "' + rule + '" contains invalid characters'); return false; }
	else if((/\{/.test(rule) || /\}/.test(rule)) && !/\{[0-9]+\}/i.test(rule)) { throw('Error from setRule() method:\nThe rule "' + rule + '" contains an invalid range token'); return false; }
	else if(/(\T)/.test(rule) && !/(\T\:)\{?[0-9]+\}?((\/)\{?[0-9]+\}?)?/.test(rule)) { throw('Error from setRule() method:\nThe rule "' + rule + '" contains an invalid triangle description'); return false; }
	else if(!/(\T)/.test(rule) && !/^((((N|E|S|W|Ne|Se|Sw|Nw)+)|([\*\$]))(\{[0-9]+\})?[,\|]?)+$/.test(rule)) { throw('Error from setRule() method:\nThe rule "' + rule + '" contains invalid compass values'); return false; }

	//convert inverted values
	rule = rule.replace(/En/g, 'Ne');
	rule = rule.replace(/Es/g, 'Se');
	rule = rule.replace(/Wn/g, 'Nw');
	rule = rule.replace(/Ws/g, 'Sw');

	//convert redundent and contradictory values
	rule = rule.replace(/N[ns]/g, 'N');
	rule = rule.replace(/E[ew]/g, 'E');
	rule = rule.replace(/S[sn]/g, 'S');
	rule = rule.replace(/W[we]/g, 'W');

	//split into multiple rules
	//(leaving us an array of one value if there is only one rule)
	rule = rule.split(',');

	//if the first value is a dollar, throw an exception and stop
	if(rule[0] == '$') { throw('Error from setRule() method:\nCannot use "$" as the first step in a sequence'); return false; }

	//if the last value is empty, throw and exception and stop
	if(rule[rule.length - 1] == '') { throw('Error from setRule() method:\nTrailing comma is not allowed in a rule'); return false; }

	//if a class name is not specified this is the global rule
	if(typeof cname == 'undefined')
	{
		//set the global rule
		this.rules.global = { 'pointer' : 0, 'rule' : rule, 'actual' : [] };

		//return true for success
		return true;
	}

	//or if a class is defined this is a subset rule
	else
	{
		//if the classname is invalid, throw an exception and stop
		if(!/^[-_a-zA-Z0-9]+$/i.test(cname) || cname == 'global') { throw('Error from setRule() method:\n"' + cname + '" is an invalid ruleset name'); return false; }

		//set the subset rule
		this.rules[cname] = { 'pointer' : 0, 'rule' : rule, 'actual' : [] };

		//return true for success
		return true;
	}

	//if we get here then something screwed up!
	//so return false for failure
	return false;
};


//remove a defined rule
dbxGroup.prototype.removeRule = function(cname)
{
	//return failure if the script is unsupported
	if(!dbx.supported) { return false; }

	//if a class name is not specified this is a global rule
	if(typeof cname == 'undefined')
	{
		//clear the global rule
		this.rules.global = { 'pointer' : 0, 'rule' : [], 'actual' : [] };

		//return true for success
		return true;
	}

	//or if a class is defined and its value is "*"
	//that means to delete all rules
	else if(cname == '*')
	{
		//which we do most effectively by rebuilding the default array
		this.rules = { 'global' : { 'pointer' : 0, 'rule' : [], 'actual' : [] } };

		//return true for success
		return true;
	}

	//otherwise this is a subset rule
	else
	{
		//if the classname is invalid, throw an exception and stop
		if(!/^[-_a-zA-Z0-9]+$/i.test(cname)) { throw('Error from removeRule() method:\n"' + cname + '" is an invalid ruleset name'); return false; }

		//if this rule does not exist, throw an exception and stop
		if(typeof this.rules[cname] == 'undefined') { throw('Error from removeRule() method:\nThe ruleset "' + cname + '" does not exist'); return false; }

		//delete the rule
		delete this.rules[cname];

		//return true for success
		return true;
	}

	//if we get here then something screwed up!
	//so return false for failure
	return false;
};


//update the rule pointer as necessary
dbxGroup.prototype._updateRulePointer = function()
{
	//if we have a rulekey set,
	//and we have more than one rule in the applicable rules array
	if(this.rulekey != '' && this.rules[this.rulekey].rule.length > 1)
	{
		//add this move to the actual moves
		this.rules[this.rulekey].actual.push(this.ruledir);

		//increase the pointer,
		this.rules[this.rulekey].pointer++;

		//clear the rulekey value
		this.rulekey = '';
	}
};


//test a direction against the rules to see if it's allowed
dbxGroup.prototype._testRules = function(direction, blocks, parent, rulekey)
{
	//if we have a rulekey override save that as the key
	if(rulekey)
	{
		var key = rulekey;
	}

	//otherwise we need to look for a value
	else
	{
		//store the classname as an array split from space-separated values
		//we know there will be at least two, because the ruleset class name has to be
		//added to the same element that is 'dbx-box'
		var cname = parent.className.split(' ');

		//now iterate through the value to see if we have a ruleset with this name
		for(var i=0; i<cname.length; i++)
		{
			if(typeof this.rules[cname[i]] != 'undefined')
			{
				var found = cname[i];
				break;
			}
		}

		//if we found one, save the key
		if(typeof found != 'undefined')
		{
			key = found;
		}

		//otherwise set it to 'global'
		else
		{
			key = 'global';
		}
	}


	//store universal dbx properties for onruletest function
	dbx.box = parent;

	//if we have no rules in the specified array
	if(this.rules[key].rule.length == 0)
	{
		//set null values for the dbx properties
		//that won't have values but reported to onruletest
		dbx.pointer = null;
		dbx.rule = null;
		dbx.pattern = null;
		key = null;

		//the movement is always allowed
		//(unless the onruletest function prevents it)
		var okay = true;
	}

	//otherwise proceed to evaluating the applicable rule(s)
	else
	{
		//store the key and direction
		this.rulekey = key;
		this.ruledir = direction;

		//if we have a lastparent defined (the last box that was tested)
		//and it's different from the box we're currently testing
		//OR if the pointer has gone past maximum,
		//reset it and the actual steps array
		if((typeof this.lastparent != 'undefined' && this.lastparent != parent)
			|| this.rules[key].pointer == this.rules[key].rule.length)
		{
			this.rules[key].pointer = 0;
			this.rules[key].actual = [];
		}

		//define the last parent as this box
		this.lastparent = parent;


		//copy the rule object
		ruleobj = this.rules[key];

		//store the pattern for this rule step
		//using the stored pointer value for sequence
		var pattern = ruleobj.rule[ruleobj.pointer];

		//if the value contains "$" then copy the last actual move
		//we know there will be one because we validated the input at setRule()
		//so we know that it's never the first value in a sequence
		if(pattern.indexOf('$') != -1)
		{
			pattern = pattern.replace('$', ruleobj.actual[ruleobj.actual.length - 1]);
		}


		//store values to dbx properties for external methods
		dbx.pattern = pattern;

		//assume by default that this match is not okay
		okay = false;


		//turn the pattern into an array of each one member
		//or multiple members split by a pipe symbol (or rules)
		pattern = pattern.split('|');

		//now iterate through the resulting array
		for(i=0; i<pattern.length; i++)
		{
			//if the pattern[i] describes a triangle
			if(pattern[i].indexOf('T:') != -1)
			{
				//remove the leading T: and split by / delimeter
				pattern[i] = pattern[i].replace('T:', '').split('/');

				//we're describing a triangle where both sides are not
				//(or not necessarily) the same length
				//unless we have only one value in the pattern, in which case
				//we're describing a 45 degree triangle, with both side the same length
				var same = false;
				if(pattern[i].length == 1)
				{
					same = true;
					pattern[i].push(pattern[i][0]);
				}

				//both numbers in the value are exact blocks values
				//unless they're surrounded by braces, in which case they're ranges
				var exact = [true, true];
				for(var j=0; j<2; j++)
				{
					if(/^\{[0-9]+\}$/.test(pattern[i][j]))
					{
						exact[j] = false;
						pattern[i][j] = pattern[i][j].replace(/[\{\}]/g, '');
					}
				}

				//sort the array numerically so it's the same shape as the blocks array
				//(even though the values aren't actually numbers, we don't need them to be)
				//*** are you sure about that? what if the pattern is 10,2?
				pattern[i].sort(function(a, b) { return a - b; });

				//if the shapes match
				if(
					((exact[0] && pattern[i][0] == blocks[0]) || (!exact[0] && blocks[0] <= pattern[i][0]))
					&&
					((exact[1] && pattern[i][1] == blocks[1]) || (!exact[1] && blocks[1] <= pattern[i][1]))
					)
				{
					//if the sides should be the same and are
					//or needn't be and it doesn't matter
					if((same && blocks[0] == blocks[1]) || !same)
					{
						//set the okay flag to true
						okay = true;
					}
				}

			}

			//otherwise it's a compass rule
			else
			{
				//if it contains a block number
				if(pattern[i].indexOf('{') != -1)
				{
					//split by the first brace
					var tmp = pattern[i].split('{');

					//save the first value (pattern[i]) back to pattern[i]
					pattern[i] = tmp[0];

					//save the second value (blocks) to blockrule,
					//trimming and converting to a number as we go
					var blockrule = parseInt(tmp[1], 10);
				}

				//split the pattern[i] by character fragments (like "N" or "Sw")
				tmp = [];
				for(j=0; j<pattern[i].length; j++)
				{
					var letter = pattern[i].charAt(j);
					if(letter.toUpperCase() == letter)
					{
						tmp.push(letter);
					}
					else
					{
						var len = tmp[tmp.length - 1].length;
						if(len < 2) { tmp[tmp.length - 1] += letter; }
						//nb. this means that if you use extraneous lower case letters they'll be ignored
						//for example, "NeeW" will come out as "NeW"
						else { continue; }
					}
				}
				//store the resultant array back to pattern[i]
				pattern[i] = tmp;

				//for each token in the pattern[i]
				for(j=0; j<pattern[i].length; j++)
				{
					//if the current direction matches this token
					//or the pattern value is '*', then it's okay
					if(direction == pattern[i][j] || pattern[i][j] == '*')
					{
						//set the okay flag to true, then break because we only need
						//one successful token match to know we're okay to proceed
						okay = true;
						break;
					}
				}

				//if we're okay and we have a blockrule
				//we need to test for the number of blocks
				if(okay && typeof blockrule != 'undefined')
				{
					//if either of the number of blocks in this move
					//is less than the value of the blockrule
					//set the okay flag back to false
					//(the blocks array is actually predictably sorted
					// so we should only have to test the largest value, blocks[1]
					// but testing both is more foolproof, in case the nature of the
					// blocks array should change in the future!)
					if(blocks[0] > blockrule || blocks[1] > blockrule) { okay = false; }
				}
			}

			//since | split conditions are OR rules
			//we don't want to check again unless we haven't got an okay so far
			//in case it's true now and then becomes false
			if(okay) { break; }
		}

		//store pointer and rule values to dbx property for onruletest
		dbx.pointer = this.rules[key].pointer;
		dbx.rule = ruleobj.rule.join(', ');
	}

	//if the onruletest function is undefined
	if(typeof dbx.onruletest == 'undefined')
	{
		//if we're okay, return true to allow the movement
		//otherwise return false so the move is disallowed
		return okay;
	}

	//otherwise if it is defined
	else
	{
		//create the properties available to it that are not already defined
		//some of which will be null if we have no rules
		dbx.dbxobject = this;
		dbx.group = this.container;
		dbx.sourcebox = this.box;
		dbx.ruleset = key;
		dbx.direction = direction;
		dbx.blocks = blocks;
		dbx.allowed = okay;

		//and return the value of the function,
		//so that it controls whether the action is allowed
		return dbx.onruletest();
	}

};



