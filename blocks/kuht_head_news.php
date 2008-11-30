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
//                                                                           //
//                         Spotlight for NEWS                                //
//---------------------------------------------------------------------------//
include_once(XOOPS_ROOT_PATH.'/class/module.textsanitizer.php');

// hhts inserted new graphic from Wellwine
function newgraphic($time, $days) {
    $new = '';
    $startdate = (time()-(86400 * $days));
    if ($startdate < $time) {
        $new = '&nbsp;<img src="'.XOOPS_URL.'/modules/spotlight/images/newred.gif" alt=""/>';
    }
    return $new;
}

function b_head_kuht_show_news($options)
{
	global $xoopsDB, $xoopsConfig;
	$myts =& MyTextSanitizer::getInstance();
	$fhometext = "";
	$block = array();
	$block['title_news']    = _MB_KUHT_TITLE_SPOTLIGHT_NEWS;
	$block['lang_by']       = _MB_KUHT_BY;
	$block['lang_read']     = _MB_KUHT_READ;
	$block['lang_comments'] = _MB_KUHT_COMMENTS;
	$block['lang_write']    = _MB_KUHT_WRITE;

	$var = $xoopsDB->query("SELECT item, auto, catid, auto_cat, image, auto_image, image_align FROM ".$xoopsDB->prefix("spotlight")." WHERE sid = 1",1,0);
	list ($item, $auto, $catid, $auto_cat, $image, $auto_image, $image_align) = $xoopsDB->fetchRow($var);
	if ($auto == 0) {
		// no auto selection
		$result = $xoopsDB->query("SELECT storyid, uid, title, hometext, topicdisplay, topicalign, comments FROM ".$xoopsDB->prefix("stories")." WHERE storyid=".$item." ",1,0);
	} else {
		// auto selection
		$sql = "SELECT storyid, uid, title, hometext, topicdisplay, topicalign, comments ";
		$sql.= "FROM ".$xoopsDB->prefix("stories")." ";
		$sql.= "WHERE published < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") ";
		if (0 <> $catid) {
			$sql.= "AND topicid = ".$catid." ";
		}
		$sql.= "ORDER BY published DESC";
		$result = $xoopsDB->query($sql,1,0);
	}
	list ($fsid, $fautore, $ftitle, $fhometext, $topicdisplay, $topicalign, $fcomments) = $xoopsDB->fetchRow($result);

	$result2 = $xoopsDB->query("SELECT uname, uid FROM ".$xoopsDB->prefix("users")." WHERE uid=".$fautore."",1,0);
	list ($fautorevero, $uidutente) = $xoopsDB->fetchRow($result2);

	if (!$fsid && !$ftitle) {
		$block['message'] = _MB_KUHT_NOTSELECT;
	} else {
		if ($auto_image == 0) {
			$block['image'] = $image;
			if ($image_align == "R") {
				$block['topicalign'] = 'right';
			} else {
				$block['topicalign'] = 'left';
			}
		} else {
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
		$ftitle = $myts->makeTboxData4Show($ftitle);
		if (!XOOPS_USE_MULTIBYTES) {
			if (strlen($ftitle) >= $options[0]) {
// hhts ml multilingo
				$ftitle = substr($myts->makeTboxData4Show($ftitle,0,($options[0] -1)))."...";
			}
		}
		$block['newstitle']     = $ftitle;
		$block['uid']           = $myts->makeTboxData4Show($uidutente);
		$block['author']        = $myts->makeTboxData4Show($fautorevero);
		$block['hometext_news']	= $myts->xoopsCodeDecode($fhometext);	// BB Codes
		$block['hometext_news']	= $myts->nl2Br($block['hometext_news']);
		$block['storyid']       = $myts->makeTboxData4Show($fsid);
		$block['comments']      = $myts->makeTboxData4Show($fcomments);
	}

	if ($options[1] == 1) {
		$block['lang_other_news']= _MB_KUHT_OTHER_NEWSTEXT;
		$nsql = "SELECT storyid, hometext, title, published, expired, counter FROM ".$xoopsDB->prefix("stories")." WHERE published < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") AND storyid != ".$fsid." ORDER BY ".$options[2]." DESC";
		$nresult = $xoopsDB->query($nsql,$options[3],0);
		while ($myrow = $xoopsDB->fetchArray($nresult)) {
			$news = array();
			$title = $myts->makeTboxData4Show($myrow["title"]);
			$hometext = $myts->makeTboxData4Show($myrow["hometext"]);
			if ( !XOOPS_USE_MULTIBYTES ) {
				if (strlen($myrow['title']) >= $options[4]) {
// hhts ml multilingo
					$title = substr($myts->makeTboxData4Show($myrow['title']),0,($options[4] -1))."...";
				}
			}
			$news['title'] = $title;
			$news['id'] = $myrow['storyid'];
			if ($options[2] == "published") {
				$news['hitsordate'] = formatTimestamp($myrow['published'],"s");
// hhts toegevoegd new graphic
  		        $news['graphic'] = newgraphic($myrow['published'], 7);
			} elseif ($options[2] == "counter") {
				$news['hitsordate'] = $myrow['counter'];
			}
			if ($options[6] == 1) {
				if (strlen($hometext) >= $options[7]) {
// hhts ml multilingo
					$hometext = substr($myts->makeTboxData4Show($hometext),0,($options[7] -1))."...";
				}
				$block['textview'] = 1;
			} else {
				$block['textview'] = 0;
			}
			$news['hometext'] = $hometext;
			$block['stories'][] = $news;
		}
	}

	if ($options[5] == 1) {
		// rb topic select form for news direct topic access
		$topic_options = '';
		$block['topicsel'] = '';
		$sql = "SELECT topic_id, topic_title FROM ".$xoopsDB->prefix("topics")." order by topic_title ASC ";
		if (!$r = $xoopsDB->query($sql)) {
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
			$topic_options .= '<option value="'.$id.'">'.$title.'</option>'."\n";
		   }
		   while($row = $xoopsDB->fetchArray($r));
		}
		if ($topic_options <> '') {
			$block['topicsel'] = '<form action="'.XOOPS_URL.'/modules/news/index.php? method="post">'."\n";
			$block['topicsel'].= '<select name="storytopic" onchange="submit();">'."\n";
			$block['topicsel'].= '<option value="0" selected>'._MB_KUHT_CHOOSE_NEWS.'</option>'."\n";
			$block['topicsel'].= $topic_options."\n";
			$block['topicsel'].= '</select></form>'."\n";
		}
		// END rb topic select form for news direct topic access
	}

	if ($options[8] == 1) {
		$block['lang_ministats'] = '<span style="font-size: 9px; text-transform: uppercase">'._MB_KUHT_MINISTATS.'</span>';

		$result = $xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("stories")."");
		list($news) = $xoopsDB->fetchRow($result);

		$result = $xoopsDB->query("select sum(counter) FROM ".$xoopsDB->prefix("stories")."");
		list($storiesviews) = $xoopsDB->fetchRow($result);

		$result = $xoopsDB->query("SELECT sum(comments) FROM ".$xoopsDB->prefix("stories")."");
		list($comment) = $xoopsDB->fetchRow($result);

		$publishednews = _MB_KUHT_PUBLISHED;
		$readnews      = _MB_KUHT_READS;
		$commentnews   = _MB_KUHT_NEWSCOMMENTS;

		$block['ministats'] = "\n".'<span style="font-size: 9px;">'."\n";
		$block['ministats'].= $publishednews.': <b>'.$news.' :</b> '."\n";
		$block['ministats'].= $readnews.': <b>'.$storiesviews.'</b> : '."\n";
		$block['ministats'].= $commentnews.': <b>'.$comment.'</b>'."\n";
		$block['ministats'].= '</span>'."\n";
	}

	if ($options[9] == 1) {
		$block['select_template'] = 1;
	}
	return $block;
}

function b_head_kuht_edit_news($options)
{
	$form = "\n".'<p>'._MB_KUHT_TITLECHARS.'&nbsp;<input type="text" size="2" name="options[0]" value="'.$options[0].'" />&nbsp;'._MB_KUHT_TITLELENGTH.'&nbsp;</p>'."\n";
	$form.= '<b>'._MB_KUHT_OTHER_NEWS.'</b><br />'."\n";
	$form.= '<i>'._MB_KUHT_MORE_LINKS.'</i>'."\n";
	if ( $options[1] == 1 ) {
		$chk = ' checked="checked"';
	}
	$form.= '&nbsp;<input type="radio" name="options[1]" value="1"'.$chk.' />'._MB_KUHT_YES."\n";
	$chk = "";
	if ( $options[1] == 0 ) {
		$chk = ' checked="checked"';
	}
	$form.= '&nbsp;<input type="radio" name="options[1]" value="0"'.$chk.' />'._MB_KUHT_NO.'<br />'."\n";
	$form.= _MB_KUHT_ORDER.'<select name="options[2]">'."\n";
	$form.= '<option value="published"';
	if ($options[2] == "published") {
		$form .= ' selected="selected"';
	}
	$form.= '>&nbsp;'._MB_KUHT_DATE.'</option>'."\n";
	$form.= '<option value="counter"';
	if ($options[2] == "counter") {
		$form .= ' selected="selected"';
	}
	$form.= '>&nbsp;'._MB_KUHT_HITS.'</option>'."\n";
	$form.= '</select><br />'."\n";
	$form.= _MB_KUHT_DISP.'&nbsp;<input type="text" size="2" name="options[3]" value="'.$options[3].'" />&nbsp;'._MB_KUHT_ARTCLS.'<br />'."\n";
	$form.= _MB_KUHT_CHARS.'&nbsp;<input type="text" size="2" name="options[4]" value="'.$options[4].'" />&nbsp;'._MB_KUHT_LENGTH.'<br />'."\n";
	$form.= '<p>'._MB_KUHT_TOPICS."\n";
	if ( $options[5] == 1 ) {
		$chk = ' checked="checked"';
	}
	$form.= '&nbsp;<input type="radio" name="options[5]" value="1"'.$chk.' />'._MB_KUHT_YES."\n";
	$chk = "";
	if ( $options[5] == 0 ) {
		$chk = ' checked="checked"';
	}
	$form.= '&nbsp;<input type="radio" name="options[5]" value="0"'.$chk.' />'._MB_KUHT_NO.'</p>'."\n";
    // hsalazar -- Show teaser text? Y/N
    $form .= _MB_KUHT_TEXTVIEW.'&nbsp;<select name="options[6]">'."\n";
    $form .= '<option value="1"';
	if($options[6] == 1) {
        $form .= ' selected="selected"';
	}
    $form .= '>'._MB_KUHT_YES.'</option>'."\n";
    $form .= '<option value="0"';
    if($options[6] == 0) {
        $form .= ' selected="selected"';
	}
    $form .= '>'._MB_KUHT_NO.'</option>'."\n";
    $form .= '</select>'."\n";
	// -- $options[7] is length of text teaser of the articles
	$form .= _MB_KUHT_HOMETEXTCHARS.'&nbsp;<input type="text" size="2" name="options[7]" value="'.$options[7].'" />&nbsp;'._MB_KUHT_LENGTH."\n";
	$form.= '<p>'._MB_KUHT_INCSTATS."\n";
	if ( $options[8] == 1 ) {
		$chk = ' checked="checked"';
	}
	$form.= ' <input type="radio" name="options[8]" value="1"'.$chk.' />'._MB_KUHT_YES."\n";
	$chk = "";
	if ( $options[8] == 0 ) {
		$chk = ' checked="checked"';
	}
	$form.= ' <input type="radio" name="options[8]" value="0"'.$chk.' />'._MB_KUHT_NO.'</p>'."\n";
	$form.= '<p>'._MB_KUHT_CHOOSETEMPLATE."\n";
	if ( $options[9] == 1 )
		{
		$chk = ' checked="checked"';
		}
	$form.= ' <input type="radio" name="options[9]" value="1"'.$chk.' />'._MB_KUHT_TWOCOLS."\n";
	$chk = "";
	if ( $options[9] == 0 ) {
		$chk = ' checked="checked"';
	}
	$form.= ' <input type="radio" name="options[9]" value="0"'.$chk.' />'._MB_KUHT_STACKED.'</p>'."\n";
	return $form;
}
?>
