<?php

namespace App\Http\Middleware;

use Closure;
use Jerry\JWT\JWT;
use Session;

class checkConsumables
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$token = Session::get('token', '');

		if (!empty($token)) {
			try {
				$tokenAry = JWT::decode($token);
			} catch (\Throwable $th) {
				Session::flush();
			}

			$expired = $tokenAry['exp'] ?? '';

			if (!empty($expired)) {
				if (strtotime('now') < $expired) {
					$tokenAry = [
						'userId' => $tokenAry['userId'],
					];

					$token = JWT::encode($tokenAry);
					Session::put('token', $token);

					return $next($request);
				}
			}

			Session::flush();
		}

		return redirect()->route('consumables.index');
	}
}
