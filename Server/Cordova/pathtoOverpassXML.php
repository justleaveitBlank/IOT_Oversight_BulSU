<?php
	header('Content-type: application/xml');
	echo file_get_contents("http://localhost/xml/overpass.xml");

?>