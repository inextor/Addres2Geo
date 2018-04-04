<?php

include_once (__DIR__.'/akou/src/Curl.php' );

use \akou\Curl;


header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=address_".date('Ymdhsi').'.csv');

$output	= fopen('php://output', 'w');


$key	= '';

fputcsv($output, array( 'Address','Latitude','Longitude' ) );

if( empty( $_POST['addresses'] ) )
{
	fputcsv($output, array( 'Error','Addresses can\'t be empty','' ) );
	exit;
}


$responseData	= array();
$addresses		= preg_split("/[\r\n]+/",  $_POST['addresses']  );
$url_base		= "https://maps.googleapis.com/maps/api/geocode/json?key=".$key.'&address=';


foreach( $addresses as $address)
{
    $curl   = new Curl( $url_base.urlencode( $address )  );
    $curl->execute();
	//echo $curl->response.PHP_EOL;
    $responseObj    =json_decode( $curl->response );

	//print_r( $responseObj );

    if( !empty( $responseObj )
        && !empty( $responseObj->results )
        && !empty( $responseObj->results[0]->geometry )
        && !empty( $responseObj->results[0]->geometry->location ) )
    {
        fputcsv($output, array( $address, $responseObj->results[0]->geometry->location->lat, $responseObj->results[0]->geometry->location->lng ) );
    }
    else
    {
        fputcsv($output, array( $address, "","" ) );
    }
}
