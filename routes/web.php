<?php

use App\Http\Controllers\GuaranteeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Projet Assurance / Garantie — Routes publiques
| Accès uniquement via le lien signé envoyé par email
|--------------------------------------------------------------------------
*/
Route::prefix('guarantee')->name('guarantee.')->group(function () {
    Route::get('/{token}', [GuaranteeController::class, 'show'])->name('show');
    Route::post('/{token}/pay', [GuaranteeController::class, 'pay'])->name('pay');
    Route::get('/{token}/questionnaire', [GuaranteeController::class, 'questionnaire'])->name('questionnaire');
    Route::post('/{token}/questionnaire', [GuaranteeController::class, 'processQuestionnaire'])->name('process-questionnaire');
    Route::get('/{token}/success', [GuaranteeController::class, 'success'])->name('success');
    Route::get('/{token}/expired', [GuaranteeController::class, 'expired'])->name('expired');
});
