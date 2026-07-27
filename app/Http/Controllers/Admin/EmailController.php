<?php

namespace App\Http\Controllers\Admin;

use App\{
    Classes\GeniusMailer,
    Models\EmailLog,
    Models\EmailTemplate,
    Models\Generalsetting,
    Models\User
};
use Illuminate\Http\Request;
use Datatables;

class EmailController extends AdminBaseController
{

    //*** JSON Request
    public function datatables()
    {
         $datas = EmailTemplate::latest('id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->addColumn('action', function(EmailTemplate $data) {
                                return '<div class="action-list"><a data-href="' . route('admin-mail-edit',$data->id) . '" class="edit" data-toggle="modal" data-target="#modal1"> <i class="fas fa-edit"></i>'.__('Edit').'</a></div>';
                            }) 
                            ->toJson();//--- Returning Json Data To Client Side
    }

    public function index(){
        return view('admin.email.index');
    }

    public function config(){
        $emailLogs = EmailLog::latest('id')->take(50)->get();
        return view('admin.email.config', compact('emailLogs'));
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        $gs = Generalsetting::findOrFail(1);
        $to = $request->test_email;
        $subject = 'IndustrialMac SMTP Test';
        $body = '<p>This is a test email from <strong>IndustrialMac</strong>.</p>'
            . '<p>If you received this, SMTP is working.</p>'
            . '<p>Sent at: ' . now()->toDateTimeString() . '</p>';

        try {
            if ((int) $gs->is_smtp === 1) {
                $ok = (new GeniusMailer())->sendCustomMail([
                    'to' => $to,
                    'subject' => $subject,
                    'body' => $body,
                    'type' => 'test',
                ]);
            } else {
                $headers = 'From: ' . $gs->from_name . ' <' . $gs->from_email . '>' . "\r\n";
                $headers .= 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                $ok = @mail($to, $subject, $body, $headers);
                try {
                    EmailLog::create([
                        'type' => 'test',
                        'to_email' => $to,
                        'from_email' => $gs->from_email,
                        'subject' => $subject,
                        'status' => $ok ? 'sent' : 'failed',
                        'error' => $ok ? null : 'PHP mail() returned false',
                    ]);
                } catch (\Throwable $ignore) {
                }
            }

            if ($ok) {
                return back()->with('success', __('Test email sent to :email. Check the inbox and Email Logs below.', ['email' => $to]));
            }

            return back()->with('unsuccess', __('Test email failed. Check Email Logs for the error details.'));
        } catch (\Throwable $e) {
            try {
                EmailLog::create([
                    'type' => 'test',
                    'to_email' => $to,
                    'from_email' => $gs->from_email,
                    'subject' => $subject,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $ignore) {
            }

            return back()->with('unsuccess', __('Test email failed: :error', ['error' => $e->getMessage()]));
        }
    }

    public function edit($id)
    {
        $data = EmailTemplate::findOrFail($id);
        return view('admin.email.edit',compact('data'));
    }

    public function groupemail()
    {
        $config = Generalsetting::findOrFail(1);
        return view('admin.email.group',compact('config'));
    }

    public function groupemailpost(Request $request)
    {
        $config = Generalsetting::findOrFail(1);
        if($request->type == 0)
        {
        $users = User::all();
        //Sending Email To Users
        foreach($users as $user)
        {
            if($config->is_smtp == 1)
            {
                $data = [
                    'to' => $user->email,
                    'subject' => $request->subject,
                    'body' => $request->body,
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);            
            }
            else
            {
               $to = $user->email;
               $subject = $request->subject;
               $msg = $request->body;
                $headers = "From: ".$config->from_name."<".$config->from_email.">";
               mail($to,$subject,$msg,$headers);
            }  
        } 
        //--- Redirect Section          
        $msg = __('Email Sent Successfully.');
        return response()->json($msg);    
        //--- Redirect Section Ends  
        }

        else if($request->type == 1)
        {
        $users = User::where('is_vendor','=','2')->get();
        //Sending Email To Vendors        
        foreach($users as $user)
        {
            if($config->is_smtp == 1)
            {
                $data = [
                    'to' => $user->email,
                    'subject' => $request->subject,
                    'body' => $request->body,
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);            
            }
            else
            {
               $to = $user->email;
               $subject = $request->subject;
               $msg = $request->body;
                $headers = "From: ".$config->from_name."<".$config->from_email.">";
               mail($to,$subject,$msg,$headers);
            }  
        } 
        //--- Redirect Section          
        $msg = __('Email Sent Successfully.');
        return response()->json($msg);    
        //--- Redirect Section Ends  
        }


    }

    public function update(Request $request, $id)
    {
        $data = EmailTemplate::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        //--- Redirect Section          
        $msg = __('Data Updated Successfully.');
        return response()->json($msg);    
        //--- Redirect Section Ends  
    }

}
