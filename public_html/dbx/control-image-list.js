//initialise the docking boxes manager
var manager = new dbxManager(
	'list',					// session ID [/-_a-zA-Z0-9/]
	'yes',					// enable box-ID based dynamic groups ['yes'|'no']
	'yes',					// hide source box while dragging ['yes'|'no']
	''						// toggle button element type ['link'|'button']
	);


//create a docking boxes group for the desktop area
var list_categories = new dbxGroup(
	'list_images', 			// container ID [/-_a-zA-Z0-9/]
	'vertical', 			// orientation ['vertical'|'horizontal'|'freeform'|'freeform-insert'|'confirm'|'confirm-insert']
	'5', 					// drag threshold ['n' pixels]
	'yes',					// restrict drag movement to container/axis ['yes'|'no']
	'10', 					// animate re-ordering [frames per transition, or '0' for no effect]
	'no', 					// include open/close toggle buttons ['yes'|'no']
	'', 					// default state ['open'|'closed']

	'', 					// word for "open", as in "open this box"
	'', 					// word for "close", as in "close this box"
	'',						// sentence for "move this box" by mouse
	'',						// pattern-match sentence for "(open|close) this box" by mouse
	'',						// sentence for "move this box" by keyboard
	'',						// pattern-match sentence-fragment for "(open|close) this box" by keyboard
	'',						// pattern-match syntax for title-attribute conflicts

	'',						// confirm dialog sentence for "selection okay"
	''						// confirm dialog sentence for "selection not okay"
	);



//onanimate method is used to create a cute opacity cross-fade :)
manager.onanimate = function()
{
	this.sourcebox.style.visibility = 'visible';
	var opacity = (1 / this.anilength) * this.anicount;
		
	this.sourcebox.style.opacity = opacity;
	this.clonebox.style.opacity = 1 - opacity;
};

manager.onstatechange = function()
{
	$('#list_sort').val(this.state);
	/*
	$.get("/manage/cats", { type: "ajax", func: "save_settings", list_sort: $('#list_sort').val() },
	    function(data){
			//$('#m00').html(data);						
	});
	*/
    return true;
};

$(document).ready(function(){
	$('a.remove_cat').each(function(){
		$(this).click(function(){
			var cat_id = $(this).attr('href');
			$.get("/manage/cats", { type: "ajax", func: "remove_cat", cat_id: cat_id },
			function(data){
				$('#listcat'+cat_id).hide('fast');
			});
			return false;
		});	
	});
});