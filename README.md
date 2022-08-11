This is a **WordPress plugin** for consuming and integrating the **Cruise Factory XML service**.

Installation
------------
* Download the zipped package ready for install in the releases section.
* Upload the `atd-cruisefactory-xml.zip` file into your WordPress website via the Plugins page.
* Activate the plugin

The Cruise Factory XML service requires an active Cruise Factory account. Please contact us to get setup using the Cruise Factory data platform.

Once you have an active XML service key and verified the plugin in your WordPress admin, you should run the following WP-CLI command on your server:

```
wp atd import services
```

This will import all content available to your account.

Documentation
-------------
A quick guide to important features can be read below. For a more indepth documentation please [visit here](https://www.agenttraveldata.com.au/wordpress/xml-plugin).
### Templates/Themes
The template files for the plugin are located in the `atd-cruisefactory-xml/templates` directory. These should not be edited directly, instead you should copy the `templates` directory of the plugin folder into your theme folder as the name `cruisefactory`.
* `atd-cruisefactory-xml/templates` -> `theme-folder/cruisefactory`

### Command Line (WP-CLI)
The plugin uses [WP-CLI](https://wp-cli.org/) to manage the potentially large amounts of data transfer into your WordPress site.

The plugin commands are as follows:
```
wp atd import [services|increment]

  [<name>...]
  The name of the feed to import.
    ---
  default: all
  options:
  - all
  - destinations
  - cruise-lines
  - ships
  - cabins
  - decks
  - ports
  - departures
  - special-departures
  - cruise-pricing

  [--wordpress=<wordpress>]
  Whether to import XML data as WordPress posts
    ---
  default: import
  options:
  - import
  - exclude
  - only

  [--images=<images>]
  Whether to import images into WordPress
    ---
  default: import
  options:
  - import
  - overwrite
  - exclude
    ---

  [--overwrite-posts]
  Whether to overwrite post details in WordPress

  [--cache=<cache>]
  Whether to use cached XML files or invalid and re-download XML file from Cruise Factory
    ---
  default: cache
  options:
  - cache
  - invalidate
    ---
```