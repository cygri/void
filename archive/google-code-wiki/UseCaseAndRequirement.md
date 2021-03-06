# Use Cases and Requirements Regarding the Discovery and Usage of Linked Datasets on the Web of Data #

## Abstract ##

This document describes use cases and lists requirements regarding the discovery and usage of linked datasets. First the use cases are presented that in turn motivate requirements. One purpose of this document is to establish an objective measure for reviewing existing technologies and standards for their suitability regarding the main desideratum of our work on the discovery and usage of linked datasets [LD](LD.md).  A Linked Dataset is understood to be a collection of data, published and maintained by a single provider, available as RDF on the web, where at least some of the resources in the dataset are identified by dereferencable URIs.


[@Comment: Should/could we go further and state that a dataset is a collection of data available within a common URIspace - ie: not distributed across multiple domains?]

## Scope ##

The intended audience are providers and consumers of RDF datasets available on the web; it is assumed that the reader is familiar with the linked data principles [LD-P] and with the basic Web of Data technologies RDF and SPARQL.

Note: Requirements in this document are deliberately kept on an abstract level.

There are some basic requirements from [AWWW1](AWWW1.md) assumed that are not listed explicitly in the following. These include but are not limited to:
  * scalability
  * openness
  * ...

The requirements, below, are separated into user-driven requirements and technology-driven requirements in order to better assess the intention of it and allow a straight-forward assignment of priorities.

## Use Cases ##
### UC1 Dataset Selection (Michael) ###

A dataset consumer may have discovered datasets (for example through means such as described in UC2). The question arises then how to select appropriate datasets from this list of potential candidates.

The 'appropriateness' could be based on (at least) the following criteria:
  * the content of the dataset, that is what is the dataset (mainly) about; based on some kind of categorization scheme a selection could take place
  * the interlinking to other datasets, that is to which and how is the dataset interlinked with other datasets

Both criteria can be understood in terms of quality and quantity.

### UC2 Discovery of Datasets (Richard) ###

As operator of the Sindice crawler, I want to discover detailed descriptions of datasets (for purposes of this use case, that's a collection of RDF documents published on the Web). The most important situations are:
  * A crawler has stumbled upon an individual RDF document in the collection. How does it discover metadata about the entire dataset?
  * A crawler has discovered a web page that is part of the same site as the dataset. How does it discover the metadata about the dataset starting from the web page?

Sindice already uses Semantic Sitemaps to enable discovery and efficient processing of datasets. It seems natural to address the situations above by building on Semantic Sitemaps. But other solutions can be considered as well.

The key question is: How does a crawler discover voiD descriptions of datasets? The simplest approach (just putting the voiD description online and linking it from somewhere on the site) does not meet out needs. This way it could take the crawler a long time until it finds the description. It is important that the voiD description is discoverable as soon as the crawler has found the first RDF document, because we want to have the voiD metadata before processing the RDF document.

### UC3 Advertising Dataset (Keith) ###

As a dataset publisher, I want to be able to publish metadata about the dataset such that
  * it can be found and aggregated by search engine applications
  * the metadata can enable my dataset to be found in relevant searches
  * the dataset can provide clear licensing information so consumers can know how they can use it

### UC4 Trust Evaluation (Jun) ###

As a user of a linked dataset, I want to apply a metric to automatically compute the trust value of this dataset. I need the provenance information about the dataset, including:
  * creator and provider of the dataset
  * creation method (e.g. extracted from a database, derived from a data mining process)
  * creation time
  * publisher and publication time of the original source

Possible information:
  * a second creator that published the same dataset
  * a digital signature from the creator

As a publisher of a linked dataset, I want my data to be more trusted by users. I need to express the following provenance information about my dataset:
  * the origin of my linked data, i.e. where it came from
  * the owner of the original data
  * the creation and publication date
  * the method used for creating the dataset
  * the publisher
  * the version of the original data source on which the published dataset is based

### UC5 linked data cloud in RDF (Richard) ###

Express the information encoded in Richard's Linking Open Data cloud picture in RDF.

Information per dataset:
  * name
  * short text description
  * Link to main page
  * Rough number of triples or resources (with link to source for number)
  * email address and name of technical contact person
  * example resources with label

Information about links to other datasets
  * from where to where (direction!)
  * rough number of triples (with link to source for number)

### UC6 expressing the Sindice Map of Data in RDF (Richard) ###

Express the information collected in the Sindice Map of Data in RDF to make it browsable with Tabulator and queryable.

Information per dataset:
  * name
  * short text description
  * Link to main page
  * Types of entities, with entity count
  * example resources with label

Additional requirements:
  * Group datasets by topic into a SKOS hierarchy
  * Support datasets published in RDFa and microformats
  * State which serialization (RDF/XML, N3, RDFa, uF) is used
  * State which microformat is used
  * State which vocabularies/ontologies are used

### UC7: expressing research data published by individuals ###

As a developer working together with biologists, I want to help them to find research data published by their peer colleagues, that are often produced for a particular experiment for a particular study or publication. We would need to have the following information about each dataset:
  * in what form the dataset was published, as a zip file, a sql-dump, an excel spreadsheet, a sparql endpoint, or a linked dataset
  * what data object is contained in the databases, e.g. which gene, which virus, or vaccine
  * where the dataset has been published, including information about the publisher, a doi reference to the publisher, a bib description about the publication
  * who should be the person in contact
  * when the dataset was first published/updated
  * what copyright is used for the dataset

### UC8: expressing research data published by database owners ###

As a developer working together with biologists, I want to help them to find research data published by major databases as linked datasets in order to answer questions that require more than one data source. We would need to have the following information about each dataset:
  * when the dataset was first published/updated
  * what copyright is used for the dataset
  * who should be the person in contact
  * what typical URIs are used in the LOD so that I can know how I can query it and how it relates to other dataset
  * if the dataset is an integration of other data sources and if it published different version of LOD according to each source data, I want to know which version of the LOD I am accessing now, the source data it integrates and which version of the source data it integrates

### UC9: Federating datasets with SPARQL ###

There is an increasing number of projects that aim to federate several SPARQL-accessible datasets into a single, virtual dataset that can be queried with SPARQL. These efforts include DARQ, SemWIQ and OpenLink Virtuoso. To federate datasets, the federation component needs some information about the data that is contained in each of the datasets. This enables smart distribution of a query across the different parts. The general assumption in these projects is that each dataset is described by some sort of “SPARQL endpoint description”, and that these descriptions are made available to the federation component. The descriptions might be provided by the individual endpoint operators, or might be created by the operator of the federation component by inspecting the endpoints. The descriptions usually contain some summary of the kind of triples contained in a dataset, and statistical information about triple counts, property selectivities and so on.

This use case is not concerned with how the federation component gains access to the individual descriptions. It is only about what information goes into the descriptions.

The projects make different proposals for what information should be put into the descriptions: triple patterns, URI patterns, selectivities, histograms. It is not yet clear which information is most useful to enable efficient federation. Therefore, voiD should not aim to replace those proposals or to solve the federation problem.

However, voiD should aim at providing a framework for a possible future unified SPARQL endpoint description language. The detailed information might be realized as extensions to voiD. If we can identify patterns that seem to be part of several proposals, then we should consider integrating them into voiD in anticipation of future work on SPARQL federation.

Some projects and proposals in this area:
  * DARQ, with its DOSE vocabulary
  * SemWIQ and RDFStats
  * Greg Williams made a proposal roughly based on the abandoned W3C SADDLE draft
  * Orri Erling's wishlist for voiD

## User-driven Requirements ##

In the following user-driven requirements (UR) are listed. Currently the order is largely random; no priorities have been assigned so far.
UR0 - Determine Nature of a Dataset

Given a dataset DSx available on the Web, I want to determine if it belongs to the so called LOD cloud [LOD-C]. This effectively means that I want to check if DSx is compliant to the linked data principles [LD-P] making it a linked dataset (LD).


[[@@COMMENTS: how about the requirement of determining the access mechanism of linked dataset?? (Michael is going to re-phrase this as one of the requirements?)]]


### UR1 - Content of a Linked Dataset ###

I want to know what kind of information I can expect from a LD. For example I might be looking for LD that contains (geo) location data or a LD that has information about books, etc. Even though there may be LD that do not focus on one single topic (such as DBpedia), usually one ore more main topics can be identified. Note: This UR influences UR4.


### UR2 - Assess Interlinking Quantity ###

I want to assess the quantity of the interlinking between two given LD regarding a certain RDF property such as owl:sameAs, foaf:topic, etc,

### UR3 - Assess Interlinking Quality ###

I want to assess the quality of interlinking between two LD sets to select between LD sets which offer the same or similar content


### UR4 - Efficient selection of a Linked Dataset ###

Based on UR1 I want to efficiently select a certain LD, that is, given that there are several LD that offer data about a certain topic I want to choose the most promising, reliable, widely-used, stable, etc.


### UR5 - Visualisation of Interlinking ###

It should be possible to generate a visual representation of the interlinking of two or more LD automatically.


### UR6 - Origin of Data ###

It should be possible for the provider of a dataset to state who the author(s)/creator(s) are, how the data was created, and, if the RDF version was generated  from another source, what that source was, and what software performed the transformation. With this information, users can cite the appropriate source, contact the creators, and look up the generating software as necessary.


Please see Use case 4.

## Technology-driven requirements ##

In the following technology-driven requirements (TR) are listed. Currently the order is random; no priorities have been assigned so far.


### TR1 - Basic WoD compliance ###

Any technologies that enables the UR listed above MUST be compliant to the Web of Data (WoD), hence needs to be based on URIs, RDF (and HTTP).


### TR2 - WoD alignment ###

Any solution that addresses the UR above should support widely deployed WoD technologies as listed in the following:
  * SPARQL
  * (X)HTML

## References ##

  * [LD-P] Linked Data - Design Issues. Tim Berners-Lee. See http://www.w3.org/DesignIssues/LinkedData.html
  * [AWWW1](AWWW1.md) Architecture of the World Wide Web, Volume One. Ian Jacobs and Norman Walsh. See http://www.w3.org/TR/webarch/
  * [LOD-C] The Linking Open Data dataset cloud. Richard Cyganiak. See http://richard.cyganiak.de/2007/10/lod/


## Further Resources & Supportive Material ##
  * http://community.linkeddata.org/MediaWiki/index.php?MetaLOD#Requirements
  * http://community.linkeddata.org/MediaWiki/index.php?VoiD#Use_Cases
  * http://lists.w3.org/Archives/Public/public-lod/2008Aug/0037.html (discussion regarding measuring interlinking)