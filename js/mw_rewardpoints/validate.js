var $j_mw = jQuery.noConflict();

$j_mw(function(){
	$j_mw("#rewardpoints_config_reward_point_for_friend_registering, #rewardpoints_config_reward_point_for_registering" +
			",#rewardpoints_config_reward_point_for_invite_friend" +
			",#rewardpoints_config_reward_point_for_submit_review" +
			",#rewardpoints_capcha_image_width, #rewardpoints_capcha_image_height," +
			",#rewardpoints_capcha_code_length, #rewardpoints_capcha_text_transparency_percentage" +
			", #rewardpoints_capcha_num_lines, #rewardpoints_send_reward_points_time_life" +
			",#rewardpoints_send_reward_points_time, #rewardpoints_exchange_to_credit_time").addClass('validate-digits');
	$j_mw("#rewardpoints_capcha_perturbation").addClass('validate-number');
	
	$j_mw("#rewardpoints_config_point_money_rate, #rewardpoints_capcha_image_width" +
			",#rewardpoints_capcha_image_height, #rewardpoints_capcha_code_length" +
			",#rewardpoints_capcha_text_color, #rewardpoints_capcha_line_color," +
			"#rewardpoints_config_reward_point_for_registering, #rewardpoints_config_reward_point_for_invite_friend," +
			",#rewardpoints_config_reward_point_for_friend_registering,#rewardpoints_config_reward_point_for_friend_purchase," +
			"#rewardpoints_config_reward_point_for_friend_next_purchase, #rewardpoints_config_reward_point_for_submit_review," +
			"#rewardpoints_config_reward_point_for_order").addClass('required-entry');
	
	$j_mw("#rewardpoints_capcha_image_bg_color, #rewardpoints_capcha_text_color, #rewardpoints_capcha_line_color").mask("******");
	
	
	//init color picker
	$j_mw('#rewardpoints_capcha_image_bg_color, #rewardpoints_capcha_text_color, #rewardpoints_capcha_line_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
		$j_mw(el).val(hex);
		$j_mw(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$j_mw(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$j_mw(this).ColorPickerSetColor(this.value);
	});
	
	//check Invitation module
	
	url = window.location.toString();
	url = url.substring(0,url.indexOf('admin'));
	url1 = url+"rewardpoints/invitation/check";

	$j_mw.ajax({ 
		url: url1, 
		context: document.body, 
		success: function(data){
			objs = "#rewardpoints_config_reward_point_for_invite_friend, #rewardpoints_config_reward_point_for_friend_registering, #rewardpoints_config_reward_point_for_friend_purchase,#rewardpoints_config_reward_point_for_friend_next_purchase";
			if(data =="0")
			{
				$j_mw(objs).attr('disabled','disabled');
				$j_mw(objs).parent().append('<p class="invitation_require" style="color:#FF0000;font-weight:bold">Invitation Module require</p>');
			}else{
				$j_mw(objs).parent().children(".invitation_require").remove();
			}
      	}
	});
	url2 = url+"rewardpoints/credit/check";
	$j_mw.ajax({ 
		url: url2, 
		context: document.body, 
		success: function(data){
			objs = "#rewardpoints_exchange_to_credit_enabled, #rewardpoints_exchange_to_credit_point_credit_rate";
			if(data =="0")
			{
				$j_mw(objs).attr('disabled','disabled');
				$j_mw(objs).parent().children("p").append('Credit Module require');
				$j_mw(objs).parent().children("p").css("color","#FF0000").css("font-weight","bold");
			}else{
				$j_mw(objs).parent().children("p").remove();
			}
      	}
	});
	

});
