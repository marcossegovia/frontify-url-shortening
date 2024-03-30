# Frontify url shortening

👋 Hello Frontify !

Please, find in the following preread everything you will need to install/run the application.

Also notice the considerations and follow-up section.

## Install

```bash
composer install
```

## Usage

To run the application in development, you can run via composer command: (make sure you have php >= 8.1)

```bash
composer start
```

Or you can use `docker-compose` to run the app with `docker`, by running:
```bash
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

## Api usage


> When running server from composer or docker, the `InMemoryUrlRepository` is configured to fetch/flush the URLs records into `frontify-url-shortening/src/Infrastructure/Repository/urls.json`, make sure this directory is writable as well as `frontify-url-shortening/logs` for logging



### Get URL from alias

* HTTP method: `GET`
* Uri: /{alias}

* Example Request: 
  * Uri: `GET` `localhost:8080/marketing-material-01`
* Expected Response: 
  * 200 OK
  * Body:
  ```json
  {
    "url": "complex-frontify-domain.com\/complex-url\/1"
  }
  ```

### Create URL with alias

> Make sure the url `complex-frontify-domain.com/complex-url/2` nor alias `marketing-material-02` exist under `urls.json`

* HTTP method: `POST`
* Uri: /url

* Example Request: 
  * Uri: POST `localhost:8080/url`
  * Body:
  ```json
  {
    "url": "complex-frontify-domain.com/complex-url/2",
    "alias": "marketing-material-02"
  }
  ```
* Expected Response:
  * 201 OK

Now, `marketing-material-02` alias should retrieve `complex-frontify-domain.com/complex-url/2` via [GET URL from Alias endpoint](#get-url-from-alias)

### Update URL with new alias

> Make sure the url `complex-frontify-domain.com/complex-url/2` exists but it does not contain alias `marketing-material-03` under `urls.json`

* HTTP method: `POST`
* Uri: /url

* Example Request: 
  * Uri: POST `localhost:8080/url`
  * Body:
  ```json
  {
    "url": "complex-frontify-domain.com/complex-url/2",
    "alias": "marketing-material-03"
  }
  ```
* Expected Response:
  * 201 OK

Now, both `marketing-material-02` and `marketing-material-03` aliases should retrieve `complex-frontify-domain.com/complex-url/2` via [GET URL from Alias endpoint](#get-url-from-alias)

## Base Considerations

* A given URL can be associated with multiple aliases
* URLs and Aliases are unique, this implies the following behaviour:
  * If URL was already used and Alias is new, then the Alias will be associated to previous URL
  * If Alias is already used in any previous URL, then the request will fail

## Improvements

* Under `UrlController.php`
  * the array $request after deserialization, could be map to a DTO to avoid dealing with array key-values
  * more validations could be set, for instance, check if the request contains a valid url
* Continuous integration could be set up to run the tests automatically, check on coverage and security analysis
* Include unit test coverage for the Repository and the controller
