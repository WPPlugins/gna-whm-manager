jQuery(function($) {
    var max_server_fields = 5;
    var add_button = $(".add_field_button");

	var skelton = 
				$('<div class="inside gna_grey_box">' +
				//'		<div class="">' +
				'			<table class="form-table">' +
				'				<tr valign="top">' +
				'					<th scope="row">' + gna.s_whm_serverip_name + ':</th>' +
				'					<td>' +
				'						<input type="text" class="g_whm_serverip regular-text" name="g_whm_serverip[]" value="" required />' +
				'					</td>' +
				'				</tr>' +
				'				<tr valign="top">' +
				'					<th scope="row">' + gna.s_whm_userid_name + ':</th>' +
				'					<td>' +
				'						<input type="text" class="g_whm_userid regular-text" name="g_whm_userid[]" value="" required />' +
				'					</td>' +
				'				</tr>' +
				'				<tr valign="top">' +
				'					<th scope="row">' + gna.s_whm_userpw_name + ':</th>' +
				'					<td>' +
				'						<input type="text" class="g_whm_userpw regular-text" name="g_whm_userpw[]" value="" required />' +
				'					</td>' +
				'				</tr>' +
				'			</table>' +
				//'		</div>' +
				'		<button class="delete_channel_button button">' + gna.s_delete_btn + '</button>' +
				'	</div>');

	var x = $('.g_whm_serverip').length;
    $(add_button).on('click', function(e) {
        e.preventDefault();
		console.log('x:'+x);
        if(x < max_server_fields){ //max input box allowed
			var original_target = skelton.clone();
			//original_target = original_target.find('input').val('').end();

            x++;
			$('form#form_whm').append(original_target);
        }
    });

    $('body').on("click", '.delete_channel_button', function(e) {
        e.preventDefault();
		$(this).closest('div.inside').remove();
		x--;
    });
});