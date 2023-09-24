<?php
use PHPUnit\Framework\TestCase;

/**
 *  Corresponding Class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 *  @author yourname
 */
class HelperTest extends TestCase
{

  /**
   * Just check if the YourClass has no syntax error 
   *
   * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
   * any typo before you even use this library in a real project.
   *
   */
  public function testIsThereAnySyntaxError()
  {
    $var = new RapTToR\Helper;
    $this->assertTrue(is_object($var));
    unset($var);
  }

  /**
   * Just check if the YourClass has no syntax error 
   *
   * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
   * any typo before you even use this library in a real project.
   *
   */
  /* public function testMethod1()
  {
    $var = new RapTToR\Helper;
    $this->assertTrue($var->method1("hey") == 'Hello World');
    unset($var);
  } */

  public function testCsv2arrFile()
  {
    $var = new RapTToR\Helper;
    $this->assertTrue(is_array($var->csv2arr("countries.csv")));
    unset($var);
  }
  public function testCsv2arrString()
  {
    $var = new RapTToR\Helper;
    $countries = file(__DIR__ . "/countries.csv");
    $this->assertTrue(is_array($var->csv2arr($countries)));
    unset($var);
  }


  private function demoArray()
  {
    $obj = new stdClass();
    $obj->id = "o1";
    $obj->name = "o";
    $arr = array(
      array("id" => "a1", "name" => "a"),
      array("id" => 22, "name" => "b"),
      array("x" => 22, "name" => "b"),
      array("x" => "s1", "name" => "b"),
      array("name" => "c"),
      $obj
    );
    return $arr;
  }
  public function testReIndex1()
  {
    $arr = self::demoArray();
    $var = new RapTToR\Helper;
    $indexed = $var->reIndex($arr, "id");
    $this->assertTrue(
      is_array($indexed) &&
      isset($indexed["a1"]) &&
      is_array($indexed["a1"]) &&
      isset($indexed["a1"]["name"]) &&
      $indexed["a1"]["name"] == "a"
    );
    unset($var);
  }
  public function testReIndex2()
  {
    $arr = self::demoArray();
    $var = new RapTToR\Helper;
    $indexxed = $var->reIndex($arr, "x");
    $this->assertTrue(
      is_array($indexxed) &&
      isset($indexxed["s1"]) &&
      is_array($indexxed["s1"]) &&
      isset($indexxed["s1"]["name"]) &&
      $indexxed["s1"]["name"] == "b"
    );

    unset($var);
  }

  public function testReIndex3()
  {
    $arr = self::demoArray();
    $var = new RapTToR\Helper;
    $indexxed = $var->reIndex($arr, "id");
    $this->assertTrue(
      is_array($indexxed) &&
      isset($indexxed["o1"]) &&
      is_array($indexxed["o1"]) &&
      isset($indexxed["o1"]["name"]) &&
      $indexxed["o1"]["name"] == "o"
    );

    unset($var);
  }

  public function testDump()
  {
    $var = new RapTToR\Helper;
    $temp = array(
      "methods" => $var->methods(true),
    );
    var_dump($temp);
    $this->assertTrue(is_array($temp) && isset($temp["methods"]) && is_string($temp["methods"]));
    return true;
  }


}