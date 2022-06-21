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
                    <li class="breadcrumb-item active" aria-current="page"> لیست {{config('global.students')}}</li>
                </ol>
            </nav>
        </div>

    </div>
@endsection('header')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="/admin/finance/group/create" method="post">
                @csrf
                <input name="class" value="{{$id}}" hidden>
                <div class="row">
                    <div class="col-md-2"><span> تعریف گروهی شهریه</span>
                    </div>
                    <div class="col-md-3"><input type="number" class="form-control" name="finance" required>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success" type="submit">ثبت</button>
                    </div>
                </div>
            </form>

            <br>
            <br>
            <input id="myInput" type="text" placeholder="نام {{config('global.student')}} را وارد کنید"
                   class="form-control col-md-4">
            <br>

            <div class="media-body table-responsive">
                <br>
                <table class="table table-bordered table-striped mb-0 table-fixed" id="myTable">

                    <thead>
                    <tr style="text-align: center">
                        <th>شمارنده</th>
                        <th>عکس</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>پایه تحصیلی و کلاس</th>
                        <th>کل شهریه</th>
                        <th>پرداختی</th>
                        <th>مانده</th>
                        <th>ثبت تغییرات</th>
                        <th>تاییدیه</th>
                        <th>آپلود فیش</th>

                    </tr>
                    </thead>
                    <tbody>

                    @include('Admin.errors')
                    @foreach($users as $index=>$user )
                        <form action="{{url('/admin/finance/edit').'/'.$user->id}}">
                            {{csrf_field()}}
                            @method('put')
                            <tr>
                                <th scope="row">{{ $index + $users->firstItem() }}</th>
                                <td>
                                    <div class="gallery">
                                        <figure class="avatar avatar-sm avatar-state-success">
                                            @if(!empty($user->filename))
                                                <img class="rounded-circle"
                                                     src="{{url('uploads/'.$user->filename)}}"
                                                     alt="...">
                                            @else
                                                <img class="rounded-circle" src="/assets/profile/avatar.png"
                                                     alt="...">
                                            @endisset
                                        </figure>
                                    </div>
                                </td>
                                <td>{{$user->f_name}}</td>
                                <td>{{$user->l_name}}</td>
                                <td>
                                    {{$user->paye}} - {{$user->class}}
                                </td>
                                <td>
                                    <input class="form-control" style="text-align: center"
                                           @if($user->finance) value="{{$user->finance->total}}" @endif name="total"
                                           required>
                                </td>
                                <td>
                                    @if($user->finance)
                                        {{number_format($user->finance->paid)}}
                                    @endif
                                </td>
                                <td>
                                    @if($user->finance)
                                        {{number_format($user->finance->remaining)}}
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <button type="submit" class="btn btn-info">ویرایش</button>
                                    </div>
                                </td>
                        </form>

                        <td style="text-align: center">

                            <input style="text-align: center" type="checkbox" class="form-check-input"
                                   id="materialUnchecked"
                                   {{ $user->finance->status ? 'checked' : '' }} {{ $user->finance->remaining==0 ? 'checked' : '' }} onclick="toggless('{{$user->finance->id}}',this) ">
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModal{{$user->id}}">
                                آپلود
                            </button>
                            <div class="modal fade" id="exampleModal{{$user->id}}" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">آپلود فیش</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="/admin/finance/upload"
                                                  enctype="multipart/form-data">
                                                {{csrf_field()}}
                                                <input name="user_id" value="{{$user->id}}" type="hidden">

                                                @include('Admin.errors')
                                                <div class="row">


                                                    <div class="col-md-5">
                    <span>
                                             آپلود تصویر فیش
                    </span>


                                                        <input type="file" name="file" class="form-control"
                                                               required>

                                                    </div>
                                                    <div class="col-md-5">
                    <span>
                                             مبلغ به ریال
                    </span>


                                                        <input type="number" name="price" class="form-control"
                                                               autocomplete="off" required>

                                                    </div>
                                                    <div class="col-md-2">
                                                        <br>
                                                        <button type="submit" class="btn btn-info"> ارسال</button>

                                                    </div>

                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>
            </div>
            {{ $users->links() }}

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

