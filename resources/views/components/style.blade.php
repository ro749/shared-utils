    <!--<link rel="icon" type="image/png"  href="{{ asset('vendor/shared-utils/images/favicon.png') }}" sizes="16x16">-->
    <!-- remix icon font css  -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/remixicon.css') }}">
    <!-- BootStrap css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/bootstrap.min.css') }}">
    <!-- Apex Chart css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/apexcharts.css') }}">
    <!-- Data Table css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/dataTables.min.css') }}">
    <!-- Text Editor css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/editor-katex.min.css') }}">
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/editor.atom-one-dark.min.css') }}">
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/editor.quill.snow.css') }}">
    <!-- Date picker css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/flatpickr.min.css') }}">
    <!-- Calendar css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/full-calendar.css') }}">
    <!-- Vector Map css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/jquery-jvectormap-2.0.5.css') }}">
    <!-- Popup css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/magnific-popup.css') }}">
    <!-- Slick Slider css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/slick.css') }}">
    <!-- prism css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/prism.css') }}">
    <!-- file upload css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/file-upload.css') }}">

    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/lib/audioplayer.css') }}">
    <!-- main css -->
    <link rel="stylesheet"  href="{{ asset('vendor/shared-utils/css/style.css') }}">
    <style>
.modal{
    --bs-modal-zindex:1055;
    --bs-modal-width:500px;
    --bs-modal-padding:1rem;
    --bs-modal-margin:0.5rem;
    --bs-modal-color: ;
    --bs-modal-bg:var(--bs-body-bg);
    --bs-modal-border-color:var(--bs-border-color-translucent);
    --bs-modal-border-width:var(--bs-border-width);
    --bs-modal-border-radius:var(--bs-border-radius-lg);
    --bs-modal-box-shadow:0 0.125rem 0.25rem rgba(var(--bs-body-color-rgb), 0.075);
    --bs-modal-inner-border-radius:calc(var(--bs-border-radius-lg) - (var(--bs-border-width)));
    --bs-modal-header-padding-x:1rem;
    --bs-modal-header-padding-y:1rem;
    --bs-modal-header-padding:1rem 1rem;
    --bs-modal-header-border-color:var(--bs-border-color);
    --bs-modal-header-border-width:var(--bs-border-width);
    --bs-modal-title-line-height:1.5;
    --bs-modal-footer-gap:0.5rem;
    --bs-modal-footer-bg: ;
    --bs-modal-footer-border-color:var(--bs-border-color);
    --bs-modal-footer-border-width:var(--bs-border-width);
    position:fixed !important;
    top:0;
    left:0;
    z-index:var(--bs-modal-zindex);
    display:none;
    width:100%;
    height:100%;
    overflow-x:hidden;
    overflow-y:auto;
    outline:0
}
.popup{
  background-color: rgba(255, 255, 255, 1) !important;
  box-shadow: 0 .3rem .8rem rgba(0, 0, 0, .12);
  margin-bottom: 1.5rem !important;
  border: 0 solid transparent;
  padding:0;
  position:relative !important;
}
.popup-header{
    background-color:rgba(0, 0, 0, 0) !important;
}
@media only screen and (min-width: 768px){
    .popup{
        width: 33%;
    }
}
@media only screen and (max-width: 767px) {
    .popup{
        width: 83%;
    }
}
    </style>