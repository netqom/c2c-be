<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Mail\SendMail;
use Auth, Str, DB, Hash, Mail, Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

	protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	protected $appends = ['role_name','display_user_image'];

	/*public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }*/

    public function getDisplayUserImageAttribute() 
	{
		if($this->image_path != ''){
			return url('storage/'.$this->image_path);
		}
		return '';
		
	}

	/** Get Connected Account*/
	public function connect_account()
	{
		return $this->hasOne(ConnectAccount::class);
	}


	public function getRoleNameAttribute()
	{
		// $roles = config('const.user_roles');
		// if($this->role == null){
		// 	return $roles[$this->role];
		// }
		return "User";
	}
	
	/** Get User country **/
    public function country()
    {
        return $this->belongsTo(country::class);
    }
	
	/**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
	
	public function getDataTotalRecords($request)
    {  
		
	   $startDate    = isset($request['start_date']) ? $request['start_date'] : '';
	   $endDate      = isset($request['end_date']) ? $request['end_date'] : '';

	   //echo date('Y-m-d', strtotime("+1 day", strtotime($endDate)));die;

	   $search       = isset($request['query']['search_string']) ? $request['query']['search_string']:'';
	   $order_colomn = isset($request['sort']['field']) ? $request['sort']['field']:'id';
	   $order_type   = isset($request['sort']['sort']) ? $request['sort']['sort']:'desc';
	   
       return $this->when($search != '', function ($query) use ($search) {
				 return $query->where('users.name', 'like',  '%'.$search.'%');
            })
			->when(($startDate != '') && ($endDate != ''),function($query) use($startDate,$endDate){
				$query->whereBetween('created_at', [$startDate, date('Y-m-d', strtotime("+1 day", strtotime($endDate)))]);
			})
			->when($order_colomn != 'action', function ($query) use ($order_colomn, $order_type) {
                 return $query->orderBy($order_colomn, $order_type);
            })
			->where(['role' => 2])
            ->select('users.*');
    }
	
	public function createUpdateItem($request)
	{
		
		$inputs = $request->all();
		$pass =  $this->generateRandomString();
		$pasHtmlForPass = "Here's your password.<br><br>".$pass."<br><br>";
		$subject = "User Added Successfully";
		$email_verification_code = '';
		if(isset($request->password)){
			$email_verification_code = $this->generateRandomString();
			$url = env('FRONTEND_APP_URL')."/verifyemail/".$email_verification_code;
			//Use this variable to send verification link while user creating from (register)
			$pasHtmlForPass = 'To verify your acccount please click on verification link <a style="color: #0d6efd; font-size: 15px; text-decoration: none;" href='.$url.'>Verify</a><br><br>';
			$subject = "Account Created Successfully.";
			$pass = $request->password;
		}
		if($request->item_id == 0){
			$user = new User();
			$user->image_path       		= 'users/user.png';
			$user->password 				= Hash::make($pass);
			$user->email_verification_code  = $email_verification_code;
		}else{
			$user = User::find($request->item_id);
		}
		
		$user->name       = Str::title($inputs['name']);
		$user->address    = isset($inputs['address']) ? $inputs['address'] : '';
		$user->email      = $inputs['email'];
		$user->phone      = $inputs['phone'];
		$user->lat        = $inputs['lat'] ?? '';
		$user->lng        = $inputs['lng'] ?? '';
		$user->zipcode    = $inputs['zipcode'] ?? '';
		$user->country    = $inputs['country'] ?? '';
		$user->state      = $inputs['state'] ?? '';
		$user->city       = $inputs['city'] ?? '';
		$user->role       = $inputs['role'] ?? '';
		$user->status     = 1;
  		if($user->save()){

  			if($request->hasFile('image')){
	  			$file = $request->file('image');
				$extension  = $file->getClientOriginalExtension();
            	$image_name = date('mdYHis') . uniqid(). '.' .$extension;
				Storage::disk('public')->putFileAs('users/', $file, $image_name);
				$user->image_path = 'users/'.$image_name;
				$user->save();
	        }

			if($request->item_id == 0){
				$positions = config('const.user_roles');
				$body = "Dear <strong>".$inputs['name']."</strong>,<br><br>
							You have been successfully added as a ".$positions[$inputs['role']]." on platform.<br><br>
							".$pasHtmlForPass."
							Please feel free to get in touch with us for questions or queries, if any.<br><br>
						Thank you.";
				Mail::send('emails.registration', ['content' => $body], function($message) use($user,$subject){
		            $message->to($user->email);
		            $message->subject($subject);
		        });
		        $this->sendNotificationForNewUser($user);
			}
		}
		return $user;
	}

	/** Send New Notification For new User */
	public function sendNotificationForNewUser($user)
	{
		$created_by = $user->id;
		$adminUsers = User::where(['role' => 1, 'status' => 1])->get();
		if(Auth::check()){
			$created_by = Auth::id();
		}
		foreach($adminUsers as $adminUser){
			$notification = new Notification();
			$notification->user_id     = $adminUser->id;
			$notification->type        = 5;
			$notification->item_id     = $user->id;
			$notification->description = "Congratulations,".$user->name ." has joined the platform recently";
			$notification->status      = 1;
			$notification->created_by  = $created_by;
			$notification->updated_by  = $created_by;
			$notification->save();
		}
	}
	
	public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	
	public function deleteItem($id)
	{    
         $user = User::find($id);
         $user->delete();
		 return true;
	}
	
	public function changePassword($inputs)
	{
		$users = $this->find(Auth::user()->id);
		$users->password = Hash::make($inputs['password']);
		$users->save();
		return true;
	}
}
