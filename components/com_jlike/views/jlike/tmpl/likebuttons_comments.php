<?php 

defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('jquery.token');
?>

<form action="<?php echo $this->urldata->url;?>" method="post" id="jlike_comments" name="jlike_comments" enctype="multipart/form-data">
<div class="row-fluid jlike_comments_container jlike_text_decoration">
		<div class="jlike-comment-header p-1">
			<div class="jlike_commentBox d-flex justify-content-between">
				<div id="divaddcomment" class="pointer " >
					<div class="jlike_loadbar"> <span id="loadingCommentsProgressBar"></span></div>
					<?php if($this->urldata->show_comments==1):?>
					<a class="jlike_comment_msg jlike_comment_padding_right" onclick="addCommentArea('<?php echo $likecontainerid;?>','0','0','97.5',0,0)"> <?php echo Text::_('COM_JLIKE_ADD_COMMENTS'); ?> </a>
					<?php endif; ?>
				</div>
				<div style="clear:both"></div>
			</div>
		</div>

	<?php if($this->urldata->show_comments == 1): ?>
	<div class="jlike_comments ">
		<?php

		$i=1;
		$user_comment_present=0;
		$annotaionIds=array();

		foreach($this->comments as $comment)
		{
			$annotaionIds[]=$comment->annotation_id;

			if($comment->annotation)
			{
			 ?>

			<div id="jlcomment<?php echo $comment->annotation_id; ?>" class="media jlike_commentingArea jlike_renderedComment jlike_no_radius jlike_text_decoration" >

				<?php if (empty($comment->avtar)) : ?>
					<?php	$comment->avtar = Uri::root(true).'/media/com_tjlms/images/default/user.png';	?>
				<?php endif;	?>

				<?php if (!empty($comment->user_profile_url)) : ?>
					<a class="pull-left" target="_blank" href="<?php echo $comment->user_profile_url?>" >
				<?php else:	?>
					<div class="pull-left">
				<?php endif;	?>
						<img class="jlike_tp_margin img-circle jlike-img-border" src="<?php echo $comment->avtar; ?>" alt="Smiley face" width="36px" height="auto">
				<?php if (!empty($comment->user_profile_url)) : ?>
					</a>
				<?php else:	?>
					</div>
				<?php endif; ?>


				<div class="media-body jlike_media_body" >

					<span>
						<?php if (!empty($comment->user_profile_url)) : ?>
							<a href="<?php echo $comment->user_profile_url; ?>">
						<?php else:	?>
							<span>
						<?php endif;	?>
								<?php echo ucwords($comment->name); ?>
						<?php if (!empty($comment->user_profile_url)) : ?>
							</a>
						<?php else:	?>
							</span>
						<?php endif; ?>

						<?php if($loged_user==$comment->user_id){ ?>
						<div class="jlike_position_relative pull-right">
							<a data-toggle="dropdown" class="pull-left" href="#">
								<i class="icon-pencil" ></i>
							</a>
							<ul class="dropdown-menu jlike_edit_dropdown jlike_list_style_type" >
								<li id="showEditDeleteButton<?php echo $comment->annotation_id; ?>" tabindex="-1">
									<a target="#showEditDeleteButton<?php echo $comment->annotation_id; ?>" onclick="EditComment(this)"><?php echo Text::_('COM_JLIKE_EDIT'); ?></a>
								</li>
								<li id="DeleteButton<?php echo $comment->annotation_id; ?>" tabindex="-1">
									<a target="#DeleteButton<?php echo $comment->annotation_id; ?>" onclick="DeleteComment(this)"><?php echo Text::_('COM_JLIKE_DELETE'); ?></a>
								</li>
							</ul>
						</div>
						<?php } ?>
					</span>

					<div class="jlike_comment_padding_top">

						<div id="showlimited<?php echo $comment->annotation_id; ?>" >
							<?php
							//echo $comment->smileyannotation;
								if(strlen(strip_tags($comment->smileyannotation))>=165)
									echo nl2br(trim($this->jlikehelperObj->getsubstrwithHTML($comment->smileyannotation, 165, '...', true)));
								else
									echo nl2br(trim($comment->smileyannotation));
							?>

							<a class="jlike_pointer"  onclick="showFullComment(<?php echo $comment->annotation_id; ?>)"><?php
								if(strlen(strip_tags($comment->smileyannotation))>=165)
								{
									 echo Text::_('COM_JLIKE_SEE_MORE');
								} ?>
							</a>
						</div>

						<div id="showlFullComment<?php echo $comment->annotation_id; ?>" class="jlike_display_none " >
							<?php echo nl2br(trim($comment->smileyannotation)); ?>&nbsp;
							<a class="jlike_pointer" onclick="showLimitedComment(<?php echo $comment->annotation_id; ?>)">
								<?php echo Text::_('COM_JLIKE_SEE_LESS'); ?>
							</a>
						</div>

						<!--comment added user & logged in user are the same then show edit comment-->

						<?php if($loged_user==$comment->user_id){ ?>
							<div id="EditComment<?php echo $comment->annotation_id;?>" class="jlike_display_none" >

								<div class="jlike_textarea taggable" id="CommentText<?php echo $comment->annotation_id;?>" contenteditable="true" <?php echo $maxlength; ?> required="required"
								onkeyup="characterLimit(id, <?php echo $maxlength; ?>)"><?php echo nl2br(trim($comment->annotation));$user_comment_present=1;?>
								</div>

								<div class="jlike_smiley_container">
									<div id="<?php echo $comment->annotation_id; ?>" class="jlike_smiley jlike_display_inline_blk jlike_btn_container" >
										<button id="jlike_smiley" class="jlike_smiley " type="button" onClick="javascript:jLikeshowSmiley(this,<?php echo $comment->annotation_id; ?>);">
										</button>
									</div>
								</div>

							</div>
						<?php }
						else{ ?>

							<div class="jlike_textarea jlike_display_none taggable"  <?php echo $maxlength; ?> id="CommentText<?php echo $comment->annotation_id;?>" contenteditable="true" <?php echo $maxlength; ?>  required="required"><?php echo nl2br(trim($comment->smileyannotation));$user_comment_present=1;?></div>
							<div id="displaytagsfor_CommentText<?php echo $comment->annotation_id;?>" class="displayme_CommentText"></div>
						<?php } ?>

						<div class="row-fluid reply_like_dislike_div" >
							<span class="small">

							<!--@ threaded REPLY button-->
							<?php if($params->get('allow_threaded_comment'))
							{ ?>
								<a id="parentid<?php echo $comment->annotation_id; ?>" class="jlike_pointer" onclick="jlike_reply(this,'<?php echo $margin_left;?>','<?php echo $width;?>',37,1)" ><?php echo Text::_('COM_JLIKE_REPLY_BTN'); ?>
								</a>
								<?php
							} ?>

							<!--@ threaded REPLIES COUNT -->
							<?php if($params->get('allow_threaded_comment')): ?>
								<span id="parentid_show_reply<?php echo $comment->annotation_id; ?>" class="jlike_pointer" onclick='show_reply(this,<?php echo (json_encode($comment->children));?>,8,92,1,0)' >
									<div class="jlike_count_box"><?php echo $comment->replycount; ?></div>
								</span>

								<span id="nbspId<?php echo $comment->annotation_id; ?>" > &nbsp;&nbsp; </span>
							<?php endif; ?>
							<!-- LIKE button
							$comment->userLikeDislike =0  => user not like or dislike on this comment
							$comment->userLikeDislike =1  => user not like this comment
							$comment->userLikeDislike =2  => user not dislike this comment
							-->

								<?php if($params->get('like_dislike_comments'))
								{

									if(!$comment->likeCount)
									{
										$comment->likeCount=0;
									 } ?>

									 <span class="">

										<!--LIKE BUTTON -->

										<a id="like_annotationid<?php echo $comment->annotation_id; ?>" class="jlike_like_btn jlike_pointer " onclick="increaseLikeCount(this,'<?php echo $likecontainerid; ?>')" >
											<span id="like_unlike<?php echo $comment->annotation_id; ?>">
											<?php
												if($comment->userLikeDislike==1)
												{
													echo Text::_('COM_JLIKE_UNLIKE_BTN');
												}
												else
												{
													echo Text::_('COM_JLIKE_LIKE_BTN');
												}
											?>
											</span>
										</a>
										<span id="jlike_like_count_area<?php echo $comment->annotation_id; ?>" >

											<!--@ threaded LIKE COUNT -->
											<span id="commentid<?php echo $comment->annotation_id; ?>" class="user_liked jlike_pointer">
												<div id="like_count<?php echo $comment->annotation_id; ?>" class="jlike_count_box">
													<?php echo $comment->likeCount; ?>
												</div>
											</span>
										</span>
									</span>

									<!-- DISLIKE BUTTON -->

									<?php if($params->get('show_comment_dislike_button'))
									{

										if(!$comment->dislikeCount){
											$comment->dislikeCount=0;
										 } ?>
										 &nbsp;&nbsp;
										<span class="">
											<a id="dislike_annotationid<?php echo $comment->annotation_id; ?>" class=" jlike_dislike_btn jlike_pointer " onclick="increaseDislikeCount(this,'<?php echo $likecontainerid; ?>')" >
												<span id="dislike_undislike<?php echo $comment->annotation_id; ?>">
												<?php
													if($comment->userLikeDislike==2){
														echo Text::_('COM_JLIKE_UNDISLIKE_BTN');
													}
													else
													{
														echo Text::_('COM_JLIKE_DISLIKE_BTN');
													}
												?>
											</span>
											</a>

											<!--@ threaded DISLIKE COUNT -->
											<span id="jlike_dislike_count_area<?php echo $comment->annotation_id; ?>">
												<span id="commentDislike<?php echo $comment->annotation_id; ?>" class="user_disliked jlike_pointer">
													<div id="dislike_count<?php echo $comment->annotation_id; ?>" class="jlike_count_box"><?php echo $comment->dislikeCount; ?></div>
												</span>
											</span>
										</span><?php
									}
								} ?>
								<?php if($loged_user==$comment->user_id)
								{ ?>
									<span >
										<span id="jlike_cancel_comment_btn<?php echo $comment->annotation_id;?>" class=" jlike_cancel_comment_btn jlike_display_none" >
											<button type="button" class='btn jlike_cancelbtn' onclick="Cancel(<?php echo $comment->annotation_id; ?>)"> <?php echo Text::_('COM_JLIKE_CACEL'); ?></button>
											<button type="button" class='btn jlike_commentbtn btn-secondary' onclick="SaveEditedComment(<?php echo $comment->annotation_id; ?>,<?php echo $comment->annotation_id; ?>,'<?php echo $likecontainerid;?>')"> <?php echo Text::_('COM_JLIKE_COMMENT'); ?></button>
										</span>
									</span>
								<?php
								} ?>

								<!--end threaded reply button-->
								<!--Show comment time -->
								<span id="<?php echo 'jlike_comment_time'.$comment->annotation_id; ?>" class="jlike_comment_time pull-right" >
									<!--<label class="timeago" title="<?php //echo $comment->date; ?>" style=" font-weight:bold"/> -->
								<?php
									echo $comment->date;
									echo $comment->time;?>
								</span>
							</span>
							<!-- this one-->
						</div>


					</div>
				</div>

			</div> <!-- end of row-fluid--class -->

			<hr />
				<?php
			$i++;
			}
		}  //print_r($annotaionIds); //end of foreach for comments ?>

	</div> <!-- End of Main comment div-->
	<div style="clear:both"></div>
	<div class="row-fluid">
		<span id="progessBar"></span>

		<!-- @S View More button
		-->

		<?php

		// Show only when comments on content available more that loaded at page load
		if($comment_limit < $this->comments_count[0])
		{ ?>
			<div class="jlike_viewCommentsMsg">
			<div id="viewCommentsMsg<?php echo $likecontainerid; ?>" class=" span12 center btn pointer" onclick="showAllComments(0,0, '<?php echo $likecontainerid;?>')">
					<span  class="comments_count"><?php echo  Text::_('COM_JLIKE_VIEW_MORE').'  '. Text::_('COM_JLIKE_VIEW_MORE1') ?></span>
					<span id="caret" class="caret jlike_caret_margin_top"></span>
				</div>
			</div>
			<?php
		} ?>
		<div class="clearfix"></div>
		<!-- @E View More button -->

		<!-- show user name in popup who like the comment !-->
		<div class="modal jlike_modal fade" id="like_dislike_users ">
			<div class="modal-dialog modal-lg">
      			<div class="modal-content">
					<div class="modal-header">
						<a class="close" data-dismiss="modal">&times;</a>
						<h3 class="modal_header"></h3>
					</div>

					<div id="modalconent" class="modal-body"></div>

					<div class="modal-footer">
						<a href="#" class="btn" data-dismiss="modal"><?php echo Text::_('COM_JLIKE_CLOSE'); ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>

		<input type="hidden" id="sorting" name="sorting" value="1" />
	<?php endif; ?>
</div>
</form>

<?php

if($this->urldata->show_comments==1)
{
	//Require the comment scripting methods file
	$comjlikeHelper = new comjlikeHelper();
	$commentJs = $comjlikeHelper->getjLikeViewpath('jlike','comment');
	require_once($commentJs);
}
?>
