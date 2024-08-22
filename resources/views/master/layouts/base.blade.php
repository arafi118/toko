<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Master Toko</title>
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/pace/pace.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-fileinput/fileinput.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-tour/bootstrap-tour.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/calculator/calculator.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        @include('master.layouts.navbar')

        <div class="content-wrapper">
            <div class="container">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        base_path = "{{ url('/') }}";
    </script>

    <script src="{{ asset('AdminLTE/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/select2/select2.full.min.js') }}"></script>
    <script
        src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js') }}">
    </script>
    <script src="{{ asset('AdminLTE/plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/DataTables/pdfmake-0.1.32/vfs_fonts.js') }}"></script>

    <script src="{{ asset('js/jquery-validation-1.16.0/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery-validation-1.16.0/dist/additional-methods.min.js') }}"></script>

    @php
        $validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale')) . '.js';
    @endphp
    @if (file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
        <script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '') }}"></script>
    @endif

    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-fileinput/fileinput.min.js') }}"></script>
    <script src="{{ asset('plugins/accounting.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-tour/bootstrap-tour.min.js') }}"></script>
    <script src="{{ asset('plugins/printThis.js') }}"></script>
    <script src="{{ asset('plugins/screenfull.min.js') }}""></script>
    <script src="{{ asset('js/AdminLTE-app.js') }}"></script>

    @if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js') }}"></script>
    @else
        <script src="{{ asset('js/lang/en.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        var financial_year = {
            start: moment('2024-01-01'),
            end: moment('2024-12-31'),
        }
        //Default setting for select2
        $.fn.select2.defaults.set("language", "en");

        var datepicker_date_format = "dd-mm-yyyy";
        var moment_date_format = "DD-MM-YYYY";
        var moment_time_format = "HH:mm";
    </script>

    <script src="{{ asset('js/functions.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/help-tour.js') }}"></script>
    <script src="{{ asset('plugins/calculator/calculator.js') }}"></script>

    @yield('script')
</body>

</html>
