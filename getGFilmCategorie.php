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
new Chart(document.getElementById("filmcategoria"), {
  type: \'line\',
  data: {
    labels: [';
    for($anno=$startY; $anno<$finalY; $anno++) echo $anno.',';
    echo $finalY;
    echo '],
    datasets: [{ 
        data: [';
                for($i=$startY ; $i<$finalY ; $i++) {
                    $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$i.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Horror Movies" RETURN count(t) as count';
                    $result = $neo4j->run($query);
                    $record = $result->getRecord();
                    echo $record->value('count');
                    echo',';
                }
                $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$finalY.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Horror Movies" RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');
        echo '],
        label: "Horror Movies",
        borderColor: "#3e95cd",
        fill: false
      }, { 
        data: [';
                for($i=$startY;$i<$finalY;$i++) {
                    $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$i.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Action & Adventure" RETURN count(t) as count';
                    $result = $neo4j->run($query);
                    $record = $result->getRecord();
                    echo $record->value('count');
                    echo',';
                }
                $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$finalY.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Action & Adventure" RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');
        echo '],
        label: "Action & Adventure",
        borderColor: "#8e5ea2",
        fill: false
      }, { 
        data: [';
                for($i=$startY;$i<$finalY;$i++) {
                    $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$i.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Thrillers" RETURN count(t) as count';
                    $result = $neo4j->run($query);
                    $record = $result->getRecord();
                    echo $record->value('count');
                    echo',';
                }
                $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$finalY.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Thrillers" RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');
        echo'],
        label: "Thrillers",
        borderColor: "#3cba9f",
        fill: false
      }, { 
        data: [';
                for($i=$startY;$i<$finalY;$i++) {
                    $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$i.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Comedies" RETURN count(t) as count';
                    $result = $neo4j->run($query);
                    $record = $result->getRecord();
                    echo $record->value('count');
                    echo',';
                }
                $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$finalY.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Comedies" RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');
        echo '],
        label: "Comedies",
        borderColor: "#e8c3b9",
        fill: false
      }, { 
        data: [';
                for($i=$startY;$i<$finalY;$i++) {
                    $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$i.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Documentaries" RETURN count(t) as count';
                    $result = $neo4j->run($query);
                    $record = $result->getRecord();
                    echo $record->value('count');
                    echo',';
                }
                $query = 'MATCH (t:Title),(g:Genre) WHERE t.releaseYear='.$finalY.' AND (t)-[:LISTED_IN]->(g) AND g.genre="Documentaries" RETURN count(t) as count';
                $result = $neo4j->run($query);
                $record = $result->getRecord();
                echo $record->value('count');
        echo '],
        label: "Documentaries",
        borderColor: "#c45850",
        fill: false
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: \'Movie & TV Show / Genre\'
    }
  }
});
';

?>