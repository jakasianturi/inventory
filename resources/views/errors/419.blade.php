@extends('errors::custom')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="419">{{ __('419') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Page Expired') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
