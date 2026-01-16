// cypress/support/commands.js

Cypress.Commands.add('login', (email, password) => {
  cy.session([email, password], () => {
    cy.visit('/tela_login.html');

    cy.intercept('POST', '**/processa_login.php').as('loginRequest');

    // --- CORREÇÃO AQUI ---
    // O seletor foi trocado de #usuario para input[name="email"]
    cy.get('input[name="email"]').type(email);
    // --- FIM DA CORREÇÃO ---

    cy.get('input[name="senha"]').type(password);
    cy.get('main button[type="submit"]').click();

    cy.wait('@loginRequest');
    cy.url().should('include', 'index.php');
  });
});