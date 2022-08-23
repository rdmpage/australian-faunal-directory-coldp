# Australian Faunal Directory (AFD) as a Catalogue of Life Data Package (ColDP)

A mapping between the [Australian Faunal Directory(AFD)](https://biodiversity.org.au/afd/home) and bibliographic databases such as the Biodiversity Heritage Library, CrossRef, and Wikidata.

The Australian Faunal Directory is an online catalogue of taxonomic and biological information on all animal species known to occur within Australia.

The data from AFD is available under a [CC-BY 4.0](https://creativecommons.org/licenses/by/4.0/) license, see [copyright notice on the Department of Climate Change, Energy, the Environment and Water website](https://www.dcceew.gov.au/about/copyright) and [Australian Faunal Directory page in Atlas of Living Australia](https://collections.ala.org.au/public/showDataResource/dr2699).

This repository is based on earlier work [harvesting AFD](https://github.com/rdmpage/afd-harvest) and constructing the  [Ozymandias](https://ozymandias-demo.herokuapp.com) knowledge graph based on that data, see Page 2019.

> Page RDM. 2019. Ozymandias: a biodiversity knowledge graph. PeerJ 7:e6739 [https://doi.org/10.7717/peerj.6739](https://doi.org/10.7717/peerj.6739)

## Format Definition

Field name | Description
-- | --
CAVS_CODE | The CAVS code if available.
CAAB_CODE | The CAAB code if available.
NAMES_VARIOUS | This field holds either the species group name or the common name.
SCIENTIFIC_NAME | The full scientific name and authority.
FAMILY | The family scientific name.
GENUS | The genus scientific name.
SUBGENUS | The subgenus scientific name.
SPECIES | The species scientific name.
SUBSPECIES | The subspecies scientific name.
NAME_TYPE | The classification of this name:
. | Valid
. | A valid scientific name.
. | Common
. | A common name.
. | Synonym
. | Taxonomically an accepted available name, but in this instance, for convenience, including the categories of names listed below.
. | Miscellaneous Literature Name
. | A literature synonym.
NAME_SUBTYPE | Further classification on the name type:
. | Valid name
. | Valid names are not broken down into subtypes.
. | Common name
. | Subtypes are either "Preferred" or "General".
. | Synonym
. | Subtypes can be "synonym", "nomen nudum", "replacement name", "invalid name", "original spelling", "subsequent misspelling", "emendation", "nomen dubium", "objective synonym", "subjective synonym", "junior homonym", "nomem oblitum" or "nomen protectum".
. | Miscellaneous Literature Name
. | Miscellaneous literature names are not broken down into subtypes.
RANK | The rank of this taxon.
QUALIFICATION | Qualification or comments for the taxon.
AUTHOR | The authority author name.
YEAR | The authority year.
ORIG_COMBINATION | Whether this is an original combination, either 'Y', 'N' or empty when not applicable.
NAME_GUID | The GUID of the name.
NAME_LAST_UPDATE | The time at which this name was last updated.
TAXON_GUID | The GUID of the AFD taxonomic concept with which this name is associated.
TAXON_LAST_UPDATE | The time at which this taxonomic concept was last updated.
TAXON_PARENT_GUID | The GUID of the parent AFD taxonomic concept.

Fields relating to the primary reference

Field name | Description
-- | --
CONCEPT_GUID | The GUID of taxonomic concept of the primary reference.
. | For valid names this will be the same as the TAXON_GUID. The publication fields will be empty, as the publication is this directory itself.
PUB_AUTHOR | The author of the publication.
PUB_YEAR | The year of the publication.
PUB_TITLE | The title of the publication.
PUB_PAGES | The pages referenced.
PUB_PARENT_BOOK_TITLE | The title of the book in which the chapter occurs, if applicable.
PUB_PARENT_JOURNAL_TITLE | The title of the journal in which the article occurs, if applicable.
PUB_PARENT_ARTICLE_TITLE | The title of the article in which the section occurs, if applicable.
PUB_PUBLICATION_DATE | The publication date.
PUB_PUBLISHER | The publisher.
PUB_FORMATTED | The formatted version of this publication.
PUB_QUALIFICATION | Qualification and comments about this publication.
PUB_TYPE | Type of publication reference:
. | Book
. | A book
. | Chapter in a Book
. | A chapter within a book
. | Article in Journal
. | An article within a journal
. | Section in an Article
. | A section within a article in a journal
. | URL
. | A website URL
. | This Work
. | A volume of the AFD
. | Miscellaneous 
. | A miscellaneous publication
PUBLICATION_GUID | The GUID for this publication record.
PUBLICATION_LAST_UPDATE | The timestamp of the last update to this publication.
PARENT_PUBLICATION_GUID | The GUID for the publication containing this publication (if any).



## Step-by-step guide

- create a GitHub repository

- create a SQLite database to store and manage the data (you can use whatever data management tool(s) you like. If your database is larger than 100 MB you will have to install [Git Large File Storage (LFS)](https://git-lfs.github.com). Once installed, `git lfs track *.db` will enable GitHub to accept a large SQLite database. Note that Mac users with [Homebrew](https://brew.sh) can simply `brew install git-lfs`.

- the release should only include ColDP files so anything else should not be in the release. Add any unwanted files to a file called `.gitattributes`. For example:
```
/code export-ignore
/data export-ignore
*.db export-ignore
*.gitattributes export-ignore
*.gitignore export-ignore
```
- download bibliography CSV files from AFD. 

- the CSV files need to be converted to UTF-8 encoding using `convert-encoding.php`.

- convert to SQL `php tosql-bib.php > bib.sql`

- import CSV files into database `sqlite3 ../afd.db ".read bib.sql"`. The use of “.read” seems to avoid character encoding issues (see https://stackoverflow.com/a/36468283/9684 ).




