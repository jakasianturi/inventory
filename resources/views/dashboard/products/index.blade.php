@extends('dashboard.layouts.app')
@section('title', 'Katalog Produk Susu')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Daftar Produk Susu</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Produk</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="d-sm-flex align-items-center justify-content-start mb-4">
                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('dashboard.products.create') }}" class="d-sm-inline-block mt-mw-sm-2 btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-plus fa-sm mr-1"></i> Tambah Produk
                        </a>
                    @endif
                </div>

                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-primary card-outline mb-4">
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="dataproduk" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 15%">SKU</th>
                                    <th scope="col" style="width: 25%">Nama Produk</th>
                                    <th scope="col" style="width: 15%">Kategori</th>
                                    <th scope="col" style="width: 15%">Harga Jual</th>
                                    <th scope="col" style="width: 15%">Stok Aktif</th>
                                    <th scope="col" class="text-center" style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Hapus Produk</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus produk ini? (Data terkait transaksi tidak akan terhapus karena menggunakan fitur Soft Delete).</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button id="deleteData" type="button" class="btn btn-danger">Hapus Produk</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('customStyle')
    <script>
        let dataId; // Variabel global untuk menampung ID yang akan dihapus

        // Setup CSRF untuk semua request AJAX
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi DataTables Server-Side
            $('#dataproduk').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('dashboard.products.index') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'sku_code', name: 'sku_code' },
                    { data: 'name', name: 'name' },
                    { data: 'category.name', name: 'category.name', defaultContent: '-' },
                    { 
                        data: 'base_price', 
                        name: 'base_price',
                        render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') 
                    },
                    { data: 'total_stock', name: 'total_stock', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']],
                language: {
                    url: '{{ asset('vendor/datatables/dataTables.indonesia.json') }}'
                }
            });
        });

        // Event saat tombol 'Hapus' di tabel diklik
        $(document).on('click', '.delete', function() {
            dataId = $(this).attr('id');
            $('#deleteModal').modal('show');
        });

        // Event saat tombol konfirmasi hapus di modal diklik
        $('#deleteData').click(function() {
            let baseUrl = "{{ url('dashboard/products') }}";
            
            $.ajax({
                url: baseUrl + '/' + dataId,
                type: 'DELETE',
                success: function(data) {
                    setTimeout(function() {
                        $('#deleteModal').modal('hide');
                        let oTable = $('#dataproduk').dataTable();
                        oTable.fnDraw(false); // Render ulang tabel secara diam-diam
                    });
                    
                    iziToast.warning({
                        title: 'Berhasil',
                        message: 'Data produk berhasil dihapus',
                        position: 'bottomRight'
                    });
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    iziToast.error({
                        title: 'Gagal',
                        message: xhr.responseJSON.message || 'Terjadi kesalahan pada server',
                        position: 'bottomRight'
                    });
                }
            });
        });
    </script>
@endsection