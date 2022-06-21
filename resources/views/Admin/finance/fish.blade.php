@extends('layouts.admin')
@section('css')

@endsection('css')
@section('script')
    <script>
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
    <!-- begin::sweet alert demo -->
    <script src="/js/sweetalert.min.js"></script>
    @include('sweet::alert')
    <!-- begin::sweet alert demo -->
@endsection('script')
@section('navbar')


@endsection('navbar')
@section('sidebar')
@endsection('sidebar')

@section('header')
    <div class="page-header">
        <div>
            <h3>مدیریت مالی</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/home">داشبورد</a></li>
                    <li class="breadcrumb-item"><a href="#">مدیریت مالی</a></li>
                    <li class="breadcrumb-item active" aria-current="page">فیش ها</li>
                </ol>
            </nav>
        </div>

    </div>
@endsection('header')
@section('content')
    <div class="card">
        <div class="card-body">


            <br>
            <input id="myInput" type="text" placeholder="Search" class="form-control col-md-4">
            <br>

            <div class="media-body table-responsive">
                <br>
                <table class="table table-bordered table-striped mb-0 table-fixed" id="myTable">
                    <thead>
                    <tr style="text-align: center">

                        <th>شمارنده</th>
                        <th>نام</th>
                        <th>هزینه شهریه</th>
                        <th>نوع پرداخت</th>
                        <th>تاریخ پرداخت</th>
                        <th>وضعیت</th>
                        <th>دانلود فایل</th>
                        <th>مبلغ</th>
                        <th>تایید</th>


                    </tr>
                    </thead>
                    <tbody id="myTable">

                    <?php $idn = 1; ?>
                    @foreach($fishs as $log)
                        <form action="{{url('/admin/finance/fish/edit').'/'.$log->id}}" method="post">
                            {{csrf_field()}}
                            @method('put')
                            @if($log->verify==1)
                                <tr style="text-align: center;background-color: #00b179">

                            @else
                                <tr style="text-align: center;background-color: firebrick;color: black">
                                    @endif
                                    <td>{{$idn}}</td>
                                     <td>{{$log->user->f_name}}
                         {{$log->user->l_name}}</td>
                                    <td>{{$log->price}}</td>
                                    <td>{{$log->type}}</td>
                                    <td>{{$log->created_at}}</td>
                                    <td>
                                        @if($log->verify==0)
                                            تاییده نشده
                                        @else
                                            تایید شده
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->filename)
                                            <a href="{{ route('finance.download', $log->id) }}"><i
                                                        class="icon-download"></i>&nbsp
                                                &nbsp<span>دانلود فیش</span> </a>
                                        @else
                                            بدون فایل
                                        @endif
                                    </td>

                                    <td>
                                        <input style="text-align: center" name="price" class="form-control"
                                               value="{{$log->price}}" required>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-info">تایید و برگشت تایید</button>
                                    </td>

                                </tr>
                        </form>
                        <?php
                        $idn = $idn + 1;
                        ?>
                    @endforeach

                    </tbody>

                </table>
            </div>
{{ $fishs->links() }}
        </div>
    </div>
    <!-- begin::sweet alert demo -->
    <script src="/js/sweetalert.min.js"></script>
    @include('sweet::alert')
    <!-- begin::sweet alert demo -->
@endsection('content')

<script>
    function toggless(id, obj) {
        var $input = $(obj);
        var status = 0;
        if ($input.prop('checked')) {
            var status = 1;
        }

        $.ajaxSetup({

            'headers': {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $.ajax({
            url: '{{url('/admin/changeStatus/finance')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                status: status,
                "id": id
            },
            success: function (data) {
                if (status == 1) {
                    swal({
                        title: "{{config('global.student')}} تایید مالی شد.",
                        icon: "success",

                    });
                }
                if (status == 0) {
                    swal({
                        title: "{{config('global.student')}} عدم تایید مالی شد.",
                        icon: "success",

                    });
                }
                location.reload();

            }
        })


    }
</script>

