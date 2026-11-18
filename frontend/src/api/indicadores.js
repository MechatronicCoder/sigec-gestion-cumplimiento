import api from "./axios";

export async function getIndicador(id) {
  const { data } = await api.get(`/api/indicadores/${id}`);
  return data;
}

export async function listIndicadores(dimensionId) {
  const { data } = await api.get("/api/indicadores", {
    params: dimensionId ? { dimension_id: dimensionId } : {},
  });
  return data;
}

export async function createIndicador(payload) {
  const { data } = await api.post("/api/indicadores", payload);
  return data;
}

export async function updateIndicador(id, payload) {
  const { data } = await api.put(`/api/indicadores/${id}`, payload);
  return data;
}

export async function deleteIndicador(id) {
  await api.delete(`/api/indicadores/${id}`);
}
