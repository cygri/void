### void:inDataset ###
```
void:inDataset a rdf:Property;
    rdfs:label "In Dataset"@en;
    rdfs:comment "Links to the void:Dataset that this document is a part of.";
    rdfs:domain foaf:Document;
    rdfs:range void:Dataset;
    rdfs:subPropertyOf dcterms:isPartOf;
    .
```

### New properties for new stats module ###
```
void:triples a rdf:Property;
    rdfs:label "Triples"@en;
    rdfs:comment "The total number of triples contained in the dataset."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:entities a rdf:Property;
    rdfs:label "Entities"@en;
    rdfs:comment "The total number of entities that are described in the dataset."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:classes a rdf:Property;
    rdfs:label "Classes"@en;
    rdfs:comment "The total number of distinct classes in the dataset. In other words, the number of distinct resources occuring as objects of rdf:type triples in the dataset."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:properties a rdf:Property;
    rdfs:label "Properties"@en;
    rdfs:comment "The total number of distinct properties in the dataset. In other words, the number of distinct resources that occur in the predicate position of triples in the dataset."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:documents a rdf:Property;
    rdfs:label "Documents"@en;
    rdfs:comment "The total number of documents in a dataset, for datasets that are published as a set of individual documents, such as RDF/XML documents or RDFa-annotated web pages. Non-RDF documents, such as web pages in HTML or images, are usually not included in this count. This property is intended for datasets where the total number of triples or entities is hard to determine. void:triples or void:entities should be preferred where practical."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:distinctSubjects a rdf:Property;
    rdfs:label "Distinct Subjects"@en;
    rdfs:comment "The total number of distinct subjects in the dataset. In other words, the number of distinct resources that occur in the subject position of triples in the dataset."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:distinctObjects a rdf:Property;
    rdfs:label "Distinct Objects"@en;
    rdfs:comment "The total number of distinct objects in the dataset. In other words, the number of distinct resources that occur in the object position of triples in the dataset. Literals are included in this count."@en;
    rdfs:domain void:Dataset;
    rdfs:range xsd:integer;
    .
void:class a rdf:Property, owl:FunctionalProperty;
    rdfs:label "Class"@en;
    rdfs:comment "States that the dataset describes only entities of the single class given."@en;
    rdfs:domain void:Dataset;
    rdfs:range rdfs:Class;
    .
void:property a rdf:Property, owl:FunctionalProperty;
    rdfs:label "Property"@en;
    rdfs:comment "States that the dataset contains only triples that have the single given predicate."@en;
    rdfs:domain void:Dataset;
    rdfs:range rdf:Property;
    .
void:classPartition a rdf:Property;
    rdfs:label "Class Partition"@en;
    rdfs:comment "Links a dataset to a class-based partition of the dataset. The class-based partition is a subset that describes only entities of a single class."@en;
    rdfs:subPropertyOf void:subset;
    rdfs:domain void:Dataset;
    rdfs:range void:Dataset;
    .
void:propertyPartition a rdf:Property;
    rdfs:label "Property Partition"@en;
    rdfs:comment "Links a dataset to a property-based partition of the dataset. The property-based partition is a subset that contains only triples having a single property."@en;
    rdfs:subPropertyOf void:subset;
    rdfs:domain void:Dataset;
    rdfs:range void:Dataset;
    .
```

### void:rootResource ###
```
void:rootResource a rdf:Property;
    rdfs:label "Root Resource"@en;
    rdfs:comment "A resource of particular importance in a dataset. All resources in a dataset can be reached by following links from its root resources in a small number of steps."@en;
    rdfs:domain void:Dataset;
    .
```

### void:uriSpace ###
```
void:uriSpace a rdf:Property;
   rdfs:label "URI Space"@en;
   rdfs:comment "A URI that is a common string prefix of all the entity URIs in a void:Datset.";
   rdfs:domain void:Dataset;
   .
```

### void:DatasetDescription ###
```
void:DatasetDescription a rdfs:Class;
    rdfs:label "Dataset Description"@en;
    rdfs:comment "A web resource whose foaf:primaryTopic or foaf:topics include void:Datasets."@en;
    rdfs:subClassOf foaf:Document;
    .
```

### Removal of old terms ###
```
void:statItem a owl:DeprecatedProperty .
```

Also, Add something like “This property is deprecated.” to the rdfs:comment of void:statItem.

And remove those instances:

```
void:numberOfTriples 
void:numberOfResources
void:numberOfDocuments
void:numberOfDistinctSubjects
void:numberOfDistinctObjects
```