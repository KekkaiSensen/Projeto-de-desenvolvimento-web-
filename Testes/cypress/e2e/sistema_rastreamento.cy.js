describe('Sistema de Rastreamento', () => {
    // Variable to store the order ID dynamically found
    let orderId;

    it('Deve rastrear pedido (Fornecedor -> Cliente)', () => {
        // ----------------------------------------------------------------
        // 1. Client logs in to get the latest Order ID
        // ----------------------------------------------------------------
        cy.visit('tela_login.html');
        cy.get('input[name="email"]').type('joao.teste+qa@example.com');
        cy.get('input[name="senha"]').type('Teste@1234');
        cy.get('form[action="../Banco de dados/processa_login.php"] button[type="submit"]').click();

        cy.visit('tela_minha_conta.php');
        cy.contains('Compras feitas').click();

        // Grab the ID of the first (latest) order card FROM SANDRA
        // Filter by supplier name to ensure we pick an order visible to the supplier account
        cy.contains('.pedido-card', 'Sandra Gomes')
            .find('strong')
            .contains('Pedido #')
            .invoke('text')
            .then((text) => {
                // Expected text: "Pedido #123"
                orderId = text.replace('Pedido #', '').trim();
                cy.log('Tracking Order ID: ' + orderId);
            })
            .then(() => {
                // ----------------------------------------------------------------
                // 2. Logout Client
                // ----------------------------------------------------------------
                cy.get('a:contains("Sair")').click();

                // ----------------------------------------------------------------
                // 3. Supplier Logs in
                // ----------------------------------------------------------------
                cy.visit('tela_login.html');
                cy.get('input[name="email"]').type('sandra.gomes@LojaLTDA.com');
                cy.get('input[name="senha"]').type('FakeUser@P@ss');
                cy.get('form[action="../Banco de dados/processa_login.php"] button[type="submit"]').click();

                // Wait for session/page transition
                cy.wait(500);

                // Visit Supplier Dashboard (My Account)
                cy.visit('tela_minha_conta.php');
                cy.contains('Pedidos Recebidos').click();

                // Ensure the panel is active
                cy.get('#painel-pedidos-recebidos').should('be.visible');

                // Find order by dynamic ID
                // Fix: cy.contains with one arg looks for text. We want selector + text.
                const orderLabel = 'Pedido #' + orderId;

                // Wait until we find the order (retry ability)
                cy.contains('strong', orderLabel).should('be.visible');

                // --- Action: Start Processing (if available) ---
                cy.contains('.pedido-card', orderLabel).within(() => {
                    cy.root().then($card => {
                        if ($card.find('button:contains("Iniciar Processamento")').length > 0) {
                            cy.wrap($card).contains('button', 'Iniciar Processamento').click();
                            // Wait for potential reload
                            cy.wait(2000);
                        }
                    });
                });

                // Reload page to refresh status (Cypress can lose DOM references, so good to reload/re-navigate)
                cy.visit('tela_minha_conta.php');
                cy.contains('Pedidos Recebidos').click();
                cy.get('#painel-pedidos-recebidos').should('be.visible');

                // --- Action: Mark as Shipped ---
                cy.contains('.pedido-card', orderLabel).within(() => {
                    cy.root().then($card => {
                        if ($card.find('button:contains("Marcar como Enviado")').length > 0) {
                            cy.wrap($card).contains('button', 'Marcar como Enviado').click();
                            cy.wait(1000);
                        }
                    });
                });

                // ----------------------------------------------------------------
                // 4. Logout Supplier
                // ----------------------------------------------------------------
                cy.get('a:contains("Sair")').click();

                // ----------------------------------------------------------------
                // 5. Client logs in again to Verify
                // ----------------------------------------------------------------
                cy.visit('tela_login.html');
                cy.get('input[name="email"]').type('joao.teste+qa@example.com');
                cy.get('input[name="senha"]').type('Teste@1234');
                cy.get('form[action="../Banco de dados/processa_login.php"] button[type="submit"]').click();

                cy.visit('tela_minha_conta.php');
                cy.contains('Compras feitas').click();

                // Assert Status is "Enviado"
                cy.contains('Pedido #' + orderId).should('be.visible');
                cy.contains('Pedido #' + orderId).parents('.pedido-card').within(() => {
                    cy.contains('Enviado').should('be.visible');
                });
            });
    });
});
