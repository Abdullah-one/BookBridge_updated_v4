<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\sendNotification;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use PharIo\Version\Exception;

class NotificationController extends Controller
{
    /**
     * @throws \Google\Exception
     */
    public function sendPushNotification($data): bool
    {
        //TODO:
        $credentialsFilePath = app_path('school-textbook-sharing-firebase-adminsdk-ytov8-830792e7a5.json');
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        //TODO:
        $apiurl = 'https://fcm.googleapis.com/v1/projects/school-textbook-sharing/messages:send';
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];
//            $test_data = [
//                "title" => "TITLE_HERE",
//                "description" => "DESCRIPTION_HERE",
//            ];
//
//            $data['data'] = $test_data;
//
//            $data['token'] = $user['fcm_token']; // Retrive fcm_token from users table

        $payload = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['data']['title'],
                    'body' => $data['data']['description'],
                ],
            //'data' => $data['data'],  // Optional: Include any additional data
            ]
        ];

        //$payload['message'] = $data;
        $payload = json_encode($payload);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_exec($ch);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log response for debugging
        Log::info('Firebase Response: ' . $response);
        Log::info('HTTP Code: ' . $httpCode);
        return $httpCode === 200;

    }

    /**
     * @throws \Google\Exception
     */
    public function create($data): bool
    {
        Notification::create($data['data']);
        if($this->sendPushNotification($data))
        {
            return true;
        }
        return false;
    }

    public function testNotification():JsonResponse
    {
        $bookDonation=87878788;
            $data =
                [
                    'data' => [
                        'title' => ' تسليم التبرع للنقطة',
                        'description' => "تم حجز تبرعك ب id {$bookDonation} ، يرجى مراجعة صفحة تبرعاتي ثم نافذة بانتظار استلامها، يرجى تسليم التبرع تكرما خلال المهلة المحددة ثلاثة أيام، بعد تسليم التبرع تحقق من وصول إشعار لجوالك ، في حال عدم وصوله تواصل معنا عن طريق الواتساب",
                        'account_id' => 1,
                    ],
                    'token' => 'ccc71udSRj2LD1861sHtme:APA91bFKYjPPgFvHRy18-v2iN0NgB07Ch2lzzwWosV6dglbYcdQqeRZK2DdfgoDaS8TuVzDtXHbuyBNrY8LdzdvSq_U5II5RYumTVZE-LfQZtJ1hSdnKfC3tNcb5yu5qTqHcgj-h_Wzu'
                ];
           sendNotification::dispatch($data);
            //$this->create($data);
            return \response()->json(['status'=>'success']);


    }

    public function get(): JsonResponse
    {
        try {
            if (Gate::denies('isUser')) {
                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
            }
            $account_id=auth()->user()->id;
            $result = DB::table('notifications')
                ->where('account_id',$account_id)
                ->select([
                    'title',
                    'description',
                    'created_at',
                    'isRead'
                ])
                ->paginate();
            Notification::query()->update([
                'isRead'=>1
            ]);
            return response()->json(['status' => 'success', 'data' => $result]);
        }
        catch (Exception $e){
            return response()->json(['status' => 'fail', 'message' => 'هناك خطأ بالخادم']);
        }

    }

    public function updateFcmToken(Request $request): JsonResponse
    {

        try {
            if (Gate::denies('isUser')) {
                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
            }
            $fcmToken=$request->fcmToken;
            DB::table('accounts')->where('id',auth()->user()->id)
                ->update([
                    'fcm_token'=>$fcmToken
                ]);
            return \response()->json(['status'=>'success']);
        }
        catch (\Exception $exception){
            return \response()->json(['status'=>'fail','message'=>'هناك خطأ بالخادم']);
        }
    }


}
