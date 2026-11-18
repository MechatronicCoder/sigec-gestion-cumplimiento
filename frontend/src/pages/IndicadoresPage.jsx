import { useEffect, useState } from "react";
import { useNavigate, useParams, Link as RouterLink } from "react-router-dom";
import {
  Box,
  Typography,
  Button,
  Table,
  TableHead,
  TableBody,
  TableRow,
  TableCell,
  IconButton,
  Paper,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  FormControlLabel,
  Switch,
  Alert,
  CircularProgress,
  Chip,
  Tooltip,
  Breadcrumbs,
  Link,
} from "@mui/material";
import AddIcon from "@mui/icons-material/Add";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";
import ArrowForwardIcon from "@mui/icons-material/ArrowForward";
import { getDimension } from "../api/dimensiones";
import {
  listIndicadores,
  createIndicador,
  updateIndicador,
  deleteIndicador,
} from "../api/indicadores";

export default function IndicadoresPage() {
  const { dimensionId } = useParams();
  const navigate = useNavigate();

  const [dimension, setDimension] = useState(null);
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const emptyForm = {
    dimension_id: dimensionId,
    nombre: "",
    descripcion: "",
    peso: 0,
    orden: 0,
    activo: true,
  };

  const [dialogOpen, setDialogOpen] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState(emptyForm);
  const [saving, setSaving] = useState(false);
  const [formError, setFormError] = useState(null);

  const [deleteTarget, setDeleteTarget] = useState(null);
  const [deleteError, setDeleteError] = useState(null);

  function load() {
    setLoading(true);
    Promise.all([getDimension(dimensionId), listIndicadores(dimensionId)])
      .then(([dim, inds]) => {
        setDimension(dim);
        setRows(inds);
      })
      .catch(() => setError("No se pudieron cargar los indicadores."))
      .finally(() => setLoading(false));
  }

  useEffect(load, [dimensionId]);

  function openCreate() {
    setEditing(null);
    setForm(emptyForm);
    setFormError(null);
    setDialogOpen(true);
  }

  function openEdit(row) {
    setEditing(row);
    setForm({
      dimension_id: dimensionId,
      nombre: row.nombre,
      descripcion: row.descripcion || "",
      peso: row.peso,
      orden: row.orden,
      activo: !!row.activo,
    });
    setFormError(null);
    setDialogOpen(true);
  }

  async function handleSave() {
    setSaving(true);
    setFormError(null);
    try {
      if (editing) {
        await updateIndicador(editing.id, form);
      } else {
        await createIndicador(form);
      }
      setDialogOpen(false);
      load();
    } catch (err) {
      const msg =
        err.response?.data?.message ||
        Object.values(err.response?.data?.errors || {})[0]?.[0] ||
        "No se pudo guardar.";
      setFormError(msg);
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete() {
    setDeleteError(null);
    try {
      await deleteIndicador(deleteTarget.id);
      setDeleteTarget(null);
      load();
    } catch (err) {
      setDeleteError(
        err.response?.data?.message || "No se pudo eliminar el indicador."
      );
    }
  }

  return (
    <Box>
      <Breadcrumbs sx={{ mb: 1 }}>
        <Link component={RouterLink} to="/admin/dimensiones" underline="hover">
          Dimensiones
        </Link>
        <Typography color="text.primary">
          {dimension ? dimension.nombre : "..."}
        </Typography>
      </Breadcrumbs>

      <Box sx={{ display: "flex", justifyContent: "space-between", mb: 2 }}>
        <Typography variant="h4">Indicadores</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={openCreate}>
          Nuevo indicador
        </Button>
      </Box>

      {error && <Alert severity="error">{error}</Alert>}

      {loading ? (
        <Box sx={{ display: "flex", justifyContent: "center", mt: 4 }}>
          <CircularProgress />
        </Box>
      ) : (
        <Paper>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Orden</TableCell>
                <TableCell>Nombre</TableCell>
                <TableCell>Peso</TableCell>
                <TableCell>Estado</TableCell>
                <TableCell align="right">Acciones</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {rows.map((row) => (
                <TableRow
                  key={row.id}
                  hover
                  sx={{ cursor: "pointer" }}
                  onClick={() => navigate(`/admin/indicadores/${row.id}/items`)}
                >
                  <TableCell>{row.orden}</TableCell>
                  <TableCell>{row.nombre}</TableCell>
                  <TableCell>{row.peso}</TableCell>
                  <TableCell>
                    <Chip
                      size="small"
                      label={row.activo ? "Activo" : "Inactivo"}
                      color={row.activo ? "success" : "default"}
                    />
                  </TableCell>
                  <TableCell align="right" onClick={(e) => e.stopPropagation()}>
                    <Tooltip title="Ver items">
                      <IconButton
                        onClick={() =>
                          navigate(`/admin/indicadores/${row.id}/items`)
                        }
                      >
                        <ArrowForwardIcon fontSize="small" />
                      </IconButton>
                    </Tooltip>
                    <IconButton onClick={() => openEdit(row)}>
                      <EditIcon fontSize="small" />
                    </IconButton>
                    <IconButton onClick={() => setDeleteTarget(row)}>
                      <DeleteIcon fontSize="small" />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Paper>
      )}

      <Dialog open={dialogOpen} onClose={() => setDialogOpen(false)} fullWidth maxWidth="sm">
        <DialogTitle>{editing ? "Editar indicador" : "Nuevo indicador"}</DialogTitle>
        <DialogContent>
          {formError && <Alert severity="error" sx={{ mb: 2 }}>{formError}</Alert>}
          <TextField
            label="Nombre"
            fullWidth
            margin="normal"
            value={form.nombre}
            onChange={(e) => setForm({ ...form, nombre: e.target.value })}
          />
          <TextField
            label="Descripcion"
            fullWidth
            multiline
            rows={2}
            margin="normal"
            value={form.descripcion}
            onChange={(e) => setForm({ ...form, descripcion: e.target.value })}
          />
          <TextField
            label="Peso"
            type="number"
            fullWidth
            margin="normal"
            value={form.peso}
            onChange={(e) => setForm({ ...form, peso: e.target.value })}
          />
          <TextField
            label="Orden"
            type="number"
            fullWidth
            margin="normal"
            value={form.orden}
            onChange={(e) => setForm({ ...form, orden: e.target.value })}
          />
          <FormControlLabel
            control={
              <Switch
                checked={form.activo}
                onChange={(e) => setForm({ ...form, activo: e.target.checked })}
              />
            }
            label="Activo"
          />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDialogOpen(false)}>Cancelar</Button>
          <Button variant="contained" onClick={handleSave} disabled={saving}>
            {saving ? "Guardando..." : "Guardar"}
          </Button>
        </DialogActions>
      </Dialog>

      <Dialog open={!!deleteTarget} onClose={() => setDeleteTarget(null)}>
        <DialogTitle>Eliminar indicador</DialogTitle>
        <DialogContent>
          {deleteError && <Alert severity="error" sx={{ mb: 2 }}>{deleteError}</Alert>}
          <Typography>
            ¿Seguro que quieres eliminar "{deleteTarget?.nombre}"? Esta accion no se puede deshacer.
          </Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteTarget(null)}>Cancelar</Button>
          <Button color="error" variant="contained" onClick={handleDelete}>
            Eliminar
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
