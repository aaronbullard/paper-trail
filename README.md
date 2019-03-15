# Paper Trail
[![Maintainability](https://api.codeclimate.com/v1/badges/df4f5f45e9c7dd3b3d5a/maintainability)](https://codeclimate.com/github/aaronbullard/paper-trail/maintainability)

JSON record version control for PHP

## Summary
Paper Trail uses the JSON Patch standard to record iterative changes to a document.  Each change is diffed to the previous version, timestamped, and cached.  This record of changes can be stringified as JSON for persistence.  This gives the user a running history of every change and thus each version.

## Installation
-----
### Library

```bash
git clone git@github.com:aaronbullard/paper-trail.git
```

### Composer
[Install PHP Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require aaronbullard/paper-trail
```

### Testing
```bash
composer test
```

## Entry Points
----
- PaperTrail\RecordStore::create(Record $record = null) - Creates the RecordStore manager
- PaperTrail\RecordStore::save($doc, $comment = null) - Saves a new verion of a StdClass or Array
- PaperTrail\RecordStore::getVersion($version) - Returns the version of a document
- PaperTrail\RecordStore::getLatest() - Returns the last version of a document
- PaperTrail\RecordStore::getHistory() - Returns version history of the document

## Examples
----

```php
<?php

use PaperTrail\RecordStore;

$store = RecordStore::create();

$version1 = [
    "library" =>  "My Personal Library",
    "total" =>  2,
    "books" =>  [
        [ "title" => "Title 1", "author" => "Jane Doe" ],
        [ "title" => "Title 2", "author" => "John Doe" ]
    ]
];

$version2 = [
    "library" =>  "My Personal Library",
    "total" =>  3,
    "books" =>  [
        [ "title" => "Title 1", "author" => "Jane Doe" ],
        [ "title" => "Title 2", "author" => "John Doe" ],
        [ "title" => "Title 3", "author" => "Jack Doe" ]
    ]
];

$store->save($version1);
$doc = $store->getLatest();
$this->assertCount(2, $doc['books']);

// Update document
$store->save($version2, "Added a book");

// Cast record to JSON for persistence
$jsonRecord = $store->toJson();
// echo $jsonRecord;
/*
{"commits":[{"version":1,"patch":[{"value":"My Personal Library","op":"add","path":"\/library"},{"value":2,"op":"add","path":"\/total"},{"value":[{"title":"Title 1","author":"Jane Doe"},{"title":"Title 2","author":"John Doe"}],"op":"add","path":"\/books"}],"timestamp":1552678141,"comment":null},{"version":2,"patch":[{"value":2,"op":"test","path":"\/total"},{"value":3,"op":"replace","path":"\/total"},{"value":{"title":"Title 3","author":"Jack Doe"},"op":"add","path":"\/books\/2"}],"timestamp":1552678141,"comment":null}]}
*/

// Hydrate the RecordStore from your JSON record
$store = RecordStore::fromJson($jsonRecord);

// Get array of each version eg. {version, timestamp, comment, document}
$versions = $store->getHistory();
$this->assertCount(2, $versions);
$this->assertCount(3, $versions[1]['document']['books']);
$this->assertEquals("Added a book", $versions[1]['comment']);

// Retrieve a version
$this->assertCount(2, $store->getVersion(1)['books']);
$this->assertCount(3, $store->getVersion(2)['books']);

```

For more examples, see the tests: `tests\FunctionalTests\RecordStoreFunctionalTest.php`