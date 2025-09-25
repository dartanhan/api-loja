window.Helpers = {
    asset: function(path) {
        if (!window.Laravel || !window.Laravel.assetUrl) {
            console.error('Laravel.assetUrl not defined');
            return path;
        }
        path = path.replace(/^\/+/, '');
        return window.Laravel.assetUrl + path;
    },

    exemplo: function() {
        console.log('Helper JS funcionando!');
    },

    calendar:function () {
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll("input[id^='validade-']").forEach(function(el) {
                flatpickr(el, {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    locale: "pt"  // ou "pt_br"
                });
            });
        });
    }
};
