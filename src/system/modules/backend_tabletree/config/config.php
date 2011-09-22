<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the widget configuration file.
 *
 * PHP version 5
 * @copyright  2008 Thyon Design
 * @author     John Brand <john.brand@thyon.com> 
 * @package    TableTree 
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['BE_FFL']['tableTree'] = 'TableTree';

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_HOOKS']['executePreActions'][] = array('TableTree', 'executePreActions');
	$GLOBALS['TL_HOOKS']['executePostActions'][] = array('TableTree', 'executePostActions');

/**
 * TL Icons Configuration
 */

	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_page'] = array('pagemounts.gif', 'regular.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_article'] = array('regular.gif', 'articles.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_news_archive'] = array('news.gif', 'news.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_news'] = array('news.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_news_comments'] = array('system/modules/news/html/comments.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_comments'] = array('system/modules/comments/html/icon.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_calendar'] = array('system/modules/calendar/html/icon.gif', 'system/modules/calendar/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_calendar_events'] = array('system/modules/calendar/html/icon.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_faq_category'] = array('system/modules/faq/html/icon.gif', 'system/modules/faq/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_faq'] = array('system/modules/faq/html/icon.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_form'] = array('form.gif', 'form.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_flash'] = array('flash.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_layout'] = array('layout.gif', 'layout.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_log'] = array('log.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_module'] = array('modules.gif', 'modules.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_member'] = array('mgroup.gif', 'member.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_member_group'] = array('mgroup.gif', 'mgroup.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_user'] = array('group.gif', 'user.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_user_group'] = array('group.gif', 'group.gif');	
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_newsletter'] = array('system/modules/newsletter/html/icon.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_newsletter_channel'] = array('system/modules/newsletter/html/icon.gif', 'system/modules/newsletter/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_newsletter_recipients'] = array('mgroup.gif', 'member.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_search'] = array('system/modules/development/html/labels.gif', 'regular.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_search_index'] = array('system/modules/development/html/labels.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_style_sheet'] = array('css.gif', 'css.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_style'] = array('css.gif', 'iconCSS.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_task'] = array('taskcenter.gif', 'taskcenter.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_task_status'] = array('taskcenter.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_undo'] = array('undo.gif', 'iconPLAIN.gif');


/**
 * Custom Application Icons
 */

	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_catalog_types'] = array('system/modules/catalog/html/icon.gif', 'system/modules/catalog/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_gallery_archive'] = array('system/modules/gallery/html/gallery.gif', 'system/modules/gallery/html/gallery.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_gallery'] = array('system/modules/gallery/html/gallery.gif', 'iconJPG.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_invitation_group'] = array('system/modules/invitation/html/icon.gif', 'system/modules/invitation/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_invitation'] = array('system/modules/invitation/html/icon.gif', 'iconPLAIN.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_quickpoll'] = array('system/modules/quickpoll/html/icon.gif', 'system/modules/quickpoll/html/icon.gif');
	$GLOBALS['TL_CONFIG']['tableTree']['icon']['tl_taxonomy'] = array('system/modules/taxonomy/html/icon.gif', 'iconPLAIN.gif');



}



?>