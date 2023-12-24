<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed platform
 */
class AccountSocialPost extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $connection = 'main_db';
    protected $table = 'sp_accounts';
    protected $hidden = [
        'token', 'secret',
    ];
    protected $TB_ACCOUNTS = 'sp_accounts';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    
    public function get_accounts_by($list = [], $field = "ids", $status = 1, $team_id = 0){

        if($team_id == 0){
            $team_id = (int)get_team("id");
        }

        $db = DB::connection('common_database')->getPdo();

        $builder = $db->table($TB_ACCOUNTS);
        $builder->select("*");
        $builder->where('team_id', $team_id);
        $builder->whereIn($field, $list);
        $builder->where('status', $status);
        $query = $builder->get();
        $result = $query->getResult();
        $query->freeResult();
        return $result;
    }


    //sync inside table
    function sync_accounts_from_sp_to_main($table=NULL)
    {
        $sp_account=Account::whereNull('platform')->get();

        foreach ($sp_account as $account){
            $account->platform=$account->social_network;

            if($account->created_at==NULL)
            $account->created_at=$account->created;

            if($account->updated_at==NULL)
            $account->updated_at=$account->changed;

            if($account->uid==NULL)
            $account->uid=$account->pid;
            
            if($account->type==NULL)
            $account->type=$account->category;
            
            if($account->user_id==NULL)
            {
                $temp_user_id=Auth::id();
                if($temp_user_id!=1)
                $account->user_id=$temp_user_id;
                else
                {
                    $team=SPTeam::where('id',$account->team_id)->first();
                    if(isset($team->owner))
                    $account->user_id=$team->owner;
                }
            }

            if($account->secret==NULL && $account->social_network=="twitter")
            {
                
                $token=json_decode($account->token);

                if(isset($token['oauth_token_secret']))
                $account->secret=$token['oauth_token_secret'];

                if(isset($token->oauth_token_secret))
                $account->secret=$token->oauth_token_secret;

            }


            $account->save();
        }




    }

    //sync inside table in case of use backup connect social by Laravel
    function sync_accounts_from_main_to_ap()
    {
        $main_account=AccountLaravel::whereNull('social_network')->get();

        foreach ($main_account as $account){
            $account->social_network=$account->platform;

            if($account->created==NULL)
            $account->created=$account->created_at;

            if($account->changed==NULL)
            $account->changed=$account->updated_at;

            if($account->pid==NULL)
            $account->pid=$account->uid;
            
            if($account->category==NULL)
            $account->category=$account->type;
            
            if($account->team_id==NULL)
            {
                $temp_user_id=Auth::id();
                if($temp_user_id!=1)
                $account->team_id=$temp_user_id;
                else
                {
                    $team=SPTeam::where('owner',$account->user_id)->first();
                    if(isset($team->id))
                    $account->team_id=$team->id;
                }
            }

            if($account->token==NULL && $account->social_network=="twitter")
            {
                
                $secret=$account->secret;
               // $token=$account->token;
               // because $account->token ==NULL so can not use $token=$account->token

                $account->token=json_encode(array(
                    "token_type"=> "bearer",
                    "scope" => "mute.write tweet.moderate.write block.read follows.read offline.access list.write bookmark.read list.read tweet.write space.read block.write like.write like.read users.read tweet.read bookmark.write mute.read follows.write",
                    "access_token" => "",
                    "refresh_token" => "",
                    "expires" => "",
                    "oauth_token" =>"",
                    "oauth_token_secret"=>$secret,
                
                ));

            }

        }



    }


    //sync between tables
   


}
