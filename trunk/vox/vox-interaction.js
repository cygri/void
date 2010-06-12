/********************************************************/
/* voX user interface and client-side interaction code  */
/********************************************************/

// config
var  voxServiceURI = "vox-service.php"; // the vox service 

// jQuery main interaction code
$(function(){
	
	// query library actions
	$("#explore").click(function () {
		renderVoiD();
	});
	
	$('#voidURI').keypress(function(e){
		if(e.which == 13){
			renderVoiD();
		}
	});
	

	/*
	$('.singleQueryRemove span').live('click', function(){
		var queryURI = $(this).attr('resource');
		if (confirm("Are you sure you want remove this SPARQL query from the library?")) { 
			removeQuery(queryURI);
			$("#cQueryLibListSELECT").html("");
			initLib();
		}
	});
	*/	
	

});