@push('scripts')
<script>
    var choosen_lang = 'es';
    var lang = [];
    document.addEventListener('DOMContentLoaded', function() {
        $.ajax({
            url: "/lang",
            method: "GET",
            success: function (data) {
                lang = data;
            }
        })
    });
    function changeLang() {
        $.ajax({
            url: "/change-lang",
            method: "POST",
        });
        choosen_lang = choosen_lang=='es'?'en':'es';
        var choosen_lang_data = lang[choosen_lang];
        Object.keys(choosen_lang_data).forEach(function(key) {
            var area = choosen_lang_data[key];
            Object.keys(area).forEach(function(key2) {
                $('.lang-' + key + '-' + key2).html(area[key2]);
            });
        });
        $(document).trigger('lang-changed');
    }
</script>
@endpush