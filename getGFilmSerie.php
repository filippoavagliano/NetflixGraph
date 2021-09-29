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


@header("Content-type: application/javascript");
echo '
new Chart(document.getElementById("filmserie"), {
    type: \'doughnut\',
    data: {
        labels: ["Movie", "TV Show"],
      datasets: [
        {
            label: "Population (millions)",
          backgroundColor: ["#3e95cd","#c45850"],
          data: [';

$query = 'MATCH (t:Title),(tp:Type) WHERE t.releaseYear>='.$startY.' AND t.releaseYear<='.$finalY.' AND (t)-[:TYPE_OF]->(tp) AND tp.typeName="Movie" RETURN count(t) as count';
$result = $neo4j->run($query);
$record = $result->getRecord();
echo $record->value('count');
echo ',';
$query = 'MATCH (t:Title),(tp:Type) WHERE t.releaseYear>='.$startY.' AND t.releaseYear<='.$finalY.' AND (t)-[:TYPE_OF]->(tp) AND tp.typeName="TV Show" RETURN count(t) as count';
$result = $neo4j->run($query);
$record = $result->getRecord();
echo $record->value('count');

     echo' ]
        }
      ]
    },
    options: {
        title: {
            display: true,
        text: \'Movie vs TV Show\'
      }
    }
});

';

?>
