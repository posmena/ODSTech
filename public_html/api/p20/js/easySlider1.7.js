/*
 * 	Easy Slider 1.7 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
/*
 *	markup example for $("#slider").easySlider();
 *	
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {

	$.fn.easySlider = function(options){
	  
		// default configuration properties
		var defaults = {			
			prevId: 		'prevBtn',
			prevText: 		'Previous',
			nextId: 		'nextBtn',	
			nextText: 		'Next',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',	
			controlsFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',	
			lastText: 		'Last',
			lastShow:		false,				
			vertical:		false,
			speed: 			800,
			auto:			false,
			pause:			2000,
			continuous:		false, 
			numeric: 		false,
			numericId: 		'controls',
			overridewidth: 	true ,
			overrideheight: true
		}; 
		
		var options = $.extend(defaults, options);  
				
		this.each(function() {  
			var obj = $(this); 		
			
			var s = $("li", obj).length;
			var w = $("li", obj).width(); 
			var h = $("li", obj).height(); 
			var clickable = true;
			if( options.overridewidth == true ) {	
			obj.width(w); 
			}
			
			if( options.overrideheight == true ) {	
			obj.height(h); 
			}
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			if(!options.vertical) $("ul", obj).css('width',s*w);			
			if(options.vertical) $("ul", obj).css('height',s*h);			
			
			if(options.continuous){
				if(!options.vertical) $("ul", obj).prepend($("ul li:last-child", obj).clone().css("margin-left","-"+ w +"px"));
				 $("ul", obj).append($("ul li:nth-child(2)", obj).clone());
				 $("ul", obj).append($("ul li:nth-child(3)", obj).clone());
				 $("ul", obj).append($("ul li:nth-child(4)", obj).clone());
				$("ul", obj).append($("ul li:nth-child(5)", obj).clone());
				$("ul", obj).append($("ul li:nth-child(6)", obj).clone());
				$("ul", obj).append($("ul li:nth-child(7)", obj).clone());
				if(!options.vertical) $("ul", obj).css('width',(s+5)*w);
			};				
			
			if(!options.vertical) $("li", obj).css('float','left');
								
			if(options.controlsShow){
				var html = options.controlsBefore;				
				if(options.numeric){
					html += '<ol id="'+ options.numericId +'"></ol>';
				} else {
					if(options.firstShow) html += '<span class="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
					html += ' <span class="'+ options.prevId +'"><a class="'+ options.prevId +'" href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
				};
				
				endHtml = '<span class="'+ options.nextId +'"><a class="'+ options.nextId +'" href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
				if(options.lastShow) endHtml += ' <span class="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';				
		
				if( !options.vertical ){
				
				endHtml += options.controlsAfter;		
				
				$(obj).before(html + endHtml);										
				}
				else
				{
				endHtml += options.controlsAfter;	
				$(obj).before(html);	
				$(obj).after(endHtml);	
				}
				
			};
			
			if(options.numeric){									
				for(var i=0;i<s;i++){						
					$(document.createElement("li"))
						.attr('id',options.numericId + (i+1))
						.html('<a rel='+ i +' href=\"javascript:void(0);\">'+ (i+1) +'</a>')
						.appendTo($("#"+ options.numericId))
						.click(function(){							
							animate($("a",$(this)).attr('rel'),true);
						}); 												
				};							
			} else {
				
				obj.parent().find("a."+options.nextId).first().click(function(){
					animate("next",true);
				});
				obj.parent().find("a."+options.prevId).first().click(function(){		
					animate("prev",true);				
				});	
				obj.parent().find("a."+options.firstId).first().click(function(){		
					animate("first",true);
				});				
				obj.parent().find("a."+options.lastId).first().click(function(){		
					animate("last",true);				
				});	
			
					
			};
			
			function setCurrent(i){
				i = parseInt(i)+1;
				$("li", "#" + options.numericId).removeClass("current");
				$("li#" + options.numericId + i).addClass("current");
			};
			
			function adjust(){
				if(t>ts) t=0;		if(t<0) t=ts;	
				if(!options.vertical) {					
					obj.find("ul").css("margin-left",(t*w*-1));
				} else {
					obj.find("ul",obj).css("margin-top",(t*h*-1));
				}
				clickable = true;
				if(options.numeric) setCurrent(t);
			};
			
			function animate(dir,clicked){
							
			   if (clickable){
					clickable = false;
					var ot = t;				
					switch(dir){
						case "next":
							t = (ot>=ts) ? (options.continuous ? t+1 : ts) : t+1;						
							break; 
						case "prev":
							t = (t<=0) ? (options.continuous ? t-1 : 0) : t-1;
							break; 
						case "first":
							t = 0;
							break; 
						case "last":
							t = ts;
							break; 
						default:
							t = dir;
							break; 
					};	
					var diff = Math.abs(ot-t);
					var speed = diff*options.speed;						
					if(!options.vertical) {
						p = (t*w*-1);
						obj.find("ul.slidingparts").animate(
							{ marginLeft: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);				
					} else {
						p = (t*h*-1);
						obj.find("ul.slidingparts").animate(
							{ marginTop: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);					
					};
					
					if(!options.continuous && options.controlsFade){					
						if(t==ts){
							obj.parent().find("a."+options.nextId).hide();
							obj.parent().find("a."+options.lastId).hide();
						} else {
							obj.parent().find("a."+options.nextId).show();
							obj.parent().find("a."+options.lastId).show();					
						};
						if(t==0){
							obj.parent().find("a."+options.prevId).hide();
							obj.parent().find("a."+options.firstId).hide();
						} else {
							obj.parent().find("a."+options.prevId).show();
							obj.parent().find("a."+options.firstId).show();
						};					
					};				
					
					if(clicked) clearTimeout(timeout);
					
					
					if(options.auto && dir=="next" && !clicked){;
						timeout = setTimeout(function(){
							animate("next",false);
						},diff*options.speed+options.pause);
					};
					
					if(options.auto && clicked){;
						timeout = setTimeout(function(){
							animate("next",false);
						},3*diff*options.speed+options.pause);
					};
			
				};
				
			};
			// init
			var timeout;
			if(options.auto){;
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			};		
			
			if(options.numeric) setCurrent(0);
		
			if(!options.continuous && options.controlsFade){					
				obj.find("a."+options.prevId).hide();
				obj.find("a."+options.firstId).hide();				
			};				
			
		});
	  
	};

})(jQuery);



jQuery(document).ready(function(){	



	jQuery(".slider").each(function() { 

		var slider = jQuery(this);

		if ( slider.hasClass('vertical') ) {

				slider.easySlider({

					vertical:true,

					auto: true,

					continuous: true ,

					overrideheight: false,

					controlsShow: true

				});

			}

		else{

			slider.easySlider({

					auto: true,

					continuous: true ,

					overridewidth: false,					

					controlsShow: true 

				});

		}

		

		});

	
	/*jQuery(".slider").easySlider({

		auto: true,

		continuous: true ,

		overridewidth: false,

		controlsShow: false

	});*/

//window.parent.document.setheight(jQuery("body").height());
});

