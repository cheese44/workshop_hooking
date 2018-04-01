<?php
require_once 'hookable.php';
  require_once 'app.php';
  require_once 'bar.php';
      require_once 'foo.php';

      $app = new app();
      $foo = new foo();
      $bar = new bar();

      $app->registerInstance($foo);
      $app->registerInstance($bar);

      $appContextFoo = $app->getInstance(get_class($foo));

      $appContextFoo->meth1('Hello', 'World');
 ?>
