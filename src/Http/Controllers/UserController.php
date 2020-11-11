<?php

namespace Aixieluo\LaravelSso\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Zttp\Zttp;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @Get(
     *     path="/oauth/code",
     *     summary="获取oauth code",
     *     tags={"Oauth"},
     *     @Parameter(name="redirect", in="query", description="跳转用户验证时的当前地址，验证完成后跳回此地址",required=false),
     *     @\OpenApi\Annotations\Response(response="302", description="跳转至用户验证系统")
     * )
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function code(Request $request)
    {
        $state = Str::random('20');
        $query = http_build_query([
            'client_id'     => config('sso.oauth.client_id'),
            'redirect_uri'  => config('sso.oauth.redirect_uri'),
            'response_type' => 'code',
            'scope'         => '*',
            'state'         => $state
        ]);
        redirect()->setIntendedUrl(session()->previousUrl() ?? '/');

        return redirect(account_url('/oauth/authorize?') . $query)->cookie(Cookie::make('state', $state, 15, '/', config('sso.domain')));
    }

    /**
     * @Post(
     *     path="/oauth/get/token",
     *     summary="获取access_token、refresh_token",
     *     tags={"Oauth"},
     *     @Parameter(name="code", in="query", description="",required=true),
     *     @Parameter(name="redirect", in="query", description="",required=false),
     *     @\OpenApi\Annotations\Response(response="200", description="")
     * )
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function accessToken(Request $request)
    {
        $params = array_merge(config('sso.oauth'),
            [
                'code' => $request->input('code')
            ]);
        try {
            $response = Zttp::post(account_url('oauth/token'), $params);
            $tokens = $response->json();
            Validator::make($tokens, [
                'token_type'    => 'required',
                'expires_in'    => 'required',
                'access_token'  => 'required',
                'refresh_token' => 'required',
            ])->validate();
            return redirect()->intended()
                ->withCookie('access_token', $tokens['access_token'], $tokens['expires_in'] / 60, '/', config('sso.domain'))
                ->withCookie('refresh_token', $tokens['refresh_token'], 60 * 24 * 30, '/', config('sso.domain'));;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            Log::error(json_encode($tokens));
            throw new Exception('请求失败');
        }
    }

    /**
     * @Post(
     *     path="/logout",
     *     summary="登出",
     *     tags={"user"},
     *     @\OpenApi\Annotations\Response(response="200", description="")
     * )
     */
    public function logout()
    {
        $c1 = Cookie::forget('access_token', '/', config('sso.domain'));
        $c2 = Cookie::forget('refresh_token', '/', config('sso.domain'));
        return Response::json()->withCookie($c1)->withCookie($c2);
    }
}
