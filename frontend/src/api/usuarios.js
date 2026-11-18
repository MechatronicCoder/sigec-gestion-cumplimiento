import api from "./axios";

export async function listUsuarios() {
  const { data } = await api.get("/api/usuarios");
  return data;
}

export async function listRoles() {
  const { data } = await api.get("/api/roles");
  return data;
}

export async function createUsuario(payload) {
  const { data } = await api.post("/api/usuarios", payload);
  return data;
}

export async function updateUsuario(id, payload) {
  const { data } = await api.put(`/api/usuarios/${id}`, payload);
  return data;
}

export async function toggleUsuario(id) {
  const { data } = await api.patch(`/api/usuarios/${id}/toggle`);
  return data;
}

export async function resetPasswordUsuario(id) {
  const { data } = await api.post(`/api/usuarios/${id}/reset-password`);
  return data;
}
