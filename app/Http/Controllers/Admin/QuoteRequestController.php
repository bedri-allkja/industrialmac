<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuoteRequest;
use Datatables;
use Illuminate\Http\Request;

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
        $data = QuoteRequest::with(['product', 'category', 'brand'])->findOrFail($id);

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

        \Illuminate\Support\Facades\Cache::forget('admin.dashboard.v2');

        return back()->with('success', __('Quote request updated successfully.'));
    }

    public function destroy($id)
    {
        $data = QuoteRequest::findOrFail($id);

        if ($data->image && file_exists(public_path('assets/images/quote-requests/' . $data->image))) {
            unlink(public_path('assets/images/quote-requests/' . $data->image));
        }

        $data->delete();

        \Illuminate\Support\Facades\Cache::forget('admin.dashboard.v2');

        return back()->with('success', __('Quote request deleted successfully.'));
    }
}
