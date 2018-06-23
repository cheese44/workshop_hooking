<?php

  class app {
    public $instances = [];
    public $beforeAll = [];
    public $afterAll = [];
    public $before = [];
    public $after = [];

    /**
     * @param object $instance
     *
     * @throws ReflectionException
     */
    public function registerInstance(object $instance): void {
      $this->instances[get_class($instance)] = $instance;

      $this->registerHooks($instance);
    }

    /**
     * @param $class
     *
     * @return hookable
     */
    public function getInstance($class) {
      return new hookable($this, $this->instances[$class]);
    }

    /**
     * @param object $instance
     *
     * @throws ReflectionException
     */
    private function registerHooks($instance) {
      $reflectionClass = new ReflectionClass($instance);

      foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

        $docComment = $method->getDocComment();

        $this->registerHookBeforeAll($docComment, $instance, $method->name);
        $this->registerHookAfterAll($docComment, $instance, $method->name);
        $this->registerHookBefore($docComment, $instance, $method->name);
        $this->registerHookAfter($docComment, $instance, $method->name);
      }
    }

    /**
     * @param $docComment
     * @param $instance
     * @param $methodName
     */
    private function registerHookAfter($docComment, $instance, $methodName) {
      if (preg_match('/\@after\s+([\w\d]+)\s+([\w\d]+)/', $docComment, $matches)) {
        list($ignore, $class, $method) = $matches;
        if (!empty($class) && !empty($method)) {
          $this->after[$class][$method][get_class($instance)][] = $methodName;
        }
      }
    }

    /**
     * @param $docComment
     * @param $instance
     * @param $methodName
     */
    private function registerHookAfterAll($docComment, $instance, $methodName) {
      if (preg_match('/\@afterAll/', $docComment, $matches)) {
        list($afterAll) = $matches;
        if (!empty($afterAll)) {
          $this->afterAll[get_class($instance)][] = $methodName;
        }
      }
    }

    /**
     * @param $docComment
     * @param $instance
     * @param $methodName
     */
    private function registerHookBeforeAll($docComment, $instance, $methodName) {
      if (preg_match('/\@beforeAll/', $docComment, $matches)) {
        list($beforeAll) = $matches;
        if (!empty($beforeAll)) {
          $this->beforeAll[get_class($instance)][] = $methodName;
        }
      }
    }

    /**
     * @param $docComment
     * @param $instance
     * @param $methodName
     */
    private function registerHookBefore($docComment, $instance, $methodName) {
      if (preg_match('/\@before\s+([\w\d]+)\s+([\w\d]+)/', $docComment, $matches)) {
        list($ignore, $class, $method) = $matches;
        if (!empty($class) && !empty($method)) {
          $this->before[$class][$method][get_class($instance)][] = $methodName;
        }
      }
    }
  }

?>
