<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\EditClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page') || 1;
        $users = User::all()->forPage($page, 20);

        if ($request->wantsJson()) {
            return $users;
        }

        return view('clients.index', ['clients' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('signup');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateClientRequest $request)
    {
        $email = $request->input('email');
        $user = User::query()->where('email', '=', $email);

        if ($user) {
            return back()->withErrors(['error_message' => 'Este email já está em uso!']);
        }

        User::query()->create([
            'name' => $request->input('name'),
            'email' => $email,
            'password' => $request->input('password'),
            'can_access_account' => true
        ]);

        return redirect()->route('clients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('clients.show', ['client' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('clients.edit', ['client' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditClientRequest $request, User $user)
    {
        $email = $request->input('email');

        if ($email) {
            $user_with_same_email = User::query()->where('email', '=', $email);

            if ($user_with_same_email) {
                return back()->withErrors(['error_message' => 'Este email já está em uso!']);
            }
        }

        $user::query()->create([
            'name' => $request->input('name'),
            'email' => $email,
            'password' => $request->input('password'),
            'can_access_account' => $request->input('can_access_account')
        ]);

        return redirect()->route('clients.edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
    }
}
