<?php

use App\Http\Controllers\auth\loginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    // AUTH
    Route::post('/login', [loginController::class, 'login']);
    Route::post('/Signup', [loginController::class, 'Signup']);
    // Route::post('/google-login', [loginController::class, 'googleLogin']);
    Route::post('/send-otp', [loginController::class, 'sendOTP']);
    Route::post('/updatePassword', [loginController::class, 'updatePassword']);


    // ROLES
    Route::get('/getRole/{id}', [RoleController::class, 'getRole']);
    Route::get('/getRoles', [RoleController::class, 'getRoles']);
    Route::post('/addRole', [RoleController::class, 'store']);
    Route::post('/deleteRole/{id}', [RoleController::class, 'deleteRole']);
    Route::post('/updateRole/{id}', [RoleController::class, 'updateRole']);


    // PERMISSIONS
    Route::get('/getPermission/{id}', [PermissionsController::class, 'getPermission']);
    Route::get('/getPermissions', [PermissionsController::class, 'getPermissions']);
    Route::post('/addPermission', [PermissionsController::class, 'store']);
    Route::post('/deletePermission/{id}', [PermissionsController::class, 'deletePermission']);
    Route::post('/updatePermission/{id}', [PermissionsController::class, 'updatePermission']);


    // USERS
    Route::post('/updateUser/{id}', [UsersController::class, 'update']);
    Route::post('/getUsers', [UsersController::class, 'index']);
    Route::get('/getUser/{id}', [UsersController::class, 'edit']);
    Route::post('/addUser', [UsersController::class, 'store']);
    Route::post('/deleteUser/{id}', [UsersController::class, 'delete']);


    // CATEGORIES
    Route::get('/getCategories', [CategoryController::class, 'index']);
    Route::post('/addCategory', [CategoryController::class, 'store']);
    Route::post('/deleteCategory/{id}', [CategoryController::class, 'delete']);
    Route::get('/getCategory/{id}', [CategoryController::class, 'edit']);
    Route::post('/updateCategory/{id}', [CategoryController::class, 'update']);


    // PRODUCTS
    Route::resource('products', ProductController::class);

});
