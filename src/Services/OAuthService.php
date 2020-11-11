<?php

namespace Aixieluo\LaravelSso\Services;

use App\Models\User;
use Arr;
use Illuminate\Contracts\Support\Arrayable;
use Request;
use Validator;
use Zttp\Zttp;

class OAuthService
{
    protected function createUser(array $user)
    {
        return app(User::class)->fillable(array_keys($user))->fill($user);
    }

    /**
     * @param $token
     *
     * @return User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function user($token)
    {
        $response = $this->zttp($token)->get(account_api('/user'));
        $user = $response->json();
        Validator::make((array)$user, ['data.id' => 'required'])->validate();
        return $this->createUser($user['data']);
    }

    public function users(array $ids)
    {
        $response = $this->zttp()->get(account_api('users'), compact('ids'));
        return data_get($response->json(), 'data');
    }

    public function zttp($token = null)
    {
        $headers = [];
        $token && $headers['Authorization'] = 'Bearer ' . $token;
        return Zttp::withHeaders($headers);
    }
}
