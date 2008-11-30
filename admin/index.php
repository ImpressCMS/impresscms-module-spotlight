<?PHP
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
include('admin_header.php');
include_once(XOOPS_ROOT_PATH.'/modules/news/class/class.newsstory.php');
include_once(XOOPS_ROOT_PATH.'/modules/news/class/xoopsuser.php');

function configStart()
{
	echo '<fieldset class="outer">'."\n"
		.'<legend><b>'._AM_KUHT_NAME_CONF.'</b></legend>'."\n"
		.'<p><a href="index.php?op=news">&nbsp;- News Block :</a></p>'."\n"
		.'<p><a href="index.php?op=wfsections">&nbsp;- WF-Section Block :</a></p>'."\n"
		.'</fieldset>'."\n";
}

function spotlightNewsForm()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    $storyarray = NewsStory::getAllPublished(10, 0, 0, 1);

    // Shows last 10 published stories
    echo '<fieldset class="outer">'."\n"
		.'<legend><a href="index.php">'._AM_KUHT_NAME_CONF.'</a><b>'._AM_KUHT_NAME_NEWS.'</b></legend>'."\n"
		.'<table class="outer" cellspacing="1" width="100%">'."\n"
        .'<tr>'."\n"
        .'<th align="center" colspan="6"><b>'._AM_LAST10ARTS.'</b></th>'."\n"
        .'</tr>'."\n"
        .'<tr>'."\n"
        .'<td class="head" align="center">ID</td>'."\n"
        .'<td class="head" align="center">'._AM_TITLE.'</td>'."\n"
        .'<td class="head" align="center">'._AM_TOPIC.'</td>'."\n"
        .'<td class="head" align="center">'._AM_POSTER.'</td>'."\n"
        .'<td class="head" align="center">'._AM_PUBLISHED.'</td>'."\n"
        .'<td class="head" align="center">'._AM_ACTION.'</td>'."\n"
        .'</tr>'."\n";
    foreach ($storyarray as $eachstory) {
        $published = formatTimestamp($eachstory->published());
        $topic = $eachstory->topic();
        echo '<tr>'."\n"
            .'<td class="even" align="center"><b>'.$eachstory->storyid().'</b></td>'."\n"
            .'<td class="odd" align="left"><a href="'.XOOPS_URL.'/modules/news/article.php?storyid='.$eachstory->storyid().'">'.$eachstory->title().'</a></td>'."\n"
            .'<td class="even" align="center">'.$topic->topic_title().'</td>'."\n"
            .'<td class="odd" align="center"><a href="'.XOOPS_URL.'/userinfo.php?uid='.$eachstory->uid().'">'.$eachstory->uname().'</a></td>'."\n"
            .'<td class="even" align="center">'.$published.'</td>'."\n"
            .'<td class="odd" align="center"><a href="/modules/news/admin/index.php?op=edit&amp;storyid='.$eachstory->storyid().'">'._AM_EDIT.'</a>-'
            .'<a href="/modules/news/admin/index.php?op=delete&amp;storyid='.$eachstory->storyid().'">'._AM_DELETE.'</a></td>'."\n"
            .'</tr>'."\n";
    }
    echo '</table><br />'."\n";

    $sql = $xoopsDB->query("SELECT item, auto, catid, auto_cat, image, auto_image, image_align FROM ".$xoopsDB->prefix("spotlight")." WHERE sid=1",1,0);
    list ($item, $auto, $catid, $auto_cat, $image, $auto_image, $image_align) = $xoopsDB->fetchRow($sql);

    echo '<form name="form_spotlight" method="post" action="index.php?op=submitNews">'."\n"
        .'<table class="outer" cellspacing="1" width="100%">'."\n";

	echo '<tr>'."\n"
        .'<td class="head" width="20%" align="right" nowrap><b>'._AM_SELECT_NEWS_AUTO.' :&nbsp;</b></td>'."\n"
        .'<td class="even" width="20%" align="left"><select size="1" name="auto">'."\n";
    if ($auto == 1) {
        echo '<option selected> On </option><option>Off</option>'."\n";
    } else {
        echo '<option selected> Off </option><option>On</option>'."\n";
    }
    echo '</select>'."\n"
        .'</td>'."\n"
        .'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_NEWS_AUTO_DESC.'</td>'."\n"
        .'</tr>'."\n";

    if ($auto == 0) {
		echo '<tr>'."\n"
			.'<td class="head" width="20%" align="right"><b>'._AM_SELECT_NEWS.' :&nbsp;</b></td>'."\n"
			.'<td class="even" width="20%" align="left"><input type="text" name="item" size="2" value="'.$item.'"></td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_NEWS_DESC.'</td>'."\n"
			.'</tr>'."\n"
			.'<tr>'."\n"
			.'<td class="head" width="20%" align="right"><b>'._AM_SELECT_IMG.' :&nbsp;</b></td>'."\n"
			.'<td class="even" width="20%" align="left"><input type="text" name="image" size="20" value="'.$image.'"></font></td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_IMG_DESC.'</td>'."\n"
			.'</tr>'."\n";
	} else {
		echo '<input type="hidden" name="item" value="'.$item.'">'."\n"
			.'<input type="hidden" name="image" value="'.$image.'">'."\n";
	}
//=========
	echo '<tr>'."\n"
        .'<td class="head" width="20%" align="right" nowrap><b>'._AM_SELECT_NEWS_AUTO_CATEGORY.' :&nbsp;</b></td>'."\n"
        .'<td class="even" width="20%" align="left"><select size="1" name="auto_cat">'."\n";
    if ($auto == 1 && $auto_cat == 1) {
        echo '<option selected> On </option><option>Off</option>'."\n";
    } else {
        echo '<option selected> Off </option><option>On</option>'."\n";
    }
    echo '</select>'."\n"
        .'</td>'."\n"
        .'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_NEWS_AUTO_CATEGORY_DESC.'</td>'."\n"
        .'</tr>'."\n";

    if ($auto == 1 && $auto_cat == 0) {
		echo '<tr>'."\n"
			.'<td class="head" width="20%" align="right">'._AM_SELECT_NEWS_CATEGORY.'</td>'."\n"
			.'<td class="even" width="20%" align="left">'."\n";
		$xt = new XoopsTopic($xoopsDB->prefix("topics"));
		if (isset($catid)) {
			$xt->makeTopicSelBox(0, $catid, "catid");
		} else {
			$xt->makeTopicSelBox(0, 0, "catid");
		}
		echo '</td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_NEWS_CATEGORY_DESC.'</td>'."\n"
			.'</tr>'."\n";
	} else {
		echo '<input type="hidden" name="catid" value="0">'."\n";
	}
//=========

    echo '<tr>'."\n"
        .'<td class="head" width="20%" align="right" nowrap><b>'._AM_SELECT_NEWS_AUTO_IMG.' :&nbsp;</b></td>'."\n"
        .'<td class="even" width="20%" align="left"><select size="1" name="auto_image">'."\n";
    if ($auto_image == 1) {
        echo '<option selected> On </option><option>Off</option>'."\n";
    } else {
        echo '<option selected> Off </option><option>On</option>'."\n";
    }
    echo '</select>'."\n"
        .'</td>'."\n"
        .'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_NEWS_AUTO_IMG_DESC.'</td>'."\n"
        .'</tr>'."\n";
    if ($auto_image == 0) {
		echo '<tr>'."\n"
			.'<td class="head" width="20%" align="right"><b>'._AM_IMAGE_ALIGN.' :&nbsp;</b></td>'."\n"
			.'<td class="even" width="20%" align="left">'."\n";
		if ($image_align == "L") {
			$checkL = 'checked="checked"';
		} else {
			$checkR = 'checked="checked"';
		}
		echo '<input type="radio" name="image_align" value="L" '.$checkL.' />L'."\n"
			.'<input type="radio" name="image_align" value="R" '.$checkR.' />R'."\n"
			.'</td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_IMAGE_ALIGN_DESC.'</td>'."\n"
			.'</tr>'."\n";
	} else {
		echo '<input type="hidden" name="image_align" value="'.$image_align.'" />'."\n";
	}
	echo '<tr>'."\n"
        .'<td class="head" width="100%" align="center" colspan="3">'."\n"
        .'<input type="submit" value="'._AM_SUBMIT.'" name="submit">'."\n"
        .'<input type="reset" value="'._AM_RESET.'" name="reset">'."\n"
        .'</td>'."\n"
        .'</tr>'."\n"
        .'</table>'."\n"
        .'</form>'."\n"
		.'</fieldset>'."\n";
}

function spotlightWFSectionForm()
{
    global $xoopsDB,$xoopsUser,$xoopsConfig;

    echo '<fieldset class="outer">'."\n"
		.'<legend><a href="index.php">'._AM_KUHT_NAME_CONF.'</a><b>'._AM_KUHT_NAME_WFSS.'</b></legend>'."\n"
		.'<table class="outer" cellspacing="1" width="100%">'."\n"
    	.'<tr class="head">'."\n"
        .'<tr>'."\n"
        .'<th align="center" colspan="8"><b>'._AM_LAST10ARTS.'</b></th>'."\n"
        .'</tr>'."\n"
		.'<td class="head" align="center"><b>'._AM_STORYID.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_TITLE.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_CATEGORYT.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_POSTER.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_PUBLISHED.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_CHANGED.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_WEIGHT.'</td>'."\n"
		.'<td class="head" align="center"><b>'._AM_ACTION.'</td></b>'."\n"
		.'</tr>'."\n";
	$sql = ("SELECT articleid, title, categoryid, uid, changed, weight, published FROM ".$xoopsDB->prefix("wfs_article")." WHERE changed < ".time()." AND published > 0 AND (expired = 0 OR expired > ".time().") ORDER BY changed DESC;");
	$result = $xoopsDB->query($sql,0,10);
	if (!$result) {
		echo '<tr ><td align="center" colspan ="8" class="head"><b>'._AM_NOTFOUND.'</b></td></tr>';
	} else {
		while (list ($articleid, $title, $catid, $uid, $changed, $weight, $published) = $xoopsDB->fetchRow($result)) {
			echo '<tr>'."\n"
				.'<td align="center" class="head"><b>'.$articleid.'</b></td>'."\n"
				.'<td align="left" class="even"><a href="/modules/wfsection/article.php?articleid='.$catid.'">'.$title.'</a></td>'."\n";
			$sql2 = ("SELECT description FROM ".$xoopsDB->prefix("wfs_category")." WHERE id=".$catid.";");
			$result2 = $xoopsDB->query($sql2,0,1);
			while (list ($description) = $xoopsDB->fetchRow($result2)) {
				echo '<td align="center" class="odd">'.$description.'</td>'."\n";
			}
			$uname = XoopsUser::getUnameFromId($uid);
			echo '<td align="center" class="even"><a href="/userinfo.php?uid='.$uid.'">'.$uname.'</a></td>'."\n"
				.'<td align="center" class="odd">'.formatTimestamp($published,"m").'</td>'."\n"
				.'<td align="center" class="even">'.formatTimestamp($changed,"m").'</td>'."\n"
				.'<td align="center" class="odd">'.$weight.'</td>'."\n"
				.'<td align="center" class="even"><a href="/modules/wfsection/admin/index.php?op=edit&articleid='.$articleid.'">'._AM_EDIT.'</a>-'
				.'<a href="/modules/wfsection/admin/index.php?op=delete&articleid='.$articleid.'">'._AM_DELETE.'</a></td>'."\n"
				.'</tr>'."\n";
		}
	}
	echo '</table><br />'."\n";

    $sql3 = ("SELECT item, auto, image, auto_image, image_align FROM ".$xoopsDB->prefix("spotlight")." WHERE sid=2;");
    $result3 = $xoopsDB->query($sql3,0,1);
    list ($item, $auto, $image, $auto_image, $image_align) = $xoopsDB->fetchRow($result3);

    echo '<form name="form_spotlight" method="post" action="index.php?op=submitWFSection">'."\n"
        .'<table class="outer" cellspacing="1" width="100%">'."\n"
        .'<tr>'."\n"
        .'<td class="head" width="20%" align="right"><b>'._AM_SELECT_WFSS_AUTO.' :&nbsp;</b></td>'."\n"
        .'<td class="even" width="20%" align="left"><select size="1" name="auto">'."\n";
    if ($auto == 1) {
        echo '<option selected> On </option><option>Off</option>'."\n";
    } else {
        echo '<option selected> Off </option><option>On</option>'."\n";
    }
    echo '</select>'."\n"
        .'</td>'."\n"
        .'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_WFSS_AUTO_DESC.'</td>'."\n"
        .'</tr>'."\n";
    if ($auto == 0) {
		echo '<tr>'."\n"
			.'<td class="head" width="20%" align="right"><b>'._AM_SELECT_WFSS.' :&nbsp;</b></td>'."\n"
			.'<td class="even" width="20%" align="left"><input type="text" name="item" size="2" value="'.$item.'"></td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_WFSS_DESC.'</td>'."\n"
			.'</tr>'."\n"
			.'<tr>'."\n"
			.'<td class="head" width="20%" align="right"><b>'._AM_SELECT_IMG.' :&nbsp;</b></td>'."\n"
			.'<td class="even" width="20%" align="left"><input type="text" name="image" size="20" value="'.$image.'"></font></td>'."\n"
			.'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_IMG_DESC.'</td>'."\n"
			.'</tr>'."\n";
	} else {
		echo '<input type="hidden" name="item" value="'.$item.'">'."\n"
			.'<input type="hidden" name="image" value="'.$image.'">'."\n";
	}
	echo '<tr>'."\n"
        .'<td class="head" width="20%" align="right"><b>'._AM_SELECT_WFSS_AUTO_IMG.' :&nbsp;</b></td>'."\n"
        .'<td class="even" width="20%" align="left"><select size="1" name="auto_image">'."\n";
    if ($auto_image == 1) {
        echo '<option selected> On </option><option>Off</option>'."\n";
    } else {
        echo '<option selected> Off </option><option>On</option>'."\n";
    }
    echo '</select>'."\n"
        .'</td>'."\n"
        .'<td class="odd" width="60%" align="left" nowrap>'._AM_SELECT_WFSS_AUTO_IMG_DESC.'</td>'."\n"
        .'</tr>'."\n";
	echo '<tr>'."\n"
		.'<td class="head" width="20%" align="right"><b>'._AM_IMAGE_ALIGN.' :&nbsp;</b></td>'."\n"
		.'<td class="even" width="20%" align="left">'."\n";
	if ($image_align == "L") {
		$checkL = 'checked="checked"';
	} else {
		$checkR = 'checked="checked"';
	}
	echo '<input type="radio" name="image_align" value="L" '.$checkL.' />L'."\n"
		.'<input type="radio" name="image_align" value="R" '.$checkR.' />R'."\n"
		.'</td>'."\n"
		.'<td class="odd" width="60%" align="left" nowrap>'._AM_IMAGE_ALIGN_DESC.'</td>'."\n"
		.'</tr>'."\n"
		.'<tr>'."\n"
        .'<td class="head" width="100%" align="center" colspan="3">'."\n"
        .'<input type="submit" value="'._AM_SUBMIT.'" name="submit">'."\n"
        .'<input type="reset" value="'._AM_RESET.'" name="reset">'."\n"
        .'</td>'."\n"
        .'</tr>'."\n"
        .'</table>'."\n"
        .'</form>'."\n"
		.'</fieldset>'."\n";
}

if (isset($HTTP_POST_VARS)) {
	foreach ($HTTP_POST_VARS as $k => $v) {
		$$k = $v;
	}
}

if (isset($HTTP_GET_VARS)) {
	foreach ($HTTP_GET_VARS as $k => $v) {
		$$k = $v;
	}
}

switch ($op) {
case 'submitNews':
    global $xoopsDB;
    $item        = addslashes($HTTP_POST_VARS["item"]);
    $image       = addslashes($HTTP_POST_VARS["image"]);
    $auto        = addslashes($HTTP_POST_VARS["auto"]);
    $catid       = addslashes($HTTP_POST_VARS["catid"]);
    $auto_cat    = addslashes($HTTP_POST_VARS["auto_cat"]);
    $auto_image  = addslashes($HTTP_POST_VARS["auto_image"]);
    $image_align = addslashes($HTTP_POST_VARS["image_align"]);
    if ($auto == 'On') {
        $auto = 1;
    } else {
        $auto = 0;
    }
    if ($auto_cat == 'On') {
        $auto_cat = 1;
		$catid    = 0;
    } else {
        $auto_cat = 0;
    }
    if ($auto_image == 'On') {
        $auto_image = 1;
    } else {
        $auto_image = 0;
    }
    $sql = "UPDATE ".$xoopsDB->prefix("spotlight")." SET item='".$item."', auto='".$auto."', catid='".$catid."', auto_cat='".$auto_cat."', image='".$image."', auto_image='".$auto_image."', image_align='".$image_align."' WHERE sid = 1;";
    if (!$result = $xoopsDB->queryF($sql)) {
        exit('error');
    }
    redirect_header('index.php?op=news',3,_AM_MESSAGE);
    exit();
    break;
case 'submitWFSection':
    global $xoopsDB;
    $item        = addslashes($HTTP_POST_VARS["item"]);
    $image       = addslashes($HTTP_POST_VARS["image"]);
    $auto        = addslashes($HTTP_POST_VARS["auto"]);
    $auto_image  = addslashes($HTTP_POST_VARS["auto_image"]);
    $image_align = addslashes($HTTP_POST_VARS["image_align"]);
    if ($auto == 'On') {
        $auto = 1;
    } else {
        $auto = 0;
    }
    if ($auto_image == 'On') {
		$auto_image = 1;
    } else {
		$auto_image = 0;
	}
	$sql = "UPDATE ".$xoopsDB->prefix("spotlight")." SET item='".$item."', auto='".$auto."', image='".$image."', auto_image='".$auto_image."', image_align='".$image_align."' WHERE sid = 2;";
    if (!$result = $xoopsDB->queryF($sql)) {
        exit('error');
    }
    redirect_header('index.php?op=wfsections',3,_AM_MESSAGE);
    exit();
    break;
case 'news':
    xoops_cp_header();
    spotlightNewsForm();
    xoops_cp_footer();
    break;
case 'wfsections':
	global $xoopsDB;
	xoops_cp_header();
    $sql = "SELECT dirname FROM ".$xoopsDB->prefix("newblocks")." WHERE dirname = 'wfsection';";
    // uncertain check if WF-Sections is installed or not.
	$result = $xoopsDB->query($sql);
	if (!$row = $xoopsDB->fetchRow($result)) {
        exit('No WF-Section found on your system.');
    } else {
		spotlightWFSectionForm();
	}
    xoops_cp_footer();
    break;
case 'start':
default:
    xoops_cp_header();
    configStart();
    xoops_cp_footer();
    break;
}
?>