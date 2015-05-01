Based on [Issue 39](https://code.google.com/p/void-impl/issues/detail?id=39): http://code.google.com/p/void-impl/issues/detail?id=39

The goal of this survey is to understand how void:feature is being used by early adopters and estimate potential impact for proposing alternative patterns in voiD 2.0.

## Query to the Keith's Talis Store ##

  * http://tinyurl.com/y94q264 is a query for  features used in existing void descriptions - there aren't many (K.J.W.Alexander). The result shows the following URIs used in voiD descriptions:
    1. RDF/JSON serialisation:(http://api.talis.com/stores/kwijibo-dev3/items/features/RDF_JSON)
    1. Turtle RDF serialisation: (http://api.talis.com/stores/kwijibo-dev3/items/features/Turtle_RDF)
    1. RDF/XML (http://neuroweb.med.yale.edu/senselab/senselab-void.ttlRDFXML)
    1. RDF/XML serialisation (http://api.talis.com/stores/kwijibo-dev3/items/features/rdfxml)
    1. Turtle (http://telegraphis.net/data/void#Turtle)
    1. HTTP Content Negotiation (http://api.talis.com/stores/kwijibo-dev3/items/features/Content_Negotiation)
    1. RDF/XML (http://telegraphis.net/data/void#RDFXML)
    1. N-Triples RDF Serialisation (http://api.talis.com/stores/kwijibo-dev3/items/features/N-Triples)
    1. N3 (http://telegraphis.net/data/void#N3)

## Terms used in the ESW wiki ##
The following terms are used by people to describe their RDF data dump, as summarized at http://esw.w3.org/topic/DataSetRDFDumps:

  * RDF/XML
  * OWL
  * RDF/OWL
  * RDFS
  * SKOS
  * N3
  * N-Triples
  * Turtle
  * TSV
  * tab-separated values
  * CSV

Not appeared in the current datasets yet:
  * RDFa
  * RDF/JSON

A lot of RDF dumps are made available as a zip file, making the actual technical features of these data invisible. They are data dump format not for describing data serialization format, which is the focus of the technical feature property.


## Search in Sindice ##
Only one instance was returned, whose features are defined in a locate domain:
  * http://telegraphis.net/data/void#Turtle
  * http://telegraphis.net/data/void#RDFXML

## Search in voiD/RKB ##
No sign of void:feature or dcterms:format.

## Search in Openlink voiD Store ##

@@@TODO: http://lod.openlinksw.com/void/Dataset

## Search in CKAN ##
http://ckan.net/group/lod has big overlaps to the ESW wiki.
