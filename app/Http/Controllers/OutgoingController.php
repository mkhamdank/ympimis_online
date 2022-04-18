<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Response;
use Excel;
use App\QaMaterial;
use App\ErrorLog;
use App\QaOutgoingVendor;
use App\QaOutgoingVendorRecheck;
use App\QaInspectionLevel;
use App\QaOutgoingSerialNumber;
use App\QaOutgoingVendorFinal;
use App\CodeGenerator;
use App\QaOutgoingPointCheck;

class OutgoingController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }

        $this->critical_true = ['Berjamur',
			'Kotor Serangga',
			'Flashing',
			'Salah Spec',
			'Salah Label',
			'Material Kurang',
			'Material Lebih',
			'Material Tercampur',
			'Tidak Terplating',
			'Plating Kelupas',
			'Tanpa Shoulder Strap',
			'Hook Patah',
		];

		$this->non_critical_true = ['Part Cuil',
			'Cloth Gundul',
			'Terlihat Kayu',
			'Terlihat Styrofoam',
			'Sobek',
			'Kelupas',
			'Celah',
			'Kaku',
			'Longgar',
			'Kerut',
			'Coretan',
			'Kotor Lem',
			'Karat',
			'Kotor Tinta / Kapur',
			'Scratch / Gores',
			'Bergelombang',
			'Cekung',
			'Cembung',
			'Miring',
			'Geser Sliding',
			'Buram',
			'Belang',
			'Painting Terkontamintasi',
			'Plating Beleber',
			'Plating Tipis',
			'Plating Kasar',
			'Sisa Benang',
			'Bahan Kain Kurang',
			'Part Goyang',
			'Jahitan Lepas',
		];

		$this->critical_arisa = ['Bari (flashing)',
					'Short-shoot',
					'Salah kunci',
					'Salah spec',
					'Material kurang'];

		$this->non_critical_arisa = [
			'Kizu (scratch)',
			'Blackmark',
			'Kake (cuil)',
			'Flowmark',
			'Flowmark',
			'Silver',
			'Ketinggian kunci',
			'Katai (kaku)',
			'Yurui (longgar)',
			'Coretan',
			'Ana (berlubang)',
			'Sukima (celah)',
			'Nami (bergelombang)',
			'Heko (cekung)',
			'Deko (cembung)',
			'Overpack',
			'Ware (retak)',
			'Toke (meleleh)',
			'Usui (tipis)',
			'Atsui (tebal)',
			'Noise / suara benda asing',
			'Sinmark',
			'Buram',
			'Belang',
			'Terlalu terang',
			'Terlalu gelap',
			'Shiny (berkilau)',
			'Yogore (kotor)',
			'Kotor serangga',
			'Butsu (bintik jarum)',
			'Kumori (kusam)',
			'Bending',
			'Yabure (sobek)',
			'Hagare (kelupas)',
			'Zure (geser)',
			'Shiwa (kerut)',
			'Salah label',
			'Material lebih',
			'Material tercampur',
			'Twist (mulet)',
		];

		$this->critical_kbi = ['Bari (flashing)',
					'Short-shoot',
					'Salah kunci',
					'Salah spec',
					'Material kurang'];

		$this->non_critical_kbi = ['Ibutsu',
			'Tankabutsu',
			'Dirty / Kotor',
			'Penyok',
			'Kontaminasi',
			'Thickness Tebal',
			'Thickness Tipis',
			'Material  Mentah',
			'Over Cutting',
			'Scratch / Kizu',
			'White spot / Hakka',
			'Shiwa / kerut',
			'Die line',
			'White line',
			'Henkei / kembung',
			'Dekok',
			'Short Mold',
			'Crack',
			'Pinhole',
			'Fitting NG',
			'Step',
			'Cutting Bergerigi',
			'Flashing',
			'Peel Off',
			'Case lock yurui / longgar',
			'Case lock katai / berat',
			'Nikudamari',
			'Noise',
			'Buble',
			'Weight NG',
			'Sukima / celah',
			'Child Part NG',
			'Insert Child Part NG',
			'Hole NG ',
			'Leak Test Bocor',
			'Drilling NG',
			'Doll',
			'Hekomi',
			'Terbakar/meleleh',
			'Cutting NG',
			'Henkei/Kembung',
			'Chiping',
			'PL Line NG',
			'Cutting tajam',
			'Vacuum NG',
];
  	}
    public function index($vendor){
		if ($vendor == 'true') {
			$title = 'Vendor Final Inspection - PT. TRUE';
			$page = 'Vendor Final Inspection - True';
			$title_jp = '';
		}else if ($vendor == 'kbi') {
			$title = 'Vendor Final Inspection - PT. KBI';
			$page = 'Vendor Final Inspection - KBI';
			$title_jp = '';
		}else if ($vendor == 'arisa') {
			$title = 'Vendor Final Inspection - PT. ARISA';
			$page = 'Vendor Final Inspection - ARISA';
			$title_jp = '';
		}

		if (Auth::user()->role_code == strtoupper($vendor) || Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'E - Purchasing') {
			return view('outgoing.index', array(
				'title' => $title,
				'vendor' => $vendor,
				'title_jp' => $title_jp,
			))->with('page', $page)->with('head', $page);
		}
		else{
			return view('404');
		}
	}

	public function indexInputTrue()
	{
		$title = 'Input Final Inspection';
		$title_jp = '';

		$ng_lists = DB::SELECT("select * from ng_lists where ng_lists.location = 'outgoing' and remark = 'true'");

		$materials = QaMaterial::where('vendor_shortname','TRUE')->get();

		return view('outgoing.true.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'ng_lists' => $ng_lists,
			'vendor' => 'PT. TRUE INDONESIA',
			'inspector' => Auth::user()->name,
			'materials' => $materials,
		))->with('page', 'Input Final Inspection TRUE')->with('head', 'Input Final Inspection TRUE');
	}

	public function fetchMaterialTrue(Request $request)
	{
		try {
			$target = QaOutgoingSerialNumber::where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'),date('Y-m',strtotime($request->get('periode'))))->where('material_number',$request->get('material_number'))->first();

			if (count($target) > 0) {
				$response = array(
			        'status' => true,
			        'target' => $target,
			    );
			    return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function confirmInputTrue(Request $request)
	{
		try {

			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$qty_check = $request->get('qty_check');
			$total_ok = $request->get('total_ok');
			$total_ng = $request->get('total_ng');
			$ng_ratio = $request->get('ng_ratio');
			$inspector = $request->get('inspector');
			$ng_name = $request->get('ng_name');
			$ng_qty = $request->get('ng_qty');
			$jumlah_ng = $request->get('jumlah_ng');
			// $check_date = $request->get('check_date');
			$check_date = date('Y-m-d');
			$serial_number = $request->get('serial_number');
			$material = QaMaterial::where('material_number',$material_number)->first();
			$outgoings = [];
			$outgoing_id = [];
			$outgoings_critical = [];
			$outgoings_non_critical = [];
			if ($total_ng == 0) {
				$outgoing = new QaOutgoingVendor([
					'check_date' => $check_date,
					'material_number' => $material_number,
					'material_description' => $material_description,
					'serial_number' => $serial_number,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'total_ok' => $total_ok,
					'total_ng' => $total_ng,
					'ng_ratio' => $ng_ratio,
					'ng_name' => '-',
					'ng_qty' => '0',
					'lot_status' => 'LOT OK',
	                'created_by' => Auth::user()->id
	            ]);
	            $outgoing->save();
			}else{
				for ($i=0; $i < count($ng_name); $i++) { 
					$outgoing = new QaOutgoingVendor([
						'check_date' => $check_date,
						'material_number' => $material_number,
						'material_description' => $material_description,
						'vendor' => $material->vendor,
						'vendor_shortname' => $material->vendor_shortname,
						'hpl' => $material->hpl,
						'inspector' => $inspector,
						'serial_number' => $serial_number,
						'qty_check' => $qty_check,
						'total_ok' => $total_ok,
						'total_ng' => $total_ng,
						'ng_ratio' => $ng_ratio,
						'ng_name' => $ng_name[$i],
						'ng_qty' => $ng_qty[$i],
		                'created_by' => Auth::user()->id,
		            ]);

		            $outgoing->save();

		            array_push($outgoing_id, $outgoing->id);
		            if (in_array($ng_name[$i], $this->critical_true)) {
		            	$mail_to = [];

		            	array_push($mail_to, 'true.indonesia@yahoo.com');
		            	array_push($mail_to, 'truejhbyun@naver.com');
		            	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');
		            	array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');
		            	array_push($mail_to, 'ardianto@music.yamaha.com');

				        $cc = [];
				        $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        $outgoing_update = QaOutgoingVendor::where('id',$outgoing->id)->first();
				        $outgoing_update->lot_status = 'LOT OUT';
				        $outgoing_update->save();

				        $outgoing_criticals = QaOutgoingVendor::where('id',$outgoing->id)->first();
				        array_push($outgoings_critical, $outgoing_criticals);

				        Mail::to($mail_to)
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($outgoing_criticals, 'critical_true'));
		            }

		            if (in_array($ng_name[$i], $this->non_critical_true)) {
		            	array_push($outgoings, $outgoing);
		            }
				}

				$total_ng_non = 0;
				for ($i=0; $i < count($outgoings); $i++) { 
					$total_ng_non = $total_ng_non + $outgoings[$i]->ng_qty;
				}

				if ($total_ng_non != 0) {
					$persen = ($total_ng_non/$qty_check)*100;
					if ($persen > 5) {
						$mail_to = [];

		            	array_push($mail_to, 'true.indonesia@yahoo.com');
		            	array_push($mail_to, 'truejhbyun@naver.com');
		            	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');
		            	array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');
		            	array_push($mail_to, 'ardianto@music.yamaha.com');

				        $cc = [];
				        $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        for ($i=0; $i < count($outgoing_id); $i++) { 
				        	$outgoing_update = QaOutgoingVendor::where('id',$outgoing_id[$i])->first();
					        $outgoing_update->lot_status = 'LOT OUT';
					        $outgoing_update->save();

					        $outgoing_non_critical = QaOutgoingVendor::where('id',$outgoing_id[$i])->first();
					        array_push($outgoings_non_critical, $outgoing_non_critical);
				        }

				        $data = array(
				        	'outgoing_non' => $outgoings_non_critical,
				        	'outgoing_critical' => $outgoings_critical, );

				        Mail::to($mail_to)
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($data, 'over_limit_ratio_true'));
					}
				}
			}

			// $updateSchedule = QaOutgoingSerialNumber::where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'),date('Y-m',strtotime($check_date)))->where('material_number',$material_number)->first();
			// $updateSchedule->qty_actual = $updateSchedule->qty_actual+$qty_check;
			// $updateSchedule->save();
			
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexInputTrueRecheck($serial_number,$check_date)
	{
		$title = 'Input Recheck Material PT. TRUE INDONESIA';
		$title_jp = '';

		$ng_lists = DB::SELECT("select * from ng_lists where ng_lists.location = 'outgoing' and remark = 'true'");

		$materials = QaMaterial::where('vendor_shortname','TRUE')->get();

		$outgoing = QaOutgoingVendor::where('serial_number',$serial_number)->where('check_date',$check_date)->get();

		return view('outgoing.true.index_recheck', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'ng_lists' => $ng_lists,
			'outgoing' => $outgoing,
			'vendor' => 'PT. TRUE INDONESIA',
			'inspector' => Auth::user()->name,
			'materials' => $materials,
		))->with('page', 'Input Final Inspection TRUE')->with('head', 'Input Final Inspection TRUE');
	}
	public function confirmInputTrueRecheck(Request $request)
	{
		try {
			$check_recheck = QaOutgoingVendorRecheck::where('serial_number',$request->get('serial_number'))->first();
			if (count($check_recheck) > 0) {
				$response = array(
			        'status' => false,
			        'message' => 'Serial Number sudah dilakukan Recheck.',
			    );
			    return Response::json($response);
			}
			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$qty_check = $request->get('qty_check');
			$total_ok = $request->get('total_ok');
			$total_ng = $request->get('total_ng');
			$ng_ratio = $request->get('ng_ratio');
			$inspector = $request->get('inspector');
			$ng_name = $request->get('ng_name');
			$ng_qty = $request->get('ng_qty');
			$jumlah_ng = $request->get('jumlah_ng');
			// $check_date = $request->get('check_date');
			$check_date = date('Y-m-d');
			$serial_number = $request->get('serial_number');
			$material = QaMaterial::where('material_number',$material_number)->first();
			$outgoings = [];
			$outgoing_id = [];
			$outgoings_critical = [];
			$outgoings_non_critical = [];
			if ($total_ng == 0) {
				$outgoing = new QaOutgoingVendorRecheck([
					'check_date' => $check_date,
					'material_number' => $material_number,
					'material_description' => $material_description,
					'serial_number' => $serial_number,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'total_ok' => $total_ok,
					'total_ng' => $total_ng,
					'ng_ratio' => $ng_ratio,
					'ng_name' => '-',
					'ng_qty' => '0',
					'lot_status' => 'LOT OK',
	                'created_by' => Auth::user()->id
	            ]);
	            $outgoing->save();
			}else{
				for ($i=0; $i < count($ng_name); $i++) { 
					$outgoing = new QaOutgoingVendorRecheck([
						'check_date' => $check_date,
						'material_number' => $material_number,
						'material_description' => $material_description,
						'vendor' => $material->vendor,
						'vendor_shortname' => $material->vendor_shortname,
						'hpl' => $material->hpl,
						'inspector' => $inspector,
						'serial_number' => $serial_number,
						'qty_check' => $qty_check,
						'total_ok' => $total_ok,
						'total_ng' => $total_ng,
						'ng_ratio' => $ng_ratio,
						'ng_name' => $ng_name[$i],
						'ng_qty' => $ng_qty[$i],
		                'created_by' => Auth::user()->id,
		            ]);

		            $outgoing->save();

		            array_push($outgoing_id, $outgoing->id);
		            if (in_array($ng_name[$i], $this->critical_true)) {
		            	// $mail_to = [];

		          //   	array_push($mail_to, 'true.indonesia@yahoo.com');
		          //   	array_push($mail_to, 'truejhbyun@naver.com');
		          //   	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		          //   	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		          //   	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		          //   	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				        // $cc = [];
				        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        // $bcc = [];
				        // $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        // $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        $outgoing_update = QaOutgoingVendorRecheck::where('id',$outgoing->id)->first();
				        $outgoing_update->lot_status = 'LOT OUT';
				        $outgoing_update->save();

				        $outgoing_criticals = QaOutgoingVendorRecheck::where('id',$outgoing->id)->first();
				        array_push($outgoings_critical, $outgoing_criticals);

				        // Mail::to($mail_to)
				        // // ->cc($cc,'CC')
				        // ->bcc($bcc,'BCC')
				        // ->send(new SendEmail($outgoing_criticals, 'critical_true'));
		            }

		            if (in_array($ng_name[$i], $this->non_critical_true)) {
		            	array_push($outgoings, $outgoing);
		            }
				}

				$total_ng_non = 0;
				for ($i=0; $i < count($outgoings); $i++) { 
					$total_ng_non = $total_ng_non + $outgoings[$i]->ng_qty;
				}

				if ($total_ng_non != 0) {
					$persen = ($total_ng_non/$qty_check)*100;
					if ($persen > 5) {
						// $mail_to = [];

		    //         	array_push($mail_to, 'true.indonesia@yahoo.com');
		    //         	array_push($mail_to, 'truejhbyun@naver.com');
		    //         	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		    //         	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		    //         	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		    //         	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				  //       $cc = [];
				  //       $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				  //       $cc[1] = 'imron.faizal@music.yamaha.com';

				  //       $bcc = [];
				  //       $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				  //       $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        for ($i=0; $i < count($outgoing_id); $i++) { 
				        	$outgoing_update = QaOutgoingVendorRecheck::where('id',$outgoing_id[$i])->first();
					        $outgoing_update->lot_status = 'LOT OUT';
					        $outgoing_update->save();

					        $outgoing_non_critical = QaOutgoingVendorRecheck::where('id',$outgoing_id[$i])->first();
					        array_push($outgoings_non_critical, $outgoing_non_critical);
				        }

				        // $data = array(
				        // 	'outgoing_non' => $outgoings_non_critical,
				        // 	'outgoing_critical' => $outgoings_critical, );

				        // Mail::to($mail_to)
				        // // ->cc($cc,'CC')
				        // ->bcc($bcc,'BCC')
				        // ->send(new SendEmail($data, 'over_limit_ratio_true'));
					}
				}
			}

			$outgoing_check = QaOutgoingVendor::where('serial_number',$serial_number)->get();
			for ($i=0; $i < count($outgoing_check); $i++) { 
				$outgoing_checks = QaOutgoingVendor::where('id',$outgoing_check[$i]->id)->first();
				$outgoing_checks->recheck_status = 'Checked';
				$outgoing_checks->save();
			}

			// $updateSchedule = QaOutgoingSerialNumber::where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'),date('Y-m',strtotime($check_date)))->where('material_number',$material_number)->first();
			// $updateSchedule->qty_actual = $updateSchedule->qty_actual+$qty_check;
			// $updateSchedule->save();
			
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexInputArisa()
	{
		$title = 'Input Final Inspection';
		$title_jp = '';

		$product = DB::SELECT("SELECT DISTINCT
			( material_number ),
			material_description,
			material_alias,
			hexa_button,
			part 
		FROM
			qa_outgoing_point_checks
			order by material_alias");

		return view('outgoing.arisa.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'product' => $product,
			'inspector' => Auth::user()->name,
			'vendor' => 'PT. ARISAMANDIRI PRATAMA',
		))->with('page', 'Input Final Inspection ARISA')->with('head', 'Input Final Inspection ARISA');
	}

	public function fetchInspectionLevel(Request $request)
	{
		try {
			$inspection_levels = QaInspectionLevel::where('remark',$request->get('vendor'))->get();
			$response = array(
		        'status' => true,
		        'inspection_levels' => $inspection_levels,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchKensaSerialNumber(Request $request)
	{
		try {
			$kensa_serial_number = DB::SELECT("SELECT DISTINCT
			( serial_number ),
			total_ok,
			total_ng,
			qty_check 
		FROM
			qa_outgoing_vendors 
		WHERE
			qa_final_status IS NULL 
			AND vendor_shortname = 'ARISA'
			and material_number = '".$request->get('material_number')."'");

			$response = array(
		        'status' => true,
		        'kensa_serial_number' => $kensa_serial_number
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchPointCheck(Request $request)
	{
		try {
			$point_check = QaOutgoingPointCheck::where('vendor_shortname',strtoupper($request->get('vendor')))->where('material_number',$request->get('material_number'))->get();
			$response = array(
		        'status' => true,
		        'point_check' => $point_check
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function confirmInputArisa(Request $request)
	{
		try {
			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$part = $request->get('part');
			$material_alias = $request->get('material_alias');
			$qty_check_appearance = $request->get('qty_check_appearance');
			$qty_check_functional = $request->get('qty_check_functional');
			$qty_check_dimensional = $request->get('qty_check_dimensional');
			$final_serial_number = $request->get('final_serial_number');
			// $serial_number = $request->get('serial_number');
			$serial_number_for = $request->get('final_serial_number');
			$lot_status = $request->get('lot_status');
			// $appearance_ng = $request->get('appearance_ng');
			// $functional_ng = $request->get('functional_ng');
			$inspector = $request->get('inspector');
			$result_check = $request->get('result_check');

			$material = QaMaterial::where('material_number',$material_number)->first();

			$lot_out_detail = [];

			for ($i=0; $i < count($result_check); $i++) { 
				$point_check = QaOutgoingPointCheck::where('id',$result_check[$i]['point_check_id'])->first();
				$inspection = QaInspectionLevel::where('remark','ARISA')->where('sample_size',$qty_check_appearance)->first();
				$outgoings = QaOutgoingVendor::where('serial_number',$final_serial_number)->first();

				if ($point_check->point_check_type == 'APPEARANCE CHECK') {
					$qty_check = $qty_check_appearance;
				}else if($point_check->point_check_type == 'FUNCTIONAL CHECK'){
					$qty_check = $qty_check_functional;
				}else{
					$qty_check = $qty_check_dimensional;
				}

				$outgoing = new QaOutgoingVendorFinal([
					'material_number' => $material_number,
					'material_description' => $material_description,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'point_check_id' => $result_check[$i]['point_check_id'],
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'final_serial_number' => $final_serial_number,
					'lot_status' => $lot_status,
					// 'serial_number' => join(",",$serial_number),
					// 'total_ok' => $total_ok,
					// 'total_ng' => $total_ng,
					// 'ng_ratio' => $ng_ratio,
					// 'ng_name' => $ng_name,
					// 'ng_qty' => $ng_qty,
					'product_index' => $result_check[$i]['product_index'],
					'product_result' => $result_check[$i]['result'],
					'created_by' => Auth::user()->id,
	            ]);

	            $outgoing->save();

	            $lot_out_details = array(
					'material_number' => $material_number,
					'material_description' => $material_description,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'qty_check' => $qty_check,
					'final_serial_number' => $final_serial_number,
					'lot_status' => $lot_status,
					'check_date' =>$outgoings->check_date,
					'point_check' => $point_check->point_check_name,
					'product_index' => $result_check[$i]['product_index'],
					'product_result' => $result_check[$i]['result'],
				);

				array_push($lot_out_detail,$lot_out_details);
			}

			// for ($j=0; $j < count($serial_number_for); $j++) { 
				$final = QaOutgoingVendor::where('serial_number',$serial_number_for)->get();
				for ($k=0; $k < count($final); $k++) { 
					$final_update = QaOutgoingVendor::where('id',$final[$k]->id)->first();
					$final_update->qa_final_status = 'Checked';
					$final_update->lot_status = $lot_status;
					$final_update->save();
				}

				if ($lot_status == 'LOT OUT') {
					$mail_to = [];

	            	array_push($mail_to, 'quality-ars@tigermp.co.id');
	            	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
	            	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
	            	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
	            	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');
	            	array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');

			        $cc = [];
			        $cc[0] = 'yayuk.wahyuni@music.yamaha.com';

			        $bcc = [];
			        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
			        $bcc[1] = 'rio.irvansyah@music.yamaha.com';

			        Mail::to($mail_to)
			        ->cc($cc,'CC')
			        ->bcc($bcc,'BCC')
			        ->send(new SendEmail($lot_out_detail, 'lot_out_arisa'));
				}
			// }
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data'
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function kensaSerialNumber($vendor)
	{
		try {
			$code_generator = CodeGenerator::where('note', '=', $vendor)->first();
	        $serial_number = $code_generator->prefix.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
	        $code_generator->index = $code_generator->index+1;
	        $code_generator->save();

	        $response = array(
		        'status' => true,
		        'serial_number' => $serial_number
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function finalSerialNumber($vendor)
	{
		try {
			$code_generator = CodeGenerator::where('note', '=', $vendor.'_final')->first();
	        $serial_number = $code_generator->prefix.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
	        $code_generator->index = $code_generator->index+1;
	        $code_generator->save();

	        $response = array(
		        'status' => true,
		        'serial_number' => $serial_number
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexKensaArisa()
	{
		$title = 'Production Check PT. ARISA';
		$page = 'Production Check ARISA';
		$title_jp = '';

		$ng_lists = DB::SELECT("select * from ng_lists where ng_lists.location = 'outgoing' and remark = 'arisa'");

		$materials = DB::SELECT("SELECT DISTINCT
					( material_number ),
					material_description,
					material_alias,
					part 
				FROM
					qa_outgoing_point_checks");

		return view('outgoing.arisa.kensa_arisa', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'ng_lists' => $ng_lists,
			'vendor' => 'PT. ARISAMANDIRI PRATAMA',
			'materials' => $materials,
			'inspector' => Auth::user()->name,
		))->with('page', $page)->with('head', $page);
	}

	public function confirmKensaArisa(Request $request)
	{
		try {

			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$qty_check = $request->get('qty_check');
			$total_ok = $request->get('total_ok');
			$total_ng = $request->get('total_ng');
			$ng_ratio = $request->get('ng_ratio');
			$inspector = $request->get('inspector');
			$ng_name = $request->get('ng_name');
			$serial_number = $request->get('serial_number');
			$ng_qty = $request->get('ng_qty');
			$jumlah_ng = $request->get('jumlah_ng');
			$material = QaMaterial::where('material_number',$material_number)->first();
			$outgoings = [];
			$outgoing_id = [];
			$outgoings_critical = [];
			$outgoings_non_critical = [];
			$outgoings = [];
			if ($total_ng == 0) {
				$outgoing = new QaOutgoingVendor([
					'material_number' => $material_number,
					'check_date' => date('Y-m-d'),
					'material_description' => $material_description,
					'serial_number' => $serial_number,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'total_ok' => $total_ok,
					'total_ng' => $total_ng,
					'ng_ratio' => $ng_ratio,
					'ng_name' => '-',
					'ng_qty' => '0',
					'lot_status' => 'LOT OK',
	                'created_by' => Auth::user()->id
	            ]);
	            $outgoing->save();
			}else{
				for ($i=0; $i < count($ng_name); $i++) { 
					$outgoing = new QaOutgoingVendor([
						'check_date' => date('Y-m-d'),
						'material_number' => $material_number,
						'material_description' => $material_description,
						'serial_number' => $serial_number,
						'vendor' => $material->vendor,
						'vendor_shortname' => $material->vendor_shortname,
						'hpl' => $material->hpl,
						'inspector' => $inspector,
						'qty_check' => $qty_check,
						'total_ok' => $total_ok,
						'total_ng' => $total_ng,
						'ng_ratio' => $ng_ratio,
						'ng_name' => $ng_name[$i],
						'ng_qty' => $ng_qty[$i],
		                'created_by' => Auth::user()->id
		            ]);

		            $outgoing->save();
		            array_push($outgoing_id, $outgoing->id);
		            if (in_array($ng_name[$i], $this->critical_arisa)) {
		            	$mail_to = [];

		            	array_push($mail_to, 'quality-ars@tigermp.co.id');
		            	// array_push($mail_to, 'suryanti@tigermp.co.id');
		            	// array_push($mail_to, 'achmad.rofiq@tigermp.co.id');
		            	// array_push($mail_to, 'agoes.jupri@tigermp.co.id');
		            	// array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	// array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	// array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	// array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');
		            	// array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');

				        $cc = [];
				        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        // $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        $outgoing_update = QaOutgoingVendor::where('id',$outgoing->id)->first();
				        $outgoing_update->lot_status = 'LOT OUT';
				        $outgoing_update->save();

				        $outgoing_criticals = QaOutgoingVendor::where('id',$outgoing->id)->first();
				        array_push($outgoings_critical, $outgoing_criticals);

				        Mail::to($mail_to)
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($outgoing_criticals, 'critical_arisa'));

				        // array_push($outgoings_critical, $outgoing);
		            }

		            if (in_array($ng_name[$i], $this->non_critical_arisa)) {
		            	array_push($outgoings, $outgoing);
		            }
				}


				$total_ng_non = 0;
				for ($i=0; $i < count($outgoings); $i++) { 
					$total_ng_non = $total_ng_non + $outgoings[$i]->ng_qty;
				}

				if ($total_ng_non != 0) {
					$persen = ($total_ng_non/$qty_check)*100;
					if ($persen > 5) {
						$mail_to = [];

		            	array_push($mail_to, 'quality-ars@tigermp.co.id');
		            	// array_push($mail_to, 'suryanti@tigermp.co.id');
		            	// array_push($mail_to, 'achmad.rofiq@tigermp.co.id');
		            	// array_push($mail_to, 'agoes.jupri@tigermp.co.id');
		            	// array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	// array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	// array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	// array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');
		            	// array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');

				        $cc = [];
				        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        // $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        for ($i=0; $i < count($outgoing_id); $i++) { 
				        	$outgoing_update = QaOutgoingVendor::where('id',$outgoing_id[$i])->first();
					        $outgoing_update->lot_status = 'LOT OUT';
					        $outgoing_update->save();

					        $outgoing_non_critical = QaOutgoingVendor::where('id',$outgoing_id[$i])->first();
					        array_push($outgoings_non_critical, $outgoing_non_critical);
				        }

				        $data = array(
				        	'outgoing_non' => $outgoings_non_critical,
				        	'outgoing_critical' => $outgoings_critical, );

				        Mail::to($mail_to)
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($data, 'over_limit_ratio_arisa'));
					}
				}
			}
			
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexReportKensaArisa()
	{
		$title = 'Report Production Check PT. ARISA';
		$page = 'Report Production Check ARISA';
		$title_jp = '';

		return view('outgoing.arisa.report_kensa_arisa', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => 'PT. ARISAMANDIRI PRATAMA',
		))->with('page', $page)->with('head', $page);
	}

	public function fetchReportKensaArisa()
	{
		try {
			$outgoing = QaOutgoingVendor::where('vendor_shortname','ARISA')->orderby('qa_outgoing_vendors.created_at','desc')->get();

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexReportQcArisa()
	{
		$title = 'Report QC Final Check PT. ARISA';
		$page = 'Report QC Final Check ARISA';
		$title_jp = '';

		return view('outgoing.arisa.report_qc_arisa', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => 'PT. ARISAMANDIRI PRATAMA',
		))->with('page', $page)->with('head', $page);
	}

	public function fetchReportQcArisa()
	{
		try {
			$outgoing = QaOutgoingVendorFinal::where('qa_outgoing_vendor_finals.vendor_shortname','ARISA')
			->join('qa_outgoing_point_checks','qa_outgoing_point_checks.id','qa_outgoing_vendor_finals.point_check_id')
			->orderby('qa_outgoing_vendor_finals.created_at','desc')
			->get();
			// $allchecks = [];
			// for ($i=0; $i < count($outgoing); $i++) { 
			// 	$sernum = explode(',', $outgoing[$i]->serial_number);
			// 	for ($j=0; $j < count($sernum); $j++) { 
			// 		$allcheck = DB::SELECT("SELECT
			// 			CONCAT(
			// 				serial_number,
			// 				'_',
			// 				material_number,
			// 				'_',
			// 				total_ok,
			// 				'_',
			// 				total_ng,
			// 				'_',
			// 				ng_ratio,
			// 				'_',
			// 				GROUP_CONCAT( ng_name ),
			// 				'_',
			// 			GROUP_CONCAT( ng_qty )) AS result_check 
			// 		FROM
			// 			`qa_outgoing_vendors` 
			// 		WHERE
			// 			serial_number = '".$sernum[$j]."' 
			// 		GROUP BY
			// 			material_number,
			// 			total_ok,
			// 			total_ng,
			// 			ng_ratio");
			// 			array_push($allchecks, [
			// 				'serial_number' => $sernum[$j],
			// 				'result_check' => $allcheck[0]->result_check
			// 			]);
			// 	}
			// }

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		        // 'allchecks' => $allchecks,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function inputSONumberArisa(Request $request)
	{
		try {
			$final_serial_number = $request->get('final_serial_number');
			$so_number = $request->get('so_number');

			$final_arisa = QaOutgoingVendorFinal::where('qa_outgoing_vendor_finals.vendor_shortname','ARISA')->get();
			for ($i=0; $i < count($final_arisa); $i++) { 
				$final = QaOutgoingVendorFinal::where('id',$final_arisa[$i]->id)->first();
				$final->so_number = $so_number;
				$final->save();
			}

			$response = array(
		        'status' => true,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexKensaKbi()
	{
		$title = 'Production Check PT. KBI';
		$page = 'Production Check KBI';
		$title_jp = '';

		$ng_lists = DB::SELECT("select * from ng_lists where ng_lists.location = 'outgoing' and remark = 'kbi'");

		$materials = QaMaterial::where('vendor_shortname','KYORAKU')->get();

		return view('outgoing.kbi.kensa_kbi', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'ng_lists' => $ng_lists,
			'vendor' => 'PT. KBI',
			'materials' => $materials,
			'inspector' => Auth::user()->name,
		))->with('page', $page)->with('head', $page);
	}

	public function scanKensaKbi(Request $request)
	{
		try {
			$serial_number = QaOutgoingSerialNumber::where('vendor_shortname','KYORAKU')->where('serial_number',$request->get('serial_number'))->where('status',null)->first();
			if (count($serial_number) > 0) {
				$response = array(
			        'status' => true,
			        'serial_number' => $serial_number,
			    );
			    return Response::json($response);
			}else{
				$response = array(
			        'status' => false,
			        'message' => 'Serial Number Invalid',
			    );
			    return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function confirmKensaKbi(Request $request)
	{
		try {

			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$qty_check = $request->get('qty_check');
			$total_ok = $request->get('total_ok');
			$total_ng = $request->get('total_ng');
			$ng_ratio = $request->get('ng_ratio');
			$inspector = $request->get('inspector');
			$ng_name = $request->get('ng_name');
			$serial_number = $request->get('serial_number');
			$ng_qty = $request->get('ng_qty');
			$jumlah_ng = $request->get('jumlah_ng');
			$material = QaMaterial::where('material_number',$material_number)->first();
			$outgoings = [];
			$outgoings_critical = [];
			if ($total_ng == 0) {
				$outgoing = new QaOutgoingVendor([
					'check_date' => date('Y-m-d'),
					'material_number' => $material_number,
					'material_description' => $material_description,
					'serial_number' => $serial_number,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'total_ok' => $total_ok,
					'total_ng' => $total_ng,
					'ng_ratio' => $ng_ratio,
					'ng_name' => '-',
					'ng_qty' => '0',
					'lot_status' => 'LOT OK',
	                'created_by' => Auth::user()->id
	            ]);
	            $outgoing->save();
			}else{
				for ($i=0; $i < count($ng_name); $i++) { 
					$outgoing = new QaOutgoingVendor([
						'check_date' => date('Y-m-d'),
						'material_number' => $material_number,
						'material_description' => $material_description,
						'serial_number' => $serial_number,
						'vendor' => $material->vendor,
						'vendor_shortname' => $material->vendor_shortname,
						'hpl' => $material->hpl,
						'inspector' => $inspector,
						'qty_check' => $qty_check,
						'total_ok' => $total_ok,
						'total_ng' => $total_ng,
						'ng_ratio' => $ng_ratio,
						'ng_name' => $ng_name[$i],
						'ng_qty' => $ng_qty[$i],
		                'created_by' => Auth::user()->id
		            ]);

		            $outgoing->save();


		            if (in_array($ng_name[$i], $this->critical_kbi)) {
		            	$mail_to = [];

		            	array_push($mail_to, 'h_susanto@kyoraku.co.id');
		            	array_push($mail_to, 'qs@kyoraku.co.id');
		            	array_push($mail_to, 'qa.claim@kyoraku.co.id');
		            	array_push($mail_to, 'ujang@kyoraku.co.id');
		            	array_push($mail_to, 'ginting@kyoraku.co.id');
		            	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				        $cc = [];
				        $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        Mail::to('mokhamad.khamdan.khabibi@music.yamaha.com')
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($outgoing, 'critical_kbi'));

				        array_push($outgoings_critical, $outgoing);
		            }

		            if (in_array($ng_name[$i], $this->non_critical_kbi)) {
		            	array_push($outgoings, $outgoing);
		            }
				}

				$total_ng_non = 0;
				for ($i=0; $i < count($outgoings); $i++) { 
					$total_ng_non = $total_ng_non + $outgoings[$i]->ng_qty;
				}

				if ($total_ng_non != 0) {
					$persen = ($total_ng_non/$qty_check)*100;
					if ($persen > 5) {
						$mail_to = [];

		            	array_push($mail_to, 'h_susanto@kyoraku.co.id');
		            	array_push($mail_to, 'qs@kyoraku.co.id');
		            	array_push($mail_to, 'qa.claim@kyoraku.co.id');
		            	array_push($mail_to, 'ujang@kyoraku.co.id');
		            	array_push($mail_to, 'ginting@kyoraku.co.id');
		            	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		            	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		            	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		            	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				        $cc = [];
				        $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        $cc[1] = 'imron.faizal@music.yamaha.com';

				        $bcc = [];
				        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        $data = array(
				        	'outgoing_non' => $outgoings,
				        	'outgoing_critical' => $outgoings_critical, );

				        Mail::to('mokhamad.khamdan.khabibi@music.yamaha.com')
				        ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($data, 'over_limit_ratio_kbi'));
					}
				}
			}

			$updateSernum = QaOutgoingSerialNumber::where('serial_number',$serial_number)->where('material_number',$material_number)->first();
			$updateSernum->status = 'Used';
			$updateSernum->save();
			
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexReportKensaKbi()
	{
		$title = 'Report Production Check PT. KBI';
		$page = 'Report Production Check KBI';
		$title_jp = '';

		return view('outgoing.kbi.report_kensa_kbi', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => 'PT. KBI',
		))->with('page', $page)->with('head', $page);
	}

	public function fetchReportKensaKbi()
	{
		try {
			$outgoing = QaOutgoingVendor::where('vendor_shortname','KYORAKU')->get();

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexReportKensaTrue()
	{
		$title = 'Report Production Check PT. TRUE';
		$page = 'Report Production Check TRUE';
		$title_jp = '';

		return view('outgoing.true.report_kensa_true', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => 'PT. TRUE',
		))->with('page', $page)->with('head', $page);
	}

	public function fetchReportKensaTrue()
	{
		try {
			$outgoing = QaOutgoingVendor::where('vendor_shortname','TRUE')->orderby('qa_outgoing_vendors.created_at','desc')->get();

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexNgRate($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Production NG Rate PT. TRUE';
			$page = 'Production NG Rate TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE';
			$view = 'outgoing.true.ng_rate';
			$materials = QaMaterial::where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Production NG Rate PT. ARISA';
			$page = 'Production NG Rate ARISA';
			$title_jp = '';
			$vendor_name = 'PT. ARISAMANDIRI PRATAMA';
			$view = 'outgoing.arisa.ng_rate';
			$materials = QaMaterial::where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Production NG Rate PT. KBI';
			$page = 'Production NG Rate KBI';
			$title_jp = '';
			$vendor_name = 'PT. KBI';
			$view = 'outgoing.kbi.ng_rate';
			$materials = QaMaterial::where('vendor_shortname','KBI')->get();
		}

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchNgRate(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISA';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'TRUE';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU';
			}

			$date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');
	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "LAST_DAY(NOW())";
	                  $firstDateTitle = date('01 M Y');
	                  $lastDateTitle = date('d M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "'".$date_to."'";
	                  $firstDateTitle = date('01 M Y');
	                  $lastDateTitle = date('d M Y',strtotime($date_to));
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "LAST_DAY(NOW())";
	                  $firstDateTitle = date('d M Y',strtotime($date_from));
	                  $lastDateTitle = date('d M Y');
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	                  $firstDateTitle = date('d M Y',strtotime($date_from));
	                  $lastDateTitle = date('d M Y',strtotime($date_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$outgoing = DB::SELECT("SELECT
				c.check_date,
				SUM( c.qty_check ) AS qty_check,
				SUM( c.qty_ng ) AS qty_ng,
				ROUND(( SUM( c.qty_ng ) / SUM( c.qty_check ) )* 100, 2 ) AS ng_ratio 
			FROM
				(
				SELECT DISTINCT
					( serial_number ),
					DATE( created_at ) AS check_date,
					( SELECT a.qty_check FROM `qa_outgoing_vendors` AS a WHERE a.serial_number = qa_outgoing_vendors.serial_number LIMIT 1 ) AS qty_check,
					( SELECT sum( b.ng_qty ) FROM `qa_outgoing_vendors` AS b WHERE b.serial_number = qa_outgoing_vendors.serial_number ) AS qty_ng 
				FROM
					`qa_outgoing_vendors` 
				WHERE
					vendor_shortname = '".$vendor_shortname."' 
					".$materialin."
					AND DATE( created_at ) >= ".$first."
					AND DATE( created_at ) <= ".$last." 
				) c 
			GROUP BY
				c.check_date");
			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		        'firstDateTitle' => $firstDateTitle,
		        'lastDateTitle' => $lastDateTitle,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchNgRateDetail(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISA';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'TRUE';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU';
			}

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$outgoing = DB::SELECT("SELECT
		          *,DATE(created_at) as created
		        FROM
		          `qa_outgoing_vendors` 
		        WHERE
		          vendor_shortname = '".$vendor_shortname."' 
		          AND DATE( created_at ) = '".$request->get('categories')."'
		          ".$materialin."");

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexPareto($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Production Pareto PT. TRUE';
			$page = 'Production Pareto TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE';
			$view = 'outgoing.true.pareto';
			$materials = QaMaterial::where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Production Pareto PT. ARISA';
			$page = 'Production Pareto ARISA';
			$title_jp = '';
			$vendor_name = 'PT. ARISAMANDIRI PRATAMA';
			$view = 'outgoing.arisa.pareto';
			$materials = QaMaterial::where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Production Pareto PT. KBI';
			$page = 'Production Pareto KBI';
			$title_jp = '';
			$vendor_name = 'PT. KBI';
			$view = 'outgoing.kbi.pareto';
			$materials = QaMaterial::where('vendor_shortname','KYORAKU')->get();
		}

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchPareto(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISA';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'TRUE';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU';
			}

			$first_month_ng = DB::SELECT("SELECT
	          DATE_FORMAT( week_date, '%Y-%m' ) AS first_month 
	        FROM
	          weekly_calendars 
	        WHERE
	          fiscal_year = (
	          SELECT
	            fiscal_year 
	          FROM
	            weekly_calendars 
	          WHERE
	            week_date = DATE(
	            NOW())) 
	        ORDER BY
	          week_date 
	          LIMIT 1");
	        $month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstDate = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $lastDate = "DATE_FORMAT( NOW(), '%Y-%m-%d' )";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstDate = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                  $last = "'".$month_to."'";
	                  $lastDate = "'".$month_to."-".date('d')."'";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $firstDate = "'".$month_from."-01'";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $lastDate = "DATE_FORMAT( NOW(), '%Y-%m-%d' )";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "'".$month_from."'";
	                  $firstDate = "'".$month_from."'-01";
	                  $last = "'".$month_to."'";
	                  $lastDate = "'".$month_to."-".date('d')."'";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$material_defect = DB::SELECT("SELECT
				ng_name,
				SUM( ng_qty ) AS count,
				SUM( total_ok ) AS count_ok,
				SUM( qa_outgoing_vendors.qty_check ) AS count_check 
			FROM
				qa_outgoing_vendors 
			WHERE
				DATE_FORMAT( qa_outgoing_vendors.created_at, '%Y-%m' ) >= ".$first." 
				AND DATE_FORMAT( qa_outgoing_vendors.created_at, '%Y-%m' ) <= ".$last."
				and vendor_shortname = '".$vendor_shortname."'
				and ng_name != '-'
				".$materialin."
			GROUP BY
				ng_name 
			ORDER BY
				count DESC,
				count_ok DESC,
				count_check DESC");

			$material_status = DB::SELECT("SELECT
					qa_outgoing_vendors.material_number,
					material_description,
					sum( ng_qty ) AS qty,
					a.qty_check,
					ROUND(( SUM( ng_qty ) / SUM( a.qty_check ) )* 100, 2 ) AS ng_ratio 
				FROM
					qa_outgoing_vendors
					JOIN (
					SELECT
						c.material_number,
						SUM( c.qty_check ) AS qty_check 
					FROM
						(
						SELECT DISTINCT
							( serial_number ),
							material_number,
							DATE( created_at ) AS check_date,
							( SELECT a.qty_check FROM `qa_outgoing_vendors` AS a WHERE a.serial_number = qa_outgoing_vendors.serial_number LIMIT 1 ) AS qty_check 
						FROM
							`qa_outgoing_vendors` 
						WHERE
							vendor_shortname = '".$vendor_shortname."' 
							AND DATE( created_at ) >= ".$firstDate."
							AND DATE( created_at ) <= ".$lastDate."
							AND ng_qty != 0 
						) c 
					GROUP BY
						c.material_number 
					) a ON a.material_number = qa_outgoing_vendors.material_number 
				WHERE
					vendor_shortname = '".$vendor_shortname."' 
					AND DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last." 
				GROUP BY
					material_number,
					material_description 
				ORDER BY
					ng_ratio DESC 
					LIMIT 5");
			$response = array(
		        'status' => true,
		        'material_defect' => $material_defect,
		        'material_status' => $material_status,
		        'firstMonthTitle' => $firstMonthTitle,
		        'lastMonthTitle' => $lastMonthTitle,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchParetoDetail(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISA';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'TRUE';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU';
			}

			$month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "'".$month_from."'";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$details = DB::SELECT("SELECT
					*,
					DATE( created_at ) AS created 
				FROM
					`qa_outgoing_vendors` 
				WHERE
					vendor_shortname = '".$vendor_shortname."' 
					AND DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first." 
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
					AND ng_name = '".$request->get('categories')."'
		          ".$materialin."");

			$response = array(
		        'status' => true,
		        'details' => $details,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexLotStatus($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Production Lot Status PT. TRUE';
			$page = 'Production Lot Status TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE';
			$view = 'outgoing.true.lot_status';
			$materials = QaMaterial::where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Production Lot Status PT. ARISA';
			$page = 'Production Lot Status ARISA';
			$title_jp = '';
			$vendor_name = 'PT. ARISAMANDIRI PRATAMA';
			$view = 'outgoing.arisa.lot_status';
			$materials = QaMaterial::where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Production Lot Status PT. KBI';
			$page = 'Production Lot Status KBI';
			$title_jp = '';
			$vendor_name = 'PT. KBI';
			$view = 'outgoing.kbi.lot_status';
			$materials = QaMaterial::where('vendor_shortname','KBI')->get();
		}

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchLotStatus(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISA';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'TRUE';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU';
			}

			$material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = "AND qa_outgoing_point_checks.material_number IN ( ".$material." )  ";
	          $materialin_ng = "  AND qa_outgoing_vendor_finals.material_number IN ( ".$material." ) ";
	        }
	        else{
	          $materialin = "";
	          $materialin_ng = "";
	        }

	        $date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');

	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE(NOW())";
	                  $last = "DATE(NOW())";
	                  $date = date('Y-m-d');
	                  $monthTitle = date("d M Y", strtotime($date));
	             }else{
	                  $first = "DATE(NOW())";
	                  $last = "'".$date_to."'";
	                  $date = date('Y-m-d');
	                  $monthTitle = date("d M Y", strtotime($date)).' to '.date("d M Y", strtotime($date_to));
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "DATE(NOW())";
	                  $date = date('Y-m-d');
	                  $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date));
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	                  $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date_to));
	             }
	        }

			$lot_status = DB::SELECT("SELECT
				a.part_category,
				SUM( a.count_ok ) AS count_ok,
				SUM( a.count_out ) AS count_out 
			FROM
				(
				SELECT DISTINCT
					( part_category ),
					0 AS count_ok,
					0 AS count_out 
				FROM
					qa_outgoing_point_checks 
				WHERE
					qa_outgoing_point_checks.vendor_shortname = '".$vendor_shortname."'
					".$materialin." UNION ALL
				SELECT DISTINCT
					( part_category ),
					0 AS count_ok,
					0 AS count_out 
				FROM
					qa_outgoing_point_checks 
				WHERE
					qa_outgoing_point_checks.vendor_shortname = '".$vendor_shortname."' UNION ALL
				SELECT
					qa_outgoing_point_checks.part_category,
				IF
					( lot_status = 'LOT OK', COUNT( DISTINCT ( final_serial_number ) ), 0 ) AS count_ok,
				IF
					( lot_status = 'LOT OUT', COUNT( DISTINCT ( final_serial_number ) ), 0 ) AS count_out 
				FROM
					qa_outgoing_vendor_finals
					JOIN qa_outgoing_point_checks ON qa_outgoing_vendor_finals.point_check_id = qa_outgoing_point_checks.id 
				WHERE
					qa_outgoing_point_checks.vendor_shortname = '".$vendor_shortname."' 
					".$materialin_ng."
				GROUP BY
					qa_outgoing_point_checks.part_category,
					final_serial_number 
				) a 
			GROUP BY
				a.part_category");

				$lot_resume = DB::SELECT("SELECT DISTINCT
					( final_serial_number ),
					GROUP_CONCAT(
					DISTINCT ( serial_number )) AS serial_number,
					qa_outgoing_vendor_finals.material_number,
					qa_outgoing_vendor_finals.material_description,
					qa_outgoing_vendor_finals.lot_status,
					qa_outgoing_vendor_finals.inspector,
					MIN( qa_outgoing_vendor_finals.created_at ) AS created 
				FROM
					qa_outgoing_vendor_finals 
				WHERE
					qa_outgoing_vendor_finals.vendor_shortname = 'ARISA' 
					".$materialin_ng."
				GROUP BY
					final_serial_number,
					material_number,
					material_description,
					qa_outgoing_vendor_finals.lot_status,
					qa_outgoing_vendor_finals.inspector");

			$response = array(
		        'status' => true,
		        'lot_status' => $lot_status,
		        'lot_resume' => $lot_resume,
		        'monthTitle' => $monthTitle,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexUploadSerialNumberKbi()
	{
		$title = 'Upload Serial Number KBI';
		$title_jp = '';
		$page = 'Upload Serial Number KBI';
		return view('outgoing.kbi.upload', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchSerialNumberKbi(Request $request)
	{
		try {
			$serial_number = QaOutgoingSerialNumber::where('vendor_shortname','KYORAKU')->get();
			$response = array(
		        'status' => true,
		        'serial_number' => $serial_number,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function downloadSerialNumberKbi()
	{
		$file_path = public_path('qa/TemplateSerialNumberKBI.xlsx');
		return response()->download($file_path);
	}

	public function uploadSerialNumberKbi(Request $request)
	{
		$filename = "";
		$file_destination = 'qa';

		if (count($request->file('newAttachment')) > 0) {
			try{
				$errors = [];
				$file = $request->file('newAttachment');
				$filename = 'serial_number_kbi_'.date('YmdHisa').'.'.$request->input('extension');
				$file->move($file_destination, $filename);

				$excel = 'qa/' . $filename;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					$reader->skipRows(1);

					$reader->each(function($row) {
					});
				})->toObject();

				for ($i=0; $i < count($rows); $i++) {
					$cek_sernum = QaOutgoingSerialNumber::where('serial_number',$rows[$i][0])->first();
					$part_names = QaMaterial::where('material_number',$rows[$i][1])->first();
					if (count($part_names) > 0) {
						$part_name = $part_names->material_description;
					}else{
						$part_name = $rows[$i][2];
						$errorlog = new ErrorLog([
							'error_message' => 'ERROR_KBI_'.$rows[$i][1],
							'created_by' => Auth::user()->id,
			            ]);

			            $errorlog->save();
					}
					if (count($cek_sernum) > 0) {
						array_push($errors, $cek_sernum->serial_number);
					}
					$menu = QaOutgoingSerialNumber::updateOrCreate(
						[
							'date' => date('Y-m-d'),
							'serial_number' => $rows[$i][0],
						],
						[
							'date' => date('Y-m-d'),
							'serial_number' => $rows[$i][0],
							'material_number' => $rows[$i][1],
							'part_name' => $part_name,
							'qty' => $rows[$i][3],
							'vendor' => 'KYORAKU BLOWMOLDING INDONESIA',
							'vendor_shortname' => 'KYORAKU',
							'created_by' => Auth::id()
						]
					);
					$menu->save();
				}

				$response = array(
					'status' => true,
					'message' => 'Serial Number succesfully uploaded',
					'qty_error' => count($errors)
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Please select file to attach'
			);
			return Response::json($response);
		}
	}

	public function indexReportIncoming($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Incoming Check Report PT. TRUE';
			$page = 'Incoming Check Report TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE INDONESIA';
			$view = 'outgoing.true.report_incoming';
			$materials = DB::connection('ympimis')->table('qa_materials')->where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Incoming Check Report PT. ARISA';
			$page = 'Incoming Check Report ARISA';
			$title_jp = '';
			$vendor_name = 'ARISAMANDIRI PRATAMA PT.';
			$view = 'outgoing.arisa.report_incoming';
			$materials = DB::connection('ympimis')->table('qa_materials')->where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Incoming Check Report PT. KBI';
			$page = 'Incoming Check Report KBI';
			$title_jp = '';
			$vendor_name = 'KYORAKU BLOWMOLDING INDONESIA';
			$view = 'outgoing.kbi.report_incoming';
			$materials = DB::connection('ympimis')->table('qa_materials')->where('vendor_shortname','KYORAKU')->get();
		}

		$inspection_levels = DB::connection('ympimis')->SELECT("SELECT * FROM `qa_inspection_levels`");

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'vendor_name' => $vendor_name,
			'inspection_levels' => $inspection_levels,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchReportIncoming(Request $request)
	{
		try {
			$date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');
	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "'".$date_to."'";
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "LAST_DAY(NOW())";
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	             }
	        }

	        if ($request->get('vendor') != '') {
	        	$vendor = "and vendor = '".$request->get('vendor')."'";
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$inspection_level = '';
	        if($request->get('inspection_level') != null){
	          $inspection_levels =  explode(",", $request->get('inspection_level'));
	          for ($i=0; $i < count($inspection_levels); $i++) {
	            $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
	            if($i != (count($inspection_levels)-1)){
	              $inspection_level = $inspection_level.',';
	            }
	          }
	          $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
	        }
	        else{
	          $inspection_levelin = "";
	        }
			$incoming = DB::connection('ympimis')
			->select("SELECT
		          qa_incoming_logs.id as id_log,
		          qa_incoming_logs.location,
		          employee_syncs.employee_id,
		          employee_syncs.name,
		          qa_incoming_logs.lot_number,
		          qa_incoming_logs.material_number,
		          qa_incoming_logs.material_description,
		          qa_incoming_logs.vendor,
		          qa_incoming_logs.invoice,
		          qa_incoming_logs.inspection_level,
		          qa_incoming_logs.`repair`,
		          qa_incoming_logs.`return`,
		          qa_incoming_logs.`qty_rec`,
		          qa_incoming_logs.`qty_check`,
		          qa_incoming_logs.`total_ok`,
		          qa_incoming_logs.`total_ng`,
		          qa_incoming_logs.`ng_ratio`,
		          qa_incoming_logs.`status_lot`,
		          qa_incoming_logs.`hpl`,
		          DATE( qa_incoming_logs.created_at ) AS created,
		          ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
		          ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
		          ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
		          ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
		        FROM
		          qa_incoming_logs
		          JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
		        WHERE
		          DATE( qa_incoming_logs.created_at ) >= ".$first." 
		          AND DATE( qa_incoming_logs.created_at ) <= ".$last."
		          ".$vendor." ".$materialin." ".$inspection_levelin." ");
			$response = array(
				'status' => true,
				'incoming' => $incoming
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}


	public function indexIncomingPareto($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Pareto Incoming Check PT. TRUE';
			$page = 'Pareto Incoming Check TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE';
			$view = 'outgoing.true.incoming_pareto';
			$materials = QaMaterial::where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Pareto Incoming Check PT. ARISAMANDIRI PRATAMA';
			$page = 'Pareto Incoming Check ARISAMANDIRI PRATAMA';
			$title_jp = '';
			$vendor_name = 'PT. ARISAMANDIRI PRATAMA';
			$view = 'outgoing.arisa.incoming_pareto';
			$materials = QaMaterial::where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Pareto Incoming Check PT. KBI';
			$page = 'Pareto Incoming Check KBI';
			$title_jp = '';
			$vendor_name = 'PT. KBI';
			$view = 'outgoing.kbi.incoming_pareto';
			$materials = QaMaterial::where('vendor_shortname','KYORAKU')->get();
		}

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchIncomingPareto(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISAMANDIRI PRATAMA PT.';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'PT. TRUE INDONESIA';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU BLOWMOLDING INDONESIA';
			}

			$first_month_ng = DB::SELECT("SELECT
	          DATE_FORMAT( week_date, '%Y-%m' ) AS first_month 
	        FROM
	          weekly_calendars 
	        WHERE
	          fiscal_year = (
	          SELECT
	            fiscal_year 
	          FROM
	            weekly_calendars 
	          WHERE
	            week_date = DATE(
	            NOW())) 
	        ORDER BY
	          week_date 
	          LIMIT 1");
	        $month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "'".$month_from."'";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and qa_incoming_logs.`material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$material_defect = DB::connection('ympimis')->SELECT("SELECT
				ng_name,
				SUM( qty_ng ) AS count,
				SUM( total_ok ) AS count_ok,
				SUM( qa_incoming_ng_logs.qty_check ) AS count_check 
			FROM
				qa_incoming_ng_logs
				JOIN qa_incoming_logs ON qa_incoming_logs.incoming_check_code = qa_incoming_ng_logs.incoming_check_code 
			WHERE
				DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) >= ".$first."
				AND DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) <= ".$last."
				AND qa_incoming_logs.vendor = '".$vendor_shortname."' 
				".$materialin."
			GROUP BY
				ng_name 
			ORDER BY
				count DESC,
				count_ok DESC,
				count_check DESC");

			$material_status = DB::connection('ympimis')->SELECT("SELECT
				SUM( a.total ) AS total,
				SUM( a.returnes )+SUM( a.scrapes )+SUM( a.repaires) AS `ng` 
			FROM
				(
				SELECT
					SUM( qty_check ) AS total,
					0 AS returnes,
					0 AS scrapes,
					0 AS repaires 
				FROM
					qa_incoming_logs 
				WHERE
					DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
					AND vendor = '".$vendor_shortname."' ".$materialin." UNION ALL
				SELECT
					0 total,
					SUM( `return` ) AS returnes,
					0 AS scrapes,
					0 AS repaires 
				FROM
					qa_incoming_logs 
				WHERE
					DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
				AND vendor = '".$vendor_shortname."' ".$materialin." 
				UNION ALL
				SELECT
					0 total,
					0 AS returnes,
					SUM( `scrap` ) AS scrapes,
					0 AS repaires 
				FROM
					qa_incoming_logs 
				WHERE
					DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
				AND vendor = '".$vendor_shortname."' ".$materialin." 
				UNION ALL
				SELECT
					0 total,
					0 AS returnes,
					0 AS scrapes,
					SUM( `repair` ) AS repaires 
				FROM
					qa_incoming_logs 
				WHERE
					DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
					AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
				AND vendor = '".$vendor_shortname."' ".$materialin." 
				) a");
			$response = array(
		        'status' => true,
		        'material_defect' => $material_defect,
		        'material_status' => $material_status,
		        'firstMonthTitle' => $firstMonthTitle,
		        'lastMonthTitle' => $lastMonthTitle,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchIncomingParetoDetail(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISAMANDIRI PRATAMA PT.';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'PT. TRUE INDONESIA';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU BLOWMOLDING INDONESIA';
			}

			$month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y');
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y');
	             }else{
	                  $first = "'".$month_from."'";
	                  $last = "'".$month_to."'";
	                  $firstMonthTitle = date('M Y',strtotime($month_from));
	                  $lastMonthTitle = date('M Y',strtotime($month_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$details = DB::CONNECTION('ympimis')->SELECT("SELECT
		            *,
		          date(created_at) as created
		          FROM
		            qa_incoming_ng_logs 
		          WHERE
		            DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first." 
		            AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last." 
		            and vendor = '".$vendor_shortname."'
		            ".$materialin." 
		            AND ng_name = '".$request->get('categories')."'");

			$response = array(
		        'status' => true,
		        'details' => $details,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexIncomingNgRate($vendor)
	{
		if ($vendor == 'true') {
			$title = 'Incoming NG Rate PT. TRUE';
			$page = 'Incoming NG Rate PT. TRUE';
			$title_jp = '';
			$vendor_name = 'PT. TRUE';
			$view = 'outgoing.true.incoming_ng_rate';
			$materials = QaMaterial::where('vendor_shortname','TRUE')->get();
		}

		if ($vendor == 'arisa') {
			$title = 'Incoming NG Rate PT. ARISAMANDIRI PRATAMA';
			$page = 'Incoming NG Rate  PT. ARISAMANDIRI PRATAMA';
			$title_jp = '';
			$vendor_name = 'PT. ARISAMANDIRI PRATAMA';
			$view = 'outgoing.arisa.incoming_ng_rate';
			$materials = QaMaterial::where('vendor_shortname','ARISA')->get();
		}

		if ($vendor == 'kbi') {
			$title = 'Incoming NG Rate PT. KBI';
			$page = 'Incoming NG Rate PT. KBI';
			$title_jp = '';
			$vendor_name = 'PT. KBI';
			$view = 'outgoing.kbi.incoming_ng_rate';
			$materials = QaMaterial::where('vendor_shortname','KBI')->get();
		}

		return view($view, array(
			'title' => $title,
			'title_jp' => $title_jp,
			'vendor' => $vendor,
			'materials' => $materials,
		))->with('page', $page)->with('head', $page);
	}

	public function fetchIncomingNgRate(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISAMANDIRI PRATAMA PT.';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'PT. TRUE INDONESIA';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU BLOWMOLDING INDONESIA';
			}

			$date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');
	        if ($date_from == "") {
	             if ($date_to == "") {
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "LAST_DAY(NOW())";
	                  $firstDateTitle = date('01 M Y');
	                  $lastDateTitle = date('d M Y');
	             }else{
	                  $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
	                  $last = "'".$date_to."'";
	                  $firstDateTitle = date('01 M Y');
	                  $lastDateTitle = date('d M Y',strtotime($date_to));
	             }
	        }else{
	             if ($date_to == "") {
	                  $first = "'".$date_from."'";
	                  $last = "LAST_DAY(NOW())";
	                  $firstDateTitle = date('d M Y',strtotime($date_from));
	                  $lastDateTitle = date('d M Y');
	             }else{
	                  $first = "'".$date_from."'";
	                  $last = "'".$date_to."'";
	                  $firstDateTitle = date('d M Y',strtotime($date_from));
	                  $lastDateTitle = date('d M Y',strtotime($date_to));
	             }
	        }

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and `material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$outgoing = DB::connection('ympimis')->SELECT("SELECT
				DATE( created_at ) AS check_date,
				SUM( qty_check ) AS qty_check,
				SUM( `return` )+ SUM( `repair` ) AS qty_ng,
				ROUND((( SUM( `repair` )+ SUM( `return` ))/ SUM( qty_check )) * 100, 1 ) AS ng_ratio 
			FROM
				`qa_incoming_logs` 
			WHERE
				DATE( created_at ) >= ".$first." 
				AND DATE( created_at ) <= ".$last."
				AND vendor = '".$vendor_shortname."' 
				".$materialin."
			GROUP BY
				DATE(
				created_at)");
			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		        'firstDateTitle' => $firstDateTitle,
		        'lastDateTitle' => $lastDateTitle,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function fetchIncomingNgRateDetail(Request $request,$vendor)
	{
		try {
			if ($vendor == 'arisa') {
				$vendor_shortname = 'ARISAMANDIRI PRATAMA PT.';
			}
			if ($vendor == 'true') {
				$vendor_shortname = 'PT. TRUE INDONESIA';
			}
			if ($vendor == 'kbi') {
				$vendor_shortname = 'KYORAKU BLOWMOLDING INDONESIA';
			}

	        $material = '';
	        if($request->get('material') != null){
	          $materials =  explode(",", $request->get('material'));
	          for ($i=0; $i < count($materials); $i++) {
	            $material = $material."'".$materials[$i]."'";
	            if($i != (count($materials)-1)){
	              $material = $material.',';
	            }
	          }
	          $materialin = " and qa_incoming_logs.`material_number` in (".$material.") ";
	        }
	        else{
	          $materialin = "";
	        }

			$outgoing = DB::connection('ympimis')->SELECT("SELECT
					qa_incoming_logs.*,
					qa_incoming_ng_logs.ng_name,
					qa_incoming_ng_logs.qty_ng,
					DATE( qa_incoming_logs.created_at ) AS created 
				FROM
					`qa_incoming_logs`
					LEFT JOIN qa_incoming_ng_logs ON qa_incoming_logs.incoming_check_code = qa_incoming_ng_logs.incoming_check_code 
				WHERE
					DATE( qa_incoming_logs.created_at ) = '".$request->get('categories')."' 
					AND qa_incoming_logs.vendor = '".$vendor_shortname."'
		          ".$materialin."");

			$response = array(
		        'status' => true,
		        'outgoing' => $outgoing,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexUploadSerialNumberTrue()
	{
		$title = 'Upload Monthly Schedule PT. TRUE';
		$title_jp = '';
		$page = 'Upload Monthly Schedule PT. TRUE';

		$material = QaMaterial::where('vendor_shortname','TRUE')->get();
		return view('outgoing.true.upload', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'material' => $material,
		))->with('page', $page)->with('head', $page);
	}

	public function downloadSerialNumberTrue()
	{
		$file_path = public_path('qa/TemplateSerialNumberTRUE.xlsx');
		return response()->download($file_path);
	}

	public function fetchSerialNumberTrue(Request $request)
	{
		try {
			$serial_number = QaOutgoingSerialNumber::select('qa_outgoing_serial_numbers.*',DB::RAW('DATE_FORMAT(qa_outgoing_serial_numbers.date,"%M %Y") as periode'))->where('vendor_shortname','TRUE')->orderby('created_at','desc')->get();
			$response = array(
		        'status' => true,
		        'serial_number' => $serial_number,
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function uploadSerialNumberTrue(Request $request)
	{
		$filename = "";
		$file_destination = 'qa';

		if (count($request->file('newAttachment')) > 0) {
			try{
				$errors = [];
				$file = $request->file('newAttachment');
				$filename = 'serial_number_true_'.date('YmdHisa').'.'.$request->input('extension');
				$file->move($file_destination, $filename);

				$excel = 'qa/' . $filename;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					$reader->skipRows(1);

					$reader->each(function($row) {
					});
				})->toObject();

				for ($i=0; $i < count($rows); $i++) {
					if ($rows[$i][2] != 0) {
						$part_names = QaMaterial::where('material_number',$rows[$i][0])->first();
						if (count($part_names) > 0) {
							$part_name = $part_names->material_description;
						}else{
							$part_name = $rows[$i][1];
							$errorlog = new ErrorLog([
								'error_message' => 'ERROR_KBI_'.$rows[$i][1],
								'created_by' => Auth::user()->id,
				            ]);

				            $errorlog->save();
						}
						$code_generator = CodeGenerator::where('note', '=', 'true')->first();
				        $serial_number = $code_generator->prefix.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
						$menu = QaOutgoingSerialNumber::create(
							[
								'date' => $request->get('periode').'-01',
								'serial_number' => $serial_number,
								'material_number' => $rows[$i][0],
								'part_name' => $part_name,
								'qty' => $rows[$i][2],
								'vendor' => 'PT. TRUE INDONESIA',
								'vendor_shortname' => 'TRUE',
								'created_by' => Auth::id()
							]
						);
						$menu->save();
						$code_generator->index = $code_generator->index+1;
				        $code_generator->save();	
					}
				}

				$response = array(
					'status' => true,
					'message' => 'Schedule succesfully uploaded',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Please select file to attach'
			);
			return Response::json($response);
		}
	}

	public function updateSerialNumberTrue(Request $request)
	{
		try {
			$sernum = QaOutgoingSerialNumber::where('id',$request->get('id'))->first();
			$sernum->qty = $request->get('qty');
			$sernum->material_number = explode('_',$request->get('material'))[0];
			$sernum->part_name = explode('_',$request->get('material'))[1];
			$sernum->save();

			$response = array(
				'status' => true,
				'message' => 'Schedule Succesfully Updated',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
		}
	}

	public function deleteSerialNumberTrue(Request $request)
	{
		try {
			$sernum = QaOutgoingSerialNumber::where('id',$request->get('id'))->first();
			$sernum->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Schedule Succesfully Deleted',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
		}
	}

	public function indexInputArisaRecheck($serial_number,$check_date)
	{
		$title = 'Input Recheck Material PT. ARISAMANDIRI PRATAMA';
		$title_jp = '';

		$ng_lists = DB::SELECT("select * from ng_lists where ng_lists.location = 'outgoing' and remark = 'arisa'");

		$materials = QaMaterial::where('vendor_shortname','ARISA')->get();

		$outgoing = QaOutgoingVendor::where('serial_number',$serial_number)->where('check_date',$check_date)->get();

		return view('outgoing.arisa.index_recheck', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'ng_lists' => $ng_lists,
			'outgoing' => $outgoing,
			'vendor' => 'PT. ARISAMANDIRI PRATAMA',
			'inspector' => Auth::user()->name,
			'materials' => $materials,
		))->with('page', 'Input Final Inspection ARISA')->with('head', 'Input Final Inspection ARISA');
	}

	public function confirmInputArisaRecheck(Request $request)
	{
		try {

			$material_number = $request->get('material_number');
			$material_description = $request->get('material_description');
			$qty_check = $request->get('qty_check');
			$total_ok = $request->get('total_ok');
			$total_ng = $request->get('total_ng');
			$ng_ratio = $request->get('ng_ratio');
			$inspector = $request->get('inspector');
			$ng_name = $request->get('ng_name');
			$serial_number = $request->get('serial_number');
			$ng_qty = $request->get('ng_qty');
			$jumlah_ng = $request->get('jumlah_ng');
			$material = QaMaterial::where('material_number',$material_number)->first();
			$outgoings = [];
			$outgoing_id = [];
			$outgoings_critical = [];
			if ($total_ng == 0) {
				$outgoing = new QaOutgoingVendorRecheck([
					'material_number' => $material_number,
					'check_date' => date('Y-m-d'),
					'material_description' => $material_description,
					'serial_number' => $serial_number,
					'vendor' => $material->vendor,
					'vendor_shortname' => $material->vendor_shortname,
					'hpl' => $material->hpl,
					'inspector' => $inspector,
					'qty_check' => $qty_check,
					'total_ok' => $total_ok,
					'total_ng' => $total_ng,
					'ng_ratio' => $ng_ratio,
					'ng_name' => '-',
					'ng_qty' => '0',
					'lot_status' => 'LOT OK',
	                'created_by' => Auth::user()->id
	            ]);
	            $outgoing->save();
			}else{
				for ($i=0; $i < count($ng_name); $i++) { 
					$outgoing = new QaOutgoingVendorRecheck([
						'check_date' => date('Y-m-d'),
						'material_number' => $material_number,
						'material_description' => $material_description,
						'serial_number' => $serial_number,
						'vendor' => $material->vendor,
						'vendor_shortname' => $material->vendor_shortname,
						'hpl' => $material->hpl,
						'inspector' => $inspector,
						'qty_check' => $qty_check,
						'total_ok' => $total_ok,
						'total_ng' => $total_ng,
						'ng_ratio' => $ng_ratio,
						'ng_name' => $ng_name[$i],
						'ng_qty' => $ng_qty[$i],
		                'created_by' => Auth::user()->id
		            ]);

		            $outgoing->save();
		            array_push($outgoing_id, $outgoing->id);
		            if (in_array($ng_name[$i], $this->critical_arisa)) {
		            	// $mail_to = [];

		          //   	array_push($mail_to, 'quality-ars@tigermp.co.id');
		          //   	// array_push($mail_to, 'suryanti@tigermp.co.id');
		          //   	// array_push($mail_to, 'achmad.rofiq@tigermp.co.id');
		          //   	// array_push($mail_to, 'agoes.jupri@tigermp.co.id');
		          //   	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		          //   	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		          //   	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		          //   	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				        // $cc = [];
				        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        // $bcc = [];
				        // $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        // $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        $outgoing_update = QaOutgoingVendorRecheck::where('id',$outgoing->id)->first();
				        $outgoing_update->lot_status = 'LOT OUT';
				        $outgoing_update->save();

				        // Mail::to($mail_to)
				        // // ->cc($cc,'CC')
				        // ->bcc($bcc,'BCC')
				        // ->send(new SendEmail($outgoing, 'critical_arisa'));

				        array_push($outgoings_critical, $outgoing);
		            }

		            if (in_array($ng_name[$i], $this->non_critical_arisa)) {
		            	array_push($outgoings, $outgoing);
		            }
				}


				$total_ng_non = 0;
				for ($i=0; $i < count($outgoings); $i++) { 
					$total_ng_non = $total_ng_non + $outgoings[$i]->ng_qty;
				}

				if ($total_ng_non != 0) {
					$persen = ($total_ng_non/$qty_check)*100;
					if ($persen > 5) {
						// $mail_to = [];

		          //   	array_push($mail_to, 'quality-ars@tigermp.co.id');
		          //   	// array_push($mail_to, 'suryanti@tigermp.co.id');
		          //   	// array_push($mail_to, 'achmad.rofiq@tigermp.co.id');
		          //   	// array_push($mail_to, 'agoes.jupri@tigermp.co.id');
		          //   	array_push($mail_to, 'agustina.hayati@music.yamaha.com');
		          //   	array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
		          //   	array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
		          //   	array_push($mail_to, 'noviera.prasetyarini@music.yamaha.com');

				        // $cc = [];
				        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
				        // $cc[1] = 'imron.faizal@music.yamaha.com';

				        // $bcc = [];
				        // $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
				        // $bcc[1] = 'rio.irvansyah@music.yamaha.com';

				        for ($i=0; $i < count($outgoing_id); $i++) { 
				        	$outgoing_update = QaOutgoingVendorRecheck::where('id',$outgoing_id[$i])->first();
					        $outgoing_update->lot_status = 'LOT OUT';
					        $outgoing_update->save();

					        // $outgoing_non_critical = QaOutgoingVendor::where('id',$outgoing_id[$i])->first();
					        // array_push($outgoings_non_critical, $outgoing_non_critical);
				        }

				        $data = array(
				        	'outgoing_non' => $outgoings,
				        	'outgoing_critical' => $outgoings_critical, );

				        Mail::to($mail_to)
				        // ->cc($cc,'CC')
				        ->bcc($bcc,'BCC')
				        ->send(new SendEmail($data, 'over_limit_ratio_arisa'));
					}
				}
			}

			$outgoing_check = QaOutgoingVendor::where('serial_number',$serial_number)->get();
			for ($i=0; $i < count($outgoing_check); $i++) { 
				$outgoing_checks = QaOutgoingVendor::where('id',$outgoing_check[$i]->id)->first();
				$outgoing_checks->recheck_status = 'Checked';
				$outgoing_checks->save();
			}
			
			$response = array(
		        'status' => true,
		        'message' => 'Success Input Data',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}

	public function indexInputTrueSosialisasi($serial_number,$check_date)
	{
		$title = 'Input Recheck Material PT. TRUE INDONESIA';
		$title_jp = '';

		$outgoing = QaOutgoingVendor::where('serial_number',$serial_number)->where('check_date',$check_date)->get();

		return view('outgoing.true.index_training', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'outgoing' => $outgoing,
			'serial_number' => $serial_number,
			'check_date' => $check_date,
			'vendor' => 'PT. TRUE INDONESIA',
			'inspector' => Auth::user()->name,
		))->with('page', 'Input Final Inspection TRUE')->with('head', 'Input Final Inspection TRUE');
	}

	public function confirmInputTrueSosialisasi(Request $request)
	{
		try {

			$filename = "";
        	$file_destination = 'data_file/true/sosialisasi';

        	$file = $request->file('newAttachment');
            $filename = 'sosialisasi_true_'.$request->get('serial_number').'_'.date('YmdHisa').'.'.$request->input('extension');
            $file->move($file_destination, $filename);

            $outgoing = QaOutgoingVendor::where('serial_number',$request->get('serial_number'))->get();

            for ($i=0; $i < count($outgoing); $i++) { 
            	$update_outgoing = QaOutgoingVendor::where('id',$outgoing[$i]->id)->first();
            	$update_outgoing->training_image = $filename;
            	$update_outgoing->training_at = date('Y-m-d H:i:s');
            	$update_outgoing->training_content = $request->get('training_content');
            	$update_outgoing->save();
            }

			$response = array(
		        'status' => true,
		        'message' => 'Berhasil Input Sosialisasi',
		    );
		    return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
		        'status' => false,
		        'message' => $e->getMessage(),
		    );
		    return Response::json($response);
		}
	}
	

}
