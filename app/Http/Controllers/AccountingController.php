<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use File;
use PDF;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AccInvoiceVendor;
use App\AccInvoicePaymentTerm;
use App\AccPaymentRequest;
use App\AccPaymentRequestDetail;
use App\AccSupplier;


class AccountingController extends Controller
{
    public function __construct()
    {
      
    }

    public function indexInvoice()
    {
        $title = 'Invoice Data';
        $title_jp = '';

        if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Billing" || Auth::user()->role_code == "E - Purchasing" || Auth::user()->role_code == "E - Accounting") {

            return view('billing.index_invoice', array(
                'title' => $title,
                'title_jp' => $title_jp
            ))->with('page', 'Invoice Data')
            ->with('head', 'Invoice Data');
        }
        else{
            return view('404',  
                array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
            );
        }
    }

    public function fetchInvoice()
    {
        try {

            if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Purchasing" || Auth::user()->role_code == "E - Accounting") {
                $restrict_vendor = "";
            }
            else{
                $restrict_vendor = "and supplier_code LIKE '%".Auth::user()->remark."%'";
            }

            $invoice = db::select("
                SELECT
                    *
                FROM
                    acc_invoice_vendors 
                WHERE deleted_at is null
                ".$restrict_vendor."
            ");

            $response = array(
                'status' => true,
                'invoice' => $invoice,
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

    public function uploadInvoice()
    {
        $title = 'Upload Invoice';
        $title_jp = '';

        $user = AccSupplier::select('acc_suppliers.*')
        ->LeftJoin('users','acc_suppliers.supplier_code','=','users.remark')
        ->where('users.remark', '=', Auth::user()->remark)
        ->first();

        return view('billing.upload_invoice', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $user
        ))->with('page', 'Upload Invoice')
        ->with('head', 'Upload Invoice');
    }

    public function uploadInvoicePost(request $request)
    {
        try{

              $lampiran = $request->file('lampiran');
              $nama=$lampiran->getClientOriginalName();
              $filename = pathinfo($nama, PATHINFO_FILENAME);
              $extension = pathinfo($nama, PATHINFO_EXTENSION);
              $filename = md5($filename.date('YmdHisa')).'.'.$extension;

              $lampiran->move('files/invoice',$filename);

              $id = Auth::id();
              $invoice = new AccInvoiceVendor([
                'tanggal' => $request->get('tanggal'),
                'supplier_code' => $request->get('supplier_code'),
                'supplier_name' => $request->get('supplier_name'),
                'pic' => $request->get('pic'),
                'kwitansi' => $request->get('kwitansi'),
                'tagihan' => $request->get('tagihan'),
                'surat_jalan' => $request->get('surat_jalan'),
                'faktur_pajak' => $request->get('faktur_pajak'),
                'purchase_order' => $request->get('purchase_order'),
                'note' => $request->get('note'),
                'currency' => $request->get('currency'),
                'amount' => $request->get('amount'),
                'ppn' => $request->get('ppn'),
                'amount_total' => $request->get('amount_total'),
                'file' => $filename,
                'status' => 'Open',
                'created_by' => $id
              ]);

              $invoice->save();

              return redirect('/index/upload_invoice')->with('status', 'New Invoice has been created.')->with('page', 'Upload Invoice');
        
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Data already exist.')->with('page', 'Upload Invoice');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Upload Invoice');
            }
        }

    }

    public function editInvoice($id)
    {
        $title = 'Edit Invoice';
        $title_jp = '';

        $invoice = AccInvoiceVendor::find($id);

        return view('billing.edit_invoice', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'invoice' => $invoice
        ))->with('page', 'Edit Invoice')
        ->with('head', 'Edit Invoice');
    }

    public function editInvoicePost(request $request)
    {
        try{
            $id = Auth::id();
            // var_dump($request->get('id_edit'));die();

              $invoice = AccInvoiceVendor::where('id', $request->get('id_edit'))->update([
                  'kwitansi' => $request->get('kwitansi'), 
                  'tagihan' => $request->get('tagihan'), 
                  'surat_jalan' => $request->get('surat_jalan'), 
                  'faktur_pajak' => $request->get('faktur_pajak'),
                  'purchase_order' => $request->get('purchase_order'),
                  'note' => $request->get('note'),
                  'currency' => $request->get('currency'), 
                  'amount' => $request->get('amount'), 
                  'ppn' => $request->get('ppn'),
                  'amount_total' => $request->get('amount_total'),
                  'updated_by' => Auth::user()->username
              ]);

              return redirect('/edit/invoice/'.$request->get('id_edit'))->with('status', 'Invoice has been update.')->with('page', 'Edit Invoice');
        
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Data already exist.')->with('page', 'Edit Invoice');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Edit Invoice');
            }
        }

    }

    public function fetchInvoiceMonitoring(){
        

        if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Purchasing" || Auth::user()->role_code == "E - Accounting") { 
            $restrict_vendor = ""; 
        } else { 
            $restrict_vendor = "and supplier_code LIKE '%".Auth::user()->remark."%'"; 

        }

        $data = db::select("
                SELECT
                    count( id ) AS jumlah,
                    MONTHNAME( tanggal ) AS bulan,
                    sum(case when status = 'Open' then 1 else 0 end) as 'purchasing',
                    sum(case when status = 'Acc' then 1 else 0 end) as 'accounting',
                    sum(case when status = 'Revised' then 1 else 0 end) as 'revised',
                    sum(case when status = 'Closed' then 1 else 0 end) as 'closed'
                FROM
                    acc_invoice_vendors 
                WHERE deleted_at is null
                    ".$restrict_vendor."
                GROUP BY
                    monthname( tanggal ) 
                ORDER BY
                    MONTH ( tanggal )
            ");

        $data_outstanding = db::select("
            SELECT
                count( id ) AS jumlah,
                COALESCE(sum(case when status = 'Open' then 1 else 0 end),0) as 'purchasing',
                COALESCE(sum(case when status = 'Acc' then 1 else 0 end),0) as 'accounting',
                COALESCE(sum(case when status = 'Revised' then 1 else 0 end),0) as 'revised',
                COALESCE(sum(case when status = 'Closed' then 1 else 0 end),0) as 'closed'
            FROM
                acc_invoice_vendors 
            WHERE deleted_at is null
            ".$restrict_vendor."
                ");
      
        $response = array(
            'status' => true,
            'datas' => $data,
            'data_outstanding' => $data_outstanding
        );
        return Response::json($response);

    }

    public function reportInvoice($id){

        $invoice = AccInvoiceVendor::select('acc_invoice_vendors.*','acc_suppliers.supplier_phone','acc_suppliers.supplier_fax','acc_suppliers.contact_name','acc_suppliers.supplier_address','acc_suppliers.supplier_city')
        ->LeftJoin('acc_suppliers','acc_suppliers.supplier_code','=','acc_invoice_vendors.supplier_code')
        ->where('acc_invoice_vendors.id', '=', $id)
        ->first();
 
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('billing.report_invoice', array(
            'invoice' => $invoice,
            'id' => $id
        ));

        $path = "invoice/" . $id . ".pdf";
        return $pdf->stream("Invoice ".$id. ".pdf");

        // return view('billing.report_invoice', array(
        //  'Invoice' => $invoice,
        // ))->with('page', 'Invoice')->with('head', 'Invoice List');
    }


    public function indexPurchasing(){

        $title = 'Purchasing';
        $page = 'Purchasing';
        $title_jp = '';

        return view('billing.purchasing.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $page)->with('head', $page);
    
    }

    public function indexPaymentRequest()
    {
        $title = 'Payment Request';
        $title_jp = '';

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $payment_term = AccInvoicePaymentTerm::select('*')
        ->whereNull('deleted_at')
        ->distinct()
        ->get();

        $invoices = AccInvoiceVendor::select('tagihan','acc_invoice_vendors.supplier_code','acc_invoice_vendors.supplier_name','supplier_duration as payment_term')
        ->leftJoin('acc_suppliers','acc_invoice_vendors.supplier_code','=','acc_suppliers.supplier_code')
        ->whereNull('acc_invoice_vendors.deleted_at')
        ->where('status','=','checked_pch')
        ->get();

        
        if (Auth::user()->role_code == "MIS"|| Auth::user()->role_code == "E - Purchasing") {
            return view('billing.index_payment_request', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'vendor' => $vendor,
                'invoice' => $invoices,
                'payment_term' => $payment_term
            ))->with('page', 'Payment Request')
            ->with('head', 'Payment Request');
        }
        else{
            return view('404',  
                array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
            );
        }
    }

    public function fetchPaymentRequest(){
        $payment = db::select("SELECT * FROM acc_payment_requests WHERE deleted_at IS NULL order by id desc");

        $response = array(
            'status' => true,
            'payment' => $payment
        );
        return Response::json($response);
    }


    public function fetchPaymentRequestDetailAll(Request $request){

        $payment = AccPaymentRequest::find($request->get('id'));

        $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
        ->distinct()
        ->get();

        $payment_term = AccInvoicePaymentTerm::select('*')->whereNull('deleted_at')
        ->distinct()
        ->get();

        $payment_detail = AccPaymentRequestDetail::select('*')
        ->where('id_payment',$request->get('id'))
        ->whereNull('deleted_at')
        ->get();

        $response = array(
            'status' => true,
            'payment' => $payment,
            'vendor' => $vendor,
            'payment_term' => $payment_term,
            'payment_detail' => $payment_detail
        );
        return Response::json($response);
    }

    public function createPaymentRequest(Request $request){
        try{

            $manager = null;
            $manager_name = null;
            $dgm = null;
            $gm = null;

            $manag = DB::connection('ympimis')->table('employee_syncs')
            ->where('department','Procurement Department')
            ->where('position','manager')
            ->get();
            
            if ($manag != null)
            {
                foreach ($manag as $mg)
                {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }

                $gm = 'PI0109004';
            }
            else{
                $manager = null;
                $manager_name = null;
                $gm = null;
            }

            $id = 0;

            $nomor = DB::select("SELECT id FROM `acc_payment_requests` ORDER BY id DESC LIMIT 1");

            if ($nomor != null){
                $id = (int)$nomor[0]->id + 1;
            }
            else{
                $id = 1;
            }
            $payment = new AccPaymentRequest([
                'payment_date' => $request->input('payment_date'),
                'supplier_code' => $request->input('supplier_code'),
                'supplier_name' => $request->input('supplier_name'),
                'currency' => $request->input('currency'),
                'payment_term' => $request->input('payment_term'),
                'payment_due_date' => $request->input('payment_due_date'),
                'amount' => $request->input('amount'),
                'kind_of' => $request->input('kind_of'),
                'attach_document' => $request->input('attach_document'),
                'pdf' => 'Payment '.$request->input('supplier_name').' '.date('d-M-y', strtotime($request->input('payment_date'))).' ('.$id.').pdf',
                'posisi' => 'user', 
                'status' => 'approval', 
                'manager' => $manager,
                'manager_name' => $manager_name,
                'gm' => $gm,
                'created_by' => Auth::user()->username,
                'created_name' => Auth::user()->name
            ]);

            $payment->save();


            for ($i = 1;$i < $request->input('jumlah');$i++)
            {
                $payment_detail = new AccPaymentRequestDetail([
                    'id_payment' => $payment->id, 
                    'invoice' => $request->get('invoice'.$i),
                    'amount' => $request->get('amount'.$i),
                    'ppn' => $request->get('ppn'.$i),
                    'typepph' => $request->get('typepph'.$i),
                    'amount_service' => $request->get('amount_service'.$i),
                    'pph' => $request->get('pph'.$i),
                    'net_payment' => $request->get('amount_final'.$i),
                    'created_by' => Auth::user()->username,
                    'created_name' => Auth::user()->name
                ]);

                $payment_detail->save();

                $update_invoice = AccInvoiceVendor::where('tagihan',$request->get('invoice'.$i))->update([
                    'status' => 'payment_pch'
                ]);

            }

            $payment_data = AccPaymentRequest::where('id','=',$payment->id)->first();
            $payment_detail = AccPaymentRequestDetail::select('*')
            ->where('id_payment',$payment->id)
            ->whereNull('deleted_at')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('billing.report_payment_request', array(
                'payment' => $payment_data,
                'payment_detail' => $payment_detail,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$request->input('supplier_name'). " ".date('d-M-y', strtotime($request->input('payment_date')))." (".$payment->id.").pdf");

            $response = array(
                'status' => true,
                'message' => 'New Payment Request Successfully Added'
            );
            return Response::json($response);
        }
        catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function editPaymentRequest(Request $request){
        try{
            $payment = AccPaymentRequest::where('id', '=', $request->get('id_edit'))->first();

            $payment->supplier_code = $request->input('supplier_code');
            $payment->supplier_name = $request->input('supplier_name');
            $payment->currency = $request->input('currency');
            $payment->payment_term = $request->input('payment_term');
            $payment->payment_due_date = $request->input('payment_due_date');
            $payment->amount = $request->input('amount');
            $payment->kind_of = $request->input('kind_of');
            $payment->attach_document = $request->input('attach_document');
            $payment->created_by = Auth::user()->username;
            $payment->save();

            $payment_detail = AccPaymentRequestDetail::select('*')
            ->where('id_payment',$payment->id)
            ->whereNull('deleted_at')
            ->get();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('billing.report_payment_request', array(
                'payment' => $payment,
                'payment_detail' => $payment_detail,
                'id' => $payment->id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$request->input('supplier_name'). " ".date('d-M-y', strtotime($payment->payment_date))." (".$payment->id.").pdf");

            $response = array(
                'status' => true,
                'message' => 'Payment Request Updated'
            );
            return Response::json($response);
        }
        catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function reportPaymentRequest($id){
        $payment = AccPaymentRequest::find($id);
        $payment_detail = AccPaymentRequestDetail::select('*')
        ->where('id_payment',$id)
        ->whereNull('deleted_at')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('billing.report_payment_request', array(
            'payment' => $payment,
            'payment_detail' => $payment_detail,
            'id' => $id
        ));
        return $pdf->stream("Payment ".$payment->kind_of. " ".date('d-M-y', strtotime($payment->payment_date)).".pdf");
    }


    public function fetchPaymentRequestList(Request $request)
    {
        $payments = AccPaymentRequest::select('*')
        ->whereNull('deleted_at')
        ->get();

        $response = array(
            'status' => true,
            'payment' => $payments
        );

        return Response::json($response);
    }

    public function fetchPaymentRequestDetail(Request $request)
    {
        $html = array();
        $invoice = AccInvoiceVendor::where('tagihan', $request->invoice)
        ->get();
        foreach ($invoice as $inv)
        {
            $html = array(
                'amount' => $inv->amount,
                'ppn' => $inv->ppn
            );

        }

        return json_encode($html);
    }

    public function emailPaymentRequest(Request $request){
        $pr = AccPaymentRequest::find($request->get('id'));
        try{
            if ($pr->posisi == "user")
            {
                $mails = "select distinct email from acc_payment_requests join users on acc_payment_requests.manager = users.username where acc_payment_requests.id = ".$request->get('id');
                $mailtoo = DB::select($mails);

                $pr->posisi = "manager";
                $pr->save();

                $isimail = "select acc_payment_requests.*, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.invoice = acc_invoice_vendors.tagihan where acc_payment_requests.id = ".$request->get('id');
                $payment = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

                $response = array(
                  'status' => true,
                  'datas' => "Berhasil"
              );

                return Response::json($response);
            }
        } 
        catch (Exception $e) {
            $response = array(
              'status' => false,
              'datas' => "Gagal"
          );
            return Response::json($response);
        }
    }

    public function deletePaymentRequest(Request $request)
    {
        try {
            $payment = AccPaymentRequest::find($request->get('id'));

            $payment_item = AccPaymentRequestDetail::where('id_payment', '=', $payment->id)->get();

            foreach ($payment_item as $pi) {
                $update_invoice = AccInvoiceVendor::where('tagihan',$pi->invoice)->update([
                    'status' => 'checked_pch'
                ]);
            }

            $delete_payment_item = AccPaymentRequestDetail::where('id_payment', '=', $payment->id)->delete();
            $delete_payment = AccPaymentRequest::where('id', '=', $payment->id)->delete();

            $response = array(
              'status' => true,
              'datas' => "Berhasil",
            );
            return Response::json($response);
        }
        catch(QueryException $e)
        {
            return redirect('/index/payment_request')->with('error', $e->getMessage())
            ->with('page', 'Payment Request');
        }
    }

    public function paymentapprovalmanager($id){
    $pr = AccPaymentRequest::find($id);
        try{
            if ($pr->posisi == "manager")
            {
                    $pr->posisi = "gm";
                    $pr->status_manager = "Approved/".date('Y-m-d H:i:s');

                    $mailto = "select distinct email from acc_payment_requests join users on acc_payment_requests.gm = users.username where acc_payment_requests.id = '" . $id . "'";
                    $mails = DB::select($mailto);

                    foreach ($mails as $mail)
                    {
                        $mailtoo = $mail->email;
                    }

                    $pr->save();

                    $payment_detail = AccPaymentRequestDetail::select('*')
                    ->where('id_payment',$id)
                    ->whereNull('deleted_at')
                    ->get();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->getDomPDF()->set_option("enable_php", true);
                    $pdf->setPaper('A4', 'potrait');

                    $pdf->loadView('billing.report_payment_request', array(
                        'payment' => $pr,
                        'payment_detail' => $payment_detail,
                        'id' => $id
                    ));

                    $pdf->save(public_path() . "/payment_list/Payment ".$pr->supplier_name. " ".date('d-M-y', strtotime($pr->payment_date))." (".$id.").pdf");

                    $isimail = "select acc_payment_requests.*, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.invoice = acc_invoice_vendors.tagihan where acc_payment_requests.id = ".$id;
                    $payment = db::select($isimail);

                    Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

                    $message = 'Payment Request';
                    $message2 ='Successfully Approved';
            }
            else{
                $message = 'Payment Request';
                $message2 ='Already Approved / Rejected';
            }

            return view('billing.purchasing.pr_message', array(
                'head' => 'Payment Request '.$pr->supplier_name,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Payment Request');

        } catch (Exception $e) {
            return view('billing.purchasing.pr_message', array(
                'head' => $pr->kind_of,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Payment Request');
        }
    }

    public function paymentapprovalgm($id){
        $pr = AccPaymentRequest::find($id);
        try{
            if ($pr->posisi == "gm")
            {
                $pr->posisi = 'acc';
                $pr->status_gm = "Approved/".date('Y-m-d H:i:s');
                $pr->status = "approval_acc";

                $mails = "select distinct email from users where username = 'PI1910003'";
                $mailtoo = DB::select($mails);

                $pr->save();

                $payment_detail = AccPaymentRequestDetail::select('*')
                ->where('id_payment',$id)
                ->whereNull('deleted_at')
                ->get();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('A4', 'potrait');

                $pdf->loadView('billing.report_payment_request', array(
                    'payment' => $pr,
                    'payment_detail' => $payment_detail,
                    'id' => $id
                ));

                $pdf->save(public_path() . "/payment_list/Payment ".$pr->supplier_name. " ".date('d-M-y', strtotime($pr->payment_date))." (".$id.").pdf");

                $isimail = "select acc_payment_requests.*, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.invoice = acc_invoice_vendors.tagihan where acc_payment_requests.id = ".$id;
                $payment = db::select($isimail);

                Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

                $message = 'Payment Request';
                $message2 ='Successfully Approved';

            }
            else{
                $message = 'Payment Request';
                $message2 ='Already Approved / Rejected';
            }

            return view('billing.purchasing.pr_message', array(
                'head' => 'Payment Request '.$pr->supplier_name,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Payment Request');

        } catch (Exception $e) {
            return view('billing.purchasing.pr_message', array(
                'head' => 'Payment Request '.$pr->supplier_name,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Payment Request');
        }
    }

    public function paymentreceiveacc($id){
        $pr = AccPaymentRequest::find($id);
        try{
            if ($pr->posisi == "acc")
            {
                $pr->posisi = 'received';
                $pr->status = "received";

                $pr->save();

                $message = 'Payment Request';
                $message2 ='Successfully Received';
            }
            else{
                $message = 'Payment Request';
                $message2 ='Already Approved / Rejected';
            }

            return view('billing.purchasing.pr_message', array(
                'head' => 'Payment Request '.$pr->supplier_name,
                'message' => $message,
                'message2' => $message2,
            ))->with('page', 'Payment Request');

        } catch (Exception $e) {
            return view('billing.purchasing.pr_message', array(
                'head' => 'Payment Request '.$pr->supplier_name,
                'message' => 'Error',
                'message2' => $e->getMessage(),
            ))->with('page', 'Payment Request');
        }
    }

    public function paymentreject(Request $request, $id)
    {
        $pr = AccPaymentRequest::find($id);

        if ($pr->posisi == "manager" || $pr->posisi == "gm")
        {
            $pr->datereject = date('Y-m-d H:i:s');
            $pr->posisi = "user";
            $pr->status_manager = null;
            $pr->status_dgm = null;
        }

        $pr->save();

        $payment_detail = AccPaymentRequestDetail::select('*')
        ->where('id_payment',$id)
        ->whereNull('deleted_at')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('billing.report_payment_request', array(
            'payment' => $pr,
            'payment_detail' => $payment_detail,
            'id' => $id
        ));

        $pdf->save(public_path() . "/payment_list/Payment ".$pr->supplier_name. " ".date('d-M-y', strtotime($pr->payment_date))." (".$id.").pdf");

        $isimail = "select * from acc_payment_requests where acc_payment_requests.id = ".$id;
        $tolak = db::select($isimail);

        //kirim email ke User
        $mails = "select distinct email from acc_payment_requests join users on acc_payment_requests.created_by = users.username where acc_payment_requests.id ='" . $id . "'";
        $mailtoo = DB::select($mails);

        Mail::to($mailtoo)->send(new SendEmail($tolak, 'payment_request'));

        $message = 'Payment Request';
        $message2 = 'Not Approved';

        return view('billing.purchasing.pr_message', array(
            'head' => 'Payment Request '.$pr->supplier_name,
            'message' => $message,
            'message2' => $message2,
        ))->with('page', 'Payment Request');
    }


    public function indexAccounting(){

        $title = 'Accounting';
        $page = 'Accounting';
        $title_jp = '';

        return view('billing.accounting.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $page)->with('head', $page);
    
    }

    public function indexWarehouse(){

        $title = 'Warehouse';
        $page = 'Warehouse';
        $title_jp = '';

        return view('billing.warehouse.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $page)->with('head', $page);
    
    }

    public function checkInvoice(Request $request)
    {
        try {
            $invoice = AccInvoiceVendor::find($request->get('id'));
            $invoice->status = 'checked_pch';
            $invoice->save();

            $response = array(
              'status' => true,
              'datas' => "Berhasil",
            );
            return Response::json($response);
        }
        catch(QueryException $e)
        {
            return redirect('/index/invoice')->with('error', $e->getMessage())
            ->with('page', 'Invoice');
        }
    }


}
