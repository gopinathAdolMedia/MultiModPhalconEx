
$('#new_member_user_form').on('submit', function(e){
	
	// ************************ To validate the Entries before Submission for New User ************************ //
	
	e.preventDefault();
	
	var user_name        = $('#user_name').val().trim();
	var user_email       = $('#user_email').val().trim();
	var user_passw1      = $('#user_passw1').val().trim();
	var user_passw2      = $('#user_passw2').val().trim();
	
	var verify_name      = isValidEntry(user_name);
	var verify_email     = isValidEntry(user_email);
	var verify_passw1    = isValidEntry(user_passw1);
	var verify_passw2    = isValidEntry(user_passw2);
	var verify_dispName  = isValidEntry(user_display_name);
	
	var verify_name_pattern  = user_name.indexOf(" ");
	var verify_email_pattern = isValidEmailAddress(user_email);
	
	if( !verify_name ){
		alert('Invalid Entry in USER NAME Field!');
	} else if( !verify_email || !verify_email_pattern){
		alert('Invalid Entry in USER EMAIL Field!');
	} else if( !verify_passw1 ){
		alert('Invalid Entry in USER PASSWORD Field!');
	} else if( verify_name_pattern >= 0 ){
		alert('The USER NAME Field should have only a single value!');
	} else if( user_passw1 !== user_passw2 ){
		alert('Password Mismatch!');
	} else {
	
			// Grab all the form data, including the CodeIgniter anti-CSRF token
		var post_data = $(this).serialize();

			// Submit the form by AJAX, and display the result.
		var url_email = $('#site_url_users_addNew').val() + "/insertUser";
		
		$.ajax({		
			url      : url_email,			
			type     : 'POST',			
			data     : post_data,
			dataType : 'json',
			timeout  : 30000,			
			success  : function( response_data, text, xhrobject ) {
				// var response = JSON.parse(response_data);
				var response = response_data;
				
				if(response['status'] == 'Failure'){
					alert(response['details']);
				} else if(response['status'] == 'Success'){
					alert(response['details']);
					window.location = $('#site_url_users_addNew').val();
				}
			}
		});
	}
});

function isValidEntry(entry) {

	// ************* Empty Value Validation ************* //
	
	if((entry == '')||(entry == null)){
		return false;
	} else {
		return true;
	}
}

function isValidEmailAddress(emailAddress) {

	// ************* E-Mail Validation ************* //
	
	var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
	return pattern.test(emailAddress);
}