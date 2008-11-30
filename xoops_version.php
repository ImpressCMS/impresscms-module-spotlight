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
//                                                                           //
//               http://linux.kuht.it  - http://www.kuht.it                  //
//                                                                           //
//                              spark@kuht.it                                //
//                                                                           //
//              Adapted for XOOPS 2.0 by Herko and dAWiLbY                   //
//---------------------------------------------------------------------------//
$modversion['name'] = _MI_KUHT_NAME;
$modversion['version'] = 1.4; // v1.4.2
$modversion['description'] = _MI_KUHT_DESC;
$modversion['author'] = 'Spark [ kuht.it ]<br />Adapted for XOOPS 2.0<br />by<br />Herko and dAWiLbY';
$modversion['credits'] = 'http://linux.kuht.it';
$modversion['help'] = 'spark@kuht.it';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['image'] = 'kuht_slogo.png';
$modversion['dirname'] = 'spotlight';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Blocks
$modversion['blocks'][1]['file'] = 'kuht_head_news.php';
$modversion['blocks'][1]['name'] = _MI_KUHT_BNAME;
$modversion['blocks'][1]['description'] = 'Spotlight - Focus News';
$modversion['blocks'][1]['show_func'] = 'b_head_kuht_show_news';
$modversion['blocks'][1]['edit_func'] = 'b_head_kuht_edit_news';
$modversion['blocks'][1]['options'] = '45|1|published|5|45|0|1|45|1|1';
$modversion['blocks'][1]['template'] = 'news_block_spotlight.html';

$modversion['blocks'][2]['file'] = 'kuht_head_wfsection.php';
$modversion['blocks'][2]['name'] = _MI_KUHT_BNAME1;
$modversion['blocks'][2]['description'] = 'Spotlight - Focus WF-Section';
$modversion['blocks'][2]['show_func'] = 'b_head_kuht_show_wfsection';
$modversion['blocks'][2]['edit_func'] = 'b_head_kuht_edit_wfsection';
$modversion['blocks'][2]['options'] = '45|1|published|5|45|0|1|45|1|1';
$modversion['blocks'][2]['template'] = 'wfsections_block_spotlight.html';

// Database
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'spotlight';

// Menu
$modversion['hasMain'] = 0;
?>
