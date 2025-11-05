    <!-- jQuery library js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Apex Chart js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/apexcharts.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/dataTables.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/iconify-icon.min.js') }}"></script>
    <!-- jQuery UI js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Vector Map js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('vendor/shared-utils/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- Popup js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/slick.min.js') }}"></script>
    <!-- prism js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/prism.js') }}"></script>
    <!-- file upload js -->
    <script src="{{ asset('vendor/shared-utils/js/lib/file-upload.js') }}"></script>
    <!-- audioplayer -->
    <script src="{{ asset('vendor/shared-utils/js/lib/audioplayer.js') }}"></script>
    <!-- select2 -->
    <script src="{{ asset('vendor/shared-utils/js/lib/select2.min.js') }}"></script>

    <script src="{{ asset('vendor/shared-utils/js/inputs.js') }}"></script>
    <!-- smart-table -->
    <script src="{{ asset('vendor/shared-utils/js/smartTable.js') }}"></script>
    <script src="{{ asset('vendor/shared-utils/js/layeredSmartTable.js') }}"></script>
    <script src="{{ asset('vendor/shared-utils/js/localSmartTable.js') }}"></script>
    <script src="{{ asset('vendor/shared-utils/js/stringUtils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/shared-utils/fancy-file-uploader/jquery.fileupload.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/shared-utils/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/shared-utils/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>