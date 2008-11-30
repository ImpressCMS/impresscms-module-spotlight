<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //
//                                                                           //
//                               "Spotlight"                                 //
//               http://linux.kuht.it  - http://www.kuht.it                  //
//                              spark@kuht.it                                //
//                                                                           //
//   Adaptation for XOOPS 2.0x by Herko (me at herkocoomans dot net) and     //
//                Dawilby (willemsen1 at chello dot nl)                      //
//---------------------------------------------------------------------------//
include_once(XOOPS_ROOT_PATH.'/class/xoopsstory.php');
include_once(XOOPS_ROOT_PATH.'/class/module.textsanitizer.php');
function b_head_kuht_show($options)
{
	global $xoopsDB, $xoopsConfig;
	$myts =& MyTextSanitizer::getInstance();
	$fhometext = "";
	$block = array();
	$block['title'] = _MB_KUHT_TITLE_SPOTLIGHT;
	$tdate = mktime(0,0,0,date("n"),date("j"),date("Y"));

	$block['lang_by']		= _MB_KUHT_BY;
	$block['lang_read']		= _MB_KUHT_READ;
	$block['lang_comments']	= _MB_KUHT_COMMENTS;
	$block['lang_write']	= _MB_KUHT_WRITE;
	$block['lang_othernews']= _MB_KUHT_OTHERNEWSTEXT;

	$var = $xoopsDB->query("SELECT news, auto, image, auto_image FROM ".$xoopsDB->prefix("spotlight")."",1,0);
	list ($number, $auto, $image, $auto_image) = $xoopsDB->fetchRow($var);
	if ($auto == 0) {
		// no auto selection
		$result = $xoopsDB->query("SELECT storyid, uid, title, hometext, topicdisplay, topicalign, comments FROM ".$xoopsDB->prefix("stories")." WHERE storyid=".$number." ",1,0);
	} else {
		// auto selection
		$result = $xoopsDB->query("SELECT storyid, uid, title, hometext, topicdisplay, topicalign, comments FROM ".$xoopsDB->prefix("stories")." WHERE published < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") ORDER BY published DESC",1,0);
	}
	list ($fsid, $fautore, $ftitle, $fhometext, $topicdisplay, $topicalign, $fcomments) = $xoopsDB->fetchRow($result);

	$result2 = $xoopsDB->query("SELECT uname, uid FROM ".$xoopsDB->prefix("users")." WHERE uid=".$fautore."",1,0);
	list ($fautorevero, $uidutente) = $xoopsDB->fetchRow($result2);

	if (!$fsid && !$ftitle) {
		$block['message'] = _MB_KUHT_NOTSELECT;
	} else {
		if ($auto_image == 0) {
			$block['image'] = $image;
		} elseif ($topicdisplay == 1) {
			$var_image = $xoopsDB->query("SELECT topicid FROM ".$xoopsDB->prefix("stories")." WHERE storyid=".$fsid."",1,0);
			list ($patt_image) = $xoopsDB->fetchRow($var_image);
			$var_image2 = $xoopsDB->query("SELECT topic_imgurl FROM ".$xoopsDB->prefix("topics")." WHERE topic_id=".$patt_image."",1,0);
			list ($image_display) = $xoopsDB->fetchRow($var_image2);
			$block['image_display'] = $image_display;
			if ($topicalign == "R") {
				$block['topicalign'] = 'right';
			} else {
				$block['topicalign'] = 'left';
			}
		}
		if (!XOOPS_USE_MULTIBYTES) {
			if (strlen($ftitle) >= $options[0]) {
				$ftitle = substr($ftitle,0,($options[0] -1))."...";
			}
		}
		$block['title']		= $myts->makeTboxData4Show($ftitle);
		$block['uid']		= $myts->makeTboxData4Show($uidutente);
		$block['author']	= $myts->makeTboxData4Show($fautorevero);
		$block['hometext']	= $myts->xoopsCodeDecode($fhometext);	// BB Codes
		$block['hometext']	= $myts->nl2Br($block['hometext']);
		$block['storyid']	= $myts->makeTboxData4Show($fsid);
		$block['comments']	= $myts->makeTboxData4Show($fcomments);
	}
	$nsql = "SELECT storyid, title, published, expired, counter FROM ".$xoopsDB->prefix("stories")." WHERE published < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") AND storyid != ".$fsid." ORDER BY published DESC";
	$nresult = $xoopsDB->query($nsql,$options[2],0);
	while ( $myrow = $xoopsDB->fetchArray($nresult) ) {
		$news = array();
		$title = $myts->makeTboxData4Show($myrow["title"]);
		if ( !XOOPS_USE_MULTIBYTES ) {
			if (strlen($myrow['title']) >= $options[3]) {
				$title = $myts->makeTboxData4Show(substr($myrow['title'],0,($options[3] -1)))."...";
			}
		}
		$news['title'] = $title;
		$news['id'] = $myrow['storyid'];
		if ($options[1] == "published") {
			$news['hitsordate'] = formatTimestamp($myrow['published'],"s");
		} elseif ($options[1] == "counter") {
			$news['hitsordate'] = $myrow['counter'];
		}
		$block['stories'][] = $news;
	}
	if ($options[4] == 1) {
	// rb topic select form for news direct topic access
	$topic_options = '';
	$block['topicsel'] = '';
	$sql = "SELECT topic_id, topic_title FROM ".$xoopsDB->prefix("topics")." order by topic_title ASC ";
	if (!$r = $xoopsDB->query($sql)){
		exit();
	}
	if ($row = $xoopsDB->fetchArray($r)) {
		do {
		$id= $row['topic_id'];
		$title =$myts->makeTboxData4Show($row['topic_title']);
		if (!XOOPS_USE_MULTIBYTES ) {
			if (strlen($title) >= 20) {
// hhts ml multilingo ?
				$title = substr($myts->makeTboxData4Show($title,0,19))."...";
			}
		}
		$topic_options .= '<option value="'.$id.'">'.$title.'</option>';
	   }
	   while($row = $xoopsDB->fetchArray($r));
	}
	if ($topic_options <> '') {
		$block['topicsel'] = '<form action="'.XOOPS_URL.'/modules/news/index.php? method="post">';
		$block['topicsel'].= '<select name="storytopic" onchange="submit();">';
		$block['topicsel'].= '<option value="0" selected>'._MB_KUHT_CHOOSE.'</option>';
		$block['topicsel'].= $topic_options;
		$block['topicsel'].= '</select></form>';
	}
	// END rb topic select form for news direct topic access
	}
	return $block;
}

function b_head_kuht_edit($options)
{
	$form = _MB_KUHT_TITLECHARS.'&nbsp;<input type="text" name="options[]" value="'.$options[0].'" />&nbsp;'._MB_KUHT_TITLELENGTH.'&nbsp;<br />';
	$form.= '<b>'._MB_KUHT_OTHERNEWS.'</b><br />';
	$form.= _MB_KUHT_ORDER.'&nbsp;<select name="options[]">';
	$form.= '<option value="published"';
	if ($options[1] == "published") {
		$form .= ' selected="selected"';
	}
	$form.= '>'._MB_KUHT_DATE.'</option>';
	$form.= '<option value="counter"';
	if ($options[1] == "counter") {
		$form .= ' selected="selected"';
	}
	$form.= '>'._MB_KUHT_HITS.'</option>';
	$form.= '</select>';
	$form.= _MB_KUHT_DISP.'&nbsp;<input type="text" size="2" name="options[]" value="'.$options[2].'" />&nbsp;'._MB_KUHT_ARTCLS.'<br />';
	$form.= _MB_KUHT_CHARS.'&nbsp;<input type="text" size="2"name="options[]" value="'.$options[3].'" />&nbsp;'._MB_KUHT_LENGTH.'<br />';
	$form.= _MB_KUHT_TOPICS.'&nbsp;<input type="text" size="2" name="options[]" value="'.$options[4].'" />';
	return $form;
}
?>
