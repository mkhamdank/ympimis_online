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
use App\AccJurnal;
use App\AccJurnalDetail;
use App\AccJurnalInvoice;
use App\FixedAssetAudit;
use App\FixedAssetCheck;
use App\User;


class AccountingController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
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
            ORDER BY id desc
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
        sum(case when status = 'Open' || status = 'checked_pch' || status = 'payment_pch' then 1 else 0 end) as 'purchasing',
        sum(case when status = 'Acc' || status = 'payment_acc' then 1 else 0 end) as 'accounting',
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
        COALESCE(sum(case when status = 'Open' || status = 'checked_pch' || status = 'payment_pch' then 1 else 0 end),0) as 'purchasing',
        COALESCE(sum(case when status = 'Acc'  || status = 'payment_acc' then 1 else 0 end),0) as 'accounting',
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

public function fetchInvoiceMonitoringPch(){

    $data = db::select("
        SELECT
        count( id ) AS jumlah,
        supplier_name,
        sum( CASE WHEN STATUS = 'Open' THEN 1 ELSE 0 END ) AS 'invoice_open',
        sum( CASE WHEN STATUS = 'checked_pch' THEN 1 ELSE 0 END ) AS 'invoice_not_payment'
        FROM
        acc_invoice_vendors
        WHERE
        deleted_at IS NULL
        GROUP BY
        supplier_name
        ORDER BY
        jumlah DESC
        ");

    $data_outstanding = db::select("
        SELECT
        count( id ) AS jumlah,
        sum( CASE WHEN STATUS = 'Open' THEN 1 ELSE 0 END ) AS 'invoice_open',
        sum( CASE WHEN STATUS = 'checked_pch' THEN 1 ELSE 0 END ) AS 'invoice_not_payment'
        FROM
        acc_invoice_vendors
        WHERE deleted_at is null
        ");

    $response = array(
        'status' => true,
        'datas' => $data,
        'data_outstanding' => $data_outstanding
    );
    return Response::json($response);

}

public function fetchInvoiceMonitoringAcc(){

    $data = db::select("
        SELECT
        count( id ) AS jumlah,
        supplier_name,
        sum( CASE WHEN STATUS = 'payment_acc' THEN 1 ELSE 0 END ) AS 'invoice_payment_acc',
        sum( CASE WHEN STATUS = 'bank_acc' THEN 1 ELSE 0 END ) AS 'invoice_bank'
        FROM
        acc_invoice_vendors
        WHERE
        deleted_at IS NULL
        GROUP BY
        supplier_name
        ORDER BY
        jumlah DESC
        ");

    $data_outstanding = db::select("
        SELECT
        count( id ) AS jumlah,
        sum( CASE WHEN STATUS = 'payment_acc' THEN 1 ELSE 0 END ) AS 'invoice_payment_acc',
        sum( CASE WHEN STATUS = 'bank_acc' THEN 1 ELSE 0 END ) AS 'invoice_bank'
        FROM
        acc_invoice_vendors
        WHERE deleted_at is null
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

    $invoices = AccInvoiceVendor::select('acc_invoice_vendors.id as id_tagihan','tagihan','acc_invoice_vendors.supplier_code','acc_invoice_vendors.supplier_name','supplier_duration as payment_term')
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
        $gm_name = null;

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
            $gm_name = 'Budhi Apriyanto';
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
            'gm_name' => $gm_name,
            'created_by' => Auth::user()->username,
            'created_name' => Auth::user()->name
        ]);

        $payment->save();


        for ($i = 1;$i < $request->input('jumlah');$i++)
        {
            $payment_detail = new AccPaymentRequestDetail([
                'id_payment' => $payment->id,
                'id_invoice' => $request->get('invoice'.$i),
                'invoice' => $request->get('invoice_number'.$i),
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

            $update_invoice = AccInvoiceVendor::where('id',$request->get('invoice'.$i))->update([
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
    $invoice = AccInvoiceVendor::where('id', $request->invoice)
    ->get();
    foreach ($invoice as $inv)
    {
        $html = array(
            'invoice' => $inv->tagihan,
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

            $isimail = "select acc_payment_requests.*, acc_payment_request_details.id_invoice, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.id_invoice = acc_invoice_vendors.id where acc_payment_requests.id = ".$request->get('id');
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
            $update_invoice = AccInvoiceVendor::where('id',$pi->id_invoice)->update([
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

            $isimail = "select acc_payment_requests.*, acc_payment_request_details.id_invoice, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.id_invoice = acc_invoice_vendors.id where acc_payment_requests.id = ".$id;
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

            foreach($payment_detail as $payment){
                $updatePayment = AccInvoiceVendor::where('id','=',$payment->id_invoice)
                ->update([
                    'status' => 'payment_acc'
                ]);
            }

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');

            $pdf->loadView('billing.report_payment_request', array(
                'payment' => $pr,
                'payment_detail' => $payment_detail,
                'id' => $id
            ));

            $pdf->save(public_path() . "/payment_list/Payment ".$pr->supplier_name. " ".date('d-M-y', strtotime($pr->payment_date))." (".$id.").pdf");

            $isimail = "select acc_payment_requests.*, acc_payment_request_details.id_invoice, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.id_invoice = acc_invoice_vendors.id where acc_payment_requests.id = ".$id;
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

public function verifikasi_payment_request($id)
{
    $payment = AccPaymentRequest::find($id);
    $payment_detail = AccPaymentRequestDetail::select('*')
    ->where('id_payment',$id)
    ->whereNull('deleted_at')
    ->get();

    $path = '/payment_list/Payment '.$payment->supplier_name. ' '.date('d-M-y', strtotime($payment->payment_date)).' ('.$id.').pdf';
    $file_path = asset($path);

    return view('billing.verifikasi_payment_request', array(
        'payment' => $payment,
        'payment_detail' => $payment_detail,
        'file_path' => $file_path,
    ))->with('page', 'Payment Request');
}

public function approval_payment_request(Request $request, $id)
{
    $approve = $request->get('approve');

    if ($approve == "1") {

        $pr = AccPaymentRequest::find($id);

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

        }


        else if ($pr->posisi == "gm")
        {
            $pr->posisi = 'acc';
            $pr->status_gm = "Approved/".date('Y-m-d H:i:s');
            $pr->status = "approval_acc";

            $payment_detail = AccPaymentRequestDetail::select('*')
            ->where('id_payment',$id)
            ->whereNull('deleted_at')
            ->get();

            foreach($payment_detail as $payment){
                $updatePayment = AccInvoiceVendor::where('id','=',$payment->id_invoice)
                ->update([
                    'status' => 'payment_acc'
                ]);
            }

            $mails = "select distinct email from users where username = 'PI1910003'";
            $mailtoo = DB::select($mails);

            $pr->save();
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

        $isimail = "select acc_payment_requests.*, acc_payment_request_details.id_invoice, acc_payment_request_details.invoice, acc_payment_request_details.amount as amount_detail, net_payment,acc_invoice_vendors.file as attach_file from acc_payment_requests join acc_payment_request_details on acc_payment_requests.id = acc_payment_request_details.id_payment join acc_invoice_vendors on acc_payment_request_details.id_invoice = acc_invoice_vendors.id where acc_payment_requests.id = ".$id;
        $payment = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($payment, 'payment_request'));

        return redirect('/payment_request/verifikasi/' . $id)->with('status', 'Payment Request Approved')
        ->with('page', 'Payment Request');
    }
    else
    {
        return redirect('/payment_request/verifikasi/' . $id)->with('error', 'Payment Request Not Approved')
        ->with('page', 'Payment Request');
    }
}

public function reject_payment_request(Request $request, $id)
{
    $alasan = $request->get('alasan');

    $pr = AccPaymentRequest::find($id);

    if ($pr->posisi == "manager"  || $pr->posisi == "gm")
    {
        $pr->alasan = $alasan;
        $pr->datereject = date('Y-m-d H:i:s');
        $pr->posisi = "user";
        $pr->status_manager = null;
        $pr->status_gm = null;
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




public function indexPaymentRequestMonitoring(){
    return view('billing.monitoring_payment_request',
        array(
          'title' => 'Monitoring Payment Request',
          'title_jp' => ''
      ))->with('page', 'Monitoring Payment Request');
}

public function fetchPaymentRequestMonitoring(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccPaymentRequest::where('posisi','<>','acc')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(payment_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}
else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}

    //per tgl
$data = db::select("
   SELECT
   count( id ) AS jumlah,
   monthname( payment_date ) AS bulan,
   YEAR ( payment_date ) AS tahun,
   sum( CASE WHEN posisi = 'acc' THEN 1 ELSE 0 END ) AS NotSigned,
   sum( CASE WHEN posisi <> 'acc' THEN 1 ELSE 0 END ) AS Signed
   FROM
   acc_payment_requests
   WHERE
   acc_payment_requests.deleted_at IS NULL
   AND DATE_FORMAT( payment_date, '%Y-%m-%d' ) BETWEEN '".$datefrom."' AND '".$dateto."'
   GROUP BY
   bulan,
   tahun
   ORDER BY
   tahun,
   MONTH ( payment_date ) ASC
   ");

$year = date('Y');

$response = array(
    'status' => true,
    'datas' => $data,
    'year' => $year
);

return Response::json($response);
}



public function fetchtableinv(Request $request)
{
  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AccPaymentRequest::where('posisi','<>','acc')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(payment_date) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
}else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
    }
}
}


if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
}



$data = db::select("
    SELECT
    acc_payment_requests.*
    FROM
    acc_payment_requests
    WHERE
    acc_payment_requests.`posisi` <> 'acc'
    AND acc_payment_requests.deleted_at IS NULL
    AND DATE_FORMAT( payment_date, '%Y-%m-%d' ) BETWEEN '".$datefrom."'
    AND '".$dateto."'
    ORDER BY
    id ASC
    ");

$response = array(
    'status' => true,
    'datas' => $data
);

return Response::json($response);
}


public function detailMonitoringInv(Request $request){

  $week = $request->get("week");
  $status = $request->get("status");
  $tglfrom = $request->get("tglfrom");
  $tglto = $request->get("status");
  $department = $request->get("department");

  $status_sign = "";

  if ($status == "Investment Incompleted") {
      $status_sign = "and posisi != 'finished'";
  }
  else if ($status == "Investment Completed") {
      $status_sign = "and posisi = 'finished'";
  }

  $qry = "SELECT acc_investments.*, weekly_calendars.week_name FROM acc_investments JOIN weekly_calendars on acc_investments.submission_date = weekly_calendars.week_date WHERE acc_investments.deleted_at is null and week_name = '".$week."' ".$department." ".$status_sign." ORDER BY acc_investments.id DESC";


  $invest = DB::select($qry);

  return DataTables::of($invest)
  ->editColumn('submission_date', function ($invest)
  {
    return date('d F Y', strtotime($invest->submission_date));
})

  ->editColumn('supplier_code', function ($invest)
  {
    return $invest->supplier_code.' - '.$invest->supplier_name;
})
  ->editColumn('file', function ($invest)
  {
    $data = json_decode($invest->file);

    $fl = "";

    if ($invest->file != null)
    {
        for ($i = 0;$i < count($data);$i++)
        {
            $fl .= '<a href="files/investment/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
        }
    }
    else
    {
        $fl = '-';
    }

    return $fl;
})
  ->editColumn('status', function ($invest)
  {
    $id = $invest->id;

    if ($invest->posisi == "user" && $invest->status == "approval")
    {
        return '<label class="label label-danger">Belum Dikirim</label>';
    }
    if ($invest->posisi == "user" && $invest->status == "comment")
    {
        return '<label class="label label-warning">Commented</label>';
    }
    else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
    {
        return '<label class="label label-warning">Verifikasi Oleh Accounting</label>';
    }
    else if ($invest->posisi == "manager")
    {
        return '<label class="label label-warning">Diverifikasi Manager</label>';
    }
    else if ($invest->posisi == "dgm")
    {
        return '<label class="label label-warning">Diverifikasi DGM</label>';
    }
    else if ($invest->posisi == "gm")
    {
        return '<label class="label label-warning">Diverifikasi GM</label>';
    }
    else if ($invest->posisi == "manager_acc")
    {
        return '<label class="label label-warning">Diverifikasi Manager Accounting</label>';
    }
    else if ($invest->posisi == "direktur_acc")
    {
        return '<label class="label label-warning">Diverifikasi Direktur Accounting</label>';
    }
    else if ($invest->posisi == "presdir")
    {
        return '<label class="label label-warning">Diverifikasi Presdir</label>';
    }
    else if ($invest->posisi == "finished")
    {
        return '<label class="label label-success">Telah Diverifikasi</label>';
    }

})
  ->addColumn('action', function ($invest)
  {
    $id = $invest->id;

    if ($invest->posisi == "user")
    {
        return '
        <a href="detail/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a>
        <a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report PDF</a>
        ';
    }
    else if ($invest->posisi == "acc_budget" || $invest->posisi == "acc_pajak")
    {
        return '<a href="report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report PDF</a>';

    }
    else if ($invest->posisi == "acc" || $invest->posisi == "manager" || $invest->posisi == "dgm" || $invest->posisi == "gm" || $invest->posisi == "manager_acc" || $invest->posisi == "direktur_acc" || $invest->posisi == "presdir" || $invest->posisi == "finished")
    {
        return '
        <a href="report/' . $id . '" target="_blank" class="btn btn-warning btn-md" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report Investment</a>
        ';
    }
})

  ->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file', 'supplier_code' => 'supplier_code'])
  ->make(true);
}

public function indexVendorRegistration(){
    $title = 'Vendor Registration';
    $title_jp = '';

    $vendor = AccSupplier::select('acc_suppliers.*')
    ->whereNull('deleted_at')
    ->get();

    if (Auth::user()->role_code == "MIS"|| Auth::user()->role_code == "E - Purchasing") {
        return view('billing.purchasing.vendor_registration', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'vendor' => $vendor
        ))->with('page', 'Vendor Registration')
        ->with('head', 'Vendor Registration');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchVendorRegistration()
{
    try {
        $user = db::select("
            SELECT
                    *
            FROM
            users
            WHERE deleted_at is null
            and
            status = 'Unconfirmed'
            ");

        $response = array(
            'status' => true,
            'user' => $user,
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

public function approveVendorRegistration(Request $request)
{
    try {

        $ven = explode("_", $request->get('vendor'));

        $vendor = User::find($request->get('id'));
        $vendor->status = 'Confirmed';
        $vendor->role_code = 'E - Billing';
        $vendor->remark = $ven[0];
        $vendor->company = $ven[1];
        $vendor->save();

        Mail::to($vendor->email)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($vendor, 'register_confirmation'));


        $response = array(
          'status' => true,
          'datas' => "Berhasil Dikonfirmasi",
      );
        return Response::json($response);
    }
    catch(QueryException $e)
    {
        return redirect('/index/vendor/registration')->with('error', $e->getMessage())
        ->with('page', 'Vendor Registration');
    }
}

public function deleteVendorRegistration(Request $request)
{
    try {
        $delete_user = User::where('id', '=', $request->get('id'))->delete();

        $response = array(
          'status' => true,
          'datas' => "Berhasil",
      );
        return Response::json($response);
    }
    catch(QueryException $e)
    {
        return redirect('/index/vendor/registration')->with('error', $e->getMessage())
        ->with('page', 'Vendor Registration');
    }
}


public function indexAccountingPayment()
{
    $title = 'Accounting Payment System';
    $title_jp = '';

        // $invoices = AccPaymentRequest::select('acc_payment_requests.supplier_name','acc_payment_request_details.invoice','acc_payment_requests.currency','acc_payment_request_details.amount','acc_payment_request_details.ppn','acc_payment_request_details.pph','acc_payment_request_details.net_payment','acc_payment_requests.payment_term','acc_payment_requests.payment_due_date','acc_invoice_vendors.surat_jalan','acc_invoice_vendors.faktur_pajak','acc_invoice_vendors.purchase_order','acc_invoice_vendors.tanggal','acc_payment_requests.payment_date','acc_payment_requests.status_gm','acc_payment_request_details.id as id_payment_detail')
        // ->join('acc_payment_request_details','acc_payment_requests.id','=','acc_payment_request_details.id_payment')
        // ->join('acc_invoice_vendors','acc_payment_request_details.id_invoice','=','acc_invoice_vendors.id')
        // ->where('acc_payment_requests.posisi','=','acc')
        // ->get();

    if (Auth::user()->role_code == "MIS"|| Auth::user()->role_code == "E - Accounting") {
        return view('billing.acc_payment_request', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Accounting Payment')
        ->with('head', 'Accounting Payment');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchAccountingPayment(){
    $payment = AccPaymentRequest::select('acc_payment_requests.supplier_name','acc_payment_request_details.invoice','acc_payment_requests.currency','acc_payment_request_details.amount','acc_payment_request_details.ppn','acc_payment_request_details.pph','acc_payment_request_details.net_payment','acc_payment_requests.payment_term','acc_payment_requests.payment_due_date','acc_invoice_vendors.surat_jalan','acc_invoice_vendors.faktur_pajak','acc_invoice_vendors.purchase_order','acc_invoice_vendors.tanggal as tt_date','acc_payment_requests.payment_date as dist_date_pch','acc_payment_requests.status_gm','acc_payment_request_details.id as id_payment_detail','acc_invoice_vendors.file')
    ->join('acc_payment_request_details','acc_payment_requests.id','=','acc_payment_request_details.id_payment')
    ->join('acc_invoice_vendors','acc_payment_request_details.id_invoice','=','acc_invoice_vendors.id')
    ->where('acc_payment_requests.posisi','=','acc')
    ->whereNull('acc_payment_request_details.acc_payment')
    ->get();

    $response = array(
        'status' => true,
        'payment' => $payment
    );
    return Response::json($response);
}

public function fetchAccountingPaymentAfter(){
    $payment = AccPaymentRequest::select('acc_payment_requests.supplier_name','acc_payment_request_details.invoice','acc_payment_requests.currency','acc_payment_request_details.amount','acc_payment_request_details.ppn','acc_payment_request_details.pph','acc_payment_request_details.net_payment','acc_payment_requests.payment_term','acc_payment_requests.payment_due_date','acc_invoice_vendors.surat_jalan','acc_invoice_vendors.faktur_pajak','acc_invoice_vendors.purchase_order','acc_invoice_vendors.tanggal as tt_date','acc_payment_requests.payment_date as dist_date_pch','acc_payment_requests.status_gm','acc_payment_request_details.id as id_payment_detail','acc_payment_request_details.acc_payment','acc_invoice_vendors.file')
    ->join('acc_payment_request_details','acc_payment_requests.id','=','acc_payment_request_details.id_payment')
    ->join('acc_invoice_vendors','acc_payment_request_details.id_invoice','=','acc_invoice_vendors.id')
    ->where('acc_payment_requests.posisi','=','acc')
    ->whereNotNull('acc_payment_request_details.acc_payment')
    ->whereNull('acc_payment_request_details.status_jurnal')
    ->get();

    $response = array(
        'status' => true,
        'payment' => $payment
    );
    return Response::json($response);
}

public function postAccountingPayment(Request $request)
{
    try {
        $id_user = Auth::id();

        $id = explode(',',$request->get('id_payment'));
        $payment = explode(',',$request->get('payment'));
        $stat = 0;


        for ($i=0; $i < count($id); $i++) {
            $cek_jurnal = AccPaymentRequestDetail::where('acc_payment','=',$payment[$i])
            ->whereNotNull('status_jurnal')
            ->get();

            if(count($cek_jurnal) > 0){
                $stat++;
            }
        }

        if ($stat > 0) {
            $response = array(
                'status' => false,
                'message' => 'ID Payment ini sudah ada'
            );
            return Response::json($response);
        }
        else{
            for ($i=0; $i < count($id); $i++) {

                if ($payment[$i] == "") {
                    $payment[$i] = null;
                }

                $audit_all = AccPaymentRequestDetail::where('id',$id[$i])
                ->update([
                    'acc_payment' => $payment[$i],
                    'created_by' => $id_user
                ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Berhasil Dikonfirmasi'
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

public function indexAccountingJurnal()
{
    $title = 'Accounting Payment Junal';
    $title_jp = '';

    $vendor = AccSupplier::select('acc_suppliers.*')
    ->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    $bank = db::select("
        SELECT
                *
        FROM
        acc_rekenings
        WHERE deleted_at is null
        and not_active = 'FALSE'
        ORDER BY id desc
        ");

    $vendor_jurnal = DB::select('
        SELECT DISTINCT
        supplier_code,
        supplier_name
        FROM
        `acc_payment_requests`
        JOIN acc_payment_request_details ON acc_payment_requests.id = acc_payment_request_details.id_payment
        WHERE
        acc_payment IS NOT NULL
        AND status_jurnal IS NULL
        ');

    if (Auth::user()->role_code == "MIS"|| Auth::user()->role_code == "E - Accounting") {
        return view('billing.acc_payment_jurnal', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'vendor' => $vendor,
            'bank' => $bank,
            'vendor_jurnal' => $vendor_jurnal
        ))->with('page', 'Accounting Jurnal')
        ->with('head', 'Accounting Jurnal');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchJurnal(){
    $jurnal = db::select("SELECT * FROM acc_jurnals WHERE deleted_at IS NULL order by id desc");

    $response = array(
        'status' => true,
        'jurnal' => $jurnal
    );
    return Response::json($response);
}

public function indexReportJurnal()
{
    $title = 'Maintain List Bank';
    $page = 'Maintain List Bank';
    $title_jp = '';

    return view('billing.report_jurnal', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', $page)->with('head', $page);
}

public function fetchReportJurnal(Request $request)
{
    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-d');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-d');
              $last = $date_to;
          }
      }else{
       if ($date_to == "") {
          $first = $date_from;
          $last = date('Y-m-d');
      }else{
          $first = $date_from;
          $last = $date_to;
      }
  }

  $jurnal = AccJurnal::select('acc_jurnals.*')
  ->where('jurnal_date','>=',$first)
  ->where('jurnal_date','<=',$last);

  if($request->get('currency') != null){
      $curr =  explode(",", $request->get('currency'));
      $jurnal = $jurnal->whereIn('acc_jurnals.currency',$curr);
  }

  $jurnal = $jurnal->orderby('jurnal_date','desc')->get();

  $response = array(
    'status' => true,
    'jurnal' => $jurnal,
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

public function exportJurnal(Request $request){

    $time = date('d-m-Y H;i;s');

    $tanggal = "";
    $currency = "";

    if (strlen($request->get('date_from')) > 0)
    {
        $date_from = date('Y-m-d', strtotime($request->get('date_from')));
        $tanggal = "and jurnal_date >= '" . $date_from . "'";
        if (strlen($request->get('date_to')) > 0)
        {
            $date_to = date('Y-m-d', strtotime($request->get('date_to')));
            $tanggal = $tanggal . "and jurnal_date  <= '" . $date_to . "'";
        }
    }

    if($request->get('currency') != null){
          // $curr =  explode(",", $request->get('currency'));
      $currency = "and acc_jurnals.currency IN ('".$request->get('currency')."')";
  }

  $jurnal_detail = db::select(
    "Select acc_rekenings.settle_acc_no,acc_jurnals.supplier_name,acc_rekenings.rekening_nama,acc_rekenings.address_vendor,acc_rekenings.city,acc_rekenings.country,acc_rekenings.rekening_no,acc_jurnals.currency,acc_rekenings.sector_select ,acc_jurnals.contract_number,acc_jurnals.exchange_method, acc_rekenings.bank_name, acc_rekenings.bank_branch, acc_rekenings.bank_city_country, acc_jurnals.swift_code, acc_jurnals.iban, acc_rekenings.resident, acc_rekenings.citizenship, acc_rekenings.relation, acc_jurnals.purpose_remit, acc_rekenings.bank_charge,acc_jurnals.amount,acc_jurnals.jurnal_date,acc_jurnals.remark from acc_jurnals JOIN acc_rekenings on acc_jurnals.bank_id = acc_rekenings.id WHERE acc_jurnals.deleted_at IS NULL ".$tanggal." ".$currency." order by acc_jurnals.id ASC");

  $data = array(
    'jurnal_detail' => $jurnal_detail
);

  ob_clean();

  Excel::create('Jurnal List '.$time, function($excel) use ($data){
    $excel->sheet('Location', function($sheet) use ($data) {
      return $sheet->loadView('billing.jurnal_excel', $data);
  });
})->export('xlsx');
}

public function get_jurnal_type(Request $request)
{
    $jurnal_type = DB::select('
        SELECT
                *
        FROM
        `acc_jurnal_types`
        WHERE
        deleted_at IS NULL
        ');

    return json_encode($jurnal_type);
}

public function get_gl_account(Request $request)
{
    $gl_account = DB::select('
        SELECT
                *
        FROM
        `acc_gl_accounts`
        WHERE
        deleted_at IS NULL
        ');

    return json_encode($gl_account);
}

public function get_cost_center(Request $request)
{
    $cost_centers = DB::select('
        SELECT
                *
        FROM
        `acc_cost_centers`
        WHERE
        deleted_at IS NULL
        ');

    return json_encode($cost_centers);
}

public function getBank(Request $request)
{
    $html = array();
        // $vendor_code = AccSupplier::where('vendor_code', $request->supplier_code)
        // ->get();

    $bank = db::select("
        SELECT
                *
        FROM
        acc_rekenings
        WHERE id = '".$request->id."'
        and not_active = 'FALSE'
        ORDER BY id desc
        ");


    foreach ($bank as $bank)
    {
        $html = array(
            'vendor' => $bank->vendor,
            'currency' => $bank->currency,
            'branch' => $bank->branch,
            'rekening_no' => $bank->rekening_no,
            'rekening_nama' => $bank->rekening_nama,
            'internal' => $bank->internal,
            'ln' => $bank->ln,
            'bank_charge' => $bank->bank_charge,
            'switch_code' => $bank->switch_code
        );
    }
    return json_encode($html);
}

public function getIDPayment(Request $request){
    try {
        if ($request->get('supplier_code') != null) {
            $payment = AccPaymentRequest::select('acc_payment_request_details.acc_payment as payment_id')
            ->join('acc_payment_request_details','acc_payment_requests.id','=','acc_payment_request_details.id_payment')
            ->where('acc_payment_requests.supplier_code','=',$request->get('supplier_code'))
            ->whereNotNull('acc_payment_request_details.acc_payment')
            ->distinct()
            ->get();

            $vendor = AccSupplier::select('supplier_name')
            ->where('supplier_code','=',$request->get('supplier_code'))
            ->first();

        }else{
            $payment = null;
        }

        if (count($payment) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Success',
                'payment' => $payment,
                'vendor' => $vendor
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'No ID Payment Data',
                'payment' => ''
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

public function fetchInvoiceVerification(Request $request){
    try {
        if ($request->get('id_payment') != null) {
            $invoice = AccPaymentRequest::select('acc_payment_requests.supplier_name','acc_payment_requests.currency','acc_payment_request_details.invoice','acc_payment_request_details.id_invoice','acc_payment_request_details.amount','acc_payment_request_details.amount_service','acc_payment_request_details.ppn','acc_payment_request_details.pph','acc_payment_request_details.net_payment')
            ->join('acc_payment_request_details','acc_payment_requests.id','=','acc_payment_request_details.id_payment')
            ->where('acc_payment_request_details.acc_payment','=',$request->get('id_payment'))
            ->whereNotNull('acc_payment_request_details.acc_payment')
            ->get();
        }else{
            $invoice = null;
        }

        if (count($invoice) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Success',
                'invoice' => $invoice
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'No ID Payment Data',
                'payment' => ''
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

public function createJurnal(Request $request){
    try{
        $jurnal = new AccJurnal([
            'jurnal_date' => $request->input('jurnal_date'),
            'supplier_code' => $request->input('supplier_code'),
            'supplier_name' => $request->input('supplier_name'),
            'bank_id' => $request->input('bank_id'),
            'bank_branch' => $request->input('bank_branch'),
            'bank_beneficiary_name' => $request->input('bank_beneficiary_name'),
            'bank_beneficiary_no' => $request->input('bank_beneficiary_no'),
            'currency' => $request->input('currency'),
            'internal' => $request->input('internal'),
            'foreign' => $request->input('foreign'),
            'switch_code' => $request->input('switch_code'),
            'bank_charge' => $request->input('bank_charge'),
            'invoice' => $request->input('invoice'),
            'remark' => $request->input('remark'),
            'exchange_method' => $request->input('exchange_method'),
            'contract_number' => $request->input('contract_number'),
            'iban' => $request->input('iban'),
            'purpose_remit' => $request->input('purpose_remit'),
            'id_payment' => $request->input('id_payment'),
            'amount_bank_charge' => $request->input('amount_bank_charge'),
            'amount' => $request->input('amount'),
            'created_by' => Auth::user()->username,
            'created_name' => Auth::user()->name
        ]);

        $jurnal->save();


        for ($i = 0;$i < $request->input('total_baris_invoice');$i++)
        {
            $jurnal_invoice = new AccJurnalInvoice([
                'jurnal_id' => $jurnal->id,
                'payment_id' => $request->get('id_payment'),
                'supplier_name' => $request->get('invoice_supplier_name_'.$i),
                'invoice_no' => $request->get('invoice_number_'.$i),
                'currency' => $request->get('invoice_currency_'.$i),
                'amount' => $request->get('invoice_amount_'.$i),
                'ppn' => $request->get('invoice_ppn_'.$i),
                'pph' => $request->get('invoice_pph_'.$i),
                'net_payment' => $request->get('net_payment_'.$i),
                'created_by' => Auth::user()->username
            ]);

            $jurnal_invoice->save();

            $update_status = AccPaymentRequestDetail::where('acc_payment',$request->get('id_payment'))
            ->where('id_invoice',$request->get('invoice_id_'.$i))
            ->update([
                'status_jurnal' => 'true'
            ]);
        }

        for ($i = 0;$i < $request->input('total_baris_jurnal');$i++)
        {
            $jurnal_detail = new AccJurnalDetail([
                'jurnal_id' => $jurnal->id,
                'seq_id' => $request->get('seq_'.$i),
                'reference' => $request->get('reference_'.$i),
                'cost_center' => $request->get('cost_center_'.$i),
                'type' => $request->get('type_'.$i),
                'gl_account' => $request->get('gl_account_'.$i),
                'gl_desc' => $request->get('gl_desc_'.$i),
                'currency' => $request->get('currency_'.$i),
                'amount' => $request->get('amount_'.$i),
                'note' => $request->get('note_'.$i),
                'created_by' => Auth::user()->username
            ]);

            $jurnal_detail->save();
        }

        $response = array(
            'status' => true,
            'message' => 'New Jurnal Successfully Added'
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


public function indexVendor()
{
    $title = 'Vendor Data';
    $title_jp = '';

    if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Purchasing" || Auth::user()->role_code == "E - Accounting") {

        return view('billing.master.index_vendor', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))
        ->with('page', 'Vendor Data')
        ->with('head', 'Vendor Data');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchVendor()
{
    try {
        $vendor = db::select("
            SELECT
                    *
            FROM
            acc_suppliers
            WHERE deleted_at is null
            ORDER BY id desc
            ");

        $response = array(
            'status' => true,
            'vendor' => $vendor,
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

public function indexBank()
{
    $title = 'Bank Data';
    $title_jp = '';

    if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Accounting") {
        return view('billing.master.index_bank', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))
        ->with('page', 'Bank Data')
        ->with('head', 'Bank Data');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchBank()
{
    try {
        $bank = db::select("
            SELECT
                    *
            FROM
            acc_rekenings
            WHERE deleted_at is null
            and not_active = 'FALSE'
            ORDER BY id desc
            ");

        $response = array(
            'status' => true,
            'bank' => $bank,
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

public function indexCostCenter()
{
    $title = 'Cost Center Data';
    $title_jp = '';

    if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Accounting") {
        return view('billing.master.index_cost_center', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))
        ->with('page', 'Bank Data')
        ->with('head', 'Bank Data');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchCostCenter()
{
    try {
        $cost_center = db::select("
            SELECT
                    *
            FROM
            acc_cost_centers_s4
            ORDER BY id desc
            ");

        $response = array(
            'status' => true,
            'cost_center' => $cost_center,
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

public function indexGLAccount()
{
    $title = 'GL Account Data';
    $title_jp = '';

    if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "E - Accounting") {
        return view('billing.master.index_gl_account', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))
        ->with('page', 'GL Account Data')
        ->with('head', 'GL Account Data');
    }
    else{
        return view('404',
            array('message' => 'Anda Tidak Memiliki Akses Untuk Halaman Ini')
        );
    }
}

public function fetchGLAccount()
{
    try {
        $gl_account = db::select("
            SELECT
                    *
            FROM
            acc_gl_accounts
            WHERE deleted_at is null
            ORDER BY id desc
            ");

        $response = array(
            'status' => true,
            'gl_account' => $gl_account,
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


    // ----------------------- FIXED ASSET -----------------------

public function indexFixedAsset()
{
    $title = 'Fixed Asset';
    $title_jp = '';

    $period = FixedAssetAudit::select('period')->groupby('period')->get();

    return view('fixed_asset.index', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'period' => $period
    ))->with('page', 'Fixed Asset')
    ->with('head', 'Fixed Asset');
}

public function fetchFixedAsset(Request $request)
{

}

public function fetchAssetAuditList(Request $request)
{
   if ($request->get('period') == '') {
    if (Auth::user()->username != 'PI0905001' && Auth::user()->username != 'PI2002021') {
        $prd = FixedAssetAudit::select('period')->whereIn('fixed_asset_audits.asset_section', ['Buyer & RD Admin Section'])
        ->orderBy('id','desc')
        ->limit(1)
        ->first();
    } else {
        $prd = FixedAssetAudit::select('period')->orderBy('id','desc')
        ->limit(1)
        ->first();
    }

    $period = $prd->period;
} else {
    $period = $request->get('period');
}

$assets = FixedAssetAudit::where('fixed_asset_checks.period', '=', $period)
->where('fixed_asset_checks.location', 'Bahana Unindo')
->leftJoin('fixed_asset_checks', function($join)
{
    $join->on('fixed_asset_checks.Period', '=', 'fixed_asset_audits.period');
    $join->on('fixed_asset_checks.sap_number', '=', 'fixed_asset_audits.sap_number');
});

if (Auth::user()->username != 'PI0905001'  && Auth::user()->username != 'PI2002021') {
    $assets = $assets->whereIn('fixed_asset_audits.asset_section', ['Buyer & RD Admin Section']);
}

$assets = $assets->select('fixed_asset_audits.period', 'fixed_asset_audits.sap_number', 'fixed_asset_audits.asset_name', 'fixed_asset_audits.location', 'fixed_asset_audits.asset_section', 'fixed_asset_checks.asset_images', 'fixed_asset_checks.status', 'fixed_asset_checks.category', 'fixed_asset_checks.appr_manager_at', 'fixed_asset_checks.remark', 'fixed_asset_audits.checked_by')
->get();

$response = array(
    'status' => true,
    'assets' => $assets
);
return Response::json($response);
}

public function indexAssetCheck($cek_num, $section, $location, $period) {
    $title = "Form Check Fixed Asset";
    $title_jp = "??";

    $loc = FixedAssetAudit::distinct()->select('location', 'asset_section')->where('period','=',$period)->get();
    $section = ['Buyer & RD Admin Section'];

    return view('fixed_asset.form.audit_form', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $loc,
        'section' => $section,
    ));
}

public function fetchAssetbyLocation(Request $request)
{
    DB::enableQueryLog();

    $section = str_replace('&amp;', '&', $request->get('section'));

    // status
    $asset = FixedAssetAudit::leftJoin('fixed_asset_items', 'fixed_asset_items.sap_number', '=', 'fixed_asset_audits.sap_number')
    ->leftJoin('fixed_asset_checks', function($join)
    {
        $join->on('fixed_asset_checks.sap_number', '=', 'fixed_asset_audits.sap_number');
        $join->on('fixed_asset_checks.period','=', 'fixed_asset_audits.period');
    })
    ->where('fixed_asset_audits.period', '=', $request->get('period'))
    ->where('fixed_asset_audits.status', '=', 'Open');

    if ($request->get('status') == 'check1') {
        $asset = $asset->where('fixed_asset_checks.status', '=' , 'Not Checked');
        $asset = $asset->whereNull('fixed_asset_checks.remark');
    }

    if ($request->get('status') == 'check2') {
        $asset = $asset->where('fixed_asset_checks.status', '=' , 'Check 1');
        $asset = $asset->whereNull('fixed_asset_checks.remark');
    }

    if ($request->get('status') == 'audit') {
        $asset = $asset->whereNull('fixed_asset_audits.remark');
    }

    $asset = $asset->where('fixed_asset_audits.asset_section', '=' ,$section);
    $asset = $asset->where('fixed_asset_audits.location' , '=',$request->get('area'));

    $asset = $asset->select('fixed_asset_audits.id', 'fixed_asset_audits.period', 'fixed_asset_audits.category', 'fixed_asset_audits.location', 'fixed_asset_audits.sap_number', 'fixed_asset_audits.asset_name', 'fixed_asset_audits.asset_section', 'fixed_asset_audits.asset_map', 'fixed_asset_audits.asset_images', 'fixed_asset_audits.pic', 'fixed_asset_audits.id', 'fixed_asset_checks.availability', 'fixed_asset_checks.asset_condition', 'fixed_asset_checks.label_condition', 'fixed_asset_checks.usable_condition', 'fixed_asset_checks.map_condition', 'fixed_asset_checks.asset_image_condition', 'fixed_asset_audits.checked_by', 'fixed_asset_audits.status', 'fixed_asset_audits.checked_date', 'fixed_asset_items.request_date', 'fixed_asset_checks.result_images')
    ->get();

    $audit = 0;
    if ($request->get('status') == 'audit') {
        $audit_q = FixedAssetAudit::whereNotNull('remark')
        ->where('period', '=', $request->get('period'))
        ->where('asset_section', '=', $section)
        ->where('location' , '=', $request->get('area'))
        ->select(db::raw('COUNT(id) as count_audit'))
        ->first();

        $audit = $audit_q->count_audit;
    }

    $response = array(
        'status' => true,
        'asset' => $asset,
        'audit' => $audit,
        'query' => DB::getQueryLog()
    );
    return Response::json($response);
}


public function inputAssetCheckTemp(Request $request)
{
    try {
        if (count($request->file('fileData')) > 0) {

            $tujuan_upload = 'files/fixed_asset/asset_check';
            $file = $request->file('fileData');
            $filename = $request->get('period').'_'.$request->get('category').'_'.$request->input('asset_id').'.'.$request->input('extension');
            $file->move($tujuan_upload,$filename);

            $check = FixedAssetCheck::where('sap_number',$request->get('asset_id'))->where('period',$request->get('period'))->first();
            $check->availability = $request->get('availability');
            $check->asset_condition = $request->get('asset_condition');
            $check->label_condition = $request->get('label_condition');
            $check->usable_condition = $request->get('usable_condition');
            $check->map_condition = $request->get('map_condition');
            $check->asset_image_condition = $request->get('image_condition');
            $check->note = $request->get('note');
            $check->result_images = $filename;

            if ($request->get('category') == 'check1') {
                $check->check_one_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_one_at = date('Y-m-d H:i:s');
                $check->remark = 'temporary save';
            } else if ($request->get('category') == 'check2') {
                $check->check_two_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_two_at = date('Y-m-d H:i:s');
                $check->remark = 'temporary save';
            }

            $check->save();

            $response = array(
              'status' => true,
          );
            return Response::json($response);
        } else if($request->get('availability') == 'Tidak Ada'){
            $check = FixedAssetCheck::where('sap_number',$request->get('asset_id'))->where('period',$request->get('period'))->first();
            $check->availability = $request->get('availability');
            $check->asset_condition = $request->get('asset_condition');
            $check->label_condition = $request->get('label_condition');
            $check->usable_condition = $request->get('usable_condition');
            $check->map_condition = $request->get('map_condition');
            $check->asset_image_condition = $request->get('image_condition');
            $check->note = $request->get('note');

            if ($request->get('category') == 'check1') {
                $check->check_one_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_one_at = date('Y-m-d H:i:s');
                $check->remark = 'temporary save';
            } else if ($request->get('category') == 'check2') {
                $check->check_two_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_two_at = date('Y-m-d H:i:s');
                $check->remark = 'temporary save';
            }

            $check->save();

            $response = array(
              'status' => true,
          );
            return Response::json($response);
        } else{
          $response = array(
              'status' => false,
              'message' => 'Upload Photo on Point '.$request->input('asset_name')
          );
          return Response::json($response);
      }
  } catch (\Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage().' on Line'. $e->getLine()
    );
    return Response::json($response);
}
}

public function inputAssetCheck(Request $request)
{
    $section = str_replace('&amp;', '&', $request->get('section'));
    try {
        if (count($request->file('fileData')) > 0) {
            $tujuan_upload = 'files/fixed_asset/asset_check';
            $file = $request->file('fileData');
            $filename = $request->get('period').'_'.$request->get('category').'_'.$request->input('asset_id').'.'.$request->input('extension');
            $file->move($tujuan_upload,$filename);

            $check = FixedAssetCheck::where('sap_number',$request->get('asset_id'))->where('period',$request->get('period'))->first();
            $check->availability = $request->get('availability');
            $check->asset_condition = $request->get('asset_condition');
            $check->label_condition = $request->get('label_condition');
            $check->usable_condition = $request->get('usable_condition');
            $check->map_condition = $request->get('map_condition');
            $check->asset_image_condition = $request->get('image_condition');
            $check->note = $request->get('note');
            $check->result_images = $filename;

            if ($request->get('category') == 'check1') {
                $check->check_one_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_one_at = date('Y-m-d H:i:s');

                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $check->asset_section)
                ->where('location', '=', $check->location)
                ->update([
                    'status' => 'Check 1',
                    'remark' => null
                ]);
            } else if ($request->get('category') == 'check2') {
                $check->check_two_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_two_at = date('Y-m-d H:i:s');

                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $check->asset_section)
                ->where('location', '=', $check->location)
                ->update([
                    'status' => 'Check 2',
                    'remark' => null
                ]);
            }

            $check->save();

            $response = array(
              'status' => true,
          );
            return Response::json($response);
        } else if($request->get('availability') == 'Tidak Ada'){
            $check = FixedAssetCheck::where('sap_number',$request->get('asset_id'))->where('period',$request->get('period'))->first();
            $check->availability = $request->get('availability');
            $check->asset_condition = $request->get('asset_condition');
            $check->label_condition = $request->get('label_condition');
            $check->usable_condition = $request->get('usable_condition');
            $check->map_condition = $request->get('map_condition');
            $check->asset_image_condition = $request->get('image_condition');
            $check->note = $request->get('note');


            if ($request->get('category') == 'check1') {
                $check->check_one_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_one_at = date('Y-m-d H:i:s');

                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $section)
                ->where('location', '=', $check->location)
                ->update([
                    'status' => 'Check 1',
                    'remark' => null
                ]);

            } else if ($request->get('category') == 'check2') {
                $check->check_two_by = Auth::user()->username.'/'.Auth::user()->name;
                $check->check_two_at = date('Y-m-d H:i:s');

                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $check->asset_section)
                ->where('location', '=', $check->location)
                ->update([
                    'status' => 'Check 2',
                    'remark' => null
                ]);
            }

            $check->save();

            $response = array(
              'status' => true,
          );
            return Response::json($response);
        }
        else if($request->get('counter') == 0){
            $z = 'a';
            if ($request->get('category') == 'check1') {
                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $section)
                ->where('location', '=', $request->get('location'))
                ->update([
                    'status' => 'Check 1',
                    'remark' => null
                ]);
                $z = 'ck1';

            } else if ($request->get('category') == 'check2') {
                $update_asset = FixedAssetCheck::where('period',$request->get('period'))
                ->where('asset_section', '=', $section)
                ->where('location', '=', $request->get('location'))
                ->update([
                    'status' => 'Check 2',
                    'remark' => null
                ]);

                $z = 'ck2';
            }

            $response = array(
              'status' => true,
              'counter' => $z
          );
            return Response::json($response);

        }

        else{
          $response = array(
              'status' => false,
              'message' => 'Upload Photo on Point '.$request->input('asset_name')
          );
          return Response::json($response);
      }
  } catch (\Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage().' on Line'. $e->getLine()
    );
    return Response::json($response);
}
}

public function indexAssetAuditListAuditor()
{
    $title = "Fixed Asset Audit List";
    $title_jp = "??";

    $section_loc = ['Buyer & RD Admin Section'];
    $period = FixedAssetAudit::select('period')->groupby('period')->get();

    return view('fixed_asset.form.auditor_audit_list', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $section_loc,
        'period' => $period
    ))->with('page', 'Fixed Asset Audit List');
}

public function approvalFixedAsset(Request $request)
{
    $asset_qty = FixedAssetCheck::where('category', '=', $request->get('category'))
    ->where('status', '<>', 'Check 2')
    ->where('location', '=', $request->get('location'))
    ->where('period', '=', $request->get('period'))
    ->get();

    if (count($asset_qty) > 0) {
        $response = array(
            'status' => false,
            'message' => 'Semua Asset dalam location harus sudah dicek'
        );
        return Response::json($response);
    }

    $asset_stat = FixedAssetCheck::where('category', '=', $request->get('category'))
    ->where('location', '=', $request->get('location'))
    ->where('period', '=', $request->get('period'))
    ->where('appr_status', '=', 'send')
    ->get();

    if (count($asset_stat) > 0) {
        $response = array(
            'status' => false,
            'message' => 'Approval sudah pernah dikirim'
        );
        return Response::json($response);
    }

    $asset_check = FixedAssetCheck::where('category', '=', $request->get('category'))
    ->where('status', '=', 'Check 2')
    ->where('location', '=', $request->get('location'))
    ->where('period', '=', $request->get('period'))
    ->select('period', 'location', db::raw('count(sap_number) as total_asset'))
    ->groupBy('period', 'location')
    ->get();

    FixedAssetCheck::where('category', '=', $request->get('category'))
    ->where('status', '=', 'Check 2')
    ->where('location', '=', $request->get('location'))
    ->where('period', '=', $request->get('period'))
    ->update([
        'appr_status' => 'send'
    ]);

    $update_mirai = db::select("UPDATE ympimis.fixed_asset_checks
    LEFT JOIN ympimis_online.fixed_asset_checks on ympimis.fixed_asset_checks.location = ympimis_online.fixed_asset_checks.location AND ympimis.fixed_asset_checks.period = ympimis_online.fixed_asset_checks.period
    SET ympimis.fixed_asset_checks.result_images = ympimis_online.fixed_asset_checks.result_images,
    ympimis.fixed_asset_checks.note = ympimis_online.fixed_asset_checks.note,
    ympimis.fixed_asset_checks.availability = ympimis_online.fixed_asset_checks.availability,
    ympimis.fixed_asset_checks.asset_condition = ympimis_online.fixed_asset_checks.asset_condition,
    ympimis.fixed_asset_checks.label_condition = ympimis_online.fixed_asset_checks.label_condition,
    ympimis.fixed_asset_checks.usable_condition = ympimis_online.fixed_asset_checks.usable_condition,
    ympimis.fixed_asset_checks.map_condition = ympimis_online.fixed_asset_checks.map_condition,
    ympimis.fixed_asset_checks.asset_image_condition = ympimis_online.fixed_asset_checks.asset_image_condition,
    ympimis.fixed_asset_checks.status = ympimis_online.fixed_asset_checks.status,
    ympimis.fixed_asset_checks.check_one_by = ympimis_online.fixed_asset_checks.check_one_by,
    ympimis.fixed_asset_checks.check_one_at = ympimis_online.fixed_asset_checks.check_one_at,
    ympimis.fixed_asset_checks.check_two_by = ympimis_online.fixed_asset_checks.check_two_by,
    ympimis.fixed_asset_checks.check_two_at = ympimis_online.fixed_asset_checks.check_two_at,
    ympimis.fixed_asset_checks.appr_chief_by = ympimis_online.fixed_asset_checks.appr_chief_by,
    ympimis.fixed_asset_checks.appr_chief_at = ympimis_online.fixed_asset_checks.appr_chief_at,
    ympimis.fixed_asset_checks.appr_manager_by = ympimis_online.fixed_asset_checks.appr_manager_by,
    ympimis.fixed_asset_checks.appr_manager_at = ympimis_online.fixed_asset_checks.appr_manager_at,
    ympimis.fixed_asset_checks.appr_status = ympimis_online.fixed_asset_checks.appr_status
    WHERE ympimis_online.fixed_asset_checks.period = '".$request->get('period')."' AND ympimis_online.fixed_asset_checks.location = '".$request->get('location')."'");

    $chief_foreman = ['adianto.heru@music.yamaha.com'];

    $mailto = [];

    foreach ($chief_foreman as $chf_fr) {
        array_push($mailto, $chf_fr);
    }

    $summary_data = db::select("select location,
        SUM(IF(availability = 'Ada', 1, 0)) as ada,
        SUM(IF(availability = 'Tidak Ada', 1, 0)) as tidak_ada,
        SUM(IF(asset_condition = 'Rusak', 1, 0)) as rusak,
        SUM(IF(usable_condition = 'Tidak Digunakan', 1, 0)) as tidak_digunakan,
        SUM(IF(label_condition = 'Rusak', 1, 0)) as label_rusak,
        SUM(IF(map_condition = 'Tidak Sesuai', 1, 0)) as map_rusak,
        SUM(IF(asset_image_condition = 'Tidak Sesuai', 1, 0)) as image_rusak
        from fixed_asset_checks where `status` = 'Check 2' and location = '".$request->get('location')."' and period = '".$request->get('period')."'
        group by location");

    $att = [];

    $data = [
        "datas" => $asset_check,
        "position" => 'Chief Foreman',
        "status" => 'Approve',
        "data_details" => $summary_data,
        "period" => $asset_check[0]->period,
        "att" => $att
    ];

    Mail::to($mailto)->bcc(['ismail.husen@music.yamaha.com','nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'fixed_asset_check'));

    $response = array(
        'status' => true,
    );
    return Response::json($response);
}

public function postAssetMap(Request $request)
{
    $location = explode(',', $request->get('location'));

    for ($i=0; $i <= count($location); $i++) {

        if ($request->file('map_'.$i)) {
            $tujuan_upload = 'files/fixed_asset/map';
            $file = $request->file('map_'.$i);

            $nama = $file->getClientOriginalName();
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = $location[$i].'.'.$extension;

            $file->move($tujuan_upload,$filename);

            FixedAssetCheck::where('location', '=', $location[$i])
            ->update([
                'asset_map' => $filename
            ]);

            FixedAssetAudit::where('location', '=', $location[$i])
            ->update([
                'asset_map' => $filename
            ]);
        }

    }

    $response = array(
        'status' => true
    );
    return Response::json($response);
}

public function pdfFixedAsset($location, $period)
{
  $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $check_data = FixedAssetCheck::leftjoin('fixed_asset_items', 'fixed_asset_items.sap_number', '=', 'fixed_asset_audits.sap_number')
    ->where('fixed_asset_audits.period', '=', $period)
    ->where('fixed_asset_audits.asset_section', '=', $section)
    ->where('fixed_asset_audits.location', '=', $location)
    ->select('fixed_asset_audits.id','fixed_asset_audits.period', 'fixed_asset_audits.category', 'fixed_asset_audits.location', 'fixed_asset_audits.sap_number', 'fixed_asset_audits.asset_name', 'fixed_asset_audits.asset_section', 'fixed_asset_audits.asset_images', 'fixed_asset_audits.result_images', 'fixed_asset_audits.note', 'fixed_asset_checks.availability', 'fixed_asset_checks.asset_condition', 'fixed_asset_checks.label_condition', 'fixed_asset_checks.usable_condition', 'fixed_asset_checks.map_condition', 'fixed_asset_checks.asset_image_condition', 'fixed_asset_audits.checked_by', 'fixed_asset_audits.checked_date', 'fixed_asset_checks.check_one_by', 'fixed_asset_checks.check_one_at', 'fixed_asset_checks.check_two_by', 'fixed_asset_checks.check_two_at', 'fixed_asset_checks.appr_manager_by', 'fixed_asset_checks.appr_manager_at', db::raw('DATE_FORMAT("fixed_asset_items.request_date", "%Y-%b") as reg_date'))
    ->get();

    $pdf->loadView('fixed_asset.report_file.audit_report', array(
        'check_data' => $check_data,
    ));

    // $pdf->save(public_path() . "/payment_list/Payment ".$request->input('kind_of'). " ".date('d-M-y', strtotime($request->input('payment_date'))).".pdf");
    return $pdf->stream("Audit Fixed Asset ".$period."_".$section.".pdf");
}
}
