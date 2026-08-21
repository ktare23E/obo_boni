<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(){
        $users = User::where('user_type','Staff')->get();
        
        return Inertia::render('Admin/Users/Index',[
            'users' => $users
        ]); 
    }

    public function clients(){
        $clients = User::where('user_type','Client')
                        ->where('status','Active')
                        ->get();
        
        return Inertia::render('Admin/Client/Index',[
            'clients' => $clients
        ]); 
    }

    public function staffClients(){
        $clients = User::where('user_type','Client')
                        ->where('status','Active')
                        ->get();
        
        return Inertia::render('Staff/Clients/Index',[
            'clients' => $clients
        ]); 
    }

    public function create(){
        return Inertia::render('Admin/Users/Create');
    }


    public function store(Request $request)
    {
        // return $request->all();
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'address'    => 'required|string',
            'password'   => 'required|string|min:6',
            'status'     => 'required|string',
            'user_type'  => 'required|string'
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

    }

    public function edit(User $user){
        return Inertia::render('Admin/Users/Edit',[
            'user' => $user
        ]);
    }

    public function update(Request $request){
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required',
            'address'    => 'required|string',
            'status'     => 'required|string',
            'user_type'  => 'required|string'
        ]);

        User::findOrFail($request->id)->update($data);
    }
}
