describe('Sistema de Notificação', () => {
    beforeEach(() => {
        cy.visit('tela_login.html');
        cy.get('input[name="email"]').type('joao.teste+qa@example.com');
        cy.get('input[name="senha"]').type('Teste@1234');
        cy.get('form[action="../Banco de dados/processa_login.php"] button[type="submit"]').click();
    });

    it('Deve visualizar notificações', () => {
        cy.get('#notification-bell').click();
        cy.get('.notification-dropdown').should('be.visible');
        // Check if there are items
        cy.get('.notification-item').should('have.length.greaterThan', 0);
    });

    it('Deve marcar notificação como lida', () => {
        cy.get('#notification-bell').click();
        cy.get('.notification-item.unread').first().click();
        // Verify it is now read (style change or api call check)
        cy.get('.notification-item.unread').should('have.length.lt', 5); // Example assertion
    });
});
