<?php

// WP-No-Format
// Version 1.1
// http://www.neoegm.com/tech/blogging/wordpress/plugins/wp-no-format/

// *************************************************************************************************
// This file is part of WP-No-Format by NeoEGM
// 
// Copyright (C) 2009 Ezequiel Gastón Miravalles
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// *************************************************************************************************

/*
Plugin Name: WP-No-Format
Plugin URI: http://www.neoegm.com/tech/blogging/wordpress/plugins/wp-no-format/
Description: This plugin lets you prevent Wordpress from modifying the HTML written code.
Version: 1.1
Author: Ezequiel Miravalles
Author URI: http://www.neoegm.com/
*/

function newautop($text)
{
	$newtext = "";
	$pos = 0;

	$tags = array('<!-- noformat on -->', '<!-- noformat off -->');
	$status = 0;

	while (!(($newpos = strpos($text, $tags[$status], $pos)) === FALSE))
	{
		$sub = substr($text, $pos, $newpos-$pos);

		if ($status)
			$newtext .= $sub;
		else
			$newtext .= convert_chars(wptexturize(wpautop($sub)));		//Apply both functions (faster)

		$pos = $newpos+strlen($tags[$status]);

		$status = $status?0:1;
	}

	$sub = substr($text, $pos, strlen($text)-$pos);

	if ($status)
		$newtext .= $sub;
	else
		$newtext .= convert_chars(wptexturize(wpautop($sub)));		//Apply both functions (faster)

	//To remove the tags
	$newtext = str_replace($tags[0], "", $newtext);
	$newtext = str_replace($tags[1], "", $newtext);

	return $newtext;
}

function newtexturize($text)
{
	return $text;	//All work already done by newautop

/*
	$newtext = "";
	$pos = 0;

	$tags = array('<!-- noformat on -->', '<!-- noformat off -->');
	$status = 0;

	while (!(($newpos = strpos($text, $tags[$status], $pos)) === FALSE))
	{
		$sub = substr($text, $pos, $newpos-$pos);

		if ($status)
			$newtext .= $sub;
		else
			$newtext .= wptexturize($sub);

		$pos = $newpos+strlen($tags[$status]);

		$status = $status?0:1;
	}

	$sub = substr($text, $pos, strlen($text)-$pos);

	if ($status)
		$newtext .= $sub;
	else
		$newtext .= wptexturize($sub);

	//To remove the tags, but it doesn't seem to be needed
	$newtext = str_replace($tags[0], "", $newtext);
	$newtext = str_replace($tags[1], "", $newtext);

	return $newtext;
*/
}

function new_convert_chars($text)
{
	return $text;	//All work already done by newautop

/*
	$newtext = "";
	$pos = 0;

	$tags = array('<!-- noformat on -->', '<!-- noformat off -->');
	$status = 0;

	while (!(($newpos = strpos($text, $tags[$status], $pos)) === FALSE))
	{
		$sub = substr($text, $pos, $newpos-$pos);

		if ($status)
			$newtext .= $sub;
		else
			$newtext .= wptexturize($sub);

		$pos = $newpos+strlen($tags[$status]);

		$status = $status?0:1;
	}

	$sub = substr($text, $pos, strlen($text)-$pos);

	if ($status)
		$newtext .= $sub;
	else
		$newtext .= wptexturize($sub);

	//To remove the tags, but it doesn't seem to be needed
	$newtext = str_replace($tags[0], "", $newtext);
	$newtext = str_replace($tags[1], "", $newtext);

	return $newtext;
*/
}

remove_filter('the_content', 'wpautop');
add_filter('the_content', 'newautop');

remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'newtexturize');

remove_filter('the_content', 'convert_chars');
add_filter('the_content', 'new_convert_chars');

?>