<?php
/**
 * @version		1.0.0 jLike $
 * @package		jomgive
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		TechJoomla
 * @author mail	extensions@techjoomla.com
 * @website		http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params        = $this->params;
$allow_dislike = $params->get('allow_dislike');

$document = Factory::getDocument();
$day_str  = "'" . implode("','", $this->linechart['days_arr']) . "'";
$like_str = implode(",", $this->linechart['like_arr']);

if ($allow_dislike)
{
	$dislike_str = implode(",", $this->linechart['dislike_arr']);
}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
   var data = new google.visualization.DataTable();

		data.addColumn('string', 'Day');
		data.addColumn('number', 'Like Count');
		<?php if ($allow_dislike){ ?>
		data.addColumn('number', 'Dislike Count');
		<?php } ?>
	var options = {
	  title: 'JLike Dashboard'
	};

		var like_cnt=[<?php echo $like_str; ?>]

		<?php if ($allow_dislike){ ?>
		var dislike_cnt=[<?php echo $dislike_str; ?>]
		<?php } ?>

		var assign_date=[<?php echo $day_str; ?>]

		data.addRows(assign_date.length+1);
		for(var i=0;i<assign_date.length;i++)
		{
			data.setValue(i, 0, assign_date[i].toString());
			data.setValue(i, 1, like_cnt[i]);

			<?php if ($allow_dislike){ ?>
			data.setValue(i, 2, dislike_cnt[i]);
			<?php } ?>
		}


	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
   chart.draw(data, {width: "90%", height: 300,title: 'JLike Dashboard', fontSize:"12", vAxis:{title: 'Number of Likes and Dislikes',  titleTextStyle: {color: '#000000'}}});
  }
</script>

<div class="techjoomla-bootstrap container">
<form action="" method="post" name="adminForm" id="adminForm" class="custom-calendar-icon custom-calendar-bx">
	<div class="row">
		<div class="jlikeinnerdiv col-sm-12 col-md-4">
			<div class="jlikeinnerdiv"><?php echo Text::_('COM_JLIKE_FROM_DATE'); ?></div>
			<div class="jlikeinnerdiv"><?php echo HTMLHelper::_('calendar', $this->fromdate, "fromdate", "fromdate", Text::_('COM_JLIKE_ALL_LIKES_LBL_DATE_FORMAT')); ?></div>
		</div>
		<div class="jlikeinnerdiv col-sm-12 col-md-4">
			<div class="jlikeinnerdiv"> <?php echo Text::_('COM_JLIKE_TO_DATE'); ?></div>
			<div class="jlikeinnerdiv"> <?php	echo HTMLHelper::_('calendar', $this->todate, "todate", "todate", Text::_('COM_JLIKE_ALL_LIKES_LBL_DATE_FORMAT')); ?></div>
		</div>
		<div class="col-sm-12 col-md-4">
			<div>&nbsp;</div>
			<input type="button" class="btn  btn-small btn-primary" value="<?php echo Text::_('COM_JLIKE_ALL_LIKES_GO_BTN'); ?>" onclick="document.adminForm.submit();">
		</div>
	</div>

	<div class="clearfix">&nbsp;</div>
	<div class="well">
	<div id="chart_div"></div>
	</div>
	<div class="clearfix">&nbsp;</div>
	<div id="filter-bar" class="btn-toolbar row">
		<div class="filter-search btn-group hidden-phone col-sm-12 col-md-6">
			<label for="filter_search" class="element-invisible"><?php echo Text::_('COM_JLIKE_AL_FILTER_SEARCH_DESC'); ?></label>
			<input type="text" name="all_filter_search" class="input-medium" placeholder="<?php echo Text::_('COM_JLIKE_AL_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php if (!empty($this->search))
			{
				echo htmlspecialchars($this->search, ENT_COMPAT, 'UTF-8');
			}?>" title="<?php echo Text::_('COM_JLIKE_AL_FILTER_SEARCH_DESC'); ?>" />

			<div class="btn-group hidden-phone like-btn">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="fa fa-times"></i></button>
			</div>
		</div>

		<div class="col-sm-12 col-md-6">
			<div class="btn-group float-end hidden-phone">&nbsp;
				<label for="limit" class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>

			<div class="btn-group float-end">
					<?php
					echo $this->filter_likecontent_classification;
					?>
					<?php
					// echo HTMLHelper::_('select.genericlist', $this->filter_likecontent_list, "filter_likecontent_list", 'class="" size="1"
					//onchange="this.form.submit();" name="filter_likecontent_list"',"value", "text",$this->lists['filter_likecontent_list']);
					?>
			</div>
		</div>
	</div>
	<div style="clear:both"></div>
	<div id="no-more-tables">
		<table class="table table-striped table-bordered table-hover ">
			<thead>
			<tr>
				<!--<th width="10%"><?php //echo HTMLHelper::_( 'grid.sort', 'COM_JLIKE_USERNAME','users.username', $this->sortDirection, $this->sortColumn);?> </th>-->
				<th><?php echo HTMLHelper::_('grid.sort', 'COM_JLIKE_TITLE', 'likecontent.title', $this->sortDirection, $this->sortColumn); ?></th>
				<!--<th><?php //echo HTMLHelper::_( 'grid.sort', 'COM_JLIKE_ANNOTATIONS','likeannotations.annotation', $this->sortDirection, $this->sortColumn);?></th>-->
				<th><?php echo HTMLHelper::_('grid.sort', 'COM_JLIKE_CLASSIFICATION', 'likecontent.element', $this->sortDirection, $this->sortColumn); ?></th>
				<th width="5%"><?php echo HTMLHelper::_('grid.sort', 'COM_JLIKE_LIKE_COUNT', 'likecontent.like_cnt', $this->sortDirection, $this->sortColumn); ?> </th>

				<?php
				if ($params->get('allow_dislike'))
				{
					?>
				<th width="5%"><?php echo HTMLHelper::_('grid.sort', 'COM_JLIKE_DISLIKE_COUNT', 'likecontent.dislike_cnt', $this->sortDirection, $this->sortColumn); ?> </th>
				<?php
				}
				?>

			</tr>
			</thead>

			<tbody>
			<?php
			$i = 1;
			if ($this->data)
			{
				foreach ($this->data as $likedata)
				{
					if (isset($likedata->like_cnt) && $likedata->like_cnt <= 0)
					{
						continue;
					} ?>
						<tr>

							<td data-title="<?php echo Text::_("COM_JLIKE_TITLE"); ?>">
								<div>

										<strong><a href="<?php echo $likedata->url; ?>"><?php echo $likedata->title; ?></a></strong>

								</div>
								<div class="com_jlike_clear_both"></div>
							</td>
							<td data-title="<?php echo Text::_("COM_JLIKE_CLASSIFICATION"); ?>"><?php

							$brodfile            = JPATH_SITE . "/components/com_jlike/classification.ini";
					$classifications = parse_ini_file($brodfile);

					$element = $likedata->element;

					foreach ($classifications as $v => $clssfcs)
					{
						if ($v == $likedata->element)
						{
							$element = $clssfcs;

							break;
						}
					}
					echo $element; ?></td>

							<td data-title="<?php echo Text::_("COM_JLIKE_LIKE_COUNT"); ?>"><?php echo $likedata->like_cnt; ?></td>
							<?php
							if ($params->get('allow_dislike'))
							{
								?>
							<td data-title="<?php echo Text::_("COM_JLIKE_DISLIKE_COUNT"); ?>"><?php echo $likedata->dislike_cnt; ?></td>
							<?php
							} ?>
						</tr>
						<?php
						$i++;
				}
			}
			else
			{
				?>
				<tr><td colspan='6' style="text-align:center"><?php echo Text::_('COM_JLIKE_NO_DATA'); ?></td></tr>
			<?php
			}
			?>
		</tbody>
		</table>
		<div class="pagination com_jlike_align_center float-end">
			<?php
				echo $this->pagination->getPagesLinks();
			?>
		</div>
	</div>

		<input type="hidden" name="option" value="com_jlike" />
		<input type="hidden" name="view" value="likes" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="layout" value="all" />
		<input type="hidden" name="all_filter_order" id="filter_order" value="<?php if (!empty($this->sortColumn))
			{
				echo $this->sortColumn;
			} ?>" />
		<input type="hidden" name="all_filter_order_Dir" id="filter_order_Dir" value="<?php if (!empty($this->sortDirection))
			{
				echo $this->sortDirection;
			} ?>" />
</form>
</div>
