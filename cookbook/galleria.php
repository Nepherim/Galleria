<?php if (!defined('PmWiki')) exit();
/*
Ref: http://www.pmwiki.org/wiki/Cookbook/Galleria
Usage in pmwiki:

(:div id="gallery":)
* Attach:DAW/tangocrash.jpg"Some caption"
* Attach:DAW/walcheturm_freeman.jpg"Some other caption"
(:divend:)
(:galleria list="#gallery" width="500" height="500":)


OR, automatically pick up DIV with class .galleria set in $galleria, and enabling galleria at the top of all pages.
Don't use this in conjunction with the (:galleria :) markup, use one or the other on a page.
  $galleria['list']='.galleria';
  include(...);
  galleria();

>>galleria<<
* Attach:DAW/tangocrash.jpg"Some caption"
* Attach:DAW/walcheturm_freeman.jpg"Some other caption"
>><<
*/

$RecipeInfo['Galleria']['Version'] = '0.5.0';
$RecipeInfo['Galleria']['Date'] = '2011-06-21';

// You can pass more options to galleria, either from config.php, or from the galleria markup. see http://galleria.aino.se/docs/1.2/options/
SDVA($galleria,array(
	'list' => '.galleria',  #If you're using the pmGallery Cookbook, use ".pmGalleryWrapper"
	'width' => '500',
	'height' => '500'
));
SDV($galleria_fn, 'galleria');  #Advanced use only. Set if you need to override the basic galleria javascript call.
Markup('galleria', 'inline', "/\\(:galleria\\s*(.*?):\\)/se", "Keep($galleria_fn(PSS('$1')))");

SDVA($HTMLHeaderFmt, array(
	'jquery' => '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>',
	'galleria-js' => '<script src="'. $PubDirUrl. '/galleria/galleria-1.2.4.js"></script>',
	'galleria-theme' => '<script type="text/javascript">Galleria.loadTheme("'. $PubDirUrl. '/galleria/themes/classic/galleria.classic.min.js");</script>'
));


function galleria ($args=false) {
	$args = ParseArgs($args);
	unset($args['#']);
	$o = array_merge($GLOBALS['galleria'],$args);

	foreach ($o as $k => $v)  if ($k != 'list')  $o_str[] = $k .':' .$v;
	$o_str = trim(join(',', $o_str), ',');

	return '
<script type="text/javascript">
$(document).ready(function(){
	$("'. $o['list']. '").galleria({ ' .$o_str .' });
});
</script>';
}
