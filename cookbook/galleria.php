<?php if (!defined('PmWiki')) exit();
/* Updated code from http://www.pmwiki.org/wiki/Cookbook/Galleria and updated it to Galleria 1.2.3 from http://galleria.aino.se/
Mauro Bieg, 25th April 2011 

Usage in pmwiki:

(:div id="gallery":)
* Attach:DAW/tangocrash.jpg"Some caption"
* Attach:DAW/walcheturm_freeman.jpg"Some other caption"
(:divend:)
(:galleria list="#gallery" width="500" height="500":)

*/

$RecipeInfo['pmGallery']['Version'] = '0.4';
$RecipeInfo['pmGallery']['Date'] = '2011-04-25';

/**
* Code executed on include
*/
Markup('galleria', 'inline', "/\\(:galleria\\s*(.*?):\\)/se", "Keep(galleria(PSS('$1')))");

SDV($galleria['skin'],'tango');
$HTMLHeaderFmt['jquery'] = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>';
$HTMLHeaderFmt['galleria'] = '<script src="' . $PubDirUrl . '/galleria/galleria-1.2.3.js"></script><script type="text/javascript">Galleria.loadTheme("' . $PubDirUrl . '/galleria/themes/classic/galleria.classic.min.js");</script>';

function galleria ($args) {
	$o = Array(
		'list'		 		=> '',		// use ".pmGalleryWrapper" with pmGallery
		'width' 			=> '',		// 500
		'height' 			=> '',		// 500
		//you can add more options to pass in pmwiki here, most probably you want to pass them along to galleria (see below)
	);
	$o = array_merge($o, $GLOBALS['galleria']);
	$o = array_merge($o,ParseArgs($args));

	return '
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("'. $o['list']. '").galleria({
		width:' . $o['width'] .',
		height:' . $o['height'] .'
		//here you can pass more options to galleria. see http://galleria.aino.se/docs/1.2/options/
	});
});
//]]>
</script>';
}
