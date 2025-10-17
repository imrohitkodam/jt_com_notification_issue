<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
/**
 * @package		jomLike
 * @author 		Techjoomla http://www.techjoomla.com
 * @copyright 	Copyright (C) 2011-2012 Techjoomla. All rights reserved.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

jimport ( 'joomla.plugin.plugin' );

//Load language file
$lang =  Factory::getLanguage();
$lang->load('plg_jlike_jomsocial', JPATH_ADMINISTRATOR);

class plgCommunityJlike_jomsocial extends CMSPlugin {

	public function onBeforeDisplaylike()
	{
		$app=Factory::getApplication();

		if($app->getName()!='site')
		{
			return;
		}

		$app        = Factory::getApplication ( 'site' );
		$task       = Factory::getApplication()->input->get('task');
		$view       = Factory::getApplication()->input->get('view');
		$item_route = JURI::getInstance()->toString();

		require_once(JPATH_SITE.'/components/com_jlike/helper.php');
		$Itemid  = Factory::getApplication()->input->get('Itemid');
		$element = '';
		$option  = Factory::getApplication()->input->get('option');

		if (!$option)
		{
			$option = $_REQUEST['option'];
		}

		$item_route = JURI::getInstance()->toString();

		$requestvars_custom = array('albumid','photoid','videoid','view','Itemid');
		$requestvars_joomla = array('option','task','controller','view','Itemid');

		$db = Factory::getDBO ();

		$columnName = 'title';
		$dbTable = '#__community_' . ('s' === substr ( $element, - 1, 1 ) ? $element : $element . 's');
		$title = 'this'; // Generic anchor text for link

		$task = Factory::getApplication()->input->get('task');
		$view = Factory::getApplication()->input->get('view');

		if ($task=='')
		{
			$element = $view;
		}
		else
		{
			$element = $task;
		}

		if (!$element)
		{
			if (strstr($_REQUEST['func'],'videos'))
			{
				$element = 'videos';
			}
		}

		$userid  = CFactory::getUser()->id;
		$cont_id = '';

		$show_comments = -1;

		switch (strtolower($element))
		{
			case 'profile' :
				$cont_id=Factory::getApplication()->input->get('userid');
				$columnName = 'username';
				$dbTable = '#__users';
				$jlikeelement="com_community.profile";
				$title = 'this profile';
				$route = "index.php?option=com_community&userid={$cont_id}&view={$element}&Itemid=".$Itemid;
			break;

			case 'video':
				$cont_id=Factory::getApplication()->input->get('videoid');
				$db->setQuery ( "SELECT creator FROM #__community_videos WHERE id={$cont_id}" );
				$row = $db->loadAssoc();
				$dbTable='#__community_videos';
				$title = 'this video';
				$route = "index.php?option=com_community&view=videos&task=video&videoid={$cont_id}&userid={$row['creator']}&Itemid=".$Itemid;

				$jlikeelement="com_community.videos";

			break;

			case 'photo':
			case 'album':
				$dbTable='#__community_photos';
				$cont_id=$photoid=Factory::getApplication()->input->get('photoid');
				$albumid=Factory::getApplication()->input->get('albumid');
				$db->setQuery ( "SELECT albumid,creator FROM #__community_photos WHERE id={$albumid}");
				$row = $db->loadAssoc();
				$columnName = 'caption';
				if($element=='photo')
				{
					$columnName = 'caption';
					$dbTable='#__community_photos';
					$cont_id=$photoid;
					$jlikeelement="com_community.photo";
					$route ="index.php?option=com_community&view=photos&task=photo&albumid={$albumid}&userid={$row['creator']}#photoid={$photoid}&Itemid=".$Itemid;

				}
				else if($element=='album')
				{
										$columnName = 'name';

					$dbTable='#__community_photos_albums';
					$cont_id=$albumid;
					$jlikeelement="com_community.album";
					$route ="index.php?option=com_community&view=photos&task=photo&albumid={$albumid}&userid={$row['creator']}&Itemid=".$Itemid;

				}
			break;

			case 'viewgroup':
			case 'viewmembers':
				$cont_id=$groupid=Factory::getApplication()->input->get('groupid');
				$title = 'this group';
				$columnName = 'name';
				$dbTable='#__community_groups';
				$jlikeelement="com_community.groups";
				$route ="index.php?option=com_community&view=groups&task=viewgroup&groupid={$groupid}&Itemid=".$Itemid;

			break;

			case 'viewevent':
			case 'viewguest':
				$cont_id=$eventid=Factory::getApplication()->input->get('eventid');
				$dbTable='#__community_events';
				$title = 'title';
				$jlikeelement="com_community.events";
				$route = "index.php?option=com_community&view=events&task=viewevent&eventid={$eventid}&Itemid=".$Itemid;
			break;
		}

		if(!$cont_id)
		{
			return;
		}

		$sql = "SELECT `{$columnName}` FROM `{$dbTable}` WHERE id={$cont_id}";
		$db->setQuery ( $sql );
		$title = $db->loadResult () ? $db->loadResult () : $title;

		$show_like_buttons = 1;

		Factory::getApplication()->input->set ( 'data', json_encode ( array ('cont_id'=>$cont_id,'element' => $jlikeelement, 'title' => $title, 'url' => $route,'plg_name'=>'jlike_jomsocial','show_comments'=>$show_comments, 'show_like_buttons'=>$show_like_buttons) ) );

		require_once(JPATH_SITE.'/'.'components/com_jlike/helper.php');
		$jlikehelperObj=new comjlikeHelper();
		$html = $jlikehelperObj->showlike();
		echo $html;

	}

	/** Trigger for comments to replace smiley as image after status update for main comments **/
	function onBeforeStreamCreate($activity) ///trigger present in SOME versions of jomsocial
	{
		return true;
	}
}
