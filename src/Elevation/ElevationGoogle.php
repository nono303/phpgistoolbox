<?php
	class ElevationGoogle extends ElevationSource {
		
		private $googleKey;
		
		const GOOGLE_ELEVATION_URL = "https://maps.googleapis.com/maps/api/elevation/json";
			
		public function __construct($googleKey) {
			$this->googleKey = $googleKey; 
		}
		
		public function getElevation($lat,$lon){
			$parsed_json = json_decode($json_string = file_get_contents($url = self::GOOGLE_ELEVATION_URL."?locations=".$lat.",".$lon."&key=".$this->googleKey), TRUE);
			if($parsed_json['status'] == "OK")
				return $parsed_json['results'][0]['elevation'];
			throw new Exception($json_string);
		}
	}
?>