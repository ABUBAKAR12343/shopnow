<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function store(Request $request)
    {

        $validated = Validator::make(
            $request->all(),
            [
                'role_name'   => 'required|unique:roles,name',
                'status'      => 'required'
            ]
        );

        if ($validated->fails()) {
            return response()->json([
                'success' => 0,
                'error' => $validated->errors()->all()
            ]);
        }
        $role = Role::create([
            'name' => $request->role_name,
            'status' => $request->status,
            'permissions' => $request->selectedPermissions, 
        ]);

        if ($role) {
            return response()->json([
                'success' => 1,
                'message' => 'Role Created Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error' => 'Error While Creating Role!'
            ]);
        }
    }

    public function getRoles()
    {
        $roles = Role::all();

        if ($roles) {
            return response()->json([
                'success' => 1,
                'roles'   => $roles
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'roles'   => []
            ]);
        }
    }

    public function deleteRole(Request $request)
    {
        $id = $request->id;

        $role = Role::where('_id', $id)->first();

        if ($role) {
            $del_role = $role->delete();
            if ($del_role) {
                return response()->json([
                    'success' => 1,
                    'message'   => 'Role Deleted Successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'error'   => 'Error While Deleting Role!'
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'error'   => 'Role Not Found!'
            ]);
        }
    }


    public function getRole($id)
    {
        $role = Role::where('_id', $id)->first();

        if ($role) {
            return response()->json([
                'success' => 1,
                'role'    => $role,
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error'    => 'Role Not Found!',
            ]);
        }
    }


    public function updateRole(Request $request,$id)
    {
        $role = Role::where('_id',$id)->first();
        $name = $request->role_name;
        $validator = Validator::make($request->all(),[
            'role_name'  => [
                'required',
               'unique:roles,name,' . $id . ',_id'
            ]
        ]);
        if( $validator->fails()){
            return response()->json([
                'success' => 1,
                'error' => $validator->errors()->all()
            ],422);
        }
        if($role)
        {
           $update = $role->update([
                'name' => $request->role_name,
                'status' => $request->status,
                'permissions' => $request->selectedPermissions,

            ]);

            if($update)
            {
                return response()->json([
                    'success' => 1,
                    'message' => 'Role Updated Successfully!'
                ]);
            }else{
                return response()->json([
                    'success' => 0,
                    'message' => 'Error While Updating Record!'
                ]);
            }
        }else{
            return response()->json([
                'success' => 0,
                'message' => 'Role Not Found!'
            ]);
        }
    }
}
