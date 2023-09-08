<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User,Product,Order,Country,Notification};
use Illuminate\Http\Request;
use Validator, Auth,Storage;

class AdminController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	protected $user;
	protected $product;
	protected $order;
	
    public function __construct(User $user, Product $product, Order $order)
    {
		$this->user    = $user;
		$this->product = $product;
		$this->order   = $order;
    }
	
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
		$users = $this->user->where('role', 2)->count();
		$orders = $this->order->count();
		$revenue = $this->order->where(function ($query) {
			$query->where('status', 'succeeded')->orWhere('status', 'processing');
		})->where('payment_status','COMPLETED')->sum('amount');
		//$prevmonth = date('m', strtotime("last month"));
		// ->whereMonth('created_at', $prevmonth)
		$commission = $this->order->where(function ($query) {
			$query->where('status', 'succeeded')->orWhere('status', 'processing');
		})->where('payment_status','COMPLETED')->sum('admin_commission_value');
        return view('admin.dashboard', compact('users', 'orders', 'revenue','commission'));
    }

	public function getDashboardChartData()
	{
		$chart_labels = array();
		$chart_data = array();
		for ($i=0; $i <=  12; $i++ ){
			if($i == 0){
				array_push($chart_labels, date('M, y'));
				$data = $this->order->getMonthWiseData(date('m-Y'));
				array_push($chart_data, round($data, 2));
			}else{
				array_push($chart_labels, date('M, y', strtotime("-$i months")));
				$data = $this->order->getMonthWiseData(date('m-Y', strtotime("-$i months")));
				array_push($chart_data, round($data, 2));
			}
		}
		
		return response()->json(['type' => 'success', 'chart_labels' => array_reverse($chart_labels), 'chart_data' => array_reverse($chart_data)]);
	}
}
