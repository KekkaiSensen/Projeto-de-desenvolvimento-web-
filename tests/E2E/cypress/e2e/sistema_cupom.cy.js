describe('Sistema de Cupom', () => {
    beforeEach(() => {
        // Ideally seed database or mock
        cy.visit('tela_login.html'); // Adjust path if needed

        // Login User
        cy.get('input[name="email"]').type('joao.teste+qa@example.com');
        cy.get('input[name="senha"]').type('Teste@1234');
        cy.get('form[action="../Banco de dados/processa_login.php"] button[type="submit"]').click();

        // Verify login
        cy.url().should('include', 'index.php');
    });

    it('Deve aplicar um cupom válido no carrinho', () => {
        // Clear cart first to ensure clean state (optional, if logic allows)
        cy.visit('tela_carrinho.php');
        cy.window().then((win) => {
            win.localStorage.removeItem('carrinho');
        });

        // 1. Visit Home and Add Product
        cy.visit('index.php');
        cy.get('.card-link').first().click(); // Select first product

        // 2. Product Page
        cy.get('#btn-adicionar-carrinho').click();

        // 3. Cart Page
        cy.url().should('include', 'tela_carrinho.php');

        // Wait for elements to load and apply coupon
        cy.get('#cupom-codigo').should('be.visible').type('DESCONTO10');
        cy.get('#btn-aplicar-cupom').click();

        // Verification
        // Success message and price update
        // cy.get('#msg-cupom').should('contain', 'Cupom aplicado');

        // Check if total discount is visible
        // User specified: div desconto-container shows "desconto" + valor-desconto
        cy.get('#desconto-container').should('be.visible').and('contain', 'Desconto');
        cy.get('#valor-desconto').should('be.visible').and('contain', 'R$');
    });

    it('Deve mostrar erro ao tentar aplicar cupom inválido', () => {
        // Clear cart first
        cy.visit('tela_carrinho.php');
        cy.window().then((win) => {
            win.localStorage.removeItem('carrinho');
        });

        // 1. Visit Home and Add Product
        cy.visit('index.php');
        cy.get('.card-link').first().click(); // Select first product

        // 2. Product Page
        cy.get('#btn-adicionar-carrinho').click();

        // 3. Cart Page
        cy.get('#cupom-codigo').type('INVALIDO123');
        cy.get('#btn-aplicar-cupom').click();

        // Verification
        cy.get('#msg-cupom').should('contain', 'Cupom inválido ou não encontrado.');
    });
});
