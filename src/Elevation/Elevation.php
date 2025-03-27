<?php
	require("ElevationHGT.php");
	require("ElevationOGR.php");
	require("ElevationGoogle.php");
	require("ElevationGDAL.php");

	abstract class ElevationSource {

		const ogr	= 0;
		const dtm05	= 1;
		const dtm1	= 2;
		const srtm1	= 3;
		const srtm3	= 4;
		const google= 5;
		const gdal	= 6;

		abstract public function getElevation($lat,$lon,&$debug = null);

		public static function getSrtmFileName($lat,$lon){
			return sprintf(
				'%s%02d%s%03d.hgt',
				floor($lat) < 0 ? 'S' : 'N',
				abs(floor($lat)),
				ceil($lon)< 0 ? 'W' : 'E',
				abs(floor($lon))
			);
		}

		public static function getSourceNameById($id) {
			foreach ((new ReflectionClass('ElevationSource'))->getConstants() as $name => $value)
				if ($value == $id)
					return $name;
		}
	}

	class Elevation extends ElevationSource{

		private $precision;
		private $sources;
		private $sourceOrder;

		public function __construct($precision = 2) {
			$this->precision = $precision;
			$this->sources = [];
			$this->sourceOrder = [self::ogr, self::dtm05, self::dtm1, self::srtm1, self::srtm3, self::google, self::google];
		}

		public function addSource($id, $src) {
			if (array_key_exists($id,$this->sourceOrder)){
				if ($src instanceof ElevationSource){
					$this->sources[$id] = $src;
				} else {
					throw new Exception("source ".$id." is not ElevationSource object");
				}
			} else {
				throw new Exception("source ID '".$id."' unknown");
			}
		}

		public function getElevation($lat,$lon,&$debug = null){
			foreach($this->sourceOrder as $sourceId){
				if($this->sources[$sourceId]){
					try{
						return $this->sources[$sourceId]->getElevation($lat,$lon,$debug);
					} catch(Exception $e) {}
				}
			}
			throw new Exception("no source available for lat: ".$lat." lon: ".$lon);
		}

		public function getAllElevation($lat,$lon,&$debug = null){
			foreach($this->sources as $id => $source){
				$sourceName = self::getSourceNameById($id);
				try{
					$retour[$sourceName] = ["value" => round($source->getElevation($lat,$lon,$debug),$this->precision)];
				} catch(Exception $e) {
					$retour[$sourceName] = ["value" => null, "error" => $e->getMessage()];
				}
			}
			return $retour;
		}
	}
?>