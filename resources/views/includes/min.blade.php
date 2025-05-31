@push('styles')
<link href="https://blixdev.com/ListingEngine/assets/css/bootstrap.min.css" rel="stylesheet">
<style> 
    .navbar {
  background-color: rgba(33, 37, 41, 0.5) !important;
}
.navbar-collapse{
    background-color: rgba(33, 37, 41, 0.0) !important;
}
.popup{
  color:white;
}
@media only screen and (min-width: 768px) { 
    .responsive-row {
        display: flex !important;
        flex-direction: row !important;
    }
    .popup{
        width: 33%;
    }
}
@media only screen and (max-width: 767px) {
  .responsive-row {
        display: flex !important;
        flex-direction: column !important;
    }
    .responsive-row > div {
        width: 100% !important; 
    }
    .dataTables_wrapper {
      overflow-x: auto;
    }
    #navbarContent {
      background-color: rgba(33, 37, 41, 0.9) !important;
      padding: 1rem;
      border-radius: 0.5rem;
    }
    .popup{
        width: 83%;
    }
}

input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] { 
  -moz-appearance: textfield; 
}
</style>
@endpush