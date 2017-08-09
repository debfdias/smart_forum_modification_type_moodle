<?php

$PDO = new PDO( 'mysql:host=localhost;dbname=moodle_26', 'root', 'Rodrigo-1819' );


//$requestedTimestamp = isset ( $_GET [ 'timestamp' ] ) ? (int)$_GET [ 'timestamp' ] : time();

 echo $requestTimestamp;
while ( true )
{
    $stmt = $PDO->prepare( "SELECT message FROM mdl_octopus_post" );
    $stmt->bindParam( ':requestedTimestamp', $requestedTimestamp );
    $stmt->execute();
    $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );

    if ( count( $rows ) > 0 )
    {
        $json = json_encode( $rows );
        echo $json;
        break;
    }
    else
    {
         break;
        //sleep( 2 );
        //continue;
    }
}

?>
