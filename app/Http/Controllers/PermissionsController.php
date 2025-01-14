<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionsController extends Controller
{
    public function store(Request $request)
    {

        $validated = Validator::make(
            $request->all(),
            [
                'permission_name'   => 'required|unique:permissions,name',
                'status'      => 'required'
            ]
        );

        if ($validated->fails()) {
            return response()->json([
                'success' => 0,
                'error' => $validated->errors()->all()
            ]);
        }
        $permission = Permission::create([
            'name' => $request->permission_name,
            'status' => $request->status,
        ]);

        if ($permission) {
            return response()->json([
                'success' => 1,
                'message' => 'Permission Created Successfully!'
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error' => 'Error While Creating Permisson!'
            ]);
        }
    }

    public function getPermissions()
    {
        $permissions = Permission::where('status',"1")->get();
        // dd($permissions);
        if ($permissions) {
            return response()->json([
                'success' => 1,
                'permissions'   => $permissions
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'permissions'   => []
            ]);
        }
    }

    public function deletePermission(Request $request)
    {
        $id = $request->id;

        $permission = Permission::where('_id', $id)->first();

        if ($permission) {
            $del_permission = $permission->delete();
            if ($del_permission) {
                return response()->json([
                    'success' => 1,
                    'message'   => 'Permission Deleted Successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'error'   => 'Error While Deleting Permission!'
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'error'   => 'Permission Not Found!'
            ]);
        }
    }


    public function getPermission($id)
    {
        $permission = Permission::where('_id', $id)->first();

        if ($permission) {
            return response()->json([
                'success' => 1,
                'permission'    => $permission,
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'error'    => 'Permission Not Found!',
            ]);
        }
    }


    public function updatePermission(Request $request,$id)
    {
        $permission = Permission::where('_id',$id)->first();
        $name = $request->permission_name;
        $validator = Validator::make($request->all(),[
            'permission_name'  => [
                'required',
               'unique:permissions,name,' . $id . ',_id'
            ]
        ]);
        if( $validator->fails()){
            return response()->json([
                'success' => 1,
                'error' => $validator->errors()->all()
            ],422);
        }
        if($permission)
        {
           $update = $permission->update([
                'name' => $request->permission_name,
                'status' => $request->status,
            ]);

            if($update)
            {
                return response()->json([
                    'success' => 1,
                    'message' => 'Permission Updated Successfully!'
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
                'message' => 'Permission Not Found!'
            ]);
        }
    }
}
