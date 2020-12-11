<?php
//	--------------------------------------------------------------------------------
//	D:	-
//	--------------------------------------------------------------------------------
//	?:	-
//	--------------------------------------------------------------------------------
//	I:	-
//	--------------------------------------------------------------------------------
//	O:	-
//	--------------------------------------------------------------------------------

	class AccessCode {

	//	Private, protected and public class variables
	//	---------------------------------------------
	//	---------------------------------------------
		public $object_arr;
		public $debug = false;
		private	$deviceType	=	"" ;
		private	$SerialNo	=	"" ;

	//	Class constructor
	//	-----------------
		public function __construct( $_deviceType, $_serialNo) {
			$this->deviceType	=	$_deviceType ;
			$this->serialNo		=	$_serialNo ;
		}

	//	Class destructor
	//	----------------
		public function __destruct() {
		}

	//	Set these params to model encoding
	//	----------------------------------
		private function getEncodingPrefs() {

		//	Get the device name
//			$deviceNameArr = database::db_select("SELECT DeviceName FROM ".$GLOBALS["prefArr"]["pref_table_prefix"]."device WHERE DeviceID = ".$this->object_arr["CustomerRefDevice"]);

		//	Get leatest key from db
//			$keyPlaceholderArr = database::db_select("SELECT * FROM ".$GLOBALS["prefArr"]["pref_table_prefix"]."keys WHERE KeyDateTimeSince < '".date("Y-m-d H:i:s")."' ORDER BY KeyDateTimeSince DESC LIMIT 1");
//			if(isset($keyPlaceholderArr[0])==false) die("No keys defined in database");

		//	Define key and placeholder.
			$key = "fb65s83nF/&IWudr";
			$placeholder = "h4nc97ödja654bT&ns)d\"NMdfM";

		//	Prepare key for usage in keybuilder.
			$fixed_key2 = str_repeat($key,ceil(16/mb_strlen($key)));	//	Repeat string n-times depending from string lenght
			$fixed_key2_arr = str_split($fixed_key2,1);					//	Build an array, every char is one array value
			$fixed_key2_arr = array_slice($fixed_key2_arr,0,16);		//	Trim array to 16 values

			return array(
				"maxNumberOfSerialNumStrings"	=> 32,
				"modulo_cycle_1" 	=> 177,
				"offset_1" 			=> 44,
				"step_1" 			=> 23,
				"modulo_cycle_2" 	=> 97,
				"offset_2" 			=> 43,
				"step_2" 			=> 57,
				"placeholder" 		=> $placeholder,
				"serialNumberPanel"	=> $this->serialNo,
				"deviceName"		=> $this->deviceType,
				"fixed_order" 		=> array(10,6,7,11,1,3,2,13,12,4,9,15,8,16,14,5),	//	coding1::C12-R12
				"fixed_key" 		=> array(7,17,3,2,11,23,7,29,3,5,13,15,11,7,19,5),	//	coding1::C14-R14
				"fixed_key2" 		=> $fixed_key2_arr,									//	coding1::C13-R13; cf. "key" above
				"chr"				=> array("9","8","7","6","5","4","3","2","1","0","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"),
				"outputIndexOrder"	=> "2112112221211222"	// Sometimes outputIndex1, sometimes outputIndex2
			);
		}

	//
	//	----------------------------------
		private function getOrdFromUtf8($char) {
			return ord(utf8_decode($char));
		}

	//	Set these params to model encoding
	//	----------------------------------
		private function getBuildKey() {

		//	Get the basic encoding method params
			$prefArr = $this->getEncodingPrefs();

		//	Init some encoding vars
			$outputStr = "";
			$debugStr = "\n";
			$resultingIndex = 0;
			for($i=0;$i<=15;$i++) {
				$licenseCode[$i] = 0;
			}

		//	Populate serial number string array
		//	Cf. ExcelSheet: LicenseKey::Z4-Z32
			$serialNumArr = array();
			$serialNumArr[1] = $prefArr["serialNumberPanel"];
			$serialNumArr[2] = $prefArr["deviceName"];
			for($i=3;$i<=$prefArr["maxNumberOfSerialNumStrings"];$i++) {
				$serialNumArr[$i] = $prefArr["placeholder"];
			}

		//	Build basic encoding values
		//	Cf. ExcelSheet: Coding1::V22-AF22
			for($i=1;$i<=$prefArr["maxNumberOfSerialNumStrings"];$i++) {

			//	Calculate reverse scan flag
				$reverseScan = ($i + $resultingIndex) % 2;

				for($j=1;$j<=mb_strlen($serialNumArr[$i]);$j++) {

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? $i."\t".$j."\t" : "");

				//	Calculate input counter 1
					if(isset($inputCounter1)==true) {
						$inputCounter1 = ($inputCounter1 + $prefArr["step_1"]) % $prefArr["modulo_cycle_1"];
					} else {
						$inputCounter1 = ($prefArr["offset_1"] + $prefArr["step_1"]) % $prefArr["modulo_cycle_1"];
					}

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? $inputCounter1."\t" : "");

				//	Calculate input counter 2
					if(isset($inputCounter2)==true) {
						$inputCounter2 = ($inputCounter2 + $prefArr["step_2"]) % $prefArr["modulo_cycle_2"];
					} else {
						$inputCounter2 = ($prefArr["offset_2"] + $prefArr["step_2"]) % $prefArr["modulo_cycle_2"];
					}

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? $inputCounter2."\t" : "");

				//	Calculate resulting index and output indices
					$resultingIndex = ($inputCounter1 * $inputCounter2 + 1) % 256;
					$outputIndex1 = ($inputCounter1 > (0.5 * $prefArr["modulo_cycle_1"]) ? $inputCounter2 : $inputCounter1);
					$outputIndex2 = ($inputCounter1 > (0.25 * $prefArr["modulo_cycle_1"]) ? $inputCounter1 : $inputCounter2);

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? $resultingIndex."\t".$outputIndex1."\t".$outputIndex2."\t".$reverseScan."\t" : "");

				//	Text index and ascii code depending on reverse scan flag
					$serialNumTextIndex = ($reverseScan==1 ? mb_strlen($serialNumArr[$i]) - $j + 1 : $j);
					$serialNumCharCode = $this->getOrdFromUtf8(mb_substr($serialNumArr[$i],$serialNumTextIndex-1,1));

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? $serialNumTextIndex."\t".$serialNumCharCode."\t" : "");

				//	Calculate the license code keys (cf. ExcelSheet AG-AV)
					for($k=15;$k>=0;$k--) {
						$outputIndex = "outputIndex".$prefArr["outputIndexOrder"]{15-$k};
						$licenseCode[$k] = ($licenseCode[$k] + $this->getOrdFromUtf8($prefArr["fixed_key2"][(($resultingIndex * $serialNumCharCode + (15-$k)) % 16)]) * ($prefArr["fixed_key"][($prefArr["fixed_order"][(($outputIndex1 + (220-$k*11)) % 16)] + (45-$k*3)) % 16]) + $$outputIndex) % 256;

					//	Debug? add var to debug str
						$debugStr .= ($this->debug ? $licenseCode[$k]."\t" : "");
					}

				//	Debug? add var to debug str
					$debugStr .= ($this->debug ? "\n" : "");
				}
			}

		//	And the final license code is …
			for($i=15;$i>=0;$i--) {
				$outputStr .= $prefArr["chr"][$licenseCode[$i] % count($prefArr["chr"])].($i%4==0&&$i>0 ? "-" : "");	// block by "-" into 4 segments
			}
			return ($this->debug ? $debugStr : $outputStr);
		}

	//	Encrypetd formula to calculate machine access code
	//	--------------------------------------------------
		private function getMachineAccessCode() {
			return $this->getBuildKey();
		}

	//	Output access code
	//	------------------
		public function getAccessCode() {
			return $this->getMachineAccessCode();
		}
	}
?>
