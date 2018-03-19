<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\User;
use App\Transformers\UserTransformer;

class RegisterController extends Controller
{   
    protected $user;

    public function register(StoreUserRequest $request, User $user)
    {
        $this->user = new $user;
        $this->user->username = $request->username;
        $this->user->email = $request->email;
        $this->user->password = bcrypt($request->password);

        $this->user->save();

        return fractal()
            ->item($this->user)
            ->transformWith(new UserTransformer)
            ->toArray();
    }
}
