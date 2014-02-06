<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\Monga;


use Tacit\Model\Collection;

class MongaCollection
{
    use Collection;

    /**
     * Cast MongoDB-specific data types to PHP-friendly data types
     *
     * @param mixed $var
     *
     * @return mixed
     */
    public function cast($var)
    {
        $casted = $var;
        if (is_object($var)) {
            $class = get_class($var);
            switch ($class) {
                case 'MongoId':
                    $casted = (string)$var;
                    break;
                case 'MongoDate':
                    $casted = (new \DateTime("@{$var->sec}"))->format(DATE_ISO8601);
                    break;
            }
        }
        return $casted;
    }
}
