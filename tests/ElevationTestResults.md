```>php ElevationTest.php
--- PHP-OGR ---
lat: 45.87902915 lon: 6.88715435
3751.25
--- GDAL HGT ---
lat: 45.97638988 lon: 7.65870801
4470
--- GDAL VRT (ogr) ---
lat: 45.87902915 lon: 6.88715435
3804.4885253906
--- DTM05 ---
lat: 47.07453399 lon: 12.69384182
3788.338
N47E012.hgt
--- DTM1 ---
lat: 45.97638988 lon: 7.65870801
4469.912
N45E007.hgt
--- STRM1 ---
lat: 40.08606004 lon: 22.35899302
2866.198
N40E022.hgt
--- STRM3 ---
lat: 44.8196218 lon: 6.7349799
2359.392
N44E006.hgt
--- GOOGLE ---
lat: 45.97638988 lon: 7.65870801
4440.2900390625
--- Elevation ---
lat: 45.97638988 lon: 7.65870801
4469.912
Array
(
    [ogr] => Array
        (
            [value] =>
            [error] => out of range: {"src":"rgealti","value":"-99999","in":{"srs":"WGS 84","epsg":"4326","x":7.65870801,"y":45.97638988},"raster":{"srs":"RGF93 v1 \/ Lambert-93 + NGF-IGN69 height","epsg":"5698","x":1060496.614355894,"y":6552489.502294987,"pixel":750497,"line":558510,"xSize":774000,"ySize":975000,"bandCount":1,"dataset":"F:\/maps\/dem\/rgealti_2-0_1m\/rgealti_2-0_1m_5698.vrt"}}
        )

    [dtm05] => Array
        (
            [value] =>
            [error] => F:/maps/dem/dtm05/N45E007.hgt doesn't exist
        )
    
    [dtm1] => Array
        (
            [value] => 4469.91
        )
    
    [srtm1] => Array
        (
            [value] =>
            [error] => F:/maps/dem/srtm1/N45E007.hgt doesn't exist
        )
    
    [srtm3] => Array
        (
            [value] =>
            [error] => F:/maps/dem/srtm3/N45E007.hgt doesn't exist
        )
    
    [google] => Array
        (
            [value] => 4440.29
        )
    
    [gdal] => Array
        (
            [value] =>
            [error] => gdallocationinfo -valonly -wgs84 F:/maps/dem/rgealti_2-0_1m/rgealti_2-0_1m_4326.vrt 7.65870801 45.97638988:  empty value
        )

)
```