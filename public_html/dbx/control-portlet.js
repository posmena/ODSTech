



//initialise the docking boxes manager
var manager = new dbxManager(
	'main',					// session ID [/-_a-zA-Z0-9/]
	'yes',					// enable box-ID based dynamic groups ['yes'|'no']
	'yes',					// hide source box while dragging ['yes'|'no']
	'button'				// toggle button element type ['link'|'button']
	);


//create new docking boxes group
var right_group = new dbxGroup(
	'right_group', 				// container ID [/-_a-zA-Z0-9/]
	'vertical', 			// orientation ['vertical'|'horizontal'|'freeform'|'freeform-insert'|'confirm'|'confirm-insert']
	'7', 					// drag threshold ['n' pixels]
	'no',					// restrict drag movement to container/axis ['yes'|'no']
	'10', 					// animate re-ordering [frames per transition, or '0' for no effect]
	'no', 					// include open/close toggle buttons ['yes'|'no']
	'open', 				// default state ['open'|'closed']

	'open', 										// word for "open", as in "open this box"
	'close', 										// word for "close", as in "close this box"
	'click-down and drag to move this box', 		// sentence for "move this box" by mouse
	'click to %toggle% this box', 					// pattern-match sentence for "(open|close) this box" by mouse
	
	'use the arrow keys to move this box. ', 		// sentence for "move this box" by keyboard
	'press the enter key to %toggle% this box. ',	// pattern-match sentence-fragment for "(open|close) this box" by keyboard
	
	'%mytitle%  [%dbxtitle%]', 						// pattern-match syntax for title-attribute conflicts

	'hit the enter key to select this target',		// confirm dialog sentence for "selection okay"
	'sorry, this target cannot be selected'			// confirm dialog sentence for "selection not okay"
	);


//create new docking boxes group
var left_group = new dbxGroup(
	'left_group', 				// container ID [/-_a-zA-Z0-9/]
	'vertical', 			// orientation ['vertical'|'horizontal'|'freeform'|'freeform-insert'|'confirm'|'confirm-insert']
	'7', 					// drag threshold ['n' pixels]
	'no',					// restrict drag movement to container/axis ['yes'|'no']
	'10', 					// animate re-ordering [frames per transition, or '0' for no effect]
	'no', 					// include open/close toggle buttons ['yes'|'no']
	'open', 				// default state ['open'|'closed']

	'open', 										// word for "open", as in "open this box"
	'close', 										// word for "close", as in "close this box"
	'click-down and drag to move this box', 		// sentence for "move this box" by mouse
	'click to %toggle% this box', 					// pattern-match sentence for "(open|close) this box" by mouse
	
	'use the arrow keys to move this box. ', 		// sentence for "move this box" by keyboard
	'press the enter key to %toggle% this box. ',	// pattern-match sentence-fragment for "(open|close) this box" by keyboard
	
	'%mytitle%  [%dbxtitle%]', 						// pattern-match syntax for title-attribute conflicts

	'hit the enter key to select this target',		// confirm dialog sentence for "selection okay"
	'sorry, this target cannot be selected'			// confirm dialog sentence for "selection not okay"
	);
manager.onstatechange = function()
{
	$('#debug').val(this.state);
	if($('#html5debug').val() != 'html5-drag')
	{
		save_layout_settings();
	}
    return true;
};

function save_layout_settings()
{
	var settings = $('#debug').val();
	$.get("/manage/layout", { type: "ajax", func: "save_layout", layout_settings: settings},
			function(data){
				$('#output').html(data);
				$('#html5debug').val('');
			});
}

function dropify()
{
	$('.dbx-box').unbind();
	$('.dbx-box')
		.attr('draggable', 'true')
		.bind('dragstart', function(ev) {
			var dt = ev.originalEvent.dataTransfer;
			dt.setData("dragged_id", ev.target.id);
			$('#html5debug').val('html5-drag');
			return true;
		})
		.bind('dragend', function(ev) {
			return false;
		});
	$('.drophere').unbind();
	$('.drophere')
		.bind('dragenter', function(ev) {
			$(ev.target).addClass('dragover');
			return false;
		})
		.bind('dragleave', function(ev) {
			$(ev.target).removeClass('dragover');
			return false;
		})
		.bind('dragover', function(ev) {
			return false;
		})
		.bind('drop', function(ev) {
			if (!$(ev.target).hasClass('drophere')) return true;

			var dt = ev.originalEvent.dataTransfer;
			//alert(dt.getData('Text'));
			//alert(ev.target.toSource());
			var org_id = dt.getData('dragged_id');
			$('#'+dt.getData('dragged_id')).clone().removeAttr('id').attr('id', 'temp123').appendTo('#'+ev.target.id);
			$('#'+dt.getData('dragged_id')).remove();
			//alert($('#temp123').attr('id'));
			$('#temp123').removeAttr('id').attr('id', org_id);
			//alert($('#' + org_id).attr('id'));
			right_group.refresh(false);
			left_group.refresh(false);
			dropify();
			save_layout_settings();
			return false;
		});

}


 $(document).ready(function() {
	dropify()
});