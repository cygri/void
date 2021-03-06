# Introduction #

This page collects SPARQL queries for computing various statistics that can be expressed using the voiD vocabulary.

An implementation based on these queries is available here: [make-void](http://github.com/cygri/make-void)

# Expressible statistics #

Here are some candidates that we would like to be able to express using voiD and that can be translated as SPARQL queries. Note, the assumption is that it's going to be SPARQL 1.1. For a void:Dataset, we would like to express:
  * total number of triples
  * total number of entities (new)
  * total number of distinct resource URIs (deprecated)
  * total number of distinct classes // the number of distinct resources occuring as objects of rdf:type (new)
  * total number of distinct properties (new)
  * total number of distinct subject nodes
  * total number of distinct object nodes
  * exhaustive list of classes used in the dataset
  * exhaustive list of properties used in the dataset
  * table: class vs. total number of instances of the class
  * table: property vs. total number of triples using the property
  * table: property vs. total number of distinct subjects in triples using the prop (??)
  * table: property vs. total number of distinct objects in triples using the prop (??)
  * list of all domain names occurring in URIs in subjects or objects (deprecated??)
  * table: domain names vs. number of triples where the domain name occurs in subject or object (deprecated??)

# SPARQL queries #

## Notes ##
  * All queries are executed against the default graph. Queries might need modification if only specific named graphs should be taken into account.

## total number of triples ##
```
SELECT (COUNT(*) AS ?no) { ?s ?p ?o  }
```

## total number of entities ##
```
SELECT COUNT(distinct ?s) AS ?no { ?s a []  }
```

## total number of distinct resource URIs (deprecated??) ##
```
SELECT (COUNT(DISTINCT ?s ) AS ?no) { { ?s ?p ?o  } UNION { ?o ?p ?s } FILTER(!isBlank(?s) && !isLiteral(?s)) } 	
```

## total number of distinct classes ##
```
SELECT COUNT(distinct ?o) AS ?no { ?s rdf:type ?o }
```

## total number of distinct predicates ##
```
SELECT count(distinct ?p) { ?s ?p ?o }
```

## total number of distinct subject nodes ##
```
SELECT (COUNT(DISTINCT ?s ) AS ?no) {  ?s ?p ?o   } 
```

## total number of distinct object nodes ##
```
SELECT (COUNT(DISTINCT ?o ) AS ?no) {  ?s ?p ?o  filter(!isLiteral(?o)) } 				
```

## exhaustive list of classes used in the dataset ##
```
SELECT DISTINCT ?type { ?s a ?type }
```

## exhaustive list of properties used in the dataset ##
```
SELECT DISTINCT ?p { ?s ?p ?o }
```

## table: class vs. total number of instances of the class ##
```
SELECT  ?class (COUNT(?s) AS ?count ) { ?s a ?class } GROUP BY ?class ORDER BY ?count
```

## table: property vs. total number of triples using the property ##
```
SELECT  ?p (COUNT(?s) AS ?count ) { ?s ?p ?o } GROUP BY ?p ORDER BY ?count
```

## table: property vs. total number of distinct subjects in triples using the property ##
```
SELECT  ?p (COUNT(DISTINCT ?s ) AS ?count ) { ?s ?p ?o } GROUP BY ?p ORDER BY ?count
```

## table: property vs. total number of distinct objects in triples using the property ##
```
SELECT  ?p (COUNT(DISTINCT ?o ) AS ?count ) { ?s ?p ?o } GROUP BY ?p ORDER BY ?count
```

## list of all domain names occurring in URIs in subjects or objects ##
Can't be done yet in SPARQL 1.1, there is no way of assigning the result of an expression to a result variable

## table: domain names vs. number of triples where the domain name occurs in subject or object ##
Can't be done yet in SPARQL 1.1, there is no way of assigning the result of an expression to a result variable