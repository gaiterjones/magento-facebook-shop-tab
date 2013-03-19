$(document).ready(function(){

	$("#submitForm").click(function(){
		$("#ajaxLoader").removeClass('hidden'); 
		$.ajax({
			type: "POST",
			url: "php/ajax.ErrorEmail.php",
			data: $("#errorReportForm").serialize(),
			dataType: "json",

			success: function(msg){
				$("#ajaxLoader").addClass('hidden');
				$("#submit").addClass('hidden'); 
			  	//$("#formResponse").removeClass('error');  
                //$("#formResponse").removeClass('success');  
                //$("#formResponse").addClass(msg.status); 
				$("#formResponse").html(msg.message);

			},
			error: function(msg){
				$("#ajaxLoader").addClass('hidden'); 
				//$("#formResponse").removeClass('success');  
                //$("#formResponse").addClass('error'); 
				$("#formResponse").html("There was an error completing this request. Please try again." + msg.message);
			}
		});

		//make sure the form doesn't post
		return false;

	});
	
});
