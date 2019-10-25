<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:32 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Tfboe\FmLib\Exceptions\PropertyNotExistingException;

/**
 * Interface SubClassDataInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface SubClassDataInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Adds an subclass property if it is not already existent. The default value is used as value.
   * @param string $name the property name
   * @param mixed $default the default value for the property
   * @return $this|SubClassData
   */
  public function addPropertyIfNotExistent(string $name, $default);

  /**
   * clones the subclass data from other into this
   * @param SubClassData $other
   */
  public function cloneSubClassDataFrom($other);

  /**
   * Gets a property or throws an error if the property does not exist.
   * @param string $name the name of the property
   * @return mixed the value of the property
   * @throws PropertyNotExistingException
   */
  public function getProperty($name);

  /**
   * Checks if the contains the given property as subclass data.
   * @param string $property the property name
   * @return bool true if it has the property and false otherwise
   */
  public function hasProperty(string $property): bool;

  /**
   * Initializes the subclassData structure and adds the given keys with null values to it.
   * @param string[] $keys the keys of the subclass data properties (the names).
   * @return $this|SubClassData
   */
  public function initSubClassData(array $keys);

  /**
   * Checks if a method with the given name exists considering also getter and setter for subclass properties
   * @param string $method
   * @return bool
   */
  public function methodExists(string $method): bool;

  /**
   * Sets a property with the given name and the given value
   * @param string $name the name of the property to set
   * @param mixed $value the new value for the property
   * @return $this|SubClassData
   * @throws PropertyNotExistingException
   */
  public function setProperty(string $name, $value);

  /**
   * @return string[]
   */
  public function getKeys(): array;
//</editor-fold desc="Public Methods">
}