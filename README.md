# SilverStripe-Global-Error-Page
=======
# SilverStripe Global Error Page Module


## Maintainer Contact

Kirk Mayo

<kmayo (at) marketo (dot) com>

## Requirements

* SilverStripe 3.2

## Documentation

This module is used to add Global error pages to a site or a collection sites.
You can use it to specify a path of where to find some standard error pages.
These pages can be plain html which will output the raw html of the file or it
can be a php file wihich it will just output the response from a php file.

## Composer Installation

  composer require marketo/silverstripe-global-error-page


## Composer Installation

  composer require solnet/socialproof

## Config

The module can be modified via a yml file to specify a path or a particular
file for a certain error code.
To define a path specify this in a yml config file under `GlobalErrorPage`
with the name i`GlobalErrorPagePath`
If no path is defined it defaults to `var/www/error_pages`
You can also specify a page for a particular error code under `GlobalErrorPage`
Some sample yml config is below

```
GlobalErrorPage:
  GlobalErrorPagePath: '/var/www/error_pages/'
  404: '/var/www/error_pages/error.php'
```

## TODO

Add Tests
