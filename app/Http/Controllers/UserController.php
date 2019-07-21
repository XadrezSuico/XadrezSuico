<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}

		$users = User::all();
		return view("usuario.index",compact("users"));
	}
	public function new(){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}


		return view("usuario.new");
	}
	public function newPost(Request $request){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}

		
		$requisicao = $request->all();
		$validator = \Validator::make($requisicao, [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);
		if($validator->fails()){
				return redirect()->back()->withErrors($validator->errors());
		}
		$user = User::create([
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => bcrypt($request->input('password')),
		]);
		return redirect("/usuario/edit/".$user->id);
	}
	public function edit($id){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}

		
		$user = User::find($id);
		return view("usuario.edit",compact("user"));
	}
	public function editPost(Request $request, $id){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}

		
		$requisicao = $request->all();
		$user = User::find($id);
		if($user->email != $request->input("email")){
			$validator = \Validator::make($requisicao, [
				'name' => 'required|string|max:255',
				'email' => 'required|string|email|max:255|unique:users',
			]);
			if($validator->fails()){
					return redirect()->back()->withErrors($validator->errors());
			}
		}else{
			$validator = \Validator::make($requisicao, [
				'name' => 'required|string|max:255',
			]);
			if($validator->fails()){
					return redirect()->back()->withErrors($validator->errors());
			}
		}
		$user->name = $request->input("name");
		$user->email = $request->input("email");
		$user->save();
		return redirect("/usuario/edit/".$user->id);
	}
	public function password($id, Request $request){
		$USER = Auth::user();
		if(!$USER->hasPermissionGlobalbyPerfil([1]) && $USER->id != $id){
			return redirect("/");
		}
		$user = User::find($id);
		$ok = 0;
		if($request->has("ok")) $ok = $request->input("ok");
		return view("usuario.password",compact("user","ok","USER"));
	}
	public function passwordPost(Request $request, $id){
		$USER = Auth::user();
		if(!$USER->hasPermissionGlobalbyPerfil([1]) && $USER->id != $id){
			return redirect("/");
		}
		$requisicao = $request->all();
		$validator = \Validator::make($requisicao, [
			'password' => 'required|string|min:6|confirmed',
		]);
		if($validator->fails()){
				return redirect()->back()->withErrors($validator->errors());
		}
		$user = User::find($id);
		$user->password = bcrypt($request->input('password'));
		$user->save();
		
		if(!$USER->hasPermissionGlobalbyPerfil([1])){
			return redirect("/usuario/password/".$user->id."?ok=1");
		}

		return redirect("/usuario/edit/".$user->id);
	}
	public function delete($id){
		$user = Auth::user();
		if(!$user->hasPermissionGlobalbyPerfil([1])){
			return redirect("/");
		}

		
		$user = User::find($id);
		$user->delete();
		return redirect("/usuario");
	}
}
