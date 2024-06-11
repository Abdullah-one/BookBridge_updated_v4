<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\RepositoryPattern\AccountRepository;
use App\RepositoryPattern\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Exception;
use Throwable;

class AccountController extends Controller
{
    protected AccountRepository $accountRepository;
    protected UserRepository $userRepository;
    function __construct(AccountRepository $AccountRepository,UserRepository $UserRepository)
    {
        $this->accountRepository=$AccountRepository;
        $this->userRepository=$UserRepository;
    }
    public function login(Request $request): JsonResponse
    {
        try {
            $emailOrPhone = $request->emailOrPhone;
            $account=$this->accountRepository->isExist($emailOrPhone,['admin','superAdmin']);
            $device_name=$request->device_name;
            if (!$account || !Hash::check($request->password, $account->password)) {
                response()->json(['status'=>'fail','message'=>'كلمة المرور أو البريد الإلكتروني غير صحيح']);
            }
            $token=$account->createToken('laptop')->plainTextToken;
            return response()->json(['status'=>'success','data'=>['token'=>$token ]]);
        }
        catch (Exception $exception){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }

    }

    public function checkValidityOfToken(): JsonResponse
    {
        try {
            if (Gate::denies('isAdmin')) {
                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
            }

            return response()->json(['status'=>'success']);
        }
        catch (\Exception $exception){
            return response()->json(['status' => 'fail', 'message' => 'هناك خطأ بالخادم']);
        }
    }

//    public function getNumberOfUsersAndPointsAndStreet(): JsonResponse
//    {
//        try {
//            if (Gate::denies('isAdmin')) {
//                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
//            }
//            $no_users=
//
//            return response()->json(['status'=>'success']);
//        }
//        catch (\Exception $exception){
//            return response()->json(['status' => 'fail', 'message' => 'هناك خطأ بالخادم']);
//        }
//
//    }



}
