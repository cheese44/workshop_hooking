<?php

  class bar {

    /**
     * @before foo meth1
     *
     * @param $param1
     * @param $param2
     *
     * @return array
     */
    public function hook($param1, $param2) {
      echo $param2 . ' ' . $param1 . PHP_EOL;

      return [true, [$param1, $param2]];
    }

    /**
     * @beforeAll
     *
     * @param $class
     * @param $method
     * @param $params
     *
     * @return array
     */
    public function hookAll($class, $method, $params) {
      echo sprintf(
          'calling "%s::%s" with params %s',
          $class,
          $method,
          json_encode($params)
        ) . PHP_EOL;

      return [true, $params];
    }

    /**
     * @afterAll
     *
     * @param $class
     * @param $method
     * @param $params
     * @param $result
     *
     * @return mixed
     */
    public function hookAll2($class, $method, $params, $result) {
      echo sprintf(
          'called "%s::%s" with params %s',
          $class,
          $method,
          json_encode($params)
        ) . PHP_EOL;

      return $result;
    }

    /**
     * @after foo meth1
     *
     * @param $result
     * @param $param1
     * @param $param2
     *
     * @return mixed
     */
    public function hook2($result, $param1, $param2) {
      echo 'goodbye ' . $param2 . PHP_EOL;

      return $result;
    }
  }

?>
