# JSON API Standard implementation 

[![Build Status](https://scrutinizer-ci.com/g/mikemirten/JsonApi/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/JsonApi/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemirten/JsonApi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/JsonApi/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemirten/JsonApi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/JsonApi/?branch=master)

This repository contains PHP-implementation of the [JsonAPI standard](http://jsonapi.org/).

An integration with the [Symfony Framework](https://symfony.com/) can be found inside of [JsonApi-Bundle repository](https://github.com/mikemirten/JsonApi-Bundle).

## How to install
Through composer:

```composer require mikemirten/json-api```

## How to use

Feel free to read the [documentation](https://github.com/mikemirten/JsonApi/wiki).

## A quick start

```php
use Mikemirten\Component\JsonApi\Document\ResourceObject;
use Mikemirten\Component\JsonApi\Document\SingleResourceDocument;

// ...

$post = $postRepository->findById($id);

$resource = new ResourceObject($id, 'Post', [
    'title' => $post->getTitle(),
    'body'  => $post->getBody()
]);

$document = new SingleResourceDocument($resource);

echo json_encode($document->toArray());
```
Response body:
```javascript
{
    "data": {
        "id": "1",
        "type": "Post",
        "attributes": {
            "title": "Lorem Ipsum",
            "body": "Lorem ipsum dolor sit amet, lobortis urna sed imperdiet..."
        }
    }
}
```
