<?php

namespace App\Lib;

class Searchable
{
    public function getSearch()
    {
        return function ($params, $like = true) {
            $search = request()->search;

            if (!$search) {
                return $this;
            }

            if (!is_array($params)) {
                throw new \InvalidArgumentException("Search parameters should be an array");
            }

            $search = $like ? "%$search%" : $search;
            // Apply the where clause to the query.
            $this->where(function ($query) use ($params, $search) {
                foreach ($params as $param) {

                    // Split relationship and column information.
                    [$relation, $columns] = array_pad(explode(':', $param, 2), 2, null);

                    if ($columns) {
                        // Process multiple columns for a relationship.
                        foreach (explode(',', $columns) as $column) {
                            if (!$relation) {
                                continue;
                            }

                            $query->orWhereHas($relation, function ($relationQuery) use ($column, $search) {
                                $relationQuery->where($column, 'LIKE', $search);
                            });
                        }
                    } else {
                        $query->orWhere($param, 'LIKE', $search);
                    }
                }
            });

            return $this;
        };
    }
}

