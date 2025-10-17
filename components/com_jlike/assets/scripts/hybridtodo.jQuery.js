(function( $ ) {
$.fn.hybridtodo = function(options){
	var defaults = {action:'', comments:'', userProfile:''};

	// options = $.extend(defaults, options); original code

	// Merge options into defaults and also override default options if already exist in default
	$.extend(defaults, options);

	if (defaults.action == "getTodosAndComments")
	{
		getTodosAndComments(defaults.obj);
	}

	if (defaults.action == "renderHybridTodos")
	{
		var element = $(this);
		renderHybridTodos(defaults.arrData, element);
	}
	if (defaults.action == "createTodo")
	{
		createTodo(defaults.obj);
	}

	function getTodosAndComments(obj)
	{
		jQuery.ajax({
			url: site_root + 'index.php?option=com_api&app=jlike&resource=hybridtodos&format=raw',
			headers: {
				'x-auth':'session'
			},
			type: 'GET',
			data:obj,
			beforeSend: function ()
			{
			},
			success: function(result)
			{
				defaults.callback.call(this, result);
			},
			error: function(err) {
				defaults.callback.call(this, err);
			}
		});
	}

	function createTodo(obj){
		jQuery.ajax({
			url: site_root + 'index.php?option=com_api&app=jlike&resource=hybridtodos&format=raw',
			headers: {
				'x-auth':'session'
			},
			data:obj,
			type: 'POST',
			success: function(data) {
				if(data.success == true){
					defaults.callback.call(this, data);
				}
			},
			error: function(data) {
				if (data.success == false){
					defaults.callback.call(this, data);
				}
			}
		});
	}

	function renderHybridTodos(arrData, element)
	{
		comments    = arrData;
		loadComments(element);
	}

	function loadComments(element) {
		var isReadOnly = '';
		isReadOnly     = element.attr("data-jlike-readonly") ? true : false;

		jQuery(element).comments({
			postCommentOnEnter : false,
			enableReplying: true,
			enableEditing : true,
			enableUpvoting: false,
			enableDeleting: true,
			roundProfilePictures: true,
			enableNavigation: false,
			enableReplying: false,
			enableDeleting: false,
			enableEditing: false,
			readOnly: isReadOnly,
			enableNavigationOnTop: true,
			disabledNoCommentIcon: true,

			/*Field mapping*/
			fieldMappings: {
				id: 'annotation_id',
				parent: 'parent_id',
				content: 'annotation',
				content_html: 'annotation_html',
				created: 'annotation_date',
				fullname: 'user_name',
				createdByCurrentUser: 'is_mine',
			},
			timeFormatter: function(time) {
				return moment(time).format('HH:mm, DD/MM/YYYY');
			},

			// Pass options
			youText: defaults.userProfile.name ? defaults.userProfile.name : 'You',
			textareaPlaceholderText: 'Reply...',
			profilePictureURL: defaults.userProfile.avatar ? defaults.userProfile.avatar : '',

			getComments: function(success, error) {
				success(comments);
			},
			postComment: function(commentJSON, success, error, div) {
				commentJSON.content_id = div.attr('data-jlike-contentid');
				commentJSON.subtype = div.attr('data-jlike-subtype');
				commentJSON.client = div.attr('data-jlike-client');
				commentJSON.context = div.attr('data-jlike-context');

				if (parseInt(commentJSON.annotation_id) != commentJSON.annotation_id)
				{
					commentJSON.annotation_id = null;
				}

				jQuery.ajax({
					type: 'POST',
					url: site_root + "index.php?option=com_api&app=jlike&resource=annotations&format=raw",
					headers: {
						'x-auth':'session'
					},
					data: commentJSON,
					success: function(result) {
						var t = result.data.results.annotation_date.split(/[- :]/);

						result.data.results.user_name = result.data.results.user.name;
						result.data.results.user_id = result.data.results.user.id;
						result.data.results.profile_picture_url = result.data.results.user.avatar;
						result.data.results.profile_url = result.data.results.user.profile_link;
						result.data.results.created =  new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
						result.data.results.created_by_current_user = true;
						success(result.data.results);
						// loadComments(div);
					},
					error: error
				});
			},
			deleteComment: function(commentJSON, success, error, div) {
				commentJSON.content_id = div.attr('data-jlike-contentid');
				commentJSON.subtype = div.attr('data-jlike-subtype');
				commentJSON.client = div.attr('data-jlike-client');
				commentJSON.context = div.attr('data-jlike-context');
				jQuery.ajax({
					type: 'delete',
					url: site_root + "index.php?option=com_api&app=jlike&resource=annotations&format=raw&id="+commentJSON.annotation_id,
					headers: {
						'x-auth':'session'
					},
					data: commentJSON,
					success: function(comment) {
						success(comment);
						// loadComments(div);
					},
					error: error
				});
			},
			putComment: function(commentJSON, success, error, div) {
				commentJSON.content_id = div.attr('data-jlike-contentid');
				commentJSON.subtype = div.attr('data-jlike-subtype');
				commentJSON.client = div.attr('data-jlike-client');
				commentJSON.context = div.attr('data-jlike-context');
				jQuery.ajax({
					type: 'POST',
					url: site_root + "index.php?option=com_api&app=jlike&resource=annotations&format=raw&annotation_id="+commentJSON.annotation_id,
					headers: {
						'x-auth':'session'
					},
					data: commentJSON,
					success: function(comment) {
						// loadComments(div);
						var dataresultarray = comment.data.results;
						success(dataresultarray);
					},
					error: error
				});
			}
		});

		// init mention
		//var instance = "#"+element.attr("id")+" .jlike-mention";
		//var userslistObj = JSON.parse(element.attr("data-jlike-mentionsUserslist"));
		//init_mention(instance, userslistObj);
	}

	return true;
}
})( jQuery );
