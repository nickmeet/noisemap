<!DOCTYPE html>	<html lang="en">	<head>		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>		<title>Silent places at the Rolex Learning Center</title>		<link rel="stylesheet" type="text/css" href="style/map.css" />		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>		<script type="text/javascript" src="js/jquery.svg.1.4.4.js"></script>		<script type="text/javascript" src="js/main.js"></script>	</head><body>	<div id="container"><!-- domain dependent is only the jnlp file -->	<script type="text/javascript">		$(document).ready(function(){			$(window).load(function() { // waits until all external files are loaded				$('#heatmapArea').svg({onLoad: drawInitial});			});		});	   	</script>	<h2>Noise levels at the Rolex Learning Center over the last 2 hours</h2>	<?php		error_reporting(E_ALL); 		if(empty($_GET) || !$_GET['isMobile'])			echo "Measure the noise intensity (in decibels) of your area with your <a href=\"x.apk\">mobile</a>.<br/>".			//"<a href=\"https://market.android.com/details?id=xifopen.noisemap.client.android&feature=search_result\">".			//"<img src=\"http://www.android.com/images/brand/45_avail_market_logo1.png\" align=\"middle\"/></a><p></p>".			"The mobile application shows this map and starts automatically measuring. By using it you agree to share your location.<p></p>";			/*			<a href=\"launch.jnlp\">laptop</a> ".			"(using <a href=\"http://java.com/en/\">Java Web Start</a>)".			" or your 			*/	?>  	  	<div id="heatmapArea"></div>  	  	<?php		error_reporting(E_ALL & ~E_NOTICE); 	  	ini_set("display_errors", "1");	    include("ffdb.inc.php");	// Flat File DataBase: .met file holds the schema and .dat file holds the data	    class Area{	    	var $x = "";	    	var $y = "";	    	var $width = "";	    	var $height = "";	    	var $noiseavg = 0;	// avg	    	var $n = 0;			// number of measurements		    function setData($x, $y, $width, $height){		        $this->x = $x;		        $this->y = $y; 		        $this->width = $width;		        $this->height = $height;		        return $this;		    }		    function addMeasurement($noise){		    	$this->n = $this->n + 1;		        $this->noiseavg = $this->noiseavg + ($noise - $this->noiseavg)/$this->n;	  		        return $this;		    }	    }	    class Areas{	    	var $arrayOfAreas = array();	    	var $max = -200;	// necessary		// TODO: remove max from PHP		    function add($x, $y, $width, $height, $noise){		    					$found = false;		    	foreach($this->arrayOfAreas as $area){		    		if($area->x==$x && $area->y==$y){		    			$area->addMeasurement($noise);		    			$found = true;		    		}		    					    	}	 						    			    	if(!$found){		    		$ar = new Area();					array_push($this->arrayOfAreas, $ar->setData($x,$y, $width, $height)->addMeasurement($noise));		    	}						    			        return $this;		    }		    function addNotMeasuredAreas(){		    	$lines = split("\n", file_get_contents('AreasOfInterest.csv'));		    	foreach($lines as $line){		    		list($areaid, $height, $width, $x, $y, $name) = split(";", $line);		    		$found = false;			    	foreach($this->arrayOfAreas as $area){			    		if($area->x==$x && $area->y==$y){			    			$found = true;			    			break;			    		}		    						    	}			    	if(!$found){			    		$ar = new Area();			    		array_push($this->arrayOfAreas, $ar->setData($x,$y, $width, $height)->addMeasurement(0));			    	}		    	}		    }		    function toString(){		    	$table = '<table cellspacing="0"><tr> <th>Noise Intensity</th></tr>';		    	foreach($this->arrayOfAreas as $area){					$table .= 	"\n".'<tr>'."<td data-x=\"$area->x\" data-y=\"$area->y\" data-width=\"$area->width\"".								"data-height=\"$area->height\" >$area->noiseavg</td>".'</tr>';		    	}		    	$table .= '</table>';		    	echo $table;			}		    	    }		$db = new FFDB();		$aps = simplexml_load_file('aps.xml');		if($db->open("noisepoints")){			$bssid = "bssid";			$noise = "noise";			$timestamp = "timestamp";			$arrayofareas = new Areas();			$unknowns = array();			// 369235 = 2 hours			// time()*1000			list($usec, $sec) = explode(" ", microtime());			$now = round(((float)$usec + (float)$sec) * 1000);			$hours2ago = $now - 1000*60*60*2;			$nothing = true;			/// empty($_GET) || ($item[$timestamp] > $_GET['min'] && $item[$timestamp] < $_GET['max'])			foreach($db->getall(NULL) as $item)	// -40dB is considered as silence, -20dB is considered too loud						//if($item[$timestamp] > $hours2ago){					foreach($aps->entry as $entry)						if($entry->string==$item[$bssid])							foreach($entry->area as $routerarea){								list($areaid, $height, $width, $x, $y) = $routerarea->attributes();								if($x!=0 && $y!=0)									$arrayofareas->add($x, $y, $width, $height, $item[$noise]+60);	//1/($item[$noise]+60)								//else if(!in_array($item[$bssid], $unknowns))								//	array_push($unknowns, $item[$bssid]);							}					$nothing = false;				//}			if($nothing)			$arrayofareas->addNotMeasuredAreas();			$arrayofareas->toString();			//foreach($unknowns as $unknown)	// for debugging			//	echo "unknown BSSID: ".$unknown."<br/>";		}	?>		<div id="note">	<br/>	For problems or suggestions, feel free to <a href="http://people.epfl.ch/nikolaos.maris">contact us</a> or browse the <a href="https://github.com/nickmeet/noisemap">source code</a>.	</div>	</div></body></html>