/******************************************************/
/* ve2 data-controller and service conmmunicate code  */
/******************************************************/

function createVoiD(){

	var data = validateInput(); 
	
	setStatus("Submitting data ...");
	$.ajax({
		type: "POST",
		url: ve2ServiceURI,
		data: "dsParams="+ $.toJSON(data),
		success: function(data){
			$("#vdOutput").val(data);
			setStatus("Ready");
		},
		error:  function(msg){
			alert(data);
			setStatus("Error creating voiD description.");
		} 
	});
}

function inspectVoiD(){
	var voiDInTTL = $("#vdOutput").val();
	$.ajax({
		type: "POST",
		url: ve2ServiceURI,
		data: "inspect="+ voiDInTTL,
		success: function(data){
			window.open(
				data,
				'sindiceInspectorWindow',
				'left=100,top=100,width=800,height=600,toolbar=0,resizable=1'
			);
			 
			return false;
			/*
			$('#inspector').dialog({
								width: 800,
								height: 600
							});
			$("#inspector").html("<div>Inspect your voiD file via Sindice's Web Inspector: <a href='" + data +"' target='_new'>start ...</a>.</div>");
			$("#inspector").dialog("open");
			*/
			setStatus("Ready");
		},
		error:  function(msg){
			alert(data);
		} 
	});
}

function announceVoiDURI(){
	var voiDURI = $("#vdAnnounceURI").val();
	
	if(voiDURI == "" || (voiDURI.substring(0,7) != "http://")) {
		alert("You have to provide an HTTP URI for the voiD file!");
		return false;
	}
	
	setStatus("Announcing voiD file");
	$("#busy").show("normal");
	$("#vdAnnounceResult").show("normal");
	$.ajax({
		type: "POST",
		url: ve2ServiceURI,
		data: "announce="+ escape(voiDURI),
		success: function(data){
			$("#busy").hide("fast");
			$("#vdAnnounceResult").append(data);
			setStatus("Ready");
		},
		error:  function(msg){
			alert(data);
		} 
	});	
}

function validateInput(){
	var data = {
		dsHomeURI : "http://example.org/",
		dsName : "Example Dataset"
	}

	var dsURI = $("#dsURI").val();	
	var dsHomeURI = $("#dsHomeURI").val();
	var dsName = $("#dsName").val();
	var dsDescription = $("#dsDescription").val();
	var dsExampleURIList = new Array();
	var dsTopicURIList = new Array();
	var tdsList = new Array();
	var dsPublisherURI = $("#dsPublisherURI").val();
	var dsSourceURI = $("#dsSourceURI").val();
	var dsLicenseURI = $("#dsLicenseURI").val();
	var dsVocURIList = new Array();
	var dsSPARQLEndpointURI = $("#dsSPARQLEndpointURI").val();
	var dsLookupURI = $("#dsLookupURI").val();
	var dsDumpURI = $("#dsDumpURI").val();
	
	
		
	// general metadata
	if(dsURI != "" && (dsURI.substring(0,7) != "http://")) {
		alert("If you provide a dataset URI, it must be a URI starting with 'http://'.");
		return false;
	}
	else data.dsURI = dsURI;

	if(dsHomeURI == "" || (dsHomeURI.substring(0,7) != "http://")) {
		alert("You have to provide a dataset homepage. This must be a URI starting with 'http://'.");
		return false;
	}
	else data.dsHomeURI = dsHomeURI;
	
	if(dsName == "") {
		alert("You have to provide a name for the dataset.");
		return false;
	}
	else data.dsName = escape(dsName);
	
	if(dsDescription == "") {
		alert("You have to provide a description for the dataset.");
		return false;
	}
	else data.dsDescription = escape(dsDescription);
	
	$(".dsExampleURI input").each(function (i) {
		dsExampleURIList.push($(this).val());
	});
	data.dsExampleURIList = dsExampleURIList;
	
	// topics
	$("#dsSelectedTopics div span").each(function (i) {
		dsTopicURIList.push($(this).attr("resource"));
	});
	data.dsTopicURIList = dsTopicURIList;
	
	// interlinking
	$("#tdsAddedTargets > div").each(function (i) {
		var target = {
			tdsHomeURI : $(this).find("a").attr("href"),
			tdsLinkType : $(this).find("div span.tlinktype").text(),
			tdsName : $(this).find("a").text(),
			tdsDescription : $(this).find("a").attr("title"),
			tdsExampleURI : $(this).find("div span.texample").text()
		}
		tdsList.push(target);
	});
	data.tdsList = tdsList;
	
	// provenance and licensing
	if (!$("#doMinimal").is(':checked')) { // don't take into account for minimal voiD file
		data.dsPublisherURI = dsPublisherURI;
		data.dsSourceURI = dsSourceURI;
		data.dsLicenseURI = dsLicenseURI;
	}
	// vocabularies
	$(".dsVocURI input").each(function (i) {
		dsVocURIList.push($(this).val());
	});
	data.dsVocURIList = dsVocURIList;
	
	// access methods
	if (!$("#doMinimal").is(':checked')) { // don't take into account for minimal voiD file
		data.dsSPARQLEndpointURI = dsSPARQLEndpointURI;
		data.dsLookupURI = dsLookupURI;
		data.dsDumpURI = dsDumpURI;
	}
	
	return data;
}

function validateURI(URI){
	setStatus("Validating " + URI);
	$.ajax({
		type: "GET",
		url: ve2ServiceURI,
		data: "validate="+ URI,
		success: function(data){
			alert(URI + " is " + data);
			setStatus(URI + " is " + data);
		}
	});
}

function lookupSubject(topic){
	setStatus("Looking up topic [" + topic + "] in DBpedia.");
	$.ajax({
		type: "GET",
		url: ve2ServiceURI,
		data: "lookupSubject="+ topic,
		dataType : "json",
		success: function(data){
			if(data && data.length > 0) {
				setStatus("Ready");
				$("#dsTopicOut").html("");
				for(i in data) {
					var URI = data[i].URI;
					var desc = data[i].desc;
					var label = data[i].label;
					if(i < maxNumOfTopicsProposed) {
						$("#dsTopicOut").append("<div class='topicopt'><span resource='"+ URI +"' content='"+ label +"' title='"+ desc +"'>" + label + "</span> [<a href='"+ URI + "' target='_new'>URI</a>] </div>");
					}
				}
				$("#dsTopicOut").show("normal");
			}		
		}
	});
}

function getDatasetList(store){
	setStatus("Retrieving list of datasets from voiD stores.");

	$.ajax({
		type: "GET",
		url: ve2ServiceURI,
		data: "listVoiD",
		dataType : "json",
		success: function(data){
			if(data && data.length > 0) {
				$("#tdsPreview").append("<div style='border: 1px solid #1d1d1d'>Store: <strong>"+ store +"</strong></div>");
				for(i in data) {
					var id = data[i].id;
					var title = data[i].title;
					var homepage = data[i].homepage;
					$("#tdsPreview").append("<span style='font-size: 90%; padding-left: 5px;' title='"+ id + "'>"+ title + "</span><br />");
				}
				setStatus("Ready");
			}		
		}
	});
}

function peekTargetDataset(store){
	setStatus("Looking up dataset description");
	var tdsHomeURI = $("#tdsHomeURI").val();

	$.ajax({
		type: "GET",
		url: ve2ServiceURI,
		data: "store=" + store + "&lookupVoiDViaHompage="+ tdsHomeURI,
		success: function(data){
			$("#tdsPreview").append("<div>Dataset with homepage " + tdsHomeURI + ": <a href='" + data + "' title='Preview dataset in RKB explorer' target='_new'>preview URI</a> (via " + store + ")</div>");
		}
	});
}

function lookupPrefix(){
	var tdsLinkType = $("#tdsLinkType").val();
	if(tdsLinkType.substring(0,7) != "http://") {
		$.ajax({
			type: "GET",
			url: ve2ServiceURI,
			data: "lookupPrefix="+ tdsLinkType,
			success: function(data){
				$("#tdsLinkType").val(data);
			}		
		});
	}
}

function lookupVoc(inputID){
	var vocPrefix = $("#"+inputID+" input").val();
	if(vocPrefix.substring(0,7) != "http://") {
		$.ajax({
			type: "GET",
			url: ve2ServiceURI,
			data: "lookupPrefix="+ vocPrefix,
			success: function(data){
				$("#"+inputID+" input").val(data);
				createVoiD();
			}		
		});
	}
}

function autocompletes(){
	var tdsLinkType = $("#tdsLinkType").val();
	if(tdsLinkType.substring(0,7) != "http://") { // we have presumably a prefix to resolve
		var separatorPosition = tdsLinkType.indexOf(":"); // abc:def -> will return 3
		var prefix = tdsLinkType.substring(0, separatorPosition);
		var localname = tdsLinkType.substring(separatorPosition + 1);
		
		$.ajax({
			type: "GET",
			url: ve2ServiceURI,
			data: "lookupPrefix="+ prefix,
			success: function(data){
				$("#tdsLinkType").val(data + localname);
			}		
		});		
	}
}
