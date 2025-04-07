import 'cypress-wait-until';

describe('Test E2E gestion des utilisateurs', () => {
  const baseUrl = 'http://localhost:8000/index.html';

  beforeEach(() => {
    cy.visit(baseUrl, { timeout: 10000 });
  });

  it('Ajoute un utilisateur', () => {
    const nom = 'kparry';
    const email = 'e2e_' + Date.now() + '@test.com';

    cy.intercept('POST', '/api.php').as('postUser');
    cy.intercept('GET', '/api.php').as('getUsers');

    cy.get('#name').type(nom);
    cy.get('#email').type(email);
    cy.get('button[type=submit]').click();

    cy.wait('@postUser');
    cy.wait('@getUsers');

    cy.waitUntil(() =>
      cy.get('#userList').then($ul => $ul.text().includes(nom)),
      {
        errorMsg: `L'utilisateur ${nom} non trouvé dans la liste`,
        timeout: 10000,
        interval: 500
      }
    );

    cy.get('#userList').contains(nom).should('exist');
  });

  it('Modifie un utilisateur', () => {
    const newName = 'Utilisateur Modifié';
    const newEmail = 'modif_' + Date.now() + '@test.com';

    cy.intercept('GET', '/api.php').as('getUsers');
    cy.intercept('PUT', '/api.php').as('updateUser');
    cy.intercept('POST', '/api.php').as('anyPostRequest');

    cy.get('#userList li', { timeout: 15000 }).should('have.length.greaterThan', 0);

    cy.get('#userList li').first().within(() => {
      cy.get('button').first().click();
    });

    cy.get('#name').clear().type(newName);
    cy.get('#email').clear().type(newEmail);
    cy.get('button[type=submit]').click();

    cy.wait('@anyPostRequest', { timeout: 10000 }).then(() => {
      cy.wait('@getUsers', { timeout: 10000 });
    });

    cy.wait(500);

    cy.get('#userList', { timeout: 10000 }).contains(newName).should('exist');
  });

  it('Supprime un utilisateur', () => {
    cy.intercept('GET', '/api.php').as('getUsers');
    cy.intercept('DELETE', '/api.php').as('deleteUser');
    cy.intercept('POST', '/api.php').as('anyPostRequest');

    cy.get('#userList li', { timeout: 15000 }).should('have.length.greaterThan', 0);

    cy.get('#userList li').first().invoke('text').as('userToDelete');

    cy.get('#userList li').first().within(() => {
      cy.get('button').eq(1).click();
    });

    cy.wait('@deleteUser', { timeout: 10000 }).then(() => {
      cy.wait('@getUsers', { timeout: 10000 });
    });

    cy.wait(500);

    cy.get('@userToDelete').then((userText) => {
      cy.get('#userList li').should((items) => {
        expect(items.text()).not.to.include(userText);
      });
    });
  });
});
