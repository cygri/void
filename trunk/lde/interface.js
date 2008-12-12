
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

function lookupTopic(){
	var topic = document.getElementById('topic').value;
	if(topic.length > 3) {
		document.getElementById('out').innerHTML = "querying sindice.com for target '" + topic + "' ...";
		sendRequest('service.php?lookup=' + topic, processLookupTopic);			
	}
	else document.getElementById('out').innerHTML = topic + "?"; 
}

function processLookupTopic(req){
	document.getElementById('out').innerHTML = req.responseText;
}


function useAsTopic(topic){
	document.getElementById('out').innerHTML = "querying repository for linked datasets on topic '" + topic + "' ...";
	sendRequest('service.php?find=' + topic, processUseAsTopic);			
}

function processUseAsTopic(req){
	document.getElementById('out').innerHTML = req.responseText;
}


function browseDatasets(){
	sendRequest('service.php?browse', processBrowse);
}

function processBrowse(req){
	document.getElementById('out').innerHTML = req.responseText;
}

function exploreDataset(dataset){
	document.getElementById('out').innerHTML = "exploring " + dataset+ "' ...";
	sendRequest('service.php?explore=' + urlencode(dataset), processExplore);
}

function processExplore(req){
	document.getElementById('out').innerHTML = req.responseText;
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

function setStatus(msg){
	document.getElementById('status').innerHTML = "<p>" + msg + "</p>";
}

