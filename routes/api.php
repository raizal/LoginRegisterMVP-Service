<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Register
Route::post('/register', 'UserController@register');

// Login
Route::post('/login', 'UserController@login');

// Logout
Route::post('/logout', 'UserController@logout');

Route::get("/note",function(Request $request){
	if(!$request->token){
		return response()->json("NOT ALLOWED",401);
	}
	$data = DB::table("note")->select("note.*")>join("users","users.id","note.id_user")->where("users.auth_token",$request->token)->orderBy("note.created_at")->get();

	return response()->json(["data"=>$data]);
});

Route::post("/note",function(Request $request){
	
	if(!$request->token){
                return response()->json("NOT ALLOWED",401);
        }
	$user = DB::table("users")->where("auth_token",$request->token)->first();
	if(!$user){
		$request->except('credit_card');
	}
	
	if($request->id){
		//edit
		$data= request->except(['token','id'])
		$result = DB::table("note")->where("id",$request->id)->update($data);
	}else{
		//insert
		$data = $request->except('token');
		$data['id_user'] = $user->id;
		$result = DB::table("note")->insert($data);
	}
	return response()->json(["status"=>$result]);
});
