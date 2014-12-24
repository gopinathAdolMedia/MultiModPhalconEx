
$('#new_member_role_form').on('submit', function(e){
	
	// ************************ To validate the Entries before Submission for New User Role ************************ //
	
	e.preventDefault();
	
	var role_name = $('#role_name').val().trim();
	
	var verify_role = isValidEntry(role_name);
	
	if( !verify_role ){
		alert('Invalid Entry in ROLE NAME Field!');
	} else {
	
			// Grab all the form data, including the CodeIgniter anti-CSRF token
		var post_data = $(this).serialize();

			// Submit the form by AJAX, and display the result.
		var url_email = $('#site_url_roles_addNew').val() + "/insertRole";
		
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
					window.location = $('#site_url_roles_addNew').val();
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