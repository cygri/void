/********************************************************/
/* ve2 user interface and client-side interaction code  */
/********************************************************/

// config
var  ve2ServiceURI = "ve2-service.php"; // the ve2 service 
var  topicLookUpMinLength = 2; // min. length for a keyword to trigger live look-up in DBPedia
var  maxNumOfTopicsProposed = 4; // max. numbers of topics shown in the result of the live look-up in DBPedia

// working vars - don't touch
var dsExampleURICounter = 1;
var dsVocURICounter = 0;
var voiDURIsList = new Array();


// UI helper methods

function initUI(){
	$("a[href='ref1']").attr("href", vgBase + vgREFGeneral_Dataset_Metadata);
	$("a[href='ref2']").attr("href", vgBase + vgREFCategorize_Datasets);
	$("a[href='ref3']").attr("href", vgBase + vgREFDescribing_Dataset_Interlink);
	$("a[href='ref4']").attr("href", vgBase + vgREFAnnouncing_the_license_of);
	$("a[href='ref5']").attr("href", vgBase + vgREFVocabularies_used);
	$("a[href='ref6']").attr("href", vgBase + vgREFSPARQL_endpoint_and_Examp);
}

function clearTopics(){
	$("#dsTopicOut").hide("fast");
	$("#dsTopicOut").html("");
	$("#dsTopic").val("");
	createVoiD();
}

function resetTargetPane(){
	$("#tdsPane").hide("normal");
	$("#tdsPreview").hide("normal");
	$("#tdsPreview").html("");
	$("#tdsHomeURI").val("http://dbpedia.org/");
	$("#tdsLinkType").val("http://www.w3.org/2002/07/owl#sameAs");
	$("#tdsName").val("DBPedia");
	$("#tdsDescription").val("Linked Data version of Wikipedia.");
	$("#tdsExampleURI").val("http://dbpedia.org/resource/Ludwig_van_Beethoven");
	$("#tdsMoreTargetDatasetStuff").hide("normal");
	$("#doShowMoreTargetDatasetStuff").show("normal");
	createVoiD();
}

function setStatus(status){
	$("#status").text(status);
	$("#status").fadeIn(1000);
}

// jQuery main interaction code
$(function(){
	
	initUI(); // reset all values to defaults
	
	// set up left-hand main navigational structure
	$("#dsItemSelection").accordion({ 
		header: "h3",
		autoHeight: false,
		change: function(event, ui) {
			createVoiD(); 
		}
	 });
	
	// general buttons
	$("#doStart").click(function () {
		$("#intro").fadeOut("slow");
	});
	
	$("#doAbout").click(function () {
		$("#about").slideToggle("normal");
	});
	
	$("#doCreate").click(function () {
		createVoiD();
	});
	
	$("#doInspectVoiD").click(function () {
		inspectVoiD();
	});
	
	$("#doAnnounce").click(function () {
		$("#vdAnnounce").slideDown("normal");
	});
	
	$("#doCloseAnnounce").click(function () {
		$("#vdAnnounce").slideUp("normal");
		$("#vdAnnounceResult").html("");	
		$("#vdAnnounceResult").hide("normal");
	});

	$("#doAnnounceURI").click(function () {
		announceVoiDURI();
	});
		
	// auto-update on focus change
	$("input").focus(function () {
		autocompletes();
		createVoiD();
	});

	$("#doMinimal").click(function () {
		createVoiD();
	});	
		
	//////////////////////
	// handle example URIs
	$(".dsExampleURI .btn").live("click", function() {
		var exURI = $(this).parent().attr('id');
		validateURI($("#"+exURI+" input").val());
	});
	
	$("#doAddDSExampleURI").click(function () {
		$("#dsExampleURIs").append("<div class='dsExampleURI' id='dsExampleURI"+ dsExampleURICounter +"'><input type='text' size='35' value='http://example.org/resource/ex' class='dsExampleURIVal' /> <span class='ibtn' title='Remove this example resource'>-</span> <span class='btn'>validate</span></div>");
		dsExampleURICounter++;
		createVoiD();
	});
	
	$(".dsExampleURI .ibtn").live("click", function() {
		var exURI = $(this).parent().attr('id');
		$("#"+exURI).remove();
		createVoiD();
	});
	
	/////////////////////////////////
	// handle dataset topic selection
		
	// add a topic
	$(".topicopt span").live("click", function() {
		var selectedURI = $(this).attr("resource");
		var label = $(this).attr("content");
		$("#dsSelectedTopics").append(
			"<div style='margin: 3px'><a href='"+ selectedURI +"'>" + label + "</a> " +
			" <span resource='"+ selectedURI + "' class='ibtn' title='Remove this topic'>-</span>" + 
			"</div>");
		$("#dsSelectedTopics").show("normal");
		clearTopics();
	});
	
	// remove a topic
	$("#dsSelectedTopics span.ibtn").live("click", function() {
		var selectedURI = $(this).attr("resource");
		$("#dsSelectedTopics div span[resource='"+selectedURI+"']").parent().remove();
		createVoiD();
	});
	
	// reset topics
	$("#doClearTopics").live("click", function() {
		clearTopics();
	});
	
	// look-up a topic
	$("#dsTopic").keyup(function () {
		var topic = $("#dsTopic").val();
		$("#dsTopicOut").html("");
		$("#dsTopicOut").hide("normal");
		if (topic.length >= topicLookUpMinLength) {
			lookupSubject(topic);
			$("div:contains('Provided Dataset Topics')").css("color", "white");
		}
	});	
	
	/////////////////////////////////////
	// handle dataset target interlinking
	
	// show interlink editing pane
	$("#doAddInterlinking").click(function () {
		$("#tdsPane").show("normal");
	});
	
	// add an interlinking
	$("#doAddTargetDS").click(function () {
		var tdsHomeURI = $("#tdsHomeURI").val();
		var tdsLinkType = $("#tdsLinkType").val();
		var tdsName = $("#tdsName").val();
		var tdsDescription = $("#tdsDescription").val();
		var tdsExampleURI = $("#tdsExampleURI").val();
		var textExampleURI = "";
		var textLinkType = "";
				
		$("div:contains('Provided Interlinking Target')").css("color", "white");
		
		if(tdsHomeURI == "" || (tdsHomeURI.substring(0,7) != "http://")) {
			alert("You have to provide the target dataset homepage. This must be a URI starting with 'http://'.");
			return false;
		}
		
		if(tdsLinkType == "" || (tdsLinkType.substring(0,7) != "http://")) {
			alert("You have to provide the link type. This must be a URI starting with 'http://'.");
			return false;
		}
		else {
			textLinkType = " <div style='color: #0B93D5; font-size: 90%'>link type: <span class='tlinktype'>" + tdsLinkType +"</span></div>";
		}
		
		if(tdsName == "") tdsName = tdsHomeURI;
		if(tdsDescription == "") tdsDescription = "No description available.";
		if(tdsExampleURI == "") {
			textExampleURI = "";
		}
		else {
			textExampleURI = " <div style='color:#6f6f6f; font-size: 90%'>(example resource: <span class='texample'>" + tdsExampleURI +"</span>)</div>";
		}
			
		$("#tdsAddedTargets").append(
			"<div style='margin: 3px; margin-bottom: 5px'><a href='"+ tdsHomeURI +"' title='"+ tdsDescription +"'>" + tdsName + "</a> " + 
			" <span resource='"+ tdsHomeURI + "' class='ibtn' title='Remove this interlink target'>-</span> " + 
			textLinkType +
			textExampleURI +
			"</div>");
		$("#tdsAddedTargets").show("normal");

		resetTargetPane();		
	});
	
	// remove an interlinking
	$("#tdsAddedTargets span.ibtn").live("click", function() {
		var selectedURI = $(this).attr("resource");
		$("#tdsAddedTargets div span[resource='"+selectedURI+"']").parent().remove();
		createVoiD();
	});
	
	// reset current interlinking
	$("#doForgetTargetDS").click(function () {
		resetTargetPane();
	});
	
	$("#doShowMoreTargetDatasetStuff").click(function () {
		$("#tdsMoreTargetDatasetStuff").show("normal");
		$("#doShowMoreTargetDatasetStuff").hide("normal");
	});	
	
	// lookup prefix
	$("#doLookupPrefix").click(function () {
		lookupPrefix();
	});
	
	// autocomplete prefix:localname
	$("#tdsLinkType").keyup(function(e) {
		if(e.keyCode == 13) {
			autocompletes();
			createVoiD();
		}
	});
	
	// look-up target
	$("#doPeekTargetDataset").click(function () {
		$("#tdsPreview").html("");
		peekTargetDataset("Talis");
		peekTargetDataset("RKB");
		$("#tdsPreview").show("normal");
	});
	
	// reset look-up
	$("#doClearTDSPreview").click(function () {
		$("#tdsPreview").hide("normal");
		$("#tdsPreview").html("");
		$("#tdsHomeURI").val("http://dbpedia.org/");
	});
	
	//////////////////////
	// handle vocabularies
	
	$(".dsVocURI .btn").live("click", function() {
		var vocPrefixInputID = $(this).parent().attr('id');
		lookupVoc(vocPrefixInputID);
	});
	
	$("#doAddDSVocURI").click(function () {
		$("#dsVocURIs").append("<div class='dsVocURI' id='dsVocURI"+ dsVocURICounter +"'><input type='text' size='35' value='http://purl.org/dc/terms/' class='dsVocURIVal' /> <span class='ibtn' title='Remove this vocabulary'>-</span> <span class='btn'>lookup</span></div>");
		dsVocURICounter++;
		createVoiD();
	});
	
	$(".dsVocURI .ibtn").live("click", function() {
		var vocURI = $(this).parent().attr('id');
		$("#"+vocURI).remove();
		createVoiD();
	});
	
	////////
	// notes
	$(".ui-icon-help").click(function () {
		var helpID = $(this).attr('id');
		$("#"+helpID+"content").slideToggle("normal");
	});

});