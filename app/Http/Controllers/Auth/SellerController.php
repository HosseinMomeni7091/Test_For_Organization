<?php

namespace App\Http\Controllers\Auth;

use App\Models\Food;
use App\Models\Order;
use App\Models\Comment;
use App\Models\Restaurant;
use App\Models\FoodCategory;
use App\Models\RestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class SellerController extends Controller
{
    public function currentOrders()
    {   
        if (isset(auth()->user()->restaurant->id)){
            $result = Order::with("restaurant","user","foods")->where("restaurant_id",auth()->user()->restaurant->id)->where("status","!=","Delivered")->get();
        }else{
            $result=[];
        }
        return view("seller.currentorder",compact("result"));

    }
    public function detailCurrentOrders(Request $request)
    {   
        $result = Order::with("restaurant","user","foods")->where("id",$request->orderId)->get()->first();
        return view("seller.detailcurrentorder",compact("result"));

    }
    public function UpdateOrderStatus(Request $request)
    {   
        // Update order
        $update = Order::where("id",$request->orderId)->update(["status"=>$request->status]);
        // $update->save();

        // Get full info
        $result = Order::with("restaurant","user","foods")->where("restaurant_id",auth()->user()->restaurant->id)->where("status","!=","Delivered")->get();
        return view("seller.currentorder",compact("result"));

    }
    public function completedOrders()
    {
        if (isset(auth()->user()->restaurant->id)){
            $result = Order::with("restaurant","user","foods")->where("restaurant_id",auth()->user()->restaurant->id)->where("status","Delivered")->get();
        }else{
            $result=[];
        }
        return view("seller.completeorder",compact("result"));
    }
    public function sellReport()
    {
        if (isset(auth()->user()->restaurant->id)){
            $orders = Order::whereBelongsTo(auth()->user()->restaurant)->whereIn("status",["Delivering","Delivered"])->get();
            // Price and Discount for each order
            $TotalPrice=array_fill(0,count($orders),0);
            $TotalOrderPrice=array_fill(0,count($orders),0);
            $TotalOrderQuantity=array_fill(0,count($orders),0);
            $OrderDiscount=array_fill(0,count($orders),0);
            foreach ($orders as $key=>$order) {
                foreach ($order->foods as $food) {
                    $TotalPrice[$key]+=($food->price)*($food->pivot->count);
                    $OrderDiscount[$key]+=($food->price)*($food->pivot->count)*(($food->discount)/100);
                }
                $TotalOrderPrice[$key]=(($order->discount ?? 100)/100)*(($TotalPrice[$key])-$OrderDiscount[$key]);
                $TotalOrderQuantity[$key]+=$order->quantity;
            }

            // Accumulative values 
            $TotalPriceForAllOrder=array_sum($TotalPrice);
            $TotalDiscountForAllOrder=array_sum($OrderDiscount);
            $TotalPriceForAllOrderAfterDiscount=array_sum($TotalOrderPrice);
            $TotalAllOrderQuantity=array_sum($TotalOrderQuantity);

            $restId=auth()->user()->restaurant->id;
            $chart_options = [
                'chart_title' => 'Sell report per day',
                'report_type' => 'group_by_date',
                'model' => 'App\Models\Order',
                'conditions'=> [['name' => 'test', 'condition' => "restaurant_id = $restId", 'color' => 'red', 'fill' => true]],
                // 'group_by_field' => 'cost',
                'group_by_field' => 'created_at',
                'group_by_period' => 'day',
                'aggregate_function' => 'sum',
                'aggregate_field' => 'cost',
                // 'group_by_period' => 'day',
                'chart_type' => 'line',
            ];
            $chart1 = new LaravelChart($chart_options);
            $status=1;
            return view("seller.report",compact("orders","TotalOrderPrice","OrderDiscount","TotalPrice","TotalPriceForAllOrder","TotalDiscountForAllOrder","TotalPriceForAllOrderAfterDiscount","TotalAllOrderQuantity","chart1","status"));
        }else{
            $orders=[];
            $TotalOrderPrice=0;
            $OrderDiscount=0;
            $TotalPrice=0;
            $TotalPriceForAllOrder=0;
            $TotalDiscountForAllOrder=0;
            $TotalPriceForAllOrderAfterDiscount=0;
            $TotalAllOrderQuantity=0;
            $status=false;
            $chart1=[];
            return view("seller.report",compact("orders","TotalOrderPrice","OrderDiscount","TotalPrice","TotalPriceForAllOrder","TotalDiscountForAllOrder","TotalPriceForAllOrderAfterDiscount","TotalAllOrderQuantity","status","chart1"));
        }
        
        

        
    }
    public function filterOnReport(Request $request)
    {
        if ($request->filter=="all"){
            $orders = Order::whereBelongsTo(auth()->user()->restaurant)->whereIn("status",["Delivering","Delivered"])->get();
            $filterday=700;
            $filter="All";
        }
        if ($request->filter=="lastWeek"){
            $orders = Order::whereBelongsTo(auth()->user()->restaurant)->whereIn("status",["Delivering","Delivered"])->where("created_at",">=",date("Y-m-d H:i:s", time()- 10080 * 60))->get();
            $filterday=7;
            $filter="Last Week";

        }
        if ($request->filter=="lastMonth"){
            $orders = Order::whereBelongsTo(auth()->user()->restaurant)->whereIn("status",["Delivering","Delivered"])->where("created_at",">=",date("Y-m-d H:i:s", time() - 43200 * 60))->get();
            $filterday=30;
            $filter="Last Month";
        }

        // Price and Discount for each order
        $TotalPrice=array_fill(0,count($orders),0);
        $TotalOrderPrice=array_fill(0,count($orders),0);
        $TotalOrderQuantity=array_fill(0,count($orders),0);
        $OrderDiscount=array_fill(0,count($orders),0);
        foreach ($orders as $key=>$order) {
            foreach ($order->foods as $food) {
                $TotalPrice[$key]+=($food->price)*($food->pivot->count);
                $OrderDiscount[$key]+=($food->price)*($food->pivot->count)*(($food->discount)/100);
            }
            $TotalOrderPrice[$key]=(($order->discount ?? 100)/100)*(($TotalPrice[$key])-$OrderDiscount[$key]);
            $TotalOrderQuantity[$key]+=$order->quantity;
        }

        // Accumulative values 
        $TotalPriceForAllOrder=array_sum($TotalPrice);
        $TotalDiscountForAllOrder=array_sum($OrderDiscount);
        $TotalPriceForAllOrderAfterDiscount=array_sum($TotalOrderPrice);
        $TotalAllOrderQuantity=array_sum($TotalOrderQuantity);

        $restId=auth()->user()->restaurant->id;
        $chart_options = [
            'chart_title' => 'Sell report per day',
            'report_type' => 'group_by_date',
            'model' => 'App\Models\Order',
            'conditions'=> [['name' => 'test', 'condition' => "restaurant_id = $restId", 'color' => 'red', 'fill' => true]],
            // 'group_by_field' => 'cost',
            'group_by_field' => 'created_at',
            'group_by_period' => 'day',
            'aggregate_function' => 'sum',
            'aggregate_field' => 'cost',
            'chart_type' => 'line',
            'filter_field' => 'created_at',
            'filter_days' => $filterday, // show only transactions for last 30 
            'continuous_time' => true, // show continuous timeline including dates without data
        ];
        $chart1 = new LaravelChart($chart_options);
        $status=1;
        return view("seller.report",compact("orders","TotalOrderPrice","OrderDiscount","TotalPrice","TotalPriceForAllOrder","TotalDiscountForAllOrder","TotalPriceForAllOrderAfterDiscount","TotalAllOrderQuantity","chart1","filter","status"));
    }
    public function foods(Request $request)
    {
        $categories = FoodCategory::select(["name", "id"])->get();
        if (isset(auth()->user()->restaurant->id)){
            if ($request->only("namesearch") != []) {
                $word="%".$request->only('namesearch')['namesearch']."%";
                $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->where("name","like", $word)->get();
                $namesearch=$request->only("namesearch")["namesearch"];
                return view("seller.fooddashboard", compact("foodinfos", "categories","namesearch"));
            }
            if ($request->only("categoryfilter") != []) {
    
                if ($request->only("categoryfilter")["categoryfilter"] == "all") {
                    $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->get();
                    $categoryfilter=$request->only("categoryfilter")["categoryfilter"];
                    return view("seller.fooddashboard", compact("foodinfos", "categories","categoryfilter"));
                } else {
                    $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->where("food_category_id", $request->only("categoryfilter")["categoryfilter"])
                        ->get();
                    return view("seller.fooddashboard", compact("foodinfos", "categories"));
                }
            }
            $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->get();
        }else{
            $foodinfos=[];
        }
        
        return view("seller.fooddashboard", compact("foodinfos", "categories"));
    }
    public function sellerComments()
    {
        if(isset(auth()->user()->restaurant->id)){
            $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->get();
            $result=Order::with("foods")->whereBelongsTo(auth()->user()->restaurant)->has('comments')->get();
        }else{
            $result=[];
            $foodinfos=[];
        }
        return view("seller.comments",compact("result","foodinfos"));
    }
    public function searchCommentOfFood(Request $request)
    {
        $foodId=$request->foodidfilter;
        $foodinfos = Food::whereBelongsTo(auth()->user()->restaurant)->get();
        $result=Order::whereBelongsTo(auth()->user()->restaurant)->whereRelation("foods","food_id",$foodId)->has('comments')->get();
        if(($request->foodidfilter)=="all"){
            $result=Order::whereBelongsTo(auth()->user()->restaurant)->has('comments')->get();
        }
        return view("seller.comments",compact("result","foodinfos"));
    }
    public function comments(Request $request)
    {
        $comments=Comment::where('order_id',$request->orderId)->get();
        $orderId=$request->orderId;
        // dd($comments);
        return view("seller.detailcomments",compact("comments","orderId"));
    }
    public function updateCommentStatus(Request $request)
    {
        $update=Comment::where("id",$request->comment_id)->update([
            "status"=>$request->status
        ]);
        $orderId=$request->orderId;
        $comments=Comment::where('order_id',$request->orderId)->get();
        return view("seller.detailcomments",compact("comments","orderId"));
    }
    public function sendCommentReply(Request $request)
    {
        $update=Comment::where("id",$request->comment_id)->update([
            "reply"=>$request->reply
        ]);
        $orderId=$request->orderId;
        $comments=Comment::where('order_id',$request->orderId)->get();
        return view("seller.detailcomments",compact("comments","orderId"));
    }
    public function foodParty()
    {
        // return view('registerform')->with("message","Please fill the following form");
    }
    public function updateconfiguration()
    {
        $restCategory=RestCategory::all();
        $restinfo = Restaurant::with("restaddress","restcategory","schedule")->where("user_id", auth()->user()->id)->get()->first();
        return view("seller.updatesellerconfig", compact("restinfo","restCategory"));
    }
    public function configuration()
    {
        $restCategory=RestCategory::all();
        $result = Restaurant::with("restaddress","restcategory","schedule")->where("user_id", auth()->user()->id)->get()->first();
        if ($result == null) {
            $status = false;
        } else {
            $status = true;
        }
        return view("seller.sellerconfig", compact("status","restCategory"));
    }
    public function editefood(Request $request)
    {
        $food = Food::find($request->only("editefood")["editefood"]);
        // dd($request->only("editefood")["editefood"],$food);
        return view("seller.foodedite", compact("food"));
    }
    public function createfood()
    {
        $categories = FoodCategory::select(["name", "id"])->get();
        return view("seller.addfood", compact("categories"));
    }
}
