import api from "./axios";

/**
 * Debe llamarse ANTES de login() en cada sesion nueva del navegador.
 * Pide a Laravel que ponga la cookie XSRF-TOKEN, que Axios luego
 * reenviara automaticamente como header X-XSRF-TOKEN en cada peticion
 * (proteccion CSRF requerida por Sanctum para las rutas 'web').
 */
export async function getCsrfCookie() {
  await api.get("/sanctum/csrf-cookie");
}

/**
 * Inicia sesion contra el endpoint API dedicado (routes/api.php),
 * que responde JSON en vez de redirigir (a diferencia del login Blade).
 */
export async function login(email, password) {
  await getCsrfCookie();
  const { data } = await api.post("/api/login", { email, password });
  return data.user;
}

/**
 * Cierra la sesion actual.
 */
export async function logout() {
  await api.post("/api/logout");
}

/**
 * Devuelve el usuario autenticado, o null si no hay sesion activa.
 */
export async function fetchUser() {
  try {
    const { data } = await api.get("/api/user");
    return data;
  } catch (err) {
    if (err.response && err.response.status === 401) {
      return null;
    }
    throw err;
  }
}
