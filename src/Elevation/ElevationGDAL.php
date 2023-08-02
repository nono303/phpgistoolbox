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

		public function getElevation($lat,$lon,&$debug = null){
			if(is_dir($this->pathorfile)){
				$file = rtrim($this->pathorfile, '/') . '/'.self::getSrtmFileName($lat,$lon); 
			} else {
				if(is_file($this->pathorfile)){
					$file = $this->pathorfile;
				} else {
					throw new exception($this->pathorfile." doesn't exist");
				}
			}
			$retcmd = shell_exec(($cmd = self::exename.self::exeparam.$file." ".$lon." ".$lat)." 2>&1");
			$debug = ["cmd" => $cmd,"retcmd" => $retcmd];
			if(is_null($retcmd))
				throw new exception($cmd.": [null]");
			if(str_starts_with($retcmd,"ERROR"))
				throw new exception($cmd.": [error] '".$retcmd."'");
			if(($ret = (float)trim($retcmd)) == $this->badvalue)
				throw new exception($cmd.": [empty] '".$retcmd."'");
			return $ret;
		}
	}
?>