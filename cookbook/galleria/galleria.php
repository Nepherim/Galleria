<?php if (!defined('PmWiki')) exit();

$RecipeInfo['pmGallery']['Version'] = '0.1.0';
$RecipeInfo['pmGallery']['Date'] = '2008-07-19';

/**
* Code executed on include
*/
Markup('galleria', 'inline', "/\\(:galleria\\s*(.*?):\\)/se", "Keep(galleria(PSS('$1')))");

SDV($galleria['skin'],'tango');
$HTMLHeaderFmt['jquery'] = '<script type="text/javascript" src="'. $PubDirUrl. '/galleria/jquery.pack.js"></script>';
$HTMLHeaderFmt['galleria'] = '
<script type="text/javascript" src="'. $PubDirUrl. '/galleria/jquery.galleria.pack.js"></script>
<script type="text/javascript" src="'. $PubDirUrl. '/galleria/jquery.jcarousel.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'. $PubDirUrl. '/galleria/galleria.css" />
<link rel="stylesheet" type="text/css" href="'. $PubDirUrl. '/galleria/jquery.jcarousel.css" />
<link rel="stylesheet" type="text/css" href="'. $PubDirUrl. '/galleria/skins/'. $galleria['skin']. '/skin.css" />
';

function galleria ($args) {
	$o = Array(
		'list'		 		=> '',			// use "ul.pmGalleryImageList" with pmGallery
		'image' 				=> '',			// leave this blank for and galleria will auto-create above the list. Use "#pmGallery_Image" with pmGallery
		'history' 			=> 'false',
		'clicknext' 		=> 'true',
		'fadein' 			=> '600',		// 
		'width' 				=> '',			// 245
		'height' 			=> '',			// 75
		// carousel settings
		'carousel'			=> 'false',
		'scroll' 			=> '3',
		'vertical'			=> 'false',		// Changes the carousel from a left/right style to a up/down style carousel.
		'visible'			=> '3',			// the width/height of the items will be calculated and set depending on the width/height of the clipping, so that exactly that number of items will be visible.
		'animation'			=> 'fast'		// The speed of the scroll animation as string in jQuery terms ("slow"  or "fast") or milliseconds as integer (See jQuery Documentation). If set to 0, animation is turned off.
	);
	$o = array_merge($o, $GLOBALS['galleria']);
	$o = array_merge($o,ParseArgs($args));
	$orient = ($o['vertical']=='true'?'vertical':'horizontal');
	
	return '
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("'. $o['list']. '").galleria({
		history   : '. $o['history']. ', 										// activates the history object for bookmarking, back-button etc.
		clickNext : '. $o['clicknext']. ', 										// helper for making the image clickable
		insert    : "'. $o['image']. '", 										// the containing selector for our main image
		onImage   : function(image,caption,thumb) { 							// lets add some image effects for demonstration purposes
			image.css("display","none").fadeIn('. $o['fadein']. '); 		// fade in the image
			var _li = thumb.parents("li");										// fetch the thumbnail container
			_li.siblings().children("img.selected").fadeTo(100,0.6);		// fade out inactive thumbnail
			thumb.fadeTo("fast",1).addClass("selected");						// fade in active thumbnail
			image.attr({
				"title":"Next image >>",											// add a title for the clickable image
				"jcarouselindex":_li.attr("jcarouselindex")					// find the index of the clicked thumb
			});
			$("'. $o['image']. '").trigger("img_change");
		},
		onThumb : function(thumb) { 												// thumbnail effects goes here
			var _li = thumb.parents("li");										// fetch the thumbnail container
			var _fadeTo = _li.is(".active") ? "1" : "0.6";					// if thumbnail is active, fade all the way.
			thumb.css({display:"none",opacity:_fadeTo}).fadeIn(500);		// fade in the thumbnail when finished loading
			thumb.hover(
				function() { thumb.fadeTo("fast",1); },
				function() { _li.not(".active").children("img").fadeTo("fast",0.6); } // don"t fade out if the parent is active
			)
		}
	});'.
	($o['carousel']=='true'
		? '
	$("'. $o['list']. ' li:first").addClass("active");
	$("'. $o['list']. '")
		.addClass("jcarousel-skin-'. $o['skin']. '")
		.jcarousel({
			scroll: '. $o['scroll']. ',
			initCallback: mycarousel_initCallback,
			vertical:'. $o['vertical']. ',
			visible:'. $o['visible']. ',
			animation:"'. $o['animation']. '"
		});'.
			(empty($o['height']) ? ''
				: '$(".jcarousel-container-'. $orient. ',.jcarousel-clip-'. $orient. '").css({"height":"'. $o['height']. 'px"});'
			).
			(empty($o['width']) ? ''
				: '$(".jcarousel-container-'. $orient. ',.jcarousel-clip-'. $orient. '").css({"width":"'. $o['width']. 'px"});'
			).'				
});
function mycarousel_initCallback(carousel) {
	$("'. (empty($o['image'])?'galleria_container':$o['image']). '").bind("img_change",function() {
		var num = $(".galleria_wrapper img").attr("jcarouselindex")-1;
		carousel.scroll(num);
		return false;
	});
};'
		: '
});'
	).'
//]]>
</script>';
}
