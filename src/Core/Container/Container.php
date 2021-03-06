<?php

declare(strict_types=1);


namespace Core\Container;


use Core\Container\Exception\ServiceContainerInvalidArgumentException;

class Container implements \ArrayAccess, ContainerInterface
{
    private $definitions = [];

    private $results = [];

    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {

            if (class_exists($id)) {

                $reflection = new \ReflectionClass($id);

                $arguments = [];

                if (($constructor = $reflection->getConstructor()) !== null) {
                    foreach ($constructor->getParameters() as $parameter) {
                        if ($class = $parameter->getClass()) {
                            $arguments[] = $this->get($class->getName());
                        } elseif ($parameter->isArray()) {
                            $arguments[] = [];
                        } else {
                            if (!$parameter->isDefaultValueAvailable()) {
                                throw new ServiceContainerInvalidArgumentException(
                                    sprintf('Unable to resolve "%s" in service "%s"', $parameter->getName(), $id)
                                );
                            }

                            $arguments[] = $parameter->getDefaultValue();
                        }
                    }
                }

                return $this->results[$id] = $this->definitions[$id] = $reflection->newInstanceArgs($arguments);
            }

            throw new ServiceContainerInvalidArgumentException(sprintf('Invalid service "%s"', $id));
        }

        if ($this->issetResults($id)) {
            return $this->results[$id];
        }

        $definition = $this->definitions[$id];

        if ($definition instanceof \Closure) {
            $this->results[$id] = $definition($this);
        } else {
            $this->results[$id] = $definition;
        }

        return $this->results[$id];
    }

    protected function issetResults(string $id): bool
    {
        return array_key_exists($id, $this->results);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function set(string $id, $value)
    {
        $this->removeResult($id);

        $this->definitions[$id] = $value;
    }

    public function removeResult($id)
    {
        if ($this->issetResults($id)) {
            unset($this->results[$id]);
        }
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->removeResult($offset);
        $this->removeDefinition($offset);
    }

    public function removeDefinition($id)
    {
        if ($this->has($id)) {
            unset($this->definitions[$id]);
        }
    }
}