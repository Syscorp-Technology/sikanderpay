<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BenificiaryController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FileImportController;
use App\Http\Controllers\GatewayCategoryController;
use App\Http\Controllers\ImageToTextController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InternalTransferController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\ExpenseCategoryController;
use App\Http\Controllers\Master\LeadSource;
use App\Http\Controllers\Master\MasterSetting;
use App\Http\Controllers\Master\PaymentModeController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\PlatFormController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\OurBankDetailController;
use App\Http\Controllers\Payment\DepositController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\WithdrawController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\UserRegister\UserRegistrationController;
use App\Http\Controllers\UserReport\UserReportController;
use App\Models\deposit;
use App\Models\IncomeAndExpense;
use App\Models\InternalTransfer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route('/run-migration',function(){
// Artisan::call('optimize:clear');
// });
Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard')->middleware('admin');



// Login controller
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [LoginController::class, 'login'])->name('login');
    Route::post('post-login', [LoginController::class, 'postLogin'])->name('login.post');
    // Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');

});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// End login
Route::get('/test', function () {
    $deposits = Deposit::with(['platformDetail.player', 'user'])->take(5)->get();
    dd($deposits);
});


Route::get('forget-password', [LoginController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [LoginController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [LoginController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::get('/success-alert', function () {
    return view('components.success-alert');
})->name('success-alert');

Route::middleware(['admin'])->group(function () {


    Route::get('/maintenance', function () {
        return view('maintenance');
    })->name('maintenance');
    // Lead controller
    Route::get('/lead-index', [LeadSource::class, 'index'])->name('lead.index');
    Route::get('/lead-create', [LeadSource::class, 'create'])->name('lead.create');
    Route::post('/lead-store', [LeadSource::class, 'store'])->name('lead.store');
    Route::get('/lead-edit/{id}', [LeadSource::class, 'edit'])->name('lead.edit');
    Route::put('/lead-update/{id}', [LeadSource::class, 'update'])->name('lead.update');
    Route::delete('/lead-delete/{id}', [LeadSource::class, 'delete'])->name('lead.delete');
    // end lead controller

    // platform controller
    Route::get('/platform-index', [PlatFormController::class, 'index'])->name('platform.index');
    Route::get('/platform-create', [PlatFormController::class, 'create'])->name('platform.create');
    Route::post('/platform-store', [PlatFormController::class, 'store'])->name('platform.store');
    Route::get('/platform-edit/{id}', [PlatFormController::class, 'edit'])->name('platform.edit');
    Route::put('/platform-update/{id}', [PlatFormController::class, 'update'])->name('platform.update');
    Route::delete('/platform-delete/{id}', [PlatFormController::class, 'delete'])->name('platform.delete');
    // end platform controller

    // branch controller
    Route::get('/branch-index', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/branch-create', [BranchController::class, 'create'])->name('branch.create');
    Route::post('/branch-store', [BranchController::class, 'store'])->name('branch.store');
    Route::get('/branch-edit/{id}', [BranchController::class, 'edit'])->name('branch.edit');
    Route::put('/branch-update/{id}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('/branch-delete/{id}', [BranchController::class, 'delete'])->name('branch.delete');
    // end branch controller

    // our bank controller
    Route::get('/ourbankdetail-index', [OurBankDetailController::class, 'index'])->name('ourbankdetail.index');
    Route::get('/ourbankdetail-create', [OurBankDetailController::class, 'create'])->name('ourbankdetail.create');
    Route::post('/ourbankdetail-store', [OurBankDetailController::class, 'store'])->name('ourbankdetail.store');
    Route::get('/ourbankdetail-edit/{id}', [OurBankDetailController::class, 'edit'])->name('ourbankdetail.edit');
    Route::put('/ourbankdetail-update/{id}', [OurBankDetailController::class, 'update'])->name('ourbankdetail.update');
    Route::delete('/ourbankdetail-delete/{id}', [OurBankDetailController::class, 'delete'])->name('ourbankdetail.delete');

    Route::get('/our-bank-income/{id}', [OurBankDetailController::class, 'ourBankIncome'])->name('our-bank.income');
    Route::get('/our-bank-all-incomes', [OurBankDetailController::class, 'ourBankAllIncome'])->name('our-bank.all-income');
    Route::get('/our-bank-expense/{id}', [OurBankDetailController::class, 'ourBankExpense'])->name('our-bank.expense');
    Route::get('/our-bank-all-expense', [OurBankDetailController::class, 'ourBankAllExpense'])->name('our-bank.all-expense');
    Route::get('/our-bank-internalTransfer-sender/{id}', [OurBankDetailController::class, 'ourBankInternalTransferSender'])->name('our-bank.internal-transfer-sender');
    Route::get('/our-bank-all-internal-bank-sender', [OurBankDetailController::class, 'allInternalTransferSenderReports'])->name('our-bank.all-internal-sender');
    Route::get('/our-bank-all-internal-bank-reciver', [OurBankDetailController::class, 'allInternalTransferReciverReports'])->name('our-bank.all-internal-reciver');

    // our-bank.internal-transfer-recive
    Route::get('/our-bank-internalTransfer-reciver/{id}', [OurBankDetailController::class, 'ourBankInternalTransferReciver'])->name('our-bank.internal-transfer-reciver');

    Route::get('/our-bank-deposit/{id}', [OurBankDetailController::class, 'ourBankDeposit'])->name('our-bank.deposit');
    Route::get('/our-bank-all-deposit', [OurBankDetailController::class, 'ourBankAllDeposit'])->name('our-bank.all-deposit');

    Route::get('/our-bank-withdraw/{id}', [OurBankDetailController::class, 'ourBankWithdraw'])->name('our-bank.withdraw');

    Route::get('/our-bank-all-withdraw', [OurBankDetailController::class, 'ourBankAllWithdraw'])->name('our-bank.all-withdraw');

    // end bank controller

    //category Contoller
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category-create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category-store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category-edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::patch('/category-update/{id}', [CategoryController::class, 'update'])->name('category.update');

    //Expense category Contoller
    Route::get('/expense-category', [ExpenseCategoryController::class, 'index'])->name('expense.category.index');
    Route::get('/expense-category-create', [ExpenseCategoryController::class, 'create'])->name('expense.category.create');
    Route::post('/expense-category-store', [ExpenseCategoryController::class, 'store'])->name('expense.category.store');
    Route::get('/expense-category-edit/{id}', [ExpenseCategoryController::class, 'edit'])->name('expense.category.edit');
    Route::patch('/expense-category-update/{id}', [ExpenseCategoryController::class, 'update'])->name('expense.category.update');

    //paymentMode controller
    Route::get('/payment-modes', [PaymentModeController::class, 'index'])->name('payment-mode.index');
    Route::get('/payment-modes-create', [PaymentModeController::class, 'create'])->name('payment-mode.create');
    Route::post('/payment-modes-store', [PaymentModeController::class, 'store'])->name('payment-modes.store');
    Route::get('/payment-modes-edit/{id}', [PaymentModeController::class, 'edit'])->name('payment-mode.edit');
    Route::patch('/payment-modes-update/{id}', [PaymentModeController::class, 'update'])->name('payment-mode.update');

    //income
    Route::get('/incomes', [IncomeController::class, 'index'])->name('income.index');
    Route::get('/income-create', [IncomeController::class, 'create'])->name('income.create');
    Route::post('/income-store', [IncomeController::class, 'store'])->name('income.store');
    Route::post('/incomes-banker-status}', [IncomeController::class, 'bankerStatus'])->name('income.banker.status');
    Route::get('/incomes-report', [IncomeController::class, 'incomeReport'])->name('income.report');
    Route::get('/all-incomes-report', [IncomeController::class, 'allIncomeReports'])->name('all-income.report');
    Route::get('/export-all-income-results', [IncomeController::class, 'exportAllIncomeResults']);
    Route::get('/income/edit/{id}',[IncomeController::class,'edit'])->name('income.edit');
    Route::post('/income/update/{id}',[IncomeController::class,'update'])->name('income.update');


    //expense
    Route::get('expense', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('expense-create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('expense-store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::post('expense-status', [ExpenseController::class, 'expenseStatus'])->name('expense.status');
    Route::get('/expense-report', [ExpenseController::class, 'expenseReport'])->name('expense.report');
    Route::get('/all-expense-report', [ExpenseController::class, 'allExpenseReports'])->name('all-expense.report');
    Route::get('/export-all-expense-results', [ExpenseController::class, 'exportAllExpenseResults']);
    Route::get('/expense/edit/{id}',[ExpenseController::class,'edit'])->name('expense.edit');
    Route::post('/expense/update/{id}',[ExpenseController::class,'update'])->name('expense.update');

    //Internal Transfer
    Route::get('/internal-Transfer', [InternalTransferController::class, 'index'])->name('InternalTransfer.index');
    Route::get('/internal-Transfer-create', [InternalTransferController::class, 'create'])->name('InternalTransfer.create');
    Route::post('/internal-Transfer-store', [InternalTransferController::class, 'store'])->name('internalTransfer.store');
    Route::post('/internal-Transfer-status', [InternalTransferController::class, 'transferStatus'])->name('internalTransfer.status');

    Route::get('/internal-Transfer-report', [InternalTransferController::class, 'internalTransferReport'])->name('internalTransfer.report');
    Route::get('/all-internal-transfer-report', [InternalTransferController::class, 'allInternalTransferReports'])->name('all-internal-transfer.report');
    Route::get('/export-all-internal-transfer-results', [InternalTransferController::class, 'exportAllInternalTransferResults']);
    Route::get('/internal-Transfer/edit/{id}',[InternalTransferController::class,'edit'])->name('internalTransfer.edit');
    Route::post('/internal-Transfer/update/{id}',[InternalTransferController::class,'update'])->name('internalTransfer.update');


     //gateway category Controller
     Route::get('/gateway-category', [GatewayCategoryController::class, 'index'])->name('gateway-category.index');
     Route::get('/gateway-category-create', [GatewayCategoryController::class, 'create'])->name('gateway-category.create');
     Route::post('/gateway-category-store', [GatewayCategoryController::class, 'store'])->name('gateway-category.store');
     Route::get('/gateway-category-edit/{id}', [GatewayCategoryController::class, 'edit'])->name('gateway-category.edit');
     Route::patch('/gateway-category-update/{id}', [GatewayCategoryController::class, 'update'])->name('gateway-category.update');


     Route::post('/get-gateway-banks',[WithdrawController::class,'getGatewayBanks'])->name('get-gateway-banks');
    //razorpay
    // Route::post('gateway-make-payout',[WithdrawController::class,'gatewayMakePayout'])->name('gateway.make.payout');

     Route::post('gateway-utr-get',[WithdrawController::class,'getUtr'])->name('gateway.utr-get');
     Route::post('gateway-status-get',[WithdrawController::class,'razorpayStatus'])->name('gateway.status-get');
    //benificiary

    Route::get('/benificiary', [BenificiaryController::class, 'index'])->name('benificiary.index');
    Route::get('/benificiary-report', [BenificiaryController::class, 'benificiaryReport'])->name('benificiary.report');
    Route::post('/benificiary-update', [BenificiaryController::class, 'benificiaryUpdate']);
    Route::post('/benificiary-show', [BenificiaryController::class, 'benificiaryShow']);
    Route::post('/benificiary-banks-fetch', [BenificiaryController::class, 'benificiaryBanksFetch']);

    // user register
    Route::get('/UserRegister-index', [UserRegistrationController::class, 'index'])->name('UserRegister.index');
    Route::get('/UserRegister-create', [UserRegistrationController::class, 'create'])->name('UserRegister.create');
    Route::post('/UserRegister-store', [UserRegistrationController::class, 'store'])->name('UserRegister.store');
    Route::get('/UserRegister-edit/{id}', [UserRegistrationController::class, 'edit'])->name('UserRegister.edit');
    Route::post('/edit-platform-details', [UserRegistrationController::class, 'editPlatformDetails'])->name('edit-platform-details');

    Route::post('/UserRegister-update/{id}', [UserRegistrationController::class, 'update'])->name('UserRegister.update');
    Route::delete('/UserRegister-delete/{id}', [UserRegistrationController::class, 'delete'])->name('UserRegister.delete');
    Route::get('/platform_selected', [UserRegistrationController::class, 'platform_selected'])->name('platform_selected');
    Route::get('/get-player-details/{playerId}', [UserRegistrationController::class, 'getUserDetails'])->name('get-player-details');
    Route::post('/ajax-validate-mobile', [UserRegistrationController::class, 'validateMobile'])->name('ajax.validate.mobile');
    Route::post('/ajax-validate-utr', [UserRegistrationController::class, 'validateUTR'])->name('ajax.validate.utr');
    Route::get('/all-players-data', [UserRegistrationController::class, 'allPlayersData'])->name('allPlayersData');
    //Player Upload

    Route::get('/player-files', [FileImportController::class, 'playerFile'])->name('player-files');
    Route::get('/player-files/upload', [FileImportController::class, 'playerUpload'])->name('player-files.upload');
    Route::post('/upload-player-files', [FileImportController::class, 'playerStore'])->name('player_files.store');

    // Payment
    Route::get('/payment-index', [PaymentController::class, 'index'])->name('payment.index');

    Route::post('/convert-image-to-text', [DepositController::class, 'convert'])->name('image-to-text');

    // Deposit
    Route::get('/all-deposit-datas', [DepositController::class, 'allDepositDatas'])->name('all_deposit.datas');
    Route::get('/deposit-index', [DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit-create', [DepositController::class, 'create'])->name('deposit.create');
    Route::post('/deposit-store', [DepositController::class, 'store'])->name('deposit.store');
    Route::get('/deposit-edit/{id}', [DepositController::class, 'edit'])->name('deposit.edit');
    Route::post('/deposit-update/{id}', [DepositController::class, 'update'])->name('deposit.update');
    Route::delete('/deposit-delete/{id}', [DepositController::class, 'delete'])->name('deposit.delete');
    Route::get('/deposit-show/{id}', [DepositController::class, 'show'])->name('deposit.show');
    Route::post('/admin_status', [DepositController::class, 'admin_status'])->name('admin_status');
    Route::post('/platform_detail_active', [DepositController::class, 'platform_detail_active'])->name('platform_detail_active');
    Route::post('/check_platform_detail_exist', [DepositController::class, 'check_platform_detail_exist'])->name('check_platform_detail_exist');
    Route::get('/get-merged-platforms/{user}', [DepositController::class, 'getMergedPlatforms'])->name('getPlatforms');
    Route::get('/get-all-platforms', [DepositController::class, 'getAllPlatforms'])->name('getPlatformsgetAllPlatforms');
    Route::get('/check-utr', [DepositController::class, 'checkUtr'])->name('checkUtr');
    Route::get('/deposit-index/pending', [DepositController::class, 'depositPending'])->name('deposit.pending');
    Route::get('/deposit-index/pending-cc', [DepositController::class, 'depositPendingcc'])->name('deposit.pendingcc');
    Route::get('/get-platform-details', [DepositController::class, 'getPlatformDetails'])->name('getPlatformDetails');
    Route::get('/get-platform-user-details', [DepositController::class, 'getPlatformUserDetails'])->name('getPlatformUserDetails');
    Route::get('/get-seleted-platform', [DepositController::class, 'getSeletectPlatform'])->name('getSeletedPlatform');

    Route::get('/deposit-index/completed', [DepositController::class, 'depositCompleted'])->name('deposit.completed');
    Route::get('/get_user_info/{userId}', [DepositController::class, 'get_user_info'])->name('get_user_info');

    // Route::delete('/deposit-deletes/{id}', [DepositController::class, 'depositdelete'])->name('deposits.delete');
    Route::delete('/delete-item/{id}', [DepositController::class, 'deleteItem'])->name('delete-item');


    Route::put('/deposit-update/{id}', [DepositController::class, 'status_update'])->name('status_update');


    // Deposit Report
    Route::get('/deposit-report', [DepositController::class, 'report'])->name('deposit.report');
    Route::post('/filter-deposits', [DepositController::class, 'filter'])->name('filter.deposits');
    Route::get('/all-deposit-reports', [DepositController::class, 'allDepositReports'])->name('all_deposit.reports');
    Route::get('/deposit-excel-report', [DepositController::class, 'depositExcelReport'])->name('deposit.excel_report');
    Route::get('/export-all-search-results', [DepositController::class, 'exportAllSearchResults']);
    // Withdraw Report
    Route::get('/withdraw-report', [WithdrawController::class, 'report'])->name('withdraw.report');
    Route::post('/filter-withdraw', [WithdrawController::class, 'filter'])->name('filter.withdraw');
    Route::get('/all-withdraw-reports', [WithdrawController::class, 'allWithdrawReports'])->name('all_withdraw.reports');
    Route::get('/withdraw-excel-report', [WithdrawController::class, 'withdrawExcelReport'])->name('withdraw.excel_report');
    Route::get('/export-all-withdraw-results', [WithdrawController::class, 'exportAllWithdrawResults']);


    // Withdraw
    Route::get('/withdraw-index', [WithdrawController::class, 'index'])->name('withdraw.index');
    Route::get('/withdraw-index/pending', [WithdrawController::class, 'withdrawPending'])->name('withdraw.pending');
    Route::get('/withdraw-index/pending-cc', [WithdrawController::class, 'withdrawPendingcc'])->name('withdraw.pendingcc');

    Route::get('/all-withdraw-datas', [WithdrawController::class, 'allWithdrawDatas'])->name('all_withdraw.datas');
    Route::get('/withdraw-razorpay/pending', [WithdrawController::class, 'withdrawRazorpayPending'])->name('withdraw.gateway.pending');
    Route::get('/withdraw-create', [WithdrawController::class, 'create'])->name('withdraw.create');
    Route::post('/fetch-banks', [WithdrawController::class, 'fetchBanks']);
    Route::post('/withdraw_status', [WithdrawController::class, 'withdraw_status'])->name('withdraw_status');
    Route::post('/submit-form', [WithdrawController::class, 'submitForm']);


    Route::post('/fetch-bank-details', [WithdrawController::class, 'fetchBankDetails']);
    Route::post('/withdraw-store', [WithdrawController::class, 'store'])->name('withdraw.store');
    Route::get('/withdraw-edit/{id}', [WithdrawController::class, 'edit'])->name('withdraw.edit');
    Route::post('/withdraw-/{id}', [WithdrawController::class, 'update'])->name('withdraw.update');
    Route::delete('/withdraw-delete/{id}', [WithdrawController::class, 'delete'])->name('withdraw.delete');
    Route::post('/submit-utrform', [WithdrawController::class, 'submitutrForm']);

    Route::post('/withdraw-assigned-to',[WithdrawController::class,'assignedTo'])->name('withdraw.assigned-to');
    Route::post('/deposit-assigned-to',[DepositController::class,'assignedTo'])->name('deposit.assigned-to');



    //feedback
    Route::post('/client/note', [FeedbackController::class, 'storeNote'])->name('client.note');
    Route::get('/get-note/{id}', [FeedbackController::class, 'getClientNote'])->name('client.get_note');

    //Report
    Route::get('/userReport-list', [ReportController::class, 'userReport'])->name('userReport.list');
    Route::get('/paymentReport-list', [ReportController::class, 'paymentReport'])->name('paymentReport.list');

    // master settings
    Route::get('/master-setting', [MasterSetting::class, 'setting'])->name('master.setting');


    // permission
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/permission-create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/permission-store', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/permission-edit/{id}', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/permission-update/{id}', [PermissionController::class, 'update'])->name('permission.update');
    Route::get('/permission-delete/{id}', [PermissionController::class, 'delete'])->name('permission.delete');

    //Role

    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role-create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/role-store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/role-edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/role-update/{id}', [RoleController::class, 'update'])->name('role.update');

    // Route::get('/role-delete/{id}', [RoleController::class, 'delete'])->name('role.delete');

    // User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user-create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user-store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user-edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user-update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user-delete/{id}',  [UserController::class, 'delete'])->name('user.delete');

    // Route::get('/user-delete/{id}', [UserController::class, 'delete'])->name('user.delete');
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear Config cache:
Route::get('/storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return '<h1>Storage linked</h1>';
});

//Schedule List:
Route::get('/schedule-list', function () {
    $exitCode = Artisan::call('schedule:list');
    return $exitCode;
});
