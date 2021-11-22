<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Mail\Verification;
use App\Models\Cuisine;
use App\Models\Faq;
use App\Models\GeneralSetting;
use App\Models\Menu;
use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Models\OrderChild;
use App\Models\Role;
use App\Models\Settle;
use App\Models\Submenu;
use App\Models\DeliveryPerson;
use App\Models\SubmenuCusomizationType;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Vendor;
use App\Models\VendorBankDetail;
use App\Models\VendorDiscount;
use App\Models\WorkingHours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mail;
use Twilio\Rest\Client;
use OneSignal;
use App\Mail\StatusChange;
use App\Models\DeliveryZoneArea;
use App\Models\Notification;
use DB;
use Config;

class VendorApiController extends Controller
{
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email_id' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        $user = ([
            'email_id' => $request->email_id,
            'password' => $request->password,
            'status' => 1,
        ]);

        if(Auth::attempt($user))
        {
            $user = Auth::user();
            if ($user->roles->contains('title', 'vendor'))
            {
                if (isset($request->device_token)) {
                    $user->device_token = $request->device_token;
                    $user->save();
                }
                if($user['is_verified'] == 1)
                {
                    $user['token'] =  $user->createToken('mealup')->accessToken;
                    return response()->json(['success' => true , 'data' => $user], 200);
                }
                else
                {
                    $admin_verify_user = GeneralSetting::find(1)->verification;
                    if($admin_verify_user == 1)
                    {
                        $otp = mt_rand(1000, 9999);
                        // $otp = 1234;

                        $sms_verification = GeneralSetting::first()->verification_phone;
                        $mail_verification = GeneralSetting::first()->verification_email;

                        $verification_content = NotificationTemplate::where('title','verification')->first();

                        $msg_content = $verification_content->notification_content;
                        $mail_content = $verification_content->mail_content;

                        $sid = GeneralSetting::first()->twilio_acc_id;
                        $token = GeneralSetting::first()->twilio_auth_token;

                        $detail['otp'] = $otp;
                        $detail['user_name'] = $user->name;
                        $detail['app_name'] = GeneralSetting::first()->business_name;
                        $data = ["{otp}", "{user_name}"];

                        $user->otp = $otp;
                        $user->save();
                        if($mail_verification == 1)
                        {
                            $message1 = str_replace($data, $detail, $mail_content);
                            try
                            {
                                Mail::to($user->email_id)->send(new Verification($message1));
                            }
                            catch (\Throwable $th)
                            {
                                //throw $th;
                            }
                        }
                        if($sms_verification == 1)
                        {
                            try
                            {
                                $phone = $user->phone_code . $user->phone;
                                $message1 = str_replace($data, $detail, $msg_content);
                                $client = new Client($sid, $token);
                                $client->messages->create(
                                    $phone,
                                    array(
                                        'from' => GeneralSetting::first()->twilio_phone_no,
                                        'body' => $message1
                                    )
                                );
                            }
                            catch (\Throwable $th) {}
                        }
                        return response(['success' => true ,'data' => $user, 'msg' => 'Otp send in your account']);
                    }
                }
            }
            else
            {
                return response(['success' => false , 'msg' => 'only vendor can login...']);
            }
        }
        else
        {
            return response()->json(['success' => false ,'message'=>'Email and password wrong..!!'], 401);
        }
    }

    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'bail|required',
            'email_id' => 'bail|required|unique:users',
            'password' => 'bail|min:6',
            'phone' => 'bail|required|numeric|digits_between:6,12',
            'vendor_own_driver' => 'bail|required',
            'phone_code' => 'bail|required',
        ]);
        $admin_verify_user = GeneralSetting::find(1)->verification;
        $veri = $admin_verify_user == 1 ? 0 : 1;
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 1;
        $data['image'] = 'noimage.jpg';
        $data['is_verified'] = $veri;
        $user = User::create($data);
        if (isset($request->device_token)) {
            $user->device_token = $request->device_token;
            $user->save();
        }
        $role_id = Role::where('title','vendor')->orWhere('title','Vendor')->first();
        $user->roles()->sync($role_id);
        $cusinies = Cuisine::whereStatus(1)->get('id');
        $tempIds = [];
        foreach ($cusinies as $value) {
            array_push($tempIds,$value->id);
        }
        $cuisine_id = implode(',',$tempIds);
        $vendor = Vendor::create([
            'name' => $request->name,
            'admin_comission_type' => 'percentage',
            'user_id' => $user->id,
            'email_id' => $user->email_id,
            'image' => 'noimage.png',
            'admin_comission_value' => '33',
            'contact' => $user->phone,
            'status' => 1,
            'password' => Hash::make($data['password']),
            'isExplorer' => 1,
            'vendor_type' => 'veg',
            'isTop' => 1,
            'vendor_logo' => 'vendor-logo.png',
            'password' => Hash::make($data['password']),
            'vendor_own_driver' => $request->vendor_own_driver,
            'time_slot' => '15',
            'vendor_language' => 'english',
            'cuisine_id' => $cuisine_id,
            'lat' => '22.3039',
            'lang' => '70.8022',
            'address' => 'rajkot , gujrat',
        ]);


        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        for($i = 0; $i < count($days); $i++)
        {
            $master = array();
            $temp['start_time'] = '8:00 am';
            $temp['end_time'] = '9:00 pm';
            array_push($master,$temp);
            $delivery_time['vendor_id'] = $vendor->id;
            $delivery_time['period_list'] = json_encode($master);
            $delivery_time['type'] = 'delivery_time';
            $delivery_time['day_index'] = $days[$i];
            $delivery_time['status'] = 1;
            WorkingHours::create($delivery_time);
        }

        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        for($i = 0; $i < count($days); $i++)
        {
            $master = array();
            $temp['start_time'] = '8:00 am';
            $temp['end_time'] = '9:00 pm';
            array_push($master,$temp);
            $pickup['vendor_id'] = $vendor->id;
            $pickup['period_list'] = json_encode($master);
            $pickup['type'] = 'pick_up_time';
            $pickup['day_index'] = $days[$i];
            $pickup['status'] = 1;
            WorkingHours::create($pickup);
        }

        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        for($i = 0; $i < count($days); $i++)
        {
            $master = array();
            $temp['start_time'] = '8:00 am';
            $temp['end_time'] = '9:00 pm';
            array_push($master,$temp);
            $selling_time['vendor_id'] = $vendor->id;
            $selling_time['period_list'] = json_encode($master);
            $selling_time['type'] = 'selling_timeslot';
            $selling_time['day_index'] = $days[$i];
            $selling_time['status'] = 1;
            WorkingHours::create($selling_time);
        }

        if($user['is_verified'] == 1)
        {
            $user['token'] =  $user->createToken('mealup')->accessToken;
            return response()->json(['success' => true , 'data' => $user , 'msg' => 'account created successfully..!!'], 200);
        }
        else
        {
            $admin_verify_user = GeneralSetting::find(1)->verification;
            if($admin_verify_user == 1)
            {
                // $otp = 1234;
                $otp = mt_rand(1000, 9999);

                $sms_verification = GeneralSetting::first()->verification_phone;
                $mail_verification = GeneralSetting::first()->verification_email;

                $verification_content = NotificationTemplate::where('title','verification')->first();

                $msg_content = $verification_content->notification_content;
                $mail_content = $verification_content->mail_content;

                $sid = GeneralSetting::first()->twilio_acc_id;
                $token = GeneralSetting::first()->twilio_auth_token;

                $detail['otp'] = $otp;
                $detail['user_name'] = $user->name;
                $detail['app_name'] = GeneralSetting::first()->business_name;
                $data = ["{otp}", "{user_name}"];

                $user->otp = $otp;
                $user->save();
                if($mail_verification == 1)
                {
                    $message1 = str_replace($data, $detail, $mail_content);
                    try
                    {
                        Mail::to($user->email_id)->send(new Verification($message1));
                    }
                    catch (\Throwable $th)
                    {
                        //throw $th;
                    }
                }
                if($sms_verification == 1)
                {
                    try
                    {
                        $phone = $user->phone_code . $user->phone;
                        $message1 = str_replace($data, $detail, $msg_content);
                        $client = new Client($sid, $token);
                        $client->messages->create(
                            $phone,
                            array(
                                'from' => GeneralSetting::first()->twilio_phone_no,
                                'body' => $message1
                            )
                        );
                    }
                    catch (\Throwable $th) {}
                }
                return response(['success' => true ,'data' => $user, 'msg' => 'your account created successfully please verify your account']);
            }
            $user['token'] =  $user->createToken('mealup')->accessToken;
            return response()->json(['success' => true , 'data' => $user , 'msg' => 'account created successfully..!!'], 200);
        }
    }

    public function apiCheckOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'bail|required',
            'otp' => 'bail|required|min:4',
        ]);

        $user = User::find($request->user_id);
        if($user)
        {
            if($user->otp == $request->otp)
            {
                $user->is_verified = 1;
                $user->save();
                $user['token'] = $user->createToken('mealup')->accessToken;
                return response(['success' => true ,'data' => $user ,'msg' => 'SuccessFully verify your account...!!']);
            }
            else
            {
                return response(['success' => false , 'msg' => 'Something went wrong otp does not match..!']);
            }
        }
        else
        {
            return response(['success' => false , 'msg' => 'Oops...user not found..!!']);
        }
    }

    public function apiForgotPassword(Request $request)
    {
        $request->validate([
            'email_id' => 'required_without:phone',
            'phone' => 'required_without:email_id',
        ]);

        $user = User::where('email_id',$request->email_id)->orWhere('phone',$request->phone)->first();
        $password = mt_rand(100000, 999999);
        if($user)
        {
            $passwordTemplate = NotificationTemplate::where('title','forgot password')->first();
            $mail_content = $passwordTemplate->mail_content;
            $detail['password'] = $password;
            $detail['user_name'] = $user->name;
            $data = ["{password}", "{user_name}"];
            $user->password = Hash::make($password);
            $user->save();
            $message1 = str_replace($data, $detail, $mail_content);
            try
            {
                Mail::to($user->email_id)->send(new ForgotPassword($message1));
            }
            catch (\Throwable $th)
            {

            }
                return response(['success' => true ,'data' => $user ,'msg' => 'your password send into your email']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Oops...user not found..!!']);
        }
    }

    public function apiChangePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'bail|required|min:6',
            'password' => 'bail|required|min:6',
            'password_confirmation' => 'bail|required|min:6',
        ]);
        $data = $request->all();
        $id = auth()->user();
        if(Hash::check($data['old_password'], $id->password) == true)
        {
            if($data['password'] == $data['password_confirmation'])
            {
                $id->password = Hash::make($data['password']);
                $id->save();
                return response(['success' => true , 'data' => 'Password Update Successfully...!!']);
            }
            else
            {
                return response(['success' => false , 'data' => 'password and confirm password does not match']);
            }
        }
        else
        {
            return response(['success' => false , 'data' => 'Old password does not match.']);
        }
    }

    public function apiResendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'bail|required',
        ]);
        $user = User::find($request->user_id);
        if($user){
            $admin_verify_user = GeneralSetting::find(1)->verification;
            if($admin_verify_user == 1)
            {
                // $otp = 1234;
                $otp = mt_rand(1000, 9999);

                $sms_verification = GeneralSetting::first()->verification_phone;
                $mail_verification = GeneralSetting::first()->verification_email;

                $verification_content = NotificationTemplate::where('title','verification')->first();

                $msg_content = $verification_content->notification_content;
                $mail_content = $verification_content->mail_content;

                $sid = GeneralSetting::first()->twilio_acc_id;
                $token = GeneralSetting::first()->twilio_auth_token;

                $detail['otp'] = $otp;
                $detail['user_name'] = $user->name;
                $detail['app_name'] = GeneralSetting::first()->business_name;
                $data = ["{otp}", "{user_name}"];

                $user->otp = $otp;
                $user->save();
                if($mail_verification == 1)
                {
                    $message1 = str_replace($data, $detail, $mail_content);
                    try
                    {
                        Mail::to($user->email_id)->send(new Verification($message1));
                    }
                    catch (\Throwable $th)
                    {
                        //throw $th;
                    }
                }
                if($sms_verification == 1)
                {
                    try
                    {
                        $phone = $user->phone_code . $user->phone;
                        $message1 = str_replace($data, $detail, $msg_content);
                        $client = new Client($sid, $token);
                        $client->messages->create(
                            $phone,
                            array(
                                'from' => GeneralSetting::first()->twilio_phone_no,
                                'body' => $message1
                            )
                        );
                    }
                    catch (\Throwable $th) {}
                }
                return response(['success' => true ,'data' => $user, 'msg' => 'Verification Code Sent.']);
            }
        }
        else
        {
            return response(['success' => false , 'msg' => 'User Not Found.']);
        }
    }

    /* Menu */
    public function apiMenu()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $menu = Menu::where('vendor_id',$vendor->id)->orderBy('id','desc')->get()->makeHidden(['created_at','updated_at','menu_category_id','vendor_id']);
        return response(['success' => true , 'data' => $menu]);
    }

    public function apiCreateMenu(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // 'image' => 'required',
            'status' => 'required',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $data['vendor_id'] = $vendor->id;
        if(isset($request->image))
        {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $Iname = uniqid();
            $file = public_path('/images/upload/') . $Iname . ".png";
            $success = file_put_contents($file, $data1);
            $data['image'] = $Iname . ".png";
        }
        else
        {
            $data['image'] = 'product_default.jpg';
        }
        Menu::create($data);
        return response(['success' => true , 'data' => 'Menu Cretaed successfully...!!']);
    }

    public function apiEditMenu($menu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $menu = Menu::where([['vendor_id',$vendor->id],['id',$menu_id]])->first()->makeHidden(['created_at','updated_at','menu_category_id','vendor_id']);;
        if($menu)
        {
            return response(['success' => true , 'data' => $menu]);
        }
        return response(['success' => false , 'data' => 'No menu Found']);
    }

    public function apiUpdateMenu(Request $request,$menu_id)
    {
        $request->validate([
            'name' => 'required',
            // 'image' => 'required',
            'status' => 'required',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $menu = Menu::find($menu_id);
        if($menu)
        {
            $data['vendor_id'] = $vendor->id;
            if(isset($request->image))
            {
                $img = $request->image;
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data1 = base64_decode($img);
                $Iname = uniqid();
                $file = public_path('/images/upload/') . $Iname . ".png";
                $success = file_put_contents($file, $data1);
                $data['image'] = $Iname . ".png";
            }
            else
            {
                $data['image'] = 'product_default.jpg';
            }
            $menu->update($data);
            return response(['success' => true , 'data' => 'Menu updated successfully...!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Oops Menu not found..!!']);
        }
    }

    public function apiSingleMenu($menu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $menu = Menu::where([['id',$menu_id],['vendor_id',$vendor->id]])->first();
        if($menu)
        {
            return response(['success' => true , 'data' => $menu]);
        }
        else
        {
            return response(['success' => true , 'data' => 'Oopss menu not found']);
        }
    }

    /** Submenu */
    public function apiSubmenu($menu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $submenu = Submenu::where([['vendor_id',$vendor->id],['menu_id',$menu_id]])->orderBy('id','desc')->get();
        return response(['success' => true , 'data' => $submenu]);
    }

    public function apiCreateSubmenu(Request $request)
    {
        $request->validate([
            'menu_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'qty_reset' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'item_reset_value' => 'required_if:qty_reset,daily',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $data['vendor_id'] = $vendor->id;
        if(isset($request->image))
        {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $Iname = uniqid();
            $file = public_path('/images/upload/') . $Iname . ".png";
            $success = file_put_contents($file, $data1);
            $data['image'] = $Iname . ".png";
        }
        else
        {
            $data['image'] = 'product_default.jpg';
        }
        Submenu::create($data);
        return response(['success' => true , 'data' => 'Submenu Cretaed successfully...!!']);
    }

    public function apiEditSubmenu($submenu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $submenu_id = Submenu::where([['vendor_id',$vendor->id],['id',$submenu_id]])->first()->makeHidden(['created_at','updated_at']);
        if($submenu_id)
        {
            return response(['success' => true , 'data' => $submenu_id]);
        }
        return response(['success' => false , 'data' => 'oops no submenu found']);
    }

    public function apiUpdateSubmenu(Request $request,$submenu_id)
    {
        $request->validate([
            'menu_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'qty_reset' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'item_reset_value' => 'required_if:qty_reset,daily',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $submenu = Submenu::find($submenu_id);
        if($submenu)
        {
            $data['vendor_id'] = $vendor->id;
            if(isset($request->image))
            {
                $img = $request->image;
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data1 = base64_decode($img);
                $Iname = uniqid();
                $file = public_path('/images/upload/') . $Iname . ".png";
                $success = file_put_contents($file, $data1);
                $data['image'] = $Iname . ".png";
            }
            $submenu->update($data);
            return response(['success' => true , 'data' => 'Submenu updated successfully...!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Oops Submenu not found..!!']);
        }
    }

    public function apiSingleSubmenu($submenu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $submenu = Submenu::where([['id',$submenu_id],['vendor_id',$vendor->id]])->first()->makeHidden(['created_at','updated_at']);
        if($submenu)
        {
            return response(['success' => true , 'data' => $submenu]);
        }
        return response(['success' => false , 'data' => 'Oopss menu not found']);
    }

    /*** Custimization */
    public function apiCustimization($submenu_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $custimization = SubmenuCusomizationType::where([['vendor_id',$vendor->id],['submenu_id',$submenu_id]])->orderBy('id','desc')->get()->makeHidden(['created_at','updated_at']);
        return response(['success' => true , 'data' => $custimization]);

    }

    public function apiCreateCustimization(Request $request)
    {
        $request->validate([
            'menu_id' => 'required',
            'submenu_id' => 'required',
            'name' => 'required',
            'custimazation_item' => 'required',
            'type' => 'required',
            'min_item_selection' => 'required',
            'max_item_selection' => 'required',
        ]);
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $data['vendor_id'] = $vendor->id;
        SubmenuCusomizationType::create($data);
        return response(['success' => true , 'data' => 'Custimization Cretaed successfully...!!']);
    }

    public function apiEditCustimization($custimization_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $custimization_id = SubmenuCusomizationType::where([['vendor_id',$vendor->id],['id',$custimization_id]])->first()->makeHidden(['created_at','updated_at']);
        if($custimization_id)
        {
            return response(['success' => true , 'data' => $custimization_id]);
        }
        return response(['success' => false , 'data' => 'oops no Custimization found']);
    }

    public function apiUpdateCustimization(Request $request,$custimization_id)
    {
        $request->validate([
            'menu_id' => 'required',
            'submenu_id' => 'required',
            'name' => 'required',
            'custimazation_item' => 'required',
            'type' => 'required',
            'min_item_selection' => 'required',
            'max_item_selection' => 'required',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $custimization = SubmenuCusomizationType::find($custimization_id);
        if($custimization)
        {
            $data['vendor_id'] = $vendor->id;
            $custimization->update($data);
            return response(['success' => true , 'data' => 'Custimization updated successfully...!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Oops Custimization not found..!!']);
        }
    }

    public function apiDeleteCustimization($custimization_id)
    {
        // $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $custimization = SubmenuCusomizationType::find($custimization_id);
        if($custimization)
        {
            $custimization->delete();
            return response(['success' => true , 'data' => 'record deleted successfully..!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Not Found..!']);
        }
    }

    /** Delivery Timeslot */
    public function apiEditDeliveryTimeslot()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $deliveryTimeslot = WorkingHours::where([['type','delivery_time'],['vendor_id',$vendor->id]])->get()->makeHidden(['created_at','updated_at','type']);
        return response(['success' => true , 'data' => $deliveryTimeslot]);
    }

    public function apiUpdateDeliveryTimeslot(Request $request)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $days = WorkingHours::find($data['id']);
        $days->update($data);
        return response(['success' => true , 'data' => 'Update successfully..!!']);
    }

    /** Pickup Timeslot */
    public function apiEditPickUpTimeslot()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $pickupTimeslot = WorkingHours::where([['type','pick_up_time'],['vendor_id',$vendor->id]])->get();
        return response(['success' => true , 'data' => $pickupTimeslot]);
    }

    public function apiUpdatePickUpTimeslot(Request $request)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $data = $request->all();
        $days = WorkingHours::find($data['id']);
        $days->update($data);
        return response(['success' => true , 'data' => 'Update successfully..!!']);
    }

    /** Selling timeslot */
    public function apiEditSellingTimeslot()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $pickupTimeslot = WorkingHours::where([['type','selling_timeslot'],['vendor_id',$vendor->id]])->get();
        return response(['success' => true , 'data' => $pickupTimeslot]);
    }

    public function apiUpdateSellingTimeslot(Request $request)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $data = $request->all();
        $days = WorkingHours::find($data['id']);
        $days->update($data);
        return response(['success' => true , 'data' => 'Update successfully..!!']);
    }

    /** Vendor discount */
    public function apiDiscount()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $custimization = VendorDiscount::where('vendor_id',$vendor->id)->orderBy('id','desc')->get()->makeHidden(['created_at','updated_at']);
        return response(['success' => true , 'data' => $custimization]);
    }

    public function apiCreateDiscount(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'discount' => 'required',
            'min_item_amount' => 'required',
            'max_discount_amount' => 'required',
            'start_end_date' => 'required',
            'description' => 'required',
        ]);

        $data = $request->all();
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        if(isset($request->image))
        {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $Iname = uniqid();
            $file = public_path('/images/upload/') . $Iname . ".png";
            $success = file_put_contents($file, $data1);
            $data['image'] = $Iname . ".png";
        }
        else
        {
            $data['image'] = 'product_default.jpg';
        }
        $data['vendor_id'] = $vendor->id;
        VendorDiscount::create($data);
        return response(['success' => true , 'data' => 'Discount Cretaed successfully...!!']);
    }

    public function apiEditDiscount($discount_id)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $discount = VendorDiscount::where([['vendor_id',$vendor->id],['id',$discount_id]])->first()->makeHidden(['created_at','updated_at']);
        if($discount)
        {
            return response(['success' => true , 'data' => $discount]);
        }
        return response(['success' => false , 'data' => 'oops no Discount found']);
    }

    public function apiUpdateDiscount(Request $request,$discount_id)
    {
        $request->validate([
            'type' => 'required',
            'discount' => 'required',
            'min_item_amount' => 'required',
            'max_discount_amount' => 'required',
            'start_end_date' => 'required',
            'description' => 'required',
        ]);

        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $discount = VendorDiscount::find($discount_id);
        if($discount)
        {
            if(isset($request->image))
            {
                $img = $request->image;
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data1 = base64_decode($img);
                $Iname = uniqid();
                $file = public_path('/images/upload/') . $Iname . ".png";
                $success = file_put_contents($file, $data1);
                $data['image'] = $Iname . ".png";
            }
            $discount->update($data);
            return response(['success' => true , 'data' => 'discount updated successfully...!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Oops Discount not found..!!']);
        }
    }

    /** vendor bank details */
    public function apiShowBankDetails()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $bankDetail = VendorBankDetail::where('vendor_id',$vendor->id)->first();
        return response(['success' => true , 'data' => $bankDetail]);
    }

    public function apiAddBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'clabe' => 'required',
        ]);
        $data = $request->all();
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $bankDetail = VendorBankDetail::where('vendor_id',$vendor->id)->first();
        if(!$bankDetail)
        {
            $data['vendor_id'] = $vendor->id;
            VendorBankDetail::create($data);
            return response(['success' => true , 'data' => 'Bank details add successfully..!!']);
        }
        else
        {
            return response(['success' => false , 'data' => 'Vendor bank details already exists ..!!']);
        }
    }

    public function apiEditBankDetails()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $bankDetail = VendorBankDetail::where('vendor_id',$vendor->id)->first();
        if($bankDetail)
        {
            return response(['success' => true , 'data' => $bankDetail]);
        }
        return response(['success' => false , 'data' => 'No details found']);
    }

    public function apiUpdateBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'clabe' => 'required'
        ]);
        $data = $request->all();
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $bankDetail = VendorBankDetail::where('vendor_id',$vendor->id)->first();
        $bankDetail->update($data);
        return response(['success' => true , 'data' => 'Update successfully..!!']);
    }

    // Finance details
    public function apiLast7Days()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $now = Carbon::today();
        $orders = array();
        for ($i = 0; $i < 7; $i++) {
            $order = Order::where('vendor_id', $vendor->id)->whereDate('created_at', $now)->get();
            $discount = $order->sum('promocode_price');
            $vendor_discount = $order->sum('vendor_discount_price');
            $amount = $order->sum('amount');
            $temp['amount'] = $discount + $vendor_discount + $amount;
            $temp['admin_commission'] = $order->sum('admin_commission');
            $temp['vendor_amount'] = $order->sum('vendor_amount');
            $temp['date'] = $now->toDateString();
            array_push($orders, $temp);
            $now =  $now->subDay();
        }
        return response(['success' => true , 'data' => $orders]);
    }

    public function apiCurrentMonth()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $days = Carbon::now()->daysInMonth;
        $now = Carbon::now()->endOfMonth();
        $orders = array();
        for ($i = 0; $i < $days; $i++)
        {
            $order = Order::where('vendor_id',$vendor->id)->whereDate('created_at', $now)->get();
            $discount = $order->sum('promocode_price');
            $vendor_discount = $order->sum('vendor_discount_price');
            $amount = $order->sum('amount');
            $temp['amount'] = $discount + $vendor_discount + $amount;
            $temp['admin_commission'] = $order->sum('admin_commission');
            $temp['vendor_amount'] = $order->sum('vendor_amount');
            $temp['date'] = $now->toDateString();
            array_push($orders, $temp);
            $now =  $now->subDay();
        }
        $now = Carbon::today();
        $month = $now->month;
        return response(['success' => true , 'data' => $orders]);
    }

    public function apiMonth(Request $request)
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $data = $request->all();
        $days = Carbon::parse($data['year'].'-'.$data['month'].'-01')->daysInMonth;
        $now = Carbon::parse($data['year'].'-'.$data['month'].'-01')->endOfMonth();
        $orders = array();
        for ($i = 0; $i < $days; $i++)
        {
            $order = Order::whereDate('created_at',$now)->where('vendor_id',$vendor->id)->get();
            $discount = $order->sum('promocode_price');
            $vendor_discount = $order->sum('vendor_discount_price');
            $amount = $order->sum('amount');
            $temp['amount'] = $discount + $vendor_discount + $amount;
            $temp['admin_commission'] = $order->sum('admin_commission');
            $temp['vendor_amount'] = $order->sum('vendor_amount');
            $temp['date'] = $now->toDateString();
            array_push($orders, $order);
            $now =  $now->subDay();
        }
        $now = $now = Carbon::parse($data['year'].'-'.$data['month'].'-01');
        $month = $now->month;
        $currency = GeneralSetting::first()->currency_symbol;
        return response(['success' => true , 'data' => $orders]);
    }


    //Order
    public function apiOrder()
    {
        $vendor = Vendor::where('user_id', auth()->user()->id)->first();
        $orders = Order::where('vendor_id', $vendor->id)->get()->each->setAppends(['orderItems'])->makeHidden(['created_at', 'updated_at']);
        foreach ($orders as $order) {
            $order->user_name = User::find($order->user_id)->name;
            $order->user_phone = User::find($order->user_id)->phone;
            if ($order->delivery_type == 'HOME') {
                if (isset($order->delivery_person_id)) {
                    $order->delivery_person = DeliveryPerson::find($order->delivery_person_id, ['first_name', 'last_name', 'contact'])->makeHidden(['image', 'deliveryzone']);
                    $order->userAddress = UserAddress::find($order->address_id)->address;
                }
            }
            $order->vendorAddress = Vendor::find($order->vendor_id)->map_address;
        }
        return response(['success' => true, 'data' => $orders]);
    }

    public function apiCreateOrder(Request $request)
    {
        // ['order_id','vendor_id','user_id','delivery_person_id','date','time','amount','item','payment_type','payment_status','vendor_discount','promocode_id','promocode_price','address_id','vendor_discount_id','vendor_discount_price','order_status','delivery_charge','delivery_type','item_custimization','admin_commission','vendor_amount'];
        $request->validate([
            'user_id' => 'required',
            'date' => 'bail|required|date_format:Y-M-D',
            'time' => 'bail|required|date_format:h:i a',
            'amount' => 'bail|required|numeric',
            'item' => 'bail|required',
            'delivery_type' => 'bail|required',
            'address_id' => 'bail|required_if:delivery_type,HOME',
            'delivery_charge' => 'bail|required_if:delivery_type,HOME'
        ]);
        $data = $request->all();
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        if ($vendor->admin_comission_type == 'percentage') {
            $comm = $data['amount'] * $vendor->admin_comission_value;
            $data['admin_commission'] = $comm / 100;
            $data['vendor_amount'] = $data['amount'] - $data['admin_commission'];
        }
        if ($vendor->admin_comission_type == 'amount') {
            $data['admin_commission'] = $data['amount'] - $vendor->admin_comission_value;
            $data['vendor_amount'] = $data['amount'] - $data['admin_commission'];
        }
        $data['vendor_id'] = $vendor->id;
        $data['payment_type'] = 'COD';
        $data['payment_status'] = 1;
        $data['order_status'] = 'COMPLETE';
        $data['item'] = implode(',',$data['item']);
        $order = Order::create($data);
        foreach ($data['item'] as $item)
        {
            $order_child = array();
            $order_child['order_id'] = $order->id;
            $order_child['item'] = $item['id'];
            $order_child['price'] = $item['price'];
            $order_child['qty'] = $item['qty'];
            if(isset($item['custimization']))
            {
                $order_child['custimization'] = $item['custimization'];
            }
            OrderChild::create($order_child);
        }

        $settle = array();
        $settle['vendor_id'] = $order->vendor_id;
        $settle['order_id'] = $order->id;
        if ($order->payment_type == 'COD')
        {
            $settle['payment'] = 0;
        }
        else
        {
            $settle['payment'] = 1;
        }
        $settle['status'] = 0;
        $settle['admin_earning'] = $order->admin_commission;
        $settle['vendor_earning'] = $order->vendor_amount;
        Settle::create($settle);
        return response(['success' => true , 'data' => "order booked successfully wait for confirmation"]);
    }

    //user
    public function apiUser()
    {
        $Vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $orders = Order::where('vendor_id',$Vendor->id)->get();
        $users = [];
        foreach($orders as $order)
        {
            array_push($users,$order->user_id);
        }
        $user = User::whereIn('id',$users)->get();
        return response(['success' => true , 'data' => $user]);
    }

    public function apiCreateUser(Request $request)
    {
        $request->validate([
            'name' => 'bail|required',
            'email_id' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'phone' => 'bail|required|numeric|digits_between:6,12',
        ]);
        $data = $request->all();
        $data['status'] = 1;
        $data['password'] = Hash::make($data['password']);
        $data['is_verified'] = 1;
        $data['image'] = 'noimage.png';
        $user = User::create($data);
        $role_id = Role::where('title','User')->orWhere('title','user')->first();
        $user->roles()->sync($role_id);
        return response(['success' => true , 'data' => $user]);
    }

    //User Address
    public function apiUserAddress($user_id)
    {
        $user_address = UserAddress::where('user_id',$user_id)->get();
        return response(['success' => true , 'data' => $user_address]);
    }

    public function apiCreateUserAddress(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'lat' => 'required',
            'lang' => 'required',
            'address' => 'required',
        ]);
        $user_address = UserAddress::create($request->all());
        return response(['success' => true , 'data' => $user_address]);
    }

    public function apiFinanceDetails()
    {
        $master = array();
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $master['total_balance'] = intval(Settle::where('vendor_id',$vendor->id)->sum('vendor_earning'));
        $master['today_earning'] = intval(Settle::whereDate('created_at',Carbon::now())->where('vendor_id',$vendor->id)->sum('vendor_earning'));
        $master['weekly_earning'] = intval(Settle::where('vendor_id', $vendor->id)->whereBetween('created_at', [Carbon::now()->subDays(7)->format('Y-m-d')." 00:00:00",  Carbon::now()->format('Y-m-d')." 23:59:59"])->sum('vendor_earning'));
        $master['yearly_earning'] = intval(Settle::where('vendor_id', $vendor->id)->whereYear('created_at', date('Y'))->sum('vendor_earning'));
        $master['earning_chart'] = $this->earningChart();
        return response(['success' => true , 'data' => $master]);
    }

    public function apiCashBalance()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $settles = Settle::where('vendor_id',$vendor->id)->get();
        $settles = Settle::where('vendor_id',$vendor->id)->get();
        $balances = [];
        foreach ($settles as $settle) {
            $temp['order_id'] = Order::find($settle->order_id)->order_id;
            $temp['amount'] = $settle->vendor_earning;
            $temp['date'] = Carbon::parse($settle->created_at)->format('l,F d Y');
            $temp['name'] = $vendor->name;
            array_push($balances,$temp);
        }
        $total_balance = intval(Settle::where('vendor_id',$vendor->id)->sum('vendor_earning'));
        return response(['success' => true , 'data' => ['balance' => $balances , 'total_balance' => $total_balance]]);
    }

    public function apiFaq()
    {
        $faqs = Faq::where('type','vendor')->get();
        return response(['success' => true , 'data' => $faqs]);
    }

    public function apiInsights()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $master['today_order'] = intval(Order::where('vendor_id',$vendor->id)->whereBetween('created_at', [Carbon::now()->format('Y-m-d')." 00:00:00",  Carbon::now()->format('Y-m-d')." 23:59:59"])->count());
        $master['total_order'] = intval(Order::where('vendor_id',$vendor->id)->count());
        $master['total_earnings'] = intval(Settle::where('vendor_id',$vendor->id)->sum('vendor_earning'));
        $master['today_earnings'] = intval(Settle::whereDate('created_at',Carbon::now())->where('vendor_id',$vendor->id)->sum('vendor_earning'));
        $master['total_menu'] = intval(Menu::where('vendor_id',$vendor->id)->count());
        $master['total_submenu'] = intval(Submenu::where('vendor_id',$vendor->id)->count());
        $master['order_chart'] = $this->orderChart();
        $master['earning_chart'] = $this->earningChart();
        return response(['success' => true , 'data' => $master]);
    }

    public function orderChart()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $g_data = [];
        $now = Carbon::today();
        for ($i = 0; $i < 12; $i++)
        {
            $total_orders = intval(Order::where('vendor_id',$vendor->id)->whereMonth('created_at', $now)->count());
            $temp['data'] = $total_orders;
            $temp['label'] = $now->format('M');
            array_push($g_data,$temp);
            $now = $now->subMonth();
        }
        return $g_data;
    }

    public function earningChart()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $g_data = [];
        $now = Carbon::today();
        for ($i = 0; $i < 12; $i++)
        {
            $toatal_earning = intval(Settle::where('vendor_id',$vendor->id)->whereMonth('created_at',$now)->sum('vendor_earning'));
            $temp['data'] = $toatal_earning;
            $temp['label'] = $now->format('M');
            array_push($g_data,$temp);
            $now = $now->subMonth();
        }
        return $g_data;
    }

    public function apiVendorLogin()
    {
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        return response(['success' => true , 'data' => $vendor]);
    }

    public function apiUpdateProfile(Request $request){
        $vendor = Vendor::where('user_id',auth()->user()->id)->first();
        $request->validate([
            'name' => 'required',
            'contact' => 'bail|required|numeric|digits_between:6,12',
            'cuisine_id' => 'bail|required',
            'address' => 'required',
            'min_order_amount' => 'required',
            'for_two_person' => 'required',
            'avg_delivery_time' => 'required',
            'license_number' => 'required',
            'vendor_type' => 'required',
            'time_slot' => 'required',
        ]);
        $data = $request->all();
        if (isset($request->image))
        {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $Iname = uniqid();
            $file = public_path('/images/upload/') . $Iname . ".png";
            $success = file_put_contents($file, $data1);
            $data['image'] = $Iname . ".png";
        }
        if (isset($request->vendor_logo))
        {
            $img = $request->vendor_logo;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data1 = base64_decode($img);
            $Iname = uniqid();
            $file = public_path('/images/upload/') . $Iname . ".png";
            $success = file_put_contents($file, $data1);
            $data['vendor_logo'] = $Iname . ".png";
        }
        if (isset($data['status'])) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $vendor->update($data);
        return response(['success' => true , 'data' => 'profile update successfully..']);
    }

    public function apiChangeStatus(Request $request)
    {
        $status = strtoupper($request->status);
        $order = Order::find($request->id);
        if ($order) {
            $vendor = Vendor::where('id',$order->vendor_id)->first();
            $order->order_status = $status;
            $order->save();
            $user = User::find($order->user_id);
            if ($request->status == 'APPROVE' || $request->status == 'approve')
            {
                $start_time = Carbon::now(env('timezone'))->format('h:i a');
                $order->order_start_time = $start_time;
                $order->save();
            }
            if ($request->status == 'COMPLETE' || $request->status == 'complete')
            {
                $order->order_end_time = Carbon::now(env('timezone'))->format('h:i a');
                $order->save();
            }
            if ($vendor->vendor_driver == 0)
            {
                if ($order->delivery_type == 'HOME' && ($request->status == 'APPROVE' || $request->status == 'approve'))
                {
                    $areas = DeliveryZoneArea::all();
                    $ds = array();
                    foreach ($areas as $value)
                    {
                        $vendorss = explode(',', $value->vendor_id);
                        if (($key = array_search($vendor->id, $vendorss)) !== false)
                        {
                            $ts = DB::select(DB::raw('SELECT id,delivery_zone_id,( 3959 * acos( cos( radians(' . $vendor->lat . ') ) * cos( radians( lat ) ) * cos( radians( lang ) - radians(' . $vendor->lang . ') ) + sin( radians(' . $vendor->lat . ') ) * sin( radians(lat) ) ) ) AS distance FROM delivery_zone_area HAVING distance < ' . $value->radius . ' ORDER BY distance'));
                            foreach ($ts as $t)
                            {
                                array_push($ds, $t->delivery_zone_id);
                            }
                        }
                    }
                    $near_drivers = DeliveryPerson::whereIn('delivery_zone_id', $ds)->get();

                    foreach ($near_drivers as $near_driver)
                    {
                        $driver_notification = GeneralSetting::first()->driver_notification;
                        $driver_mail = GeneralSetting::first()->driver_mail;
                        $content = NotificationTemplate::where('title', 'delivery person order')->first();
                        $detail['drive_name'] = $near_driver->first_name . ' - ' . $near_driver->last_name;
                        $detail['vendor_name'] = $vendor->name;
                        if (UserAddress::find($request->address_id))
                        {
                            $detail['address'] = UserAddress::find($request->address_id)->address;
                        }
                        $h = ["{driver_name}", "{vendor_name}", "{address}"];
                        $notification_content = str_replace($h, $detail, $content->notification_content);
                        if ($driver_notification == 1)
                        {
                            Config::set('onesignal.app_id', env('driver_app_id'));
                            Config::set('onesignal.rest_api_key', env('driver_api_key'));
                            Config::set('onesignal.user_auth_key', env('driver_auth_key'));
                            try {
                                OneSignal::sendNotificationToUser(
                                    $notification_content,
                                    $near_driver->device_token,
                                    $url = null,
                                    $data = null,
                                    $buttons = null,
                                    $schedule = null,
                                    GeneralSetting::find(1)->business_name
                                );
                            }
                            catch (\Throwable $th)
                            {

                            }
                        }
                        $p_notification = array();
                        $p_notification['title'] = 'create order';
                        $p_notification['user_type'] = 'driver';
                        $p_notification['user_id'] = $near_driver->id;
                        $p_notification['message'] = $notification_content;
                        Notification::create($p_notification);
                        if ($driver_mail == 1) {
                            $mail_content = str_replace($h, $detail, $content->mail_content);
                            try
                            {
                                Mail::to($near_driver->email_id)->send(new DriverOrder($mail_content));
                            }
                            catch (\Throwable $th) {
                            }
                        }
                    }
                }
            }

            if ($user->language == 'spanish')
            {
                $status_change = NotificationTemplate::where('title','change status')->first();
                $mail_content = $status_change->spanish_mail_content;
                $notification_content = $status_change->spanish_notification_content;
                $detail['user_name'] = $user->name;
                $detail['order_id'] = $order->order_id;
                $detail['date'] = $order->date;
                $detail['order_status'] = $order->order_status;
                $detail['company_name'] = GeneralSetting::find(1)->business_name;
                $data = ["{user_name}","{order_id}","{date}","{order_status}","{company_name}"];

                $message1 = str_replace($data, $detail, $notification_content);
                $mail = str_replace($data, $detail, $mail_content);
                if(GeneralSetting::find(1)->customer_notification == 1)
                {
                    if($user->device_token != null)
                    {
                        try {
                            Config::set('onesignal.app_id', env('customer_app_id'));
                            Config::set('onesignal.rest_api_key', env('customer_auth_key'));
                            Config::set('onesignal.user_auth_key', env('customer_api_key'));
                            OneSignal::sendNotificationToUser(
                                $message1,
                                $user->device_token,
                                $url = null,
                                $data = null,
                                $buttons = null,
                                $schedule = null,
                                GeneralSetting::find(1)->business_name
                            );
                        } catch (\Throwable $th) {

                        }
                    }
                }

                if (GeneralSetting::find(1)->customer_mail == 1)
                {
                    try {
                        Mail::to($user->email_id)->send(new StatusChange($mail));
                    } catch (\Throwable $th) {

                    }
                }
            }
            else
            {
                $status_change = NotificationTemplate::where('title','change status')->first();
                $mail_content = $status_change->mail_content;
                $notification_content = $status_change->notification_content;
                $detail['user_name'] = $user->name;
                $detail['order_id'] = $order->order_id;
                $detail['date'] = $order->date;
                $detail['order_status'] = $order->order_status;
                $detail['company_name'] = GeneralSetting::find(1)->business_name;
                $data = ["{user_name}","{order_id}","{date}","{order_status}","{company_name}"];

                $message1 = str_replace($data, $detail, $notification_content);
                $mail = str_replace($data, $detail, $mail_content);
                if(GeneralSetting::find(1)->customer_notification == 1)
                {
                    if($user->device_token != null)
                    {
                        try {
                            Config::set('onesignal.app_id', env('customer_app_id'));
                            Config::set('onesignal.rest_api_key', env('customer_auth_key'));
                            Config::set('onesignal.user_auth_key', env('customer_api_key'));
                            OneSignal::sendNotificationToUser(
                                $message1,
                                $user->device_token,
                                $url = null,
                                $data = null,
                                $buttons = null,
                                $schedule = null,
                                GeneralSetting::find(1)->business_name
                            );
                        } catch (\Throwable $th) {

                        }
                    }
                }

                if (GeneralSetting::find(1)->customer_mail == 1)
                {
                    try {
                        Mail::to($user->email_id)->send(new StatusChange($mail));
                    } catch (\Throwable $th) {

                    }
                }
            }
            $notification = array();
            $notification['user_id'] = $user->id;
            $notification['user_type'] = 'user';
            $notification['title'] = $status;
            $notification['message'] = $message1;
            Notification::create($notification);

            if($order->delivery_type == 'SHOP')
            {
                $end_time = Carbon::now(env('timezone'))->format('h:i a');
                $order->order_start_time = $end_time;
                $order->save();
                if ($request->status == 'complete' || $request->status == 'COMPLETE')
                {
                    $settle = array();
                    $settle['vendor_id'] = $order->vendor_id;
                    $settle['order_id'] = $order->id;
                    if ($order->payment_type == 'COD')
                    {
                        $settle['payment'] = 0;
                    } else {
                        $settle['payment'] = 1;
                    }
                    $settle['vendor_status'] = 0;
                    $settle['admin_earning'] = $order->admin_commission;
                    $settle['vendor_earning'] = $order->vendor_amount;
                    Settle::create($settle);
                }
            }
            return response(['success' => true, 'data' => 'status updated']);
        }
        else{
            return response(['success' => false , 'data' => 'order not found']);
        }
    }

    public function apiVendorSetting()
    {
        $setting = GeneralSetting::first(['vendor_app_id','vendor_auth_key','vendor_api_key','currency','currency_symbol'])->makeHidden(['whitelogo','blacklogo']);
        return response(['success' => true , 'data' => $setting]);
    }
}