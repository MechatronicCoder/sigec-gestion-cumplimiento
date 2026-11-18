import api from "./axios";

export async function listItems(indicadorId) {
  const { data } = await api.get("/api/items", {
    params: indicadorId ? { indicador_id: indicadorId } : {},
  });
  return data;
}

export async function createItem(payload) {
  const { data } = await api.post("/api/items", payload);
  return data;
}

export async function updateItem(id, payload) {
  const { data } = await api.put(`/api/items/${id}`, payload);
  return data;
}

export async function deleteItem(id) {
  await api.delete(`/api/items/${id}`);
}
