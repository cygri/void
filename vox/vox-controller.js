/******************************************************/
/* vox data-controller and service conmmunicate code  */
/******************************************************/

function renderVoiD(){
	var voidURI = $("#voidURI").val();
	
	isBusy();
	
	$.ajax({
		type: "GET",
		url: voxServiceURI,
		data: "uri=" + escape(voidURI),
		success: function(data){
			if(data) {
				$("#out").html(data);
				$("#out").show("slow");
			}
			notBusy();
		},
		error:  function(msg){
			alert(msg);
		} 
	});
}

function executeQuery(endpointURI, queryStr) {

	var data =  {
		endpointURI : escape(endpointURI),
		queryStr : queryStr
	}
	
	if( queryStr.toLowerCase().indexOf("construct") > 0 ) { // a CONSTRUCT query
		$.ajax({
			type: "POST",
			url: omniqServiceURI,
			data: "eParams="+ $.toJSON(data),
			success: function(data){
				if(data) {
					$("#cQueryResult").text(data).html();
				}
			},	
			error:  function(msg){
				alert(data);
			} 
		});
	}
	else { // SELECT or ASK query
		$.ajax({
			type: "POST",
			url: omniqServiceURI,
			data: "eParams="+ $.toJSON(data),
			dataType : "json",
			success: function(data){
				if(data) {
					renderSPARQLResult(data);
				}
			},	
			error:  function(msg){
				alert(data);
			} 
		});
	}
}

function renderSPARQLResult(data){
		var vars =  Array();
		var buffer = "";
		$("#cQueryResult").html("");
		
		// SELECT query result		
		if(data.head.vars) {
			buffer = "<table><tr>";
			for(rvar in data.head.vars) { // table head with vars
				buffer += "<th>" + data.head.vars[rvar] + "</th>";
			}
			buffer += "</tr>";
			for(entry in data.results.bindings) { // iterate over rows
				if(entry%2) buffer += "<tr>";
				else buffer += "<tr class='invrow'>";

				for(rvar in data.head.vars) { // iterate over columns per row
					var col = data.head.vars[rvar];
					buffer += "<td>" + data.results.bindings[entry][col].value + "</td>";
				}
				buffer += "</tr>";
			}
			buffer += "</table>";
		}
		// ASK query result
		if(data.boolean) {
			buffer = "<p style='font-size:140%'>The query yields <strong>" + data.boolean + "</strong>.</p>";
		}
		
		$("#cQueryResult").append(buffer);
}

function isBusy(){
	$("#status").show();
	$("#status").html("<img src='img/ajax-loader.gif' id='busy' width='32px' alt='busy />");	
}

function notBusy(){
	$("#status").hide();
	$("#status").html("");	
}