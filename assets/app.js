// ============================================================================
//  app.js — JavaScript do Lab Inseguro
// ============================================================================

// [VULN: Tokens hardcoded no front-end] Chave de API exposta no cliente.
const API_KEY = "sk_live_51H8xY2eZvKflkjasd09182job";

// [VULN: Informações Encodadas em base64]
// "Ofuscar" credenciais com base64 NÃO é criptografia — qualquer um decodifica.
// atob("YWRtaW46U3VwZXJTZWNyZXQxMjMh") => "admin:SuperSecret123!"
const BASIC_AUTH = "YWRtaW46U3VwZXJTZWNyZXQxMjMh";

function autenticar() {
  const creds = atob(BASIC_AUTH); // decodifica o segredo no cliente
  // [VULN: Comunicações inseguras HTTP] chamada em http:// sem TLS
  // e enviando a chave de API por querystring (vai para logs/histórico).
  return fetch("http://localhost:8000/api?key=" + API_KEY, {
    headers: { Authorization: "Basic " + BASIC_AUTH },
  }).then((r) => r.text());
}

// [VULN: XSS via DOM] escreve HTML não sanitizado vindo da URL.
// Usa jQuery 1.7.2 (versão vulnerável) de propósito.
(function () {
  const params = new URLSearchParams(window.location.search);
  const nome = params.get("nome");
  if (nome) {
    // innerHTML com entrada do usuário => DOM-based XSS
    document.getElementById &&
      (document.title = nome);
    if (window.jQuery) {
      jQuery(function ($) {
        $("body").append("<div class='greet'>Olá, " + nome + "</div>");
      });
    }
  }
})();
