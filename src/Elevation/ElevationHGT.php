<?php
	ini_set("memory_limit","256M");

	class ElevationHGT extends ElevationSource {

		private $measPerDeg;
		private $resolution;
		private $hgtPath;
		private $dataset;
		private $debug;
		
		private static $hgtCache = [];
		
		const DATASET = [
			self::dtm05 =>	["measPerDeg" => 7201,	"resolution" => 0.5],
			self::dtm1 =>	["measPerDeg" => 3601,	"resolution" => 1],
			self::srtm1 =>	["measPerDeg" => 3601,	"resolution" => 1],
			self::srtm3 =>	["measPerDeg" => 1201,	"resolution" => 3]
		];
		// required memory for caching (only) is (HGTCACHE_MAXSIZE + 1) * 26MB (DTM1)
		const HGTCACHE_MAXSIZE = 4;

		public function __construct($dataset, $hgtPath, $debug = false) {
			if (array_key_exists($dataset,self::DATASET)){
				$this->measPerDeg = self::DATASET[$dataset]["measPerDeg"];
				$this->resolution  = self::DATASET[$dataset]["resolution"];
				$this->hgtPath = rtrim($hgtPath, '/') . '/';
				$this->dataset = $dataset;
				$this->debug = $debug;
			} else {
				throw new Exception($dataset." unknown [".implode("|",array_keys(self::DATASET))."]");
			}
		}

		public function getElevation($lat, $lon, &$hgtfile = null) {
			$latSec = $this->getSec($lat);
			$lonSec = $this->getSec($lon);

			$Xn = round($latSec / $this->resolution, 3);
			$Yn = round($lonSec / $this->resolution, 3);
			if($this->debug){
				echo "lat: ".$lat.PHP_EOL."lon: ".$lon.PHP_EOL;
				echo "Xn: ".$Xn.PHP_EOL;
				echo "Yn: ".$Yn.PHP_EOL;
			}
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
			$a3 = $this->getElevationAtPosition($hgtfile, $a1, $a2);
			$b3 = $this->getElevationAtPosition($hgtfile, $b1, $b2);
			$c3 = $this->getElevationAtPosition($hgtfile, $c1, $c2);

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
		
		private function getElevationAtPosition($file, $row, $column) {
			if(!is_file($this->hgtPath.$file))
				throw new Exception($this->hgtPath.$file." doesn't exist");
			$aRow = $this->measPerDeg - $row;
			$position = ($this->measPerDeg * ($aRow - 1)) + $column;
			$position *= 2;
			if(is_null(self::$hgtCache[$this->hgtPath.$file])){
				if(sizeof(self::$hgtCache) >= self::HGTCACHE_MAXSIZE){
					if($this->debug)
						echo "hgt memory cache: ".realpath(array_key_first(self::$hgtCache))." removed. size limit(".sizeof(self::$hgtCache).") reached".PHP_EOL;
					array_shift(self::$hgtCache);
				}
				// read file and write memory https://stackoverflow.com/a/39649785 - https://stackoverflow.com/a/2987330
				fwrite(
					self::$hgtCache[$this->hgtPath.$file] = fopen("php://memory", "w+b"),
					fread(
						$tempStream = fopen($this->hgtPath.$file, "r"),
						filesize($this->hgtPath.$file)
					)
				);
				fclose($tempStream);
				if($this->debug)
					echo "hgt memory cache: ".realpath(array_key_first(self::$hgtCache))." added (cached-hgt:".sizeof(self::$hgtCache)." memory-used:".memory_get_usage(true)." memory-limit:".ini_get("memory_limit")."]".PHP_EOL;
			}
			fseek(self::$hgtCache[$this->hgtPath.$file], $position);
			$short  = fread(self::$hgtCache[$this->hgtPath.$file], 2);
			$_	  = unpack("n*", $short);
			$shorts = reset($_);
			if($this->debug)
				echo "get ".$this->hgtPath.$file." @".$row."x".$column.":".$shorts.PHP_EOL;
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