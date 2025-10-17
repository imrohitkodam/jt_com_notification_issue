<?php
/**
 * @version     1.0.0
 * @package     com_jlike
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Techjoomla <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.multiselect');

If (JVERSION >= 3.0)
{
	HTMLHelper::_('bootstrap.tooltip');
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$user = Factory::getUser();

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

?>

<div class="techjoomla-bootstrap">
	<form action="<?php echo Route::_('index.php?option=com_jlike&view=recommendations&layout=recommendtome'); ?>" method="post" name="adminForm" id="adminForm">

		<?php if (count($this->items) == 0 ){ ?>
			<div class="alert alert-info">
				<?php echo Text::_("COM_JLIKE_NO_REC_TO_SHOW"); ?>
			</div>
		<?php return; } ?>

		<div id="no-more-tables">
			<table class="table table-striped" id = "todosList" >
				<thead >
					<tr>
						<th >
						<?php echo HTMLHelper::_('grid.sort',  'COM_JLIKE_RECOMMENDATIONS_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>

						<th >
						<?php echo Text::_("COM_JLIKE_MESSAGE_FROM_SENDER"); ?>
						</th>

						<th >
						<?php echo HTMLHelper::_('grid.sort',  'COM_JLIKE_RECOMMENDATIONS_ASSIGNED_BY', 'a.assigned_by', $listDirn, $listOrder); ?>
						</th>

						<th class='hidden-phone'>
						<?php echo HTMLHelper::_('grid.sort',  'COM_JLIKE_RECOMMENDATIONS_CREATED', 'a.created_date', $listDirn, $listOrder); ?>
						</th>
					</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>

				<tr class="row<?php echo $i % 2; ?>">
					<td data-title="<?php echo Text::_("COM_JLIKE_RECOMMENDATIONS_TITLE"); ?>">
						<a href="<?php echo $item->content_url; ?>" target="_blank"> <?php echo $item->content_title; ?></a>
					</td>

					<td class="jlike_msg_container" data-title="<?php echo Text::_("COM_JLIKE_MESSAGE_FROM_SENDER"); ?>">
						<div class="jlike_msg">
							<?php echo !empty($item->sender_msg) ? $item->sender_msg: '-'; ?>
						</div>
					</td>

					<td data-title="<?php echo Text::_("COM_JLIKE_RECOMMENDATIONS_ASSIGNED_BY"); ?>">
						<?php echo Factory::getUser($item->assigned_by)->name; ?>
					</td>
					<td  data-title="<?php echo Text::_("COM_JLIKE_RECOMMENDATIONS_CREATED"); ?>">
						<?php echo Factory::getDate($item->created_date)->Format(Text::_('COM_JLIKE_DATE_FORMAT'));?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
		</div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
