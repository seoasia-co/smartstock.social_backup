<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;

class Plan_Token_FixController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function team_plan_token_fix()
    {
        $team_users = User::where('team_id', '!=', 0)->get();
        foreach ($team_users as $team_user) {
            $team_user_id = $team_user->id;
            $team_user_email = $team_user->email;
            $team_user_data = $team_user->toArray();
            $team_user_data_name = 'users';
            $team_user_upFromWhere = 'main_coin';
            $this->plan_token_fix($team_user_id, $team_user_email, $team_user_data, $team_user_data_name, $team_user_upFromWhere);
        }
    }
}


