<?php
	class ElevationOGR extends ElevationSource {
		
		public $gdalCache = [];
		
		private $gdalTrCache;
		private $noDataVal;
		private $srcSRS; 
		private $trgSRS;
		private $dataset;
		private $gdalregisterall;

		public function __construct($srcSRS, $trgSRS, $dataset,$noDataVal = -99999) {
			if(!function_exists("gdalregisterall"))
				throw new exception("module php-ogr not installed. see https://github.com/nono303/php-ogr");
			$this->srcSRS = $srcSRS;
			$this->trgSRS = $trgSRS;
			$this->dataset = $dataset;
			$this->noDataVal = $noDataVal;
			$this->gdalregisterall = false;
			$this->gdalTrCache = [];
		}
		
		public function getElevation($lat,$lon){
			return $this->getElevationDetails($lat,$lon)["value"];	
		}
		
		public function getElevationDetails($lat,$lon){
			if(!$this->gdalregisterall){
				gdalregisterall();
				$this->gdalregisterall = true;
			}
			if($this->srcSRS && !$this->gdalCache[$this->dataset]){
				$this->trgSRS = -1;
				$this->gdalCache[$this->dataset] = ["GDALDataset" => gdalopen($this->dataset,$this->trgSRS), "trgSRS" => $this->trgSRS];
				if(!$this->gdalTrCache[$this->srcSRS."-".$this->trgSRS])
					$this->gdalTrCache[$this->srcSRS."-".$this->trgSRS] = gdal_tr_create($this->srcSRS,$this->trgSRS);
			}
			$ret = ["src" => "rgealti"];
			if($this->srcSRS){
				$ret += gdal_locationinfo($this->gdalCache[$this->dataset]["GDALDataset"],$lon,$lat,$this->gdalTrCache[$this->srcSRS."-".$this->gdalCache[$this->dataset]["trgSRS"]]);
			} else {
				$ret += gdal_locationinfo($this->gdalCache[$this->dataset]["GDALDataset"],$lon,$lat);
			}
			$ret["raster"] += ["dataset" => $this->dataset];
			if($ret["value"] == $this->noDataVal)
				throw new Exception("out of range: ".json_encode($ret));
			return $ret;
		}
	}
?>