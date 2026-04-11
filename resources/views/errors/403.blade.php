@extends('errors::custom')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="403">{{ __('403') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Forbidden') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
