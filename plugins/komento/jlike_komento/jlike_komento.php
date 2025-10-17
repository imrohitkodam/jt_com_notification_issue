<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
/**
 * @package		jLike
 * @author 		Techjoomla http://www.techjoomla.com
 * @copyright 	Copyright (C) 2011-2012 Techjoomla. All rights reserved.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

// Import library dependencies
jimport ( 'joomla.plugin.plugin' );
require_once(JPATH_SITE.'/components/com_jlike/helper.php');
class plgKomentojlike_komento extends CMSPlugin {

function onAfterdisplaylike($context, $addata)
	{
		$app=Factory::getApplication();
		if($app->getName()!='site'){
			return;
		}
		$html='';
		$app = Factory::getApplication ();

		$show_like_buttons=1;
		$show_comments=-1;

		Factory::getApplication()->input->set ( 'data', json_encode ( array ('cont_id' => $addata['id'], 'element' => $context, 'title' => $addata['title'], 'url' => $addata['url'], 'plg_name'=>'jlike_komento', 'show_comments'=>$show_comments, 'show_like_buttons'=>$show_like_buttons ) ) );
		require_once(JPATH_SITE.'/'.'components/com_jlike/helper.php');
		$jlikehelperObj=new comjlikeHelper();
		$html = $jlikehelperObj->showlike();
		echo $html;
   }

}
