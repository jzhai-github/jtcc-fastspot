/************** Date picker for form input box *************/
$( function() {
	$( "#datepicker" ).datepicker();
} );	
$( function() {
	$( "#datepicker-second" ).datepicker();
} );
/**********************************************************/


/************** Proctored Test Form **********************/
/*function createInstructors(){
    $('#instructor').val("");
    $('#insEmail').val("");

    if ($(this).data('options') === undefined){
        $(this).data('options', $('#instructor option').clone());
    }
    var subject = $('#class_name option:selected').attr('subject');
    var options = $(this).data('options').filter('[data-subject*=' + subject + ']');
    $('#instructor').html(options);
    $('#instructor').prepend('<option value="" selected>Select: </option>');
}

function updateSubject(){
    //createInstructors();
    subject = $('#class_name option:selected').attr('subject');
    createInstructors();
}

function updateInstructor(){
    $('#insEmail').val($('#instructor option:selected').attr('data-email'));
}

$(document).ready(function() {
    updateSubject();
    updateInstructor();
});*/
/**********************************************************/
