<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Netflix Dataset</title>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

      <script src="node_modules/jquery/dist/jquery.min.js"></script>
      <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
      <script src="node_modules/chart.js/dist/Chart.bundle.js"></script>
      <link href="Hover-master/css/hover.css" rel="stylesheet">
      <link rel="stylesheet" href="css/bootstrap.min.css"/></link>
      <link rel="stylesheet" href="style.css"/></link>

  </head>
  <body style="background-color:black">

    <div class="header"></div>
  <input type="checkbox" class="openSidebarMenu" id="openSidebarMenu">
  <label style="background-color:black;" for="openSidebarMenu" id="openSidebarMenu" class="sidebarIconToggle">
    <div class="spinner diagonal part-1"></div>
    <div class="spinner horizontal"></div>
    <div class="spinner diagonal part-2"></div>
  </label>

  <div id="sidebarMenu" style="z-index:20">
  <ul class="sidebarMenuInner">
    <li><h2>Grafici</h2></li>

    <li><a href="graficoFilmAnno.html" target="_blank" class="hvr-underline-reveal">Movie & TV Show / Year</a></li>
    <li><a href="graficoFilmPaese.html" target="_blank" class="hvr-underline-reveal">Movie & TV Show / Country</a></li>
    <li><a href="graficoFilmCategoria.html" target="_blank" class="hvr-underline-reveal">Movie & TV Show / Genre</a></li>
    <li><a href="graficoFilmSerie.html" target="_blank" class="hvr-underline-reveal">Movie vs TV Show</a></li>


  </ul>
</div>

    <div>

    <div class="container-fluid" >
      <div class="row justify-content-center">

            <img src="logoNet.png"  width="400" height="300" alt="Netflix" loading="lazy" ></img>

      </div>


      <div class="row justify-content-center">
        <div class="col col-lg-4" style="display:flex;" >
          <input type="text"  class="form-control" id="testoBarraRicerca" style="border-top-left-radius:50px !important;border-bottom-left-radius:50px !important; flex:10; padding:23px;"/>
          <button  onclick="getRicerca()" class="form-control" style="border-top-right-radius:50px !important;background-image: url('lente3.png');background-repeat: no-repeat;background-position: center center; background-size: 27%;border-bottom-right-radius:50px !important; flex:1; padding:23px;">

          </button>


        </div>
      </div>

  <div class="row justify-content-md-center">
      <c><p style="color:white; margin-top:2.7vh;">Search By </p></c>

    <c><div class="input-group" style="margin-top:2vh;"></c>
    <div class="input-group-prepend">
      <div class="input-group-text">

        <div class="col col-lg-4">
          <input type="radio" name="filtrohome" checked  value="title">Title</input>
        </div>
        <div class="col col-lg-4">
          <input type="radio" name="filtrohome"  value="actor">Actor</input>
        </div>
        <div class="col col-lg-4">
          <input type="radio" name="filtrohome"  value="director">Director</input>
        </div>
      </div>
    </div>
  </div>
  </div>

  <div class="row justify-content-center">
  <a  href="#" id="avanzato" style="color:white; margin-top:1.2vh;margin-bottom:1.2vh">Ricerca avanzata </a>
  </div>

  <div id="appari" class="container justify-content-center" style="visibility:hidden;">
    <c>
    <div class="input-group ">

  <div class="col col-lg-2 col-md-2">
    <label class="input-group-text" style="text-align:left !important;">Type</label>
    <select class="custom-select" id="filtroTipo" onchange="getGeneri()">
        <option selected></option>
        <option value="Movie">Movie</option>
        <option value="TV Show">TV Show</option>
    </select>
  </div>


  <div class="col col-lg-3 col-md-3" id="divGenre">
    <label class="input-group-text">Genre</label>
        <select class="custom-select" id="filtroGenre" >
        <option selected ></option>
        </select>
  </div>


  <div class="col col-lg-2 col-md-2">
    <label class="input-group-text">Year</label>
    <input id="annoUscita" type="number" min="1942" max="2020" style="width:5em;" >
  </div>

  <div class="col col-lg-3 col-md-3">
    <label class="input-group-text">OrderBy</label>
    <select class="custom-select" id="filtroOrderBy" >
        <option selected ></option>
        <option value="ASCtitle">ASC title</option>
        <option value="DESCtitle">DESC title</option>
        <option value="ASCreleaseyear">ASC release year</option>
        <option value="DESCreleaseyear">DESC release year</option>
    </select>
  </div>

  <div class="col col-lg-2 col-md-2">
      <label class="input-group-text" >View Cover</label>
      <select class="custom-select" id="viewCover" onchange="getGeneri()">
          <option value="no" selected>no</option>
          <option value="yes">yes</option>

      </select>
  </div>

</div>
</c>
  </div>

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




  echo '<div id="catalogo" class="row justify-content-md-center" style="margin-top:1vh;"></div>';

  ?>

</div>


  </form>

  <!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="proprioqui" class="row align-items-center" style="margin-bottom:20px;">



    </div>

  </div>

</div>

    <script>

// Get the modal
var modal = document.getElementById("myModal");


// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal

function filmSingolo(clicked_id)
{
    var tit=clicked_id;
    var viewCover = document.getElementById("viewCover").value;
    $.post("getSingleFilm.php", {title: tit,viewC:viewCover},function (data,status) {
        $("#proprioqui").html(data);

    });
    modal.style.display = "block";
}
function getGeneri()
{
    var tipo = document.getElementById("filtroTipo").value;


    $.post("getGenreBox.php", {tipo: tipo},function (data,status) {
        $("#filtroGenre").html(data);

    });
}

function getRicerca()
{
    var testoBR = document.getElementById("testoBarraRicerca").value;
    var ele = document.getElementsByName("filtrohome");
    var ricercaper;
    for(i = 0; i < ele.length; i++) {
        if(ele[i].checked)
            ricercaper = ele[i].value;
    }
    var viewCover = document.getElementById("viewCover").value;
    var type = document.getElementById("filtroTipo").value;
    var filtroGenre = document.getElementById("filtroGenre").value;
    var annoUscita = document.getElementById("annoUscita").value;
    var orderby = document.getElementById("filtroOrderBy").value;

    $.post("getCatalogo.php",{text:testoBR,searchby:ricercaper,type:type,genre:filtroGenre,releaseyear:annoUscita,orderby:orderby,viewC:viewCover},function (data,status) {
        $("#catalogo").html(data);
    });


}


// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

<script>
var variabile = document.getElementById("appari");
var button = document.getElementById("avanzato");

button.onclick = function(){
  if (variabile.style.visibility=="hidden") {
    variabile.style.visibility="visible";
  }
  else{
    variabile.style.visibility="hidden";
  }
}
</script>





  </body>
</html>
