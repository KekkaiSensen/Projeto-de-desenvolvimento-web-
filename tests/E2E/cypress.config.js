// cypress.config.js
const { defineConfig } = require('cypress');

module.exports = defineConfig({
  e2e: {
    // Aponte para a RAÍZ do seu servidor, não para um arquivo
    baseUrl: 'http://localhost:8000/public', // ⬅️ CORREÇÃO: Aponta para a pasta public
    supportFile: false,
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});