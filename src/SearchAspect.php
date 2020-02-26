<?php

namespace Dixyd\Searchable;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class SearchAspect
{
    abstract public function getResults(string $term, array $params): Collection;

    public function getType(): string
    {
        if (isset(static::$searchType)) {
            return static::$searchType;
        }

        $className = class_basename(static::class);

        $type = Str::before($className, 'SearchAspect');

        $type = Str::snake(Str::plural($type));

        return Str::plural($type);
    }
}
