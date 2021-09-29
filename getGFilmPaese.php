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

$paesi = ["Italy","United Kingdom","Canada","United States","Spain","China","France","Denmark","Colombia","Hong Kong"];
$numP = count($paesi);

@header("Content-type: application/javascript");
echo '
var ctx = document.getElementById(\'filmpaese\').getContext(\'2d\');
    var myChart = new Chart(ctx, {
        type: \'horizontalBar\',
        data: {
            labels: [           
            ';

for($i=0 ; $i<$numP-1 ; $i++) {
    echo '"'.$paesi[$i].'",';
}
echo '"'.$paesi[$numP-1].'"';

echo  ' ],
            datasets: [{
                label: \'# Numero film prodotti / paese\',
                data: [';
for($i = 0; $i < $numP-1; $i++) {
    $query = 'MATCH (t:Title),(c:Country) WHERE t.releaseYear>='.$startY.' AND t.releaseYear<='.$finalY.' AND (t)-[:FILMED_IN]->(c) AND c.countryName CONTAINS "'.$paesi[$i].'" RETURN count(t) as count';
    $result = $neo4j->run($query);
    $record = $result->getRecord();
    echo $record->value('count').',';
}
$query = 'MATCH (t:Title),(c:Country) WHERE t.releaseYear>='.$startY.' AND t.releaseYear<='.$finalY.' AND (t)-[:FILMED_IN]->(c) AND c.countryName CONTAINS "'.$paesi[$numP-1].'" RETURN count(t) as count';
$result = $neo4j->run($query);
$record = $result->getRecord();
echo $record->value('count');

echo '
                ],
                
                
                backgroundColor:
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
