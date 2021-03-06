<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class PagesController extends Controller
{
    //

    public function viewHomepage(){
        return view('homepage');
    }

    public function viewStudentLogin(){
        if(Auth::check()){

            return redirect()->route('Dashboard');
        }else{

            $office = db::table('office_libraries')
                ->get();

            $department = db::table('department_libraries')
                ->get();

            $division = db::table('division_libraries')
                ->get();

            return view('Reservation.Loginpage')
                ->with(['office' => $office , 'division' => $division , 'department' => $department]);
        }
    }

    public function viewTicketingLogin(){
        if(Auth::check()){

            return redirect()->route('Dashboard');
        }else{

            $office = db::table('office_libraries')
                ->get();

            $department = db::table('department_libraries')
                ->get();

            $division = db::table('division_libraries')
                ->get();

            return view('Reservation.TicketingPage')
                ->with(['office' => $office , 'division' => $division , 'department' => $department]);
        }
    }

    public function viewDashboard(){

        $place_libraries = db::table('place_libraries')
            ->get();

        $data_list = db::table('reservation_details as res')
            ->join('users', 'res.user_id', '=' ,'users.id')
            ->leftjoin('reservation_approver_status as res_status', 'res.reservation_id', '=', 'res_status.reservation_fk_id')
            ->leftJoin('reservation_details_file as e', 'res.reservation_id', '=', 'e.reservation_fk_id')
            ->leftjoin('reservation_emo_status as emo', 'res.reservation_id', '=', 'emo.reservation_fk_id')
            ->join('place_libraries as place', 'place.id', '=', 'res.facility_id')
            ->where('emo.reservation_emo_status', 1)
            ->get();

        $others = db::table('reservation_details_others as a')
            ->select('reservation_id', 'firstname', 'lastname' , 'reservation_date' , 'reservation_status')
            ->join('reservation_details as b', 'a.reservation_fk_id', '=' , 'b.reservation_id')
            ->join('users as c' , 'b.user_id' , '='  , 'c.id')
            ->leftjoin('reservation_emo_status as emo', 'b.reservation_id', '=', 'emo.reservation_fk_id')
            ->leftjoin('reservation_others_status as d', 'a.reservation_fk_id', '=' , 'd.id_fk')
            ->where('emo.reservation_emo_status', 1)
            ->groupBy('b.reservation_id')
            ->orderBy('reservation_id', 'desc')
            ->get();

        $others2 = db::table('reservation_details as a')
            ->select('e.id as ticket_id', 'reservation_id', 'firstname', 'lastname' , 'reservation_date' , 'res_status', 'res_description')
            ->join('users as c' , 'a.user_id' , '='  , 'c.id')
            ->leftjoin('reservation_ticket_status as e', 'a.reservation_id', '=' ,'e.res_fk_id')
            ->orderBy('reservation_id', 'desc')
            ->where('res_status' , '!=', null)
            ->get();


        if(Auth::user() -> approver == 1 || Auth::user() -> approver == 2 ){
            return view('approver_dashboard')
                ->with(['place' => $place_libraries, 'data' => $data_list]);
        }elseif(Auth::user() -> approver == 3){
            return view('Ticketing.viewticketing')
                ->with(['data' => $others , 'data2' => $others2]);
        }
        else{
            return view('dashboard')
                ->with(['place' => $place_libraries, 'data' => $data_list]);
        }

    }

    public function accountLogout(){
            Auth::logout();
            return redirect()->route('Homepage');
    }


}
