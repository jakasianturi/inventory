@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message')
    <div class="centered mx-auto">
        <div class="text-center">
            <div class="error mx-auto" data-text="429">{{ __('429') }}</div>
            <p class="lead text-gray-800 mb-5">{{ __('Too Many Requests') }}</p>
            <a type="button" href="{{ url('/') }}"
                class="btn btn-sm btn-primary text-white">{{ __('Kembali ke Beranda') }}</a>
        </div>
    </div>
@endsection
