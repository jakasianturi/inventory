@extends('errors::custom')

@section('title', __('Not Found'))
@section('code', '404')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="404">{{ __('404') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Not Found') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
