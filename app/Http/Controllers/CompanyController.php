<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\FavouriteCompany;
use App\User;
use Auth;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $company = Company::get();
        return response()->json([
            'success' => true,
            'message' => 'Company List!',
            'data'    => $company
        ], 200);
    }

    //API Search
    public function findCompanyByName(Request $request){
        //get company name as param
        $name = $request->query('name');
        //select fav company id
        $fav = FavouriteCompany::select('company_id')
                ->where('user_id', '=', Auth::user()->id)
                ->get();
        //query to select company that contain param
        $company = Company::where('company_name', 'like', '%'.$name.'%')
                        // ->whereNotIn('id', $fav)
                        ->get();
        $favIdCompany = [];
        foreach ($fav as $key => $value) {
            $favIdCompany[] = $value->company_id; 
        }
        foreach ($company as $com) {
            if(in_array($com->id, $favIdCompany)){
                $com->favourite = true;
            }
            else{
                $com->favourite = false;
            }
        }
        // $company = Company::leftJoin('favourite_companies', 'favourite_companies.company_id', '=', 'companies.id')
        //                 ->select('companies.company_name as companyName', 'companies.id as companyId', 'favourite_companies.id as favId', 'favourite_companies.user')
        //                 ->where('company_name', 'like', '%'.$name.'%')
        //                 //->groupBy('company_name')
        //                 //->whereNotIn('id', $fav)
        //                 ->get();
        if (count($company) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Company!',
                'data'    => $company
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Company not found!',
                'data'    => ''
            ], 404);
        }
    }
     
    public function markCompany($companyId){
        $favourite = FavouriteCompany::create([
            'user_id'       => Auth::user()->id,
            'company_id'    => $companyId
        ]);
        if($favourite){
            return response()->json([
                'success'   => true,
                'message'   => 'Data has been saved',
                'data'      => $favourite
            ]);
        }
        else{
            return response()->json([
                'success'   => false,
                'message'   => 'Not saved',
                'status'    => 400,
            ]);
        }
    }

    public function unmarkCompany($companyId){
        $favourite = FavouriteCompany::where('user_id','=',Auth::user()->id)
                                    ->where('company_id','=',$companyId)    
                                    ->delete();
        if($favourite){
            return response()->json([
                'success'   => true,
                'message'   => 'Data has been deleted'
            ]);
        }
        else{
            return response()->json([
                'success'   => false,
                'message'   => 'Not saved',
                'status'    => 400,
            ]);
        }
    }

    public function favouriteCompany(){
        $data = User::find(11)->companies()->get();
        //var_dump($data->get());
        if(count($data) > 0){
            return response()->json([
                'success'   => true,
                'message'   => 'Company list',
                'data'      => $data
            ]);
        }
        else{
            return response()->json([
                'success'   => true,
                'message'   => 'Don\'t have any favourite company',
            ]);
        }
        
    }
}