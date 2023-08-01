<?php
	class ElevationGDAL extends ElevationSource {
		
		private $pathorfile;
		private $badvalue;
		
		const exename = "gdallocationinfo";
		const exeparam = " -valonly -wgs84 ";
		const checkexe = true; // true : take time to fork not necessarily exe
		
		public function __construct($pathorfile,$badvalue = "-99999") {
			if(self::checkexe && !str_starts_with($ret = shell_exec(self::exename),"Usage"))
				throw new exception(self::exename." not in PATH: '".$ret."'");
			$this->pathorfile = $pathorfile;
			$this->badvalue = $badvalue;
		}

		public function getElevation($lat,$lon){
			if(is_dir($this->pathorfile)){
				$file = rtrim($this->pathorfile, '/') . '/'.self::getSrtmFileName($lat,$lon); 
			} else {
				if(is_file($this->pathorfile)){
					$file = $this->pathorfile;
				} else {
					throw new exception($this->pathorfile." doesn't exist");
				}
			}
			$cmdex = ($cmd = self::exename.self::exeparam.$file." ".$lon." ".$lat).": ";
			if(is_null($retcmd = shell_exec($cmd." 2>&1")))
				throw new exception($cmd.": [null] '".$retcmd."'");
			if(($ret = (float)trim($retcmd)) == $this->badvalue)
				throw new exception($cmdex." empty value");
			return $ret;
		}
	}
?>