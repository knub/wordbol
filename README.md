### Wordbol – A wordpress plugin integrating Apache Stanbol

This plugin integrates some of[Apache Stanbol's](https://stanbol.apache.org/) features into your [Wordpress](https://wordpress.org/) blog.

#### Features
* Generate links to Wikipedia articles automatically
* Auto-generate maps with locations in post
* Provide useful background information when writing your blog post

#### How to use
1. Download plugin and copy to your plugin directory
2. Setup your Stanbol instance to
3. Open config.php,

#### StanbolEnhancer.php

Apart from being a plugin for WordPress, this project also offers a library, which queries an Apache Stanbol instance from PHP.
You can use this to access Stanbol from within PHP.

##### Usage
```php
$text = "The Stanbol enhancer can detect famous cities such as Paris " +
        "and people such as Bob Marley.";

$enhancer = new WordPressStanbol\Enhancer();
$enhancer->enhance($text);
```
This will issue an asynchronous request to your configured Stanbol instance.