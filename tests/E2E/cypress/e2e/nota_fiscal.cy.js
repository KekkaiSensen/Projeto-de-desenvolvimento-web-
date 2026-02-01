describe('Verificação de Dados da Nota Fiscal', () => {

    beforeEach(() => {
        cy.visit('/');
        cy.clearLocalStorage();
        cy.clearCookies();
    });

    it('Deve gerar a nota fiscal com os valores corretos após o pagamento', () => {
        // --- 1. Preparação: Login e Adição de Item ---
        // Login com usuário existente
        cy.visit('/tela_login.html');
        cy.get('input[name="email"]').type('joao.teste+qa@example.com');
        cy.get('input[name="senha"]').type('Teste@1234');
        cy.get('form[action*="processa_login.php"] button[type="submit"]').click();

        // Verifica login
        cy.url().should('not.include', 'tela_login.html');

        // Adiciona produto ao carrinho (Id 58 - Parafusadeira)
        cy.visit('/tela_produto.php?id=58');

        // Captura o preço na tela de produto para conferência futura
        cy.get('#produto-preco').invoke('text').as('precoProdutoInicial');

        cy.get('#btn-adicionar-carrinho').click();

        // --- 2. Checkout e Entrega ---
        cy.visit('/tela_carrinho.php');
        cy.get('.btn-continuar').click();

        cy.url().should('include', 'tela_entrega.php');

        // Seleciona envio para endereço (Grátis)
        cy.get('#enviar-endereco').click({ force: true });

        // Verifica se há endereço. Se não, cadastra um rápido.
        cy.get('body').then($body => {
            if ($body.find('p:contains("Nenhum endereço cadastrado")').length > 0) {
                cy.contains('Alterar ou escolher outro endereço').click();
                cy.get('#cep').type('13184230');
                cy.wait(1000);
                cy.get('#numero').type('999');
                cy.get('#btn-salvar-endereco').click();
                cy.wait(500);
            }
        });

        cy.get('#form-entrega .btn-continuar-entrega').click();

        // --- 3. Tela de Pagamento e Verificação ---
        cy.url().should('include', 'tela_pagamento.php');

        // Intercepta o backend
        cy.intercept('POST', '**/processa_pedido.php', {
            statusCode: 200,
            body: { sucesso: true, mensagem: 'Pedido processado com sucesso' }
        }).as('processaPedido');

        // Stub do jsPDF
        cy.window().then((win) => {
            // Cria um stub para o construtor ou para o objeto global se for UMD
            // No código da tela_pagamento.php: const { jsPDF } = window.jspdf;

            // Vamos mockar a propriedade jspdf da window
            const jsPDFMock = {
                text: cy.stub().as('docText'), // Stub para capturar textos escritos
                rect: cy.stub(),
                line: cy.stub(),
                setFontSize: cy.stub(),
                setFont: cy.stub(),
                splitTextToSize: cy.stub().returns(['Texto Simulado']),
                save: cy.stub().as('docSave') // Stub para o save
            };

            // Precisamos mockar o construtor jsPDF
            const jsPDFConstructor = function () {
                return jsPDFMock;
            };

            win.jspdf = { jsPDF: jsPDFConstructor };
        });

        // Seleciona PIX e finaliza
        cy.get('#pix').check({ force: true });
        cy.get('#form-pagamento .btn-continuar-entrega').click();

        // Aguarda processamento
        cy.wait('@processaPedido');

        // Verifica se a notificação apareceu (o que dispara o setTimeout do PDF)
        cy.get('#success-notification').should('be.visible');

        // Aguarda o setTimeout do PDF (3000ms + 400ms no código)
        // Damos uma margem de segurança
        cy.wait(4000);

        // --- 4. Asserções na Nota Fiscal ---

        // Recupera os valores esperados do localStorage (antes que sejam apagados? Não, o código apaga DEPOIS de gerar)
        // Mas como estamos stubando, o código vai rodar o stub e depois apagar.
        // Vamos verificar os chamados ao doc.text

        cy.get('@docText').should('have.been.called');

        // Verifica se o texto "NOTA FISCAL" foi escrito
        cy.get('@docText').should('have.been.calledWith', 'NOTA FISCAL');

        // Verifica se o valor total foi escrito corretamente
        // Primeiro recupera o preço que salvamos lá no começo
        cy.get('@precoProdutoInicial').then(precoTexto => {
            // O preço vem formatado ex: "R$ 150,00"
            // Na nota fiscal, ele deve aparecer formatado também.
            // O código usa toFixed(2).replace(".", ",") -> "150,00"

            // Remove R$ e espaços para pegar só o valor nu se necessário, 
            // mas o código da tela de produto e da nota podem ter formatações levemente diferentes (espaços).
            // Vamos buscar pelo valor numérico parcial.

            const valorNumerico = precoTexto.replace(/[^\d,]/g, ''); // "150,00"

            // A função gerarNotaFiscalPDF chama doc.text(fTotal, ..., ...)
            // Verificamos se houve alguma chamada com esse valor
            cy.get('@docText').should('have.been.calledWith', Cypress.sinon.match(valorNumerico));
        });

        // Verifica se o método save foi chamado
        cy.get('@docSave').should('have.been.calledWith', 'nota_fiscal_simulada.pdf');
    });

});
