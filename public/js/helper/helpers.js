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
    }
};
