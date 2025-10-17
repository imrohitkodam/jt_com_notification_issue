<?php
/**
 * @package     JLike
 * @subpackage  mod_lms_categorylist
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
require_once JPATH_SITE . '/components/com_jlike/helper.php';

$input = Factory::getApplication()->input;
$post	= $input->post;

$session = Factory::getSession();
$Itemid = Factory::getApplication()->input->get('Itemid');

if ($Itemid)
{
	$session->set('JT_Itemid', $Itemid);
}

$doc = Factory::getDocument();
$doc->addStyleSheet(JURI::base() . 'modules/mod_jlike_most_likes/css/most_like.css');
$limit = $params->get('no_of_likes');
$showlikescount = $params->get('showlikescount');
$jlikehelperobj = new comjlikeHelper;
$mostlikes = $jlikehelperobj->GetMostLikes($limit);
$lang = Factory::getLanguage();
$lang->load('mod_jlike_most_likes', JPATH_SITE);
$layout = ModuleHelper::getLayoutPath('mod_jlike_most_likes');

require_once $layout;
