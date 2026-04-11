@extends('errors::custom')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="503">{{ __('503') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Service Unavailable') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
