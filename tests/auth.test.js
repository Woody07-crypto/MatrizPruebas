const request = require('supertest');
const app = require('../app'); // Asegúrate de que este sea el archivo donde exportas Express

describe('Auth System Tests', () => {

    // Test para un Login Exitoso
    it('Debería loguear al usuario y devolver un token', async () => {
        const response = await request(app)
            .post('/api/auth/login') // La ruta que definiste en auth.routes.js
            .send({
                email: 'usuario@prueba.com',
                password: 'password123'
            });

        expect(response.statusCode).toBe(200);
        expect(response.body).toHaveProperty('token'); // Verifica que el controller devuelva el token
    });

    // Test para Login Fallido
    it('Debería rechazar el login con credenciales incorrectas', async () => {
        const response = await request(app)
            .post('/api/auth/login')
            .send({
                email: 'usuario@prueba.com',
                password: 'wrongpassword'
            });

        expect(response.statusCode).toBe(401); // O el código que hayas definido en el catch de tu controller
    });
});