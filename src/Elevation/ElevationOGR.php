<?php
	class ElevationOGR extends ElevationSource {

		private $coordsTransformer;
		private $noDataVal;
		private $srcSRS;
		private $trgSRS;
		private $datafile;
		private $dataset;

		public function __construct($datafile, $srcSRS = null, $trgSRS =null , $noDataVal = -99999) {
			if(!function_exists("gdalregisterall"))
				throw new exception("module php-ogr not installed. see https://github.com/nono303/php-ogr");
			$this->trgSRS = $trgSRS;
			$this->datafile = $datafile;
			$this->noDataVal = $noDataVal;
			gdalregisterall();
			if($srcSRS == $this->trgSRS){ // no transformation needed
				$this->srcSRS = null;
			} elseif($srcSRS){
				if(!$this->trgSRS)
					throw new Exception("target SRS 'null' must be set if source SRS '".$this->srcSRS."' specified");
				$this->srcSRS = $srcSRS;
				$this->coordsTransformer = gdal_tr_create($this->srcSRS,$this->trgSRS);
			}
			$this->dataset = gdalopen($this->datafile,$this->trgSRS);
		}

		public function getElevation($lat,$lon,&$debug = null){
			$debug = $this->getElevationDetails($lat,$lon);
			if($debug["value"] == $this->noDataVal)
				throw new Exception("out of range: ".json_encode($ret));
			return $debug["value"];
		}

		public function getElevationDetails($lat,$lon){
			if($this->srcSRS){
				$ret = gdal_locationinfo($this->dataset,$lon,$lat,$this->coordsTransformer);
			} else {
				$ret = gdal_locationinfo($this->dataset,$lon,$lat);
			}
			$ret["raster"] += ["datafile" => $this->datafile];
			return $ret;
		}
	}
?>