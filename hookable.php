<?php

  class hookable {
    /** @var object $instance */
    private $instance;
    /** @var app $app */
    private $app;

    /**
     * hookable constructor.
     *
     * @param app    $app
     * @param object $instance
     */
    public function __construct(app $app, object $instance) {
      $this->app = $app;
      $this->instance = $instance;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments) {
      $result = null;
      list($continue, $arguments) = $this->_beforeAll($name, $arguments);
      if ($continue) {
        list($continue, $arguments) = $this->_before($name, $arguments);
        if ($continue) {
          $result = $this->_execute($name, $arguments);
          $this->_after($name, $arguments, $result);
          $this->_afterAll($name, $arguments, $result);
        }
      }
      return $result;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    private function _execute($name, $arguments) {
      return $this->instance->$name(...$arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @param $result
     *
     * @return mixed
     */
    public function _afterAll($name, $arguments, $result) {
      $class = get_class($this->instance);
      foreach ($this->app->afterAll as $className => $methods) {
        foreach ($methods as $method) {
          $result = $this->app->instances[$className]->$method($class, $name, $arguments, $result);
        }
      }

      return $result;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return array
     */
    public function _beforeAll($name, $arguments) {
      $continue = true;
      $class = get_class($this->instance);
      foreach ($this->app->beforeAll as $className => $methods) {
        foreach ($methods as $method) {
          list($stillContinue, $arguments) = $this->app->instances[$className]->$method($class, $name, $arguments);
          $continue = $continue && $stillContinue;
        }
      }

      return [$continue, $arguments];
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return array
     */
    public function _before($name, $arguments) {
      $continue = true;
      $class = get_class($this->instance);
      foreach ($this->app->before[$class][$name] as $className => $methods) {
        foreach ($methods as $method) {
          list($stillContinue, $arguments) = $this->app->instances[$className]->$method(...$arguments);
          $continue = $continue && $stillContinue;
        }
      }

      return [$continue, $arguments];
    }

    /**
     * @param $name
     * @param $arguments
     * @param $result
     *
     * @return mixed
     */
    public function _after($name, $arguments, $result) {
      $class = get_class($this->instance);
      foreach ($this->app->after[$class][$name] as $className => $methods) {
        foreach ($methods as $method)
          $result = $this->app->instances[$className]->$method($result, ...$arguments);
      }

      return $result;
    }
  }


?>
