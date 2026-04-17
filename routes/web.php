<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\Action\AirtimeController;
use App\Http\Controllers\Action\DataController;
use App\Http\Controllers\Action\EducationalController;
use App\Http\Controllers\Agency\BvnServicesController;
use App\Http\Controllers\Agency\BvnModificationController;
use App\Http\Controllers\Agency\ManualSearchController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Agency\BvnUserController;
use App\Http\Controllers\Agency\NinModificationController;
use App\Http\Controllers\EnrolmentReportController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Agency\NinValidationController;
use App\Http\Controllers\Agency\IpeController;
use App\Http\Controllers\Agency\LoanController;
use App\Http\Controllers\Verification\NinVerificationController;
use App\Http\Controllers\Verification\BvnverificationController;
use App\Http\Controllers\Verification\NinDemoVerificationController;
use App\Http\Controllers\Verification\NinPhoneVerificationController;
use App\Http\Controllers\Action\SmeDataController;
use App\Http\Controllers\Action\NecoNabtedController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\AiCommentController;




Route::post('/palmpay/webhook', [PaymentWebhookController::class, 'handleWebhook'])->middleware('throttle:60,1');
Route::post('/validations-webhook', [NinValidationController::class, 'webhook'])->name('validations.webhook');
Route::get('/', function () {
    return view('welcome');
});
// Webhooks (public, validate signature in controller)



Route::post('/support/send', [SupportController::class, 'sendContactMessage'])->name('support.send');

Route::middleware(['auth'])->group(function () {



    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/update-required', [ProfileController::class, 'updateRequired'])->name('profile.updateRequired');

    // New Profile Routes
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/additional', [ProfileController::class, 'updateAdditionalInfo'])->name('profile.additional');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');


    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet');
        Route::post('/create-virtual-account', [WalletController::class, 'createWallet'])->name('virtual.account.create');
        Route::post('/claim-bonus', [WalletController::class, 'claimBonus'])->name('wallet.claimBonus');
    });


    /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */
    Route::prefix('services')->group(function () {
        Route::get('/bvn', [ServicesController::class, 'bvnServices'])->name('bvn.services');
        Route::get('/vip', [ServicesController::class, 'vipServices'])->name('vip.services');
        Route::get('/nin', [ServicesController::class, 'ninServices'])->name('nin.services');
        Route::get('/verification', [ServicesController::class, 'verificationServices'])->name('verification.services');
        Route::get('/support', [ServicesController::class, 'supportServices'])->name('support.services');
        Route::get('/settings', [ServicesController::class, 'settingServices'])->name('settings.services');
        Route::get('/transaction-pin', [ServicesController::class, 'transactionPin'])->name('transaction.pin');
        Route::get('/airtime', [ServicesController::class, 'airtime'])->name('airtime.index');
    });



    /*
    |--------------------------------------------------------------------------
    | Utility bill payment (Airtime & Data)
    |--------------------------------------------------------------------------
    */

    Route::get('/airtime', [AirtimeController::class, 'airtime'])->name('airtime');
    Route::post('/buy-airtime', [AirtimeController::class, 'buyAirtime'])->name('buyairtime');


    Route::get('/data', [DataController::class, 'data'])->name('buy-data');
    Route::post('/buy-data', [DataController::class, 'buydata'])->name('buydata');
    Route::get('/fetch-data-bundles', [DataController::class, 'fetchBundles'])->name('fetch.bundles');
    Route::get('/fetch-data-bundles-price', [DataController::class, 'fetchBundlePrice'])->name('fetch.bundle.price');
    Route::post('/verify-pin', [DataController::class, 'verifyPin'])->middleware('throttle:5,1')->name('verify.pin');


    // SME Data (New Service)
    Route::prefix('sme-data')->group(function () {
        Route::get('/', [SmeDataController::class, 'index'])->name('buy-sme-data');
        Route::post('/buy', [SmeDataController::class, 'buySMEdata'])->name('buy-sme-data.submit');

        // SME Data AJAX Routes
        Route::get('/fetch-data-type', [SmeDataController::class, 'fetchDataType']);
        Route::get('/fetch-data-plan', [SmeDataController::class, 'fetchDataPlan']);
        Route::get('/fetch-sme-data-bundles-price', [SmeDataController::class, 'fetchSmeBundlePrice']);
    });


    Route::get('/education', [EducationalController::class, 'pin'])->name("education");
    Route::post('/buy-pin', [EducationalController::class, 'buypin'])->middleware('throttle:5,1')->name('buypin');
    Route::get('/education/receipt/{transaction_ref}', [EducationalController::class, 'receipt'])->name('education.receipt');

    Route::get('/neco-nabted', [NecoNabtedController::class, 'index'])->name('neco-nabted');
    Route::post('/buy-neco-nabted', [NecoNabtedController::class, 'purchase'])->name('buy-neco-nabted');



    Route::get('/jamb', [EducationalController::class, 'jamb'])->name('jamb');
    Route::post('/verify-jamb', [EducationalController::class, 'verifyJamb'])->name('verify.jamb');
    Route::post('/buy-jamb', [EducationalController::class, 'buyJamb'])->middleware('throttle:5,1')->name('buyjamb');

    Route::get('/electricity', [App\Http\Controllers\Action\ElectricityController::class, 'index'])->name('electricity');
    Route::post('/verify-electricity', [App\Http\Controllers\Action\ElectricityController::class, 'verifyMeter'])->middleware('throttle:10,1')->name('verify.electricity');
    Route::post('/buy-electricity', [App\Http\Controllers\Action\ElectricityController::class, 'purchase'])->middleware('throttle:5,1')->name('buy.electricity');

    Route::get('/cable', [App\Http\Controllers\Action\CableController::class, 'index'])->name('cable');
    Route::get('/cable/variations', [App\Http\Controllers\Action\CableController::class, 'getVariations'])->name('cable.variations');
    Route::post('/cable/verify', [App\Http\Controllers\Action\CableController::class, 'verifyIuc'])->middleware('throttle:10,1')->name('verify.cable');
    Route::post('/cable/buy', [App\Http\Controllers\Action\CableController::class, 'purchase'])->middleware('throttle:5,1')->name('buy.cable');

    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions');

    /*
    |--------------------------------------------------------------------------
    | Transfer to Smart User
    |--------------------------------------------------------------------------
    */
    Route::get('/transfer', [App\Http\Controllers\TransferController::class, 'index'])->name('transfer.index');
    Route::post('/transfer/verify', [App\Http\Controllers\TransferController::class, 'verifyUser'])->name('transfer.verify');
    Route::post('/transfer/process', [App\Http\Controllers\TransferController::class, 'processTransfer'])->name('transfer.process');

    Route::get('/thankyou', [App\Http\Controllers\TransactionController::class, 'receipt'])->name('thankyou');


    /*
    |--------------------------------------------------------------------------
    | BVN Services & CRM
    |--------------------------------------------------------------------------
    */
    Route::get('/bvn-crm', [BvnServicesController::class, 'index'])->name('bvn-crm');
    Route::post('/bvn-crm', [BvnServicesController::class, 'store'])->name('crm.store');

    Route::get('/enrolment-report', [EnrolmentReportController::class, 'index'])->name('enrolment.report');

    Route::get('/send-vnin', [BvnServicesController::class, 'index'])->name('send-vnin');
    Route::post('/send-vnin', [BvnServicesController::class, 'store'])->name('send-vnin.store');

    Route::get('/modification-fields/{serviceId}', [BvnModificationController::class, 'getServiceFields'])->name('modification.fields');
    Route::get('/modification', [BvnModificationController::class, 'index'])->name('modification');
    Route::post('/modification', [BvnModificationController::class, 'store'])->name('modification.store');

    Route::get('/phone-search', [ManualSearchController::class, 'index'])->name('phone.search.index');
    Route::post('/phone-search', [ManualSearchController::class, 'store'])->name('phone.search.store');
    Route::get('/phone-search/{id}/details', [ManualSearchController::class, 'showDetails'])->name('phone.search.details');

    /*
    |--------------------------------------------------------------------------
    | Affidavit Services
    |--------------------------------------------------------------------------
    */
    Route::get('/affidavit', [App\Http\Controllers\Agency\AffidavitController::class, 'index'])->name('affidavit.index');
    Route::post('/affidavit', [App\Http\Controllers\Agency\AffidavitController::class, 'store'])->name('affidavit.store');

    /*
    |--------------------------------------------------------------------------
    | CAC Registration Services
    |--------------------------------------------------------------------------
    */
    Route::get('/cac-reg', [App\Http\Controllers\Agency\CacRegistrationController::class, 'index'])->name('cac.index');
    Route::post('/cac-reg', [App\Http\Controllers\Agency\CacRegistrationController::class, 'store'])->name('cac.store');

    /*
    |--------------------------------------------------------------------------
    | TIN Registration Services
    |--------------------------------------------------------------------------
    */
    Route::controller(\App\Http\Controllers\Verification\TinVerificationController::class)->group(function () {
        Route::get('/tin-reg', 'index')->name('cac.tin');
        Route::post('/tin-reg/validate', 'validateTin')->name('tin.validate');
        Route::post('/tin-reg/download', 'downloadSlip')->name('tin.download');
        Route::get('/tin-reg/get-price', 'getFieldPrice')->name('tin.getFieldPrice');
        Route::get('/tin-reg/check-balance', 'checkBalance')->name('tin.checkBalance');
    });

    // Support Routes
    Route::prefix('support')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('support.index');
        Route::get('/create', [SupportController::class, 'create'])->name('support.create');
        Route::post('/store', [SupportController::class, 'store'])->name('support.store')->middleware('throttle:5,1');
        Route::get('/{ticket}', [SupportController::class, 'show'])->name('support.show');
        Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply')->middleware('throttle:10,1');
        Route::post('/{ticket}/close', [SupportController::class, 'close'])->name('support.close');
        Route::get('/{ticket}/updates', [SupportController::class, 'fetchUpdates'])->name('support.updates');
    });

    // BVN User routes
    Route::get('/bvn', [BvnUserController::class, 'index'])->name('bvn.index');
    Route::post('/bvn/store', [BvnUserController::class, 'store'])->name('bvn.store');

    // NIN Modification Routes
    Route::prefix('nin-modification')->group(function () {
        Route::get('/', [NinModificationController::class, 'index'])->name('nin-modification');
        Route::post('/', [NinModificationController::class, 'store'])->name('nin-modification.store');
    });

    // NIN Validation & IPE Routes
    // NIN Validation
    Route::controller(NinValidationController::class)->prefix('nin-validation')->name('nin-validation.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/check/{id}', 'checkStatus')->name('check');
        Route::post('/webhook', 'webhook')->name('webhook');
    });

    Route::controller(IpeController::class)->prefix('ipe-validation')->name('ipe-validation.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/check/{id}', 'checkStatus')->name('check');
    });

    // Loan Service
    Route::controller(LoanController::class)->prefix('loan')->name('loan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
    });

    /*
    |--------------------------------------------------------------------------
    | NIN Verification
    |--------------------------------------------------------------------------
    */
    Route::prefix('nin-verification')->group(function () {
        Route::get('/', [NinVerificationController::class, 'index'])->name('nin.verification.index');
        Route::post('/', [NinVerificationController::class, 'store'])->name('nin.verification.store');
        Route::post('/{id}/status', [NinVerificationController::class, 'updateStatus'])->name('nin.verification.status');
        Route::get('/standardSlip/{id}', [NinVerificationController::class, 'standardSlip'])->name('standardSlip');
        Route::get('/premiumSlip/{id}', [NinVerificationController::class, 'premiumSlip'])->name('premiumSlip');
        Route::get('/vninSlip/{id}', [NinVerificationController::class, 'vninSlip'])->name('vninSlip');
    });

    /*
    |--------------------------------------------------------------------------
    | BVN Verification
    |--------------------------------------------------------------------------
    */
    Route::prefix('bvn-verification')->group(function () {
        Route::get('/', [BvnverificationController::class, 'index'])->name('bvn.verification.index');
        Route::post('/', [BvnverificationController::class, 'store'])->name('bvn.verification.store');
        Route::get('/standardBVN/{id}', [BvnverificationController::class, 'standardBVN'])->name("standardBVN");
        Route::get('/premiumBVN/{id}', [BvnverificationController::class, 'premiumBVN'])->name("premiumBVN");
        Route::get('/plasticBVN/{id}', [BvnverificationController::class, 'plasticBVN'])->name("plasticBVN");
    });

    /*
    |--------------------------------------------------------------------------
    | NIN Demographic Verification
    |--------------------------------------------------------------------------
    */
    Route::prefix('nin-demo-verification')->group(function () {
        Route::get('/', [NinDemoVerificationController::class, 'index'])->name('nin.demo.index');
        Route::post('/', [NinDemoVerificationController::class, 'store'])->name('nin.demo.store');
        Route::get('/freeSlip/{id}', [NinDemoVerificationController::class, 'freeSlip'])->name('nin.demo.freeSlip');
        Route::get('/regularSlip/{id}', [NinDemoVerificationController::class, 'regularSlip'])->name('nin.demo.regularSlip');
        Route::get('/vninSlip/{id}', [NinDemoVerificationController::class, 'vninSlip'])->name('nin.demo.vninSlip');
    });

    /*
    |--------------------------------------------------------------------------
    | NIN Phone Verification
    |--------------------------------------------------------------------------
    */
    Route::prefix('nin-phone-verification')->group(function () {
        Route::get('/', [NinPhoneVerificationController::class, 'index'])->name('nin.phone.index');
        Route::post('/', [NinPhoneVerificationController::class, 'store'])->name('nin.phone.store');
        Route::get('/regularSlip/{id}', [NinPhoneVerificationController::class, 'regularSlip'])->name('nin.phone.regularSlip');
        Route::get('/standardSlip/{id}', [NinPhoneVerificationController::class, 'standardSlip'])->name('nin.phone.standardSlip');
        Route::get('/premiumSlip/{id}', [NinPhoneVerificationController::class, 'premiumSlip'])->name('nin.phone.premiumSlip');
        Route::get('/vninSlip/{id}', [NinPhoneVerificationController::class, 'vninSlip'])->name('nin.phone.vninSlip');
    });


    /*
    |--------------------------------------------------------------------------
    | API Dashboard & Applications
    |--------------------------------------------------------------------------
    */
    Route::prefix('api-dashboard')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApiDashboardController::class, 'index'])->name('api.dashboard');
        Route::post('/apply', [\App\Http\Controllers\Api\ApiDashboardController::class, 'apply'])->name('api.apply');
    });

    // Buy Website Service
    Route::get('/buy-website', [WebsiteController::class, 'index'])->name('website.index');

    // Referral System
    Route::get('/referrals', [App\Http\Controllers\ReferralController::class, 'index'])->name('referrals.index');

    // AI Comment Routes
    Route::prefix('ai')->middleware('throttle:10,1')->group(function () {
        Route::post('/summarize', [AiCommentController::class, 'summarize'])->name('ai.summarize');
        Route::post('/ask', [AiCommentController::class, 'ask'])->name('ai.ask');
        Route::post('/chat', [AiCommentController::class, 'chat'])->name('ai.chat');
        Route::get('/history', [AiCommentController::class, 'fetchHistory'])->name('ai.history');
    });

    // Gift Card Routes
    Route::prefix('gift-cards')->name('gift-card.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Action\GiftCardController::class, 'index'])->name('index');
        Route::get('/generate', [\App\Http\Controllers\Action\GiftCardController::class, 'create'])->name('generate');
        Route::post('/store', [\App\Http\Controllers\Action\GiftCardController::class, 'store'])->name('store');
        Route::get('/redeem', [\App\Http\Controllers\Action\GiftCardController::class, 'redeemPage'])->name('redeem');
        Route::post('/redeem', [\App\Http\Controllers\Action\GiftCardController::class, 'processRedeem'])->name('processRedeem');
        Route::get('/code/{giftCard}', [\App\Http\Controllers\Action\GiftCardController::class, 'showCode'])->name('showCode');
        Route::get('/thankyou', [\App\Http\Controllers\Action\GiftCardController::class, 'thankYou3'])->name('thankyou3');
    });

    // Wallet Withdrawal Routes
    Route::prefix('withdraw')->name('withdraw.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Action\WithdrawController::class, 'index'])->name('index');
        Route::get('/sync-banks', [\App\Http\Controllers\Action\WithdrawController::class, 'syncBanks'])->name('syncBanks');
        Route::post('/verify-account', [\App\Http\Controllers\Action\WithdrawController::class, 'verifyAccount'])->name('verifyAccount');
        Route::post('/process', [\App\Http\Controllers\Action\WithdrawController::class, 'processWithdrawal'])->name('process');
    });

});


require __DIR__ . '/auth.php';
