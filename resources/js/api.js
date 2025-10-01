import axios from 'axios';

const api = axios.create({
    baseURL: '/api', // Las llamadas irán a rutas definidas en routes/api.php
    headers: {
        Accept: 'application/json',
    },
    // withCredentials: true, // habilita si usas autenticación basada en sesión/Sanctum
});

export default api;
