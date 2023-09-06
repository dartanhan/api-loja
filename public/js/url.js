   /**
     * Monto a URL de DSV ou PRD a depender do protocolo
    * */
        const fncUrl = function() {
        const protocolo = window.location.protocol;
        const hostname = window.location.hostname;
        const pathname = (window.location.origin+""+window.location.pathname).split("/");

        const url = (protocolo === "https:") ? protocolo +"//"+ hostname + "/admin" : protocolo +"//"+ hostname + "/"+pathname[3]+"/admin" ;

        return url;
    }
