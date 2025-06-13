@extends('layouts.vitrine')

@section('title', 'Politique de Confidentialité - NaturaCorp')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-5">Politique de Confidentialité</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Collecte des informations</h2>
                    <p>Nous collectons les informations suivantes :</p>
                    <ul>
                        <li>Nom et prénom</li>
                        <li>Adresse email</li>
                        <li>Numéro de téléphone</li>
                        <li>Adresse postale</li>
                    </ul>
                    <p>Les informations personnelles que nous collectons sont recueillies au travers de formulaires et grâce à l'interactivité établie entre vous et notre site Web.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Formulaires et interactivité</h2>
                    <p>Vos informations personnelles sont collectées par le biais de formulaires, à savoir :</p>
                    <ul>
                        <li>Formulaire d'inscription à la newsletter</li>
                        <li>Formulaire de contact</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Droit d'opposition et de retrait</h2>
                    <p>Nous nous engageons à vous offrir un droit d'opposition et de retrait quant à vos renseignements personnels. Le droit d'opposition s'entend comme étant la possibilité offerte aux internautes de refuser que leurs renseignements personnels soient utilisés à certaines fins mentionnées lors de la collecte.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Droit d'accès</h2>
                    <p>Nous nous engageons à reconnaître un droit d'accès et de rectification aux personnes concernées désireuses de consulter, modifier, voire radier les informations les concernant.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">Sécurité</h2>
                    <p>Les renseignements personnels que nous collectons sont conservés dans un environnement sécurisé. Les personnes travaillant pour nous sont tenues de respecter la confidentialité de vos informations.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Cookies</h2>
                    <p>Nous utilisons les cookies pour :</p>
                    <ul>
                        <li>Améliorer votre expérience utilisateur</li>
                        <li>Établir des statistiques de visite</li>
                        <li>Vous proposer des contenus adaptés à vos centres d'intérêt</li>
                    </ul>
                    <p>Vous pouvez désactiver les cookies dans les paramètres de votre navigateur.</p>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="mb-3">Pour toute question concernant notre politique de confidentialité :</p>
                <a href="{{ route('contact') }}" class="btn btn-success">
                    <i class="fas fa-envelope me-2"></i>Contactez-nous
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-body {
    padding: 2rem;
}

ul {
    list-style-type: none;
    padding-left: 0;
}

ul li {
    position: relative;
    padding-left: 1.5rem;
    margin-bottom: 0.5rem;
}

ul li::before {
    content: "•";
    color: #198754;
    font-weight: bold;
    position: absolute;
    left: 0;
}
</style>
@endsection 