<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Account;
use App\Models\User;
use App\RepositoryPattern\AccountRepository;
use App\RepositoryPattern\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class UserController extends Controller
{
    protected AccountRepository $accountRepository;
    protected UserRepository $userRepository;
    function __construct(AccountRepository $AccountRepository,UserRepository $UserRepository)
    {
        $this->accountRepository=$AccountRepository;
        $this->userRepository=$UserRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $users=DB::table('users')
                ->join('accounts','users.account_id','=','accounts.id')
                ->select([
                    'users.id',
                    'userName',
                    'email',
                    'phoneNumber',
                    'isBlocked',
                    'no_donations as عدد مرات التبرع',
                    'no_benefits as عدد مرات الاستفادة',
                    'no_bookingOfFirstSemester as عدد مرات الحجز لكتب الفصل الأول خلال دورة تشغيلية',
                    'no_bookingOfSecondSemester as عدد مرات الحجز لكتب الفصل الثاني خلال دورة تشغيلية',
                    'no_non_adherence_donor as عدد مرات عدم الالتزام بتسليم التبرعات ',
                    'no_non_adherence_beneficiary as عدد مرات عدم الالتزام باستلام التبرعات'
                ])
                ->orderByDesc('created_at')
                ->paginate(8);
            return response()->json(['status'=>'success','data'=>$users]);
        }
        catch (Throwable $throwable){
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterUserRequest $request)
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $email=$request->email;
            $password=$request->password;
            $username = strstr($email, '@', true); // Extract characters before '@'
            if (strlen($username) > 20) {
                $username = '@'.substr($username, 0, 20); // Take the first 20 characters
            }
            $phoneNumber=$request->phoneNumber;
            $alreadyAccountWithSamePhone=$this->accountRepository->exist($phoneNumber);
            if($alreadyAccountWithSamePhone){
                return  response()->json(['status'=>'fail','message'=>'يوجد حساب برقم الجوال هذا ']);
            }
            DB::beginTransaction();
            $account = $this->accountRepository->store($username,$email, 'user', $password,$phoneNumber);
            $this->userRepository->store($account->id);
            DB::commit();
            return response()->json(['status'=>'success']);
        }
        catch (Throwable $throwable){
            DB::rollBack();
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(RegisterUserRequest $request, string $id)
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $user=User::find($id);
            if(! $user){
                return \response()->json(['status'=>'fail','message'=>'هذا المستخدم غير موجود']);
            }
            $email=$request->email;
            $password=$request->password;
            $deviceName=$request->deviceName;
            $username = strstr($email, '@', true); // Extract characters before '@'
            if (strlen($username) > 20) {
                $username = '@'.substr($username, 0, 20); // Take the first 20 characters
            }
            $phoneNumber=$request->phoneNumber;
            $alreadyAccountWithSamePhone=$this->accountRepository->exist($phoneNumber);
            if($alreadyAccountWithSamePhone){
                return  response()->json(['status'=>'fail','message'=>'يوجد حساب برقم الجوال هذا ']);
            }
            $idOfUserAccount=User::find($id)->account_id;
            $account=Account::find($idOfUserAccount);
            $account->update([
                'userName' => $username,
                'email' => $email,
                'phoneNumber' => $phoneNumber,
                'password'=> $password

            ]);
            return response()->json(['status'=>'success']);
        }
        catch (Throwable $throwable){
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function block(string $id)
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $user=User::find($id);
            if(! $user){
                return \response()->json(['status'=>'fail','message'=>'هذا المستخدم غير موجود']);
            }
            $account=Account::find($user->account_id);
            $account->update([
                'isBlocked' => 1
            ]);
            return response()->json(['status'=>'success']);
        }
        catch (Throwable $throwable){
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function unblock(string $id)
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $user=User::find($id);
            if(! $user){
                return \response()->json(['status'=>'fail','message'=>'هذا المستخدم غير موجود']);
            }
            $account=Account::find($user->account_id);
            $account->update([
                'isBlocked' => 0
            ]);
            return response()->json(['status'=>'success']);
        }
        catch (Throwable $throwable){
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function customGet(Request $request): \Illuminate\Http\JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $identifier=$request->identifier;
            $user=DB::table('users')
                ->join('accounts','users.account_id','=','accounts.id')
                ->where('email',$identifier)
                ->orWhere('users.id',$identifier)
                ->orWhere('phoneNumber',$identifier)
                ->select([
                    'users.id',
                    'userName',
                    'email',
                    'phoneNumber',
                    'isBlocked',
                    'no_donations as عدد مرات التبرع',
                    'no_benefits as عدد مرات الاستفادة',
                    'no_bookingOfFirstSemester as عدد مرات الحجز لكتب الفصل الأول خلال دورة تشغيلية',
                    'no_bookingOfSecondSemester as عدد مرات الحجز لكتب الفصل الثاني خلال دورة تشغيلية',
                    'no_non_adherence_donor as عدد مرات عدم الالتزام بتسليم التبرعات ',
                    'no_non_adherence_beneficiary as عدد مرات عدم الالتزام باستلام التبرعات'
                ])
                ->first();
            if(!$user){
                return \response()->json(['status'=>'fail','message'=>'لا يوجد مستخدم بهذا المعرف']);
            }

            return response()->json(['status'=>'success','data'=>$user]);
        }
        catch (Throwable $throwable){
            return  response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }


    }
}
