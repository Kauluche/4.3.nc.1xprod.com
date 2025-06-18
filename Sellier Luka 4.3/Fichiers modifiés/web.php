<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PharmacyApiController;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::resource('notifications', NotificationController::class);
    Route::patch('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes commerciales
    Route::prefix('commercial')->name('commercial.')->group(function () {
        Route::resource('orders', \App\Http\Controllers\Commercial\OrderController::class);
    });

    // Routes pour les notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{notification}/approve', [NotificationController::class, 'approve'])->name('notifications.approve');
    Route::post('/notifications/{notification}/reject', [NotificationController::class, 'reject'])->name('notifications.reject');
});
Route::get('/test-role', function () {
    return "Middleware role fonctionne !";
})->middleware('role:admin');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('reports', ReportController::class);
    Route::post('/zones/swap', [ZoneController::class, 'swap'])->name('zones.swap');
    
    // Routes pour les exports CSV des rapports
    Route::get('/export/pharmacies-by-zone', [ExportController::class, 'exportAllPharmaciesByZone'])->name('export.pharmacies-by-zone');
    Route::get('/export/pharmacies-by-zone/{zone}', [ExportController::class, 'exportPharmaciesByZone'])->name('export.pharmacies-by-zone.show');
    Route::get('/export/commercials-performance', [ExportController::class, 'exportAllCommercialsPerformance'])->name('export.commercials-performance');
    Route::get('/export/commercials-performance/{commercial}', [ExportController::class, 'exportCommercialPerformance'])->name('export.commercials-performance.show');
});

// Routes accessibles uniquement aux commerciaux
Route::middleware(['auth', 'role:commercial'])->group(function () {
    // Route de recherche avant le resource pour éviter les conflits
    Route::get('/pharmacies/search', [App\Http\Controllers\PharmacyController::class, 'search'])->name('pharmacies.search');
    
    Route::resource('pharmacies', App\Http\Controllers\PharmacyController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('notifications', NotificationController::class);
    
    // Routes pour les exports CSV du tableau de bord commercial
    Route::get('/export/sales', [ExportController::class, 'exportCommercialSales'])->name('export.sales');
    Route::get('/export/clients', [ExportController::class, 'exportCommercialClients'])->name('export.clients');
    Route::get('/export/recent-sales', [ExportController::class, 'exportCommercialRecentSales'])->name('export.recent-sales');
});

// Routes du site vitrine
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produits', [ProductController::class, 'index'])->name('products');
Route::get('/produits/mushblue', [ProductController::class, 'mushblue'])->name('products.mushblue');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::resource('blog', BlogController::class)->only(['index', 'show']);
Route::get('/entreprise', [EnterpriseController::class, 'index'])->name('enterprise');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/politique-confidentialite', [PrivacyController::class, 'index'])->name('privacy');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Route d'accès au CRM
Route::get('/crm', function () {
    return redirect('/crm/login');
})->name('crm');

// Routes du CRM (préfixées par /crm)
Route::prefix('crm')->group(function () {
    require __DIR__.'/auth.php';
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('crm.dashboard');
        
        // Routes pour les commerciaux
        Route::middleware(['role:commercial'])->group(function () {
            Route::resource('pharmacies', App\Http\Controllers\PharmacyController::class)->names([
                'index' => 'crm.pharmacies.index',
                'create' => 'crm.pharmacies.create',
                'store' => 'crm.pharmacies.store',
                'show' => 'crm.pharmacies.show',
                'edit' => 'crm.pharmacies.edit',
                'update' => 'crm.pharmacies.update',
                'destroy' => 'crm.pharmacies.destroy'
            ]);
            Route::get('/pharmacies/find', [App\Http\Controllers\PharmacyController::class, 'find'])->name('crm.pharmacies.find');
            Route::resource('orders', App\Http\Controllers\OrderController::class)->names('crm.orders');
            Route::resource('documents', App\Http\Controllers\DocumentController::class)->names('crm.documents');
            Route::get('/documents/generate-invoice/{order}', [App\Http\Controllers\DocumentController::class, 'generateInvoice'])->name('crm.documents.generate-invoice');
            Route::get('/documents/generate-quote/{pharmacy}', [App\Http\Controllers\DocumentController::class, 'generateQuote'])->name('crm.documents.generate-quote');
            Route::get('/documents/generate-delivery-note/{order}', [App\Http\Controllers\DocumentController::class, 'generateDeliveryNote'])->name('crm.documents.generate-delivery-note');
            Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('crm.documents.download');
            Route::get('/documents/{document}/preview', [App\Http\Controllers\DocumentController::class, 'preview'])->name('crm.documents.preview');
            Route::resource('notifications', App\Http\Controllers\NotificationController::class)->names('crm.notifications');
            Route::post('/notifications/{notification}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('crm.notifications.markAsRead');
            Route::post('/notifications/{notification}/approve', [App\Http\Controllers\NotificationController::class, 'approve'])->name('crm.notifications.approve');
            Route::post('/notifications/{notification}/reject', [App\Http\Controllers\NotificationController::class, 'reject'])->name('crm.notifications.reject');
        });
        
        // Routes pour les administrateurs
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            Route::resource('users', App\Http\Controllers\Admin\UserController::class)->names('crm.admin.users');
            Route::resource('zones', App\Http\Controllers\Admin\ZoneController::class)->names('crm.admin.zones');
            Route::resource('reports', App\Http\Controllers\Admin\ReportController::class)->names('crm.admin.reports');
            Route::resource('notifications', App\Http\Controllers\NotificationController::class)->names('crm.admin.notifications');
            Route::post('/notifications/{notification}/approve', [App\Http\Controllers\NotificationController::class, 'approve'])->name('crm.admin.notifications.approve');
            Route::post('/notifications/{notification}/reject', [App\Http\Controllers\NotificationController::class, 'reject'])->name('crm.admin.notifications.reject');
        });
    });
});

// Routes API temporaires
Route::prefix('api')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });

    Route::get('/commercial/pharmacies', [PharmacyApiController::class, 'getCommercialPharmacies'])
        ->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});