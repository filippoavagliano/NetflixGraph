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

if(isset($_POST['text'])){
    $text = $_POST['text'];
}

if(isset($_POST['searchby'])){
    $searchby = $_POST['searchby'];
}

if(isset($_POST['type'])){
    $type = $_POST['type'];
}

if(isset($_POST['genre'])){
    $genre = $_POST['genre'];
}

if(isset($_POST['releaseyear'])){
    $releaseyear = $_POST['releaseyear'];
}

if(isset($_POST['orderby'])){
    $orderby = $_POST['orderby'];
}

if(isset($_POST['viewC'])){
    $viewImg = $_POST['viewC'];
}


// mancano tutti e 4
if($type=="" && $genre=="" && $releaseyear=="" && $orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
// mancano type, genre, releaseyear
elseif ($type=="" && $genre=="" && $releaseyear=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }

}
// manca type, genre, orderby
elseif($type=="" && $genre=="" && $orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
// manca type, releaseyear, orderby
elseif($type=="" && $releaseyear=="" && $orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
// mancano genre, release year, orderby
elseif( $genre=="" && $releaseyear=="" && $orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
// mancano solo type e genre
elseif($type=="" && $genre=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}

// mancano releaseyear e orderby
elseif($releaseyear=="" && $orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title),(g:Genre),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title),(g:Genre),(tp:Type) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title),(g:Genre),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}

// mancano genre e releaseyear
elseif( $genre=="" && $releaseyear=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}
// mancano type e orderby

elseif($type=="" && $orderby=="")
    {
        if($searchby=="title")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
        }
        elseif ($searchby=="actor")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
        }
    }

// mancano  type e release year
elseif($type=="" && $releaseyear=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}

//mancano genere e orderby
elseif($genre=="" && $orderby=="")
{
    echo 'hai inserito solo ';
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
//manca solo type
elseif($type=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}

//manca solo Genre
elseif($genre=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND t.releaseYear=toInteger("'.$releaseyear.'") AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}
// manca solo release year
elseif($releaseyear=="")
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}

//manca solo orderby
elseif($orderby=="")
{
    if($searchby=="title")
    {
        $query = 'MATCH (t:Title),(g:Genre),(tp:Type) WHERE t.titleName CONTAINS"'.$text.'" AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    elseif ($searchby=="actor")
    {
        $query = 'MATCH (a:Actor),(t:Title),(g:Genre),(tp:Type) WHERE a.actorName CONTAINS"'.$text.'" AND (a)-[:ACTED_IN]->(t) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
    else
    {
        $query = 'MATCH (d:Director),(t:Title),(g:Genre),(tp:Type) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear LIMIT 50';
    }
}
//non manca nessun filtro
else
{
    if($searchby=="title")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        elseif ($orderby=="DESCtitle")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        elseif ($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        else
        {
            $query = 'MATCH (t:Title),(tp:Type),(g:Genre) WHERE t.titleName CONTAINS"'.$text.'" AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }

    }
    elseif ($searchby=="actor")
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleNAME DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (a:Actor),(t:Title),(tp:Type),(g:Genre) WHERE a.actorName CONTAINS"' . $text . '" AND (a)-[:ACTED_IN]->(t) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.releaseYear DESC LIMIT 50';
        }
    }
    else
    {
        if($orderby=="ASCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName LIMIT 50';
        }
        if($orderby=="DESCtitle")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear ORDER BY t.titleName DESC LIMIT 50';
        }
        if($orderby=="ASCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
        if($orderby=="DESCreleaseyear")
        {
            $query = 'MATCH (d:Director),(t:Title),(tp:Type),(g:Genre) WHERE d.directorName CONTAINS"'.$text.'" AND (t)-[:DIRECTED_BY]->(d) AND tp.typeName="'.$type.'" AND (t)-[:TYPE_OF]->(tp) AND g.genre CONTAINS"'.$genre.'" AND (t)-[:LISTED_IN]->(g) AND t.releaseYear=toInteger("'.$releaseyear.'") RETURN t.titleName,t.releaseYear t.releaseYear LIMIT 50';
        }
    }
}
$result = $neo4j->run($query);

if($viewImg=="yes") {
    include_once 'PHP-IMDB-Grabber-6.1.7/imdb.class.php';

    echo '<div id="catalogo" class="row justify-content-md-center" style="margin-top:1vh;">';

    foreach ($result->getRecords() as $record) {
        $titolo = $record->value('t.titleName');
        $oIMDB = new IMDB($titolo);
        if ($oIMDB->isReady) {
            echo '
            <div class="col col-lg-auto" >
            <div class="card hvr-grow" style = "width: 13rem; color:white !important;" >';

            echo '
                <img src="' . $oIMDB->getPoster('small', false) . '" class="card-img-top"  name="' . $titolo . '" onclick="filmSingolo(this.name)" ></img>
               ';
            echo '</div > </div>';
        }

    }
    echo '</div>';
}
else{
    echo '<div id="catalogo" class="justify-content-md-center" style="margin-top:1vh;"> 
            
          <table style="border: 2px solid red" > 
          <tr>
            <td style="border: 2px solid red;">
            <h3 style="color: white;text-align: center; padding: 15px;">Title</h3>
            </td> 
            <td style="border: 2px solid red;">
            <h3 style="color: white; padding: 15px; text-align: center">Release Year</h3>
            </td>
          </tr>
          ';

    foreach ($result->getRecords() as $record)
    {
        $titolo = $record->value('t.titleName');
        $years = $record->value('t.releaseYear');


            echo '  <tr>
                    <td style="color: white; border: 1px solid red; padding: 10px;" id="'. $titolo .'" onclick="filmSingolo(this.id)">'.$titolo.'</td>
                    <td style="color: white;border: 1px solid red; padding: 10px; text-align:center">'.$years.'</td>
                    </tr>
                    ';

    }
    echo '</table></div>';
}

?>