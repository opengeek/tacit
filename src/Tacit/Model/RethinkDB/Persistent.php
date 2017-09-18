<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\RethinkDB;


use ArrayObject;
use DateTime;
use DateTimeZone;
use Tacit\Model\Exception\ModelValidationException;

class Persistent extends \Tacit\Model\Persistent
{
    /**
     * The unique identifier for a RethinkDB model item.
     *
     * @var string
     */
    public $id;

    /**
     * Hydrate this instance using the provided data and an optional mask.
     *
     * @param array|object $data
     * @param bool|array $mask
     */
    public function hydrate($data, $mask = false)
    {
        parent::hydrate($this->fromArrayObject($data), $mask);
    }

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function insert()
    {
        $validated = $this->validate([], Collection::getMask($this, [], [$this->getKey()]));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for new item in collection ' . static::$collectionName, $validated);
        }
        $saved = static::collection($this->getRepository())->insert($this->distill($this->toArray(Collection::getMask($this, [], [$this->getKeyField()]), false)));
        if ($saved !== false) {
            $this->{$this->getKeyField()} = $saved;

            return true;
        }

        return false;
    }

    /**
     * Patch this model in the repository, updating only dirty fields.
     *
     * @throws ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function patch()
    {
        $validated = $this->validate([], array_keys($this->_dirty));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for existing item in collection ' . static::$collectionName, $validated);
        }
        $saved = static::collection($this->getRepository())->update([$this->getKeyField() => $this->getKey()], $this->distill($this->dirty(false), true));
        if ($saved === false) {
            return false;
        }

        return true;
    }

    protected function fromArrayObject($data)
    {
        if ($data instanceof ArrayObject) {
            $data = $data->getArrayCopy();
        }
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if ($value instanceof ArrayObject) {
                    $value = $this->fromArrayObject($value);
                }
            }
        }

        return $data;
    }

    protected function distill($data, $literalNested = false)
    {
        if (!($data instanceof \r\DatumConverter) && (is_object($data) || is_array($data))) {
            if (!$data instanceof DateTime && !$data instanceof ArrayObject) {
                $data = is_object($data) ? new ArrayObject($data) : $data;

                foreach ($data as $key => &$value) {
                    if (!($value instanceof \r\DatumConverter) && (is_object($value) || is_array($value))) {
                        if ($literalNested && (!$value instanceof DateTime)) {
                            if (is_object($value)) {
                                $value = get_object_vars($value);
                            }
                            if (is_array($value)) {
                                $value = \r\literal($value);
                            }
                        } else {
                            $value = $this->distill($value);
                        }
                    }
                }
            } elseif ($data instanceof DateTime && $data->getTimezone()->getName() !== 'UTC') {
                $data->setTimezone(new DateTimeZone('UTC'));
            }
        }

        return $data;
    }
}
