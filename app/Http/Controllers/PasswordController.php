<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DataTables;
use File;
use Illuminate\Database\QueryException;
use PDF;
use Excel;
use App\User;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function requestResetPassword(Request $request)
    {
		$cek = User::where('email',$request->get('email'))->first();
		if (count($cek) > 0) {
			$suhu = $cek->id;
	    	$mail_to = $request->get('email');
	    	$contactList = [];
	        $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
	        $contactList[1] = 'rio.irvansyah@music.yamaha.com';
	    	Mail::to($mail_to)->bcc($contactList,'BCC')->send(new SendEmail($suhu, 'request_reset_password'));

	    	return back()->with('success', "Please check your email.")->with('user',$cek);
		}else{
	    	return back()->with('error', "Your email doesn't exists.");
		}
    }

    public function resetPassword($id)
    {
    	return view('auth.passwords.reset')->with('id',$id);
    }

    public function resetPasswordConfirm(Request $request)
    {
    	$password = $request->get('password');
    	$password_confirm = $request->get('password_confirm');
    	if ($password == $password_confirm) {
    		$user = User::where('id',$request->get('id'))->first();
    		$user->password = Hash::make($request->get('password'));
    		$user->save();
    		return redirect('')->with('success','Reset password was successful.');
    	}else{
    		return back()->with('error', "Password doesn't match.");
    	}
    }

    public function register()
    {
    	return view('auth.register');
    }

    public function confirmRegister(Request $request)
    {
    	$password = $request->get('password');
    	$password_confirm = $request->get('password_confirm');

    	if ($password == $password_confirm) {
    		$cek = User::where('username',$request->get('username'))->first();
    		if (count($cek) == 0) {
    			$users = User::create(
					[
						'name' => $request->get('full_name'),
						'username' => $request->get('username'),
						'email' => $request->get('email'),
						'password' => Hash::make($request->get('password')),
						'company' => $request->get('company'),
						'role_code' => '',
						'status' => 'Unconfirmed',
						'avatar' => 'image-user.png',
						'created_by' => 1
					]
				);
				$users->save();

				$to = [
					'erlangga.kharisma@music.yamaha.com',
					'shega.erik.wicaksono@music.yamaha.com',
					'amelia.novrinta@music.yamaha.com',
					'bakhtiar.muslim@music.yamaha.com'
				];

				Mail::to($to)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($users, 'register'));

	    		return redirect('')->with('success','Your account has been created. Please Wait For the Confirmation');
    		}else{
    			return back()->with('error', "This credentials already exists.");
    		}
    	
    	}else{
    		return back()->with('error', "Password doesn't match.");
    	}
    }

	// $full_name = $request->get('full_name');
	// $mail_to = $request->get('email');
	// $contactList = [];
 //    $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
 //    $contactList[1] = 'rio.irvansyah@music.yamaha.com';
	// Mail::to($mail_to)->bcc($contactList,'BCC')->send(new SendEmail($full_name, 'register'));

    public function terms()
    {
        # code...
    }

    public function policy()
    {
    	return view('billing.policy');
    }
}
