<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * @package		jomLike
 * @author 		Techjoomla http://www.techjoomla.com
 * @copyright 	Copyright (C) 2011-2012 Techjoomla. All rights reserved.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
jimport('joomla.application.component.view');

class jLikeViewButtonset extends HtmlView
{
	public function display($tpl = null)
	{
		$JlikeHelper = new JLikeHelper();
		$JlikeHelper->addSubmenu('buttonset');
		$list       =  $this->get('Data');
		$this->list = $list;

		$this->_setToolBar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	public function _setToolBar()
	{
		ToolbarHelper::title(Text::_('COM_JLIKE_BTN_SETTING'), 'jlike.png');
		ToolbarHelper::apply();
	}
}
