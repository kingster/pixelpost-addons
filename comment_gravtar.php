<?php

/**
 * @author kingster
 * @copyright 2010
 */

$addon_name = "Comment with Image";
$addon_description = "This addon simply gives image along with gravtar for comments.";
$addon_version = "1.0";

if(preg_match("<GRAVTAR_IMAGE_COMMENTS>", $tpl)) {
 
$iid = $_GET['showimage'];
$image_gcomments =  print_gravcomments($iid);
$tpl = ereg_replace("<GRAVTAR_IMAGE_COMMENTS>",$image_gcomments,$tpl);
 
}


function print_gravcomments($imageid)
{
	global $pixelpost_db_prefix;
	global $lang_no_comments_yet;
	global $lang_visit_homepage;
	global $cfgrow;

	$comment_count = 0;
	$image_comments = "<div><table>"; // comments stored in this string
	$cquery = mysql_query("select datetime, message, name, url, email  from ".$pixelpost_db_prefix."comments where parent_id='".$imageid."' and publish='yes' order by id asc");
	while(list($comment_datetime, $comment_message, $comment_name, $comment_url, $comment_email) = mysql_fetch_row($cquery))
	{
		$comment_message = pullout($comment_message);
		$comment_name = pullout($comment_name);
		$comment_email = pullout($comment_email);

		if($comment_url != "")
		{
	  	if( preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'.'((:[0-9]{1,5})?\/.*)?$/i' ,$comment_url))
			{
  			$comment_name = "<a href=\"$comment_url\" title=\"$lang_visit_homepage\" target=\"_blank\" rel=\"nofollow\">$comment_name</a>";
			}
			else
			{
				unset($comment_url);
				$comment_name = "$comment_name";
			}
		}

		$comment_datetime = strtotime($comment_datetime);
		$comment_datetime = date($cfgrow['dateformat'],$comment_datetime);
        
     
	$image_comments .= "<tr><td>" .get_gravatar($comment_email) ."</td>	<td>";
 
        
		if ($comment_email == $cfgrow['email']){
			// admin comment
			$image_comments .= "<p class=\"admin_comment\">$comment_message<br />$comment_name @ $comment_datetime</p>";
		} else {
			$image_comments .= "<p>$comment_message<br />$comment_name @ $comment_datetime</p>";
		}
        
       $image_comments .= "</td></tr>";
		$comment_count++;

	}

	if($comment_count == 0)	$image_comments .= "<tr><td><p>$lang_no_comments_yet</p></td></tr>";

	$image_comments .= "</table></div>";

	return $image_comments;
}


/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'r', $img = true, $atts = array() ) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}


?>