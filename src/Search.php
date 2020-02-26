<?php

namespace Dixyd\Searchable;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;

class Search
{
    protected $aspects = [];

    /**
     * @param  string|\Dixyd\Searchable\SearchAspect  $searchAspect
     *
     * @return \Dixyd\Searchable\Search
     */
    public function registerAspect($searchAspect): self
    {
        if (is_string($searchAspect)) {
            $searchAspect = app($searchAspect);
        }

        $this->aspects[$searchAspect->getType()] = $searchAspect;

        return $this;
    }

    public function registerModel(string $modelClass, ...$attributes): self
    {
        if (isset($attributes[0]) && is_callable($attributes[0])) {
            $attributes = $attributes[0];
        }

        if (is_array(Arr::get($attributes, 0))) {
            $attributes = $attributes[0];
        }

        $searchAspect = new ModelSearchAspect($modelClass, $attributes);

        $this->registerAspect($searchAspect);

        return $this;
    }

    public function getSearchAspects(): array
    {
        return $this->aspects;
    }

    public function search(string $query, array $params = [], ?User $user = null): SearchResultCollection
    {
        return $this->perform($query, $params, $user);
    }

    public function perform(string $query, array $params = [], ?User $user = null): SearchResultCollection
    {
        $searchResults = new SearchResultCollection();

        collect($this->getSearchAspects())
            ->each(function (SearchAspect $aspect) use ($query,$params, $user, $searchResults)
            {
                $searchResults->addResults($aspect->getType(), $aspect->getResults($query,$params, $user));
            });

        return $searchResults;
    }
}
