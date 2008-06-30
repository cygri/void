
/**
 * XML HTTP request to a given URL and calls a given callback with the data.
 *
 * @param url the URL to call
 * @param callback the callback to call with the returned data.
 * @param postData the data to use when posting to the given URL.
 */
function sendRequest(url, callback, callback_data, postData) {
	var req = createXMLHTTPObject();

	if(!req)
		return;
	var method = (postData) ? "POST" : "GET";
	
	req.open(method, url, true);
	req.setRequestHeader('User-Agent','XMLHTTP/1.0');
	
	if(postData)
		req.setRequestHeader('Content-type','application/x-www-form-urlencoded');

	req.onreadystatechange = function() {
			if(req.readyState != 4)
				return;
			if(callback_data)
				callback(req, callback_data);
			else
				callback(req);
		}

	if(req.readyState == 4)
		return;
	
	req.send(postData);
}

/**
 * Creates an XML HTTP Object based on the type of browser that the client is using.
 */
function createXMLHTTPObject() {
   var xmlHttp;

   // get the IE (ActiveX) version of the xml http object
   /*@cc_on
   @if(@_jscript_version >= 5)
   {
      try
      {
         xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
      }
      catch(e)
      {
         try
         {
            xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
         }
         catch(e)
         {
            // nothing to do
         }
      }
   }
   @end
   @*/

   // get the non-IE (non-ActiveX) version of the xml http object
   // if the object hasn't been acquired yet
   if(xmlHttp == null && typeof XMLHttpRequest !== 'undefined') {
      xmlHttp = new XMLHttpRequest();
   }

   return xmlHttp;
}

// AJAX CALLS

function lookupSubject(){
	var term = document.getElementById('subject').value;
	if(term.length > 3) {
		document.getElementById('subjectOut').innerHTML = "querying DBpedia.org for subject '" + term + "' ...";
		sendRequest('wrapper.php?lookup=' + term, processLookupSubject);			
	}
	else document.getElementById('subjectOut').innerHTML = term + "?"; 
}

function processLookupSubject(req){
	document.getElementById('subjectOut').innerHTML = req.responseText;
}

function findTarget(){
	var linkTarget = document.getElementById('linkTarget').value;
	if(linkTarget.length > 2) {
		document.getElementById('lsOut').innerHTML = "querying sindice.com for target '" + linkTarget + "' ...";
		sendRequest('wrapper.php?find=' + linkTarget, processFindTarget);			
	}
	else document.getElementById('lsOut').innerHTML = linkTarget + "?"; 
}

function processFindTarget(req){
	document.getElementById('lsOut').innerHTML = req.responseText;
}


// END oF AJAX CALLS

function resetField(fieldID) {
	document.getElementById(fieldID).value = "";
}
function resetOut(outID){
	document.getElementById(outID).innerHTML = ""; 	
}

function useAsSubject(subjectURI){
	document.getElementById('subject').value = subjectURI;
}

function useAsTarget(targetURI){
	document.getElementById('linkTarget').value = targetURI;
}

/* UTIL */
// http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_urlencode/
function urlencode(str) {
	var ret = str;
  ret = ret.toString();  
  ret = ret.replace(/#/g, '%23');
  return ret;
}

// http://mousewhisperer.co.uk/js_page.html
function getCacheBusterParam(){
	return  "&rcb=" + parseInt(Math.random()*99999999); 
}

function genVoid(){	
	var myDataset = document.getElementById('datasetURI').value;
	var subject = document.getElementById('subject').value;
	var linkTarget = document.getElementById('linkTarget').value;
	var buffer = "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n";
	buffer =  buffer + "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .\n";
	buffer =  buffer +  "@prefix owl: <http://www.w3.org/2002/07/owl#> .\n";
	buffer =  buffer +  "@prefix dc: <http://purl.org/dc/elements/1.1/> .\n";
	buffer =  buffer +  "@prefix void: <http://purl.org/vocab/voiD/ns#> .\n\n";
	buffer =  buffer +  ":myDataset rdf:type void:Dataset ;\n";
	buffer =  buffer +  "           owl:sameAs <" + myDataset +"> ;\n";
	buffer =  buffer +  "           dc:subject <" + subject +"> ;\n";
	buffer =  buffer +  "           void:containsLinks :myLinkset .\n";
	buffer =  buffer +  ":myLinkset rdf:type void:Linkset ;\n";
	buffer =  buffer +  "           void:target <" + linkTarget + ">\n";
	document.getElementById('voidOut').value = buffer;
}

