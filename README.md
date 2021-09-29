# NetflixGraph

Database version 3.5.19 Enterprise

DATABASE SNIPPETS

-------------------------------------------------------------------------------------------

IMPORT TITLES:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.title is not null and line.date_added is not null and line.release_year is not null and line.duration is not null and line.description is not null
CREATE (:Title{titleName:line.title,dateAdded:line.date_added,releaseYear:toInteger(line.release_year),duration:line.duration,description:line.description})

--------------------------------------------------------------------------------------------

DELETE RELATIONSHIP:
MATCH ()-[r:ACTED_IN]->()
DELETE r

--------------------------------------------------------------------------------------------

FILMED_IN:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.country is not null and line.title is not null
UNWIND split(line.country, ',') AS cou
MERGE (c:Country{countryName:cou})
MERGE (t:Title{titleName:line.title})
CREATE (t)-[:FILMED_IN]->(c)


ACTED_IN:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.cast is not null and line.title is not null
UNWIND split(line.cast, ',') AS actor
MERGE (a:Actor{actorName:actor})
MERGE (t:Title{titleName:line.title})
CREATE (a)-[:ACTED_IN]->(t)


DIRECTED_BY:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.director is not null and line.title is not null
UNWIND split(line.director, ',') AS dir
MERGE (d:Director{directorName:dir})
MERGE (t:Title{titleName:line.title})
CREATE (t)-[:DIRECTED_BY]->(d)


LISTED_IN:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.listed_in is not null and line.title is not null
UNWIND split(line.listed_in, ',') AS gen
MERGE (g:Genre{genre:gen})
MERGE (t:Title{titleName:line.title})
CREATE (t)-[:LISTED_IN]->(g)


RATED_IN:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.rating is not null and line.title is not null
UNWIND split(line.rating, ',') AS rat
MERGE (r:Rating{rating:rat})
MERGE (t:Title{titleName:line.title})
CREATE (t)-[:RATED]->(r)


TYPE_OF:
LOAD CSV WITH HEADERS FROM "file:///netflix_titles.csv" AS line with line where line.type is not null and line.title is not null
MERGE (x:Type{typeName:line.type})
MERGE (t:Title{titleName:line.title})
CREATE (t)-[:TYPE_OF]->(x)

--------------------------------------------------------------------------------------------

Query per film singolo:
match (a:Actor),(c:Country),(d:Director),(g:Genre),(r:Rating),(t:Title),(ti:Type)
WHERE t.titleName="Apaches" AND (a)-[:ACTED_IN]->(t) AND (t)-[:DIRECTED_BY]->(d) AND (t)-[:FILMED_IN]->(c) AND (t)-[:LISTED_IN]->(g) AND (t)-[:RATED]->(r) AND (t)-[:TYPE_OF]->(ti)
RETURN t.duratduration,t.releaseYear,r.rating,t.description,d.directorName,a.actoractorName,g.genre,t.dateAdded


//Query per farti restituire i generi di tipo "movie"
MATCH (t:Title),(g:Genre),(tp:Type)
WHERE tp.typeName="Movie" AND NOT(g.genre CONTAINS "Shows") AND (t)-[:TYPE_OF]->(tp) AND (t)-[:LISTED_IN]->(g)
RETURN DISTINCT g.genre

//Query per farti restituire i generi di tipo "tv shows"
MATCH (t:Title),(g:Genre),(tp:Type)
WHERE tp.typeName="TV Show" AND NOT(g.genre CONTAINS "Movies") AND (t)-[:TYPE_OF]->(tp) AND (t)-[:LISTED_IN]->(g)
RETURN DISTINCT g.genre
