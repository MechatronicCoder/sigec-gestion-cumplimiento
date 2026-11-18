import api from "./axios";

export async function listAuditoria(params = {}) {
  const { data } = await api.get("/api/auditoria", { params });
  return data;
}

export async function getFiltrosAuditoria() {
  const { data } = await api.get("/api/auditoria/filtros");
  return data;
}

export async function exportarAuditoriaExcel(params = {}) {
  const { data } = await api.get("/api/auditoria/exportar-excel", {
    params,
    responseType: "blob",
  });
  return data;
}

export async function exportarAuditoriaPdf(params = {}) {
  const { data } = await api.get("/api/auditoria/exportar-pdf", {
    params,
    responseType: "blob",
  });
  return data;
}