<?php
set_time_limit(1800);
error_reporting(E_ALL ^ E_WARNING);
$time_zone="Asia/Karachi";
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// GLOBAL VARIABLES ///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

$FreeSwitch   = "true";

//Numbers and ID's used in Testing
$apnay_banday = array("3566", "03239754007", "26573", "03018444884", "03314910288", "03350883787", "03467776602", "03465765641","03334204496", "1776", "03216818631", "142566", "142562", "03225537934", "147650", "03360434141");

// $logFiletest3 = "D:/xampp/htdocs/wa/logFilesFS/Incoming-calls-Rahemma.txt";
// $testerpolly  = fopen($logFiletest3, 'w');
// fwrite($testerpolly, "qweqwe");
//for esl connection
$password = "Conakry2014";
$port 	  = "8021";
$host 	  = "127.0.0.1";
$fp 	  = "";	// Connection handle to FreeSwitch Server
$uuid 	  = "";
$Pollyid  = "";

//Paths for resources such as scripts, recordings
$Drive 	   = "D";
$base_dir  = "http://127.0.0.1/wa/";
$pbase_dir = $Drive.":/xampp/htdocs/wa/";
$polly_base= $Drive.":/xampp/htdocs/wa/Praat/";

$scripts_dir 		= $base_dir."Scripts/";
$praat_dir 			= $pbase_dir."Praat/";
$promptsBaseDir 	= $pbase_dir."prompts/";
$DB_dir 			= "http://127.0.0.1/wa/DBScripts/";

$logFilePath 		= $Drive.":/xampp/htdocs/wa/BLogs/";

$Polly_prompts_dir  = "";

$MVP_base  		    = $Drive.":/xampp/htdocs/MVP/";
$MVP_lbase  		= "http://127.0.0.1/MVP/";
$MVP_recordings	    = $MVP_base."Recs/";
$MVP_Story_Recordings = 	$MVP_base."Story/";
$MVP_prompts  		= $MVP_base."Prompts/";
$MVP_scripts  		= $MVP_lbase."Scripts/";
$MVP_fb_dir  		= $MVP_base."Feedback/";
$MVP_feedbacks 		= $MVP_base."Feedback/";
$MVP_comments		= $MVP_base."Comments/";
$FriendName_Dir 	= $polly_base . "FriendNames/";
$SenderName_Dir 	= $polly_base . "UserNames/";

$Feedback_Dir 		= $polly_base."Feedback/";
$CallRecordings_Dir = $polly_base."Recordings/";
$Polly_prompts_dir  = "";
$Country 			="PK";
$SystemLanguage 	="Urdu";
$MessageLanguage 	="Urdu";

$channel 	  = "WateenE1";

if(isset($deployment)){
    if($deployment == 'jmv'){
        // Base directories
        $Drive = "G";
        $base_dir = "http://127.0.0.1/b/";
        $scripts_dir = $base_dir."Scripts/";
        $praat_dir = "http://127.0.0.1/b/Praat/";
        $promptsBaseDir = "http://127.0.0.1/b/prompts/";
        $DB_dir = "http://127.0.0.1/b/DBScripts/";
		$logFilePath = $Drive.":\\htdocs\\b\\LogFiles\\";
		$SystemLanguage="AmerEnglish";
		$MessageLanguage="AmerEnglish";
	}
}

$countryCode 		 = "";

$thiscallStatus 	  = "Answered";    // Temporary assignment
$useridUnCond 		  = "";    // added
$useridUnEnc 		  = "";    // added
$currentStatus 		  = "";
$fh 				  = "";    // temprary variable to act as a place holder for file handle

$logEntry 	   = "";
$callid 	   = "";

// Generic Code, shared by all three types of requests

if($FreeSwitch == "true"){
	$fp = event_socket_create($host, $port, $password);

	if(isset($_REQUEST["uuid"])){
		$uuid = $_REQUEST["uuid"];	// Comment if Freeswitch is disabled
	}
}else{
	answer();
}

// $oreqid 		= "0";
// $currentStatus  = 'InProgress';
// $ReM			= '9916';

// $Pollyid = '2509';

// $userid  = '1000';

// $ouserid = $userid;

// $requestType = "";
// $calltype 	 = 'REM-IN';
// $app 		 = 'Raah-e-Maa';

// fwrite($testerpolly, "calltype: ".$_REQUEST["calltype"]."\n");
// echo $_REQUEST["calltype"];
// echo var_dump($_REQUEST);
// if(strpos($Pollyid, $ReM) !== FALSE) {

// 	$testcall 	 = "true";
// 	$requestType = "";
// 	$calltype 	 = 'REM-IN';
// 	$app 		 = 'Raah-e-Maa';
// }
// else {

// 	$testcall = "FALSE";
// 	$requestType = "";
// 	$calltype = 'Unknown';
// 	$app = 'Alien';
// 	Prehangupunknown();
// }

// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $useridUnEnc 	= $userid;
// $userid 		= '5';
// $countryCode 	='92';

// $callid = makeNewCall($oreqid, $userid, $currentStatus, $calltype, 'WateenE1');	// Create a row in the call table

// writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: ".$app.", Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: ".$Country.", ouserid: ".$ouserid.", Country Code: " . $countryCode);

// $Polly_prompts_dir = $promptsBaseDir.$SystemLanguage."/Polly/";

$AlreadygivenFeedback = "FALSE";
$AlreadyHeardJobs = "FALSE";
$thiscallStatus = "Answered";    // Temporary assignment
$checkForQuota = "false";
$callerPaidDel = "false";
$useridUnCond = "";    // added
$useridUnEnc = "";    // added
$currentStatus = "";
$GlobalVar = 0;
$fh = "";    // temprary variable to act as a place holder for file handle
$seqNo = 0;
$PGameCMBAge = 0;    // How many times has this user called us before in CMB and SysMsg?
$PBrowseCMBAge = 0;    // How many times has this user called us before in ECMB?
$logEntry = "";
$callid="";

$explicitSysLangOption = "FALSE"; 	// Should there be a syslang option menu
$WaitWhileTheUserSearchesForPhNo = "FALSE";

// Generic Code, shared by all three types of requests
$CallTableCutoff = getCallTableCutoff(5);

$ReqTableCutoff = getReqTableCutoff(5);
if(isset($_REQUEST["calltype"])) {	// This is not incoming call as $calltype is set 

	///////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////// OUTGOING CALLS ///////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
    // echo "string";
	$calltype 	 = $_REQUEST["calltype"];
 	$testcall 	 = $_REQUEST["testcall"];
    $channel 	 = $_REQUEST["ch"];
	$userid 	 = $_REQUEST["phno"];
 	$oreqid 	 = $_REQUEST["oreqid"];
 	$recIDtoPlay = $_REQUEST["recIDtoPlay"];
 	$effectno 	 = $_REQUEST["effectno"];
 	$ocallid 	 = $_REQUEST["ocallid"];
 	$ouserid 	 = $_REQUEST["ouserid"];
 	$app 		 = $_REQUEST["app"];
 	$From 		 = $_REQUEST["From"];



    $currentStatus = 'InProgress';

	$useridUnEnc   = KeyToPh($userid);	// added
	
	$countryCode   = getCountryCode($useridUnEnc);
	$PGameCMBAge   = searchPh($userid, "PollyGame");					// How many times has he called us before, CMB? 
    		
	$temp 			 = getPreferredLangs($oreqid);
	$Langs 			 = explode(",", $temp);
	$SystemLanguage  = $Langs[0];
	$MessageLanguage = $Langs[1];
	$healthMsgDir 	 = $base_dir."EbolaMsgs/$MessageLanguage/";
	
	$ouserid = KeyToPh($ouserid);
	$callid  = makeNewCall($oreqid, $userid, $currentStatus, $calltype, $channel);	// Create a row in the call table
	$fh = createLogFile($callid);	// commented out for cloud deployment

 	if(isset($_REQUEST["error"])){
 		$error= $_REQUEST["error"];
 		switch ($error) {
 			case 'USER_BUSY':
 			case 'CALL_REJECTED':
 				busyFCN($callid);
 				break;
 			case 'ALLOTTED_TIMEOUT':
 			case 'NO_ANSWER':
 			case 'RECOVERY_ON_TIMER_EXPIRE':
 				timeOutFCN($callid);
 				break;
 			case 'NO_ROUTE_DESTINATION':
 			case 'INCOMPATIBLE_DESTINATION':
 			case 'UNALLOCATED_NUMBER':
 				callFailureFCN($callid);
 				break;
			
 			default:
 				errorFCN($callid);
 				break;
 		}
 		exit(1);
 	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: ".$app.", Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: ".$Country.", ouserid: ".$ouserid.", Age: ".$PGameCMBAge.", EHLAge: ".$PBrowseCMBAge . ", Country Code: " . $countryCode);	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Call Table cutoff found at:".$CallTableCutoff);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Req Table cutoff found at:".$ReqTableCutoff);

	$Polly_prompts_dir = $promptsBaseDir.$SystemLanguage."/Polly/";
	$EHL_prompts_dir   = $promptsBaseDir.$SystemLanguage."/EHL/";

	sayInt($Polly_prompts_dir. "sil1500.wav ".$Polly_prompts_dir. "sil1500.wav ");
	// sayInt($Polly_prompts_dir."polly-relaunch.wav");

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PGame prompts directory set to: ". $Polly_prompts_dir);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PHealth prompts directory set to: ". $EHL_prompts_dir);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Now attempting to call: ".$NumberToDial);

	if($channel == "WateenE1"){
	
		$dlvRequestType="Delivery";
	
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Calling via $channel.");
	
	    $NumberToDial = str_replace('tel:+', '0', $NumberToDial);

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Dialling $NumberToDial.");

	    if($FreeSwitch == true){
	    	 if($calltype == "REM-ROBO" || $calltype == "REM-ROBO-FWD"){
	    		StartUpFn();
	    		ReahemaRoboCall();
	    	}else if($calltype == "REM-CMB" || $calltype == "REM-CMB-F" || $calltype == "REM-CMB-W" || $calltype == "REM-CMB-ROBO" ||
	    		$calltype == "REM-CMB-CF" || $calltype == "REM-CMB-PF" || $calltype == "REM-CMB-LR" || $calltype == "REM-CMB-S"){
	    		StartUpFn();
	    		RahemmaMVP();
	    	}else if($calltype == "REM-DEL" || $calltype == "REM-DEL-F" || $calltype == "REM-DEL-W" || $calltype == "REM-DEL-ROBO" ||
	    		$calltype == "REM-DEL-CF" || $calltype == "REM-DEL-PF" || $calltype == "REM-DEL-LR" || $calltype == "REM-DEL-S"){
	    		StartUpFn();
	    		MVP_Delivery();
	    	}else{
		    	Prehangup();
		    }
	    }
		else{
			$callResult = callNumber(array($NumberToDial), array(   // 128.2.211.183
				"onAnswer" => create_function("$event", $functionToExecute),
				"callerID" => $callerid,
				"timeout" => 300,
				"onCallFailure" => create_function("$event", "callFailureFCN($callid);"),
				"onError" => create_function("$event", "errorFCN($callid);"),
				"onBusy" => create_function("$event", "busyFCN($callid);"),
				"onTimeout" => create_function("$event", "timeOutFCN($callid);")
				)
			);	
		}
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Calling via $channel.");
		$callResult = callNumber($NumberToDial,
			array(
			"onAnswer" => create_function("$event", $functionToExecute), //when callee answers - initiate prompts, $reply = 'FALSE'
			"callerID" => $callerid,
			"timeout" => 300,
			"onCallFailure" => create_function("$event", "callFailureFCN($callid);"),
			"onError" => create_function("$event", "errorFCN($callid);"),
			"onBusy" => create_function("$event", "busyFCN($callid);"),
			"onTimeout" => create_function("$event", "timeOutFCN($callid);")
			)
		);	
	}
}
else{	

	///////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////// INCOMING CALLS ///////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////

	$oreqid 		= "0";
	$currentStatus  = 'InProgress';

	$ReMCMBCF 			= "0428900800";
	$ReMCMBPF 			= "0428900801";
	$ReMCMBLR			= "0428900808";
	$ReMCMBRS 			= "0428900803";

	$ReMCMB 			= "0428900805";
	$ReMCMBW 			= "0428900809";
	$ReMCMBF 			= "0428900802";
	$ReMCMBR 			= "0428900804";


	$PollyGameCMB 		= "0428333112";
	$BaangCMB	 		= "0428333113";
	$PQuizCallIn 		= "0428333114";
	$PollyBaangCallIn 	= "0428333115";
	$JokeLine 			= "0428333116";
	$PHLBrowseCMB 		= "4132008508";
	$PHLSpreadCMB 		= "4125739955";
	$PollyTestBed 		= "4132008478";
	$PHLABrowseCMB 		= "4132008510";
    $PollyMessageCMB	= "4122677976";
	$PHLUnSub 			= "4132008503";
    $PollyGameUnSub 	= "4123466014";
    $PollyForecariah	= "4123466013";
    

	$Pollyid = calledID(); 	//which number was called in the current call?
	$Pollyid = trim(preg_replace('/\s\s+/', ' ', $Pollyid));

	$userid  = getCallerID();
	$userid  = trim(preg_replace('/\s\s+/', ' ', $userid));
	
	$ouserid = $userid;

	$requestType = "";
	$calltype 	 = 'REM-CMB';
	$app 		 = 'Raah-e-Maa';

	// fwrite($testerpolly, "Pollyid: ".$Pollyid."\n");
	// fwrite($testerpolly, "userid: ".$userid."\n");
	// fwrite($testerpolly, "ouserid: ".$ouserid."\n");
	// fwrite($testerpolly, "calltype: ".$calltype."\n");
	// fwrite($testerpolly, "app: ".$app."\n");

// 	if($userid!="03314910288" && $userid!="03004613744" && $userid!="03465765641" && $userid!="03334204496"
// 		&& $userid!="03225537934" && $userid!="03216818631" && $userid!="03214188989" && $userid!="03360434141"
// 		&& $userid!="03018444884" && $userid != "03228425252" && $userid != "03347582387")
// 	{
// //		Prehangup();
// 		exit(0);
// 	}

	if(strpos($Pollyid, $ReMCMBCF) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-CF";
		$calltype 	 = 'REM-MC-CF';
		$app 		 = 'Raah-e-Maa-Cricket-Flyer';
	}else if(strpos($Pollyid, $ReMCMBPF) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-PF";
		$calltype 	 = 'REM-MC-PF';
		$app 		 = 'Raah-e-Maa-Plain-Flyer';
	}else if(strpos($Pollyid, $ReMCMBLR) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-LR";
		$calltype 	 = 'REM-MC-LR';
		$app 		 = 'Raah-e-Maa-Lahore-Radio';
	}else if(strpos($Pollyid, $ReMCMBRS) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-S";
		$calltype 	 = 'REM-MC-S';
		$app 		 = 'Raah-e-Maa-Social';
	}else if(strpos($Pollyid, $ReMCMB) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB";
		$calltype 	 = 'REM-MC';
		$app 		 = 'Raah-e-Maa';
	}else if(strpos($Pollyid, $ReMCMBW) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-W";
		$calltype 	 = 'REM-MC-W';
		$app 		 = 'Raah-e-Maa-WhatsApp';
	}else if(strpos($Pollyid, $ReMCMBF) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-F";
		$calltype 	 = 'REM-MC-F';
		$app 		 = 'Raah-e-Maa-Flyer';
	}else if(strpos($Pollyid, $ReMCMBR) !== FALSE){
		$testcall 	 = "TRUE";
		$requestType = "REM-CMB-ROBO";
		$calltype 	 = 'REM-MC-ROBO';
		$app 		 = 'Raah-e-Maa-Robo';
	}else{
		$requestType = "";
		$testcall = "FALSE";
		$calltype = 'Missed_Call';
		$app = 'PollyGame';
		Prehangup();
		exit(0);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$useridUnCond 	= $userid;
	$useridUnEnc 	= conditionPhNo($userid, $calltype);
	$useridUnEnc	= trim(preg_replace('/\s\s+/', ' ', $useridUnEnc));
	$userid 		= PhToKeyAndStore($useridUnEnc, 0);
	$countryCode 	= getCountryCode($useridUnEnc);

	$PGameCMBAge 	= searchPh($userid, "PollyGame");						// How many times has he called us before, CMB? 
	
	$PBrowseCMBAge = searchPh($userid, "PollyBrowse");					// How many times has he called us before BCMB?

	$callid = makeNewCall($oreqid, $userid, $currentStatus, $calltype, 'WateenE1');	// Create a row in the call table
	$fh = createLogFile($callid);	// commented out for cloud deployment

	$Polly_prompts_dir = $promptsBaseDir.$SystemLanguage."/Polly/";

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PhToKeyAndStore returned :" . $userid);

	phoneNumBeforeAndAfterConditioning($useridUnCond, $useridUnEnc, $calltype, "");	

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: ".$app.", Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: ".$Country.", ouserid: ".$ouserid.", Age: ".$PGameCMBAge.", EHLAge: ".$PBrowseCMBAge . ", Country Code: " . $countryCode);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Call Table cutoff found at:".$CallTableCutoff);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Req Table cutoff found at:".$ReqTableCutoff);

	$Polly_prompts_dir = $promptsBaseDir.$SystemLanguage."/Polly/";
	$EHL_prompts_dir = $promptsBaseDir.$SystemLanguage."/EHL/";

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PGame prompts directory set to: ". $Polly_prompts_dir);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PHealth prompts directory set to: ". $EHL_prompts_dir);

	if(searchCalls($userid)>1 || $userid < '1000'){	// If its a missed call then reject() will generate 2 retries from the Cisco equipment. This is to ignore those. OR If its a self loop call... ignore it. &&$$** Added < '1000' in place of == '04238333111'
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Ignoring Call as it passed: searchCalls($userid)>1 || $userid < '1000' check."); 	 //&&$$** Added < '1000' in place of == '04238333111'
		////fwrite($tester," In if befre reject .\n".searchCalls($userid));		
		
		rejectCall($app);
		exit(0);
	}

	if(searchCallsReq($userid) <= 0){ // Is there a pending request from this guy already which has never been retried?

		updateCallsReq($userid); // Upgrade all retry type Pending requests from this guy, if any.
		$reqid = createMissedCall('0', '0', $callid, $requestType, $userid, "Pending", $SystemLanguage, $MessageLanguage, "WateenE1", $Pollyid);
	}
	else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Not making a request because there are already first try Call-me-back requests of status Pending from this phone number.");
	}

	$thiscallStatus = "Complete";
	$status = "Complete";
	
	updateCallStatus($callid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Complete. Now exiting.");
	markCallEndTime($callid);
	rejectCall($app);
	
	exit(0);
}

Prehangup();
///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Error Handlers /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function callFailureFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	global$FreeSwitch;

	$thiscallStatus = "Failed";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Failed.");

	Prehangup();
}

function errorFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	global$FreeSwitch;

	$thiscallStatus = "Error";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Error.");

	Prehangup();
}

function timeOutFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	global$FreeSwitch;

	$thiscallStatus = "TimedOut";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Timed Out.");

	Prehangup();
}

function busyFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	global$FreeSwitch;

	$thiscallStatus = "Busy";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Destination number is busy.");

	Prehangup();
}

function keystimeOutFCN($event){
	global $Polly_prompts_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the timed-out prompt.");
	sayInt($Polly_prompts_dir."Nobutton.wav");
}

function ReMkeystimeOutFCN($event){
	global $MVP_prompts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the timed-out prompt.");
	sayInt($MVP_prompts."Nobutton.wav");
}

function keysbadChoiceFCN($event){
	global $Polly_prompts_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the invalid key prompt.");
	sayInt($Polly_prompts_dir."Wrongbutton.wav");
}

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Tropo Library //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
/*
function playInfoMessage(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $info_msg_base;

	sayInt($info_msg_base."infomsg-intro.wav");

	$id = getInfoMessageToPlay();

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Info message returned: ".$id);

	sayInt($info_msg_base.$id.".wav");

	updateInfoMessageCount($id);

	sayInt($info_msg_base."infomsg-end.wav");

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function getInfoMessageToPlay(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $callid;
	global $DB_dir;

	$result = doCurl($DB_dir."get_info_message.php?callid=".$callid."&uid=".$userid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " MSG ID: ".$result["msg_id"]);
		return $result["msg_id"];
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, outside=true.");
}

function firstCall(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $DB_dir;
	global $calltype;

	$result = doCurl($DB_dir."first_call.php?uid=".$userid."&calltype=".$calltype);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " first_call: ".$result["first_call"]);
		return $result["first_call"];
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, outside=true.");
}

function getMsgSurveyFlag(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $DB_dir;

	$result = doCurl($DB_dir."check_ms_flag.php?uid=".$userid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, errorMessage= ".$result["message"]);
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " flag: ".$result["flag"]);
		return $result["flag"];
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, outside=true.");
}

function updateInfoMessageCount($id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $callid;
	global $DB_dir;

	$result = doCurl($DB_dir."update_info_message.php?callid=".$callid."&uid=".$userid."&id=".$id);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return true;
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, outside=true.");
}

function userDemographicSurvey(){

	global $Polly_prompts_dir, $survey_types, $userid;

	if ($userid == "3566") {
		return;
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	// Questions for survey:
	//  1- Education: DTMF
	//  2- Profession: voice-based
	//  3- Location (zilla, tehsil): voice-based
	//  4- Preffered language - DTMF + voice-based

	$types = checkAlreadyExistingSurveys();

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Total Surveys: ".sizeof($survey_types) );
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Answered Surveys: ".sizeof($types) );
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Types of Answered Surveys:".implode(", ",$types));

	// if (sizeof($types) == sizeof($survey_types)) {
	if ($userid != "3566" && in_array($survey_types["edu"], $types) && in_array($survey_types["prof"], $types) && in_array($survey_types["loc"], $types) && in_array($survey_types["lang"], $types) && in_array($survey_types["blind"], $types)) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " All survey questions have been completed by this user.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Returning.");
		return;
	}

	//sayInt($Polly_prompts_dir."SurveyMaloomat.wav");
	sayInt($Polly_prompts_dir."survey-intro.wav");

	if (!in_array($survey_types["lang"], $types) || $userid == "3566") {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Type:".$survey_types['lang']);
		getPrefferedLanguage();
	} else if (!in_array($survey_types["edu"], $types) || $userid == "3566") {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Type:".$survey_types['edu']);
		getEducation();
	} else 	if (!in_array($survey_types["prof"], $types) || $userid == "3566") {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Type:".$survey_types['prof']);
		getProfession();
	} else 	if (!in_array($survey_types["blind"], $types) || $userid == "3566") {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Type:".$survey_types['blind']);
		getInfoBlindUsers();
	} else if (!in_array($survey_types["loc"], $types) || $userid == "3566") {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Type:".$survey_types['loc']);
		getLocation();
	}

	//sayInt($Polly_prompts_dir."SurveyAnswerSaved.wav");
	sayInt($Polly_prompts_dir."maloomat-shukriya.wav");
}

function updateLangSurvey($key, $choice, $perm){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $callid;
	global $DB_dir;
	global $app;

	$result = doCurl($DB_dir."update_survey_lang.php?callid=".$callid."&uid=".$userid."&key=".$key."&opt=".$choice."&id=".$perm);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return true;
	}
}

function updateSurvey($type, $choice){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $callid;
	global $DB_dir;
	global $app;

	$result = doCurl($DB_dir."record_survey.php?callid=".$callid."&uid=".$userid."&type=".$type."&choice=".$choice."&app=".$app);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return true;
	}
}

function checkAlreadyExistingSurveys($type){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid;
	global $DB_dir;

	$result = doCurl($DB_dir."check_already_existing_surveys.php?uid=".$userid."&type=".$type);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["types"];
	}
}

function getEducation(){

	// Primary, Matric ya O Level, Hifz-e-Quran, Barhveen tak, Barhveen say zyada, Rasmi Taleem Kay Baghair
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $Polly_prompts_dir;
	global $survey_types, $survey_edu;

	$loop = true;

	while ($loop) {

		// $prompt =   $Polly_prompts_dir. "SurveyTaleem.wav ".
		// 			$Polly_prompts_dir. 'SurveyOptPrimary.wav '. $Polly_prompts_dir."SendTo1.wav ".
		// 			$Polly_prompts_dir. 'SurveyOptMatric.wav '. $Polly_prompts_dir."SendTo2.wav ".
		// 			$Polly_prompts_dir. 'SurveyOpt12th.wav '. $Polly_prompts_dir."SendTo3.wav ".
		// 			$Polly_prompts_dir. 'SurveyOpt12thplus.wav '. $Polly_prompts_dir."SendTo4.wav ".
		// 			$Polly_prompts_dir. 'SurveyOptHafiz.wav '. $Polly_prompts_dir."SendTo5.wav ".
		// 			$Polly_prompts_dir. 'SurveyOptUneducated.wav '. $Polly_prompts_dir."SendTo6.wav ";

		$prompt =   $Polly_prompts_dir. 'parhay-likhay-nahi-hain.wav '.// $Polly_prompts_dir."SendTo1.wav ".
					$Polly_prompts_dir. 'madrassay-ki-taleem.wav '.// $Polly_prompts_dir."SendTo2.wav ".
					$Polly_prompts_dir. 'matric-pass.wav '.// $Polly_prompts_dir."SendTo3.wav ".
					$Polly_prompts_dir. 'paanchween-pass.wav '.// $Polly_prompts_dir."SendTo4.wav ".
					$Polly_prompts_dir. 'matric-zyada.wav ';// $Polly_prompts_dir."SendTo5.wav ";
					//$Polly_prompts_dir. 'hafiz-madrassay-ki-taleem.wav '. $Polly_prompts_dir."SendTo6.wav ";

		// writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "keystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);

		if ($result->value == "1") {
			updateSurvey($survey_types["edu"], $survey_edu["noed"]);
			$loop = false;
		} else if ($result->value == "2"){
			updateSurvey($survey_types["edu"], $survey_edu["hfz"]);
			$loop = false;
		}else if ($result->value == "3"){
			updateSurvey($survey_types["edu"], $survey_edu["ssc"]);
			$loop = false;
		}else if ($result->value == "4"){
			updateSurvey($survey_types["edu"], $survey_edu["pr"]);
			$loop = false;
		}else if ($result->value == "5"){
			updateSurvey($survey_types["edu"], $survey_edu["grad"]);
			$loop = false;
		}else{
			$loop = true;
		}
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function getProfession(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $DB_dir;
	global $Polly_prompts_dir;
	global $callid;
	global $userid;
	global $survey_types;
	global $profession_demographic_dir;
	global $app;

	$rerecord = true;
	While ($rerecord) {

	    recordAudio($Polly_prompts_dir."mulazmat.wav", array(
	       "beep"=>true,
	       "timeout"=>30,
	       "bargein" => false,
	       "silenceTimeout"=>4,
	       "maxTime"=>4,
	       "terminator" => "#",
	      // "recordFormat" => "audio/wav",
	       "format" => "audio/wav",
	       "recordURI" => $DB_dir."record_survey.php?callid=".$callid."&uid=".$userid."&type=".$survey_types["prof"]."&choice=0"."&app=".$app,
	        )
	    );

	    sayInt($Polly_prompts_dir."aap_nay_ye_record_karwaya_hai.wav");
	   	$filefolder=$userid-($userid%1000);
	   	$path = $profession_demographic_dir.$filefolder."/".$userid.".wav";
		$path = str_replace("\\", "/", $path);
		sayInt($path);


		    $result2 = gatherInput( $Polly_prompts_dir."agar_ye_theek_hai.wav ",
									//$Polly_prompts_dir."SendTo1.wav ".
									//$Polly_prompts_dir."agar_ye_theek_nai_hai.wav ".
									//$Polly_prompts_dir."SendTo2.wav ",
									array(
			"choices" => "[1 DIGITS]",
			"mode" => 'dtmf',
			"bargein" => true,
			"repeat" => 2,
			"timeout"=> 10,
			"onBadChoice" => "keysbadChoiceFCN",
			"onTimeout" => "keystimeOutFCN",
			"onHangup" => create_function("$event", "Prehangup()")
				)
			);

	    	if ($result2->value == "1") {
	    		$rerecord = false;
	    	}
		}


    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function getLocation(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $DB_dir;
	global $Polly_prompts_dir;
	global $callid;
	global $userid;
	global $survey_types;
	global $location_demographic_dir;
	global $app;

	$rerecord = true;

	while ($rerecord) {

	    recordAudio($Polly_prompts_dir."location.wav", array(
	       "beep"=>true,
	       "timeout"=>30,
	       "bargein" => false,
	       "silenceTimeout"=>4,
	       "maxTime"=>4,
	       "terminator" => "#",
	      // "recordFormat" => "audio/wav",
	       "format" => "audio/wav",
	       "recordURI" => $DB_dir."record_survey.php?callid=".$callid."&uid=".$userid."&type=".$survey_types["loc"]."&choice=0"."&app=".$app,
	        )
	    );

		sayInt($Polly_prompts_dir."aap_nay_ye_record_karwaya_hai.wav");
	   	$filefolder=$userid-($userid%1000);
	   	$path = $location_demographic_dir.$filefolder."/".$userid.".wav";
		$path = str_replace("\\", "/", $path);
		sayInt($path);


		$result2 = gatherInput( $Polly_prompts_dir."agar_ye_theek_hai.wav ",
									//$Polly_prompts_dir."SendTo1.wav ".
									//$Polly_prompts_dir."agar_ye_theek_nai_hai.wav ".
									//$Polly_prompts_dir."SendTo2.wav ",
									array(
			"choices" => "[1 DIGITS]",
			"mode" => 'dtmf',
			"bargein" => true,
			"repeat" => 2,
			"timeout"=> 10,
			"onBadChoice" => "keysbadChoiceFCN",
			"onTimeout" => "keystimeOutFCN",
			"onHangup" => create_function("$event", "Prehangup()")
			)
		);

    	if ($result2->value == "1") {
    		$rerecord = false;
    	}
	}

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function getInfoBlindUsers(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $DB_dir;
	global $Polly_prompts_dir;
	global $callid;
	global $userid;
	global $survey_types;
	global $disabled_demographic_dir;
	global $app;

	$rerecord = true;

	while ($rerecord) {

	    recordAudio($Polly_prompts_dir."blind-info.wav", array(
	       "beep"=>true,
	       "timeout"=>30,
	       "bargein" => false,
	       "silenceTimeout"=>4,
	       "maxTime"=>4,
	       "terminator" => "#",
	      // "recordFormat" => "audio/wav",
	       "format" => "audio/wav",
	       "recordURI" => $DB_dir."record_survey.php?callid=".$callid."&uid=".$userid."&type=".$survey_types["blind"]."&choice=0"."&app=".$app,
	        )
	    );

		sayInt($Polly_prompts_dir."aap_nay_ye_record_karwaya_hai.wav");
	   	$filefolder=$userid-($userid%1000);
	   	$path = $disabled_demographic_dir.$filefolder."/".$userid.".wav";
		$path = str_replace("\\", "/", $path);
		sayInt($path);


		$result2 = gatherInput( $Polly_prompts_dir."agar_ye_theek_hai.wav ",
									//$Polly_prompts_dir."SendTo1.wav ".
									//$Polly_prompts_dir."agar_ye_theek_nai_hai.wav ".
									//$Polly_prompts_dir."SendTo2.wav ",
									array(
			"choices" => "[1 DIGITS]",
			"mode" => 'dtmf',
			"bargein" => true,
			"repeat" => 2,
			"timeout"=> 10,
			"onBadChoice" => "keysbadChoiceFCN",
			"onTimeout" => "keystimeOutFCN",
			"onHangup" => create_function("$event", "Prehangup()")
			)
		);

    	if ($result2->value == "1") {
    		$rerecord = false;
    	}
	}

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function getLangPermutation(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $DB_dir;

	$result = doCurl($DB_dir."get_permutation.php");

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " permutation: ".$result["permu"]);
		return $result;
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, outside=true.");
}

function getPrefferedLanguage(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	// Urdu, Punjabi, Pushto, Sindhi, Saraiki, Balochi,

	global $Polly_prompts_dir, $survey_languages, $survey_types, $DB_dir, $userid, $callid, $language_demographic_dir, $app, $survey_languages_id;

	$loop = true;

	$pres = getLangPermutation();
	$perm = $pres["permu"];
	$pid  = $pres["id"];
	while ($loop) {

		$prompt = $Polly_prompts_dir. 'zubaan.wav ';
		$count = 1;
		foreach ($perm as $p) {
			$prompt .= $Polly_prompts_dir. $survey_languages_id[$p][1] . $Polly_prompts_dir."SendTo".$count.".wav ";
			$count += 1;
		}
		$prompt .= $Polly_prompts_dir. 'other-zubaan.wav ';

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", " LANG PROMPT: ". $prompt);

		$result = gatherInput($prompt, array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

		if ($result->value == "1") {
			//updateSurvey($survey_types["lang"] , $survey_languages["pj"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[0]][0]);
			updateLangSurvey($result->value, $perm[0], $pid);
			$loop = false;
		}
		else if ($result->value == "2"){
			//updateSurvey($survey_types["lang"] , $survey_languages["si"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[1]][0]);
			updateLangSurvey($result->value, $perm[1], $pid);
			$loop = false;
		}
		else if ($result->value == "3"){
			//updateSurvey($survey_types["lang"] , $survey_languages["bl"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[2]][0]);
			updateLangSurvey($result->value, $perm[2], $pid);
			$loop = false;
		}
		else if ($result->value == "4"){
			//updateSurvey($survey_types["lang"]  , $survey_languages["ur"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[3]][0]);
			updateLangSurvey($result->value, $perm[3], $pid);
			$loop = false;
		}
		else if ($result->value == "5"){
			//updateSurvey($survey_types["lang"] , $survey_languages["sr"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[4]][0]);
			updateLangSurvey($result->value, $perm[4], $pid);
			$loop = false;
		}
		else if ($result->value == "6"){
			//updateSurvey($survey_types["lang"] , $survey_languages["pu"]);
			updateSurvey($survey_types["lang"] , $survey_languages_id[$perm[5]][0]);
			updateLangSurvey($result->value, $perm[5], $pid);
			$loop = false;
		}
		else if ($result->value == "7"){

			$rerecord = true;

			while ($rerecord) {

			    recordAudio($Polly_prompts_dir."ZubaanKaNaam.wav", array(
			       "beep"=>true,
			       "timeout"=>30,
			       "bargein" => false,
			       "silenceTimeout"=>4,
			       "maxTime"=>4,
			       "terminator" => "#",
			      // "recordFormat" => "audio/wav",
			       "format" => "audio/wav",
			       "recordURI" => $DB_dir."record_survey.php?callid=".$callid."&uid=".$userid."&type=".$survey_types["lang"]."&choice=0"."&app=".$app,
			        )
			    );

				sayInt($Polly_prompts_dir."aap_nay_ye_record_karwaya_hai.wav");
			   	$filefolder=$userid-($userid%1000);
			   	$path = $language_demographic_dir.$filefolder."/".$userid.".wav";
			   	$path = str_replace("\\", "/", $path);
			   	sayInt($path);

		    	$result2 = gatherInput( $Polly_prompts_dir."agar_ye_theek_hai.wav ",
									//$Polly_prompts_dir."SendTo1.wav ".
									//$Polly_prompts_dir."agar_ye_theek_nai_hai.wav ".
									//$Polly_prompts_dir."SendTo2.wav ",
									array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => false,
				"repeat" => 2,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "keystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
					)
				);

		    	if ($result2->value == "1") {
		    		$rerecord = false;
		    	}
			}

		    $loop = false;
		}
		else{
			$loop = true;
		}
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

*/
function writeToLog($id, $handle, $tag, $str){
	global $seqNo;
	global $deployment;
	global $logEntry;
	global $tester;
	global $userid;
	global $fh;
	
	$writeToTropoLogs = "true";
	$spch1 = "%%";
	$spch2 = "$$";
	$del = "~";
	$colons = ":::";
	// From Apr 01, 2015: tag could be L0: System level, L1: Mixed interest, L2: User Experience
	if($tag!= 'L0' && $tag!= 'L1' && $tag!= 'L2'){
		$tag = 'L1';
	}
	if($id == "" || $id == 0){
		$id = 'UnAssigned';
	}	

	
	//$string = $spch1 . $spch2 . $del . $id . $del . $seqNo . $del . date('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str . $spch2 . $spch1;
    // replaced the above with the following to overcome the date bug in tropo cloud. Details in email. Dec 18, 2013
    $now = new DateTime;
	$actualLogLine = $deployment . $del . $id . $del . $seqNo . $del . $now->format('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str;
    $string = $spch1 . $spch2  . $del . $actualLogLine . $spch2 . $spch1;
	
	// $logEntry = $logEntry . $actualLogLine . $spch1 . $spch2;
	fwrite($fh, $string . "\n");
	fflush($fh);
}

function StartUpFn(){
	global $userid;
	global $oreqid;
	global $scripts_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Starting Call Recording");
	//startRecordingCall($scripts_dir."process_callRec.php?callid=".$GLOBALS['callid']);	//process_callRec

	$status = "InProgress";
	updateCallStatus($GLOBALS['callid'], $status);
	$status = "fulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Call Answered.");
}

function Prehangup(){
	global $callid;
	global $fh;
	global $thiscallStatus;
	global $currentCall;
	global $fh;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");
	/////////////////////
	updateWaitingDlvRequests($callid);
	updateCallStatus($callid, $thiscallStatus);

	$sessID = getSessID();
	$calledID = calledID();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "calledID: $calledID , SessionID: $sessID");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging Up. Call ended for callid: ".$callid);
	/////////////////////
	markCallEndTime($callid);
	//sendLogs();
	//stopRecordingCall();
	hangupFT();
	fclose($fh);
	exit(0);
}

function Prehangupunknown(){
	global $callid;
	global $fh;
	global $thiscallStatus;
	global $currentCall;
	global $fh;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");

	$sessID = getSessID();
	$calledID = calledID();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "calledID: $calledID , SessionID: $sessID");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging Up. Call ended for callid: ".$callid);

	hangupFT();
	fclose($fh);
	exit(0);
}

function callNumber($recipient, $params){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " was called with prompt: " . $recipient . " and parameters: " . $params);
	$result = call($recipient, $params);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " complete.");
	return $result;
}

function callTransfer($recipient, $params){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " was called with prompt: " . $recipient . " and parameters: " . $params);
	$result = transfer($recipient, $params);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " complete.");
	return $result;
}

function isThisCallActive(){
	global $FreeSwitch;
	global $uuid;
	global $fp;
	global $callid;
	global $thiscallStatus;
	global $fh;

	$retVal = "";
	if($FreeSwitch == "false"){
		$retVal = $currentCall->isActive;
	}
	else{
		$cmd = "api lua isActive.lua ".$uuid;
		$response = event_socket_request($fp, $cmd);
		$retVal =  trim($response);
	}
	writeToLog($callid, $fh, "isActive", "Is the current call active? ".$retVal.". Hanging up if the call is not active.");
	if($retVal == "false"){
		//$thiscallStatus = "Normal Clearning";
		Prehangup();
	}
	return $retVal;
}

function calledID(){
	global $fp;
	global $FreeSwitch;
	if($FreeSwitch == "false")
	{
		global $currentCall;
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " was called and is returning: " . ($currentCall->calledID));
		return $currentCall->calledID;
	}
	else
	{
		global $uuid;
		$cmd = "api lua getCalledID.lua ".$uuid;//first character is a null (0)
		$response = event_socket_request($fp, $cmd);
		return $response;
	}
}

function getSessID(){
	global $FreeSwitch;

	if($FreeSwitch == "false")
	{
		global $currentCall;
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " was called and is returning: " . ($currentCall->sessionId));
		return $currentCall->sessionId;
	}
	else
	{
		global $uuid;
		return $uuid;
	}
}

function getCallerID(){
	global $FreeSwitch;

	if($FreeSwitch == "false")
	{
		global $currentCall;
		//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " was called.");

		// Getting the User's Phone Number from the sip header
		$useridUnclean = $currentCall->getHeader("from");
		$Useless = array("<", ">", "@", ";", "_");
		$Clean = str_replace($Useless, "&", $useridUnclean);
		$colon = array(":");
		$equals = str_replace($colon, "=", $Clean);
		parse_str($equals);
		$userid = $sip;  // Phone number acquired
		//&&$$** ph encoding
		$useridBfTrim = $userid;
		$userid = trim($userid, " \t\n\r\0\x0B");
		$useridAfTrim = $userid;

		$userCallerID = $currentCall->callerID;

		if($userid == ""){
			$userid = $currentCall->callerID;
		}
		//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "SIP based caller ID before trim:". $useridBfTrim);
		//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "SIP based caller ID after trim:". $useridAfTrim);
		//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Tropos callerID function returned the caller ID to be: ". $userCallerID);

		//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is returning callerid: $userid");
		return $userid;
	}
	else
	{
		global $uuid;
		global $fp;
		global $fh;

		$cmd = "api lua getCallerID.lua ".$uuid;//first character is a null (0)
		$response = event_socket_request($fp, $cmd);
		return $response;
	}
}

function startRecordingCall($params){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " was called with params: " . $params);
	startCallRecording($params);
}

function stopRecordingCall(){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " was called.");
	stopCallRecording();
}

function answerCall(){
	global $FreeSwitch;

	if($FreeSwitch == "false")
	{
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");
	answer();
	}
	else
	{


		global $uuid;
		global $fp;
		global $fh;

		$cmd = "api lua answer.lua ".$uuid;//first character is a null (0)
		$response = event_socket_request($fp, $cmd);
		return $response;
	}
}

function sendSMS($msg, $params){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with msg: $msg and params:" . $params);
	return message($msg, $params);
}

function rejectIfCallInactive(){
	if(isThisCallActive()=="true")
		return;
	Prehangup();
}

function rejectCall($app) {

	global $FreeSwitch;

	if($FreeSwitch == "false") {

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");
		reject();
	}
	else {

		global $uuid;
		global $fp;
		global $fh;

	    $cmd = "api lua reject.lua ".$uuid;//first character is a null (0)
		$response = event_socket_request($fp, $cmd);
	}
	Prehangup();
}

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

/*>>>>*********************************************************************************************************<<<<*/
/*>>>>********************************************  ReM MVP  **********************************************<<<<*/

////////////////////////////////// Raah-e-Maa MVP Main Menu Start/////////////////////
function RahemmaMVP() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);
	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_scripts;

	sayInt($MVP_prompts."MainPrompt1.wav ");

	$loop = true;

	while ($loop) {

		$prompt =   $MVP_prompts."MainPrompt2.wav";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					));

		if ($result->value == "1") {	// Questions

			//setLog(getActionID("menu_questions"), "1");
			Questions();
		}
		else if ($result->value == "2") {	//Stories

			//setLog(getActionID("menu_stories"), "12");
			Stories();
		}
		else if ($result->value == "3"){	// fun stuff

			//setLog(getActionID("menu_clips_quizes"), "19");
			Funstuff();
		}
		else if ($result->value == "4") {	//main feedback

			//setLog(getActionID("main_feedback"), "32");
			Feedback();
		}
		else {
			sayInt($MVP_prompts."Wrongbutton.wav");
			$loop = true;
		}
	}
	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}
////////////////////////////////// Raah-e-Maa MVP Main Menu Start/////////////////////
////////////////////////////////// Raah-e-Maa ROBO CAll //////////////////////////////
function ReahemaRoboCall() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_scripts;

	sayInt($MVP_prompts."SabaJawaab.wav ");
	sayInt($MVP_prompts."FatimaJawaab.wav ");

	$loop = true;

	while ($loop) {

		$prompt =   $MVP_prompts."Replay.wav";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					));

		if ($result->value == "1") {	
			RoboToRaahemaTransfer();
			RahemmaMVP();
		} else if ($result->value == "2") {
			MVP_Forward(-1,"RoboCall");
		} else {
			sayInt($MVP_prompts."Wrongbutton.wav");
			$loop = true;
		}
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function RoboToRaahemaTransfer(){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	global $MVP_scripts;
		
	$result = doCurl($MVP_scripts."robocallTransfer.php?user_id=".$userid."&call_id=".$callid);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
}
//////////////////////////////////////////////////////////////////////


////////////////////////////////// Questions Start ///////////////////////////////////
function Questions() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_scripts;

	$loop = true;

	while ($loop) {
		$prompt =   $MVP_prompts."QuestionPrompt1.wav";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);
		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);


		if ($result->value == "1") { 	//ask question

			//setLog(getActionID("menu_ask_question"), "0");
			askQuestion();															//its arguments
		}
		else if ($result->value == "2") {	//all question
			//setLog(getActionID("menu_all_questions") , "0");
			allQuestions();
		}
		else if ($result->value == "3"){	// user question
			//setLog( getActionID("menu_user_question"), "0");
			listenOwnQuestion();
		}
		else if ($result->value == "4"){	//return to main menu
			$loop = false;
		}
		else{
			sayInt($MVP_prompts."Wrongbutton.wav");
			$loop = true;
		}
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function askQuestion() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $callid;
	global $userid;
	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_scripts;

	// sayInt($MVP_prompts."Disclaimer1.wav");

	$question_id = addQuestion();

	if (!$question_id) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Something bad happened in ".__FUNCTION__);
		return;
	}

	$loop	     = true;
	$rerecord 	 = true;

	while ($question_id && $loop) {

		if ($rerecord) {
			recordAudio($MVP_prompts."Record1.wav", array(
		       "beep"=>true,
		       "timeout"=>300,
		       "bargein" => false,
		       "silenceTimeout"=>4,
		       "maxTime"=>30,
		       "terminator" => "#",
		       "format" => "audio/wav",
		       "recordURI" => $MVP_scripts."API/api/add/question_user?question_id=".$question_id."&user_id=".$userid."&call_id=".$callid,
		        )
		    );
		    $rerecord = false;
		}

	    sayInt($MVP_prompts."Record2.wav");

	   	$path = $MVP_recordings."Q".$question_id.".wav";
		sayInt($path);

	    $result = gatherInput( $MVP_prompts."Record3.wav", array(
									"choices" => "[1 DIGITS]",
									"mode" => 'dtmf',
									"bargein" => true,
									"repeat" => 2,
									"timeout"=> 10,
									"onBadChoice" => "keysbadChoiceFCN",
									"onTimeout" => "ReMkeystimeOutFCN",
									"onHangup" => create_function("$event", "Prehangup()")
								)
							);

    	if ($result->value == "1") {

    		sayInt($MVP_prompts."Record4.wav");
	    	$loop = false;
		    while (true) {
			    $result2 = gatherInput( $MVP_prompts."QDisc1.wav", array(
											"choices" => "[1 DIGITS]",
											"mode" => 'dtmf',
											"bargein" => true,
											"repeat" => 2,
											"timeout"=> 10,
											"onBadChoice" => "keysbadChoiceFCN",
											"onTimeout" => "ReMkeystimeOutFCN",
											"onHangup" => create_function("$event", "Prehangup()")
										)
									);
		    	if ($result2->value == "1") {
		    		MVP_makeQuestionPublic($question_id);
		    		sayInt($MVP_prompts."Shukriya2.wav");
		    		break;
			    } else if ($result2->value == "2") {
    				sayInt($MVP_prompts."QDisc3.wav");
    				break;
				} else {
    				sayInt($MVP_prompts."Wrongbutton.wav");
				}
		    }
	    }
	    else if ($result->value == "2") {

    		$loop = true;
    		$rerecord = true;
    	} else {
    		sayInt($MVP_prompts."Wrongbutton.wav");
    		$loop = true;
    	}
    }

	addFunctionLog(__FUNCTION__, 1);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function listenOwnQuestion() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

    global $callid;
	global $userid;
	global $MVP_prompts;
	global $MVP_scripts;
	global $MVP_recordings;

	$questions = getUserQuestions(0);

	if (!$questions) {
		sayInt($MVP_prompts."Noquestion.wav");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Something bad happened in ".__FUNCTION__);
		return;
	}

	if ( $ex_questions = getUserQuestions(1)) {
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "ex_questions : ". $ex_questions);
		$result = gatherInput( $MVP_prompts."Return1.wav", array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 3,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "ReMkeystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);

		if ($result->value == "1") { //repeat
			$questions = $ex_questions;
		}

		else if ($result->value == "2") { 	// next
			// pass
		}
	}

	$qid  = 0;
	$loop = true;

	$play = true;

	while($loop) {

		if ($qid >= sizeof($questions)) {
			sayInt($MVP_prompts."NoMorePrivate.wav");
			break;
		}

		$question_id = $questions[$qid]['question_id'];
		$answer_id   = $questions[$qid]['answer_id'];
		$faq 		 = $questions[$qid]['faq'];
		$question_action = $questions[$qid]['question_action'];

		if ($play) {
	/*New Question - 1
	FAQ - 2
	Ignore - 3
	Irrelevant - 4
	Inappropriate - 5
	Inaudible - 6
	Unresponsive - 7
	*/
			if ($question_action == "3") {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Ignore.wav ");
			}else if ($question_action == "4") {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Irrelevant.wav ");
			}else if ($question_action == "5") {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Inappropriate.wav ");
			}else if ($question_action == "6") {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Inaudible.wav ");
			}else if ($question_action == "7") {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Irresponsive.wav ");
			}else if( !isset($question_id) ) { 
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "QID not set, check API!");
				sayInt($MVP_prompts."Question2.wav");
				$loop = false;
			}else if( isset($faq) && isset($question_id) ) {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Jawaab.wav ".$MVP_recordings."FAQ".$faq.".wav ". $MVP_prompts. "Postanswer.wav");
				Vote($question_id, "question");
			}else if( isset($answer_id) && isset($question_id) ) {
				setLog(getActionID("listen_q"), $question_id);
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Jawaab.wav ".$MVP_recordings."A".$answer_id.".wav ". $MVP_prompts. "Postanswer.wav");
				Vote($question_id, "question");
			} else if( !isset($answer_id)) {

				//setLog(getActionID("listen_oq"), $question_id);

				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Busy.wav");
			}

			$play = false;
		}

		$result = gatherInput( $MVP_prompts."PublicAnswer3.wav", array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 3,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "ReMkeystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);

		if ($result->value == "1") { //repeat
			$loop = true;
			$play = true;
		}

		else if ($result->value == "2") { 	// next
			$qid++;
			$loop = true;
			$play = true;
		}

		else if ($result->value == "4")  {      //back to main prompt
			$loop  = false;
		}
		else if ($result->value == "3") {
			//forwardQuestion($question_id);
			MVP_Forward($question_id, "Question");
			$loop  = true;
			$play = false;
		}
		else {
			sayInt($MVP_prompts."Wrongbutton.wav");
			$loop = true;
		}
	}

	addFunctionLog(__FUNCTION__, 1);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function allQuestions() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

    global $callid;
	global $userid;
	global $MVP_prompts , $MVP_recordings;
	global $MVP_scripts;

	$questions = getQuestions(getActionID("listen_q"), 0, 0);

	if ( $ex_questions = getQuestions(getActionID("listen_q"), 0, 1) ) {

		$result = gatherInput( $MVP_prompts."Return1.wav", array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 3,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "ReMkeystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);

		if ($result->value == "1") { //repeat
			$questions = $ex_questions;
		}

		else if ($result->value == "2") { 	// next
			// pass
		}
	}


	if (!$questions) {
		sayInt($MVP_prompts."Nopublicquestion.wav");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Something bad happened in ".__FUNCTION__);
		return;
	}

	sayInt($MVP_prompts."PublicAnswer1.wav");

	$qid  = 0;
	$loop = true;
	$play = true;

    while ($loop) 	{

		if ($qid >= sizeof($questions)) {
			sayInt($MVP_prompts."Nomorequestion.wav");
			break;
		}

		$question_id 	 = $questions[$qid]['question_id'];
		$answer_id       = $questions[$qid]['answer_id'];
		$listen_before   = $questions[$qid]['listen_before'];
		$faq 		 = $questions[$qid]['faq'];

		if(is_null($question_id)){
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__." - Question ID is null! Iter: ".$qid);
			break;
		}else if( isset($question_id) ) {
			setLog(getActionID("listen_q"), $question_id);

			if(!is_null($answer_id) && $play) {
				sayInt($MVP_prompts."Sawaal.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Jawaab.wav ".$MVP_recordings."A".$answer_id.".wav");
		 		$play = false;
		 		Vote($question_id, "question");
			} else if (!is_null($faq) && $play) {
				sayInt($MVP_prompts."Yourquestion.wav ".$MVP_recordings."Q".$question_id.".wav ".$MVP_prompts."Jawaab.wav ".$MVP_recordings."FAQ".$faq.".wav ". $MVP_prompts. "Postanswer.wav");				
		 		$play = false;
		 		Vote($question_id, "question");
			}

	 		$result = gatherInput( $MVP_prompts."PublicAnswer3.wav", array(
									"choices" => "[1 DIGITS]",
									"mode" => 'dtmf',
									"bargein" => true,
									"repeat" => 2,
									"timeout"=> 10,
									"onBadChoice" => "keysbadChoiceFCN",
									"onTimeout" => "ReMkeystimeOutFCN",
									"onHangup" => create_function("$event", "Prehangup()")
								)
							);

		    if ($result->value == "1") {
		    	$loop = true;
		    	$play = true;
		    } else if ($result->value == "2") {
	    		$qid++;
	    		$loop = true;
	    		$play = true;
	    		continue;
	    	}
	    	/* else if (in_array($result->value, array("3","4","5"))) {
	    		if ($like) {
	    			sayInt($MVP_prompts."Alreadyliked.wav");
	    		} else if ($dislike) {
	    			sayInt($MVP_prompts."Alreadydisliked.wav");
	    		} else if ($report) {
	    			sayInt($MVP_prompts."Alreadyreported.wav");
	    		} else {
	    			if ($result->value == "3") {
			    		sayInt($MVP_prompts."Answer3.wav");
			    		$like = $questions[$qid]['like'] = true;
		    		} else if ($result->value == "4") {
			    		sayInt($MVP_prompts."Answer4.wav");
			    		$dislike = $questions[$qid]['dislike'] = true;
		    		} else if ($result->value == "5") {
			    		sayInt($MVP_prompts."Answer4.wav");
			    		$report = $questions[$qid]['report'] = true;
		    		}
			    	setResponse($question_id, $result->value);
		    		$loop = true;
	    		}
	    	} */
	    	else if($result->value == "3") {
	    		MVP_Forward($question_id, "Question");
	    		//forwardQuestion($question_id);
	    		$loop = true;
	    		$play = false;
	    	} else if($result->value == "4") {
	    		$loop = false;
	    	} else {
	    		sayInt($MVP_prompts."Wrongbutton.wav");
	    		$loop = true;
	    	}
	 	} else {
    		$qid++;
    		$loop = true;
    		$play = true;
			continue;
		}
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

////////////////////////////////// Questions End /////////////////////////////////////

////////////////////////////////// Stories Start /////////////////////////////////////
function Stories() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_scripts;

	sayInt($MVP_prompts."StoriesPrompt1.wav ");

	$loop = true;

	while ($loop) {

		$prompt =   $MVP_prompts."StoriesPrompt2.wav";

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);


		if ($result->value == "1") { 	//listen stories

			//setLog(getActionID("listen_story"), "0");
			listenStories();															//its arguments
		}
		else if ($result->value == "2") {	//share stories
			//setLog( getActionID("share_own_story") , "0");
			$record_id = MVP_CreateStoryEntry();
			MVP_Record("story", $record_id);
		}
		else if ($result->value == "3") {	//return to main menu
			$loop = false;
		}
		else {
			sayInt($MVP_prompts."Wrongbutton.wav");
			$loop = true;
		}
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function listenStories() {
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_Story_Recordings;
	global $MVP_scripts;
	global $userid;

	$files = MVP_GetFile($userid, "Story");

	if (!$files) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " error in fetching $type files.");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
		return;
	}

	if (sizeof($files) <= 0) {
		sayInt($MVP_prompts."Nopublicstory.wav");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
		return;
	}


	sayInt($MVP_prompts."Storydisclaimer.wav");

	$count = 0;

	foreach ($files as $file) {

		if ($count > 0) {
			sayInt($MVP_prompts."Nextstory.wav");
		}

		$loop        = true;
		$break_outer = false;
		$captured    = false;
		$play_story = true;

		while($loop) {

			if ($play_story) {

				sayInt($MVP_Story_Recordings.$file['name']);
				$voted =  Vote($file['file_id'],"story");
				if ($voted == 1) {
					continue;
				}
				//  else if ($voted == 0) {
				// 	$promptToplay = $MVP_prompts."Listenagain.wav ".$MVP_prompts."Storyoptipn.wav";
				// } else {
				// 	$promptToplay = $MVP_prompts."Storyoptipn.wav ". $MVP_prompts."Listenagain.wav ";
				// }
			}

			$promptToplay =  $MVP_prompts."Storyoptions2.wav";
 			$result = gatherInput($promptToplay, array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

			$play_story = true;

			if($result->value == "1") {  // Repeat this story
				continue;
			}
			else if($result->value == "2") { // Proceed to Next Story
				$loop = false;
			}
			else if($result->value == "3") { // Comment on this Story				
				listen_comments($file['file_id']);
			}
			else if($result->value == "4") { // Forward this story
				MVP_Forward($file['file_id'], "Story");
			}
			else if($result->value == "5") { // return to previous menu
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
				return;
			}
		}

		$count++;
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_Forward($file_id, $type, $info = false) {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $MVP_prompts;
	global $MVP_scripts;
	global $userid;
	global $calltype;
	global $callid, $SystemLanguage, $MessageLanguage, $channel;

	$prompt = $MVP_prompts."Forward1.wav";

	$loop = true;

	$number = "";

	while($loop){
		$NumberList = gatherInput($prompt, array(
				        "choices" => "[11 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => true,
						"repeat" => 3,
						"timeout"=> 30,
						"interdigitTimeout"=> 20,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"terminator" => "#",
						"onHangup" => create_function("$event", "Prehangup()")
				    )
				);

		if($NumberList->name == 'choice'){

			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Friend's phone number entered: ".$NumberList->value.". Now playing it.");

			$num12 = str_split($NumberList->value);

			for($index1 = 0; $index1 < count($num12); $index1 += 1){
				if($index1 == 0){
					$fileName = $num12[$index1].'.wav';
					$numpath = $MVP_prompts.$fileName;
				}
				else{
					$fileName = $num12[$index1].'.wav';
					$numpath = $numpath . "\n" . $MVP_prompts.$fileName;
				}
			}

			sayInt($MVP_prompts."Forward8.wav ");
			$presult = sayInt($numpath);
				
			if ($presult->name == 'choice'){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$numpath.".");
			}

			$choice = gatherInput(
					$MVP_prompts."Forward2.wav ", array(
			        "choices" => "[1 DIGITS],*,#",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 3,
					"timeout"=> 5,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
		        )
		    );
		    
			if($choice->value=="1"){
				$number = $NumberList->value;
				if ($dinfo = MVP_GetDeliveryInfo()) { 
					//if ($dinfo["count"] <= 0) 
						MVP_Record("name", $userid);
					
				}

				$dreq = MVP_CreateDeliveryRequest($file_id, $type,PhToKeyAndStore($number, $userid), $info); 
				if ($dreq) {
					if ($userid == 3566) {
						if(makeNewReq($dreq["id"], -1, $callid, "REM-DEL", PhToKeyAndStore($number, $userid), "Pending", $SystemLanguage, $MessageLanguage, $channel)){
							//MVP_CaptureEvent($dreq["id"], 8);
							//setLog(8, $dreq["id"]);
							sayInt($MVP_prompts."Forward6.wav ");
							$loop   = false;
						}else
							$loop   = true;
					}else{
						$delType = "REM-DEL";
						if ($type == "RoboCall") {
							$delType = "REM-ROBO-FWD";
						} else 	if ($calltype == "REM-CMB-W" || $calltype == "REM-DEL-W") {
							$delType = "REM-DEL-W";
						} else 	if ($calltype == "REM-CMB-ROBO" || $calltype == "REM-DEL-ROBO" || $calltype == "REM-ROBO" 
							|| $calltype == "REM-ROBO-FWD") {
							$delType = "REM-DEL-ROBO";
						} else 	if ($calltype == "REM-CMB-F"|| $calltype == "REM-DEL-F") {
							$delType = "REM-DEL-F";
						} else 	if ($calltype == "REM-CMB-PF"|| $calltype == "REM-DEL-PF") {
							$delType = "REM-DEL-PF";
						} else 	if ($calltype == "REM-CMB-S"|| $calltype == "REM-DEL-S") {
							$delType = "REM-DEL-S";
						} else 	if ($calltype == "REM-CMB-LR"|| $calltype == "REM-DEL-LR") {
							$delType = "REM-DEL-LR";
						} else 	if ($calltype == "REM-CMB-CF"|| $calltype == "REM-DEL-CF") {
							$delType = "REM-DEL-CF";
						}

						if(makeNewReq($dreq["id"], -1, $callid, $delType, PhToKeyAndStore($number, $userid), "Pending", $SystemLanguage, $MessageLanguage, $channel)){
						sayInt($MVP_prompts."Forward6.wav ");
						$loop   = false;
					}else
						$loop   = true;
					}
				}else
					$loop   = true;
				$loop = false;
			}else if($choice->value=="2"){
				$loop = true;
			}
		}
		else{
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. No number entered. Now hanging up.");		
			$FriendsNumber = 'false';
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
			Prehangup();
		}
	}

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_CreateDeliveryRequest($file_id, $type, $fuid, $info = false){

	global $MVP_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts.'new_delivery_request.php?uid='.$userid.'&file_id='.$file_id.'&call_id='.$callid.'&fuid='.$fuid.'&type='.$type.'&info='.intval($info);
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function MVP_Record($type, $record_id){

	global $callid;
	global $userid;
	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_Story_Recordings;
	global $MVP_scripts;
	global $calltype;
	global $MVP_comments;
	global $MVP_requests;
	global $MVP_fb_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__."_".$type, 0);

	if($type == 'request'){
		$maxtime = 60;
		$prompt  = $MVP_prompts."Inforequest.wav ".$MVP_prompts."Beepcomment.wav ";
	}else if($type == 'name'){
		$maxtime = 10;
		$prompt  = $MVP_prompts."Forward3.wav ";
	}else if($type == 'story'){
		$maxtime = 120;
		$prompt  = $MVP_prompts."Apnistory.wav ". $MVP_prompts."Storydisclaimer2.wav ".$MVP_prompts."StoryRecord.wav ";
	}else if($type == 'comment'){
		$maxtime = 60;
		$prompt  = $MVP_prompts."Comment1.wav ";//.$MVP_prompts."Commentdisclaimer.wav ".$MVP_prompts."Beepcomment.wav ";
	}else if($type == 'fb'){
		$maxtime = 60;
		$prompt  = $MVP_prompts."Mainfeedback.wav ";
	}

	$uri = $MVP_scripts."save_recording.php?type=".$type."&uid=".$userid."&record_id=".$record_id."&call_id=".$callid."&suno_abbu=true";

    $loop=true;

    while ($loop) {

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting user to record something of type: $type");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "URI: $uri");

		$record_return = recordAudio($prompt , array(
		       "beep"=>true,
		       "timeout"=>30,
		       "bargein" => false,
		       "silenceTimeout"=>5,
		       "maxTime"=>$maxtime,
		       "terminator" => "#",
		       "format" => "audio/wav",
		       "recordURI" => $uri
		        )
		    );

		sayInt($MVP_prompts."Replay.wav");

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Return value of record: ".$record_return->value);

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Replaying the recording of type: $type");

		if($type == "request"){
	   		sayInt($MVP_requests.$record_id.".wav");
	   		//MVP_CaptureEvent($record_id, 13);
		}
	   	else if($type == "name")		{
	   		sayInt($MVP_recordings."U".$record_id.".wav");
	   		//setLog(getActionID(""),$record_id)
	   		//MVP_CaptureEvent($record_id, 15);
	   	}
	   	else if($type == "story"){
	   		sayInt($MVP_Story_Recordings.$record_id.".wav");
	   		//setLog(getActionID(""),$record_id)
	   		//MVP_CaptureEvent($record_id, 12);
	   	}
	   	else if($type == "comment"){
	   		sayInt($MVP_comments."C-".$record_id.".wav");
	   		//setLog(getActionID(""),$record_id)
	   		//MVP_CaptureEvent($record_id, 11);
	   	}else if($type == 'fb'){
	   		$filefolder = $record_id-($record_id%1000);
		   	$path       = $MVP_fb_dir.$filefolder."/".$record_id.".wav";
			$path       = str_replace("\\", "/", $path);
			sayInt($path);
	   		//setLog(getActionID(""),$record_id)
			//MVP_CaptureEvent($record_id, 16);
		}

		$choice = gatherInput(
				$MVP_prompts."Genconfirmation.wav ", array(
			        "choices" => "[1 DIGITS],*,#",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 3,
					"timeout"=> 5,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
		        )
	    );

		if($choice->value=="1"){
			$loop = false;
			if($type == "request")
	   			sayInt($MVP_prompts."Shukriya3.wav");
		   	// else if($type == "name")
		   	// 	sayInt($MVP_prompts."Shukriya1.wav");
		   	else if($type == "story")
		   		sayInt($MVP_prompts."Shukriya3.wav");
		   	else if($type == "comment"){
		   		sayInt($MVP_prompts."Shukriya1.wav");
		   	}else if($type == 'fb'){
				sayInt($MVP_prompts."Shukriya1.wav");
			}
		}else if($choice->value=="2") {
			$loop = true;
			if($type == "comment")
				$prompt  = $MVP_prompts."Comment1.wav ";
			elseif($type == "story")
				$prompt  = $MVP_prompts."StoryRecord.wav ";
		}
	}

	addFunctionLog(__FUNCTION__."_".$type, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_CreateStoryEntry(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $MVP_scripts;

	$result = doCurl($MVP_scripts."create_story.php?uid=".$userid."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function MVP_CreateFBEntry(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $MVP_scripts;

	$result = doCurl($MVP_scripts."create_fb_entry.php?uid=".$userid."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function MVP_CreateRaayeEntry($story_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $MVP_scripts;

	$result = doCurl($MVP_scripts."create_comment.php?uid=".$userid."&call_id=".$callid."&story_id=".$story_id);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function MVP_CaptureEvent($f_id, $action_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_scripts;
	global $userid, $callid;

	$result = doCurl($MVP_scripts."set_call.php?uid=".$userid."&f_id=".$f_id."&call_id=".$callid."&action_id=".$action_id);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return true;
	}
}

function MVP_GetFile($uid, $type){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_scripts;
	global $userid, $callid;

	$result = doCurl($MVP_scripts."get_file_id.php?uid=".$uid."&type=".$type."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["files"];
	}
}

function MVP_GetDeliveryInfo(){

	global $MVP_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts.'get_delivery_info.php?uid='.$userid;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function setResponse( $question_id, $response,$response_type) {
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/add/response?user_id=".$userid."&call_id=".$callid."&question_id=".$question_id."&response=".$response."&response_type=".$response_type);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
	
		return false;
	
	}

	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");

	
		return true;
	}
}

function MVP_makeQuestionPublic($question_id) {
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	global $MVP_scripts;
	$url    = $MVP_scripts.'set_question_public.php?question_id='.$question_id;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}

}

function MVP_Delivery($del_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_prompts, $MVP_recordings, $MVP_Story_Recordings;
	$del_id = $_REQUEST["recIDtoPlay"];
	if ($params = MVP_GetDeliveryParams($del_id)) {

		$id   = $params["file_id"];
		$fuid = $params["fuid"];
		$type = $params["type"];
		$answer_id = $params["answer_id"];

		sayInt($MVP_prompts."Received1.wav");
		sayInt($MVP_recordings."U".$fuid.".wav");
		sayInt($MVP_prompts."Received2.wav");
		
		$loop=true;
		
		while($loop){

			if ($type == "Story") {
				sayInt($MVP_Story_Recordings.$id.".wav");
			} else {
				sayInt($MVP_prompts."Sawaal.wav ".$MVP_recordings."Q".$id.".wav ".$MVP_prompts."Jawaab.wav ".$MVP_recordings."A".$answer_id.".wav");
			}
			
			$result = gatherInput($MVP_prompts."Options1.wav ", array(
					"choices" => "[1 DIGITS]", 
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
		
			if($result->value == "1"){
				continue;
			}
			
			// else if($result->value == "2"){
			// 	MVP_Forward($id, $type);
			// }
			
			else if($result->value == "2"){
				RahemmaMVP();
			} else {
				sayInt($MVP_prompts."Wrongbutton.wav");
			}
		}
	}
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_GetDeliveryParams($id){

	global $MVP_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts.'get_delivery_params.php?id='.$id;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

////////////////////////////////// Stories End ///////////////////////////////////////

////////////////////////////////// Fun Stuff Start ///////////////////////////////////
function Funstuff() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	addFunctionLog(__FUNCTION__, 1);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	return;
	// global $MVP_prompts;
	// global $MVP_recordings;
	// global $MVP_scripts;

	// $loop = true;

	// while ($loop) {

	// 	$prompt =   $MVP_prompts."FunPrompt.wav";

	// 	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

	// 	$result = gatherInput($prompt, array(
	// 					"choices" => "[1 DIGITS]",
	// 					"mode" => 'dtmf',
	// 					"bargein" => false,
	// 					"repeat" => 2,
	// 					"timeout"=> 10,
	// 					"onBadChoice" => "keysbadChoiceFCN",
	// 					"onTimeout" => "ReMkeystimeOutFCN",
	// 					"onHangup" => create_function("$event", "Prehangup()")
	// 				)
	// 			);


	// 	if ($result->value == "1") { 	//fun factual Clips

	// 		//setLog(getActionID("menu_fun_factual_clips"), "0");
	// 		FunFactClips();
	// 	}
	// 	else if ($result->value == "2") {	//personality trivia quizes

	// 		//setLog( getActionID("menu_personality_trivia_quizes") , "0");
	// 	}
	// 	else if ($result->value == "3") {	//return to main menu
	// 		//setLog( getActionID("return_mainmenu") , "0");
	// 		$loop = false;
	// 	}
	// 	else {
	// 		sayInt($MVP_prompts."Wrongbutton.wav");
	// 		$loop = true;
	// 	}
	// }

}

//---------------------------------------
function FunFactClips(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_prompts;
	global $MVP_scripts;

	$options = array(
	  array('type' => 'funny', 'log_value' =>'choose_fun_clips','name' => "funny-baatain.wav "),
	  array('type' => 'fact', 'log_value' =>'choose_fact_clips', 'name' => "factual-baatain.wav "),
	);

	shuffle($options);

	$loop = true;

	while ($loop) {

		$prompt =   $MVP_prompts.$options[0]['name'].$MVP_prompts."1.wav ".
					$MVP_prompts.$options[1]['name'].$MVP_prompts."2.wav ".
					$MVP_prompts."wapis.wav ".$MVP_prompts."3.wav ";

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " prompt: ".$prompt);

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "keystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);

		if ($result->value == "1") {
			//setLog(getActionID($options[0]["log_value"]), "0");
			ReMPlay($options[0]["type"]);
			$loop = true;
		} else if ($result->value == "2"){
			//setLog(getActionID($options[1]["log_value"]), "0");
			ReMPlay($options[1]["type"]);
			$loop = true;
		}else if ($result->value == "4"){
			$loop = false;
			//setLog( getActionID("return_mainmenu") , "0");
		}else{
			$loop = true;
		}
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function ReMRotateStack($arr){

	array_push($arr, array_shift($arr));
	return $arr;
}

function ReMSetPreference($preference, $fileid){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $ReMT1_scripts;
	global $userid, $callid;

	$result = doCurl($ReMT1_scripts."set_preference.php?uid=".$userid."&pref=".$preference."&callid=".$callid."&fileid=".$fileid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	return $result;
}
	
function ReMPlay($type){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered with type:".$type);

	global $ReMT1_prompts;
	global $ReMT1_recs;
	// global $ReMT1_recs_stack;

	$loop = true;
	$play = true;
	$newid= true;
	$pref_exists = false;

	$id = "";

	$ReM_recs_stack = ReMGetRecordings();

	while ($loop) {

		if ($pref_exists){
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Already assigned preference for ID:".json_encode($id));
			sayInt($ReMT1_prompts."pref_exists.wav ");
			$pref_exists = false;
		}else{

			if ($newid){
				$id = $ReM_recs_stack[$type][0];
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " New ID:".json_encode($id));
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Now Rotating!");
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Current stack: ".json_encode($ReM_recs_stack[$type]));
				$ReM_recs_stack[$type] = ReMRotateStack($ReM_recs_stack[$type]);
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Rotated stack: ".json_encode($ReM_recs_stack[$type]));
				$newid = false;
			}

			if ($play){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " ID:".json_encode($id));
				//sayInt($ReM_recs.$type."-".$id.".wav");
				sayInt($ReMT1_recs.$id[1].".wav ".$ReMT1_recs.$id[0].".wav ");
				ReMDBLogT1("listen", $id[2]);
				$play = false;
			}
		}

		$prompt = $ReMT1_prompts. 'Dobara_sunney_ke_liay.wav '.
				  //$ReMT1_prompts. 'pasand.wav '.
				  $ReMT1_prompts. 'more-'.$type.'.wav '.
				  //$ReMT1_prompts. 'Aur.wav '.
				  $ReMT1_prompts. 'Koi_aur.wav '.
				  $ReMT1_prompts. 'Tafseelaat.wav ';

		$result = gatherInput($prompt, array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "keystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);

		if ($result->value == "1") {
			$play = true;
			$loop = true;
			ReMDBLogT1("repeat", $id[2]);
		}else if ($result->value == "5"){
			$newid= true;
			$play = true;
			$loop = true;
			ReMDBLogT1('aur-'.$type, $id[2]);
		}else if ($result->value == "2"){
			ReMDBLogT1("like", $id[2]);
			$exists = ReMSetPreference("like", $id[2]);
			$pref_exists = $exists["exists"];
			$loop = true;
		}else if ($result->value == "3"){
			ReMDBLogT1("dislike", $id[2]);
			$exists = ReMSetPreference("dislike", $id[2]);
			$pref_exists = $exists["exists"];
			$loop = true;
		}else if ($result->value == "4"){
			ReMDBLogT1("report", $id[2]);
			$exists = ReMSetPreference("report", $id[2]);
			$pref_exists = $exists["exists"];
			$loop = true;
		}else if ($result->value == "6"){
			$loop = false;
			ReMDBLogT1('other-options', $id[2]);
		}else if ($result->value == "9"){
			ReMDBLogT1("youtube", $id[2]);
			sayInt($ReMT1_prompts."Youtube.wav ");
			$loop = true;
		}else{
			$loop = true;
		}
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function ReMDBLogT1($action, $fileid){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $ReMT1_scripts;
	global $userid, $callid;

	$result = doCurl($ReMT1_scripts."update_log.php?uid=".$userid."&action=".$action."&callid=".$callid."&fileid=".$fileid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return true;
	}
}
////////////////////////////////// Fun Stuff End /////////////////////////////////////

////////////////////////////////// Feedback Start ///////////////////////////////////

function feedback() {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	addFunctionLog(__FUNCTION__, 0);

	global $callid;
	global $userid;
	global $MVP_prompts;
	global $MVP_recordings;
	global $MVP_feedbacks;
	global $MVP_scripts;

	$feedback_id = addFeedback();

	if (!$feedback_id) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Something bad happened in ".__FUNCTION__);
		return;
	}

	$loop 	  = true;
	$rerecord = true;
	$replay   = true;

	while ($loop) {

		if ($rerecord) {
			recordAudio($MVP_prompts."Mainfeedback.wav", array(
		       "beep"=>true,
		       "timeout"=>300,
		       "bargein" => false,
		       "silenceTimeout"=>4,
		       "maxTime"=>30,
		       "terminator" => "#",
		      // "recordFormat" => "audio/wav",
		       "format" => "audio/wav",
		       "recordURI" => $MVP_scripts."API/api/add/main_feedback?feedback_id=".$feedback_id."&call_id=".$callid."&user_id=".$userid,
		        )
		    );
		    $rerecord = false;
		    $replay   = true;
		}

		if ($replay) {
			sayInt($MVP_prompts."Replay.wav");
			sayInt($MVP_feedbacks.$feedback_id.".wav");
			$replay = false;
		}

	    $result = gatherInput( $MVP_prompts."Record3.wav", array(
									"choices" => "[1 DIGITS]",
									"mode" => 'dtmf',
									"bargein" => true,
									"repeat" => 2,
									"timeout"=> 10,
									"onBadChoice" => "keysbadChoiceFCN",
									"onTimeout" => "ReMkeystimeOutFCN",
									"onHangup" => create_function("$event", "Prehangup()")
								)
							);

    	if ($result->value == "1") {

    		sayInt($MVP_prompts."Shukriya1.wav");

    		$more_loop = true;

    		while ($more_loop) {

    			$result = gatherInput( $MVP_prompts."Morefeedback.wav",  array(
										"choices" => "[1 DIGITS]",
										"mode" => 'dtmf',
										"bargein" => true,
										"repeat" => 2,
										"timeout"=> 10,
										"onBadChoice" => "keysbadChoiceFCN",
										"onTimeout" => "keystimeOutFCN",
										"onHangup" => create_function("$event", "Prehangup()")
									)
								);

			   	if ($result->value == "1") {
		    		$feedback_id = addFeedback();
		    		$more_loop= false;
		    		$loop 	  = true;
		    		$rerecord = true;
		    	}
		    	else if ($result->value == "2") {
		    		$more_loop= false;
		    		$loop 	  = false;
		        }else{
		        	sayInt($MVP_prompts."Wrongbutton.wav");
		        	$more_loop= true;
		        }
    		}
    	}

    	else if ($result->value == "2") {
    		$loop 	  = true;
    		$rerecord = true;
    	}
    	else {
    		sayInt($MVP_prompts."Wrongbutton.wav");
    		$loop 	  =  true;
    		$rerecord = false;
    	}
	}

	addFunctionLog(__FUNCTION__, 1);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function addFeedback() 
{
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/add/main_feedback?user_id=".$userid."&call_id=".$callid);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");	
	
	if ($result['result']["error"]) 
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");

		return false;
	
	}
	
	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
	
		return $result['result']["feedback_id"]; 
	}
}

////////////////////////////////// Feedback End /////////////////////////////////////

/*>>>>***********************************************************************************************<<<<*/
/*>>>>**************************************** Rahemaa's MVP *****************************************<<<<*/
/*>>>>***********************************************************************************************<<<<*/


/*******************************************************************************************************************/
/************************************************  Model Functions  ************************************************/
/*******************************************************************************************************************/
/*
function getActionID($type) {

	switch ($type) {
			case "menu_questions":
				return 1;
			case "ask_question":
				return 2;
			case "set_question_private":
				return 3;
			case "set_question_public":
				return 4;
			case "listen_own_question_answer":
				return 5;
			case "applaud_answer_private_question":
				return 6;
			case "forward_private_question_answer":
				return 7;
			case "listen_all_question_answer":
				return 8;
			case "applaud_answer_public_question":
				return 9;
			case "forward_public_question_answer":
				return 10;
			case "questions_return":
				return 11;
			case "menu_stories":
				return 12;
			case "listen_story":
				return 13;
			case "report_story":
				return 14;
			case "comment_on_story":
				return 15;
			case "forward_story":
				return 16;
			case "share_own_story":
				return 17;
			case "stories_return":
				return 18;
			case "menu_clips_quizes":
				return 19;
			case "menu_fun_factual_clips":
				return 20;
			case "choose_fun_clips":
				return 21;
			case "listen_fun_clips":
				return 22;
			case "listen_factual_clips":
				return 23;
			case "choose_fact_clips":
				return 24;
			case "menu_personality_trivia_quizes":
				return 25;
			case "menu_personality_quizes":
				return 26;
			case "choose_personality_quiz":
				return 27;
			case "attempt_personality_quiz":
				return 28;
			case "menu_trivia_quizes":
				return 29;
			case "choose_trivia_quiz":
				return 30;
			case "attempt_trivia_quiz":
				return 31;
			case "main_feedback":
				return 32;
			case "return_mainmenu":
				return 33;
			default:
				return 0;
	}
}

function setLog($action, $fileid)
{

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_scripts;
	global $userid, $callid;

	$result = doCurl($MVP_scripts."api/add/log?user_id=".$userid."&action_id=".$action."&call_id=".$callid."&file_id=".$fileid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);


	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");

		return false;
	}
	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");

		return true;
	}
}

function getActionID($type) {

	switch ($type) {
		case "menu_all_questions":
			return 1;
		case "listen_oq":
			return 2;
		case "menu_user_question":
			return 3;
		case "menu_ask_question":
			return 4;
		case "listen_case":
			return 5;
		case "menu_case":
			return 6;
		case "main_feedback":
			return 7;
		case "listen_aq":
			return 8;
		case "main_return":
			return 9;
		default:
			return 0;
	}
}

function setLog($action, $fileid) {
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $test4_scripts;
	global $userid, $callid;
	
	$result = doCurl($test4_scripts."api/add/log?user_id=".$userid."&action_id=".$action."&call_id=".$callid."&file_id=".$fileid);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	
	if ($result['result']["error"])  
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
	
		return false;
	}
	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
	
		return true;
	}
}
*/

function getActionID($type){
 /*
	case "menu_questions":
				return 1;
			case "ask_question":
				return 1;
			case "set_question_private":
				return 1;
			case "set_question_public":
				return 1;
			case "listen_own_question_answer":
				return 1;
			case "applaud_answer_private_question":
				return 1;
			case "forward_private_question_answer":
				return 1;
			case "listen_all_question_answer":
				return 1;
			case "applaud_answer_public_question":
				return 1;
			case "forward_public_question_answer":
				return 1;
			case "questions_return":
				return 1;
			case "menu_stories":
				return 1;
			case "listen_story":
				return 1;
			case "report_story":
				return 1;
			case "comment_on_story":
				return 1;
			case "forward_story":
				return 1;
			case "share_own_story":
				return 1;
			case "stories_return":
				return 1;
			case "menu_quizes_fun":
				return 1;
			case "main_feedback":
				return 1;
						*/

	switch ($type) {
		case "menu_all_questions":
			return 1;
		case "menu_user_questions":
			return 3;
		case "menu_ask_question":
			return 4;
		case "menu_listen":
		case "listen_oq":
			return 2;
		case "listen_case":
			return 5;
		case "menu_case":
			return 6;
		case "main_feedback":
			return 7;
		case "listen_aq":
			return 8;
		case "main_return":
			return 9;
		case "listen_q":
			return 10;
		default:
			return 0;
	}
}

function setLog($functionName, $action) {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $MVP_scripts;
	global $userid, $callid;

	$result = doCurl($MVP_scripts."API/api/add/log?user_id=".$userid."&action_id=".$action."&call_id=".$callid."&file_id=".$fileid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.".$result['result']["error"]);

		return false;
	}
	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.".$result['result']["error"]);

		return true;
	}
}

function addFunctionLog($functionName, $action){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $MVP_scripts;

	$result = doCurl($MVP_scripts."insert_function_log.php?functionName=".$functionName."&callid=".$callid."&action=".$action."&userid=".$userid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function addQuestion() {
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/add/question_user?user_id=".$userid."&call_id=".$callid);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, ".$result);

	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		
		return false;
	
	}
	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");

		if(!isset($result['result']['question_id']))
    	{
    	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true, message = api object error ");
    	}
	
		return $result['result']["question_id"];
	}
}

function getUserQuestions($old) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/questions_user?user_id=".$userid."&old=".$old);            
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
  
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"])  {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");	
	} else {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false ");
    	if(!isset($result['result']['user_questions']))
    		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true, message = api object error ");
    	else
			return $result['result']['user_questions'];
	}
	return false;
}

function getUserAttemptedQuestions($action_id, $attempted = true) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/questions_user_attempted?user_id=".$userid."&action_id=".$action_id."&attempted=".$attempted);            
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
  
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"])  {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");	
	} else {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false ");
    	if(!isset($result['result']['questions']))
    		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true, message = api object error ");
    	else if(sizeof($result['result']['questions']) > 0)
			return $result['result']['questions'];
	}
	return false;
}

function getCaseQuestions()  {

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $test4_scripts;
	
	$result = doCurl($test4_scripts."API/api/cases_questions");              
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result['result']["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
	}
	else {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		if(!isset($result['result']['case_questions']))
    		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true, message = api object error ");
    	else
			return $result['result']['case_questions'];
	}
	return false;
}

function setCaseAnswer($case_id,$answer) {
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $test4_scripts;
	
	$result = doCurl($test4_scripts."API/api/add/case_answer?user_id=".$userid."&call_id=".$callid."&case_id=".$case_id."&user_answer=".$answer);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result['result']["error"]) 
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	} else {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return true; 
	}
}

function getQuestions($action_id, $oq, $attempted) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $MVP_scripts;
	
	$result = doCurl($MVP_scripts."API/api/get_questions?user_id=$userid&action_id=$action_id&attempted=$attempted&oq=$oq");    //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	//$result = '{"result":{"error":false,"length":0,"questions":[]}}';
	$result = json_decode($result, true);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result['result']["error"])
		return false;
	else
		return $result['result']['questions'];
}

function createComment($question_id) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $test4_scripts;
	
	$result = doCurl($test4_scripts."API/api/add/question_feedback?question_id=".$question_id."&user_id=".$userid."&call_id=".$callid);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}

	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");

	    return $result['result']['q_feedback_id'] ;
    }
}

function createForward($question_id, $fuid) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $test4_scripts;
	
	$result = doCurl($test4_scripts."API/api/forward/question?file_id=".$question_id."&user_id=".$userid."&call_id=".$callid."&dest=".$fuid);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"]){

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}

	else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
	    return $result['result']['forward_id'] ;
    }
}

function getUserComments($question_id) {
	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	
	global $userid, $callid;
	
	global $test4_scripts;
	
	$result = doCurl($test4_scripts."API/api/question_feedback?question_id=".$question_id);              //api hit 
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);
	
	$result = json_decode($result, true);
	
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	
	if ($result['result']["error"])
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
	
		return false;
	
	}

	else
	{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");

	    return $result['result']['question_feedback'] ;
    }
}

function listen_comments($file_id) {
	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	global $MVP_prompts;
	global $MVP_comments;

	$comments_result = MVP_GetComments($file_id);
	$comments = $comments_result["comments"];
	if ($comments_result["len"] > 0) {
		sayInt($MVP_scripts."FirstComment.wav");
		$back = false;
		for ($i=0; $i < $comments_result["len"] && !$back; $i++) { 
			writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", "Playing comment with id = " . $comments[$i]);
			sayInt($MVP_comments."C-".$comments[$i].".wav");
			while (true) {
	 			$result = gatherInput($MVP_prompts."CommentMenu1.wav", array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => false,
						"repeat" => 2,
						"timeout"=> 10,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);

				if($result->value == "2") { 
					$record_id = MVP_CreateRaayeEntry($file_id);
					MVP_Record("comment", $record_id);
				} else if($result->value == "1") {  
					// MVP_ListenToComments($file_id);
					if ($i <= $comments_result["len"] - 1 ) {
						sayInt($MVP_prompts."NextComment.wav");
					} else {
						sayInt($MVP_prompts."Nomorecomment.wav");	
					}
					break;					
				} else if($result->value == "3") { 
					$back = true; 
					break;
				} else {
					sayInt($MVP_prompts."Wrongbutton.wav");
				}
			}
		}
	} else {
		while (true) {
 			$result = gatherInput($MVP_prompts."CommentMenu2.wav", array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

			if($result->value == "1") {  
				$record_id = MVP_CreateRaayeEntry($file_id);
				MVP_Record("comment", $record_id);
				break;
			} else if($result->value == "2") { 
				break;
			} else {
				sayInt($MVP_prompts."Wrongbutton.wav");
			}
		}		
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_ListenToComments($story_id){

	global $callid;
	global $userid;
	global $MVP_prompts;
	global $MVP_scripts;
	global $MVP_comments;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$result = MVP_GetComments($story_id);

	if($result){

		$comments = $result["comments"];

		if($result["len"] <= 0){
			sayInt($MVP_prompts."no-comments.wav");
		}else{
			
			sayInt($MVP_prompts."Comments.wav");

			$next_comment = true;
			for($i = 0; $i < $result["len"]; $i++) {
				
				if (!$next_comment) break;

				$loop = true;
				while ($loop) {
					
					sayInt($MVP_comments.$comments[$i].".wav");

					$prompt =   $MVP_prompts."Commentoptions.wav";

					$choice = gatherInput($prompt, array(
				        "choices" => "[1 DIGITS], *, #",
						"mode" => 'dtmf',
						"bargein" => true,
						"repeat" => 3,
						"timeout"=> 5,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
				        )
				    );
				    
					if($choice->value=="1"){
						$loop = true;
					}else if($choice->value=="2"){
						$loop = false;
					}else if($choice->value=="4"){
						$loop = false;
						$next_comment = false;
					}else if($choice->value=="3"){
						$record_id = MVP_CreateRaayeEntry($file_id);
						MVP_Record("comment", $record_id);
						$loop = false;
						$next_comment = true;
					}
				}
			} 
		}
	}else{
		sayInt( $PQPrompts."error.wav ");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Error: Comments returned with error. Exiting.");
		//PQLog("error", "comments", "exiting");
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function MVP_GetComments($story_id){
	
	global $userid;
	global $MVP_prompts;
	global $MVP_scripts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts."get_comments.php?story_id=".$story_id;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function vote($file_id, $type) {

	writeToLog($GLOBALS['call_id'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	global $MVP_prompts;

	$return_val = 0;
	if (getuserResponse($file_id, $type) == false) {
		$userorder = getUserVotePromptOrder();
		$promptToplay = $MVP_prompts."PublicAnswer2.1.wav ". $MVP_prompts."PublicAnswer2.2.wav ".$MVP_prompts."Press2.wav ". $MVP_prompts."PublicAnswer2.3.wav ".$MVP_prompts."Press3.wav ". $MVP_prompts."PublicAnswer2.4.wav ";
		if ($userorder % 2 == 1) {
		$promptToplay = $MVP_prompts."PublicAnswer2.1.wav ". $MVP_prompts."PublicAnswer2.3.wav ".$MVP_prompts."Press2.wav ". $MVP_prompts."PublicAnswer2.2.wav ".$MVP_prompts."Press3.wav ". $MVP_prompts."PublicAnswer2.4.wav ";
		}

		while (true) {
				$result = gatherInput($promptToplay, array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

			$play_story = true;

			if($result->value == "1") {  // up vote
				$return_val = 1;
				break;
			} else if(($result->value == "2" && $userorder % 2 == 0) || ($result->value == "3" && $userorder % 2 == 1)) {  // down vote) { 
				setResponse($file_id, 3 , $type);
				sayInt($MVP_prompts."Shukriya1.wav");
				$return_val = 2;
				break;
			} else if(($result->value == "3" && $userorder % 2 == 0) || ($result->value == "2" && $userorder % 2 == 1)) {
				setResponse($file_id, 4 , $type);
				sayInt($MVP_prompts."Shukriya1.wav");
				$return_val = 2;
				break;
			} else if($result->value == "4") {  // down vote
				setResponse($file_id, 5 , $type);
				sayInt($MVP_prompts."Shukriya1.wav");
				$return_val = 2;
				break;
			} else {
				sayInt($MVP_prompts."Wrongbutton.wav");
			}
		}		
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
	return $return_val;
}

function getuserResponse($content_id, $type){
	
	global $userid;
	global $MVP_prompts;
	global $MVP_scripts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts."getuserResponse.php?user_id=".$userid."&content_id=".$content_id."&type=".$type;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"] || $result["count"] == 0) {
		return false;
	}else{
		return true;
	}
}
 
function getUserVotePromptOrder() {
	global $userid;
	global $MVP_scripts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $MVP_scripts."getUserVotePromptOrder.php?user_id=".$userid;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"] ) {
		return 0;
	}else{
		return $result["id"];
	}
}

/*******************************************************************************************************************/
/**************************************************  Controllers   *************************************************/
/*******************************************************************************************************************/


/*>>>>***********************************************************************************************<<<<*/
/*>>>>**************************************** Rahemaa's MVP *****************************************<<<<*/
/*>>>>***********************************************************************************************<<<<*/

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// DB Access Functions ////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
function makeNewRec($callid){
	global $DB_dir;
	$url = $DB_dir."New_Rec.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function callRecURI($url){
	global $tester;
	//////fwrite($tester, "in callRecURI  .\n");
	//////fwrite($tester,$url. " = callRecURI  .\n");
	$result = doCurl($url);
	return $result;
}

function makeNewCall($reqid, $phno, $status, $calltype, $chan){
	global $DB_dir;
	$url = $DB_dir."New_Call.php?reqid=$reqid&phno=$phno&calltype=$calltype&status=$status&ch=$chan";
	$result = doCurl($url);
	return $result;
}

function makeNewSess($reqid, $type, $ph){
	global $DB_dir;
	global $calltype;
	//$url = $DB_dir."New_Sess.php?reqid=$reqid&calltype=$type";
	//$url = "http://localhost/wa/DBScripts/New_Sess.php?calltype=$type&reqid=$reqid&ph=$ph";
	$url = $DB_dir."New_Sess.php?calltype=$type&reqid=$reqid&ph=$ph";
	$result = doCurl($url);
	return $result;
}

function makeNewReq($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch){
	global $DB_dir;
	global $userid;
	global $testcall;
	$url = $DB_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$userid&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
	$result = doCurl($url);
	return $result;
}
/*
function makeNewReqREM($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch){
	global $DB_dir;
	global $userid;
	global $testcall;
	$url = $DB_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=0428333112&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
	$result = doCurl($url);
	return $result;
}*/

function createMissedCall($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch, $pollyid){
	global $DB_dir;
	global $userid;
	global $testcall;
	global $tester;
	$url = $DB_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$pollyid&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
				////fwrite($tester,"$url.\n");
	$result = doCurl($url);
	return $result;
}

function makeNewTransferLog($callid, $phno, $event, $from, $to, $menuid){
	global $DB_dir;
	$url = $DB_dir."New_Transfer.php?callid=$callid&event=$event&from=$from&to=$to&menuid=$menuid&phno=$phno";
	$result = doCurl($url);
	return $result;
}


function appendXferred($app){
	global $DB_dir;
	global $callid;
	$url = $DB_dir."updateXferredTo.php?app=$app&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function makeNewFB($fbtype, $callid){
	global $DB_dir;
	$url = $DB_dir."New_FB.php?fbtype=$fbtype&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function makeNewEBQ($callid, $ph){
	global $DB_dir;
	$url = $DB_dir."New_EBQ.php?ph=$ph&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function markCallEndTime($callid){
	global $DB_dir;
	$url = $DB_dir."Update_Call_Endtime.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function updateCallStatus($callid, $status){
	global $DB_dir;
	$url = $DB_dir."Update_Call_Status.php?callid=$callid&status=$status";
	$result = doCurl($url);
	return $result;
}

function updateRequestStatus($reqid, $status){
	global $DB_dir;
	global $channel;
	$url = $DB_dir."Update_Request_Status.php?reqid=$reqid&status=$status&ch=$channel";
	$result = doCurl($url);
	return $result;
}

function updateFollowupStatus($reqid, $status){
	global $DB_dir;
	$url = $DB_dir."Update_Request_Followup.php?reqid=$reqid&fwstatus=$status";
	$result = doCurl($url);
	return $result;
}

function updateWaitingDlvRequests($id){
	global $DB_dir;
	$url = $DB_dir."Update_Waiting_DLV_Reqs.php?rcallid=$id";
	$result = doCurl($url);
	return $result;
}

function gaveFeedBack($ph){
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."gave_feedback.php?ph=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function updateFeedbackNotifyStatus($fbid, $notify){
	global $DB_dir;
	$url = $DB_dir."Update_FB_Notify.php?fbid=$fbid&notify=$notify";
	$result = doCurl($url);
	return $result;
}

function getPhNo(){
	global $DB_dir;
	global $ocallid;
	$url = $DB_dir."GetPhNo.php?callID=$ocallid";
	$result = doCurl($url);
	return $result;
}

function getPreferredLangs($id){
	global $DB_dir;
	$url = $DB_dir."GetPreferredLangs.php?id=$id";
	$result = doCurl($url);
	return $result;
}

// Function to send Information to BJ
function addToBJLogs($callid, $fh, $sender, $friend, $recid, $count){
	global $praat_dir;

	$URL = $praat_dir."ModifiedRecordings/". getFilePath($recid.".wav", "TRUE") . $count."-s-".$recid.".wav";
	$URLEnc = urlencode($URL);
	$curlString = "http://test.babajob.com/services/service.asmx/PollyInvitation?inviteeMobile=$friend&invitorMobile=$sender&invitorVoiceNameUrl=$URLEnc&serviceName=polly&servicePassword=pollytalks";
	$response = doCurl($curlString);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Response: ".$response.", URL invoked: ".$curlString);
}

function isThisAMissedCall(){
	global $userid;
	global $TreatmentGroup;

	/*if($userid == '03334204496'){
		return 0;
	}*/


	if(inAnyTG($userid) > 0){
		$TreatmentGroup = getTG($userid);
	}

	$AgeToday = getAgeToday($userid);
	if($AgeToday <= ($TreatmentGroup)){
		return 1;
	}
	else{
		return 0;
	}
}

function getQ(){
	global $DB_dir;
	$url = $DB_dir."GetQ.php";
	$result = doCurl($url);
	return $result;
}

function getk(){
	global $DB_dir;
	$url = $DB_dir."Getk.php";
	$result = doCurl($url);
	return $result;
}

function inTGx($ph, $tg){
	global $DB_dir;
	$url = $DB_dir."search_TGx.php?ph=$ph&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function getTGxD($ph, $tg){
	global $DB_dir;
	$url = $DB_dir."get_TGxD.php?ph=$ph&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function hasThisGuyEverHeardQuota($ph, $tg){
	global $DB_dir;
	$url = $DB_dir."has_Ever_Heard_Quota.php?ph=$ph&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function IsQuotaApplicable($ph, $tg){
	global $DB_dir;
	$url = $DB_dir."IsQuotaAppliedToday.php?ph=$ph&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function setHeardBye($phno){
	global $callid;
	global $DB_dir;
	$url = $DB_dir."setHeardBye.php?phno=$phno&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function getCountryCode($ph){
	global $DB_dir;
	$url = $DB_dir."CountryCode.php?ph=$ph";
	$result = doCurl($url);
	if($result != "Not found."){
		return explode(" - ", $result)[0];
	}
	return "";
}

function phoneNumBeforeAndAfterConditioning($before, $after, $type, $sender){
	global $callid;
	global $DB_dir;
	$url = $DB_dir."Update_Conditioned_PhNo.php?callid=$callid&uncond=$before&cond=$after&type=$type&sender=$sender";
	$result = doCurl($url);
	return $result;
}

function doesFileExist($fname){
	global $scripts_dir;
	$url = $scripts_dir."doesFileExist.php?fname=$fname";
	$result = doCurl($url);
	return $result;
}

function whatWasTheChannelOfTheOriginalRequest($OrigReqID){
	global $DB_dir;
	$url = $DB_dir."getReqChannel.php?id=$OrigReqID";
	$result = doCurl($url);
	return $result;
}

/////////////////// Cutoff based functions start ///////////////////////////
function getAgeToday($ph){
	global $DB_dir;
	global $calltype;
	global $CallTableCutoff;
	$url = $DB_dir."AgeToday.php?phno=$ph&type=$calltype&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function getAgeinDays($ph){
	global $DB_dir;
	global $calltype;
	global $CallTableCutoff;
	$url = $DB_dir."AgeinDays.php?phno=$ph&type=$calltype&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchCalls($ph){
	global $DB_dir;
	global $calltype;
	global $CallTableCutoff;
	$url = $DB_dir."search_calls.php?ph=$ph&type=$calltype&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchPh($ph, $application){
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."search_phno.php?ph=$ph&app=$application&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchCallsReq($ph){
	global $DB_dir;
	global $calltype;
	global $ReqTableCutoff;
	$url = $DB_dir."search_calls_hist.php?ph=$ph&type=$calltype&cutoff=$ReqTableCutoff";
	$result = doCurl($url);
	return $result;
}

function updateCallsReq($ph){
	global $DB_dir;
	global $ReqTableCutoff;
	$url = $DB_dir."update_calls_hist.php?ph=$ph&cutoff=$ReqTableCutoff";
	$result = doCurl($url);
	return $result;
}

/////////////////// Cutoff based functions end ///////////////////////////
function readAndInc(){
	global $DB_dir;
	$url = $DB_dir."ReadInc.php";
	$result = doCurl($url);
	return $result;
}

function readAndIncx($k){
	global $DB_dir;
	$url = $DB_dir."ReadIncx.php?k=$k";
	$result = doCurl($url);
	return $result;
}

function inAnyTG($ph){
	global $DB_dir;
	$url = $DB_dir."search_TG.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function getTG($ph){
	global $DB_dir;
	$url = $DB_dir."get_TG.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function addToTG($tg){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."add_to_TG.php?ph=$userid&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function addToTGx($Q, $k){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."add_to_TGx.php?ph=$userid&Q=$Q&k=$k";
	$result = doCurl($url);
	return $result;
}

function setLastPlayedOn($tg){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."set_last_played_on.php?ph=$userid&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function getLastPlayedOn($tg){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."get_last_played_on.php?ph=$userid&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function getCallTableCutoff($days){
	global $DB_dir;
	$url = $DB_dir."getCallTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

function getReqTableCutoff($days){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."getReqTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

//-------------------------------
function doesFileExistHTTP($path){
	global $DB_dir;
	$url = $DB_dir."doesFileExistHTTP.php?path=".urlencode($path);
	$result = doCurl($url);
	return $result;
}

function availableMsgFiles($supportedMsglangs, $currMsgPath){
	global $base_dir;

	$availableLangs = array();
	$len = count($supportedMsglangs);
	$count = 0;
	for($i=0; $i<$len; $i++){
		$path = $base_dir."EbolaMsgs/".$supportedMsglangs[$i]."/".$currMsgPath;
		if(doesFileExistHTTP($path) == "TRUE"){
			$availableLangs[$count] = $supportedMsglangs[$i];
			$count++;
		}
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "av Langs: ".print_r($availableLangs, true));
	return $availableLangs;
}

function getEbolaMsgList($category){
	global $DB_dir;
	$url = $DB_dir."GetEbolaMsgList.php?cat=$category";
	$result = doCurl($url);
	return $result;
}

function GetEbolaMsgListbyUser($category, $ph, $dlvid){
	global $DB_dir;
	$url = $DB_dir."GetEbolaMsgListbyUser.php?cat=$category&ph=$ph&dlvid=$dlvid";
	$result = doCurl($url);
	return $result;
}

function incPlayedAppendUser($ph, $msgid){
	global $DB_dir;
	global $MessageLanguage;
	global $callid;
	$url = $DB_dir."markEbolaMsgPlayed.php?msgid=$msgid&ph=$ph&lang=$MessageLanguage&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function hasUserHeardThisMsg($ph, $msgid){
	global $DB_dir;
	$url = $DB_dir."hasUserHeardMsg.php?msgid=$msgid&ph=$ph";
	$result = doCurl($url);
	return $result;
}

function PathToMsgID($dir, $file){
	global $DB_dir;
	$url = $DB_dir."GetEbolaMsgID.php?dir=$dir&file=$file";
	$result = doCurl($url);
	return $result;
}

function MsgIDToPath($id){
	global $DB_dir;
	$url = $DB_dir."GetEbolaIDToPath.php?dlvid=$id";
	$result = doCurl($url);
	return $result;
}

function getMaxEbID_LessThanID($id, $category){
	global $DB_dir;
	$url = $DB_dir."Max_EbID_LessThanID.php?ID=$id&cat=$category";
	$result = doCurl($url);
	return $result;
}
function getMinEbID_GreaterThanID($id, $category){
	global $DB_dir;
	$url = $DB_dir."Min_EbID_GreaterThanID.php?ID=$id&cat=$category";
	$result = doCurl($url);
	return $result;
}
function incNoOfTimesPlayedEb($id){
	global $DB_dir;
	$url = $DB_dir."Inc_NoOfTimesPlayedEb.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function playNextEbMsg(){
	global $DB_dir;
	//http://127.0.0.1/wa/DBScripts/Get_EbMsgNameAndDir.php?id=1
	// General\1.wav
}
//-------------------------------
//-------------------------------
function getMaxAdID(){
	global $DB_dir;
	$url = $DB_dir."Max_AdID.php";
	$result = doCurl($url);
	return $result;
}
function getMaxAdID_LessThanID($id){
	global $DB_dir;
	$url = $DB_dir."Max_AdID_LessThanID.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function getMinAdID_GreaterThanID($id){
	global $DB_dir;
	$url = $DB_dir."Min_AdID_GreaterThanID.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function incNoOfTimesPlayed($id){
	global $DB_dir;
	$url = $DB_dir."Inc_NoOfTimesPlayed.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function newAdsPlayedbyPhNo($adid, $phno){
	global $DB_dir;
	$url = $DB_dir."New_Ads_played_by_phno.php?adid=$adid&phno=$phno";
	$result = doCurl($url);
	return $result;
}
function newAdsPlayedbyCallID($adid, $cid){
	global $DB_dir;
	$url = $DB_dir."New_Ads_played_by_CallID.php?adid=$adid&cid=$cid";
	$result = doCurl($url);
	return $result;
}
//-------------------------------
//&&$$** Added Functions
// functions to encode, decode, store phone numbers
function PhToKeyAndStore($phno, $sender){
	global $DB_dir;
	global $tester;
	////fwrite($tester,"hello". $phno." in PhToKeyAndStore ..asd\n");
	$url = $DB_dir."insertNewPhByMatchingFromOldTable.php?sender=$sender&ph=".$phno;
	$result = doCurl($url);
	////fwrite($tester, $url."\n");
	////fwrite($tester, "result".$result."\n");
	return $result;
}

function PhToKey($ph){
	global $DB_dir;
	$url = $DB_dir."phToKey.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function KeyToPh($key){
	global $DB_dir;
	$url = $DB_dir."keyToPh.php?key=$key";
	$result = doCurl($url);
	return $result;
}

// Phone Directory Functions
function getPhDir(){
	global $userid;
	global $DB_dir;
	$url = $DB_dir."getPhDir.php?user=$userid";
	$result = doCurl($url);
	return $result;
}

function updatePhDir($phoneNumber){
	global $userid;
	global $DB_dir;
	$url = $DB_dir."updatePhDir.php?user=$userid&friend=$phoneNumber";
	$result = doCurl($url);
	return $result;
}

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// Misc. Functions ////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
function sendUnSubSMS($ph){
	global $fh;
	global $callid;
	$To_Whom = $ph;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Sending an SMS to: ".$To_Whom." to inform him about the unsub number.");
	//$message = doCurl("http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=Abhi%2004238333112%20per%20call%20ker%20kay%20baghair%20kisi%20intizar%20kay%20Mian%20Mithoo%20say%20baat%20kerain%20(Iss%20call%20ki%20qemat%20aap%20ko%20ada%20kerni%20ho%20gi).&number=$To_Whom");
	$message = doCurl("http://api.tropo.com/1.0/sessions?action=create&token=1950c84caa3aa1489428e3946049fdfa96c92736612b6abb738b004d0c8f12c253ae0bdec48ab3828dd1d89d&msg=Abhi%2004238333112%20per%20call%20ker%20kay%20baghair%20kisi%20intizar%20kay%20Mian%20Mithoo%20say%20baat%20kerain%20(Iss%20call%20ki%20qemat%20aap%20ko%20ada%20kerni%20ho%20gi).&ph=$To_Whom");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "System returned: ".$message);

}

function sendLogs(){
	global $DB_dir;
	global $callid;
	global $logEntry;
	global $tester;//change testing

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called.");

	$LogScript = $DB_dir."createLogs.php";

	$arr = explode('^', chunk_split($logEntry, 1000, '^'));		// Send logs in chunks of 1000 characters
	$i=0;
	$len = count($arr);
	while($i < $len){
		$datatopost = array (
			"callid" => $callid,
			"data" => $arr[$i]
		);

		$ch = curl_init ($LogScript);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $datatopost);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$returndata = curl_exec ($ch);

		$i++;
	}
}

function checkIfPhNoValid($phno){

	$phno = rtrim($phno, "*");
	$phno = ltrim($phno, "*");
	$phno = rtrim($phno, "#");

	if(sizeof($phno) < 11 || sizeof($phNo) > 15)
		return false;

	$phno = str_replace("+", "00", $phno);

	if(is_numeric($phno)){
		if ($phno[0] == 0 || $phno[0] == "0") {
			return true;
		}
		return true;
	}
	return false;
}

function conditionPhNo($phno, $type){
	global $useridUnEnc;
	global $countryCode;
	$returnNumber = $phno;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called with ph: $phno and type: $type");

	if($type == "Missed_Call" || $type == "EMissed_Call" || $type == "AMissed_Call" || $type == "MMissed_Call" || $type == "BMissed_Call" || $type == "Unsubsidized" || $type == "FCMissed_Call"){
		if(substr($returnNumber, 0, 1) == '1'){										// 1 got prepended by mistake
			$returnNumber = substr($returnNumber, 1, strlen($returnNumber)-1);		// remove it
		}
		else if(strlen($phno) <= 10){						//local US	e.g. 4122677909
			$returnNumber = "1".$phno;						// 1 is to say that missed call is coming in from US
		}
		else{												// International
			$returnNumber = $phno;
		}
	}
	else if($type == "Delivery" || $type == "EDelivery" || $type == "BDelivery" || $type == "ADelivery" || $type == "MDelivery" || $type == "FCDelivery"){
		$returnNumber = $phno;
		if(substr($returnNumber, 0, 2) == '00'){			// A non-US sender entered an Intl. number with country code
			$returnNumber = ltrim($returnNumber, 0);
		}
		else if(substr($returnNumber, 0, 3) == '011'){		// A US sender entered an Intl. number with country code
			$returnNumber = substr($returnNumber, 3, strlen($returnNumber)-3);
		}
		else if(substr($returnNumber, 0, 1) == '0'){					// A non-US sender entered a local number
			$returnNumber = $returnNumber;	//$countryCode . ltrim($returnNumber, 0);		// Prepend the country code of the sender
		}
		else if((strlen($returnNumber)==9) && $countryCode == '224'){	// A Guinean sender entered a local number without a 0 prefix
			$returnNumber = $countryCode . ltrim($returnNumber, 0);		// Prepend the country code of Guinea
		}
		else if((strlen($useridUnEnc) == 11) && (substr($useridUnEnc, 0, 1) == 1) && (strlen($returnNumber)==10)){	// A US sender entered a 10-dig number without country code -> Assmue it is a US number
			$returnNumber = $returnNumber; //"1".$returnNumber;
		}
		phoneNumBeforeAndAfterConditioning($phno, $returnNumber, $type, $useridUnEnc);
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is returning: $returnNumber");
	return $returnNumber;
}

function createLogFile($id){
	global $logFilePath;
	//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called with id: $id");
	$logFilePathLocal = $logFilePath . ($id%1000);
	if(!file_exists($logFilePathLocal)){ 
		mkdir($logFilePathLocal);
	}
	$logFile = $logFilePathLocal."\\".$id.".txt";

	// if(!file_exists($logFilePath."\\".date('Y'))){ 
	// 	mkdir($logFilePath."\\".date('Y'));
	// } 
	// if(!file_exists($logFilePath."\\".date('Y')."\\".date('m'))){ 
	// 	mkdir($logFilePath."\\".date('Y')."\\".date('m'));
	// } 
	// if(!file_exists($logFilePath."\\".date('Y')."\\".date('m')."\\".date('d'))){ 
	// 	mkdir($logFilePath."\\".date('Y')."\\".date('m')."\\".date('d'));
	// }
	
	// $now = new DateTime;
	// $timestamp = $now->format('Y-m-d_H-i-s');
	// $logFile = $logFilePath."\\".$now->format('Y')."\\".$now->format('m')."\\".$now->format('d')."\\log_".$timestamp."_".$id.".txt"; // Give a caller ID based name

	$handle = fopen($logFile, 'a+');
	/*echo "HAH!:";
	var_dump($handle);*/
	return $handle;
}


function schedSMS($phno, $type){
	global $callid;
	global $userid;
	global $SystemLanguage;
	global $MessageLanguage;
    global $channel;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called with ph: $phno and type: $type");
	if($type=='Del'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20call%20kerain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
	}
	else if($type=='JDel'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSJDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
	}
	else if($type=='EDel'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSEDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
	}
	else if($type=='BDel'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSBDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
	}
	else if($type=='ADel'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSADelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
	}
    else if($type=='MDel'){
		//$url = "http://65.98.91.179/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
        $smsreq = makeNewReq('0', '0', $callid, "SMSMDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
    }
    else if($type=='ForecariahDel'){
        $smsreq = makeNewReq('0', '0', $callid, "SMSFCDelivery", $userid, "WPending", $SystemLanguage, $MessageLanguage, $channel);
    }

	//$result = doCurl($url);
	//return $result;
}

function doCurl($url){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$result = curl_exec($ch);
		curl_close($ch);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with url: $url, is returning: $result");
	return $result;
}

function doCurlPost($url, $postVars){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);

	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	curl_close($ch);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with url: $url, is returning: $result");

	return $result;
}

function getFilePath($fileName, $pathOnly = "FALSE")
{
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");

	$fname = explode('.', $fileName);

	$FilePath = ($fname[0] - ($fname[0] % 1000));		// rounding down to the nearest 1000

	$File = $FilePath."/".$fileName;
	if($pathOnly == "TRUE"){
		$File = $FilePath . "/";
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with params: $fileName, $pathOnly, is returning: $File");

	return $File;
}

function createFilePath($filePath,$fileName, $pathOnly = "FALSE")
{
	global $tester;

	$fname = explode('.', $fileName);
	$FilePathNew = $filePath.($fname[0] - ($fname[0] % 1000));		// rounding down to the nearest 1000
	//////fwrite($tester,$fname[0]." path of file.\n");

	if( is_dir($FilePathNew) === false )
	{
	    mkdir($FilePathNew);
	}
	$File = $FilePathNew."\\".$fileName;
	if($pathOnly == "TRUE"){
		$File = $FilePathNew . "\\";
	}
	return $File;
}


////////////////////////////////////////////// Free Switch lib //////////////////////


function event_socket_create($host, $port, $password) {
	global $fp;

   $fp = fsockopen($host, $port, $errno, $errdesc)
     or die("Connection to $host failed");
   socket_set_blocking($fp,false);

   if ($fp) {
       while (!feof($fp)) {
          $buffer = fgets($fp, 1024);
          usleep(50); //allow time for reponse
          if (trim($buffer) == "Content-Type: auth/request") {
             fputs($fp, "auth $password\n\n");
             break;
          }
       }
       return $fp;
    }
    else {
        return false;
    }
}

function event_socket_request($fp, $cmd) {

    if ($fp) {

        fputs($fp, $cmd."\n\n");
        usleep(50); //allow time for reponse

        $response = "";
        $i = 0;
        $contentlength = 0;
        while (!feof($fp)) {
           $buffer = fgets($fp, 4096);
           if ($contentlength > 0) {
              $response .= $buffer;
           }

           if ($contentlength == 0) { //if contentlenght is already don't process again
               if (strlen(trim($buffer)) > 0) { //run only if buffer has content
                   $temparray = explode(":", trim($buffer));
                   if ($temparray[0] == "Content-Length") {
                      $contentlength = trim($temparray[1]);
                   }
               }
           }

           usleep(50); //allow time for reponse

           //optional because of script timeout //don't let while loop become endless
           if ($i > 100000) { break; }

           if ($contentlength > 0) { //is contentlength set
               //stop reading if all content has been read.
               if (strlen($response) >= $contentlength) {
                  break;
               }
           }
           $i++;
        }

        return $response;
    }
    else {
      echo "no handle";
    }
}

function hangupFT(){
	global $FreeSwitch;
	global $uuid;
	global $fp;

	if($FreeSwitch == "false"){
		hangup();
	}
	else{
		if(isset($_REQUEST["uuid"])){			# code...
			$cmd = "api lua hangup.lua ".$uuid;
			$response = event_socket_request($fp, $cmd);
			fclose($fp);
		}
	}
}

function makechoices($choices){
	//types seen "[1 DIGITS], *" , "[1 DIGITS]" , "sometext(1,sometext)"
	$fchoices = "";
	if($choices[0] == '[')
	{
		//tokenizing all possible choices
		$pchoices = explode(",",$choices);
		$i = 1;

		while($pchoices[0][$i] != ' ' && $i < strlen($pchoices[0]))
		{
			$fchoices .= $pchoices[0][$i];
			 ++$i;
		}

		if(strlen($fchoices ) > 1)
		{
			$fchoices=str_replace("-","",$fchoices);
		}
		$j=1;

		while($j<count($pchoices))
		{
			$fchoices .= trim($pchoices[$j]);
			++$j;
		}

	}
	else
	{
		$i = 0;
		while($i< strlen($choices))
		{
			$j = $i;
			$cchoices = "";//choice in context


			while($choices[$j] != '(')
			{
				$j++;

			}
			$j++;

			while($choices[$j] != ')')
			{
				$cchoices .= $choices[$j];
				$j++;

			}
			$j++;
			$i=$j;
			$pchoices = explode(",",$cchoices);
			$fchoices .= $pchoices[0];


		}

	}

	return $fchoices;
}

//to make regex for valid input freeswitch like [1 digits], * => \\d+ or 1234* => [1234]
function makeValidINput($choices,$fchoices)
{
	if($choices[0] == '[')
	{
		return "d" ;
	}
	else
	{
		$valid = '[';
		$i = 0;
		while($i<strlen($fchoices))
		{
			if($fchoices[$i]!='*' && $fchoices[$i]!='#' )
			{
				$valid .= $fchoices[$i];
			}
			$i++;
		}
		$valid .= ']';
		return $valid;
	}
}
//fchoices choice for freeswitch
//making terms
function makeTerminators($fchoices,$terms)
{
	$termsin = "";
	$i = 0;
	$b = 0;
	while($i<strlen($fchoices))
	{
		if($fchoices[$i]=='*' || $fchoices[$i]=='#' )
		{
			$b = 1;
			$termsin .= $fchoices[$i];
		}
		$i++;
	}
	if($terms == "@" && $b == 1)
	{
		return $termsin;
	}
	else
	{
		$terms.=$termsin;
		return $terms;
	}
}
//fchoice return by freeswitch map corrosponding class, 1 for notify
function mapChoice($choices,$fchoice)
{
	if($choices[0] == '[')
	{
		return $fchoice;
	}
	else
	{
		$i = 0;
		while($i< strlen($choices))
		{
			$j = $i;
			$cchoices = "";//choice in context


			while($choices[$j] != '(')
			{
				$j++;

			}
			$j++;

			while($choices[$j] != ')')
			{
				$cchoices .= $choices[$j];
				$j++;

			}
			$j++;
			$i=$j;
			$pchoices = explode(",",$cchoices);
			if($fchoice == $pchoices[0])
			{
				return $pchoices[1];
			}



		}

		return $fchoice;

	}
}

function calculateMaxDigits($choices,$fchoice)
{
	if($choices[0]=="[")
	{
		$i= strpos($choices,'-');
		if( $i !== false )
		{
			$subchoice = "";//the part 9-14 in [9-14 digits]
			$i=1;

			while($choices[$i]=="-" || is_numeric($choices[$i]))
			{

				$subchoice .= $choices[$i];
				$i++;

			}
			$subchoice = explode("-",$subchoice);
			$max=(int)($subchoice[1]);

		}
		else
		{
			$subchoice = "";//the part 9 in [9 digits]
			$i=1;
			while( is_numeric($choices[$i]))
			{
				$subchoice .= $choices[$i];
				$i++;
			}
			$max=(int)($subchoice);
		}
		return $max;
	}
	else
	{

		return 1;//in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
	}

}


function calculateMinDigits($choices,$fchoice)
{
	if($choices[0]=="[")
	{
		$i= strpos($choices,'-');
		if( $i !== false )
		{
			$subchoice = "";//the part 9-14 in [9-14 digits]
			$i=1;

			while($choices[$i]=="-" || is_numeric($choices[$i]))
			{

				$subchoice .= $choices[$i];
				$i++;

			}
			$subchoice = explode("-",$subchoice);
			$min=(int)($subchoice[0]);

		}
		else
		{
			$subchoice = "";//the part 9 in [9 digits]
			$i=1;
			while( is_numeric($choices[$i]))
			{
				$subchoice .= $choices[$i];
				$i++;
			}
			$min=(int)($subchoice);
		}
		return $min;
	}
	else
	{
		return 1;//in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
	}
}
//to handle if invalid key is entered for freeswitch
//make sure to make proper changes before integrating it to poly
function invalid($onBadCoice)
{
	global $Polly_prompts_dir;

	if ( $onBadCoice == "keysbadChoiceFCN" )
	{
		return $Polly_prompts_dir."Wrongbutton.wav";
	}
}
//to handle if timeout occured for freeswitch
function onTimeOut($onTimeout)
{
	global $Polly_prompts_dir;
	global $MVP_prompts;
	if ( $onTimeout == "keystimeOutFCN" )
	{
		return $Polly_prompts_dir."Nobutton.wav";
	}else if ( $onTimeout == "ReMkeystimeOutFCN" )
	{
		return $MVP_prompts."Nobutton.wav";
	}
}
function gatherInputTropo($toBeSaid, $params)
{
	$repeat = "TRUE";
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));
	while($repeat == "TRUE"){
		$repeat = "FALSE";
		$result = ask($toBeSaid, $params);
	//	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "The ask() in " . __FUNCTION__ . " is now complete.");
		if($result->value == "*"){	// pause the system
			//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was paused by pressing *.");
			ask("",
			array(	"choices" => "[1 DIGITS], *",
					"mode" => 'dtmf',
					"repeat" => 1,
					"bargein" => true,
					"timeout" => 300,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			$repeat = "TRUE";
			//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " resumed.");
		}
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($result->value) . ", name: " . ($result->name) . ", attempt: " . ($result->attempt) . ", choice: " . ($result->choice));
	return $result;
}
function gatherTnputFreeSwitch($toBeSaid,$invalidFS,$mindigitsFS,$maxdigitsFS,$maxattemptsFS,$timeoutFS,$bargein,$termFS,$validInput,$onTimeOutFS,$interdigitTimeout)
{

	global $uuid;
	global $fp;
	global $tester;//change testing
	//////fwrite($tester, "in gatherTnputFreeSwitch  .\n");

	/* uncomment to test what the params you are getting
	fwrite($fh, "invalidFS".""." ".$invalidFS."\n");
	fwrite($fh, "mindigitsFS".""." ".$mindigitsFS."\n");
	fwrite($fh, "maxdigitsFS".""." ".$maxdigitsFS."\n");
	fwrite($fh, "maxattemptsFS".""." ".$maxattemptsFS."\n");
	fwrite($fh, "timeoutFS".""." ".$timeoutFS."\n");
	fwrite($fh, "bargein".""." ".$bargein."\n");
	fwrite($fh, "termFS".""." ".$termFS."\n");
	fwrite($fh, "validInput".""." ".$validInput."\n");
	fwrite($fh, "onTimeOutFS".""." ".$onTimeOutFS."\n");
	fwrite($fh, "interdigitTimeout".""." ".$interdigitTimeout."\n");
	*/
	$output = (object) array('name' => 'choice', 'value' => '');
	$repeat = "TRUE";

	$kaho = "file_string://";
		$s=preg_split('/[ \n]/', $toBeSaid);
        for($i=0;$i<count($s);$i++)
        {
        	$j = 0;
        	if($s[0]=="")
        	{
        		$j = 1;
        	}
        	if($i>$j)
        	{
        		$kaho .= "!".$s[$i];
        	}
        	else
        	{
        		$kaho .= $s[$i];
        	}

        }


	//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));
	while($repeat == "TRUE"){
		$repeat = "FALSE";
		$cmd = "api lua askGather.lua ".$uuid." ".$kaho." ".$invalidFS." ".$mindigitsFS." ".$maxdigitsFS." ".$maxattemptsFS." ".$timeoutFS." ".$termFS." ".$validInput." ".$onTimeOutFS." ".$interdigitTimeout;
		//////fwrite($tester,$cmd." record command filepath .\n");
		$response = event_socket_request($fp, $cmd);
		////fwrite($tester,$response." reeesponse .\n");
		if(substr($response, 1)){
			$val = substr($response, 1);
			if($val[0]==' ' || $val[0]=='-'  )
			{
				$output->value= $val[1];
			}
			elseif($val[0]=='_')
			{
				$i = 1;
				while( $i < strlen($val))
				{
					$output->value .= $val[$i];
					$i++;
				}

			}
			else
			{
				$output->name = "not_Good_timeout_or_invalid";
				$output->value= "-";
			}
		}
		else{
			$output->name = "not_Good_timeout_or_invalid";
			$output->value= "-";
		}
	//	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "The ask() in " . __FUNCTION__ . " is now complete.");
		if($output && $output->value == "*"){	// pause the system
			//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was paused by pressing *.");
			//pause system
			pauseFT();
			$repeat = "TRUE";
			//writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " resumed.");
		}
	}
	////fwrite($tester,$output->name." nam ".$output->value." val .\n");
	isThisCallActive();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($output->value) . ", name: " . ($output->name) );
	return $output;
}

function gatherInput($toBeSaid, $params){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));

	global $Polly_prompts_dir;
	global $Silence;
	global $FreeSwitch;


	if($FreeSwitch == "false"){

		return  gatherInputTropo($toBeSaid, $params);

	}
	else
	{
		if(isThisCallActive()=="true")
		{

		    //making parameters
		    //parameters that should always be in ask array no matter what..choices...bargein..timeout
		    $choices=$params['choices'];//choices given by user for tropo
			$fchoices=makechoices($choices);//fchoices for freeswitch in format 123 or 1234* or 1*#..//fchoices made out of choices given by user,for freeswitch
			$mindigitsFS=calculateMinDigits($choices,$fchoices);
			$maxdigitsFS=calculateMaxDigits($choices,$fchoices);
			$bargein=$params['bargein'];
			$timeoutFS=$params['timeout'] * 1000;//to convert to millisecs as freeswitch timeout is in millisecs
			$validInput=makeValidINput($choices,$fchoices);

			//parameters that should may be in ask array..repeat || attempt...terminator..onBadChoice..onTimeout
			$termFS = "@";//default value menas no terminator
			if(checkifexists('terminator',$params)==true )
				$termFS = $params['terminator'];//intialized with passed parameter terminator
			$termFS= makeTerminators($fchoices,$termFS);//making it for freeswitch like if * is not in term but in choice it will put it in terminator
			$maxattemptsFS=0;//default value
			if(checkifexists('repeat',$params)==true )
				$maxattemptsFS=$params['repeat'] + 1;//intialized with passed parameter repeat
			if(checkifexists('attempts',$params)==true )
				$maxattemptsFS=$params['attempts'];//intialized with passed parameter attempts
			$invalidFS=$Polly_prompts_dir.$Silence;//default value that is silence
			if(checkifexists('onBadChoice',$params)==true )
				$invalidFS=invalid($params['onBadChoice']);//intialized with prompt corrosponding to the onBadChoice value
			$onTimeOutFS="-";//default value that is silence
			if(checkifexists('onTimeout',$params)==true )
				$onTimeOutFS=onTimeOut($params['onTimeout']);//intialized with prompt corrosponding to the onTimeout value
			$interdigitTimeout=$timeoutFS;//default value is equal to timeout in freeswitch
			if(checkifexists('interdigitTimeout',$params)==true )
				$interdigitTimeout=$params['interdigitTimeout']*1000;//intialized with passed parameter interdigitTimeout
			return gatherTnputFreeSwitch($toBeSaid,$invalidFS,$mindigitsFS,$maxdigitsFS,$maxattemptsFS,$timeoutFS,$bargein,$termFS,$validInput,$onTimeOutFS,$interdigitTimeout);
		}
	}
}
//checking if  parameter exists in array
function checkifexists($parameter,$array){

	if(array_key_exists($parameter, $array)) {
		return true;
	}
	//else return false if parameter doesnt exist in array
	return false;

}
function recordAudio($toBeSaid, $params){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Record Params: ".$params);

    global $FreeSwitch;
    global $uuid;
    global $fp;
    global $Feedback_Dir;
    global $CallRecordings_Dir;
    global $tester;
    global $scripts_dir;
    global $CallRecordings_Dir_Baang;
    global $UserIntro_Dir_Baang;
    global $Feedback_Dir_Baang;
    global $PQRecs;
    global $PQPrompts;
    global $PQScripts;

	////fwrite($tester, "Inside recordAudio with prompt = ".$toBeSaid." \n  & params = ".$params."\n");

    $recid = "";
    $result = "";
    $filepathFS= "";
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));
    if($FreeSwitch == "false")
    {
        $result = record($toBeSaid,$params);

    }
	else
	{
   	 	if(isThisCallActive()=="true")
		{
	   	 	if(checkifexists('silenceTimeout',$params)==true){
	            $silTimeout= $params['silenceTimeout'];

	        }
	        else{
	        	//setting default silence timeout
	        	$silTimeout=5;
	        }
	        if(checkifexists('maxTime',$params)==true){
	            $maxTime= $params['maxTime'];

	        }
	        else{
	        	//setting default maximum time
	        	$maxTime=30;
	        }
	        $rec_feed = 0;
	        $recordURI = $params['recordURI'];
	        ////fwrite($tester,$recordURI." record--------------9248 .\n");
	       	if( strpos($recordURI, 'process_feedback') !== false )
	       		{
	       			$parameterarray=explode("&", explode("?", $recordURI)[1]);

	       			$fbid=explode("=", $parameterarray[0])[1];

	       			if(strpos($recordURI, 'baang') !== false){
	       				$filepathFS=$Feedback_Dir_Baang;
				        $userid=explode("=", $parameterarray[2])[1];
				        $rec_id=explode("=", $parameterarray[1])[1];
				        $filepathFS = createFilePath($filepathFS,$fbid.".wav",TRUE);
				        $filepathFS .= "Feedback-".$fbid."-u-".$userid."-r-".$rec_id ;
	       			}else{
		       			$filepathFS=$Feedback_Dir;
				        $filepathFS = createFilePath($filepathFS,$fbid.".wav",TRUE);
				        $filepathFS .= "Feedback-".$fbid."-";
		       			$i = strpos($recordURI, '=');
				        $i = $i +1;
				        $fbid = "";
				        ////fwrite($tester,$i." feedback  fbid i .\n");
				        ////fwrite($tester,$filepathFS." feedback filepath  .\n");

					    while( $recordURI[$i] != '&')
				        {
				        	////fwrite($tester," times .\n");
				        	$fbid .= $recordURI[$i];
				        	$i = $i +1;
				        }
				        while( $recordURI[$i] != '=')
				        {
				        	////fwrite($tester," finding second = pos i .\n");

				        	$i = $i +1;
				        }
				        $i = $i +1;
				        ////fwrite($tester,$i." feedback  fbtype i .\n");
				        ////fwrite($tester,$filepathFS." feedback filepath  .\n");
				        while(  $i < strlen($recordURI))
				        {
				        	////fwrite($tester," times .\n");
				        	$filepathFS .= $recordURI[$i];
				        	$i = $i +1;
				        }
	       			}

			        $filepathFS .= ".wav";
			        $rec_feed = 1;

	       		}
	        else if( strpos($recordURI, 'process_recording') !== false )
	        	{
	        		$rec_feed = 0;
	       			$parameterarray=explode("&", explode("?", $recordURI)[1]);
	       			$recid=explode("=", $parameterarray[0])[1];

	       			if(strpos($recordURI, 'name') !== false){
	       				$filepathFS=$UserIntro_Dir_Baang;
				        $filepathFS .= "intro-".$recid.".wav";
		        		$rec_feed = 6;
	       			}else if(strpos($recordURI, 'baang') !== false){
	       				$rec_feed=2;
	       				$filepathFS=$CallRecordings_Dir_Baang;
				        $filepathFS = createFilePath($filepathFS,$recid.".wav",TRUE);
				        $filepathFS .= "Rec-".$recid.".wav";
	       			}else{
		        		$filepathFS=$CallRecordings_Dir;
				        $filepathFS = createFilePath($filepathFS,$recid.".wav",TRUE);
				        $filepathFS .= "s-".$recid.".wav";
		        		$rec_feed = 0;
	       			}

	       			/*
	        		$i = strpos($recordURI, '=');
			        $i = $i +1;
			        ////fwrite($tester,$i." record filepath i .\n");
			        ////fwrite($tester,$filepathFS." record filepath  .\n");

				    while( $i < strlen($recordURI) & $recordURI[$i] !=& )
			        {
			        	////fwrite($tester," idididi .\n");
			        	$recid .= $recordURI[$i];
			        	$i = $i +1;
			        }
			        */

			       // $rec_feed = 0;
	        	}
	        else if( strpos($recordURI, 'save_recording') !== false )
	        	{
	        		// "save_recording.php?type=".$type."&uid=".$userid."&record_id=".$record_id."&call_id=".$callid."&suno_abbu=true";

	        		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " handling save_recording");

	       			$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$type           = explode("=", $parameterarray[0])[1];
	       			$uid            = explode("=", $parameterarray[1])[1];
	       			$record_id      = explode("=", $parameterarray[2])[1];

       				$rec_feed   = 6;

       				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " type = $type , record_id = $record_id");

			        if($type == "request"){ // QU-5972-3.wav

			        	global $SA_requests;

			        	$filepathFS = $SA_requests. $record_id.".wav";

					}else if($type == "name"){ // U1.wav

						global $MVP_recordings, $userid;

						$filepathFS = $MVP_recordings."U".$userid.".wav";

					}else if($type == "story"){ //QU-5972-3-1.wav

						global $MVP_Story_Recordings;

						$filepathFS = $MVP_Story_Recordings.$record_id.".wav";


					}else if($type == "comment"){

						global $MVP_comments;

						$filepathFS = $MVP_comments."C-".$record_id.".wav";

				   	}else if($type == "fb") {

				   		global $SA_fb_dir;

				        $filepathFS = createFilePath($SA_fb_dir, $record_id.".wav", TRUE);
		   				$rec_feed   = 6;
						$filepathFS .= $record_id . ".wav";
	        		}

	        		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " filepathFS = $filepathFS");
	        	}
	        else if( strpos($recordURI, 'process_FriendNamerecording') !== false )
	        	{

	       			$parameterarray=explode("&", explode("?", $recordURI)[1]);
	       			$reqid=explode("=", $parameterarray[0])[1];
	       			$userid=explode("-", $reqid)[0];
						global $FriendName_Dir;
						// global $testerpolly;
						$filepathFS= $FriendName_Dir;//"C:/xampp/htdocs/wa/Praat/FriendNames/";
				        $filepathFS = createFilePath($filepathFS,$userid.".wav",TRUE);
	       				$rec_feed=6;
						$filepathFS .= $reqid.".wav";
						//fwrite($testerpolly, "friend name record uri: ".$filepathFS."\n");
        		}
			else if( strpos($recordURI, 'process_UserNamerecording') !== false )
        		{
	   			$parameterarray=explode("&", explode("?", $recordURI)[1]);
	   			$callid=explode("=", $parameterarray[0])[1];
					global $SenderName_Dir;
					// global $testerpolly;
					$filepathFS= $SenderName_Dir ;//"C:/xampp/htdocs/wa/Praat/UserNames/";
			        $filepathFS = createFilePath($filepathFS,$callid.".wav",TRUE);
	   				$rec_feed=6;
					$filepathFS .= "UserName-" . $callid . ".wav";
					//fwrite($testerpolly, "user naem path :".$filepathFS."\n");
        		}
        	else if( strpos($recordURI, 'record_survey') !== false )
        		{
					global $userid;
					global $language_demographic_dir;
					global $profession_demographic_dir;
					global $location_demographic_dir;
					global $disabled_demographic_dir;

					if (strpos($recordURI, "type=LANGUAGE") !== false) {
						$filepathFS = $language_demographic_dir;
					} else if (strpos($recordURI, "type=PROFESSION") !== false) {
						$filepathFS = $profession_demographic_dir;
					} else if (strpos($recordURI, "type=LOCATION") !== false) {
						$filepathFS = $location_demographic_dir;
					}else if (strpos($recordURI, "type=DISABLED") !== false) {
						$filepathFS = $disabled_demographic_dir;
					}
			        $filepathFS = createFilePath($filepathFS,$userid.".wav",TRUE);
	   				$rec_feed = 6;
					$filepathFS .= $userid . ".wav";
        		}
        	else if( strpos($recordURI, 'remt1_feedback') !== false )
        		{
        			global $ReMT1_fb_dir;

					$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$record_id      = explode("=", $parameterarray[0])[1];

			        $filepathFS = createFilePath($ReMT1_fb_dir, $record_id.".wav", TRUE);
	   				$rec_feed   = 6;
					$filepathFS .= $record_id . ".wav";
        		}
        	else if( strpos($recordURI, 'remt2_feedback') !== false )
        		{
        			global $ReMT2_fb_dir;

					$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$record_id      = explode("=", $parameterarray[0])[1];

			        $filepathFS = createFilePath($ReMT2_fb_dir, $record_id.".wav", TRUE);
	   				$rec_feed   = 6;
					$filepathFS .= $record_id . ".wav";
        		}

        	else if( strpos($recordURI, 'API/api/add/main_feedback') !== false )
        		{
        			global $MVP_feedbacks;

					$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$record_id      = explode("=", $parameterarray[0])[1];

			        $rec_feed   = 6;
					$filepathFS = $MVP_feedbacks.$record_id . ".wav";
        		}
        	else if( strpos($recordURI, 'API/api/add/question_feedback') !== false )
        		{
        			global $usercomments;

					$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$record_id      = explode("=", $parameterarray[0])[1];

	   				$rec_feed   = 6;
					$filepathFS = $usercomments.$record_id . ".wav";
        		}
        	else if( strpos($recordURI, 'API/api/add/question_user') !== false )
        		{
        			global $MVP_recordings;

					$parameterarray = explode("&", explode("?", $recordURI)[1]);
	       			$record_id      = explode("=", $parameterarray[0])[1];

	   				$rec_feed   = 6;
					$filepathFS = $MVP_recordings."Q".$record_id . ".wav";
        		}
        	else if( strpos($recordURI, 'API/api/username') !== false )
        		{
        			global $MVP_recordings, $userid;

	   				$rec_feed   = 6;
					$filepathFS = $MVP_recordings."U".$userid.".wav";
        		}


	        $kaho = "file_string://";
			$s=preg_split('/[ \n]/', $toBeSaid);
	        for($i=0;$i<count($s);$i++)
	        {
	        	$j = 0;
	        	if($s[0]=="")
	        	{
	        		$j = 1;
	        	}
	        	if($i>$j)
	        	{
	        		$kaho .= "!".$s[$i];
	        	}
	        	else
	        	{
	        		$kaho .= $s[$i];
	        	}

	        }
	        ////fwrite($tester,$filepathFS." record --filepath-- .\n");
	        $filepathFS=str_replace("\\", "_", $filepathFS);
	        $kaho=rtrim($kaho,"!");
	       	$cmd = "api lua record.lua ".$uuid." ".$kaho." ".$filepathFS." ".$maxTime." ".$silTimeout; // some suggest using 500 as the threshold of silence
	    	$result = event_socket_request($fp, $cmd);
	    	isThisCallActive();
    		$filepathFS=str_replace("_", "\\", $filepathFS);
    		correctWavFT($filepathFS);

	    	if($rec_feed === 0)
	    	{
	    		////fwrite($tester,$rec_feed." processAudFile is on.\n");
	    		//$filepathFS=str_replace("_", "\\", $filepathFS);
	    		///correctWavFT($filepathFS);
	    		$res = doCurl($scripts_dir."processAudFile.php?path=s-$recid".".wav");
	    		////fwrite($tester,$scripts_dir."processAudFile.php?path=s-$recid".".wav"." curl request .\n");
	    		////fwrite($tester,$resutl." curl request .\n");
	    	}else if($rec_feed === 2 || $rec_feed === 5 || $rec_feed === 6){
	    	//	correctWavFT($filepathFS);
	    		////fwrite($tester,$resutl." 2-. $recordURI.\n");
	    		$res = doCurl($recordURI);
	    	}else if($rec_feed === 1){
	    	///	$filepathFS=str_replace("_", "\\", $filepathFS);
	    	//	correctWavFT($filepathFS);
	    	}

	    	////fwrite($tester,$result." record command result .\n");
    	}
    }

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning.");
    return $result;
}

function correctWavFT($filepath){
	global $FreeSwitch;


	if($FreeSwitch == "false"){
		// NOP
	}
	else{
		$rep = file_get_contents($filepath);
		$rep[20] = "\x01";
		$rep[21] = "\x00";
		file_put_contents($filepath, $rep);
	}
}

function askFT($toBeSaid, $choices, $mode, $repeat, $bargein, $timeout,$hanguppr, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $termFS, $invalidFS){
	global $FreeSwitch;
	global $uuid;
	global $fp;

	global $tester;//change testing
	////fwrite($tester, "in askft  .\n");

	$output = (object) array('name' => 'choice', 'value' => '');
	if($FreeSwitch == "false"){
		$result = ask($toBeSaid,
		array(
				"choices" => $choices,
				"mode" => $mode,
				"repeat" => $repeat,
				"bargein" => $bargein,
				"timeout" => $timeout,
				"terminator" => $termFS,
				"onHangup" => $hanguppr
			)
		);
		$output = $result;
	}
	else{
		$kaho = "file_string://";
		$s=preg_split('/[ \n]/', $toBeSaid);
        for($i=0;$i<count($s);$i++)
        {
        	$j = 0;
        	if($s[0]=="")
        	{
        		$j = 1;
        	}
        	if($i>$j)
        	{
        		$kaho .= "!".$s[$i];
        	}
        	else
        	{
        		$kaho .= $s[$i];
        	}

        }
        $kaho=rtrim($kaho,"!");

		$cmd = "api lua ask.lua ".$uuid." ".$kaho." ".$invalidFS." ".$mindigitsFS." ".$maxdigitsFS." ".$maxattemptsFS." ".$timeoutFS." ".$termFS;
		$response = event_socket_request($fp, $cmd);//here
		isThisCallActive();
		if(substr($response, 1)){
			$val = substr($response, 1);
			if($val[0]=='_'||$val[0]=='+'||$val[0]==' ')
			{
				$output->value= $val[1];
			}
			else
			{
				$output->name = "not_Good_timeout_or_invalid";
				$output->value= "-";
			}

		}
		else{

			$output->name = "not_Good_timeout_or_invalid";
			$output->value= "-";
		}

	}
	return $output;
}
function pauseFT(){
	global $tester;//change testing
	////fwrite($tester, "in pauseft  .\n");
	$choices = "[1 DIGITS], *, #" ;
	$mode = 'dtmf';
	$repeat = 1;
	$bargein = true;
	$timeout = 300;
    $hanguppr = "Prehangup";
    $mindigitsFS = 0;
    $maxdigitsFS = 1;
    $maxattemptsFS = 2;
    $timeoutFS = 300000;
    $termFS = "*#";
    $invalidFS="nothing";
    $result = askFT(
				$invalidFS,
				$choices,
				$mode,
				$repeat,
				$bargein,
				$timeout,
				$hanguppr,
				$mindigitsFS,
				$maxdigitsFS,
				$maxattemptsFS,
				$timeoutFS,
				$termFS,
				$invalidFS
				);

}
function sayInt($toBeSaid){
	global $tester;//change testing
	////fwrite($tester, "in sayint  .\n");
	if(isThisCallActive()=="true")
	{
		$choices = "[1 DIGITS], *, #" ;
		$mode = 'dtmf';
		$repeatMode = 0;
		$bargein = true;
		$timeout = 0.1;
	    $hanguppr = "Prehangup";
	    $mindigitsFS = 1;
	    $maxdigitsFS = 1;
	    $maxattemptsFS = 1;
	    $timeoutFS = 100;
	    $termFS = "*#";
	    $invalidFS="nothing";


		$repeat = "TRUE";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " about to play: " . $toBeSaid);
		while($repeat == "TRUE"){
			$repeat = "FALSE";
				$result = askFT(
					$toBeSaid,
					$choices,
					$mode,
					$repeatMode,
					$bargein,
					$timeout,
					$hanguppr,
					$mindigitsFS,
					$maxdigitsFS,
					$maxattemptsFS,
					$timeoutFS,
					$termFS,
					$invalidFS
					);

			if($result->name == 'choice'){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " prompt " . $toBeSaid . " was barged-in with " . ($result->value));
			}
			if($result->value == "*"){	// pause the system
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was paused by pressing *");
				pauseFT();
				$repeat = "TRUE";
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " resumed.");
			}
		}
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($result->value) . ", name: " . ($result->name) . ", attempt: " . ($result->attempt) . ", choice: " . ($result->choice));
		return $result;
	}
}
//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// End of Code ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
?>