<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator, Storage, Str;
use Illuminate\Support\Facades\{Lang}; 
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use PDF;

class OrdersController extends Controller
{
	
	
    public $order;
    public $product;
    public $category;
    public $productcategory;
    public $productimage;
    public function __construct(Order $order, Product $product, Category $category, ProductCategory $productcategory, ProductImage $productimage){
        $this->order           = $order;
        $this->product         = $product;
        $this->category        = $category;
        $this->productcategory = $productcategory;
        $this->productimage    = $productimage;
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.order.list');
    }
	
	public function getData(Request $request)
	{
		$ordersType = config('const.order_types');
		if(!$request->filled('order_types')){
         $request->merge(['order_types' => $ordersType[1]]);
        }
		$queryObj = $this->order->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
        $data['total_transaction'] = $this->order->whereNull('refund_id')->whereBetween('created_at', [$request->start_date, date('Y-m-d', strtotime("+1 day", strtotime($request->end_date)))])->sum('amount');
        $data['total_revenue'] = $this->order->whereNull('refund_id')->whereBetween('created_at', [$request->start_date, date('Y-m-d', strtotime("+1 day", strtotime($request->end_date)))])->sum('admin_commission_value');
		return response()->json($data,200);
	}
	
	public function getOrderDetail($id)
	{
		$data = $this->order->find($id);
        return view('admin.order.detail', compact('data', 'id'));
	}

    public function destroy($id)
    {
		$result = $this->product->deleteItem($id);
        if ($result) {
			return response()->json(['type' => 'success', 'msg' => Lang::get('auth.productRemove')]);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError') ]);
        }
    }
    public function exportOrder($id){
        $data = $this->order->find($id);
        $pdf = PDF::loadView('admin.order.detail-template', compact('data'))->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $pdf_name = 'order-detail.pdf';
        return $pdf->download($pdf_name);
    }
}