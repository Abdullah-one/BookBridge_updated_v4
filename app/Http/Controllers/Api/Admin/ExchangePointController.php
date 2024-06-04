<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangePointRequest;
use App\Models\Account;
use App\Models\ExchangePoint;
use App\RepositoryPattern\AccountRepository;
use App\RepositoryPattern\ExchangePointRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Exception;


class ExchangePointController extends Controller
{
    protected ExchangePointRepository $exchangePointRepository;
    protected AccountRepository $accountRepository;

    function __construct(AccountRepository $accountRepository,ExchangePointRepository $exchangePointRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->exchangePointRepository = $exchangePointRepository;
    }

    public function getByResidentialQuarter(Request $request): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $residentialQuarter_id=$request->residentialQuarter_id;
            return response()->json(['status'=>'success','data'=>$this->exchangePointRepository->getByResidentialQuarter($residentialQuarter_id)]);
        }
        catch (Exception $exception){
            return \response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }


    }

    public function getByName(Request $request): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $exchangePoint=$request->exchangePoint;
            return response()->json(['status'=>'success','data'=>$this->exchangePointRepository->getByName($exchangePoint)]);
        }
        catch (Exception $exception){
            return \response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }


    }

    public function getAll(): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            return response()->json(['status'=>'success','data'=>$this->exchangePointRepository->getAll()]);
        }
        catch (Exception $exception){
            return \response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }


    }

    public function customGet(Request $request): JsonResponse
    {

        $residentialQuarter_id=$request->residentialQuarter_id;
        $district=$request->district;
        $city_id=$request->city_id;
        return response()->json($this->exchangePointRepository->customGet($residentialQuarter_id,$district,$city_id));
    }

    public function register(ExchangePointRequest $request): JsonResponse
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $userName = $request->userName;
            $phoneNumber = $request->phoneNumber;
            $email = $request->email;
            $maxPackages = $request->maxPackages;
            $location=$request->location;
            $residentialQuarter_id = $request->residentialQuarter_id;
            $password =$request->password;
            DB::beginTransaction();
            $account=$this->accountRepository->store($userName,$email, $phoneNumber, 'point',  $password);
            $this->exchangePointRepository->store($account->id,$residentialQuarter_id,$maxPackages,$location);
            DB::commit();
            return response()->json(['status'=>'success']);
        }
        catch (\Throwable $throwable){
            DB::rollBack();
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function get($id): JsonResponse
    {
        try
        {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $exchangePoint=$this->exchangePointRepository->get($id);
            if(!$exchangePoint){
                return response()->json(['status'=>'fail','message'=>'النقطة غير موجودة']);
            }
            return response()->json(['status'=>'success','data'=>$exchangePoint]);
        }
        catch (\Throwable $throwable)
        {
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function update($id,ExchangePointRequest $request): JsonResponse
    {
        try {
            if(Gate::denies('isAdmin')){
                return \response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $exchangePoint=ExchangePoint::find($id);
            if(!$exchangePoint)
            {
                return response()->json(['status'=>'fail','message'=>'النقطة غير موجودة']);
            }
            $userName = $request->userName;
            $phoneNumber = $request->phoneNumber;
            $email = $request->email;
            $maxPackages = $request->maxPackages;
            $location=$request->location;
            $residentialQuarter_id = $request->residentialQuarter_id;
            $password =$request->password;

            DB::beginTransaction();
            $this->accountRepository->update($exchangePoint->account_id, $userName, $phoneNumber,$email,$password);
            $this->exchangePointRepository->update($id, $residentialQuarter_id, $maxPackages,$location);
            DB::commit();
            return response()->json(['status'=>'success']);


        }
        catch (\Throwable $throwable){
            DB::rollBack();
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function getRemovableResidentialQuarters(): JsonResponse
    {
        return \response()->json($this->exchangePointRepository->getRemovableResidentialQuarters());
    }

    public function destroyRemovableResidentialQuarter($id): void
    {

        $result = $this->residentialQuarterRepository->destroy($id);
        if (!$result) {
            abort(404);
        }
    }

    public function softDelete($id): JsonResponse
    {
        return \response()->json($this->exchangePointRepository->softDelete($id));
    }

}
