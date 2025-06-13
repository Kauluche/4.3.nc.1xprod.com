@extends('layouts.vitrine')

@section('title', 'Notre Entreprise - NaturaCorp')

@section('content')
<!-- Hero section -->
<div class="hero">
    <div class="hero-image">
        <img src="{{ asset('images/vitrine/hero/entreprise.jpg') }}" alt="NaturaCorp entreprise" class="w-100" style="height: 400px; object-fit: cover;">
    </div>
    <div class="hero-content position-absolute start-50 translate-middle-x text-center text-white">
        <h1 class="display-4 mb-3">NaturaCorp entreprise</h1>
        <h2 class="h2 mb-3">Notre Histoire</h2>
        <p class="lead">L'innovation naturelle au service de votre santé depuis 2020</p>
    </div>
</div>

<div class="container py-5">
    <!-- Notre Mission -->
    <section class="mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">Notre Mission</h2>
                <p class="lead">Chez NaturaCorp, nous croyons en la puissance de la nature pour améliorer la santé et le bien-être. Notre mission est de développer des produits innovants qui combinent les bienfaits des ingrédients naturels avec la recherche scientifique moderne.</p>
            </div>
        </div>
    </section>

    <!-- Nos Valeurs -->
    <section class="mb-5">
        <h2 class="text-center mb-5">Nos Valeurs</h2>
        <div class="row g-4">
            @foreach($values as $value)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="value-icon mb-3">
                                <i class="fas fa-{{ $value['icon'] }} fa-2x text-success"></i>
                            </div>
                            <h3 class="h5 mb-3">{{ $value['title'] }}</h3>
                            <p class="mb-0">{{ $value['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Certifications -->
    <section class="mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-4">Nos Certifications</h2>
                <div class="row justify-content-center">
                    @foreach($certifications as $certification)
                        <div class="col-md-6">
                            <div class="certification-item d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>{{ $certification }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Engagement Environnemental -->
    <section>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h2 class="h3 mb-4">Notre Engagement Environnemental</h2>
                        <p>Nous nous engageons à minimiser notre impact environnemental à travers :</p>
                        <div class="row g-4 mt-3">
                            <div class="col-md-4">
                                <i class="fas fa-recycle text-success mb-3 fa-2x"></i>
                                <h4 class="h6">Emballages Recyclables</h4>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-leaf text-success mb-3 fa-2x"></i>
                                <h4 class="h6">Production Responsable</h4>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-solar-panel text-success mb-3 fa-2x"></i>
                                <h4 class="h6">Énergie Verte</h4>
                            </div>
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

.hero-image img {
    vertical-align: middle;
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

.value-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: rgba(25, 135, 84, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.certification-item {
    padding: 1rem;
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