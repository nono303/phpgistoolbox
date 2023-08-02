gdal_translate -of GTiff ..\dtm1\N47E012.hgt N47E012.tif
gdalbuildvrt -overwrite -srcnodata -99999 -vrtnodata -99999 -a_srs epsg:4326 N47E012.vrt N47E012.tif
pause