<?php

/*
 * This class handles the Routing.
 * It is used to register routes (using the add function) and to connect
 * them with a function that should be executed when the route is called.
 */

class Router{

    static array $PARAMETER_TYPE = array(
        "number" => '[0-9]+',
        "string" => '[^/?]+'
    );

    public static function createRoute($method, $expression, $function){
        array_push(self::$routes,Array(
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ));
    }

  public static function createRouteWithQueryParameters($method, $uri, array $queryParameters, $function) {

      // Build route
      $route = $uri . "?(";
      foreach ($queryParameters as $name => $regex) {
          $route .= '(' . $name . '=' . $regex . ')|';
      }
      $route .= '&)+';

      // Function to get parameter values from the matches of the http request
      $routeFunction = function(...$matches) use ($function, $queryParameters) {

          // Get all the uri parameters from the matches (They will be in the correct order)
          $uriValues = array_filter($matches, function($match) {
              return urldecode(count(explode("/", $match)) == 1 && strlen($match) > 0);
          });

          // Get the query parameters from the QUERY_STRING in the the correct order according to the passed $queryParameters
          $queries = array();
          parse_str($_SERVER['QUERY_STRING'], $queries);
          $queryValues = array_map(function($queryParameterName) use ($queries) {
              return urldecode($queries[$queryParameterName]);
          }, array_keys($queryParameters));

          // Combine both value arrays in the correct order
          $values = $uriValues;
          array_push($values, ...$queryValues);

          // Call the function that handles the route with all parameters in the correct order
          call_user_func_array($function, $values);
      };

      self::createRoute($method, $route, $routeFunction);
  }

  private static $routes = Array();
  private static $pathNotFound = null;
  private static $methodNotAllowed = null;

  public static function pathNotFound($function){
    self::$pathNotFound = $function;
  }

  public static function methodNotAllowed($function){
    self::$methodNotAllowed = $function;
  }

  public static function run($basepath = '/'){

    // Parse current url
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);//Parse Uri

    if(isset($parsed_url['path'])) {
      $path = $parsed_url['path'];
      if (isset($parsed_url['query'])) {
          $path .= '/' . str_replace("&", "/", str_replace("=", "/", $parsed_url['query']));
      }
    }else{
      $path = '/';
    }

    // Get current request method
    $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach(self::$routes as $route){

      // If the method matches check the path

      // Add basepath to matching string
      if($basepath!=''&&$basepath!='/'){
        $route['expression'] = '('.$basepath.')'.$route['expression'];
      }

      $route['expression'] = str_replace("&", "/", $route['expression']);
      $route['expression'] = str_replace("=", "/", $route['expression']);
      $route['expression'] = str_replace("?", "/", $route['expression']);

      // Add 'find string start' automatically
      $route['expression'] = '^'.$route['expression'];

      // Add 'find string end' automatically
      $route['expression'] = $route['expression'].'$';

      // Check path match	
      if(preg_match('#'.$route['expression'].'#',$path,$matches)){

        $path_match_found = true;

        // Check method match
        if(strtolower($method) == strtolower($route['method'])){

          array_shift($matches);// Always remove first element. This contains the whole string

          if($basepath!=''&&$basepath!='/'){
            array_shift($matches);// Remove basepath
          }

          call_user_func_array($route['function'], $matches);

          $route_match_found = true;

          // Do not check other routes
          break;
        }
      }
    }

    // No matching route was found
    if(!$route_match_found){

      // But a matching path exists
      if($path_match_found){
        header("HTTP/1.0 405 Method Not Allowed");
        if(self::$methodNotAllowed){
          call_user_func_array(self::$methodNotAllowed, Array($path,$method));
        }
      }else{
        header("HTTP/1.0 404 Not Found");
        if(self::$pathNotFound){
          call_user_func_array(self::$pathNotFound, Array($path));
        }
      }

    }

  }

}