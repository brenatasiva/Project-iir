# Twitter Stream API (v2)

[![Tests](https://github.com/redwebcreation/twitter-stream-api/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/redwebcreation/twitter-stream-api/actions/workflows/tests.yml)
[![Formats](https://github.com/redwebcreation/twitter-stream-api/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/redwebcreation/twitter-stream-api/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/redwebcreation/twitter-stream-api/version)](//packagist.org/packages/redwebcreation/twitter-stream-api)
[![Total Downloads](https://poser.pugx.org/redwebcreation/twitter-stream-api/downloads)](//packagist.org/packages/redwebcreation/twitter-stream-api)

Consume the Twitter Stream API v2 in real-time.

This package is the spiritual successor of `fennb/phirehose`.

## Getting started

> Requires [PHP 8.0+](https://www.php.net/releases/)

You can install the package via composer:

```bash
composer require redwebcreation/twitter-stream-api
```

## Usage

```php
use RWC\TwitterStream\RuleBuilder;
use RWC\TwitterStream\Fieldset;
use RWC\TwitterStream\Sets;
use RWC\TwitterStream\TwitterStream;

$twitterStream = new TwitterStream(
    $bearerToken = '',
    $apiKey  = '',
    $apiSecretKey = '',
);

RuleBuilder::create('cats')
  ->not->retweets()
  ->hasLinks()
  ->save();

$sets = new Sets(
    new Fieldset('user.fields', 'created_at')
);

foreach ($twitterStream->filteredTweets($sets) as $tweet) {
    dump($tweet['data']['text']);
    
    if ($enoughTweets) {
        $twitterStream->stopListening();
    }
}
```

## Concepts

### Rules

Rules are made up of one, or many operators that are combined using boolean logic and parentheses to help define which
Tweets will deliver to your stream. Rules are saved in the Twitter API and are persistent.

> You need to create a `TwitterStream` before using anything related to rules.
> Alternatively, you can use `Rule::useBearerToken()` for full control over which token is used.

#### Listing all the rules

```php
use RWC\TwitterStream\Rule;

Rule::all();
```

#### Adding a rule

```php
use RWC\TwitterStream\Rule;

$rule = new Rule('cat has:image', 'cats with images');
$rule->add();
```

If no tag is provided, the fallback is the rule content itself.

For your convience, there is a "query builder" for rules available, learn more [here](#rule-builder).

#### Deleting a rule

> Note: you can not delete a rule before adding it.

```php
use RWC\TwitterStream\Rule;

$rule  = Rule::all()[0];
$rule->delete();
```

To reduce the number of requests made to Twitter's API, you may want to use bulk rules creation.

```php
use RWC\TwitterStream\Rule;

// One request
Rule::addBulk(
    new Rule('one rule'),
    new Rule('another one')
);

// 2 requests instead of 1 + x rules to delete
Rule::deleteBulk(...Rule::all());
```

### Sets

If you would like to receive additional fields beyond id and text, you will have to specify those fields in your request
with sets.

Sets are also referred as expansions / additional fields in the Twitter documentation.

Sets are a group of `Fieldset`, Twitter exposes three as of now :

* `tweet.fields`
* `user.fields`
* `expansions`

```php
use RWC\TwitterStream\Fieldset;
use RWC\TwitterStream\Sets;

$sets = new Sets(
    new Fieldset('tweet.fields', 'created_at', '...'),
    new Fieldset('expansions', 'author_id', '...')
);
```

Then, pass it to `filteredTweets()`

```php
$twitterStream->filteredTweets($sets);
```

## Rule Builder

It's a powerful tool to build complex rules using an expressive syntax.

```php
use RWC\TwitterStream\RuleBuilder;

$builder = RuleBuilder::create('#php')
    ->group(function (RuleBuilder $builder) {
        $builder->raw('tip')->or()->raw('🔥');
    })
    ->retweets()
    ->hasImages()
    ->not->hasLinks();

// Produces #php (tip OR 🔥) is:retweet has:images -has:links
```

You can negate an operator using the magic property `not`.

```php
use RWC\TwitterStream\RuleBuilder;
RuleBuilder::create('#php')
  ->not->retweets()
  ->hasLinks();

// Produces: #php -is:retweet has:links
```

You can also group operators together :

```php
use RWC\TwitterStream\RuleBuilder;
RuleBuilder::create('#laravel')
    ->group(function (RuleBuilder $builder) {
        $builder->raw('tip')->or()->raw('tips')->or()->raw('🔥');
    });

// Produces: #laravel (tip OR tips OR 🔥)
```

You can also directly save the rule :

```php
use RWC\TwitterStream\RuleBuilder;

RuleBuilder::create('cats')
  ->hasImages()
  ->not->retweets()
  ->save('cats with images, not a retweet');
```

This sends a request to Twitter.


### Available methods

* `from` : Matches any Tweet from a specific user.
* `to` : Matches any Tweet that is in reply to a particular user.
* `sample` : Returns a random percent sample of Tweets that match a rule rather than the entire set of Tweets.
* `nullcast` :  Removes Tweets created for promotion only on ads.twitter.com. (Must always be negated)
* `replies` :  Deliver only explicit replies that match a rule.
* `retweets` : Matches on Retweets that match the rest of the specified rule.
* `quote` : Returns all Quote Tweets, also known as Tweets with comments.
* `verified` : Deliver only Tweets whose authors are verified by Twitter.
* `retweetsOf` : Matches Tweets that are Retweets of the specified user.
* `context` :  Matches Tweets with a specific domain id and/or domain id, entity id pair.
* `hasHashtags` : Matches Tweets that contain at least one hashtag.
* `hasCashtags` : Matches Tweets that contain a cashtag symbol.
* `hasLinks` : This operator matches Tweets which contain links and media in the Tweet body.
* `hasMentions` : Matches Tweets that mention another Twitter user.
* `hasMedia` : Matches Tweets that contain a media object, such as a photo, GIF, or video, as determined by Twitter.
* `hasImages` : Matches Tweets that contain a recognized URL to an image.
* `hasVideos` :  Matches Tweets that contain native Twitter videos, uploaded directly to Twitter.
* `hasGeographicDataAttached` : Matches Tweets that have Tweet-specific geolocation data provided by the Twitter user.
* `locale` :  Matches Tweets that have been classified by Twitter as being of a particular language
* `url` : Matches on any validly-formatted URL of a Tweet.
* `entity` : Matches Tweets with a specific entity string value.
* `conversation` :  Matches Tweets with a specific entity string value.
* `bio` : Matches a keyword or phrase within the Tweet publisher's bio.
* `bioName` :Matches a keyword within the Tweet publisher's user bio name.
* `bioLocation` :    Matches Tweets that are published by users whose location contains the specified keyword or phrase.
* `place` :    Matches Tweets tagged with the specified location or Twitter place ID.
* `placeCountry` :    Matches Tweets where the country code associated with a tagged place/location matches the given
  ISO alpha-2 character code.
* `pointRadius` : Matches against the place.geo.coordinates object of the Tweet when present, and in Twitter, against a
  place geo polygon, where the Place polygon is fully contained within the defined region.
* `boundingBox` : Matches against the place.geo.coordinates object of the Tweet when present, and in Twitter, against a
  place geo polygon, where the place polygon is fully contained within the defined region.

## Testing

```bash
composer test
```

**Twitter Stream API** was created by [Félix Dorn](https://twitter.com/afelixdorn) under
the [MIT License](https://opensource.org/licenses/MIT).

<!-- (179) -->
