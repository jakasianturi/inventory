@extends('errors::custom')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="500">{{ __('500') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Server Error') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
