@extends('layouts.app')
@section('title','Dashboard')
@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">UFM Records</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">UFM Records</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Default box -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">UFM Violations</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Exam Name</th>
                                                <th>Description</th>
                                                <th>UFM Flag</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ufmRecords as $key=>$record)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>{{ $record['student_name'] }}</td>
                                                    <td>{{ $record['exam_name'] }}</td>
                                                    <td>{{ $record['description'] }}</td>
                                                    <td>
                                                        @if($record['ufm_flag'])
                                                            <span class="badge badge-danger">Flagged</span>
                                                        @else
                                                            <span class="badge badge-success">Clean</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($record['created_at'])->format('d M Y H:i') }}</td>
                                                    <td>
                                                  <form action="{{ route('admin.ufm.destroy', $record['id']) }}" method="POST" style="display:inline;" class="delete-form">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm delete-btn">Delete</button>
</form>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- /.content-wrapper -->

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (confirm('Are you sure you want to delete this record?')) {
                    form.submit();
                }
            });
        });
    });
</script>
