### Wordbol – A wordpress plugin integrating Apache Stanbol

This plugin integrates some of[Apache Stanbol's](https://stanbol.apache.org/) features into your [Wordpress](https://wordpress.org/) blog.

#### Features
* Generate links to Wikipedia articles automatically
* Auto-generate maps with locations in post
* Provide useful background information when writing your blog post

#### How to use
1. Download plugin and copy to your plugin directory (usually this is ```/wp-content/plugins```)
2. Set up your Stanbol instance to have an [enhancement engine](http://stanbol.apache.org/docs/0.9.0-incubating/enhancer/engines/) with at least:
  1. One NER engine (tested with OpenNLP NER)
  2. DBpedia Linking
  3. DBpedia Dereferencing
  4. A language detection engine
3. Open config.php, define:
  1. STANBOL_INSTANCE to the correct HTTP endpoint (previously defined in step 2)

#### StanbolEnhancer.php

Apart from being a plugin for WordPress, this project also offers a library, which queries an Apache Stanbol instance from PHP.
You can use this to access Stanbol from within PHP.

##### Usage
###### Run enhancer

```php
$text = "The Stanbol enhancer can detect famous cities such as Paris " +
        "and people such as Bob Marley.";

// enhance text
$enhancer = new WordPressStanbol\Enhancer();
$result   = $enhancer->enhance($text);
```

This will issue an asynchronous request to your configured Stanbol instance.

###### Determine language of text

```php
// retrieve language
$language = $result->get_languages();
/*
array(
  LanguageEnhancement("en", confidence = 100%)
)
*/
```

###### Retrieve all matched entities

```php
// retrieve matched entities
$entities = $result->get_entity_annotations();
/*
EntityStorage(
  TextAnnotation("Merkel", start = 50, end = 56) =>
    array(
      Entity("http://dbpedia.org/page/Angela_Merkel",
             EntityType::Person, confidence = 90%),
      Entity("http://dbpedia.org/page/Merkel,_Texas",
             EntityType::Place, confidence = 30%)
    ),
  TextAnnotation("Barack Obama", start = 100, end = 112) =>
    array(
      Entity("http://dbpedia.org/page/Barack_Obama",
             EntityType::Person, confidence = 100%)
    )
)
```

###### Retrieve further information about an entity

```php
// retrieve special information about an entity
*/
```