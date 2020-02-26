<?php

namespace Dixyd\Searchable;

interface Searchable
{
    public function getSearchResult(): SearchResult;
}
