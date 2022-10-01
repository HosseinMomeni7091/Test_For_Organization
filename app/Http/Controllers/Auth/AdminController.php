<?php

namespace App\Http\Controllers\Auth;

use App\Models\Comment;
use App\Models\Discount;
use App\Models\FoodCategory;
use App\Models\RestCategory;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function restaurantCategories()
    {
        $restcategories=RestCategory::all();
        return view("admin.restcategory", compact("restcategories"));
    }
    public function foodCategories()
    {
        $foodcategories=FoodCategory::all();
        return view("admin.foodcategory", compact("foodcategories"));
    }
    public function discounts()
    {
        $discounts=Discount::all();
        return view("admin.discount", compact("discounts"));
    }
    public function adminComments()
    {
      $comments=Comment::with(["order"=>fn($order)=>$order->with("restaurant"),"user"])->where("status","delete request")->get();
      return view("admin.comments",compact("comments"));
    }
    public function actionOnComment(Request $request)
    {
      $update=Comment::where("id",$request->commentId)->update(["status"=>$request->status]);
      $comments=Comment::with(["order"=>fn($order)=>$order->with("restaurant"),"user"])->where("status","delete request")->get();
      return view("admin.comments",compact("comments"));
    }
    public function advertisements()
    {
        // return view('registerform')->with("message","Please fill the following form");

    }
}
