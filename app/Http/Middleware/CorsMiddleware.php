<?php namespace App\Http\Middleware;

class CorsMiddleware
  {
    /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, \Closure $next)
  {
      $headers = [
          'Access-Control-Allow-Origin'      => '*',
          'Access-Control-Allow-Methods'     => 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS',
          'Access-Control-Allow-Credentials' => 'true',
          'Access-Control-Max-Age'           => '86400',
          'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
      ];

      if ($request->method == 'OPTIONS')
      {
        return response()->json('{"method":"OPTIONS"}', 200, $headers);
      }

      $response = $next($request);
      foreach($headers as $key => $value)
      {
          $response->header($key, $value);
      }

      return $response;
  }
}

