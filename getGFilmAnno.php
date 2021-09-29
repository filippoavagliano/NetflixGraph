<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use GraphAware\Neo4j\Client\ClientBuilder;
require __DIR__.'/vendor/autoload.php';


$app = new Application();


if (false !== getenv('GRAPHSTORY_URL')) {
    $cnx = getenv('GRAPHSTORY_URL');
} else {
    $config = Yaml::parse(file_get_contents(__DIR__.'/config/config.yml'));
    $cnx = $config['neo4j_url'];
}


$neo4j = ClientBuilder::create()
    ->addConnection('default', $cnx)
    ->build();



if(isset($_POST['startY'])){
    $startY = $_POST['startY'];
}

if(isset($_POST['finalY'])){
    $finalY = $_POST['finalY'];
}


/*
$startY = 2010;
$finalY = 2020;
*/

@header("Content-type: application/javascript");
echo '
var ctx = document.getElementById(\'filmanno\').getContext(\'2d\');
    var myChart = new Chart(ctx, {
        type: \'line\',
        data: {
            labels: [           
            ';
            for($i = $startY; $i<$finalY; $i++) {
                echo $i.',';
            }
            echo $finalY;

         echo  ' ],
            datasets: [{
                label: \'# Numero di film usciti/anno\',
                data: [';
                for($i = $startY; $i<$finalY; $i++) {
                $query = 'MATCH (t:Title) WHERE t.releaseYear='.$i.' RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count').',';
            }
                $query = 'MATCH (t:Title) WHERE t.releaseYear='.$finalY.' RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');

                echo '
                ],
                
                
                borderColor:
                    \'rgba(212,17,17,0.45)\'
            }]
        },
        options: {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: true
                    }
        }]
            }
}
    });

';

?>
