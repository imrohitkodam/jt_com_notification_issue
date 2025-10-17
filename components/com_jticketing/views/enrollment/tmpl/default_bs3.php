<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Jticekting
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$input          = Factory::getApplication()->input;
$user	        = Factory::getUser();
$userId	        = $user->get('id');
$listOrder	    = $this->state->get('list.ordering');
$listDirn	    = $this->state->get('list.direction');
$selectedEvents = $this->state->get('filter.selected_events');
?>
<div class="<?php echo JTICKETING_WRAPPER_CLASS;?>">
	<div class="container-fluid">
		<div>
			<h3>
				<?php echo Text::_('COM_JTICKETING_ENROLLMENTS_NEW'); ?>
			</h3>
		</div>
		<form method="post" name="adminForm" id="adminForm">
			<div class="control-group event_id_row ">
				<div class="control-label">
					<label id="jform_title-lbl" for="jform_title" class="hasTooltip required" title="<?php echo Text::_('COM_JTICKETING_SELECT_EVENT_TO_ENROLLMENT_DESCRIPTION') ?>">
						<?php echo JText::_('COM_JTICKETING_SELECT_EVENT_TO_ENROLLMENT'); ?><span class="star">&nbsp;*</span>
					</label>
					<?php
					echo HTMLHelper::_('select.genericlist', $this->eventoptions, 'selected_events[]', 'class="btn input-medium" multiple="multiple" size="10" name="groupfilter"', "value", "text",$selectedEvents);
					?>
				</div>
			</div>
			<div class="form-actions">
				<div id="enroll-user" class='span10'>
					<div class='row-fluid'>
						<div class="span5 pull-right">
							<div class="row-fluid">
								<div class="span4">
									<label id="user1">
										<input id="notify_user_enroll" type="radio" name='notify_user_enroll' value="1" checked>
										<span><?php echo Text::_('COM_JTICKETING_NOTIFY_ENROLLED_USER'); ?></span>
									</label>
								</div>
								<button class="span4 btn btn-block btn-primary pull-right" type="button" name="enrol" id="enrol" onclick="jtCommon.enrollment.saveEnrollment('save')" value="" /><?php echo Text::_('COM_JTICKETING_ENROL_USER_BUTTON'); ?></button>
							</div>
						</div>
					</div>
				</div><!--enroll-user-->
			</div><!--form-actions-->

			<?php

			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

			if (empty($this->items))
				{?>

				<div class="alert alert-warning">
					<?php	echo Text::_('COM_JTICKETING_ENROLLMENT_NO_USERS_FOUND');	?>
				</div>
				<?php
			}
			else
			{?>
				<table class="table table-striped" id="usersList">
					<thead>
						<tr>
							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>

							<th class='left'>
								<?php echo HTMLHelper::_('grid.sort',  'COM_JTICKETING_ENROLMENT_USER_USERNAME', 'uc.username', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo HTMLHelper::_('grid.sort',  'COM_JTICKETING_ENROLMENT_USER_NAME', 'uc.name', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo HTMLHelper::_('grid.sort',  'COM_JTICKETING_ENROLMENT_GROUP_TITLE', 'title', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo HTMLHelper::_('grid.sort',  'COM_JTICKETING_ENROLMENT_USERID', 'uc.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<td colspan="6">
								<div class="pager">
									<?php echo $this->pagination->getPagesLinks(); ?>
								</div>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach ($this->items as $i => $item) :
							$ordering   = ($listOrder == 'a.ordering');
							?>
							<tr class="row<?php echo $i % 2; ?>">

								<td class="center hidden-phone">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>

								<td>
									<?php echo htmlspecialchars($item->username); ?>
								</td>
								<td>
									<?php echo htmlspecialchars($item->name); ?>
								</td>
								<td>
									<?php echo $item->groups; ?>
								</td>
								<td>
									<?php echo htmlspecialchars($item->id); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php
			} // else end here	?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>

<?php
Factory::getDocument()->addScriptDeclaration("
jQuery(document).ready(function (){
	jQuery('.icon-search').addClass('fa fa-search');
});
");
