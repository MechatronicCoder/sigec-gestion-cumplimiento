import axios from "axios";

// Todo pasa por Nginx en el puerto 8080 (no directo al backend en 9000,
// que ni siquiera esta expuesto al host).
const BASE_URL = "http://localhost:8080";

const api = axios.create({
  baseURL: BASE_URL,
  withCredentials: true, // envia y recibe cookies de sesion (laravel-session, XSRF-TOKEN)
  xsrfCookieName: "XSRF-TOKEN",
  xsrfHeaderName: "X-XSRF-TOKEN",
  headers: {
    Accept: "application/json",
  },
});

export default api;
