// cypress/support/commands.js

/**
 * Cria um comando de login reutilizável.
 * Isso usa o cy.session() para manter o cookie de login
 * entre os testes, tornando-os muito mais rápidos.
 */
Cypress.Commands.add('login', (email, password) => {
  cy.session([email, password], () => {
    // Visita a página de login
    cy.visit('/tela_login.html');

    // Intercepta a chamada para saber quando ela terminar
    // O wildcard ** funciona tanto para api/ quanto para outros caminhos
    cy.intercept('POST', '**/processa_login.php').as('loginRequest');

    // Preenche os dados (Seletores corrigidos para name=email/senha)
    cy.get('input[name="email"]').type(email);
    cy.get('input[name="senha"]').type(password);
    cy.get('form[action="api/processa_login.php"] button[type="submit"]').click();

    // Espera o redirecionamento para o index.php
    cy.wait('@loginRequest');
    cy.url().should('include', 'index.php'); // Confirma que o login deu certo
  });
});