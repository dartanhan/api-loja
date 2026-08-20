   /**
     * Monto a URL de DSV ou PRD a depender do protocolo
    * */
        const fncUrl = function() {
        const protocolo = window.location.protocol;
        const host = window.location.host;
        const pathname = (window.location.origin+""+window.location.pathname).split("/");

        const url = (protocolo === "https:") ? protocolo +"//"+ host + "/admin" : protocolo +"//"+ host + "/"+pathname[3]+"" ;

        return url;
    }
