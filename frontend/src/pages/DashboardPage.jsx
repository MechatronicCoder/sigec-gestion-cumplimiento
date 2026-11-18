import { useEffect, useState } from "react";
import { Box, Typography, Grid, Card, CardContent, CircularProgress, Alert } from "@mui/material";
import { useAuth } from "../context/AuthContext";
import api from "../api/axios";

const labels = {
  dimensiones: "Dimensiones",
  indicadores: "Indicadores",
  items: "Items de control",
  evaluaciones: "Evaluaciones registradas",
};

export default function DashboardPage() {
  const { user } = useAuth();
  const [stats, setStats] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    api
      .get("/api/dashboard/stats")
      .then(({ data }) => setStats(data))
      .catch(() => setError("No se pudieron cargar las estadisticas."));
  }, []);

  return (
    <Box>
      <Typography variant="h4" gutterBottom>
        Bienvenido, {user?.name}
      </Typography>
      <Typography variant="body1" color="text.secondary" sx={{ mb: 4 }}>
        Panel general del Sistema de Gestion de Cumplimiento (SIGEC).
      </Typography>

      {error && <Alert severity="error">{error}</Alert>}

      {!stats && !error && (
        <Box sx={{ display: "flex", justifyContent: "center", mt: 4 }}>
          <CircularProgress />
        </Box>
      )}

      {stats && (
        <Grid container spacing={3}>
          {Object.entries(stats).map(([key, value]) => (
            <Grid item xs={12} sm={6} md={3} key={key}>
              <Card>
                <CardContent>
                  <Typography variant="h3">{value}</Typography>
                  <Typography color="text.secondary">
                    {labels[key] || key}
                  </Typography>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
      )}
    </Box>
  );
}
