   /**
     * Monto a URL de DSV ou PRD a depender do protocolo
     */
    const fncUrl = function() {
      const protocolo = window.location.protocol;
      const hostname = window.location.hostname;
      const url = (protocolo === "https:") ? protocolo +"//"+ hostname + "/admin" : protocolo +"//"+ hostname + "/api-loja/admin" ;

      return url;
    }
