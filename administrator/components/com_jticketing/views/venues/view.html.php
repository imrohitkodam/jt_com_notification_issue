<?php
/**
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * View class for a list of Jticketing.
 *
 * @since  1.6
 */
class JticketingViewVenues extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JticketingHelper::addSubmenu('venues');

		// Get component params
		$this->params     = ComponentHelper::getParams('com_jticketing');

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  Toolbar instance
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state   = $this->get('State');
		$canDo = JticketingHelper::getActions();

		if (JVERSION >= '3.0')
		{
			ToolBarHelper::title(Text::_('COM_JTICKETING_TITLE_VENUES'), 'book');
		}
		else
		{
			ToolBarHelper::title(Text::_('COM_JTICKETING_TITLE_VENUES'), 'courses.png');
		}

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/venue';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('venue.add', 'JTOOLBAR_NEW');
			}

			if ($canDo->get('venue.edit') && isset($this->items[0]))
			{
				ToolBarHelper::editList('venue.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				ToolBarHelper::divider();
				ToolBarHelper::custom('venues.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolBarHelper::custom('venues.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				ToolBarHelper::deleteList('COM_JTICKETING_VENUE_DELETE_MSG', 'venues.delete', 'JTOOLBAR_DELETE');
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.statefilter') == -2 && $canDo->get('core.delete'))
			{
				ToolBarHelper::deleteList('COM_JTICKETING_VENUE_DELETE_MSG', 'venues.delete', 'JTOOLBAR_EMPTY_TRASH');
				ToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				ToolBarHelper::trash('venues.trash', 'JTOOLBAR_TRASH');
				ToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_jticketing');
		}
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
