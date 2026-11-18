import { useEffect, useState } from "react";
import {
  Box,
  Typography,
  Table,
  TableHead,
  TableBody,
  TableRow,
  TableCell,
  Paper,
  Select,
  MenuItem,
  InputLabel,
  FormControl,
  TextField,
  Alert,
  CircularProgress,
  Chip,
  Pagination,
  IconButton,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  Tooltip,
} from "@mui/material";
import VisibilityIcon from "@mui/icons-material/Visibility";
import { listAuditoria, getFiltrosAuditoria } from "../api/auditoria";
import DownloadIcon from "@mui/icons-material/Download";
import PictureAsPdfIcon from "@mui/icons-material/PictureAsPdf";
import { exportarAuditoriaExcel, exportarAuditoriaPdf } from "../api/auditoria";

const accionColor = {
  creado: "success",
  actualizado: "info",
  eliminado: "error",
};

function formatFecha(fecha) {
  return new Date(fecha).toLocaleString();
}

export default function AuditoriaPage() {
  const [rows, setRows] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1 });
  const [usuarios, setUsuarios] = useState([]);
  const [modelos, setModelos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [filtros, setFiltros] = useState({
    modelo: "",
    user_id: "",
    desde: "",
    hasta: "",
  });
  const [page, setPage] = useState(1);

  const [detalle, setDetalle] = useState(null);

  useEffect(() => {
    getFiltrosAuditoria()
      .then((data) => {
        setUsuarios(data.usuarios);
        setModelos(data.modelos);
      })
      .catch(() => {
        // Los filtros son secundarios; si fallan, la tabla igual puede cargar.
      });
  }, []);

  function load() {
    setLoading(true);
    setError(null);
    const params = { page };
    if (filtros.modelo) params.modelo = filtros.modelo;
    if (filtros.user_id) params.user_id = filtros.user_id;
    if (filtros.desde) params.desde = filtros.desde;
    if (filtros.hasta) params.hasta = filtros.hasta;

    listAuditoria(params)
      .then((data) => {
        setRows(data.data);
        setMeta({ current_page: data.current_page, last_page: data.last_page });
      })
      .catch(() => setError("No se pudo cargar el historial de auditoria."))
      .finally(() => setLoading(false));
  }

  useEffect(load, [page, filtros]);

  function handleFiltroChange(campo, valor) {
    setPage(1);
    setFiltros((f) => ({ ...f, [campo]: valor }));
  }

  function construirParams() {
  const params = {};
  if (filtros.modelo) params.modelo = filtros.modelo;
  if (filtros.user_id) params.user_id = filtros.user_id;
  if (filtros.desde) params.desde = filtros.desde;
  if (filtros.hasta) params.hasta = filtros.hasta;
  return params;
}

function descargarBlob(blob, nombreArchivo) {
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = nombreArchivo;
  document.body.appendChild(link);
  link.click();
  link.remove();
  window.URL.revokeObjectURL(url);
}

async function handleExportarExcel() {
  const blob = await exportarAuditoriaExcel(construirParams());
  descargarBlob(blob, `sigec-auditoria-${Date.now()}.xlsx`);
}

async function handleExportarPdf() {
  const blob = await exportarAuditoriaPdf(construirParams());
  descargarBlob(blob, `sigec-auditoria-${Date.now()}.pdf`);
}

  return (
    <Box>
        <Box sx={{ display: "flex", justifyContent: "space-between", alignItems: "center", mb: 2 }}>
    <Typography variant="h4">Auditoria</Typography>
    <Box sx={{ display: "flex", gap: 1 }}>
        <Button
        variant="outlined"
        startIcon={<DownloadIcon />}
        onClick={handleExportarExcel}
        >
        Excel
        </Button>
        <Button
        variant="outlined"
        color="error"
        startIcon={<PictureAsPdfIcon />}
        onClick={handleExportarPdf}
        >
        PDF
        </Button>
    </Box>
    </Box>

      <Paper sx={{ p: 2, mb: 2, display: "flex", gap: 2, flexWrap: "wrap" }}>
        <Box>
            <Typography variant="caption" color="text.secondary" sx={{ display: "block", mb: 0.3 }}>
            Modelo
            </Typography>
            <FormControl size="small" sx={{ minWidth: 170 }}>
            <Select
                value={filtros.modelo}
                onChange={(e) => handleFiltroChange("modelo", e.target.value)}
                displayEmpty
            >
                <MenuItem value="">Todos</MenuItem>
                {modelos.map((m) => (
                <MenuItem key={m} value={m}>
                    {m}
                </MenuItem>
                ))}
            </Select>
            </FormControl>
        </Box>

        <Box>
            <Typography variant="caption" color="text.secondary" sx={{ display: "block", mb: 0.3 }}>
            Usuario
            </Typography>
            <FormControl size="small" sx={{ minWidth: 190 }}>
            <Select
                value={filtros.user_id}
                onChange={(e) => handleFiltroChange("user_id", e.target.value)}
                displayEmpty
            >
                <MenuItem value="">Todos</MenuItem>
                {usuarios.map((u) => (
                <MenuItem key={u.id} value={u.id}>
                    {u.name}
                </MenuItem>
                ))}
            </Select>
            </FormControl>
        </Box>

        <Box>
            <Typography variant="caption" color="text.secondary" sx={{ display: "block", mb: 0.3 }}>
            Desde
            </Typography>
            <TextField
            size="small"
            type="date"
            value={filtros.desde}
            onChange={(e) => handleFiltroChange("desde", e.target.value)}
            sx={{ minWidth: 170 }}
            />
        </Box>

        <Box>
            <Typography variant="caption" color="text.secondary" sx={{ display: "block", mb: 0.3 }}>
            Hasta
            </Typography>
            <TextField
            size="small"
            type="date"
            value={filtros.hasta}
            onChange={(e) => handleFiltroChange("hasta", e.target.value)}
            sx={{ minWidth: 170 }}
            />
        </Box>
        </Paper>

      {error && <Alert severity="error">{error}</Alert>}

      {loading ? (
        <Box sx={{ display: "flex", justifyContent: "center", mt: 4 }}>
          <CircularProgress />
        </Box>
      ) : (
        <>
          <Paper>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Fecha</TableCell>
                  <TableCell>Usuario</TableCell>
                  <TableCell>Accion</TableCell>
                  <TableCell>Modelo</TableCell>
                  <TableCell>ID</TableCell>
                  <TableCell align="right">Detalle</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {rows.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={6} align="center">
                      No hay registros de auditoria con estos filtros.
                    </TableCell>
                  </TableRow>
                )}
                {rows.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{formatFecha(row.created_at)}</TableCell>
                    <TableCell>{row.usuario?.name || "Sistema"}</TableCell>
                    <TableCell>
                      <Chip
                        size="small"
                        label={row.accion}
                        color={accionColor[row.accion] || "default"}
                      />
                    </TableCell>
                    <TableCell>{row.modelo}</TableCell>
                    <TableCell>{row.modelo_id}</TableCell>
                    <TableCell align="right">
                      <Tooltip title="Ver detalle">
                        <IconButton onClick={() => setDetalle(row)}>
                          <VisibilityIcon fontSize="small" />
                        </IconButton>
                      </Tooltip>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>

          {meta.last_page > 1 && (
            <Box sx={{ display: "flex", justifyContent: "center", mt: 2 }}>
              <Pagination
                count={meta.last_page}
                page={meta.current_page}
                onChange={(_, value) => setPage(value)}
              />
            </Box>
          )}
        </>
      )}

      <Dialog open={!!detalle} onClose={() => setDetalle(null)} fullWidth maxWidth="sm">
        <DialogTitle>
          {detalle?.accion} - {detalle?.modelo} #{detalle?.modelo_id}
        </DialogTitle>
        <DialogContent>
          {detalle?.valores_anteriores && (
            <>
              <Typography variant="subtitle2" sx={{ mt: 1 }}>
                Valores anteriores
              </Typography>
              <Box
                component="pre"
                sx={{
                  bgcolor: "grey.100",
                  p: 1.5,
                  borderRadius: 1,
                  overflowX: "auto",
                  fontSize: 13,
                }}
              >
                {JSON.stringify(detalle.valores_anteriores, null, 2)}
              </Box>
            </>
          )}
          {detalle?.valores_nuevos && (
            <>
              <Typography variant="subtitle2" sx={{ mt: 2 }}>
                Valores nuevos
              </Typography>
              <Box
                component="pre"
                sx={{
                  bgcolor: "grey.100",
                  p: 1.5,
                  borderRadius: 1,
                  overflowX: "auto",
                  fontSize: 13,
                }}
              >
                {JSON.stringify(detalle.valores_nuevos, null, 2)}
              </Box>
            </>
          )}
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDetalle(null)}>Cerrar</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}