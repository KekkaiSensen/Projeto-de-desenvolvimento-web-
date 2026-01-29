// cypress/e2e/jornada_cliente.cy.js

describe('Jornada E2E do Cliente (Happy Path)', () => {

  beforeEach(() => {
    cy.visit('/');
    cy.clearLocalStorage('carrinho');
    cy.clearLocalStorage('totalCompra');
    cy.clearLocalStorage('valorFinal');
  });

  it('deve permitir que um usuário adicione um item, faça login e finalize a compra', () => {

    // --- 1. Home (index.php) ---
    // Debug: Verifica quantos produtos existem na tela
    cy.get('.card-link').should('have.length.greaterThan', 0).then($cards => {
      cy.log(`Encontrados ${$cards.length} produtos.`);
    });

    // Clica no primeiro produto (que será o ID 22, "Parafusadeira")
    cy.get('.card-link').first().click();

    // --- 2. Página de Produto (tela_produto.php) ---
    cy.url().should('include', 'tela_produto.php');
    cy.get('#btn-adicionar-carrinho').click();

    // --- 3. Página do Carrinho (tela_carrinho.php) ---
    cy.url().should('include', 'tela_carrinho.php');
    cy.get('#valor-total').should('not.contain', 'R$ 0,00');
    cy.get('.btn-continuar').click();

    // --- 4. Página de Login (tela_login.html) ---
    cy.url().should('include', 'tela_login.html');
    cy.intercept('POST', '**/processa_login.php').as('postLogin');

    // --- CORREÇÃO APLICADA ---
    // Usamos o usuário e senha que existem no seu bancodadosteste.sql
    // A senha para 'joao.teste+qa@example.com' é 'senha123' (hash $2y$10$BzliJoZptHJnsCFGKk4ADO8MOXxT89I3LfYgG/QSqC7CXCjLgEfzO)
    cy.get('input[name="email"]').type('joao.teste+qa@example.com');
    cy.get('input[name="senha"]').type('Teste@1234'); // Senha correta do fornecedor
    cy.get('form[action*="processa_login.php"] button[type="submit"]').click();

    cy.wait('@postLogin');

    // --- 5. Página de Entrega (tela_entrega.php) ---
    // Se o login redirecionar para index ou minha conta, forçamos a ida para a entrega
    // já que o carrinho deve estar mantido na sessão/localstorage.
    cy.visit('/tela_entrega.php');
    cy.url().should('include', 'tela_entrega.php');
    cy.get('#resumo-total-preco').should('not.contain', 'R$ 0,00');

    // Verifica se há endereço cadastrado (se o texto "Nenhum endereço cadastrado" está visível)
    cy.get('body').then($body => {
      if ($body.find('p:contains("Nenhum endereço cadastrado")').length > 0) {
        // Abre modal de adicionar endereço
        cy.contains('Alterar ou escolher outro endereço').click();
        cy.get('#modal-editar-endereco').should('be.visible');

        // Preenche endereço
        cy.get('#cep').type('13184-230');
        // Aguarda preenchimento automático (mockando wait ou apenas type se não houver listener blur imediato no teste, mas o código tem blur)
        cy.get('#cep').blur();
        cy.wait(1000); // Espera viaCEP

        cy.get('#numero').type('123');
        cy.get('#btn-salvar-endereco').click();

        // Espera modal fechar e endereço aparecer no bloco de endereço
        cy.get('#modal-editar-endereco').should('not.be.visible');
        cy.get('label[for="enviar-endereco"]').contains('RUA LUIZ CAMILO DE CAMARGO').should('be.visible');
      }
    });

    // Usa seletor específico pois há botão com mesma classe em modal
    cy.get('#form-entrega .btn-continuar-entrega').click();

    // --- 6. Página de Pagamento (tela_pagamento.php) ---
    cy.url().should('include', 'tela_pagamento.php');
    cy.intercept('POST', '**/processa_pedido.php').as('processaPedido');
    cy.get('#pix').check();
    // Usa seletor específico para garantir
    cy.get('#form-pagamento .btn-continuar-entrega').click();

    // --- 7. Sucesso ---
    cy.wait('@processaPedido').its('response.statusCode').should('eq', 200);
    cy.get('#success-notification')
      .should('be.visible')
      .and('contain', 'Pagamento bem-sucedido');

    // --- 8. Verificação Pós-Compra ---
    cy.visit('/tela_carrinho.php');
    cy.get('#painel-carrinho').should('contain', 'Nenhum produto no carrinho');
  });
});



