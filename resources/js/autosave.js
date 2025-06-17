(function ($) {
    $.fn.autoSaveInput = function (options = {}) {
        return this.each(function () {
            const input = $(this);
            const id = input.data('id'); 
            const url = '/api/autosave/' + id;
            const debounceTime = 500;
            let timeout = null;

            if (!url) return;

            input.on('input change', function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const value = input.val();

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            param: value,
                            _token:t('meta[name="csrf-token"]').attr("content")
                        }
                    });
                }, debounceTime);
            });
        });
    };
})(jQuery);