<?php
/**
 * @package     tjcertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2023 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * tjcertificate utility class for common methods
 *
 * @since  j4x
 */
class TJCertificateHelper
{
    /**
	 * Get item id of url
	 *
	 * @param   string  $link          link
	 * @param   string  $skipIfNoMenu  skipIfNoMenu
	 *
	 * @return  int  Itemid of the given link
	 *
	 * @since   j4x
	 */
	public function getItemId($link, $skipIfNoMenu = 0)
	{
		$itemid    		= 0;
		$parsedLinked 	= array();

		parse_str($link, $parsedLinked);

		$app = Factory::getApplication();
		$menu = $app->getMenu();

		$jinput = Factory::getApplication();

		if ($jinput->isClient('site'))
		{
			$items = $menu->getItems('link', $link);

			if (isset($items[0]))
			{
				$itemid = $items[0]->id;
			}
		}

		if (isset($itemid) && isset($parsedLinked['view']))
		{
			$db		= Factory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select($db->qn('id'));
			$query->from($db->qn('#__menu'));
			$query->where($db->qn('link') . ' LIKE ' . $db->Quote('%' . $link . '%'));
			$query->where($db->qn('published') . '=' . $db->Quote(1));
			$query->where($db->qn('client_id') . '=' . $db->Quote(0));
			$query->setLimit(1);
			$db->setQuery($query);
			$itemid = $db->loadResult();
		}

		if (!$itemid)
		{
			if ($skipIfNoMenu)
			{
				$itemid = 0;
			}
			else
			{
				$input = $jinput->input;
				$itemid = $input->get('Itemid', '0', 'INT');
			}
		}

		return $itemid;
	}
}