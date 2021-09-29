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

if(isset($_POST['title'])){
    $titolo = $_POST['title'];
}

if(isset($_POST['viewC'])){
    $viewImg = $_POST['viewC'];
}



// estrai campi da Title
$query = 'match (t:Title) where t.titleName= "'.$titolo.'" return t.duration,t.releaseYear,t.description,t.dateAdded';
$result = $neo4j->run($query);
$record = $result->getRecord();
    $duration = $record->value('t.duration');
    $releaseYear = $record->value('t.releaseYear');
    $description = $record->value('t.description');
    $dateAdded = $record->value('t.dateAdded');

//estrai campi da Actor
$query = 'match (t:Title),(a:Actor) WHERE t.titleName="'.$titolo.'" AND (a)-[:ACTED_IN]->(t) RETURN a.actorName';
$result = $neo4j->run($query);
$finalActor='';
foreach ($result->getRecords() as $record) {
    $finalActor=$record->value('a.actorName').', '.$finalActor;
}
$finalActor = substr($finalActor, 0, strlen($finalActor)-2);

//estrai campi da Director
$query = 'match (t:Title),(d:Director) WHERE t.titleName="'.$titolo.'" AND (t)-[:DIRECTED_BY]->(d) RETURN d.directorName';
$result = $neo4j->run($query);
$finalDirector='';
foreach ($result->getRecords() as $record) {
    $finalDirector=$record->value('d.directorName').', '.$finalDirector;
}
$finalDirector = substr($finalDirector, 0, strlen($finalDirector)-2);

// estrai campi da Genre
$query = 'match (t:Title),(g:Genre) where t.titleName= "'.$titolo.'" AND (t)-[:LISTED_IN]->(g) RETURN g.genre';
$result = $neo4j->run($query);
$finalGenre='';
foreach ($result->getRecords() as $record) {
    $finalGenre=$record->value('g.genre').', '.$finalGenre;
}
$finalGenre = substr($finalGenre, 0, strlen($finalGenre)-2);

// estrai campi da Country
$query = 'match (t:Title),(c:Country) where t.titleName= "'.$titolo.'" AND (t)-[:FILMED_IN]->(c) RETURN c.countryName';
$result = $neo4j->run($query);
$finalCountry='';
foreach ($result->getRecords() as $record) {
    $finalCountry=$record->value('c.countryName').', '.$finalCountry;
}
$finalCountry = substr($finalCountry, 0, strlen($finalCountry)-2);

// estrai campi da Rating
$query = 'match (t:Title),(r:Rating) where t.titleName= "'.$titolo.'" AND (t)-[:RATED]->(r) RETURN r.rating';
$result = $neo4j->run($query);
$finalRating='';
foreach ($result->getRecords() as $record) {
    $finalRating=$record->value('r.rating').', '.$finalRating;
}
$finalRating = substr($finalRating, 0, strlen($finalRating)-2);


// estrai campi da Type
$query = 'match (t:Title),(tp:Type) where t.titleName= "'.$titolo.'" AND (t)-[:TYPE_OF]->(tp) RETURN tp.typeName';
$result = $neo4j->run($query);
$record = $result->getRecord();
$tipo=$record->value('tp.typeName');


if($viewImg=="yes") {
    include_once 'PHP-IMDB-Grabber-6.1.7/imdb.class.php';
    echo '
<div class="col col-lg-6" style="padding:10% !important;">
          
        <h1 id="title" style="color:#ffffff">' . $titolo . '</h1>
        <h3 id="dar" style="color:#D81F26">' . $duration . '|' . $releaseYear . '|' . $finalRating . '</h3>
        <p id="description">' . $description . '</p>
        <p id="tipo">Type <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $tipo . '</a></li></p>
        <p id="director">Directors <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $finalDirector . '</a></li></p>
        <p id="cast">Actors <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;" >' . $finalActor . '</a></li></p>
        <p id="genre">Genre <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;" >' . $finalGenre . '</a></li></p>
        <p id="releaseyear">Date added <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $dateAdded . '</a></li></p>
        <p id="releaseyear">Country <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $finalCountry . '</a></li></p>
      </div>

      <div id="copertina" class="col-lg-6" style="text-align:center !important;">
      ';
    $oIMDB = new IMDB($titolo);
    if ($oIMDB->isReady) {
        echo '
                <img src="' . $oIMDB->getPoster('small', false) . '"  style="border-radius:5px" height="60%" width="60%"  name="' . $titolo . '"  ></img>
               ';

    } else {
        echo '<p>Movie not found!</p>';
    }
    '
      </div>
';
}


else{
    echo '
<div class="col col-lg-6" style="padding:10% !important;">
          
        <h1 id="title" style="color:#ffffff">' . $titolo . '</h1>
        <h3 id="dar" style="color:#D81F26">' . $duration . '|' . $releaseYear . '|' . $finalRating . '</h3>
        <p id="description">' . $description . '</p>
        <p id="tipo">Type <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $tipo . '</a></li></p>
        <p id="director">Directors <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $finalDirector . '</a></li></p>
        <p id="cast">Actors <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;" >' . $finalActor . '</a></li></p>
        <p id="genre">Genre <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;" >' . $finalGenre . '</a></li></p>
        <p id="releaseyear">Date added <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $dateAdded . '</a></li></p>
        <p id="releaseyear">Country <a href="#" target="_blank" style="text-decoration:none;margin-left:1vw;">' . $finalCountry . '</a></li></p>
      </div>
      ';
}

?>
