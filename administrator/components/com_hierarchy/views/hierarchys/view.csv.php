<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// Import CSV library view
jimport('techjoomla.view.csv');

/**
 * Hierarchy View class that extends TjExportCsv class
 *
 * @since  0.0.1
 */
class HierarchyViewHierarchys extends TjExportCsv
{
	/**
	 * Display the Hierarchy view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$user  = Factory::getUser();
		$userAuthorisedExport = $user->authorise('core.create', 'com_hierarchy');

		if ($userAuthorisedExport != 1 || !$user)
		{
			// Redirect to the list screen.
			$redirect = Route::_('index.php?option=com_hierarchy&view=hierarchys', false);

			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect($redirect);

			return false;
		}
		else
		{
			if ($input->get('task') == 'download')
			{
				$fileName = $input->get('file_name');
				$this->download($fileName);
				$app->close();
			}
			else
			{
				parent::display();
			}
		}
	}
}
