<?php if (!defined('PmWiki')) exit();
/*
Ref: http://www.pmwiki.org/wiki/Cookbook/Galleria
Usage in pmwiki:

(:div id="gallery":)
* Attach:DAW/tangocrash.jpg"Some caption"
* Attach:DAW/walcheturm_freeman.jpg"Some other caption"
(:divend:)
(:galleria list="#gallery" width="500" height="500":)

*/

$RecipeInfo['Galleria']['Version'] = '0.4.1';
$RecipeInfo['Galleria']['Date'] = '2011-04-29';

/**
* Code executed on include
*/

// You can pass more options to galleria, either from config.php, or from the galleria markup. see http://galleria.aino.se/docs/1.2/options/
SDVA($galleria,array(
	'list'	=> '',		#If you're using the pmGallery Cookbook, use ".pmGalleryWrapper"
	'width'	=> '500',
	'height'	=> '500'
));
SDV($galleria_fn, 'galleria');	#Only set if you need to override the basic galleria javascript call
Markup('galleria', 'inline', "/\\(:galleria\\s*(.*?):\\)/se", "Keep($galleria_fn(PSS('$1')))");

$HTMLHeaderFmt['jquery'] = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>';
$HTMLHeaderFmt['galleria-js'] = '<script src="'. $PubDirUrl. '/galleria/galleria-1.2.3.js"></script>';
$HTMLHeaderFmt['galleria-theme'] = '<script type="text/javascript">Galleria.loadTheme("'. $PubDirUrl. '/galleria/themes/classic/galleria.classic.min.js");</script>';

function galleria ($args) {
	$args = ParseArgs($args);
	unset($args['#']);

	$o = array_merge($GLOBALS['galleria'],$args);
	$o_galleria = galleria_json_encode($o);
	return '
<script type="text/javascript">
$(document).ready(function(){
	$("'. $o['list']. '").galleria('. $o_galleria. ');
});
</script>';
}

#json_encode only in PHP5.2+. Rather than overriding json_encode, and supporting two versions. ref http://www.mike-griffiths.co.uk/php-json_encode-alternative/
function galleria_json_encode($a=false){
	if (is_null($a)) return 'null';
	if ($a === false) return 'false';
	if ($a === true) return 'true';
	if (is_scalar($a)){
		if (is_float($a) || is_numeric($a))  return floatval(str_replace(",", ".", strval($a)));  #Always use "." for floats.
		if (is_string($a)) {
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}else  return $a;
	}
	$isList = true;
	for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		if (key($a) !== $i){ $isList = false; break; }
	$result = array();
	if ($isList){
		foreach ($a as $v)  $result[] = galleria_json_encode($v);
		return '[ ' . join(', ', $result) . ' ]';
	}else{
		foreach ($a as $k => $v) $result[] = galleria_json_encode($k).': '.galleria_json_encode($v);
		return '{ ' . join(', ', $result) . ' }';
	}
}
