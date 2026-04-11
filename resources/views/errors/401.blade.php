@extends('errors::custom')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="401">{{ __('401') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Unauthorized') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
