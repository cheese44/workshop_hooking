<?php

  class hookable {
   private $instance;
   private $app;

   public function __construct(app $app, object $instance) {
     $this->app = $app;
     $this->instance = $instance;
   }

   public function __call(string $name , array $arguments) {
     list($continue, $arguments) = $this->_beforeAll($name, $arguments);
     if($continue) {
     list($continue, $arguments) = $this->_before($name, $arguments);
     if($continue) {
       $result = $this->_execute($name, $arguments);
       $this->_after($name, $arguments, $result);
       $this->_afterAll($name, $arguments, $result);
       return $result;
     }}
   }

     private function _execute($name, $arguments) {
       return $this->instance->$name(...$arguments);
     }

     public function _afterAll($name, $arguments, $result) {
       $continue = true;
       $class = get_class($this->instance);
       foreach ($this->app->afterAll as $className => $methods) {
         foreach($methods as $method) {
         $result = $this->app->instances[$className]->$method($class, $name,$arguments, $result);
       }
       }

       return $result;
     }

     public function _beforeAll($name, $arguments) {
       $continue = true;
       $class = get_class($this->instance);
       foreach ($this->app->beforeAll as $className => $methods) {
         foreach($methods as $method) {
         list($stillContinue, $arguments) = $this->app->instances[$className]->$method($class, $name, $arguments);
         $continue = $continue && $stillContinue;
       }
       }

       return [$continue, $arguments];
     }

     public function _before($name, $arguments) {
       $continue = true;
       $class = get_class($this->instance);
       foreach ($this->app->before[$class][$name] as $className => $methods) {
         foreach($methods as $method) {
         list($stillContinue, $arguments) = $this->app->instances[$className]->$method(...$arguments);
         $continue = $continue && $stillContinue;
       }
       }

       return [$continue, $arguments];
     }

     public function _after($name, $arguments, $result) {
       $class = get_class($this->instance);
       foreach ($this->app->after[$class][$name] as $className => $methods) {
         foreach($methods as $method)
          $result = $this->app->instances[$className]->$method($result, ...$arguments);
       }

       return $result;
     }
   }



?>
