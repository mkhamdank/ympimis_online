<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TrialController extends Controller
{
    public function testmail()
	{
		try {
			// $mail = ['mokhamad.khamdan.khabibi@music.yamaha.com',
			// 'rio.irvansyah@music.yamaha.com',
			// 'muhammad.ikhlas@music.yamaha.com',
			// 'nasiqul.ibat@music.yamaha.com'];

			$mail = [
				'mokhamad.khamdan.khabibi@music.yamaha.com',
				'rio.irvansyah@music.yamaha.com',
				'nasiqul.ibat@music.yamaha.com',
				'muhammad.ikhlas@music.yamaha.com'
			]
			;
			$bodyHtml2 = "MIS Test Mail dari MIRAI Online";

			Mail::raw([], function($message) use($bodyHtml2,$mail) {
				$message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
				$message->to($mail);
				$message->subject('MIS Test Mail');
				$message->setBody($bodyHtml2, 'text/html' );
			});
		} catch (\Exception $e) {
			if ($e instanceof \Swift_TransportException){
				echo '</script>alert("Email Tidak Terkirim")</script>';
			}
		}
	}
}
