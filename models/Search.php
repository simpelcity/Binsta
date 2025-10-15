<?php

namespace Binsta\Models;

use RedBeanPHP\R as R;

class Search
{
    public static function searchUsers($query)
    {
        $query = '%' . $query . '%';
        return R::findAll('users', 'username LIKE ? ORDER BY username ASC', [$query]);
    }
}
