<?php
namespace SlimRest;

abstract class Resource {
	// for generating JWT auth token
	// TODO store this in environment variable
	private $secret_jwt = "th1s1s0n3l0ngf*ck1ns3cr3ty07sh07ldn0tr3v3al";
	public $app;

	public function __construct($app){
		$this->app = $app;
		$this->routes();
	}

	abstract function routes();

    public function respond($response, $reply, $http_status=200){
      return $this->app->response($response, json_encode($reply, JSON_UNESCAPED_UNICODE), $http_status);
    }

  public function group( $route, $routeGtoup ){
    return $this->$app->group( $route , $routeGtoup );
  }

	public function get($route, $callback, $middleware = null){
		return $this->app->route('get', $route, $callback, $middleware );
	}
	public function post($route, $callback, $middleware = null){
		return $this->app->route('post', $route, $callback, $middleware);
	}
	public function put($route, $callback, $middleware = null){
		return $this->app->route('put', $route, $callback, $middleware);
	}
	public function delete($route, $callback, $middleware = null){
		return $this->app->route('delete', $route, $callback, $middleware);
	}
	public function patch($route, $callback, $middleware = null){
		return $this->app->route('patch', $route, $callback, $middleware);
	}

    // this method validates jwt token passed in Authorization header and retrive back the token information
    public function authorize($request){
      $message = "Using token from request header";
      $header = $request->getHeader("Authorization");
      $header = isset($header[0]) ? $header[0] : "";
      $token = "";
      if (preg_match("/Bearer\s+(.*)$/i", $header, $matches)) {
        // decode the token
        $token = $matches[1];
      }

      /* Bearer not found, try a cookie. */
      $cookie_params = $request->getCookieParams();

      if (isset($cookie_params["token"])) {
          $token = $cookie_params["token"];
      };

      if($token != ""){
        return $this->decodeToken($token);
      }

      return false;
    }

    public function decodeToken($token){
      try {
          return \JWT::decode(
              $token,
              $this->secret_jwt,
              ["HS256", "HS512", "HS384", "RS256"]
          );
      } catch (\Exception $exception) {
          return false;
      }
    }

    public function encodeToken($data){
      return \JWT::encode($data, $this->secret_jwt);
    }
}
