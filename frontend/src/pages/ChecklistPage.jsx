import { useEffect, useState } from "react";
import {
  Box,
  Typography,
  Select,
  MenuItem,
  Button,
  Accordion,
  AccordionSummary,
  AccordionDetails,
  Table,
  TableBody,
  TableRow,
  TableCell,
  TextField,
  Chip,
  Alert,
  CircularProgress,
  Paper,
  Stack,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Typography as Text,
} from "@mui/material";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import AddIcon from "@mui/icons-material/Add";
import SaveIcon from "@mui/icons-material/Save";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import {
  listEvaluaciones,
  crearEvaluacion,
  getChecklist,
  guardarChecklist,
  finalizarEvaluacion,
} from "../api/checklist";

const OPCIONES_CALIFICACION = [0, 1, 2, 3, 4, 5];

export default function ChecklistPage() {
  const [evaluaciones, setEvaluaciones] = useState([]);
  const [evaluacionId, setEvaluacionId] = useState("");
  const [checklist, setChecklist] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [calificaciones, setCalificaciones] = useState({});
  const [observaciones, setObservaciones] = useState({});
  const [saving, setSaving] = useState(false);
  const [saveMsg, setSaveMsg] = useState(null);

  const [confirmFinalizar, setConfirmFinalizar] = useState(false);
  const [creating, setCreating] = useState(false);

  function loadEvaluaciones(selectId) {
    listEvaluaciones().then((evs) => {
      setEvaluaciones(evs);
      const target = selectId || evs.find((e) => e.estado === "abierta")?.id || evs[0]?.id || "";
      setEvaluacionId(target);
    });
  }

  useEffect(loadEvaluaciones, []);

  useEffect(() => {
    if (!evaluacionId) {
      setLoading(false);
      return;
    }
    setLoading(true);
    getChecklist(evaluacionId)
      .then((data) => {
        setChecklist(data);
        const califs = {};
        const obs = {};
        Object.entries(data.calificaciones).forEach(([itemId, row]) => {
          califs[itemId] = row.calificacion ?? "";
          obs[itemId] = row.observaciones ?? "";
        });
        setCalificaciones(califs);
        setObservaciones(obs);
      })
      .catch(() => setError("No se pudo cargar el checklist."))
      .finally(() => setLoading(false));
  }, [evaluacionId]);

  async function handleCrearEvaluacion() {
    setCreating(true);
    setError(null);
    try {
      const nueva = await crearEvaluacion();
      loadEvaluaciones(nueva.id);
    } catch (err) {
      setError(err.response?.data?.message || "No se pudo crear la evaluacion.");
    } finally {
      setCreating(false);
    }
  }

  async function handleGuardar() {
    setSaving(true);
    setSaveMsg(null);
    try {
      await guardarChecklist(evaluacionId, calificaciones, observaciones);
      setSaveMsg("Guardado correctamente.");
    } catch (err) {
      setSaveMsg(err.response?.data?.message || "No se pudo guardar.");
    } finally {
      setSaving(false);
    }
  }

  async function handleFinalizar() {
    try {
      await finalizarEvaluacion(evaluacionId);
      setConfirmFinalizar(false);
      loadEvaluaciones(Number(evaluacionId));
    } catch (err) {
      setError(err.response?.data?.message || "No se pudo finalizar.");
    }
  }

  if (loading && evaluaciones.length === 0) {
    return (
      <Box sx={{ display: "flex", justifyContent: "center", mt: 6 }}>
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Box>
      <Box sx={{ display: "flex", justifyContent: "space-between", alignItems: "center", mb: 2, flexWrap: "wrap", gap: 2 }}>
        <Typography variant="h4">Checklist de evaluacion</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={handleCrearEvaluacion} disabled={creating}>
          Nueva evaluacion
        </Button>
      </Box>

      {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

      {evaluaciones.length === 0 ? (
        <Alert severity="info">Todavia no hay evaluaciones. Crea la primera con el boton de arriba.</Alert>
      ) : (
        <Paper sx={{ p: 2, mb: 3 }}>
          <Stack direction="row" spacing={2} alignItems="center" flexWrap="wrap">
            <Select
              size="small"
              value={evaluacionId}
              onChange={(e) => setEvaluacionId(e.target.value)}
              sx={{ minWidth: 260 }}
            >
              {evaluaciones.map((ev) => (
                <MenuItem key={ev.id} value={ev.id}>
                  {ev.nombre}
                </MenuItem>
              ))}
            </Select>

            {checklist && (
              <Chip
                label={checklist.evaluacion.estado === "abierta" ? "Abierta" : "Finalizada"}
                color={checklist.evaluacion.estado === "abierta" ? "success" : "default"}
              />
            )}

            {checklist?.es_editable && (
              <>
                <Button
                  variant="contained"
                  startIcon={<SaveIcon />}
                  onClick={handleGuardar}
                  disabled={saving}
                >
                  {saving ? "Guardando..." : "Guardar"}
                </Button>
                <Button
                  variant="outlined"
                  color="warning"
                  startIcon={<CheckCircleIcon />}
                  onClick={() => setConfirmFinalizar(true)}
                >
                  Finalizar evaluacion
                </Button>
              </>
            )}
          </Stack>
          {saveMsg && (
            <Alert severity={saveMsg.includes("correctamente") ? "success" : "error"} sx={{ mt: 2 }}>
              {saveMsg}
            </Alert>
          )}
        </Paper>
      )}

      {loading && (
        <Box sx={{ display: "flex", justifyContent: "center", mt: 4 }}>
          <CircularProgress />
        </Box>
      )}

      {!loading && checklist && (
        <Box>
          {checklist.dimensiones.map((dim) => (
            <Accordion key={dim.id} defaultExpanded={false}>
              <AccordionSummary expandIcon={<ExpandMoreIcon />}>
                <Typography sx={{ fontWeight: 600 }}>{dim.nombre}</Typography>
              </AccordionSummary>
              <AccordionDetails>
                {dim.indicadores.map((ind) => (
                  <Box key={ind.id} sx={{ mb: 3 }}>
                    <Text variant="subtitle1" sx={{ mb: 1 }}>
                      {ind.nombre}
                    </Text>
                    <Table size="small">
                      <TableBody>
                        {ind.items.map((item) => (
                          <TableRow key={item.id}>
                            <TableCell sx={{ width: "50%" }}>{item.nombre}</TableCell>
                            <TableCell sx={{ width: 110 }}>
                              <Select
                                size="small"
                                fullWidth
                                value={calificaciones[item.id] ?? ""}
                                disabled={!checklist.es_editable}
                                onChange={(e) =>
                                  setCalificaciones({
                                    ...calificaciones,
                                    [item.id]: e.target.value,
                                  })
                                }
                              >
                                <MenuItem value="">-</MenuItem>
                                {OPCIONES_CALIFICACION.map((n) => (
                                  <MenuItem key={n} value={n}>
                                    {n}
                                  </MenuItem>
                                ))}
                              </Select>
                            </TableCell>
                            <TableCell>
                              <TextField
                                size="small"
                                fullWidth
                                placeholder="Observaciones"
                                disabled={!checklist.es_editable}
                                value={observaciones[item.id] ?? ""}
                                onChange={(e) =>
                                  setObservaciones({
                                    ...observaciones,
                                    [item.id]: e.target.value,
                                  })
                                }
                              />
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </Box>
                ))}
              </AccordionDetails>
            </Accordion>
          ))}
        </Box>
      )}

      <Dialog open={confirmFinalizar} onClose={() => setConfirmFinalizar(false)}>
        <DialogTitle>Finalizar evaluacion</DialogTitle>
        <DialogContent>
          <Typography>
            Una vez finalizada, ya no se podra editar. ¿Continuar?
          </Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setConfirmFinalizar(false)}>Cancelar</Button>
          <Button variant="contained" color="warning" onClick={handleFinalizar}>
            Finalizar
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
