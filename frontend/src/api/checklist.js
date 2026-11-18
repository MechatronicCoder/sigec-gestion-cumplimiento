import api from "./axios";

export async function listEvaluaciones() {
  const { data } = await api.get("/api/evaluaciones");
  return data;
}

export async function crearEvaluacion() {
  const { data } = await api.post("/api/evaluaciones");
  return data;
}

export async function getChecklist(evaluacionId) {
  const { data } = await api.get(`/api/evaluaciones/${evaluacionId}/checklist`);
  return data;
}

export async function guardarChecklist(evaluacionId, calificaciones, observaciones) {
  const { data } = await api.post(`/api/evaluaciones/${evaluacionId}/checklist`, {
    calificaciones,
    observaciones,
  });
  return data;
}

export async function finalizarEvaluacion(evaluacionId) {
  const { data } = await api.patch(`/api/evaluaciones/${evaluacionId}/finalizar`);
  return data;
}

export async function getResumen(evaluacionId) {
  const { data } = await api.get(`/api/evaluaciones/${evaluacionId}/resumen`);
  return data;
}
