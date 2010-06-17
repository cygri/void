/********************************************************/
/* voX user interface and client-side interaction code  */
/********************************************************/

// config
var  voxServiceURI = "vox-service.php"; // the vox service 

// jQuery main interaction code
$(function(){
	
	// setup UI
	$('#infodialog').dialog({
		autoOpen: false,
		width: 600,
//		heigth: 800,		
		buttons: {
			"Cancel": function() { 
						$(this).dialog("close"); 
					} 
			}
	});
					
	//  render voiD either via button click or hit return on input
	$("#explore").click(function () {
		renderVoiD();
	});
	
	$('#voidURI').keypress(function(e){
		if(e.which == 13){
			renderVoiD();
		}
	});
	
	// show more details about a certain dataset topic
	$('.dstopic').live('click', function(){
		var topicURI = $(this).attr('resource');
		$('.dstopic .smallbtn').html("+"); 
		$('.dstopic .topicdetails').slideUp("slow"); // close other, potentially visible details
		$("div[resource='"+topicURI+"'] .smallbtn").html("-"); // show selected details
		$("div[resource='"+topicURI+"'] .topicdetails").slideDown("slow"); // show selected details
	});
	
	

});

function showInfo(info){
	$('#infodialog').html(info);
	$('#infodialog').dialog('open');	
}