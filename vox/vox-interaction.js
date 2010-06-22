/********************************************************/
/* voX user interface and client-side interaction code  */
/********************************************************/

// config
var  voxServiceURI = "vox-service.php"; // the vox service 

// jQuery main interaction code
$(function(){

	//  render voiD either via button click or hit return on input
	$("#explore").click(function () {
		renderVoiD();
	});
	
	$('#voidURI').keypress(function(e){
		if(e.which == 13){
			renderVoiD();
		}
	});


	$('#showexamples').click(function(e){
		if($(this).text() == "show ...") $(this).text("hide ...")
		else $(this).text("show ...");
		$('#exvoidfiles').slideToggle("normal");
	});

	// example voiD files
	$('#ex0, #ex1, #ex2, #ex3, #ex4, #ex5').click(function(e){
		$('#voidURI').val($(this).attr('resource'));
		$('#showexamples').text("show ...")
		$('#exvoidfiles').slideUp("normal");
	});
		
	// show more details about a certain dataset topic
	$('.dstopic').live('click', function(){
		var topicURI = $(this).attr('resource');
		$('.dstopic .smallbtn').html("+"); 
		$('.dstopic .topicdetails').slideUp("slow"); // close other, potentially visible details
		$("div[resource='"+topicURI+"'] .smallbtn").html("-"); // show selected details
		$("div[resource='"+topicURI+"'] .topicdetails").slideDown("slow"); // show selected details
	});

	$('.sparqlep .smallbtn').live('click', function(){
		var endpointURI = $(this).parent().attr('resource');
		var queryStr = $(".sparqlep[resource='"+ endpointURI +"'] textarea").val();
		//alert("query for" + queryStr + " at " + endpointURI);
		executeQuery($(".sparqlep[resource='"+ endpointURI +"'] .sparqlresult"), endpointURI, queryStr);
	});	
	
	$('.lookupep .smallbtn').live('click', function(){
		var endpointURI = $(this).parent().attr('resource');
		var queryStr = $(".lookupep[resource='"+ endpointURI +"'] input").val();
		//alert("query for" + queryStr + " at " + endpointURI);
		executeLookup($(".lookupep[resource='"+ endpointURI +"'] .lookupresult"), endpointURI, queryStr);
	});
	

});
