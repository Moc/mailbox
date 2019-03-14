/**
 * @file
 * Forum JavaScript behaviors integration.
 */

var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	/**
	 * Behavior to bind click events on action buttons/links.
	 *
	 * @type {{attach: e107.behaviors.mailboxActions.attach}}
	 * @see "e107_web/js/core/all.jquery.js" file for more information.
	 */
	e107.behaviors.mailboxActions = {
		attach: function (context, settings)
		{
			$(function () {
				// When button 'mark' is clicked
			    $("button[data-mailbox-action='readunread'], button[data-mailbox-action='star'], span[data-mailbox-action='star']").click(function () {

			    	// Initiate array of selected ID's
			        var selectedIDs = [];
			        var selectedValues = [];

			        var $this = $(this);
					var script = $this.attr("src");
					var action = $this.attr('data-mailbox-action');
					var token = $(':checkbox:checked:first').attr('data-token');
					var checked = $('#mailbox-messages').find(':checked').length;

					//var script = $(':checkbox:checked:first').attr('data-src');

			        // Check which checkboxes were checked
					$(':checkbox[name="messages[]"]:checked').each (function () {
						// push the ID of each each selected checkbox to selectedID array
					    selectedIDs.push(this.id);
					    selectedValues.push(this.value);
					});


					// If star icon was checked, and no checkboxes checked
					if(!checked)
					{
						//console.log("Direct star");
						var directID = $this.attr('data-mailbox-starid');
						selectedIDs.push(directID);
					}

					if(selectedIDs[0] === undefined)  
					{
						console.log("No ID's selected!");
						console.log(selectedIDs);
						return;
					}

					//console.log(selectedIDs);
					//return;

					$.ajax({  
					    url: script,	
					    type: 'post',
					    dataType: 'JSON',       
					    data: 	{
					    			action: action,
					    			etoken: token,
			                    	ids: selectedIDs,
			                    	values: selectedValues
			                	},

			        	success: function(response){

			        		// Mark as read/unread
			        		if(action == 'readunread')
			        		{
				        		//console.log(response);
						        for(var key in response) 
						        {
								  //console.log(key + ":" + response[key]);
								  $('#message-'+key+'').attr('class', response[key]); // edit tr row class
								  $('#'+key+'').attr('value', response[key]); // edit input checkbox value 
								}
							}

							// Mark as starred / unstarred
							if(action == 'star')
			        		{
				        		//console.log("succes from star");
				        		for(var key in response) 
						        {
						        	if(response[key] == '1')
						        	{
						        		var newStar = '<i class="fas fa-star"></i>'; // MAILBOX TODO - check FA icon syntax
						        	}
						        	else
						        	{
						        		var newStar = '<i class="fa fa-star-o"></i>'; // MAILBOX TODO - check FA icon syntax
						        	}
					        		
					        		$('span[data-mailbox-starid='+key+']').html(newStar);
					       		}
							}

			            },
			            error: function(jqXHR, textStatus, errorThrown) {
			                alert('An error occurred... Look at the console for more information!');
			                console.log('jqXHR:');
			                console.log(jqXHR);
			                console.log('textStatus:');
			                console.log(textStatus);
			                console.log('errorThrown:');
			                console.log(errorThrown);
			            },
					});
			    });

			    
			    $("button[data-mailbox-action='refresh']").click(function () {
			    	location.reload();
			    	//console.log("refresh button clicked!");
			  	});
			});
		}
	};
})(jQuery);