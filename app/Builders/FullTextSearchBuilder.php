<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class FullTextSearchBuilder extends Builder
{
    private function fullTextWildcards(string $term): string
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            if (strlen($word) >= 1) {
                $words[$key] = '+'.$word.'*';
            }
        }

        return implode(' ', $words);
    }

    public function search(array $columns, string $term): self
    {
        $columns = implode(',', $columns);

        $this->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));

        return $this;
    }
}
