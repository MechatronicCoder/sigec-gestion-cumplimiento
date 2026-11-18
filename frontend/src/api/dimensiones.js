import api from "./axios";

export async function getDimension(id) {
  const { data } = await api.get(`/api/dimensiones/${id}`);
  return data;
}

export async function listDimensiones() {
  const { data } = await api.get("/api/dimensiones");
  return data;
}

export async function createDimension(payload) {
  const { data } = await api.post("/api/dimensiones", payload);
  return data;
}

export async function updateDimension(id, payload) {
  const { data } = await api.put(`/api/dimensiones/${id}`, payload);
  return data;
}

export async function deleteDimension(id) {
  await api.delete(`/api/dimensiones/${id}`);
}
