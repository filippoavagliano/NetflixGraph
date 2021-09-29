<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use GraphAware\Neo4j\Client\ClientBuilder;
require __DIR__.'/vendor/autoload.php';
include_once 'PHP-IMDB-Grabber-6.1.7/imdb.class.php';

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


if(isset($_POST['tipo'])){
    $tipoScelto = $_POST['tipo'];
}
if ($tipoScelto==""){
    $query = 'MATCH (t:Title),(g:Genre),(tp:Type)
WHERE  (t)-[:TYPE_OF]->(tp) AND (t)-[:LISTED_IN]->(g)
RETURN DISTINCT g.genre';
    echo '<select class="custom-select" >
      <option value="" selected ></option>
      ';
    $result = $neo4j->run($query);
    foreach ($result->getRecords() as $record) {
        $oneGenre=$record->value('g.genre');
        echo '<option value="'.$oneGenre.'">'.$oneGenre.'</option>';
    }
}
else{



// estrai generi
if($tipoScelto=="Movie") {

    $query = 'MATCH (t:Title),(g:Genre),(tp:Type)
WHERE tp.typeName="Movie" AND NOT(g.genre CONTAINS "Shows") AND (t)-[:TYPE_OF]->(tp) AND (t)-[:LISTED_IN]->(g)
RETURN DISTINCT g.genre';
}
else{
    $query = 'MATCH (t:Title),(g:Genre),(tp:Type)
WHERE tp.typeName="TV Show" AND NOT(g.genre CONTAINS "Movies") AND (t)-[:TYPE_OF]->(tp) AND (t)-[:LISTED_IN]->(g)
RETURN DISTINCT g.genre';
}
echo '<select class="custom-select" >
      <option value="" selected ></option>
      ';

$result = $neo4j->run($query);

foreach ($result->getRecords() as $record) {
    $oneGenre=$record->value('g.genre');
    echo '<option value="'.$oneGenre.'">'.$oneGenre.'</option>';
}
echo '</select>';

}


?>
