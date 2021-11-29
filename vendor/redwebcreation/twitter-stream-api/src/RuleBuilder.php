<?php

namespace RWC\TwitterStream;

use InvalidArgumentException;
use LogicException;
use RWC\TwitterStream\Support\Arr;

/**
 * @property RuleBuilder  $not
 * @property array|string $from
 * @property array|string $to
 * @property string       $is
 * @property string       $has
 * @property int          $sample
 * @property array|string $retweets_of
 * @property array|string $context
 * @property array|string $lang
 * @property array|string $url
 * @property array|string $entity
 * @property array|string $conversation_id
 * @property array|string $bio
 * @property array|string $bio_name
 * @property array|string $bio_location
 * @property array|string $place
 * @property array|string $place_country
 * @property array        $point_radius
 * @property array        $bounding_box
 * @property string       $raw
 * @property bool         $or
 * @property bool         $and
 * @property RuleBuilder  $group
 */
class RuleBuilder
{
    protected bool $negates     = false;
    protected array $attributes = [];

    public function __construct(string $query = '')
    {
        // For some reason, PHPStorm autocompletes the query property with a $this->query = $query;
        $this->__set('query', $query);
    }

    public static function create(string $query = ''): RuleBuilder
    {
        return new self($query);
    }

    public function __get(string $name): ?RuleBuilder
    {
        if ($name !== 'not') {
            trigger_error('Undefined property QueryBuilder::' . $name, E_USER_WARNING);

            return null;
        }

        return $this->negates();
    }

    public function __set(string $name, mixed $value): void
    {
        $headless = in_array($name, ['and', 'or', 'query', 'raw']);

        if (empty($value)) {
            $this->negates(false);

            return;
        }

        $this->attributes[] = [
            $headless ? strtoupper($name) : $name,
            [Arr::wrap($value), $this->negates],
            $headless,
        ];
        $this->negates(false);
    }

    protected function negates(bool $negates = true): static
    {
        $this->negates = $negates;

        return $this;
    }

    public function from(string | array $users): static
    {
        $this->from = $users;

        return $this;
    }

    public function to(string | array $users): static
    {
        $this->from = $users;

        return $this;
    }

    public function sample(int $size): static
    {
        if (0 >= $size || $size > 100) {
            throw new InvalidArgumentException('The sample size must be between 1 and 100 percents');
        }

        if ($this->negates) {
            throw new LogicException('Can not negate the sample field');
        }

        $this->sample = $size;

        return $this;
    }

    public function replies(): static
    {
        $this->is = 'reply';

        return $this;
    }

    public function retweets(): static
    {
        $this->is = 'retweet';

        return $this;
    }

    public function quote(): static
    {
        $this->is = 'quote';

        return $this;
    }

    public function verified(): static
    {
        $this->is = 'verified';

        return $this;
    }

    public function retweetsOf(string | array $users): static
    {
        $this->retweets_of = $users;

        return $this;
    }

    public function context(string | array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function hasHashtags(): static
    {
        $this->has = 'hashtags';

        return $this;
    }

    public function hasCashtags(): static
    {
        $this->has = 'cashtags';

        return $this;
    }

    public function hasLinks(): static
    {
        $this->has = 'links';

        return $this;
    }

    public function hasMentions(): static
    {
        $this->has = 'mentions';

        return $this;
    }

    public function hasMedia(): static
    {
        $this->has = 'media';

        return $this;
    }

    public function hasImages(): static
    {
        $this->has = 'images';

        return $this;
    }

    public function hasVideos(): static
    {
        $this->has = 'videos';

        return $this;
    }

    public function hasGeographicDataAttached(): static
    {
        $this->has = 'geo';

        return $this;
    }

    public function locale(string $lang): static
    {
        $this->lang = $lang;

        return $this;
    }

    public function url(string | array $urls): static
    {
        $this->url = $urls;

        return $this;
    }

    public function entity(string | array $entities): static
    {
        $this->entity = $entities;

        return $this;
    }

    public function conversation(string | array $conversations): static
    {
        $this->conversation_id = $conversations;

        return $this;
    }

    public function bio(string | array $bios): static
    {
        $this->bio = $bios;

        return $this;
    }

    public function or(): static
    {
        $this->or = true;

        return $this;
    }

    public function group(callable $builder): static
    {
        if ($this->negates) {
            throw new LogicException('A group can not be negated. Negate each individual statement.');
        }

        $stub = new self();
        // Returning the builder is optional.
        $builder($stub);
        $this->group = $stub;

        return $this;
    }

    public function nullcast(): static
    {
        if (!$this->negates) {
            throw new LogicException('The nullcast operator must be negated');
        }

        $this->is = 'nullcast';

        return $this;
    }

    public function and(): static
    {
        $this->and = true;

        return $this;
    }

    public function bioName(string | array $bioNames): static
    {
        $this->bio_name = $bioNames;

        return $this;
    }

    public function bioLocation(string | array $bioLocations): static
    {
        $this->bio_location = $bioLocations;

        return $this;
    }

    public function place(string | array $places): static
    {
        $this->place = $places;

        return $this;
    }

    public function placeCountry(string | array $placesCountry): static
    {
        $this->place_country = $placesCountry;

        return $this;
    }

    public function pointRadius(array $points): static
    {
        $isCollection = array_reduce($points, function ($_, $point) {
            return $_ || is_array($point);
        }, false);

        $this->point_radius = $isCollection ? $points : [$points];

        return $this;
    }

    public function boundingBox(array $boxes): static
    {
        $isCollection = array_reduce($boxes, function ($_, $box) {
            return $_ || is_array($box);
        }, false);

        $this->bounding_box = $isCollection ? $boxes : [$boxes];

        return $this;
    }

    public function __toString(): string
    {
        return $this->compile();
    }

    public function compile(): string
    {
        $rule = [];

        foreach ($this->attributes as $attribute) {
            [$name, $set, $headless] = $attribute;
            [$properties, $negates]  = $set;

            foreach ($properties as $property) {
                if (is_array($property)) {
                    $property = '[' . implode(' ', $property) . ']';
                }

                if ($property instanceof RuleBuilder) {
                    $rule[] = '(' . $property . ')';
                    continue;
                }

                $rule[] = ($negates ? '-' : '') . ($headless ? '' : $name . ':') . (in_array($name, ['AND', 'OR']) ? $name : $property);
            }
        }

        return implode(' ', $rule);
    }

    public function raw(string $expression): static
    {
        $this->raw = $expression;

        return $this;
    }

    public function save(string $tag = null): Rule
    {
        $compiled = $this->compile();

        return Rule::create($compiled, $tag ?? $compiled);
    }
}
