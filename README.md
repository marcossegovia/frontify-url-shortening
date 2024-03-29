# Frontify url shortening


## Install

```bash
composer install
```

## Usage

To run the application in development, you can run these commands 

```bash
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

## Base Assumptions

* A given URL can be associated with multiple aliases
* URLs and Aliases are unique, this implies the following behaviour:
  * If URL was already used and Alias is new, then the Alias will be associated to previous URL
  * If Alias is already used in any previous URL, then the request will fail

## Improvements

* Under `UrlController.php`
  * the array $request after deserialization, could be map to a DTO to avoid dealing with array key-values
  * more validations could be set, for instance if the request contains a valid url
