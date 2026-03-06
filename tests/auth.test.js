const request = require('supertest');
const app = require('../app'); 

describe('Auth System Tests', () => {


    it('Debería loguear al usuario y devolver un token', async () => {
        const response = await request(app)
            .post('/api/auth/login') 
            .send({
                email: 'usuario@prueba.com',
                password: 'password123'
            });

        expect(response.statusCode).toBe(200);
        expect(response.body).toHaveProperty('token'); 
    });

   
    it('Debería rechazar el login con credenciales incorrectas', async () => {
        const response = await request(app)
            .post('/api/auth/login')
            .send({
                email: 'usuario@prueba.com',
                password: 'wrongpassword'
            });

        expect(response.statusCode).toBe(401); 
    });
});