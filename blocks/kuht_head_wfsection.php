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
//                        Spotlight for WF-Section                           //
//---------------------------------------------------------------------------//
include_once(XOOPS_ROOT_PATH.'/class/module.textsanitizer.php');

// hhts inserted new graphic from Wellwine
function wfsnewgraphic($time, $days) {
    $new = '';
    $startdate = (time()-(86400 * $days));
    if ($startdate < $time) {
        $new = '&nbsp;<img src="'.XOOPS_URL.'/modules/spotlight/images/newred.gif" alt=""/>';
    }
    return $new;
}

function b_head_kuht_show_wfsection($options)
{
	global $xoopsDB, $xoopsConfig;
	$myts =& MyTextSanitizer::getInstance();
	$fhometext = "";
	$block = array();
	$block['title_wfsection'] = _MB_KUHT_TITLE_SPOTLIGHT_WFSS;
	$block['lang_by']         = _MB_KUHT_BY;
	$block['lang_read']       = _MB_KUHT_READ;
	$block['lang_rating']     = _MB_KUHT_RATING;
	$block['lang_write']      = _MB_KUHT_WRITE;

	$var = $xoopsDB->query("SELECT item, auto, image, auto_image, image_align FROM ".$xoopsDB->prefix("spotlight")." WHERE sid = 2",1,0);
	list ($item, $auto, $image, $auto_image, $image_align) = $xoopsDB->fetchRow($var);
	if ($auto == 0) {
		// no auto selection
		$result = $xoopsDB->query("SELECT articleid, uid, title, summary, TRUNCATE(rating,1) FROM ".$xoopsDB->prefix("wfs_article")." WHERE articleid=".$item." ",1,0);
	} else {
		// auto selection
		$result = $xoopsDB->query("SELECT articleid, uid, title, summary, TRUNCATE(rating,1) FROM ".$xoopsDB->prefix("wfs_article")." WHERE changed < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") ORDER BY changed DESC",1,0);
	}
	list ($fsid, $fautore, $ftitle, $fsummary, $frating) = $xoopsDB->fetchRow($result);

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
			$var_image = $xoopsDB->query("SELECT categoryid FROM ".$xoopsDB->prefix("wfs_article")." WHERE articleid=".$fsid."",1,0);
			list ($patt_image) = $xoopsDB->fetchRow($var_image);
			$var_image2 = $xoopsDB->query("SELECT imgurl FROM ".$xoopsDB->prefix("wfs_category")." WHERE id=".$patt_image."",1,0);
			list ($image_display) = $xoopsDB->fetchRow($var_image2);
			$block['image_display'] = $image_display;
			if ($image_align == "R") {
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
		$block['articletitle']      = $ftitle;
		$block['uid']               = $myts->makeTboxData4Show($uidutente);
		$block['author']            = $myts->makeTboxData4Show($fautorevero);
		$block['summary_wfsection'] = $myts->xoopsCodeDecode($fsummary);	// BB Codes
		$block['summary_wfsection'] = $myts->nl2Br($block['summary_wfsection']);
		$block['articleid']         = $myts->makeTboxData4Show($fsid);
		$block['rating']            = $myts->makeTboxData4Show($frating);
	}

	if ($options[1] == 1) {
		$block['lang_other_articles'] = _MB_KUHT_OTHER_WFSSTEXT;
		$nsql = "SELECT articleid, title, summary, changed, published, expired, TRUNCATE(rating,1) FROM ".$xoopsDB->prefix("wfs_article")." WHERE changed < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") AND articleid != ".$fsid." ORDER BY ".$options[2]." DESC";
		$nresult = $xoopsDB->query($nsql,$options[3],0);
		while ( $myrow = $xoopsDB->fetchArray($nresult) ) {
			$wfss = array();
			$title = $myts->makeTboxData4Show($myrow["title"]);
			$summary = $myts->makeTboxData4Show($myrow["summary"]);
			if ( !XOOPS_USE_MULTIBYTES ) {
				if (strlen($myrow['title']) >= $options[4]) {
// hhts ml multilingo
					$title = substr($myts->makeTboxData4Show($myrow['title']),0,($options[4] -1))."...";
				}
			}
			$wfss['title'] = $title;
			$wfss['id'] = $myrow['articleid'];
			if ($options[2] == "published") {
				$wfss['hitsordate'] = formatTimestamp($myrow['published'],"s");
// hhts toegevoegd new graphic
  		        $wfss['graphic'] = wfsnewgraphic($myrow['published'], 7);
			} elseif ($options[2] == "changed") {
				$wfss['hitsordate'] = formatTimestamp($myrow['changed'],"s");
//			$wfss['graphic'] = wfsnewgraphic($myrow['changed'], 7);
			} else {
				$wfss['hitsordate'] = $myrow['rating'];
			}
			if ($options[6] == 1) {
				if (strlen($summary) >= $options[7]) {
// hhts ml multilingo
					$summary = substr($myts->makeTboxData4Show($summary),0,($options[7] -1))."...";
				}
				$block['textview'] = 1;
			} else {
				$block['textview'] = 0;
			}
			$wfss['summary'] = $summary;
			$block['articles'][] = $wfss;
		}
	}

	if ($options[5] == 1) {
		// rb topic select form for news direct topic access
		$topic_options = '';
		$block['catsel'] = '';
		$sql = "SELECT id, title FROM ".$xoopsDB->prefix("wfs_category")." order by title ASC";
		if (!$r = $xoopsDB->query($sql)) {
			exit();
		}
		if ($row = $xoopsDB->fetchArray($r)) {
			do {
			$id = $row['id'];
			$title =$myts->makeTboxData4Show($row['title']);
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
			$block['catsel'] = '<form action="'.XOOPS_URL.'/modules/wfsection/index.php? method="post">'."\n";
			$block['catsel'].= '<select name="category" onchange="submit();">'."\n";
			$block['catsel'].= '<option value="0" selected>'._MB_KUHT_CHOOSE_WFSS.'</option>'."\n";
			$block['catsel'].= $topic_options."\n";
			$block['catsel'].= '</select></form>'."\n";
		}
		// END rb topic select form for news direct topic access
	}
// todo: ministats for wfsections.
	if ($options[8] == 1) {
		$block['lang_ministats'] = '<span style="font-size: 9px; text-transform: uppercase">'._MB_KUHT_MINISTATS.'</span>';

		$result = $xoopsDB->query("SELECT count(*) FROM ".$xoopsDB->prefix("wfs_article")."");
		list($news) = $xoopsDB->fetchRow($result);

		$result = $xoopsDB->query("select sum(counter) FROM ".$xoopsDB->prefix("wfs_article")."");
		list($storiesviews) = $xoopsDB->fetchRow($result);

		$result = $xoopsDB->query("SELECT TRUNCATE((sum(rating)/".$news."),1) FROM ".$xoopsDB->prefix("wfs_article")."");
		list($rating) = $xoopsDB->fetchRow($result);

		$result = $xoopsDB->query("select sum(votes) FROM ".$xoopsDB->prefix("wfs_article")."");
		list($votes) = $xoopsDB->fetchRow($result);

		$block['ministats'] = "\n".'<span style="font-size: 9px;">'."\n";
		$block['ministats'].= _MB_KUHT_PUBLISHED.': <b>'.$news.'</b> : '."\n";
		$block['ministats'].= _MB_KUHT_READS.': <b>'.$storiesviews.'</b> : '."\n";
// hhts uitge commend
//		$block['ministats'].= _MB_KUHT_WFSSRATING.': <b>'.$rating.'</b> '._MB_KUHT_AVERAGE."\n";
//		$block['ministats'].= ' <b>'.$votes.'</b> '._MB_KUHT_WFSSVOTES."\n";
		$block['ministats'].= '</span>'."\n";
	}

	if ($options[9] == 1) {
		$block['select_template'] = 1;
	}
	return $block;
}

function b_head_kuht_edit_wfsection($options)
{
	$form = "\n".'<p>'._MB_KUHT_TITLECHARS.'&nbsp;<input type="text" size="2" name="options[0]" value="'.$options[0].'" />&nbsp;'._MB_KUHT_TITLELENGTH.'&nbsp;</p>'."\n";
	$form.= '<b>'._MB_KUHT_OTHER_WFSS.'</b><br />'."\n";
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
	$form.= '>&nbsp;'._MB_KUHT_CHNG.'</option>'."\n";
	$form.= '<option value="changed"';
	if ($options[2] == "changed") {
		$form .= ' selected="selected"';
	}
	$form.= '>&nbsp;'._MB_KUHT_DATE.'</option>'."\n";
	$form.= '<option value="rating"';
	if ($options[2] == "rating") {
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
