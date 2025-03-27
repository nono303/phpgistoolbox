<?php
	include_once("../src/Elevation/Elevation.php");
	
	$dispalyDebug = true;
	
	$gk = null;//"__YOUR-GOOGLE-API-KEY__";				
	
	$lat = 47.07452739;
	$lon = 12.69384063;
	echo "lat: ".$lat." lon: ".$lon.PHP_EOL;
	
	$datapath = "./data/";
	$srcfile = "N47E012";	
	echo Elevation::getSrtmFileName($lat,$lon).PHP_EOL;
	
	$sources = [
		/* https://github.com/nono303/php-ogr */
		Elevation::ogr		=> ($ogr = new ElevationOGR($datapath."geotiff/".$srcfile.".tif")),
	//	Elevation::ogr		=> ($ogr = new ElevationOGR($datapath."geotiff/".$srcfile.".vrt")),
		/* // https://drive.google.com/drive/folders/1GEXis6pHcqn3MqvsDBuOlRU5kSS1O6Xk */
		Elevation::dtm05	=> ($dtm05 = new ElevationHGT(Elevation::dtm05,$datapath."dtm05")),
		/* https://sonny.4lima.de/ */
		Elevation::dtm1		=> ($dtm1 = new ElevationHGT(Elevation::dtm1,$datapath."dtm1")),
		/* https://e4ftl01.cr.usgs.gov/MEASURES/SRTMGL1.003/ -- require registration */
		Elevation::srtm1	=> ($srtm1 = new ElevationHGT(Elevation::srtm1,$datapath."srtm1")),
		/* https://e4ftl01.cr.usgs.gov/MEASURES/SRTMGL3.003/ */
		Elevation::srtm3	=> ($srtm3 = new ElevationHGT(Elevation::srtm3,$datapath."srtm3")),
		/* https://gdal.org/programs/gdallocationinfo.html */
		Elevation::gdal		=> ($gdal = new ElevationGDAL($datapath."geotiff/".$srcfile.".vrt")),
	//	Elevation::gdal		=> ($gdal = new ElevationGDAL($datapath."geotiff/".$srcfile.".tif")),
	//	Elevation::gdal		=> ($gdal = new ElevationGDAL($datapath."dtm1")),
	];

	if($gk)
		$sources[Elevation::google]	= ($google = new ElevationGoogle($gk));
	
	$elevation = new Elevation(2);
	foreach($sources as $id => $source){
		$elevation->addSource($id,$source);
		echo "--- ".Elevation::getSourceNameById($id)." ---".PHP_EOL;
		echo $source->getElevation($lat,$lon,$debug).PHP_EOL;
		if($dispalyDebug)
			echo "  debug: ".json_encode($debug,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
	}
	echo "--- Elevation ---".PHP_EOL;
	echo $elevation->getElevation($lat,$lon,$debug).PHP_EOL;
	echo json_encode($elevation->getAllElevation($lat,$lon),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
?>