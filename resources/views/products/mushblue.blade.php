@extends('layouts.vitrine')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('images/vitrine/products/mushblue.png') }}" alt="MushBlue" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h1 class="mb-4">MushBlue</h1>
            <h2 class="h4 text-muted mb-4">Complément à base d'extrait de champignon</h2>
            
            <div class="mb-4">
                <h3 class="h5">Description</h3>
                <p>MushBlue est un complément alimentaire innovant à base d'extrait de champignon, spécialement conçu pour améliorer votre bien-être quotidien.</p>
            </div>
            
            <div class="mb-4">
                <h3 class="h5">Bienfaits</h3>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i> Renforce le système immunitaire</li>
                    <li><i class="fas fa-check text-success me-2"></i> Améliore la vitalité</li>
                    <li><i class="fas fa-check text-success me-2"></i> Source naturelle d'antioxydants</li>
                    <li><i class="fas fa-check text-success me-2"></i> Favorise un bon équilibre énergétique</li>
                </ul>
            </div>
            
            <div class="mb-4">
                <h3 class="h5">Composition</h3>
                <p>Extrait de champignon médicinal, vitamines et minéraux essentiels.</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ asset('pdf/mushblue.pdf') }}" class="btn btn-success" download>
                    <i class="fas fa-download me-2"></i>Télécharger la fiche PDF
                </a>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center mb-4">Informations complémentaires</h3>
            <div class="accordion" id="mushblueAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            Mode d'emploi
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#mushblueAccordion">
                        <div class="accordion-body">
                            Prendre 1 à 2 gélules par jour avec un grand verre d'eau, de préférence pendant les repas.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                            Précautions d'emploi
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#mushblueAccordion">
                        <div class="accordion-body">
                            Ne pas dépasser la dose journalière recommandée. Ce complément alimentaire ne peut se substituer à une alimentation variée et équilibrée et à un mode de vie sain.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                            Conservation
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#mushblueAccordion">
                        <div class="accordion-body">
                            À conserver dans un endroit frais et sec, à l'abri de la lumière. Tenir hors de portée des enfants.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.accordion-button:not(.collapsed) {
    background-color: #198754;
    color: white;
}
</style>
@endsection 