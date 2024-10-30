<?php namespace CovidCoupons\App;

class Container {

  protected static $container = [];

  protected static $singletons = [];

  protected static $instance;

      /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

  public function getResolver($name)
  {
    if(isset(static::$container[$name])){
        return static::$container[$name] ;
    } else {
        return $name;
    }
  }

  /**
  * Build an instance of the given class
  * 
  * @param string $class
  * @return mixed
  *
  * @throws Exception
  */
  public function resolve($name)
  {

      if (isset(static::$singletons[$name])) {
          return static::$singletons[$name];
      }

      $instance = $this->autoinjectNewInstanceOf($name);

      return $instance;
  }

  private function autoinjectNewInstanceOf($class)
  {

      $reflector = new \ReflectionClass($this->getResolver($class));

      if( ! $reflector->isInstantiable())
      {
        throw new \Exception("[$class] is not instantiable");
      }
      
      $constructor = $reflector->getConstructor();
      
      if(is_null($constructor))
      {
        return new $class;
      }
      
      $parameters = $constructor->getParameters();
      $dependencies = $this->getDependencies($parameters);
      
      return $reflector->newInstanceArgs($dependencies);
  }
  
  /**
   * Build up a list of dependencies for a given methods parameters
   *
   * @param array $parameters
   * @return array
   */
  public function getDependencies($parameters)
  {
    $dependencies = array();
    
    foreach($parameters as $parameter)
    {
      $dependency = $parameter->getClass();
      
      if(is_null($dependency))
      {
        $dependencies[] = $this->resolveNonClass($parameter);
      }
      else
      {
        $dependencies[] = $this->resolve($dependency->name);
      }
    }
    
    return $dependencies;
  }
  
  /**
   * Determine what to do with a non-class value
   *
   * @param ReflectionParameter $parameter
   * @return mixed
   *
   * @throws Exception
   */
  public function resolveNonClass(\ReflectionParameter $parameter)
  {
    if($parameter->isDefaultValueAvailable())
    {
      return $parameter->getDefaultValue();
    }
    
    throw new \Exception("Erm.. Cannot resolve the unkown!?");
  }

  public function bind($name, $resolver)
  {   
    static::$container[$name] = $resolver;

    return $this;
  }

  public function singleton($name, $resolver)
  {   
    static::$singletons[$name] = $this->autoinjectNewInstanceOf($resolver);
    return $this;
  }

}