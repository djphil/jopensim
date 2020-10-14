//********************************
//* TerminalScript for jOpenSim  *
//*                              *
//* created 2018-03-17 by FoTo50 *
//* https://www.jopensim.com     *
//********************************

// if you find a bug or even a security issue,
//I would be happy for a report at the support forum at https://www.jopensim.com

string targetUrl			= "http://path-to-your-joomla/index.php?option=com_opensim&view=interface"; // this is the target script, handling the requests
string inworldUrl			= "http://path-to-your-joomla/inworld-account.html"; // Add here the url for the inworld account to provide an easy link to go
integer listenchannel		= 555; // Channel for the Tracker to listen to

// some values for easier translation:
string llD_explain	= "\nI am a jOpenSim terminal!\nWant to get a notecard to see what I can do for you?";
string llD_yes		= "Yes";
string llD_no		= "No";
string llD_cancel	= "Cancel";
string llD_site		= "Website";
string llD_Click	= "Click to visit your account page on the Website!";


// nothing interesting to change below this point!!!
key		urlRequestId;
key		requestId;
key		registerId;
key		response_key;
string	resident;
string	myurl;
string	querystring;
key		owner;
integer	dialogchannel;
string	terminalDescription;

default {
    state_entry() {
    	if(targetUrl == "http://path-to-your-joomla/index.php?option=com_opensim&view=interface") {
    		llOwnerSay("Please enter the correct path for 'targetUrl' first");
    	} else {
            terminalDescription = llGetObjectDesc();
	        dialogchannel = (integer)(llFrand(99999.0) * -1);
	        owner = llGetOwner();
	        urlRequestId = llRequestURL();
	        llOwnerSay("Terminal running");
	        llListen(listenchannel, "", NULL_KEY, "");
	        llListen(dialogchannel,"", NULL_KEY,"");
	    }
    }

    listen( integer channel, string name, key id, string message ) {
        if (channel != listenchannel && channel != dialogchannel) {
            return;
        }

        if(channel == dialogchannel) {
            if(id == owner) {
                if(message == llD_yes) {
                    string registerUrl = targetUrl+"&action=setState&state=1";
                    registerId = llHTTPRequest(registerUrl,[HTTP_METHOD,"GET"],"");
                }
                if(message == llD_no) {
                    string registerUrl = targetUrl+"&action=setState&state=0";
                    registerId = llHTTPRequest(registerUrl,[HTTP_METHOD,"GET"],"");
                }
                if(message == llD_site) {
					llLoadURL(id, llD_Click, inworldUrl);
                }
            } else {
                if(message == llD_yes) {
                    llGiveInventory(id,llGetInventoryName(INVENTORY_NOTECARD, 0));
                }
                if(message == llD_site) {
					llLoadURL(id, llD_Click, inworldUrl);
                }
            }
        } else {

            string action = llGetSubString(message,0,7);
            string identString = llGetSubString(message,9,-1);
            if( action == "identify" ) {
                response_key = id;
                string requestUrl = targetUrl+"&action=identify&identString="+llEscapeURL(identString)+"&identKey="+(string)id;
                requestId = llHTTPRequest(requestUrl,[HTTP_METHOD,"GET"],"");
            }
        }
    }

	changed(integer change) {
		if (change & (CHANGED_OWNER | CHANGED_INVENTORY)) llResetScript();
		if (change & (CHANGED_REGION | CHANGED_REGION_START | CHANGED_TELEPORT)) urlRequestId = llRequestURL();
    }

    http_request(key id, string method, string body){    
        if (id == urlRequestId) {
            myurl=body;
            string registerUrl = targetUrl+"&action=register&terminalDescription="+llEscapeURL(terminalDescription)+"&myurl="+myurl;
            registerId = llHTTPRequest(registerUrl,[HTTP_METHOD,"GET"],"");
        } else if(method=="GET" || method=="POST") {
            querystring = llGetHTTPHeader(id,"x-query-string");
            if(querystring == "ping=jOpenSim") {
                llHTTPResponse(id,200,"ok, I am here");
            }
        }
    }

    touch_start(integer count) {
        if(llDetectedKey(0) == owner) {
            llDialog(llDetectedKey(0), "\nShow this terminal in jOpenSim?",
                 [llD_yes, llD_no, llD_cancel], dialogchannel);
        } else {
            llDialog(llDetectedKey(0), llD_explain,
                 [llD_yes, llD_no, llD_site], dialogchannel);
        }
    }


    http_response(key request_id, integer status, list metadata, string body) {
        if (request_id == requestId) {
            integer i = llSubStringIndex(body,resident);
            string messagestring = llGetSubString(body,i,i+llStringLength(resident)+36);
            string seentrigger = llGetSubString(messagestring,0,4);
            if(response_key != NULL_KEY) llInstantMessage(response_key,body);
        }
    }
}

