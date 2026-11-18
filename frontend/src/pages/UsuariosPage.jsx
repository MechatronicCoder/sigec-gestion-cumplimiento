import { useEffect, useState } from "react";
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
  Select,
  MenuItem,
  InputLabel,
  FormControl,
  FormControlLabel,
  Switch,
  Alert,
  CircularProgress,
  Chip,
  Tooltip,
} from "@mui/material";
import AddIcon from "@mui/icons-material/Add";
import EditIcon from "@mui/icons-material/Edit";
import LockResetIcon from "@mui/icons-material/LockReset";
import {
  listUsuarios,
  listRoles,
  createUsuario,
  updateUsuario,
  toggleUsuario,
  resetPasswordUsuario,
} from "../api/usuarios";

const emptyForm = { name: "", email: "", password: "", rol: "", activo: true };

export default function UsuariosPage() {
  const [rows, setRows] = useState([]);
  const [roles, setRoles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [dialogOpen, setDialogOpen] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState(emptyForm);
  const [saving, setSaving] = useState(false);
  const [formError, setFormError] = useState(null);

  const [resetInfo, setResetInfo] = useState(null);

  function load() {
    setLoading(true);
    Promise.all([listUsuarios(), listRoles()])
      .then(([users, rolesList]) => {
        setRows(users);
        setRoles(rolesList);
      })
      .catch(() => setError("No se pudieron cargar los usuarios."))
      .finally(() => setLoading(false));
  }

  useEffect(load, []);

  function openCreate() {
    setEditing(null);
    setForm(emptyForm);
    setFormError(null);
    setDialogOpen(true);
  }

  function openEdit(row) {
    setEditing(row);
    setForm({
      name: row.name,
      email: row.email,
      password: "",
      rol: row.roles?.[0]?.name || row.rol || "",
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
        await updateUsuario(editing.id, {
          name: form.name,
          email: form.email,
          rol: form.rol,
          activo: form.activo,
        });
      } else {
        await createUsuario(form);
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

  async function handleToggle(row) {
    await toggleUsuario(row.id);
    load();
  }

  async function handleReset(row) {
    const data = await resetPasswordUsuario(row.id);
    setResetInfo({ email: row.email, password: data.password_temporal });
  }

  return (
    <Box>
      <Box sx={{ display: "flex", justifyContent: "space-between", mb: 2 }}>
        <Typography variant="h4">Usuarios</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={openCreate}>
          Nuevo usuario
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
                <TableCell>Nombre</TableCell>
                <TableCell>Correo</TableCell>
                <TableCell>Rol</TableCell>
                <TableCell>Estado</TableCell>
                <TableCell align="right">Acciones</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {rows.map((row) => (
                <TableRow key={row.id}>
                  <TableCell>{row.name}</TableCell>
                  <TableCell>{row.email}</TableCell>
                  <TableCell>
                    <Chip size="small" label={row.roles?.[0]?.name || row.rol || "-"} />
                  </TableCell>
                  <TableCell>
                    <Chip
                      size="small"
                      label={row.activo ? "Activo" : "Inactivo"}
                      color={row.activo ? "success" : "default"}
                      onClick={() => handleToggle(row)}
                      sx={{ cursor: "pointer" }}
                    />
                  </TableCell>
                  <TableCell align="right">
                    <Tooltip title="Restablecer contrasena">
                      <IconButton onClick={() => handleReset(row)}>
                        <LockResetIcon fontSize="small" />
                      </IconButton>
                    </Tooltip>
                    <IconButton onClick={() => openEdit(row)}>
                      <EditIcon fontSize="small" />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Paper>
      )}

      <Dialog open={dialogOpen} onClose={() => setDialogOpen(false)} fullWidth maxWidth="sm">
        <DialogTitle>{editing ? "Editar usuario" : "Nuevo usuario"}</DialogTitle>
        <DialogContent>
          {formError && <Alert severity="error" sx={{ mb: 2 }}>{formError}</Alert>}
          <TextField
            label="Nombre"
            fullWidth
            margin="normal"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
          />
          <TextField
            label="Correo"
            type="email"
            fullWidth
            margin="normal"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
          />
          {!editing && (
            <TextField
              label="Contrasena"
              type="password"
              fullWidth
              margin="normal"
              value={form.password}
              onChange={(e) => setForm({ ...form, password: e.target.value })}
            />
          )}
          <FormControl fullWidth margin="normal">
            <InputLabel>Rol</InputLabel>
            <Select
              label="Rol"
              value={form.rol}
              onChange={(e) => setForm({ ...form, rol: e.target.value })}
            >
              {roles.map((r) => (
                <MenuItem key={r} value={r}>
                  {r}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
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

      <Dialog open={!!resetInfo} onClose={() => setResetInfo(null)}>
        <DialogTitle>Contrasena restablecida</DialogTitle>
        <DialogContent>
          <Typography sx={{ mb: 1 }}>
            Nueva contrasena temporal para <strong>{resetInfo?.email}</strong>:
          </Typography>
          <Typography variant="h6" sx={{ fontFamily: "monospace" }}>
            {resetInfo?.password}
          </Typography>
          <Alert severity="info" sx={{ mt: 2 }}>
            Compartela con el usuario por un canal seguro. No se volvera a mostrar.
          </Alert>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setResetInfo(null)}>Cerrar</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
