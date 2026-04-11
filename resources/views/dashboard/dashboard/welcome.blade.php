@extends('dashboard.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <!-- Item List -->
                <div class="card card-primary card-outline  mb-4">
                    <div class="card-body">
                        Welcome!
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('customStyle')
@endsection
