<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResidentialQuarter;
use App\RepositoryPattern\ResidentialQuarterRepository;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ResidentialQuarterController extends Controller
{
    protected ResidentialQuarterRepository $residentialQuarterRepository;

    function __construct(ResidentialQuarterRepository $residentialQuarterRepository)
    {
        $this->residentialQuarterRepository = $residentialQuarterRepository;
    }

    public function get($id): JsonResponse
    {
        try {
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $residentialQuarter=DB::table('residential_quarters')->where('id',$id)
                ->select(['id','name','created_at'])->first();
            if(!$residentialQuarter){
                return response()->json(['status'=>'fail','message'=>'الحي غير موجودة']);
            }
            return \response()->json(['status'=>'success','data'=>$residentialQuarter]);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }

    public function getByCity(Request $request): JsonResponse
    {
        $city_id=$request->city_id;
        return response()->json($this->residentialQuarterRepository->getByCity($city_id));
    }
    public function customGet(Request $request): JsonResponse
    {
        $district=$request->district;
        $city_id=$request->city_id;
        return response()->json($this->residentialQuarterRepository->customGet($district,$city_id));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $name=$request->name;
            $this->residentialQuarterRepository->store($name);
            return \response()->json(['status'=>'success']);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }

    }

    public function update($id,Request $request): JsonResponse
    {
        try {
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            if(!ResidentialQuarter::find($id)){
                return response()->json(['status'=>'fail','message'=>'الحي غير موجودة']);

            }
            $name=$request->name;
            $this->residentialQuarterRepository->update($id,$name);
            return \response()->json(['status'=>'success']);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }

    }

    public function getByName(Request $request): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            $name=$request->name;
            return response()->json(['status'=>'success','data'=>$this->residentialQuarterRepository->getByName($name)]);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }

    }

    public function getAll(Request $request): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            return response()->json(['status'=>'success','data'=>$this->residentialQuarterRepository->getAll()]);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }

    }

    public function getRemovableResidentialQuarters(): JsonResponse
    {
        return \response()->json($this->residentialQuarterRepository->getRemovableResidentialQuarters());
    }

    public function isHasExchangePoints($id):bool
    {
        $residentialQuarter=ResidentialQuarter::find($id);
        if($residentialQuarter->exchangePoints()->exists()){
            return true;
        }
        return false;
    }

    public function destroy($id): JsonResponse
    {
        try{
            if(Gate::denies('isAdmin')){
                return response()->json(['status'=>'fail','message'=>'غير مصرح لهذا الأمر']);
            }
            if(!ResidentialQuarter::find($id)){
                return response()->json(['status'=>'fail','message'=>'النقطة غير موجودة']);
            }
            if($this->isHasExchangePoints($id)){
                return response()->json(['status'=>'fail','message'=>'لا يمكن حذف هذا الحي لوجود نقاط تبادل مرتبطة بها، ألغي الارتباط أولا']);
            }
            $this->residentialQuarterRepository->destroy($id);
            return response()->json(['status'=>'success']);
        }
        catch (\Throwable $throwable){
            return response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }




}
