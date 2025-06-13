@extends('layouts.vitrine')

@section('title', 'Nos Produits - NaturaCorp')

@section('content')
<!-- Hero section -->

<div class="container py-5">
    <!-- MushBlue Section -->
    <section class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('images/vitrine/products/mushblue.png') }}" alt="MushBlue" class="img-fluid rounded shadow-lg">
            </div>
            <div class="col-lg-6">
                <h2 class="h1 mb-4">MushBlue</h2>
                <p class="lead mb-4">Notre complément alimentaire phare à base d'extrait de champignon bleu, alliant tradition et innovation pour votre santé.</p>
                
                <div class="benefits mb-4">
                    <h3 class="h4 mb-3">Bienfaits principaux :</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="benefit-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Renforce le système immunitaire</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Propriétés antioxydantes</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Améliore la vitalité</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>100% naturel</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('products.mushblue') }}" class="btn btn-success btn-lg mb-3">
                        <i class="fas fa-info-circle me-2"></i>En savoir plus sur MushBlue
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-success btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contactez-nous
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pourquoi choisir MushBlue -->
    <section class="bg-light py-5 rounded">
        <div class="container">
            <h2 class="text-center mb-5">Pourquoi choisir MushBlue ?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-microscope text-success fa-2x mb-3"></i>
                            <h3 class="h5">Recherche scientifique</h3>
                            <p>Développé en collaboration avec des experts en mycologie et nutrition.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt text-success fa-2x mb-3"></i>
                            <h3 class="h5">Qualité garantie</h3>
                            <p>Processus de fabrication rigoureux et contrôles qualité stricts.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-heart text-success fa-2x mb-3"></i>
                            <h3 class="h5">Satisfaction client</h3>
                            <p>Des milliers de clients satisfaits et des retours positifs constants.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extra_css')
<style>
.hero {
    position: relative;
    width: 100%;
    margin-top: 76px;
}

.hero-image {
    position: relative;
    width: 100%;
    line-height: 0;
}

.hero-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1;
}

.hero-content {
    position: absolute;
    top: 50%;
    width: 100%;
    padding: 2rem;
    transform: translateY(-50%);
    z-index: 2;
}

.benefit-item {
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection 