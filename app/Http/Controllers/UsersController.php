<?php

namespace App\Http\Controllers;

use App\Mail\NewUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {


        $users = User::where('_id', '!=', $request->id)
            ->with('role')
            ->get();

        if (!empty($users)) {
            return response()->json([
                'success' => 1,
                'users'   => $users
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'users'   => []
            ]);
        }
    }


    public function store(Request $request)
    {
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'number' => 'required',
                'role' => 'required',
            ]
        );

        if ($validated->fails()) {
            return response()->json([
                'success' => 0,
                'errors' => $validated->errors()->all()
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->number = $request->number;
        $user->role_id = $request->role;
        $user->status = $request->status;



        if ($user->save()) {
            Mail::to($request->email)->send(new NewUserMail($user,$request->password));
            return response()->json([
                'success' => 1,
                'message' => 'User Created Successfully!',
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Error While Creating User!',
            ]);
        }
    }


    public function delete(Request $request)
    {
        $id = $request->id;

        $user = User::where('_id', $id)->first();

        if ($user) {
            $del_user = $user->delete();
            if ($del_user) {
                return response()->json([
                    'success' => 1,
                    'message'   => 'User Deleted Successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'error'   => 'Error While Deleting User!'
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'error'   => 'User Not Found!'
            ]);
        }
    }

    public function edit($id)
    {
        $user = User::where('_id', $id)->first();

        if ($user) {
            return response()->json([
                'success' => 1,
                'user'    => $user,
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error'    => 'User Not Found!',
            ]);
        }
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validated = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'number' => 'required',
                'role' => 'required',
                'status' => 'required',
            ]
        );

        if ($validated->fails()) {
            return response()->json([
                'success' => 0,
                'errors' => $validated->errors()->toArray(),
            ]);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'User not found!',
            ]);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->role_id = $request->role;
        $user->status = $request->status;

        if ($user->save()) {
            return response()->json([
                'success' => 1,
                'message' => 'User Updated Successfully!',
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Error While Updating User!',
            ]);
        }
    }
}
