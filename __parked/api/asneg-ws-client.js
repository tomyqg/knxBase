/*
   Copyright 2015 Kai Huebl (kai@huebl-sgh.de)

   Lizenziert gemäß Apache Licence Version 2.0 (die „Lizenz“); Nutzung dieser
   Datei nur in Übereinstimmung mit der Lizenz erlaubt.
   Eine Kopie der Lizenz erhalten Sie auf http://www.apache.org/licenses/LICENSE-2.0.

   Sofern nicht gemäß geltendem Recht vorgeschrieben oder schriftlich vereinbart,
   erfolgt die Bereitstellung der im Rahmen der Lizenz verbreiteten Software OHNE
   GEWÄHR ODER VORBEHALTE – ganz gleich, ob ausdrücklich oder stillschweigend.

   Informationen über die jeweiligen Bedingungen für Genehmigungen und Einschränkungen
   im Rahmen der Lizenz finden Sie in der Lizenz.

   Autor: Samuel Huebl (samuel.huebl@asneg.de)
*/

// ##############################################
//  ASNeG WebSocket Client
// ##############################################
function ASNeG_Client(ip, format, callbackReveiceFromServer, callbackASNeGClientStatus) {

	// public
	this.status = status;
	this.readEvent = readEvent;
	this.writeEvent = writeEvent;
	this.startMonitoring = startMonitoring;
	this.stopMonitoring = stopMonitoring;
	this.readValueList = readValueList;
	this.readValueInfos = sendValueInfo;

	// private
	var ws;
	var monitoringVariableArray = [];

	// init WebSocket
	initWebSocket();

	// **********************************************
	//  Client - Functions

	// ##############################################
	//  Create WebSocket
	// ##############################################

	function initWebSocket() {
		// Create a connection to Server
		connect();
	}

	// ##############################################
	//  Connection
	// ##############################################

	function connect() {
		ws = new WebSocket(ip, format);
		ws.onopen = webSocket_onopen;
		ws.onerror = webSocket_onerror;
		ws.onmessage = webSocket_onmessage;
		ws.onclose = webSocket_onclose;
		reconnect();
	}

	function reconnect() {
		statusChange();
		if (status() == "CLOSED") {
			connect();
		}
	}

	// ##############################################
	//  Value List
	// ##############################################

	function readValueList() {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'VALUELIST_REQUEST',
					ClientHandle: 'VALUELIST'
				},
				Body : {
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: readValueList... websocket_status: " + status());
		}	
	}

	// ##############################################
	//  Value List Info
	// ##############################################

	function sendValueInfo(variables) {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'VALUEINFO_REQUEST',
					ClientHandle: 'VALUEINFO'
				},
				Body : {
					Variables: variables
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: sendValueInfo... websocket_status: " + status());
		}	
	}

	// ##############################################
	//  Read Event
	// ##############################################

	function readEvent(clientHandle) {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'READ_REQUEST',
					ClientHandle: clientHandle
				},
				Body : {
					Variable: clientHandle
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: read event... websocket_status: " + status());
		}
	}

	// ##############################################
	//  Write Event
	// ##############################################

	function writeEvent(clientHandle, value) {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'WRITE_REQUEST',
					ClientHandle: clientHandle
				},
				Body : {
					Variable: clientHandle,
					SourceTimestamp: getSourceTimestamp(),
					Value: value
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: write event... websocket_status: " + status());
		}
	}

	// ##############################################
	//  Monitoring
	// ##############################################

	function startMonitoring(clientHandle) {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'MONITORSTART_REQUEST',
					ClientHandle: clientHandle
				},
				Body : {
				   	Variable: clientHandle
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: startMonitoring... websocket_status: " + status());
		}
	}

	function stopMonitoring(clientHandle) {
		if (ws.readyState == 1) { // OPEN
			var msg = {
				Header : {
					MessageType: 'MONITORSTOP_REQUEST',
					ClientHandle: clientHandle
				},
				Body : {
				   	Variable: clientHandle
				}
			}
			ws.send(JSON.stringify(msg));
		} else {
			console.log("[ASNeG_Client] Exception: stopMonitoring... websocket_status: " + status());
		}
	}

	function startOldMonitorVariables() {
		for (i=0; i<monitoringVariableArray.length; i++) {
			startMonitoring(monitoringVariableArray[i]);			
		}		
	}

	function checkOldMonitorVariables(value) {
		var unknownVariables = [];
		for (i=0; i<monitoringVariableArray.length; i++) {
			if (value.indexOf(monitoringVariableArray[i]) < 0) {
				unknownVariables.push(monitoringVariableArray[i]);
			}
		}

		for (i=0; i<unknownVariables.length; i++) {
			removeMonitoringVariableFromArray(unknownVariables[i]);
		}		
	}

	function removeMonitoringVariableFromArray(clientHandle) {
		var tmpArray = [];
		for (i=0; i<monitoringVariableArray.length; i++) {
			if (monitoringVariableArray[i] != clientHandle) {
				tmpArray.push(monitoringVariableArray[i]);
			}
		}
		monitoringVariableArray = tmpArray;
	}

	// ##############################################
	//  Status
	// ##############################################

	function status() {
		var status;
		
		switch (ws.readyState) {
			case 0 : 
				status = "CONNECTING";
				break;
			case 1 : 
				status = "OPEN";
				break;
			case 2 : 
				status = "CLOSING";
				break;
			case 3 : 
				status = "CLOSED";
				break;
			default :
				status = "UNDEFINED: status=" + ws.readyState;
		}

		return status;
	}

	function statusChange() {
		callbackASNeGClientStatus(status());
	}

	// ##############################################
	//  Timestamp
	// ##############################################

	function getLocalTimestamp(utc) {
		var utcDate = new Date(utc);
		var offsetInMinutes = utcDate.getTimezoneOffset();
		var targetDate = new Date(utcDate.getTime() - (offsetInMinutes * 60000)); //millisec 60*1000
		return targetDate.toLocaleString();
	}

	function getSourceTimestamp() {
		var utcTime = new Date().toUTCString();
    	var utcDate = new Date(utcTime);
		return utcDate.toISOString();
	}

	// **********************************************
	//  WebSocket - Functions

	// ##############################################
	//  WebSocket Open
	// ##############################################

	function webSocket_onopen()
	{
		statusChange();
		console.log("[ASNeG_Client] WebSocket Verbindung geoeffnet");
		readValueList();
	}

	// ##############################################
	//  WebSocket Close
	// ##############################################

	function webSocket_onclose(event) {
		statusChange();
		if (this.readyState == 2) { // CLOSING
			console.log("[ASNeG_Client] Schliesse Verbindung...");
			console.log("[ASNeG_Client] Die Verbindung durchlaeuft den Closing Handshake");
		}
		else if (this.readyState == 3) { // CLOSED
			console.log("[ASNeG_Client] Verbindung geschlossen...");
			console.log("[ASNeG_Client] Die Verbindung wurde geschlossen oder konnte nicht aufgebaut werden");
			console.log("[ASNeG_Client] Die Verbindung wird neu aufgebaut...");
			reconnect();
		}
		else {
			console.log("[ASNeG_Client] Verbindung geschlossen...");
			console.log("[ASNeG_Client] Nicht behandelter State: " + this.readyState);
		}
	}

	// ##############################################
	//  WebSocket receive message
	// ##############################################

	function webSocket_onmessage(message) {
		//console.log("[ASNeG_Client] WebSocket Data: " + message.data);
		try {
			var data = JSON.parse(message.data);

			var responseObj = new ResponseObj();

			responseObj.msgType = data.Header.MessageType;
			responseObj.clientHandle = data.Header.ClientHandle;

			if (data.Header.StatusCode != undefined) { // Error
				console.log("[ASNeG_Client] ConnectionException: StatusCode=" + data.Header.StatusCode);
				responseObj.connectionStatusCode = data.Header.StatusCode;
				callbackReveiceFromServer(responseObj);
				return;
			}

			responseObj.sourceTimestamp = getLocalTimestamp(data.Body.SourceTimestamp);
			responseObj.serverTimestamp = getLocalTimestamp(data.Body.ServerTimestamp);
			switch (responseObj.msgType) {
				case "READ_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
						responseObj.value = data.Body.Value;
					}
					break;
				case "WRITE_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
					}
					break;
				case "VALUELIST_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						console.log("[ASNeG_Client] ConnectionException: Cannot send ValueList!");
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.value = data.Body.Variables;
						responseObj.valueStatusCode = "Success";;
						sendValueInfo(responseObj.value);
						checkOldMonitorVariables(responseObj.value);
					}
					break;
				case "VALUEINFO_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						console.log("[ASNeG_Client] ConnectionException: Cannot send ValueListInfo!");
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
						responseObj.value = data.Body.Variables;
						startOldMonitorVariables();
					}
					break;	
				case "MONITORSTART_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
						monitoringVariableArray.push(responseObj.clientHandle);					
					}
					break;
				case "MONITORSTOP_RESPONSE" :
					if (data.Body.StatusCode != undefined) {
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
					}
					removeMonitoringVariableFromArray(responseObj.clientHandle);
					break;
				case "MONITORUPDATE_MESSAGE" :
					if (data.Body.StatusCode != undefined) {
						responseObj.valueStatusCode = data.Body.StatusCode;
					} else {
						responseObj.valueStatusCode = "Success";
						responseObj.value = data.Body.Value;
					}
					break;	
				default :
					console.log("[ASNeG_Client] TypeException: unknown msgType=" + responseObj.msgType);
			}

			callbackReveiceFromServer(responseObj);
		} catch(exception) {
			console.log("[ASNeG_Client] " + exception);
		}
	}

	// ##############################################
	//  WebSocket Error
	// ##############################################

	function webSocket_onerror(event) {
		statusChange();
		var reason = event.reason;
		var code = event.code;
		console.log("[ASNeG_Client] WebSocketException: " + reason + "(" + code + ")");
	}
}

