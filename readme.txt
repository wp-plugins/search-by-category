=== Search By Category ===
Contributors: Fire G
Plugin link: http://fire-studios.com/blog/search-by-category/
Tags: search, category, specify, results
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 1.2

Reconfigures search results to display results based off of category of posts.

== Description ==

To help users find the posts they're looking for faster, this plugin allows them to search for articles or posts within certian categories, cutting back on the number of results the user needs to crawl through to find the article they want.

**Change log**

_1.2_
 - Included Shortcode: [sbc]

1.1
 - Added security fixes
 - Removed some excess code

1.0.0
 - Default text
 - Custom styling

Beta 3
 - Search Text
 - Exclude Child categories
 - search box auto empties and refills if nothing entered

Beta 2
 - First complete working version
 - Hide Empty
 - Focus
 - Exclude Categories

Beta 1
 - First working version
 - Category exclustion from drop-down list isn't functional

Alpha 1
 - All functions are present but independent

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. activate the plugin within you WordPress Administration
3. Go to Settings > Search By Category
4. Use the following code in your Theme:
<pre>
&lt;?php if(function_exists('sbc')){ 
	sbc();
} else { ?&gt;
	// Your regular form code goes here
&lt;?php } ?&gt;
</pre>
or
<pre>
[sbc]
</pre>

== Screenshots ==

1. SBC From with custom styling
2. SBC config page