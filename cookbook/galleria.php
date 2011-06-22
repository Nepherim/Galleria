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

$RecipeInfo['Galleria']['Version'] = '0.5.1';
$RecipeInfo['Galleria']['Date'] = '2011-06-22';

// You can pass more options to galleria, either from config.php, or from the galleria markup. see http://galleria.aino.se/docs/1.2/options/
SDVA($galleria,array(
	'list' => '.galleria',  #If you're using the pmGallery Cookbook, use ".pmGalleryImageList"
	'width' => '500',
	'height' => '500'));

// if your pages are locked, or you trust editors, set this true to allow unsafe options to be specified from markup. Otherwise unsafe options can be specified only from config.php
SDV($galleria_safe_mode, true);
SDVA($galleria_unsafe_options, array('dataConfig', 'extend'));  #galleria.js expects these options to be functions, and thus NOT quoted when passed into galleria(). Potentially unsafe.

SDV($galleria_fn, 'galleria');  #Advanced use only. Set if you need to override the basic galleria javascript call.
Markup('galleria', 'inline', "/\\(:galleria\\s*(.*?):\\)/se", "Keep($galleria_fn(PSS('$1')))");

SDVA($HTMLHeaderFmt, array(
	'jquery' => '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>',
	'galleria-js' => '<script src="'. $PubDirUrl. '/galleria/galleria-1.2.4.js"></script>',
	'galleria-theme' => '<script type="text/javascript">Galleria.loadTheme("'. $PubDirUrl. '/galleria/themes/classic/galleria.classic.min.js");</script>'
));

function galleria ($args=false) {
global $galleria, $galleria_unsafe_options, $galleria_safe_mode;

	$args = ParseArgs($args);
	unset($args['#']);
	if ($galleria_safe_mode)  #remove unsafe options from markup args -- only allow from config.php
		foreach ($galleria_unsafe_options as $v)
			unset($args[$v]);

	$o = array_merge($galleria, $args);

	return '
<script type="text/javascript">
$(document).ready(function(){
	$("'. $o['list']. '").galleria(' . bi_json_encode($o, false, $galleria_unsafe_options).');
});
</script>';
}


#json_encode only in PHP5.2+. Rather than overriding json_encode, and supporting two versions. ref http://www.mike-griffiths.co.uk/php-json_encode-alternative/
#$strict will wrap the key in quotes.
#keys in $np array if found in $a will not have VALUES wrapped in quotes. Allows javascript functions to be option values.
function bi_json_encode($a=false, $strict=true, $np=false){
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
		foreach ($a as $v)  $result[] = bi_json_encode($v, $strict, $np);
		return '[ ' . join(', ', $result) . ' ]';
	}else{
		foreach ($a as $k => $v) $result[] = ($strict ?bi_json_encode($k, $strict, $np) :$k) .': ' .(in_array($k, $np) ?$v :bi_json_encode($v, $strict, $np));
		return '{ ' . join(', ', $result) . ' }';
	}
}
