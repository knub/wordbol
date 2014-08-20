=== wordbol ===
Contributors: knub
Tags: apache stanbol,stanbol,semantic lifting,semantic enhancement,semantic web,semantic,linking,wikipedia,entity,ner,named-entity-recognition
Requires at least: 3.0
Tested up to: 3.9
Stable Tag: 1.0
License: MIT
License URI: http://opensource.org/licenses/MIT

Integration of Apache Stanbol in WordPress.
Enhance your articles with semantic information.

== Description ==
This plugin integrates some of [Apache Stanbol](https://stanbol.apache.org/)'s features into your [WordPress](https://wordpress.org/) blog.

= Video =
You can find video showcasing the plugin's features on [Youtube](http://youtu.be/Aut3ziKSinE).

= Features =
* Generate links to Wikipedia articles automatically
* Generate maps with locations mentioned in the post
* Provide useful background information when writing your blog post
* Auto-tag your posts

= Problems? =
Feel free to contact me! I worked hard to make the current feature set stable, but as this is my first plugin chance is that something's still rough.

== Installation ==

1. Download plugin and copy to your plugin directory (usually this is ```/wp-content/plugins```)
2. Set up your Stanbol instance to have an [enhancement engine](http://stanbol.apache.org/docs/0.9.0-incubating/enhancer/engines/) with at least:
  1. One NER engine (tested with OpenNLP NER)
  2. DBpedia Linking
  3. DBpedia Dereferencing
  4. A language detection engine
3. In case you want to recognize special entities, you probably need to add a training model to your Stanbol enhancement engines. The standard model works well for politicians, countries etc., but fails for sportspersons.
4. Open config.php, define:
  1. ```STANBOL_INSTANCE``` to the correct HTTP endpoint (previously defined in step 2)
  2. ```GOOGLE_MAPS_API_KEY``` to your Google Maps API key (get one at [Google's API console](https://code.google.com/apis/console/))
  3. Have a look at the other settings as well.
5. Activate the plugin.

== Screenshots ==

1. This shows the selected entities to auto-link and auto-tag (North Carolina and Barack Obama), the infobx with detailed information about Barack Obama, and the auto-created map, which currently displays New York, India, and some other state in the USA. Also note the tags on the right side, which have already been added.

== Changelog ==

= 1.0 =
* First release
