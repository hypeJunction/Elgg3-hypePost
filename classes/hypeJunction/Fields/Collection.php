<?php

namespace hypeJunction\Fields;

use ArrayAccess;
use Countable;
use SeekableIterator;
use Serializable;

class Collection implements ArrayAccess, SeekableIterator, Countable, Serializable {

	/**
	 * @var FieldInterface[]
	 */
	protected $items = [];

	/**
	 * Constructor
	 *
	 * @param FieldInterface[] $fields Fields
	 */
	public function __construct(array $fields = []) {
		$this->items = array_filter($fields, function ($e) {
			return $e instanceof FieldInterface && $e->name;
		});
	}

	/**
	 * Add a new field
	 *
	 * @param string         $name  Field name
	 * @param FieldInterface $field Field
	 */
	public function add($name, FieldInterface $field) {
		if (!$field->name) {
			$field->name = $name;
		}

		if (!$field->name) {
			throw new \InvalidArgumentException("Fields must have a name");
		}

		$name = $field->name;

		$this->items[$name] = $field;
	}

	/**
	 * Get a field
	 *
	 * @param string $name Field name
	 *
	 * @return FieldInterface|null
	 */
	public function get($name) {
		return elgg_extract($name, $this->items);
	}

	/**
	 * Check if collection has a field with a given name
	 *
	 * @param string $name Field name
	 *
	 * @return bool
	 */
	public function has($name) {
		return array_key_exists($name, $this->items);
	}

	/**
	 * Remove field by its name
	 *
	 * @param string $name Field name
	 * @return void
	 */
	public function remove($name) {
		unset($this->items[$name]);
	}

	/**
	 * Filter fields
	 *
	 * @param callable $callback Filter callable
	 *
	 * @return Collection
	 */
	public function filter(callable $callback) {
		$this->sort();

		$items = array_filter($this->items, $callback);

		return new Collection($items);
	}

	/**
	 * Get all fields in collection
	 *
	 * @return array
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * Sort fields by priority
	 */
	public function sort() {
		uasort($this->items, function ($f1, $f2) {
			$p1 = $f1->priority ? : 500;
			$p2 = $f2->priority ? : 500 ;
			if ($p1 === $p2) {
				return 0;
			}
			return $p1 < $p2 ? -1 : 1;
		});
	}

	/**
	 * Whether a offset exists
	 * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset) {
		return $this->has($offset);
	}

	/**
	 * Offset to retrieve
	 * @link  http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * Offset to set
	 * @link  http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return void
	 * @throws \InvalidParameterException
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value) {
		if (!$value instanceof FieldInterface) {
			throw new \LogicException('Array values must implement ' . FieldInterface::class);
		}

		$this->add($offset, $value);
	}

	/**
	 * Offset to unset
	 * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset) {
		unset($this->items[$offset]);
	}

	/**
	 * Return the current element
	 * @link  http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current() {
		return current($this->items);
	}

	/**
	 * Move forward to next element
	 * @link  http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next() {
		next($this->items);
	}

	/**
	 * Return the key of the current element
	 * @link  http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key() {
		return key($this->items);
	}

	/**
	 * Checks if current position is valid
	 * @link  http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid() {
		return key($this->items) !== null;
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link  http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind() {
		reset($this->items);
	}

	/**
	 * Count elements of an object
	 * @link  http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return count($this->items);
	}

	/**
	 * Seeks to a position
	 * @link  http://php.net/manual/en/seekableiterator.seek.php
	 *
	 * @param int $position <p>
	 *                      The position to seek to.
	 *                      </p>
	 *
	 * @return void
	 * @since 5.1.0
	 */
	public function seek($position) {
		$keys = array_keys($this->items);
		if (isset($keys[$position])) {
			throw new \OutOfBoundsException();
		}

		$key = $keys[$position];

		return $this->items[$key];
	}

	/**
	 * String representation of object
	 * @link  http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize() {
		return serialize($this->items);
	}

	/**
	 * Constructs the object
	 * @link  http://php.net/manual/en/serializable.unserialize.php
	 *
	 * @param string $serialized <p>
	 *                           The string representation of the object.
	 *                           </p>
	 *
	 * @return void
	 * @since 5.1.0
	 */
	public function unserialize($serialized) {
		$data = unserialize($serialized);
		$items = unserialize($data);

		if (is_array($items)) {
			$this->items = $items;
		}
	}
}