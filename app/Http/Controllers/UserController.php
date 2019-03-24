<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$users = User::all();
		return view("usuario.index",compact("users"));
	}
	public function new(){
		return view("usuario.new");
	}
	public function newPost(Request $request){
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
		$user = User::find($id);
		return view("usuario.edit",compact("user"));
	}
	public function editPost(Request $request, $id){
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
	public function password($id){
		$user = User::find($id);
		return view("usuario.password",compact("user"));
	}
	public function passwordPost(Request $request, $id){
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
		return redirect("/usuario/edit/".$user->id);
	}
	public function delete($id){
		$user = User::find($id);
		$user->delete();
		return redirect("/usuario");
	}
}
