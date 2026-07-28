<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use App\Models\QuoteMessage;
use App\Models\QuoteRequest;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuoteRequestController extends AdminBaseController
{
    public function datatables()
    {
        $datas = QuoteRequest::with(['product', 'category', 'brand'])->orderBy('id', 'desc')->get();

        return Datatables::of($datas)
            ->editColumn('product_name', function (QuoteRequest $data) {
                return $data->product_name ?: ($data->product->name ?? '-');
            })
            ->editColumn('status', function (QuoteRequest $data) {
                return ucfirst($data->status);
            })
            ->addColumn('action', function (QuoteRequest $data) {
                return '<div class="action-list"><a href="' . route('admin-quote-show', $data->id) . '"><i class="fas fa-eye"></i>' . __('View') . '</a></div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function index()
    {
        return view('admin.quote.index');
    }

    public function show($id)
    {
        $data = QuoteRequest::with(['product', 'category', 'brand', 'messages'])->findOrFail($id);

        return view('admin.quote.show', compact('data'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $data = QuoteRequest::findOrFail($id);
        $data->status = $request->status;
        $data->save();

        Cache::forget('admin.dashboard.v2');

        return back()->with('success', __('Quote request updated successfully.'));
    }

    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
        ]);

        $quote = QuoteRequest::findOrFail($id);
        $to = $quote->customer_email;
        $gs = $this->gs;

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return back()->with('unsuccess', __('This quote has no valid customer email.'));
        }

        $subject = $request->subject;
        $plain = $request->body;
        $body = nl2br(e($plain));

        $ok = (new GeniusMailer())->sendCustomMail([
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'type' => 'quote_reply',
        ]);

        QuoteMessage::create([
            'quote_request_id' => $quote->id,
            'direction' => 'outbound',
            'to_email' => $to,
            'from_email' => $gs->from_email,
            'subject' => $subject,
            'body' => $plain,
            'status' => $ok ? 'sent' : 'failed',
            'error' => $ok ? null : 'SMTP send failed — check Email Logs',
        ]);

        if ($ok) {
            return back()->with('success', __('Email sent to :email', ['email' => $to]));
        }

        return back()->with('unsuccess', __('Email failed to send. Check Email Logs.'));
    }

    public function destroy($id)
    {
        $data = QuoteRequest::findOrFail($id);

        if ($data->image && file_exists(public_path('assets/images/quote-requests/' . $data->image))) {
            unlink(public_path('assets/images/quote-requests/' . $data->image));
        }

        QuoteMessage::where('quote_request_id', $data->id)->delete();
        $data->delete();

        Cache::forget('admin.dashboard.v2');

        return back()->with('success', __('Quote request deleted successfully.'));
    }
}
