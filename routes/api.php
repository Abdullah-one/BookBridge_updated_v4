<?php


use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Shared\AccountController;
use App\Http\Controllers\Api\User\BookDonationController;
use App\Http\Controllers\Api\User\ExchangePointController;
use App\Http\Controllers\Api\User\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//                                          *  Notifications  Controller*
Route::controller(NotificationController::class)->prefix('notifications')->group(function (){
    Route::middleware('auth:sanctum')->get('/get','get')->name('notifications.get');
    Route::middleware('auth:sanctum')->post('/updateFcmToken','updateFcmToken')->name('updateFcmToken');
    Route::get('/testNotification','testNotification')->name('testNotification');

});









//                                           ---   SHARED   ---
//                                     --   between Point and User   --
//                                         *  Account Controller  *
Route::controller(AccountController::class)->prefix('shared/accounts')->group(function (){
    Route::middleware('auth:sanctum')->get('/getLoginInformation','getLoginInformation');
    Route::post('/login','login')->name('login');
    Route::post('/ForgotPassword','ForgotPassword')->name('ForgotPassword');
    Route::post('/ResetPassword','ResetPassword')->name('ResetPassword');
    Route::post('/checkTokenOfResetPassword','checkTokenOfResetPassword')->name('checkTokenOfResetPassword');
    Route::post('/sendVerificationCodeForPhone','sendVerificationCodeForPhone')->name('sendVerificationCodeForPhone');
    Route::middleware('auth:sanctum')->post('/verifyPhoneNumber','verifyPhoneNumber')->name('verifyPhoneNumber');
    Route::middleware('auth:sanctum')->delete('/logout','logout')->name('logout');
    Route::middleware('auth:sanctum')->put('/UpdatePassword','UpdatePassword')->name('UpdatePassword');
    Route::middleware('auth:sanctum')->post('/checkValidityOfPassword','checkValidityOfPassword')->name('checkValidityOfPassword');
    Route::middleware('auth:sanctum')->put('/updateEmail','updateEmail')->name('updateEmail');


});


//                                          --   between Point and Admin   --
//                                            *  BookDonation Controller  *
Route::controller(\App\Http\Controllers\Api\Shared\BookDonationController::class)->prefix('shared/bookDonations')->group(function (){
   Route::post('/re_addToPoint/{id}','re_addToPoint')->name('re_addToPoint');
});









//                                           ---   USER   ---
//                                        *  Account Controller  *
Route::controller(\App\Http\Controllers\Api\User\AccountController::class)->prefix('user/accounts')->group(function (){
    Route::post('/registerUserAccount','registerUserAccount')->name('RegisterUserAccount');
    Route::get('/getUserInformation','getUserInformation')->name('getUserInformation');
});
//                                      *  BookDonation Controller  *
Route::controller(BookDonationController::class)->prefix('user/bookDonations')->group(function (){
    Route::middleware('auth:sanctum')->get('getLastDonations','getLastDonations')->name('getLastDonations');
    Route::middleware('auth:sanctum')->put('book/{id}','book')->name('book');
    Route::middleware('auth:sanctum')->get('get/{id}','get')->name('BookDonation.get');
    Route::middleware('auth:sanctum')->post('store','store')->name('BookDonation.store');
    Route::middleware('auth:sanctum')->put('update/{id}','update')->name('BookDonation.update');
    Route::middleware('auth:sanctum')->delete('destroy/{id}','destroy')->name('BookDonation.destroy');
    Route::middleware('auth:sanctum')->get('getWaitedDonationsForUser','getWaitedDonationsForUser')->name('getWaitedDonationsForUser');
    Route::middleware('auth:sanctum')->post('searchAvailableBookPackages','searchAvailableBookPackages')->name('searchAvailableBookPackages');
    Route::middleware('auth:sanctum')->put('cancelReservationByDonor/{bookDonation_id}','cancelReservationByDonor')->name('cancelReservationByDonor');
    Route::middleware('auth:sanctum')->get('getUndeliveredDonationsForUser','getUndeliveredDonationsForUser')->name('getUndeliveredDonationsForUser');
    Route::middleware('auth:sanctum')->get('getDeliveredDonationsForUser','getDeliveredDonationsForUser')->name('getDeliveredDonationsForUser');
    Route::middleware('auth:sanctum')->get('getRejectedDonationsForUser','getRejectedDonationsForUser')->name('getRejectedDonationsForUser');
    Route::middleware('auth:sanctum')->get('getWaitedReservationsToReceive','getWaitedReservationsToReceive')->name('getWaitedReservationsToReceive');
    Route::middleware('auth:sanctum')->get('getWaitedReservationsToCollect','getWaitedReservationsToCollect')->name('getWaitedReservationsToCollect');
    Route::middleware('auth:sanctum')->get('getDeliveredReservations','getDeliveredReservations')->name('getDeliveredReservations');
    Route::middleware('auth:sanctum')->get('getFailedReservations','getFailedReservations')->name('getFailedReservations');
    Route::middleware('auth:sanctum')->put('cancelReservationInPointByBeneficiary/{bookDonation_id}','cancelReservationInPointByBeneficiary')->name('cancelReservationInPointByBeneficiary');
    Route::middleware('auth:sanctum')->put('cancelReservationNotInPointByBeneficiary/{bookDonation_id}','cancelReservationNotInPointByBeneficiary')->name('cancelReservationNotInPointByBeneficiary');
});
//                                                  *  Image Controller  *
Route::controller(ImageController::class)->prefix('images')->group(function (){
    Route::delete('destroy/{id}','destroy')->name('Image.destroy');
    Route::post('uploadImage','uploadImage')->name('uploadImage');
});
//                                      * Residential Quarter Controller *
Route::controller(\App\Http\Controllers\Api\User\ResidentialQuarterController::class)->prefix('user/residentialQuarter')->group(function (){
    Route::get('/getResidentialQuarterAndItsPoints','getResidentialQuarterAndItsPoints')->name('getResidentialQuarterAndItsPoints');
    Route::get('/getResidentialQuarter','getResidentialQuarter')->name('getResidentialQuarter');
});
//                                      * Exchange Point Controller *
Route::controller(ExchangePointController::class)->prefix('user/exchangePoints')->group(function (){
    Route::get('/getExchangePointsByStreetID','getExchangePointsByStreetID')->name('getExchangePointsByStreetID');
});







//                                       ---  SUPER ADMIN  ---
//                                     **   AccountController   **
Route::controller(\App\Http\Controllers\Api\Admin\SuperAdmin\AccountController::class)->prefix('superAdmin/accounts')->group(function (){
    Route::post('/registerAdmin','registerAdmin')->name('registerAdmin');
    Route::delete('/deleteAdmin/{account_id}','deleteAdmin')->name('deleteAdmin');
    Route::get('/getAdmins','getAdmins')->name('getAdmins');
    Route::get('/customGetAdmins','customGetAdmins')->name('customGetAdmins');
    Route::get('/getAdminByPhoneNumber','getAdminByPhoneNumber')->name('getAdminByPhoneNumber');
    Route::put('/update/{id}','update')->name('updateAdmin');
    Route::get('/getAdmin/{id}','getAdmin')->name('getAdmin');
    Route::get('/getAdminByUserName/','getAdminByUserName')->name('getAdminByUserName');
});
//                                               **   CityController   **
Route::controller(\App\Http\Controllers\Api\Admin\SuperAdmin\CityController::class)->prefix('superAdmin/cities')->group(function () {
    Route::get('/getAdministratorCities/admins/{admin_id}','getAdministratorCities')->name('getAdministratorCities');
    Route::put('/addCityForAdministration/{city_id}/admins/{admin_id}','addCityForAdministration')->name('addCityForAdministration');
    Route::put('/removeCityFromAdministration/{city_id}/admins/{admin_id}','removeCityFromAdministration')->name('addCityForAdministration');
    Route::post('/store','store')->name('cities.store');
    Route::get('/customGet','customGet')->name('cities.customGet');
    Route::get('/getByName','getByName')->name('cities.getByName');
    Route::get('/getAdminsOfCity/{id}','getAdminsOfCity')->name('getAdminsOfCity');
    Route::delete('/destroyRemovableCity/{id}','destroyRemovableCity')->name('destroyRemovableCity');
    Route::get('/getRemovableCities','getRemovableCities')->name('getRemovableCities');
    Route::put('/update/{id}','update')->name('cities.update');
    Route::get('/{id}','get')->name('cities.get');
    Route::get('/getByDistrict','getByDistrict')->name('cities.getByDistrict');
});
//                                           **   ResidentialQuarterController   **
Route::controller(\App\Http\Controllers\Api\Admin\SuperAdmin\ResidentialQuarterController::class)->prefix('superAdmin/residentialQuarters')->group(function () {
    Route::get('/getByCity','getByCity')->name('residentialQuarters.getByCity');
    Route::get('/customGet','customGet')->name('residentialQuarters.customGet');
    Route::get('/getByName','getByName')->name('residentialQuarters.getByName');
    Route::get('/getRemovableResidentialQuarters','getRemovableResidentialQuarters')->name('getRemovableResidentialQuarters');
    Route::post('/store','store')->name('residentialQuarters.store');
    Route::put('/update/{id}','update')->name('residentialQuarters.update');
    Route::delete('/destroyRemovableResidentialQuarter/{id}','destroyRemovableResidentialQuarter')->name('destroyRemovableResidentialQuarter');
});
//                                              **   ExchangePointController   **
Route::controller(\App\Http\Controllers\Api\Admin\SuperAdmin\ExchangePointController::class)->prefix('superAdmin/exchangePoints')->group(function () {
    Route::get('/getByResidentialQuarter','getByResidentialQuarter')->name('exchangePoints.getByResidentialQuarter');
    Route::get('/customGet','customGet')->name('ExchangePointController.customGet');
    Route::post('/register','register')->name('ExchangePointController.register');
    Route::put('/update/{id}','update')->name('ExchangePointController.update');
    Route::get('/{id}','get')->name('ExchangePointController.get');
});









//                                         ---  ADMIN ---
//                                     *  Account Controller  *
Route::controller(\App\Http\Controllers\Api\Admin\AccountController::class)->prefix('admin/accounts')->group(function () {
    Route::post('/login','login')->name('admin.accounts.login');
});


//                                           ---   Other   ---
//                                         **   CityController   **
Route::controller(\App\Http\Controllers\Api\User\CityController::class)->prefix('cities')->group(function (){
    Route::get('getByDistrict/{district}','getByDistrict')->name('getByDistrict');
    Route::get('get','get')->name('get');

});
//                                          **   ResidentialQuarterController   **
Route::controller(\App\Http\Controllers\Api\User\ResidentialQuarterController::class)->prefix('residential_quarters')->group(function (){
    Route::get('getByCity/{city_id}','getByCity')->name('getByCity');
});








//                                                  --   Exchange Point  ---
//                                                 **   BookDonationController   **
Route::controller(\App\Http\Controllers\Api\Point\BookDonationController::class)->prefix('exchangePoint/bookDonations')->group(function (){
    Route::middleware('auth:sanctum')->put('RejectByExchangePoint/{bookDonation_id}','RejectByExchangePoint')->name('RejectByExchangePoint');
    Route::middleware('auth:sanctum')->get('getUnWaitedDonationsByPhoneNumber','getUnWaitedDonationsByPhoneNumber')->name('getUnWaitedDonationsByPhoneNumber');
    Route::middleware('auth:sanctum')->get('getWaitedDonationsByPhoneNumber','getWaitedDonationsByPhoneNumber')->name('getWaitedDonationsByPhoneNumber');
    Route::put('confirmReceptionOfWaitedDonations/{bookDonation_id}','confirmReceptionOfWaitedDonations')->name('confirmReceptionOfWaitedDonations');
    Route::put('confirmReceptionOfUnWaitedDonations/{bookDonation_id}','confirmReceptionOfUnWaitedDonations')->name('confirmReceptionOfUnWaitedDonations');
    Route::get('getWaitedReservationsByPhoneNumber','getWaitedReservationsByPhoneNumber')->name('getWaitedReservationsByPhoneNumber');
    Route::put('RejectFromBeneficiary/{bookDonation_id}','RejectFromBeneficiary')->name('RejectFromBeneficiary');
    Route::put('confirmDelivery/{bookDonation_id}','confirmDelivery')->name('confirmDelivery');

});
//
Route::controller(ExchangePointController::class)->prefix('exchangePoints')->group(function (){
    Route::get('getExchangePoints','getExchangePoints')->name('getExchangePoints');
});
//                                                    **   Account Controller   **
Route::controller(\App\Http\Controllers\Api\Point\AccountController::class)->prefix('point/accounts')->group(function (){
    Route::get('/getPointInformation','getPointInformation')->name('getPointInformation');
    Route::get('/checkValidityOfToken','checkValidityOfToken')->name('checkValidityOfToken');
    Route::post('/login','login')->name('login');
});




