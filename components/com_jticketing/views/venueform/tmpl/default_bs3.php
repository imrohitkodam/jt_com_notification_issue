<?php
/**
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_jticketing', JPATH_SITE);

// Check native integration
if ($this->integration != 2)
{
	?>
	<div class="alert alert-info alert-help-inline">
		<?php echo Text::_('COMJTICKETING_INTEGRATION_NATIVE_NOTICE');?>
	</div>
	<?php

	return false;
}

if ($this->allowedToCreate == 1)
{
	if ($this->checkVendorApproval || (JT::config()->get('silent_vendor') == 1))
	{
		// Call helper function
		JticketingCommonHelper::getLanguageConstant();

		$editId = (int) $this->item->id;
		?>

		<div id="jtwrap" class="tjBs3">
			<div id="venueform">
				<div class="page-header">
					<h1>
						<?php
						if (!empty($this->item->id))
						{
							echo Text::_('COM_JTICKETING_VENUE_EDIT');
						}
						else
						{
							echo Text::_('COM_JTICKETING_VENUE_ADD');
						}
						?>
					</h1>
				</div>

				<form id="form-venue"
					action="<?php echo Route::_('index.php?option=com_jticketing&task=venueform.save&id=' . (int) $this->item->id); ?>"
					method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

					<?php
					echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));

					echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('COM_JTICKETING_VENUE_TAB_DETAILS', true));
					?>
						<input type="hidden" name="jform[id]" id="venue_id" value="<?php echo $this->item->id; ?>" />
						<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
						<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
						<input type="hidden" id="venue_params" name="params" value=""/>

						<?php
						if (empty($this->item->created_by))
						{
							?>
							<input type="hidden" name="jform[created_by]" value="<?php echo Factory::getUser()->id; ?>" />
							<?php
						}
						else
						{
							?>
							<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
							<?php
						}

						if (empty($this->item->modified_by))
						{
							?>
							<input type="hidden" name="jform[modified_by]" value="<?php echo Factory::getUser()->id; ?>" />
							<?php
						}
						else
						{
							?>
							<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />
							<?php
						}
						?>

						<div class="row">
							<div class="col-xs-12 col-md-6 createVenue af-mt-25">
								<div class="row">
									<div class="col-xs-12 af-mb-10">
										<?php echo $this->form->getLabel('name'); ?>
										<?php echo $this->form->getInput('name'); ?>
									</div>
									<div class="col-xs-12 af-mb-10">
										<?php echo $this->form->getLabel('alias'); ?>
										<?php echo $this->form->getInput('alias'); ?>
									</div>
									<div class="col-xs-12 af-mb-10">
										<?php echo $this->form->getLabel('state'); ?>
										<?php echo $this->form->getInput('state'); ?>
									</div>
									<div class="col-xs-12 af-mb-10">
										<?php echo $this->form->getLabel('venue_category'); ?>
										<?php echo $this->form->getInput('venue_category'); ?>
									</div>
									<?php
									if ($this->EnableOnlineEvents == 1)
									{
										?>
										<div class="col-xs-12 af-mb-10">
											<?php echo $this->form->getLabel('online'); ?>
											<?php echo $this->form->getInput('online'); ?>
										</div>
										<div class="online_provider_wrap">
											<div class="col-xs-12 af-mb-10">
												<?php echo $this->form->getLabel('online_provider'); ?>
												<?php echo $this->form->getInput('online_provider'); ?>
											</div>
										</div>
										<?php
									}
									?>
									<div id="jform_offline_provider">
										<div class="col-xs-12 af-mb-10">
											<?php 
												echo $this->form->getLabel('address');
												echo $this->form->getInput('address');
												echo $this->form->getLabel('longitude');
												echo $this->form->getInput('longitude');
												echo $this->form->getLabel('latitude');
												echo $this->form->getInput('latitude');
											?>
										</div>
									</div>
									<div class="col-xs-12 af-mb-10">
										<div class="control-group">
											<?php echo $this->form->getLabel('privacy'); ?>
											<?php echo $this->form->getInput('privacy'); ?>
										</div>
									</div>
									<div class="col-xs-12 af-mb-10">
										<div class="control-group">
											<?php echo $this->form->getLabel('seats_capacity'); ?>
											<?php echo $this->form->getInput('seats_capacity'); ?>
										</div>
									</div>
									<div class="col-xs-12 af-mb-10 capacityCountRow">
										<div class="control-group">
											<?php echo $this->form->getLabel('capacity_count'); ?>
											<?php echo $this->form->getInput('capacity_count'); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-md-6 af-mt-25">
								<div class="row">
									<div class="control-group basicDetails__desc">
										<?php echo $this->form->getLabel('description'); ?>
										<?php echo $this->form->getInput('description'); ?>
									</div>
								</div>
							</div>

							<div class="col-xs-12 col-md-12 jTEventPluginForm">
								<div id="provider_html"> </div>
							</div>
						</div>

					<?php
					echo HTMLHelper::_('bootstrap.endTab');

					if ($this->showVenueGallery)
					{
						echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'gallery', Text::_('COM_JTICKETING_VENUE_GALLERY', true));
						?>
							<div class="gallery-tab-cover">
								<div class="event-gallery-option panel-default form-panel jticketing_container">
									<div class="com_jticketing_repeating_block panel panel-default">
										<div class="panel-heading">
											<?php echo Text::_('COM_JTICKETING_CREATE_EVENT_IMAGE_UPLOAD');?>
										</div>
										<div class="form-group af-p-10 af-mt-20">
											<div class="upload_file">
												<div class="col-sm-2 col-xs-12 control-label ">
													<?php echo $this->form->getLabel('gallery_file'); ?>
												</div>
												<div class="col-sm-10 col-xs-12 upload_file_gal">
													<?php
													echo $this->form->getInput('gallery_file');
													HTMLHelper::_('jquery.token');
													?>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="event-gallery-option panel-default form-panel jticketing_container">
									<div class="com_jticketing_repeating_block panel panel-default">
										<div class="panel-heading">
											<?php echo Text::_('COM_JTICKETING_CREATE_EVENT_VIDEO_UPLOAD');?>
										</div>
										<div class="form-group af-p-10 af-mt-20">
											<div class="row">
												<div class="col-xs-12 af-mb-10">
													<div class="col-sm-2 col-xs-12 control-label">
														<?php echo $this->form->getLabel('gallery_link'); ?>
													</div>
													<div class="col-sm-5 col-xs-12 upload_file_gal">
														<?php
														echo $this->form->getInput('gallery_link');
														?>
													</div>
													<div class="gallary__validateBtn col-sm-5 col-xs-12">
														<input type="button" class="validate_video_link"
															onclick="tjMediaFile.validateFile(this,1, <?php echo $this->isAdmin;?>, 'venueform')"
															value="<?php echo Text::_('COM_TJMEDIA_ADD_VIDEO_LINK');?>">
															<?php HTMLHelper::_('jquery.token'); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="span12">
								<div class="subform-wrapper">
									<ul class="gallary__media thumbnails list-inline">
										<li class="hide gallary__media--li af-valign-top af-mb-15">
											<i class="close"
												onclick="tjMediaFile.tjMediaGallery.deleteMedia(this, <?php echo $this->isAdmin;?>, <?php if (!empty($this->item->id)) {echo $this->item->id;}else{echo "0";}?>,'venueform');return false;">×</i>

											<?php HTMLHelper::_('jquery.token'); ?>

											<input type="hidden" name="jform[gallery_file][media][]"
												class="media_field_value" value="">

											<div class="thumbnail"></div>
										</li>
									</ul>
								</div>
							</div>

						<?php echo HTMLHelper::_('bootstrap.endTab');
					}

					// Loading joomla's params layout to show the fields and field group added for the event.
					echo LayoutHelper::render('joomla.edit.params', $this);

					echo HTMLHelper::_('bootstrap.endTabSet');
					?>

					<div>
						<input type="hidden" name="task" value="" />
						<?php echo HTMLHelper::_('form.token'); ?>
					</div>

					<div class="form-group">
						<div class="col-xs-12">
						<?php
						if ($this->canSave)
						{
							?>
							<button type="submit" class="validate btn  btn-default btn-success" onclick="Joomla.submitbutton('venueform.save'); return false;">
								<?php echo Text::_('JSUBMIT'); ?>
							</button>
							<?php
						}
						?>

						<a class="btn btn-default" href="<?php echo $this->veneuesLink;?>" title="<?php echo Text::_('JCANCEL'); ?>">
							<?php echo Text::_('JCANCEL'); ?>
						</a>
					</div>
					</div>

					<input type="hidden" name="option" value="com_jticketing"/>
					<input type="hidden" name="task" value="venueform.save"/>
					<?php echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_JTICKETING_VENDOR_NOT_APPROVED_MESSAGE');?>
		</div>
		<?php
	}
}
else
{
	?>
	<div class="alert alert-info alert-help-inline">
		<?php echo Text::_('COM_JTICKETING_VENDOR_ENFORCEMENT_ERROR');?>
		<?php echo Text::_('COM_JTICKETING_VENDOR_ENFORCEMENT_EVENT_REDIRECT_MESSAGE');?>
	</div>

	<div>
		<a href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=com_jticketing');?>" target="_blank" >
			<button class="btn btn-primary">
				<?php echo Text::_('COM_JTICKETING_VENDOR_ENFORCEMENT_EVENT_REDIRECT_LINK'); ?>
			</button>
		</a>
	</div>
	<?php
}

if (!empty($this->googleMapLink))
{
	HTMLHelper::_('script', $this->googleMapLink);
}

Factory::getDocument()->addScriptDeclaration('
	var googleMapApiKey = "' .$this->googleMapApiKey .'";
	var googleMapLink = "' .$this->googleMapLink .'";
	var mediaSize = "' .$this->mediaSize . '";
	var galleryImage = "' .$this->venueGalleryImage . '";
	var mediaGallery = ' .$this->mediaGalleryObj . ';
	var jticketing_baseurl = "' .Uri::root() .'";
	jtSite.venueForm.initVenueFormJs();
	var root_url = "' .Uri::root() .'";
	var editId     = "' .$editId .'";
	var getValue   = "' . $this->form->getValue('online_provider') . '";
	Joomla.submitbutton = function(task){jtSite.venueForm.venueFormSubmitButton(task);}
	jtSite.venue.initVenueJs();
');
