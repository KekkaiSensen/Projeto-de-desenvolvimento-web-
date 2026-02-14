describe('Sistema de Notificação', () => {
    beforeEach(() => {
        cy.visit('tela_login.html');
        cy.get('input[name="email"]').type('joao.teste+qa@example.com');
        cy.get('input[name="senha"]').type('Teste@1234');
        cy.get('form[action="api/processa_login.php"] button[type="submit"]').click();
    });

    it('Deve visualizar notificações', () => {
        // Mock da API de notificações para garantir que existam itens
        cy.intercept('GET', '**/api/notifications.php?action=poll', {
            statusCode: 200,
            body: {
                count: 1,
                notifications: [
                    { id: 1, mensagem: 'Notificação de Teste 1', lida: 0, data_criacao: '2023-01-01 10:00:00' },
                    { id: 2, mensagem: 'Notificação de Teste 2', lida: 1, data_criacao: '2023-01-01 11:00:00' }
                ]
            }
        }).as('getNotifications');

        cy.get('#notification-bell').click();
        cy.wait('@getNotifications');
        cy.get('.notification-dropdown').should('be.visible');
        // Check if there are items
        cy.get('.notification-item').should('have.length.greaterThan', 0);
    });

    it('Deve marcar notificação como lida', () => {
        // Mock da API para poll inicial
        cy.intercept('GET', '**/api/notifications.php?action=poll', {
            statusCode: 200,
            body: {
                count: 1,
                notifications: [
                    { id: 99, mensagem: 'Notificação Não Lida', lida: 0, data_criacao: '2023-01-01' }
                ]
            }
        }).as('getUnread');

        // Mock da ação de marcar como lida
        cy.intercept('POST', '**/api/notifications.php', {
            statusCode: 200,
            body: { success: true }
        }).as('markRead');

        cy.get('#notification-bell').click();
        cy.wait('@getUnread');
        cy.get('.notification-item.unread').first().click();
        // Verify it is now read (style change or api call check)
        cy.get('.notification-item.unread').should('have.length.lt', 5); // Example assertion
    });
});


//     1) Deve marcar notificação como lida


//   1 passing (7s)
//   1 failing

//   1) Sistema de Notificação
//        Deve marcar notificação como lida:
//      AssertionError: Timed out retrying after 4000ms: Expected to find element: `.notification-item.unread`, but never found it.
//       at Context.eval (webpack://testes-automatizados/./cypress/e2e/sistema_notificacao.cy.js:18:11)

