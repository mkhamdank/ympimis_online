<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\MaterialVolume;
use App\Flo;
use App\FloDetail;
use App\FloLog;
use App\ContainerSchedule;
use App\ContainerAttachment;
use App\User;
use App\Inventory;
use App\EmployeeSync;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;
use File;
use Storage;
use Carbon\Carbon;
use App\StampInventory;
use App\LogProcess;
use App\LogTransaction;
use App\ErrorLog;
use App\Mail\SendEmail;
use App\KnockDown;
use App\ShipmentSchedule;
use App\MasterChecksheet;
use Illuminate\Support\Facades\Mail;
use App\Mutasi;
use App\MutasiAnt;
use App\Navigation;


class MutasiController extends Controller
{
    public function Dashboard()
    {
        return view('mutasi.dashboard', array(
        ))->with('page', 'Mutasi');
    }
    public function viewCekEmail()
    {
        $mutasi = MutasiAnt::find($id);
        $isimail = "select id, departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
        return view('mails.mutasi_antar', array(
        ))->with('page', 'Mutasi');
    }
    public function dashboardAnt()
    {
        // return view('mutasi.dashboard_ant', array(
        // ))->with('page', 'Mutasi');
       $dept  = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
       $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
       $section = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
       $group   = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
       $user    = db::select('SELECT employee_id,name FROM employee_syncs');

      // $departement = db::select("select DISTINCT department from employee_syncs");
      $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('department')
      ->first();

      return view('mutasi.dashboard_ant',  
        array(
          'title' => 'Mutasi Antar Departemen Monitoring & Control', 
          'title_jp' => '監視・管理',
          'emp_dept' => $emp_dept,
          'dept' => $dept,
          'post' => $post,
          'group' => $group,
          'section' => $section,
          'user' => $user
      )
    )->with('page', 'Purchase Requisition Control');
    }

     public function get_employee( Request $request)
    {
        try {
            $emp = DB::SELECT("SELECT
                employee_id,
                name,
                department,
                position,
                section,
                `group`
            FROM
                `employee_syncs` 
            WHERE
            `employee_id` = '".$request->get('employee_id')."'
            AND `end_date` IS NULL");

            if (count($emp) > 0) {
                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'employee' => $emp
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                    'message' => 'Failed',
                    'employee' => ''
                );
                return Response::json($response);
            }
        }   
            catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }
    //reject
    public function rejected(Request $request, $id){
        try{
            $mutasi = Mutasi::find($id);
            $mutasi->status = 'Not Approved';
            $mutasi->chief_or_foreman = 'Not Approved';
            $mutasi->manager = 'Not Approved';
            $mutasi->gm = 'Not Approved';
            $mutasi->save();
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }        
    //reject antar departemen
    public function rejectedAnt(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->status = 'Not Approved';
            $mutasi->chief_or_foreman_asal = 'Not Approved';
            $mutasi->chief_or_foreman_tujuan = 'Not Approved';
            $mutasi->gm_division = 'Not Approved';
            $mutasi->manager_hrga = 'Not Approved';
            $mutasi->pres_dir = 'Not Approved';
            $mutasi->direktur_hr = 'Not Approved';
            $mutasi->save();
            return redirect('/dashboard_ant/mutasi')->with('status', 'Approved Canceled.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }        
    // ====================================================================================================================== SATU DEPARTEMEN
    //tampilan dashboard
    public function fetchResumeMutasi(Request $request)
    {   $resumes = Mutasi::select('status', 'mutasi_nik', 'mutasi_nama', 'mutasi_bagian', 'ke_section', 'chief_or_foreman', 'manager', 'gm', 'director', db::raw('chief.name as nama_chief'), db::raw('manager.name as nama_manager'), 'id', db::raw('gm.name as nama_gm'))
        ->leftJoin(db::raw('employee_syncs as chief'), 'mutasi_depts.chief_or_foreman', '=', 'chief.employee_id')
        ->leftJoin(db::raw('employee_syncs as manager'), 'mutasi_depts.manager', '=', 'manager.employee_id')
        ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_depts.gm', '=', 'gm.employee_id')
        ->orderBy('mutasi_depts.created_at', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }
    //tampilan detail
    public function showApproval($id){

        $mutasi = Mutasi::select('mutasi_tanggal', 'status', 'mutasi_nik', 'mutasi_nama', 'mutasi_bagian', 'mutasi_jabatan1', 'mutasi_rekomendasi', 'mutasi_ke_bagian','mutasi_jabatan' , 'chief_or_foreman', 'manager', 'gm', 'director', db::raw('chief.name as nama_chief'), db::raw('manager.name as nama_manager'), 'id', db::raw('gm.name as nama_gm'))
        ->leftJoin(db::raw('employee_syncs as chief'), 'mutasi_depts.chief_or_foreman', '=', 'chief.employee_id')
        ->leftJoin(db::raw('employee_syncs as manager'), 'mutasi_depts.manager', '=', 'manager.employee_id')
        ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_depts.gm', '=', 'gm.employee_id')
        ->orderBy('mutasi_depts.created_at', 'desc')
        ->where('mutasi_depts.id', '=', $id)
        ->get();

        
        return view('mutasi.print', array(
            'mutasi' => $mutasi
        ))->with('page', 'Mutasi');
    }
    //new create mutasi
    public function create(){
        $dept = db::select('SELECT DISTINCT department FROM employee_syncs
                            ORDER BY department ASC');

        $sect = db::select('SELECT DISTINCT section FROM employee_syncs
                            ORDER BY section ASC');

        $post = db::select('SELECT DISTINCT position FROM employee_syncs
                            ORDER BY position ASC');

        $user = db::select('SELECT employee_id,name FROM employee_syncs');

        return view('mutasi.create', array(
            'dept' => $dept,
            'sect' => $sect,
            'post' => $post,
            'user' => $user

        ))->with('page', 'Mutasi');
    }

    public function store(Request $request)
    {
            $id = Auth::id();

            $chf = db::select("select employee_id, name, position, section from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$request->get('ke_bagian')."' and section = '".$request->get('ke_section')."'");

            if ($chf != null)
            {
                foreach ($chf as $cf)
                {
                    $chief = $cf->employee_id;
                }
            }
            else
            {
                if ($request->get('ke_section') == 'Software Section') {
                    $chief = 'PI0103002';
                }
                else{
                    $chief = 'NIK Tidak Terdaftar';
                }
            }

        try {
            $mutasi = new Mutasi([
                'status' => "Chief/Foreman",
                'mutasi_nama' => $request->get('name'),
                'mutasi_nik' => $request->get('employee_id'),
                'mutasi_bagian' => $request->get('department'),
                'mutasi_jabatan1' => $request->get('position'),
                'mutasi_rekomendasi' => $request->get('rekom'),
                'mutasi_ke_bagian' => $request->get('ke_bagian'),
                'ke_section' => $request->get('ke_section'),
                'mutasi_jabatan' => $request->get('jabatan_bagian'),
                'mutasi_tanggal' => $request->get('tanggal'),
                'mutasi_alasan' => $request->get('alasan'),
                'chief_or_foreman' => $chief,
                'manager' => 'Waiting',
                'gm' => 'Waiting',
                'director' => 'Waiting'
            ]);
            $mutasi->save();

            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.chief_or_foreman = users.username where mutasi_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, status, mutasi_nik, mutasi_nama, mutasi_bagian, mutasi_jabatan1, mutasi_rekomendasi, mutasi_ke_bagian, mutasi_jabatan, chief_or_foreman, manager, gm from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);

            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi'));

            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }
    //approval chief
    public function mutasi_approvalchief_or_foreman(Request $request, $id){     
        try{
            $mutasi = Mutasi::find($id);
            $mgr = EmployeeSync::where('position','manager')->where('department',$mutasi->mutasi_ke_bagian)->first();
            if (count($mgr) > 0)
            {
                $manager = $mgr->employee_id;
            }
            else
            {
                if ($mutasi->mutasi_ke_bagian == 'Management Information System Department') {
                    $manager = 'PI0109004';
                }
            }

            $mutasi->status = 'Manager';
            $mutasi->chief_or_foreman = 'Approved';
            $mutasi->manager = $manager;
            $mutasi->save();
            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager = users.username where mutasi_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, status, mutasi_nik, mutasi_nama, mutasi_bagian, mutasi_jabatan1, mutasi_rekomendasi, mutasi_ke_bagian, mutasi_jabatan, chief_or_foreman, manager, gm from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi'));

           return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval manager
    public function mutasi_approvalmanager(Request $request, $id){
        
        
        try{

            $mutasi = Mutasi::find($id);

            if($mutasi->mutasi_ke_bagian == "Human Resources Department" || $mutasi->mutasi_ke_bagian == "General Affairs Department"){

                //GM Pak Arief
                $gm = 'PI9709001';
            }
            //if Production Support Division Maka GM Pak Budhi
            else if($mutasi->mutasi_ke_bagian == "Logistic Department" || $mutasi->mutasi_ke_bagian == "Production Control Department" || $mutasi->mutasi_ke_bagian == "Purchasing Control Department" || $mutasi->mutasi_ke_bagian == "Procurement Department" ){

                $gm = 'PI0109004';
            }
            //if accounting maka GM Pak IDA
            else if($mutasi->mutasi_ke_bagian == "Accounting Department"){
                $gm = 'PI1712018';

            }
            //Selain Itu GM Pak Hayakawa
            else{
                $gm = 'PI1206001';
            }

            $mutasi->status = 'General Manager';
            $mutasi->manager = 'Approved';
            $mutasi->gm = $gm;

            $mutasi->save();

            $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager = users.username where mutasi_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, status, mutasi_nik, mutasi_nama, mutasi_bagian, mutasi_jabatan1, mutasi_rekomendasi, mutasi_ke_bagian, mutasi_jabatan, chief_or_foreman, manager, gm from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);

            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi'));

           return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval gm
    public function mutasi_approvalgm(Request $request, $id){
        
        
        try{
            $mutasi = Mutasi::find($id);
            $mutasi->status = 'Approved';
            $mutasi->gm = 'Approved';
            $mutasi->save();
            
            $mail1 = 'ummi.ernawati@music.yamaha.com';
            $mail2 = 'l08111284@ympi.com';
            $isimail = "select id, status, mutasi_nik, mutasi_nama, mutasi_bagian, mutasi_jabatan1, mutasi_rekomendasi, mutasi_ke_bagian, mutasi_jabatan, chief_or_foreman, manager, gm from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mail1, $mail2)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }
    // ====================================================================================================================== ANTAR DEPARTEMEN
    //tampilan dashboard
    public function fetchResumeMutasiAnt(Request $request)
    {   $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi', 
        'users.name', 'mutasi_ant_depts.created_by')
        ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
        ->orderBy('mutasi_ant_depts.created_at', 'desc')
        ->get();
        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

    //tampilan detail
    public function showAntApproval($id){
        $mutasi = MutasiAnt::select('status', 'nik', 'nama', 'sub_seksi', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_seksi', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'chief_or_foreman_asal', 'date_atasan_asal', 'chief_or_foreman_tujuan', 'date_atasan_tujuan', 'gm_division', 'date_gm', 'manager_hrga', 'date_manager_hrga', 'pres_dir', 'date_pres_dir', 'direktur_hr', 'date_direktur_hr', db::raw('atasan_asal.name as nama_atasan_asal'), db::raw('atasan_tujuan.name as nama_atasan_tujuan'), db::raw('gm.name as nama_gm'), db::raw('manager_hrga.name as nama_manager'), db::raw('pres_dir.name as nama_pres_dir'), db::raw('direktur_hr.name as nama_direktur_hr'))
        ->leftJoin(db::raw('employee_syncs as atasan_asal'), 'mutasi_ant_depts.chief_or_foreman_asal', '=', 'atasan_asal.employee_id')
        ->leftJoin(db::raw('employee_syncs as atasan_tujuan'), 'mutasi_ant_depts.chief_or_foreman_tujuan', '=', 'atasan_tujuan.employee_id')
        ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_ant_depts.gm_division', '=', 'gm.employee_id')
        ->leftJoin(db::raw('employee_syncs as manager_hrga'), 'mutasi_ant_depts.manager_hrga', '=', 'manager_hrga.employee_id')
        ->leftJoin(db::raw('employee_syncs as pres_dir'), 'mutasi_ant_depts.pres_dir', '=', 'pres_dir.employee_id')
        ->leftJoin(db::raw('employee_syncs as direktur_hr'), 'mutasi_ant_depts.direktur_hr', '=', 'direktur_hr.employee_id')
        ->orderBy('mutasi_ant_depts.created_at', 'desc')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();
        return view('mutasi.print_ant', array(
            'mutasi' => $mutasi
        ))->with('page', 'Mutasi');
    }

    public function fetchMutasiDetail(Request $request){

        $resumes = MutasiAnt::select('nik', 'nama', 'sub_seksi', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_seksi', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan')
        ->where('mutasi_ant_depts.id', '=', $request->get('id'))
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }

     public function viewMutasiDetail(Request $request){

        $resumes = MutasiAnt::select('nik', 'nama', 'sub_seksi', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_seksi', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan')
        ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response);
    }
    //new create mutasi
    public function createAnt(){
        $dept    = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
        $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
        $section = db::select('SELECT DISTINCT section FROM employee_syncs ORDER BY section ASC');
        $group   = db::select('SELECT DISTINCT `group` FROM employee_syncs ORDER BY `group` ASC');
        $user    = db::select('SELECT employee_id,name FROM employee_syncs');
        return view('mutasi.create_ant', array(
            'dept' => $dept,
            'post' => $post,
            'group' => $group,
            'section' => $section,
            'user' => $user
        ))->with('page', 'Mutasi');
    }

    public function storeAnt(Request $request)
    {       
            $submission_date = $request->get('submission_date');
            $mutasi_date = date('Y-m-d', strtotime($submission_date . ' + 1 month'));

            $departemen = $request->get('department');
            $seksi = $request->get('section');
            $id  = Auth::id();
            $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$departemen."' and section = '".$seksi."'");
            
                if ($chf != null)
                {
                    foreach ($chf as $cf)
                    {
                        $chief = $cf->employee_id;
                        $nama_chief = $cf->name;
                    }
                }
                else
                {
                    if ($request->get('section') == 'Software Section') {
                        $chief = 'PI0103002';
                        $nama_chief = 'Agus Yulianto';
                    }
                    else{
                        $chief = 'NIK Tidak Terdaftar';
                    }
                }
        try {
        $mutasi = new MutasiAnt([
                'posisi' => 'chf_asal',
                'nik' => $request->get('employee_id'),
                'nama' => $request->get('name'),
                'sub_seksi' => $request->get('group'),
                'seksi' => $request->get('section'),
                'departemen' => $request->get('department'),
                'jabatan' => $request->get('position'),
                'rekomendasi' => $request->get('rekom'),
                'ke_sub_seksi' => $request->get('ke_sub_seksi'),
                'ke_seksi' => $request->get('ke_seksi'),
                'ke_departemen' => $request->get('ke_departemen'),
                'ke_jabatan' => $request->get('ke_jabatan'),
                'tanggal' => $request->get('tanggal'),
                'tanggal_maksimal' => $mutasi_date,
                'alasan' => $request->get('alasan'),
                'chief_or_foreman_asal' => $chief,
                'nama_chief_asal' => $nama_chief,
                'created_by' => $id
            ]);
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
    }

    //approval chief or foreman asal
    public function mutasi_approvalchief_or_foremanAsal(Request $request, $id){
            try{
            $dgm = null;
            $nama_dgm = null;
            $manager = null;
            $nama_manager = null;


            $mutasi = MutasiAnt::find($id);
            $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
            if ($manager != null)
            {
                foreach ($manager as $mgr)
                {
                    $manager = $mgr->employee_id;
                    $nama_manager = $mgr->name;
                }
            }
            elseif($manager == null)
            {
                if ($mutasi->departemen == 'Production Engineering Department') {
                    $manager = 'PI0703002';
                    $nama_manager = 'Susilo Basri Prasetyo';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $manager = 'PI9805006';
                    $nama_manager = 'Fatchur Rozi';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                elseif 
                    ($mutasi->departemen == 'Management Information System Department') {
                    $manager = 'PI0109004';
                    $nama_manager = 'Budhi Apriyanto';
                }
                elseif 
                    ($mutasi->departemen == 'Purchasing Control Department') {
                    $manager = 'PI9807014';
                    $nama_manager = 'Imron Faizal';
                }
                else{
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto';
                }
            }
            $mutasi->app_ca = 'Approved';
            $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_asal';
            $mutasi->manager_asal = $manager;
            $mutasi->nama_manager_asal = $nama_manager;
            $mutasi->dgm_asal = $dgm;
            $mutasi->nama_dgm_asal = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->manager_asal != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'dgm_asal';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval manager asal
    public function mutasi_approval_managerAsal(Request $request, $id){
            try{
            $dgm = null;
            $nama_dgm = null;
            $gm = null;
            $nama_gm = null;

            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_asal == null) {
                if ($mutasi->departemen == 'Woodwind Instrument - Final Assembly (WI-FA) Department' || 
                    $mutasi->departemen == 'Maintenance Department'||
                    $mutasi->departemen == 'Production Engineering Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
                    $mutasi->departemen == 'Quality Assurance Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
                    $mutasi->departemen == 'Educational Instrument (EI) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
                    $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
                elseif($mutasi->departemen == 'Logistic Department'||
                    $mutasi->departemen == 'Procurement Department'||
                    $mutasi->departemen == 'Production Control Department'||
                    $mutasi->departemen == 'Purchasing Control Department'){
                    $gm = 'PI0109004';
                    $nama_gm = 'Budhi Apriyanto';
                }
            }                

            $mutasi->app_ma = 'Approved';
            $mutasi->date_manager_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'dgm_asal';
            $mutasi->dgm_asal = $dgm;
            $mutasi->nama_dgm_asal = $nama_dgm;
            $mutasi->gm_asal = $gm;
            $mutasi->nama_gm_asal = $nama_gm;            
            $mutasi->save();

            if ($mutasi->dgm_asal != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'gm_asal';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval dgm asal
    public function mutasi_approval_dgmAsal(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);
            if ($mutasi->dgm_asal != null) {
                $gm = 'PI1206001';
                $nama_gm = 'Yukitaka Hayakawa';
            }

            $mutasi->app_da = 'Approved';
            $mutasi->date_dgm_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'gm_asal';
            $mutasi->gm_asal = $gm;
            $mutasi->nama_gm_asal = $nama_gm;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval gm asal
    public function mutasi_approval_gmAsal(Request $request, $id){
            try{
            $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$ke_departemen."' and section = '".$ke_seksi."'");
            
                if ($chf != null)
                {
                    foreach ($chf as $cf)
                    {
                        $chief = $cf->employee_id;
                        $nama_chief = $cf->name;
                    }
                }
                else
                {
                    if ($request->get('section') == 'Software Section') {
                        $chief = 'PI0103002';
                        $nama_chief = 'Agus Yulianto';
                    }
                    else{
                        $chief = null;
                    }
                }

            $mutasi->app_ga = 'Approved';
            $mutasi->date_gm_asal = date('Y-m-d H-y-s');
            $mutasi->posisi = 'chf_tujuan';
            $mutasi->chief_or_foreman_tujuan = $chief;
            $mutasi->nama_chief_tujuan = $nama_chief;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval chief or foreman tujuan
    public function mutasi_approvalchief_or_foremanTujuan(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'"); 
            if ($manager != null)
            {
                foreach ($manager as $mgr)
                {
                    $manager = $mgr->employee_id;
                    $nama_manager = $mgr->name;
                }
            }
            else
            {
                if ($mutasi->departemen == 'Production Engineering Department') {
                    $manager = 'PI0703002';
                    $nama_manager = 'Susilo Basri Prasetyo';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
                    $manager = 'PI9805006';
                    $nama_manager = 'Fatchur Rozi';
                }
                elseif 
                    ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                else{
                    $manager = null;
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }
            }
            $mutasi->app_ct = 'Approved';
            $mutasi->date_atasan_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_tujuan';
            $mutasi->manager_tujuan = $manager;
            $mutasi->nama_manager_tujuan = $nama_manager;
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->manager_tujuan != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'dgm_tujuan';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }
    //approval manager tujuan
    public function mutasi_approval_managerTujuan(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);

                if ($mutasi->departemen == 
                    'Logistic Department' || 
                    'Procurement Department'||
                    'Production Control Department') {
                    $dgm = null;
                }
                else{
                    $dgm = 'PI0109004';
                    $nama_dgm = 'Budhi Apriyanto'; 
                }

            $mutasi->app_mt = 'Approved';
            $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'dgm_tujuan';
            $mutasi->dgm_tujuan = $dgm;
            $mutasi->nama_dgm_tujuan = $nama_dgm;            
            $mutasi->save();

            if ($mutasi->dgm_tujuan != null) {
                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }else{
                $mutasi->posisi = 'gm_tujuan';
                $mutasi->save();

                $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            }
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval dgm tujuan
    public function mutasi_approval_dgmTujuan(Request $request, $id){
            try{
            $mutasi = MutasiAnt::find($id);

                if ($mutasi->departemen == 
                    'Logistic Department' || 
                    'Procurement Department'||
                    'Production Control Department') {
                    $gm = 'PI0109004';
                    $nama_gm = 'Budhi Apriyanto';
                }
                else{
                    $gm = 'PI1206001';
                    $nama_gm = 'Yukitaka Hayakawa'; 
                }

            $mutasi->app_dt = 'Approved';
            $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'gm_tujuan';
            $mutasi->gm_tujuan = $gm;
            $mutasi->nama_gm_tujuan = $nama_gm;           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval gm tujuan
    public function mutasi_approval_gmTujuan(Request $request, $id){
            try{
            $mutasi->app_gt = 'Approved';
            $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
            $mutasi->posisi = 'mgr_hrga';
            $mutasi->manager_hrga = 'PI9707011';
            $mutasi->nama_manager = 'Prawoto';           
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_hrga = users.username where mutasi_ant_depts.id = ".$mutasi->id;
                $mailtoo = DB::select($mails);
            
            $isimail = "select id, posisi,status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }
    //approval manager hrga
    public function mutasi_approvalManager_Hrga(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->posisi = 'dir_hr';
            $mutasi->app_m = 'Approved';
            $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
            $mutasi->direktur_hr = 'PI9709001';
            $mutasi->nama_direktur_hr = 'Arief Soekamto';
            $mutasi->save();

            $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.direktur_hr = users.username where mutasi_ant_depts.id = ".$mutasi->id;
            $mailtoo = DB::select($mails);
            $isimail = "select id, posisi, status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannularif87@gmail.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    //approval direktur hr
    public function mutasi_approvalDirektur_Hr(Request $request, $id){
        try{
            $mutasi = MutasiAnt::find($id);
            $mutasi->status = 'All Approved';
            $mutasi->app_dir = 'Approved';
            $mutasi->date_direktur_hr = date('Y-m-d H-y-s');
            $mutasi->save();

            $mail1 = 'ummi.ernawati@music.yamaha.com';
            $mail2 = 'l08111284@ympi.com';
            $isimail = "select id, status, nik, nama, sub_seksi, seksi, departemen, jabatan, rekomendasi, ke_sub_seksi, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan, chief_or_foreman_asal, date_atasan_asal, chief_or_foreman_tujuan, date_atasan_tujuan, gm_division, date_gm, manager_hrga, date_manager_hrga, pres_dir, date_pres_dir, direktur_hr, date_direktur_hr from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
            $mutasi = db::select($isimail);
            Mail::to($mail1, $mail2)->bcc(['lukmannularif87@gmail.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
            return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
            }
            catch (QueryException $e){
            return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

    // ===========================================================================================================
        //==================================//
    //          Verifikasi mutasi           //
    //==================================//
    public function verifikasi_mutasi_ant(Request $request, $id)
    {
        $mutasi = MutasiAnt::find($id);

        $resumes = MutasiAnt::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'sub_seksi', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_seksi', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'manager_asal', 'nama_manager_asal', 'date_manager_asal',
        'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
        'gm_asal', 'nama_gm_asal', 'date_gm_asal', 
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan', 
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan', 
        'manager_hrga', 'nama_manager', 'date_manager_hrga',
        'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr', 
        
        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();

        return view('mutasi.verifikasi.verifikasi_mutasi_ant', array(
            'mutasi' => $mutasi,
            'resumes' => $resumes
        ))->with('page', 'Mutasi');
    }

    public function report_mutasi_ant(Request $request, $id){
        $mutasi = MutasiAnt::find($id);

        $resumes = MutasiAnt::select(
        'id','status', 'posisi', 'nik', 'nama', 'sub_seksi', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_seksi', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'manager_asal', 'nama_manager_asal', 'date_manager_asal',
        'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
        'gm_asal', 'nama_gm_asal', 'date_gm_asal', 
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan', 
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan', 
        'manager_hrga', 'nama_manager', 'date_manager_hrga',
        'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr', 
        
        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A5', 'landscape');

        $pdf->loadView('mutasi.report.report_ant', array(
            'pr' => $resumes
        ));

        $path = "mutasi/" . $resumes[0]->nik . ".pdf";
        return $pdf->stream("Mutasi ".$resumes[0]->nik. ".pdf");
    }

    public function fetchMonitoringMutasiAnt(Request $request)
  {
      $tahun = date('Y');
      $dateto = $request->get('dateto');

      if ($dateto == "") {
          $dateto = date('Y-m', strtotime(carbon::now()));
      } else {
          $dep = '';
      }

      $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN app_dir IS NULL THEN 1 ELSE 0 END ) AS NotSigned,
        sum( CASE WHEN app_dir IS NOT NULL THEN 1 ELSE 0 END ) AS Signed 
        FROM
        mutasi_ant_depts 
        WHERE
        mutasi_ant_depts.deleted_at IS NULL 
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '".$dateto."'
        GROUP BY
        bulan,
        tahun 
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");

      $response = array(
        'status' => true,
        'datas' => $data,
        'tahun' => $tahun,
        'dateto' => $dateto
    );

      return Response::json($response); 
  }
}