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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of Hierarchy.
 *
 * @since  1.6
 */
class HierarchyViewHierarchys extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$user = Factory::getUser();
		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		$tempArr = array();

		// To remove duplicate users from the list
		foreach ($this->items as $item)
		{
			if (isset($tempArr[$item->subuserId]))
			{
				// Found duplicate
				continue;
			}

			// Remember unique item
			$tempArr[$item->subuserId] = $item;
		}

		$this->items = array_values($tempArr);

		$this->pagination = $this->get('Pagination');

		// Get filter form.
		$this->filterForm = $this->get('FilterForm');

		// Get active filters.
		$this->activeFilters = $this->get('ActiveFilters');

		// Fetch client and client ID from URL
		$jinput = Factory::getApplication()->input;
		$this->client = $jinput->get('client');
		$this->clientId = $jinput->get('client_id');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		HierarchyHelper::addSubmenu('hierarchys');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		// Get permissions
		$this->canCreate  = $user->authorise('core.create', 'com_hierarchy');
		$this->canEdit    = $user->authorise('core.edit', 'com_hierarchy');
		$this->canCheckin = $user->authorise('core.manage', 'com_hierarchy');
		$this->canChange  = $user->authorise('core.edit.state', 'com_hierarchy');
		$this->canViewChart = $user->authorise('core.chart.view', 'com_hierarchy');
		$this->canImportCSV = $user->authorise('core.csv.import', 'com_hierarchy');
		$this->canExportCSV = $user->authorise('core.csv.export', 'com_hierarchy');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/hierarchy.php';

		// Import Csv export button
		jimport('techjoomla.tjtoolbar.button.csvexport');

		$bar = JToolBar::getInstance('toolbar');

		$state = $this->get('State');
		$canDo = HierarchyHelper::getActions($state->get('filter.category_id'));

		$message = array();
		$message['success'] = Text::_("COM_HIERARCHY_EXPORT_FILE_SUCCESS");
		$message['error'] = Text::_("COM_HIERARCHY_EXPORT_FILE_ERROR");
		$message['inprogress'] = Text::_("COM_HIERARCHY_EXPORT_FILE_NOTICE");
		$message['btn-name'] = Text::_("COM_HIERARCHY_EXPORT_CSV");

		if ($canDo->get('core.csv.export'))
		{
			$bar->appendButton('CsvExport',  $message);
		}

		ToolbarHelper::title(Text::_('COM_HIERARCHY_TITLE_HIERARCHYS'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/hierarchy';

		$bar = JToolBar::getInstance('toolbar');
		$buttonImport = '<a href="#import_append" class="btn button modal" rel="{size: {x: 800, y: 200}, ajaxOptions: {method: &quot;get&quot;}}">
		<span class="icon-upload icon-white"></span>' . Text::_('COM_HIERARCHY_IMPORT_CSV') . '</a>';

		if ($canDo->get('core.csv.import'))
		{
			$bar->appendButton('Custom', $buttonImport);
		}

		ToolbarHelper::deleteList('', 'hierarchys.remove', 'JTOOLBAR_DELETE');

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->checked_out))
			{
				ToolbarHelper::custom('hierarchys.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::preferences('com_hierarchy');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => Text::_('JGRID_HEADING_ID'),
			'a.user_id' => Text::_('COM_HIERARCHY_HIERARCHYS_USER_ID'),
			'a.subuser_id' => Text::_('COM_HIERARCHY_HIERARCHYS_SUBUSER_ID')
		);
	}
}
