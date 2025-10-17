<?php
/**
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var $this JticketingViewOrder */

$Itemid = $this->utilities->getItemId('events');
$eventsUrl = Route::_(Uri::root() . 'index.php?option=com_jticketing&view=events&layout=default&Itemid=' . $Itemid, false);

// Call helper function
JticketingCommonHelper::getLanguageConstant();
?>

<form  class="form-horizontal">
	<h3><?php echo Text::_('JT_TICKET_BOOKING_FORM_TITLE');?></h3><hr>
	<div class="form-group">
		<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 form-control-label" for="online_guest">
			<?php echo HTMLHelper::tooltip(Text::_('JT_TICKET_TYPE_BOOKING_ID_TOOLTIP'), Text::_('JT_TICKET_TYPE_BOOKING_ID_LABEL'), '', Text::_('JT_TICKET_TYPE_BOOKING_ID_LABEL')); ?>
		</label>
		<div class="col-xs-12">
			<input type="text" class="jticketing-input" id="online_guest" class="  " name="online_guest"
			 placeholder="<?php echo Text::_('JT_TICKET_TYPE_BOOKING_ID_PLACEHOLDER');?>" value=""><br>
			 <span class="alert alert-info">
				<strong><?php echo Text::_('JT_TICKET_TYPE_BOOKING_NOTE');?></strong> <?php echo Text::_('JT_TICKET_TYPE_BOOKING_NOTE_DESC');?>
			</span>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="button" class="btn btn-success" onclick="verifyBookingID()"><?php echo Text::_('JT_TICKET_BOOKING_ID_SUBMIT');?></button>
				<a href="<?php echo $eventsUrl; ?>" class="btn btn-danger" role="button"><?php  echo Text::_('COM_JTICKETING_CANCEL');?></a>
		</div>
	</div>
</form>
