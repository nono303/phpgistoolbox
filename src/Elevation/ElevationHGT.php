<?php
	class ElevationHGT extends ElevationSource {

		private $measPerDeg;
		private $resolution;
		private $hgtPath;
		private $dataset;
		
		const DATASET = [
			self::dtm05 =>	["measPerDeg" => 7201,	"resolution" => 0.5],
			self::dtm1 =>	["measPerDeg" => 3601,	"resolution" => 1],
			self::srtm1 =>	["measPerDeg" => 3601,	"resolution" => 1],
			self::srtm3 =>	["measPerDeg" => 1201,	"resolution" => 3]
		];

		public function __construct($dataset,$hgtPath) {
			if(!is_dir($hgtPath))
				throw new exception($hgtPath." doesn't exist");
			if (array_key_exists($dataset,self::DATASET)){
				$this->measPerDeg = self::DATASET[$dataset]["measPerDeg"];
				$this->resolution  = self::DATASET[$dataset]["resolution"];
				$this->hgtPath = rtrim($hgtPath, '/') . '/';
				$this->dataset = $dataset;
			} else {
				throw new Exception($dataset." unknown [".implode("|",array_keys(self::DATASET))."]");
			}
		}

		public function getElevation($lat,$lon,&$debug = null) {
			$latSec = $this->getSec($lat);
			$lonSec = $this->getSec($lon);

			$Xn = round($latSec / $this->resolution, 3);
			$Yn = round($lonSec / $this->resolution, 3);

			$a1 = round($Xn);
			$a2 = round($Yn);

			if ($Xn <= $a1 && $Yn <= $a2) {
				$b1 = $a1 - 1;
				$b2 = $a2;
				$c1 = $a1;
				$c2 = $a2 - 1;
			} else if ($Xn >= $a1 && $Yn >= $a2) {
				$b1 = $a1 + 1;
				$b2 = $a2;
				$c1 = $a1;
				$c2 = $a2 + 1;
			} else if ($Xn > $a1 && $Yn < $a2) {
				$b1 = $a1;
				$b2 = $a2 - 1;
				$c1 = $a1 + 1;
				$c2 = $a2;
			} else if ($Xn < $a1 && $Yn > $a2) {
				$b1 = $a1 - 1;
				$b2 = $a2;
				$c1 = $a1;
				$c2 = $a2 + 1;
			} else {
				throw new \Exception("{$Xn}:{$Yn}");
			}
			$hgtfile = self::getSrtmFileName($lat,$lon);
			$debug = [
				"lat" => $lat,
				"lon" => $lon,
				"Xn" => $Xn,
				"Yn" => $Yn,
				"file" => $hgtfile,
				"pos" => []
			];
			$a3 = $this->getElevationAtPosition($hgtfile, $a1, $a2,$debug);
			$b3 = $this->getElevationAtPosition($hgtfile, $b1, $b2,$debug);
			$c3 = $this->getElevationAtPosition($hgtfile, $c1, $c2,$debug);

			$n1 = ($c2 - $a2) * ($b3 - $a3) - ($c3 - $a3) * ($b2 - $a2);
			$n2 = ($c3 - $a3) * ($b1 - $a1) - ($c1 - $a1) * ($b3 - $a3);
			$n3 = ($c1 - $a1) * ($b2 - $a2) - ($c2 - $a2) * ($b1 - $a1);

			$d  = -$n1 * $a1 - $n2 * $a2 - $n3 * $a3;
			$zN = (-$n1 * $Xn - $n2 * $Yn - $d) / $n3;
			if ($zN > 10000) {
				return 0;
			} else {
				return $zN;
			}
		}
		
		private function getElevationAtPosition($file, $row, $column,&$debug) {				
			if(!is_file($this->hgtPath.$file))
				throw new Exception($this->hgtPath.$file." doesn't exist");
			$aRow     = $this->measPerDeg - $row;
			$position = ($this->measPerDeg * ($aRow - 1)) + $column;
			$position *= 2;
			fseek($stream = fopen($this->hgtPath.$file, "r"), $position);
			$short  = fread($stream, 2);
			$_      = unpack("n*", $short);
			$shorts = reset($_);
			$debug["pos"][] = [
				"row" => $row,
				"col" => $column,
				"val" => $shorts
			];
			return $shorts;
		}

		private function getSec($deg) {
			if($deg < 0) // fix for negative (S lat, W lon) value(s)
				$deg = abs(floor($deg))+$deg;
			$deg = abs($deg);
			$sec = round($deg * 3600, 4);
			$m   = fmod(floor($sec / 60), 60);
			$s   = round(fmod($sec, 60), 4);
			return ($m * 60) + $s;
		}
	}
?>