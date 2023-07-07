   /**
     * Monto a URL de DSV ou PRD a depender do protocolo
     */
    let fncUrl = function() {
      let protocolo = window.location.protocol;
      let hostname = window.location.hostname;
      let url = (protocolo === "https") ? protocolo +"//"+ hostname + "/admin" : protocolo +"//"+ hostname + "/api-loja/admin" ;

      return url;
    }
