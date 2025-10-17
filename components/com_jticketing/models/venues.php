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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Methods supporting a list of Jticketing records.
 *
 * @since  1.6
 */
class JticketingModelVenues extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			'id', 'a.id',
			'ordering', 'a.ordering',
			'state', 'a.state',
			'created_by', 'a.created_by',
			'modified_by', 'a.modified_by',
			'name', 'a.name',
			'country', 'a.country',
			'city', 'a.city',
			'zipcode', 'a.zipcode',
			'longitude', 'a.longitude',
			'latitude', 'a.latitude',
			'privacy', 'a.privacy',
			'categoryfilter', 'a.categoryfilter',
			'venue_category', 'a.venue_category',
			'typefilter', 'a.typefilter',
			'privacyfilter', 'a.privacyfilter',
			'statefilter', 'a.statefilter',
			'online','a.online'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app    = Factory::getApplication();
		$userId = Factory::getUser()->id;

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'));
		$this->setState('list.limit', $limit);

		$limitStart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0);
		$this->setState('list.start', $limitStart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');

		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');

			if (!in_array($ordering, $this->filter_fields))
			{
				$ordering = 'ordering';
			}

			$list['ordering'] = $ordering;
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');

		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');

			if (!in_array(strtoupper($orderingDirection), array('ASC', 'DESC')))
			{
				$orderingDirection = 'asc';
			}

			$list['direction'] = $orderingDirection;
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		if (empty($list['ordering']))
		{
			$list['ordering'] = 'ordering';
		}

		if (empty($list['direction']))
		{
			$list['direction'] = 'asc';
		}

		if (isset($list['ordering']))
		{
			$this->setState('list.ordering', $list['ordering']);
		}

		if (isset($list['direction']))
		{
			$this->setState('list.direction', $list['direction']);
		}

		// Load the filter search.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');

		// Filter the search key value
		$search = htmlspecialchars($search ? $search : '', ENT_COMPAT, 'UTF-8');

		$this->setState('filter.search', $search);

		if ($app->isClient('administrator'))
		{
			// Filtering cat_id
			$this->setState('filter.cat', $app->getUserStateFromRequest($this->context . '.filter.categoryfilter', 'filter_categoryfilter', '', 'string'));

			// Filtering type
			$this->setState('filter.type', $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_typefilter', '', 'string'));

			// Filtering Privacy
			$this->setState('filter.privacy', $app->getUserStateFromRequest($this->context . '.filter.privacyfilter', 'filter_privacyfilter', '', 'string'));
		}
		else
		{
			// Filtering type
			$this->setState('venue_type', $app->getUserStateFromRequest($this->context . '.venue_type', 'venue_type', '', 'string'));

			// Filtering Privacy
			$this->setState('venue_privacy', $app->getUserStateFromRequest($this->context . '.venue_privacy', 'venue_privacy', '', 'string'));

			// Filtering login user
			$this->setState('jtUserId', $userId);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		$app = Factory::getApplication();

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__jticketing_venues` AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');

		// Join over categories
		$query->select('c.title AS venue_category');
		$query->join('LEFT', '#__categories AS c ON c.id=a.venue_category');

		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');

		if (!Factory::getUser()->authorise('core.edit', 'com_jticketing'))
		{
			$query->where('a.state = 1');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('( a.name LIKE ' . $search . ' )');
			}
		}

		if ($app->isClient('administrator'))
		{
			// Filter by category
			$cat = $this->getState('filter.categoryfilter');

			if (!empty($cat))
			{
				$query->where('a.venue_category = ' . (int) $cat);
			}

			// Filter by type
			$typeFilter = $this->getState('filter.typefilter');

			if ($typeFilter != "" && ($typeFilter == 0 || $typeFilter == 1))
			{
				$query->where('a.online = ' . (int) $typeFilter);
			}

			// Filter by privacy
			$privacyFilter = $this->getState('filter.privacyfilter');

			if ($privacyFilter != "")
			{
				$query->where('a.privacy = ' . (int) $privacyFilter);
			}

			// Added for getting data - Need in Privacy Plugin
			$userId = $this->getState('jtUserId');

			if ($userId)
			{
				$query->where('a.created_by = ' . (int) $userId);
			}
		}
		else
		{
			// Filter by type
			$typeFilter = $this->getState('venue_type');

			if ($typeFilter != "" && ($typeFilter == 0 || $typeFilter == 1))
			{
				$query->where('a.online = ' . (int) $typeFilter);
			}

			// Filter by privacy
			$privacyFilter = $this->getState('venue_privacy');

			if ($privacyFilter != "")
			{
				$query->where('a.privacy = ' . (int) $privacyFilter);
			}

			$userId = $this->getState('jtUserId');

			if ($userId)
			{
				$query->where('a.created_by = ' . (int) $userId);
			}
		}

		$stateFilter = $this->getState('filter.statefilter');

		// Filter by state
		if (is_numeric($stateFilter))
		{
			$query->where('a.state = ' . (int) $stateFilter);
		}
		elseif ($stateFilter === '')
		{
			$query->where('(a.state IN (0, 1))');
		}
		elseif ($stateFilter == '')
		{
			$query->where('(a.state IN (0,1))');
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$errorDateFormat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$errorDateFormat = true;
			}
		}

		if ($errorDateFormat)
		{
			$app->enqueueMessage(Text::_("COM_JTICKETING_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);

		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}

	/**
	 * Function to get the venue filter options
	 *
	 * @param   INT  $created_by  Fetch creators venue
	 *
	 * @return  array
	 *
	 * @since 3.2.0
	 */
	public function getVenueFilterOptions($created_by = 0)
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select($db->quoteName('e.venue', 'venue_id'));
		$query->select($db->quoteName('v.name', 'title'));
		$query->from($db->quoteName('#__jticketing_events', 'e'));
		$query->join('INNER', $db->quoteName('#__jticketing_venues', 'v') . ' ON (' . $db->quoteName('e.venue') . ' = ' . $db->quoteName('v.id') . ')');

		if ($created_by)
		{
			$query->where('created_by = ' . (int) $created_by);
		}

		$db->setQuery($query);
		$venues = $db->loadObjectList();

		$venueFilter = array();
		$venueFilter[] = HTMLHelper::_('select.option', '', Text::_('COM_JTICKETING_FILTER_SELECT_VENUE'));

		if (!empty($venues))
		{
			foreach ($venues as $eachVenue)
			{
				$venueFilter[] = HTMLHelper::_('select.option', $eachVenue->venue_id, $eachVenue->title);
			}
		}

		return $venueFilter;
	}
}
