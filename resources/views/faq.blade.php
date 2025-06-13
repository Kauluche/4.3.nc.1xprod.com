@extends('layouts.vitrine')

@section('title', 'FAQ - NaturaCorp')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-5">Questions Fréquentes</h1>
            
            <div class="accordion" id="faqAccordion">
                @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <p class="mb-4">Vous ne trouvez pas la réponse à votre question ?</p>
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
.accordion-button:not(.collapsed) {
    background-color: #198754;
    color: white;
}

.accordion-button:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.accordion-item {
    margin-bottom: 0.5rem;
}
</style>
@endsection 