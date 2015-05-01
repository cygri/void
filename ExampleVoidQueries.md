# By Category #

Returns all datasets that are about [Computer Science](http://dbpedia.org/resource/Computer_science).

```
SELECT DISTINCT ?dataset
 WHERE {
   ?dataset a void:Dataset .
   ?dataset dcterms:subject <http://dbpedia.org/resource/Computer_science> .
 }
```


# By Interlinking with Certain Dataset #

Returns all datasets that contain links that interlink them with the Geonames dataset.

```
SELECT DISTINCT ?dataset
 WHERE {
   ?dataset a void:Dataset .
   ?linkset void:target ?dataset ;
   	    void:target :Geonames .
 }
```

# By Certain Interlinking Type #
```
SELECT ?dataset 
 WHERE { 
   ?dataset a void:Dataset ;
            void:subset ?linkset .
   ?linkset void:subjectsTarget ?dataset ;
            void:objectsTarget :Geonames;
            void:linkPredicate foaf:based_near .
 }
```

Returns all datasets that contain `foaf:based_near` links to the Geonames dataset.

# By Vocabulary Used #
```
SELECT ?dataset
WHERE {
   ?dataset a void:Dataset ;
            void:vocabulary <http://xmlns.com/foaf/0.1/> .
}
```

Returns all dataset that use the FOAF vocabulary.

# By URI (Regex) Pattern #

If we have a URI `http://dbpedia.org/resource/Amsterdam`, and we want to find datasets that might contain triples using that URI, we can do the following query:

```
SELECT ?dataset
 WHERE {
   ?dataset a void:Dataset .
   ?dataset void:uriRegexPattern ?pattern .
   FILTER(REGEX("http://dbpedia.org/resource/Amsterdam", ?pattern))
 }
```

Returns all datasets with a URI Regex Pattern that matches `"http://dbpedia.org/resource/Amsterdam"`.

# By the Identifier of a Dataset #
If multiple voiD descriptions about a dataset are published by different parties, this dataset might be given different URIs in each voiD description. Thus, we will use the `foaf:homepage` property, which uniquely identifies the dataset, to gather these descriptions, smushing them under a single URI.

```
CONSTRUCT {
 :DBpedia ?p ?o
}
WHERE {
  ?dataset a void:Dataset; foaf:homepage <http://dbpedia.org> ; ?p ?o .
}
```

Smushes all the properties of any dataset description with a [foaf:homepage](http://xmlns.com/foaf/spec/homepage) property of [http://dpbedia.org/](http://dpbedia.org), into a single RDF description.