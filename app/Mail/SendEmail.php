<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $remark;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $remark)
    {
        $this->data = $data;
        $this->remark = $remark;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build()
    {
        if($this->remark == 'request_reset_password'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Reset Password Request (パスワードリセットの申請)')->view('mails.request_reset_password');
        }

        if($this->remark == 'register'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Register Vendor Information (登録情報)')->view('mails.register');
        }

        if($this->remark == 'register_confirmation'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('YMPI Register Confirmation')->view('mails.register_confirmation');
        }

        if($this->remark == 'change_password'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Change Password Information (パスワード変更の情報)')->view('mails.change_password');
        }

        if($this->remark == 'critical_true'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Critical Defect PT. TRUE')->view('mails.critical_true');
        }

        if($this->remark == 'over_limit_ratio_true'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Non Critical Defect Ratio ( > 5%) PT. TRUE')->view('mails.over_limit_ratio_true');
        }

         if($this->remark == 'critical_arisa'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Inpection Report (100%) PT. ARISAMANDIRI PRATAMA')->view('mails.critical_arisa');
        }

        if($this->remark == 'lot_out_arisa'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Lot Out Report PT. ARISAMANDIRI PRATAMA')->view('mails.lot_out_arisa');
        }

        if($this->remark == 'over_limit_ratio_arisa'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Inpection Report (100%) PT. ARISAMANDIRI PRATAMA')->view('mails.over_limit_ratio_arisa');
        }

        if($this->remark == 'critical_kbi'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Inpection Report (100%) PT. KYORAKU BLOWMOLDING INDONESIA')->view('mails.critical_kbi');
        }

        if($this->remark == 'over_limit_ratio_kbi'){
            return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')->subject('Inpection Report (100%) PT. KYORAKU BLOWMOLDING INDONESIA')->view('mails.over_limit_ratio_kbi');
        }

        if($this->remark == 'payment_request'){
            if($this->data[0]->pdf != null){
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Payment Request (支払リクエスト)')
                ->view('mails.payment_request')
                ->attach(public_path('payment_list/'.$this->data[0]->pdf));
            }else{
                return $this->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia')
                ->priority(1)
                ->subject('Payment Request (支払リクエスト)')
                ->view('mails.payment_request');
            }
        }
    }
}
