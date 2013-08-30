$(document).ready(function () {

	if($('#edit-purchaser-licence').val()){
		makeFieldsMandatory();
	}
	
	// get payment type radio buttons value
	var payment_type_init = $('#cel-5d-course-registration-licences-form input:radio[name=payment_type]:checked').val();

	if(payment_type_init !== 'po'){
		$('#edit-po-number-wrapper').hide();
	}
	
		// display PO number on licences form page
		$('#cel-5d-course-registration-licences-form input:radio[name=payment_type]').click(
				function(){
					payment_type_init = $(this).val();
					
					if(payment_type_init == 'po'){
						$('#edit-po-number-wrapper').show('slow');
					}else{
						$('#edit-po-number-wrapper').hide('slow');
					}
				}
			);
		
		$('#cel-5d-course-registration-licences-form #edit-ok').click(
			function(){
				var validationStatus = true;
				
				if($('#edit-purchaser-licence').attr('checked') !== true){
					if(validationStatus == true){
						validationStatus = validateFields();
					}
				}				
				
				if( payment_type_init == 'po' && $('#edit-po-number').val().length < 3){
					$('#edit-po-number').css('border', '2px solid red');
					if(validationStatus == true){
						validationStatus = false;
					}
				}
				
				if(validationStatus == false){
					alert('Mandatory fields should be filled');
				}
				return validationStatus
			}
		);
		//alert(payment_type);
		
		
		// display and hide user info when there is checkbox checked
		var purchaser_lic = $('#cel-5d-course-registration-licences-form #edit-purchaser-licence');
		if(purchaser_lic.attr('checked')==true){
			$('#cel-5d-course-registration-licences-form #subscriber_info1').hide();
		}
		
		
		$('#cel-5d-course-registration-licences-form #edit-purchaser-licence').click(
			function(){
				
				$('#cel-5d-course-registration-licences-form #subscriber_info1').toggle();
//				if($(this).attr('checked') == TRUE){
//				}
			}
		);
		
//		if ($('#cel-5d-course-registration-licences-form #edit-purchaser-licence:checked').val() !== undefined) {
//			  // Insert code here.
//			}
		
		
	}
);



function makeFieldsMandatory(){
	var redAsteriks = '<span class="form-required" title="This field is required.">*</span>';
	
	$('#edit-mail-subscriber1-wrapper label, #edit-profile-organization-subscriber1-wrapper label, #edit-profile-last-name-subscriber1-wrapper label, #edit-profile-first-name-subscriber1-wrapper label, #edit-po-number-wrapper label, #edit-profile-current-position-subscriber1-wrapper label').append(redAsteriks);
	$('#edit-mail-subscriber1-wrapper input, #edit-profile-organization-subscriber1-wrapper input, #edit-profile-last-name-subscriber1-wrapper input, #edit-profile-first-name-subscriber1-wrapper input, #edit-po-number-wrapper input, #edit-profile-current-position-subscriber1-wrapper input').addClass('required');
}


function validateFields(){
	var subscriberInputs = $('#subscriber_info1 .required');
	
	var validation = true;
	
	$.each(subscriberInputs, function(k,v){
		var value = $(v).val();
		if(value.length < 1){
			$(v).addClass('error');
			if(validation==true){
				validation = false;
			}
		}
	});
	
	return validation;
}
