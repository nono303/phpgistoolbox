<?php
	$gk = "__YOUR-GOOGLE-API-KEY__";				

	include_once("../src/Elevation/Elevation.php");
	
	// https://github.com/nono303/php-ogr
	echo "--- PHP-OGR ---".PHP_EOL;
	echo "lat: ".($lat = 45.87902915)." lon: ".($lon = 6.88715435).PHP_EOL;
	$ogr = new ElevationOGR(4326,5698,"F:/maps/dem/rgealti_2-0_1m/rgealti_2-0_1m_5698.vrt");		
	echo $ogr->getElevation($lat,$lon).PHP_EOL;
	
	// https://gdal.org/programs/gdallocationinfo.html
	echo "--- GDAL HGT ---".PHP_EOL;
	echo "lat: ".($lat = 45.97638988)." lon: ".($lon = 7.65870801).PHP_EOL;
	$gdalHgt = new ElevationGDAL("F:/maps/dem/dtm1");												
	echo $gdalHgt->getElevation($lat,$lon).PHP_EOL;
	
	// https://gdal.org/programs/gdallocationinfo.html	
	echo "--- GDAL VRT (ogr) ---".PHP_EOL;
	echo "lat: ".($lat = 45.87902915)." lon: ".($lon = 6.88715435).PHP_EOL;
	$gdalVrt = new ElevationGDAL("F:/maps/dem/rgealti_2-0_1m/rgealti_2-0_1m_4326.vrt");			
	echo $gdalVrt->getElevation($lat,$lon).PHP_EOL;
	
	// https://drive.google.com/drive/folders/1GEXis6pHcqn3MqvsDBuOlRU5kSS1O6Xk
	$dtm05 = new ElevationHGT(Elevation::dtm05,"F:/maps/dem/dtm05");								
	echo "--- DTM05 ---".PHP_EOL;
	echo "lat: ".($lat = 47.07453399)." lon: ".($lon = 12.69384182).PHP_EOL;
	echo $dtm05->getElevation($lat,$lon).PHP_EOL;
	echo Elevation::getSrtmFileName($lat,$lon).PHP_EOL;
	
	// https://sonny.4lima.de/
	$dtm1 = new ElevationHGT(Elevation::dtm1,"F:/maps/dem/dtm1");									
	echo "--- DTM1 ---".PHP_EOL;
	echo "lat: ".($lat = 45.97638988)." lon: ".($lon = 7.65870801).PHP_EOL;
	echo $dtm1->getElevation($lat,$lon).PHP_EOL;
	echo Elevation::getSrtmFileName($lat,$lon).PHP_EOL;
	
	// https://e4ftl01.cr.usgs.gov/MEASURES/SRTMGL1.003/ -- require registration
	$srtm1 = new ElevationHGT(Elevation::srtm1,"F:/maps/dem/srtm1");								
	echo "--- STRM1 ---".PHP_EOL;
	echo "lat: ".($lat = 40.08606004)." lon: ".($lon = 22.35899302).PHP_EOL;
	echo $srtm1->getElevation($lat,$lon).PHP_EOL;
	echo Elevation::getSrtmFileName($lat,$lon).PHP_EOL;
	
	// https://e4ftl01.cr.usgs.gov/MEASURES/SRTMGL3.003/
	$srtm3 = new ElevationHGT(Elevation::srtm3,"F:/maps/dem/srtm3");								
	echo "--- STRM3 ---".PHP_EOL;
	echo "lat: ".($lat = 44.8196218)." lon: ".($lon = 6.7349799).PHP_EOL;
	echo $srtm3->getElevation($lat,$lon).PHP_EOL;
	echo Elevation::getSrtmFileName($lat,$lon).PHP_EOL;
	
	// https://code.google.com/apis/console/
	$google = new ElevationGoogle($gk);															
	echo "--- GOOGLE ---".PHP_EOL;
	echo "lat: ".($lat = 45.97638988)." lon: ".($lon = 7.65870801).PHP_EOL;
	echo $google->getElevation($lat,$lon).PHP_EOL;
	
	$ele = new Elevation(2);
	$ele->addSource(Elevation::ogr,$ogr);
	$ele->addSource(Elevation::dtm05,$dtm05);
	$ele->addSource(Elevation::dtm1,$dtm1);
	$ele->addSource(Elevation::srtm1,$srtm1);
	$ele->addSource(Elevation::srtm3,$srtm3);
	$ele->addSource(Elevation::google,$google);
	$ele->addSource(Elevation::gdal,$gdalVrt);
	echo "--- Elevation ---".PHP_EOL;
	echo "lat: ".($lat = 45.97638988)." lon: ".($lon = 7.65870801).PHP_EOL;
	echo $ele->getElevation($lat,$lon).PHP_EOL;
	print_r($ele->getAllElevation($lat,$lon)).PHP_EOL;
?>