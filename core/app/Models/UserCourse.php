<?php

namespace App\Models;


use Carbon\Carbon;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserCourse extends Authenticatable 
{
    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected static $flushCacheOnUpdate = true;
    protected $connection = 'course_db';
    protected $table = 'users';

    protected $fillable = [
        'name', 'role_id', 'username', 'email', 'phone', 'password', 'email_verified_at', 'mobile_verified_at', 'avatar', 'subscribe', 'accept_affiliate_request'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['first_name', 'last_name', 'blocked_by_me'];


    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function offlinePayments()
    {
        return $this->hasMany(OfflinePayment::class, 'user_id');
    }


    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriber::class, 'user_id', 'id')->whereDate('valid', '>=', Carbon::now());
    }


    public function enrolls()
    {
        return $this->hasManyThrough(CourseEnrolled::class, Course::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'instructor_id');
    }


    public function earnings()
    {
        return $this->hasMany(InstructorPayout::class, 'instructor_id');
    }

    public function forumReply()
    {
        return $this->hasMany(ForumReply::class, 'user_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class, 'created_by');
    }

    public function gettotalEarnAttribute()
    {

        return round($this->earnings()->sum('reveune'), 2);
    }

    public function lastMessage()
    {
        $message = Message::where('sender_id', $this->id)->orWhere('reciever_id', $this->id)->orderBy('id', 'desc')->first();
        if ($message) {
            return $message;
        } else {
            return null;
        }
    }

    public function reciever()
    {
        return $this->hasOne(Message::class, 'reciever_id')->latest();
    }


    public function sender()
    {
        return $this->hasOne(Message::class, 'sender_id')->latest();
    }

    public function getmessageFormatAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function enrollCourse()
    {
        return $this->belongsToMany(Course::class, 'course_enrolleds', 'user_id', 'course_id');
    }

    public function enCoursesInstance()
    {
        return $this->hasMany(CourseEnrolled::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function recievers()
    {
        return $this->hasMany(Message::class, 'reciever_id')->latest();
    }

    public function senders()
    {
        return $this->hasMany(Message::class, 'sender_id')->latest();
    }

    public function userLanguage()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault();
    }

    public function enrollStudents()
    {
        return $this->hasMany(CourseEnrolled::class)->EnrollStudent();
    }

    public function apiKey()
    {
        return $this->zoom_api_key_of_user;
    }

    public function apiSecret()
    {
        return $this->zoom_api_serect_of_user;
    }

    public function submittedExam()
    {
        return $this->hasOne(StudentTakeOnlineQuiz::class, 'student_id')->latest();
    }

    public function userCountry()
    {
        return $this->belongsTo(Country::class, 'country')->withDefault();
    }

    public function totalCourses()
    {
        $totalCourses = Course::where('user_id', '=', $this->id)->count();
        return $totalCourses;
    }

    public function totalEnrolled()
    {
        $totalEnrolled = Course::where('user_id', '=', $this->id)->sum('total_enrolled');
        return $totalEnrolled;
    }

    public function totalRating()
    {
        $totalRatings['total'] = 0;
        $totalRatings['rating'] = 0;
        $courses = Course::where('user_id', '=', $this->id)->get('id');
        foreach ($courses as $course) {
            $all = CourseReveiw::where('course_id', $course->id)->where('status', 1)->get();
            $totalRatings['total'] = $totalRatings['total'] + count($all);
            $ratings = 0;
            foreach ($all as $data) {
                $ratings = $data->star + $ratings;

            }
            $totalRatings['rating'] = $totalRatings['rating'] + $ratings;

        }

        if ($totalRatings['total'] != 0) {
            $avg = ($totalRatings['rating'] / $totalRatings['total']);
        } else {
            $avg = 0;
        }

        if ($avg != 0) {
            if ($avg - floor($avg) > 0) {
                $rate = number_format($avg, 1);
            } else {
                $rate = number_format($avg, 0);
            }
            $totalRatings['rating'] = $rate;
        }


        return $totalRatings;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function cityName()
    {
        $find = DB::table('spn_cities')->where('id', $this->city)->first();
        $city = '';
        if ($find) {
            $city = $find->name;
        }
        return $city;
    }

    public function totalSellCourse()
    {
        return $this->hasManyThrough(CourseEnrolled::class, Course::class, 'user_id', 'course_id', 'id');
    }

    public function totalReview()
    {
        return $this->hasManyThrough(CourseReveiw::class, Course::class, 'user_id', 'course_id', 'id');
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }

    public function position()
    {
        return $this->belongsTo(OrgPosition::class, 'org_position_code', 'code')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(OrgBranch::class, 'org_chart_code', 'code')->withDefault();
    }

    public function policy()
    {
        return $this->belongsTo(OrgPolicy::class, 'policy_id')->withDefault();
    }

    public function totalCertificate()
    {
        return $this->hasMany(CertificateRecord::class, 'student_id')->count();
    }

    public function cpds()
    {
        return $this->hasMany(AssignStudent::class, 'student_id', 'id');
    }

    public function totalStudentCourses()
    {
        $enrolls = $this->hasMany(CourseEnrolled::class, 'user_id')->with('course')->get();

        $result['complete'] = 0;
        $result['process'] = 0;
        foreach ($enrolls as $enroll) {
            if ($enroll->course->loginUserTotalPercentage >= 100) {
                $result['complete']++;
            } else {
                $result['process']++;
            }

        }
        return $result;
    }

    public function category()
    {
        $courses = $this->courses;

        $category_ids = [];
        $category = [];
        foreach ($courses as $key => $course) {
            if (!array_search($course->category_id, $category_ids)) {
                $category_ids[] = $course->category_id;
                $category[] = $course->category->name;
            }
        }
        if (count($category) != 0) {
            $result = $category[0];
        } else {
            $result = 'N/A';
        }
        return $result;
    }

    protected static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            if ($model->role_id == 2) {
                saasPlanManagement('instructor', 'create');
            }
            if ($model->role_id == 3) {
                saasPlanManagement('student', 'create');
            }
        });
        self::deleted(function ($model) {
            if ($model->role_id == 2) {
                saasPlanManagement('instructor', 'delete');
            }
            if ($model->role_id == 3) {
                saasPlanManagement('student', 'delete');
            }

        });

    }
}
