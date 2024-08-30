let $ = jQuery;
let d_left = null;
let d_right = null;
let m_left = null;
let m_right = null;
let mobile_position = false;
let bgX = null;
let bgY = null;
let centered_text = 'Centered (default)';
let mobile_use_desktop_text = 'Desktop focus point (default)';
let imageWt = 0;
let imageHt = 0;
let containerHt = 0;
let containerWt = 0;

function set_bg_values(){
	let d = $("#bg_pos_desktop_id").val().replaceAll('%','');
	let m = $("#bg_pos_mobile_id").val().replaceAll('%','');
	d_left = d.split(' ')[0];
	d_right = d.split(' ')[1];
	m_left = m.split(' ')[0];
	m_right = m.split(' ')[1];
}
function cancel_focus(){
	$(".media-frame-content,.media-sidebar").removeClass('show');
	$('.overlay').removeClass('show');
	mobile_position = false;
}
function close_overlay(){
	$(".media-frame-content,.media-sidebar").removeClass('show');
	$('.overlay').removeClass('show');
	let message = $("#mobile_value");
	if(mobile_position){
		m_left = bgX;
		m_right = bgY;
		$('#bg_pos_mobile_id').val(bgX+'% '+bgY+'%').trigger("change");
		if(bgX!=50 && bgY!=50){
			$('#mobile_value').html('<b>Mobile</b>: '+bgX+'% '+bgY+'%');
			$("#label_mobile").attr('value','Change');
			$("#reset_mobile").show();
		}
		else{
			if($("#bg_pos_desktop_id").val()=='50% 50%')
				$('#mobile_value').html('<b>Mobile</b>: '+centered_text);
			else
				$('#mobile_value').html('<b>Mobile</b>: '+mobile_use_desktop_text);
			$("#label_mobile").attr('value','Set');
		}
	}
	else{
		d_left = bgX;
		d_right = bgY;
		$('#bg_pos_desktop_id').val(bgX+'% '+bgY+'%').trigger("change");
		if(bgX!=50 && bgY!=50){
			$('#desktop_value').html('<b>Desktop</b>: '+bgX+'% '+bgY+'%');
			$("#label_desktop").attr('value','Change');
			if($("#bg_pos_mobile_id").val()=='50% 50%')
				message.html('<b>Mobile</b>: Desktop focus point (default)');
			$("#reset_desktop").show();
		}
		else{
			$("#desktop_value").html('<b>Desktop</b>: '+centered_text);
			if($("#bg_pos_desktop_id").val()!='50% 50%')
				message.html('<b>Mobile</b>: '+centered_text);
			$("#label_desktop").attr('value','Set');
		}
	}
	mobile_position = false;
}
function set_focus(mobile=false,disable){
	$(".media-frame-content,.media-sidebar").addClass('show');
	$(".media-toolbar,.media-menu-item").css('z-index',0);
	set_bg_values();
	$('.overlay').addClass('show'); 
	
	let container = $(".image_focus_point").find('.container');
	let image = $(".image_focus_point").find('img');
	containerHt = container.height();
	containerWt = container.width();
	imageWt = image.width();
	imageHt = image.height();
	
	if(mobile){
		mobile_position = true;
		let left = (imageWt*(m_left/100))+((containerWt-imageWt)/2);
		let top = (imageHt*(m_right/100))+((containerHt-imageHt)/2);
		$('.pin').css({'left':left+'px','top':top+'px'});		
		$("#mobile_msg").html('');
	}
	else{	
		let left = (imageWt*(d_left/100))+((containerWt-imageWt)/2);
		let top = (imageHt*(d_right/100))+((containerHt-imageHt)/2);
		$('.pin').css({'left':left+'px','top':top+'px'});	
	}
	if(disable)
		$("#reset_btn").attr('disabled','');
}
function reset_focus(mobile=false){
	bgX = 50;
	bgY = 50;
	$(".overlay").find('.pin').css({'left':'50%','top':'50%'});
	if(mobile){
		mobile_position = true;
		$("#reset_mobile").hide();
	}
	else{
		$("#reset_desktop").hide();
	}
	close_overlay();
}
$(document).on('click',".overlay .container img",function(e){	
	let Offset = $(this).offset(); 
	
	let relX = e.pageX - Offset.left;
	let relY = e.pageY - Offset.top;
	bgX = Math.round((relX/imageWt)*100);
	bgY = Math.round((relY/imageHt)*100);
	
	let left = (imageWt*(bgX/100))+((containerWt-imageWt)/2);
	let top = (imageHt*(bgY/100))+((containerHt-imageHt)/2);
	
	$('.pin').css({'left':left+'px','top':top+'px'});	
});
